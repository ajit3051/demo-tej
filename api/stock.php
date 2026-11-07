<?php
/**
 * Tejas ERP - Stock & Inventory Management API
 * 
 * Endpoints:
 *   GET  /api/stock.php?action=report_shortcut - Stock shortcut report (Purchase, Sold, Balance)
 *   GET  /api/stock.php?action=report_details  - Detailed stock report by barcode/item
 *   GET  /api/stock.php?action=store_items     - List items in store inventory
 *   GET  /api/stock.php?action=transfers       - List stock transfer transactions
 *   POST /api/stock.php?action=transfer        - Process stock transfer between stores
 *   POST /api/stock.php?action=return_item     - Return store items
 */

require_once __DIR__ . '/config.php';

// Strict authentication enforcement
$authUser = Auth::requireAuth();

$action = Request::input('action') ?? Request::input('route') ?? 'report_shortcut';
$method = Request::method();

switch ($action) {
    case 'report_shortcut':
        $page = max((int)Request::input('page', 1), 1);
        $limit = max((int)Request::input('limit', 20), 1);
        $offset = ($page - 1) * $limit;
        $search = trim((string)Request::input('search', ''));
        $fromDate = Request::input('from_date');
        $toDate = Request::input('to_date');

        $where = ["TP.id > 0"];
        $params = [];

        if (!empty($fromDate)) {
            $where[] = "TP.purchage_date >= :from_date";
            $params[':from_date'] = apiDateInSQLFormat($fromDate);
        }
        if (!empty($toDate)) {
            $where[] = "TP.purchage_date <= :to_date";
            $params[':to_date'] = apiDateInSQLFormat($toDate);
        }

        $whereSql = implode(' AND ', $where);

        $sql = "SELECT 
                    TP.barcode_number,
                    MAX(TP.item_name) as item_name,
                    MAX(TP.article_no) as article_no,
                    MAX(TP.category) as category,
                    MAX(TP.brand) as brand,
                    MAX(TP.mou_name) as mou_name,
                    MAX(TP.rate) as rate,
                    MAX(TP.mrp) as mrp,
                    MAX(TP.purchage_date) as last_purchase_date,
                    SUM(CAST(TP.qty AS DECIMAL(10,2))) as total_purchase_qty,
                    COALESCE(sales.sold_qty, 0) as total_sold_qty,
                    (SUM(CAST(TP.qty AS DECIMAL(10,2))) - COALESCE(sales.sold_qty, 0)) as balance_qty
                FROM tbl_purchase AS TP
                LEFT JOIN (
                    SELECT barcode_number, SUM(qty) as sold_qty 
                    FROM tbl_bill 
                    GROUP BY barcode_number
                ) sales ON sales.barcode_number = TP.barcode_number
                WHERE $whereSql
                GROUP BY TP.barcode_number";

        if ($search !== '') {
            $sql .= " HAVING (TP.barcode_number LIKE :s1 OR MAX(TP.item_name) LIKE :s2 OR MAX(TP.article_no) LIKE :s3 OR MAX(TP.category) LIKE :s4)";
            $params[':s1'] = "%$search%";
            $params[':s2'] = "%$search%";
            $params[':s3'] = "%$search%";
            $params[':s4'] = "%$search%";
        }

        $allRows = ApiDB::fetchAll($sql, $params);
        $totalRecords = count($allRows);

        $totalPurchQty = array_sum(array_column($allRows, 'total_purchase_qty'));
        $totalSoldQty  = array_sum(array_column($allRows, 'total_sold_qty'));
        $totalBalQty   = array_sum(array_column($allRows, 'balance_qty'));

        $paginated = array_slice($allRows, $offset, $limit);
        foreach ($paginated as &$r) {
            $r['last_purchase_date_formatted'] = apiDateFormat($r['last_purchase_date']);
        }

        Response::success($paginated, "Stock report retrieved.", [
            'summary' => [
                'total_purchase_qty' => $totalPurchQty,
                'total_sold_qty'     => $totalSoldQty,
                'total_balance_qty'  => $totalBalQty,
            ],
            'pagination' => apiPaginate($totalRecords, $page, $limit),
        ]);
        break;

    case 'report_details':
        $page = max((int)Request::input('page', 1), 1);
        $limit = max((int)Request::input('limit', 20), 1);
        $offset = ($page - 1) * $limit;
        $search = trim((string)Request::input('search', ''));

        $sql = "SELECT 
                    TP.barcode_number,
                    MAX(TP.item_name) as item_name,
                    MAX(TP.article_no) as article_no,
                    MAX(IM.item_code) as item_code,
                    MAX(TP.category) as category,
                    MAX(TP.brand) as brand,
                    MAX(TP.size) as size,
                    MAX(TP.color) as color,
                    MAX(TP.mou_name) as mou_name,
                    MAX(TP.purchase_price) as purchase_price,
                    MAX(TP.mrp) as mrp,
                    MAX(TP.rate) as rate,
                    SUM(CAST(TP.qty AS DECIMAL(10,2))) as purchase_qty,
                    COALESCE(sales.sold_qty, 0) as sale_qty,
                    (SUM(CAST(TP.qty AS DECIMAL(10,2))) - COALESCE(sales.sold_qty, 0)) as balance_qty
                FROM tbl_purchase AS TP
                LEFT JOIN tbl_item_master AS IM ON (IM.barcode_no = TP.barcode_number OR IM.sku_no = TP.article_no)
                LEFT JOIN (
                    SELECT barcode_number, SUM(qty) as sold_qty 
                    FROM tbl_bill 
                    GROUP BY barcode_number
                ) sales ON sales.barcode_number = TP.barcode_number
                WHERE TP.id > 0
                GROUP BY TP.barcode_number";

        $params = [];
        if ($search !== '') {
            $sql .= " HAVING (TP.barcode_number LIKE :s1 OR MAX(TP.item_name) LIKE :s2 OR MAX(TP.article_no) LIKE :s3 OR MAX(TP.category) LIKE :s4)";
            $params[':s1'] = "%$search%";
            $params[':s2'] = "%$search%";
            $params[':s3'] = "%$search%";
            $params[':s4'] = "%$search%";
        }

        $allRows = ApiDB::fetchAll($sql, $params);
        $totalRecords = count($allRows);
        $paginated = array_slice($allRows, $offset, $limit);

        Response::success($paginated, "Detailed stock report retrieved.", [
            'pagination' => apiPaginate($totalRecords, $page, $limit),
        ]);
        break;

    case 'store_items':
        $page = max((int)Request::input('page', 1), 1);
        $limit = max((int)Request::input('limit', 20), 1);
        $offset = ($page - 1) * $limit;
        $search = trim((string)Request::input('search', ''));
        $storeId = Request::input('store_id');

        $where = ["id > 0"];
        $params = [];

        if (!empty($storeId)) {
            $where[] = "store_id = :sid";
            $params[':sid'] = $storeId;
        }
        if ($search !== '') {
            $where[] = "(item_name LIKE :s1 OR barcode_number LIKE :s2 OR store_name LIKE :s3)";
            $params[':s1'] = "%$search%";
            $params[':s2'] = "%$search%";
            $params[':s3'] = "%$search%";
        }

        $whereSql = implode(' AND ', $where);
        $totalRecords = (int)ApiDB::fetchColumn("SELECT COUNT(*) FROM tbl_store_items WHERE $whereSql", $params);

        $sql = "SELECT * FROM tbl_store_items WHERE $whereSql ORDER BY id DESC LIMIT $offset, $limit";
        $records = ApiDB::fetchAll($sql, $params);

        Response::success($records, "Store items retrieved.", [
            'pagination' => apiPaginate($totalRecords, $page, $limit),
        ]);
        break;

    case 'transfers':
        // List stock transfer purchases
        $page = max((int)Request::input('page', 1), 1);
        $limit = max((int)Request::input('limit', 20), 1);
        $offset = ($page - 1) * $limit;

        $where = ["transfer_by IS NOT NULL AND transfer_by != ''"];
        $totalRecords = (int)ApiDB::fetchColumn("SELECT COUNT(*) FROM tbl_purchase WHERE " . implode(' AND ', $where));

        $sql = "SELECT id, purchage_date, bill_number, invoice_number, store_name, transfer_by, location, item_name, barcode_number, qty, rate, amount, status 
                FROM tbl_purchase 
                WHERE " . implode(' AND ', $where) . " 
                ORDER BY id DESC 
                LIMIT $offset, $limit";
        $records = ApiDB::fetchAll($sql);

        Response::success($records, "Stock transfers retrieved.", [
            'pagination' => apiPaginate($totalRecords, $page, $limit),
        ]);
        break;

    case 'transfer':
        if ($method !== 'POST') {
            Response::error("Method not allowed. Use POST.", 405);
        }

        $input = Request::all();
        $sourceStore = $input['from_store'] ?? ($input['transfer_by'] ?? '');
        $destStore   = $input['to_store'] ?? ($input['store_name'] ?? '');
        $barcode     = $input['barcode_number'] ?? '';
        $transferQty = (int)($input['qty'] ?? 1);

        if (empty($destStore) || empty($barcode) || $transferQty <= 0) {
            Response::error("to_store, barcode_number, and positive qty are required.", 422);
        }

        // Find source purchase item
        $item = ApiDB::fetchOne("SELECT * FROM tbl_purchase WHERE barcode_number = :bc ORDER BY id DESC LIMIT 1", [':bc' => $barcode]);
        if (!$item) {
            Response::notFound("Item not found with barcode $barcode.");
        }

        ApiDB::beginTransaction();
        try {
            $billNo = apiGenerateBillNo('tbl_purchase', 'bill_number', 'ST');
            $createdDate = date('Y-m-d H:i:s');

            $row = $item;
            unset($row['id']);
            $row['bill_number']   = $billNo;
            $row['transfer_by']   = $sourceStore;
            $row['store_name']    = $destStore;
            $row['qty']           = $transferQty;
            $row['amount']        = (float)$item['rate'] * $transferQty;
            $row['created_date']  = $createdDate;
            $row['updated_date']  = $createdDate;

            $newId = ApiDB::insert('tbl_purchase', $row);

            ApiDB::commit();

            Response::success([
                'transfer_id' => $newId,
                'bill_number' => $billNo,
                'qty'         => $transferQty,
            ], "Stock transferred successfully.", [], 201);
        } catch (Exception $e) {
            ApiDB::rollBack();
            Response::error("Stock transfer failed: " . $e->getMessage(), 500);
        }
        break;

    default:
        Response::error("Unknown action. Valid actions: report_shortcut, report_details, store_items, transfers, transfer", 400);
        break;
}

