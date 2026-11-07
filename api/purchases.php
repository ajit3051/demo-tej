<?php
/**
 * Tejas ERP - Purchases API
 * 
 * Endpoints:
 *   GET  /api/purchases.php?action=list     - List purchase bills (billwise) with totals & search
 *   GET  /api/purchases.php?action=itemwise - List item-wise purchases
 *   GET  /api/purchases.php?action=details  - Get purchase bill details with item lines (bill_no=...)
 *   GET  /api/purchases.php?action=next_bill_no - Generate next auto purchase bill number
 *   POST /api/purchases.php?action=create   - Create new purchase bill with item lines
 *   POST /api/purchases.php?action=update   - Update existing purchase bill
 *   POST /api/purchases.php?action=delete   - Delete purchase bill by bill_no or id
 */

require_once __DIR__ . '/config.php';

// Strict authentication enforcement
$authUser = Auth::requireAuth();

$action = Request::input('action') ?? Request::input('route') ?? 'list';
$method = Request::method();

switch ($action) {
    case 'list':
    case 'billwise':
        $page = max((int)Request::input('page', 1), 1);
        $limit = max((int)Request::input('limit', 20), 1);
        $offset = ($page - 1) * $limit;
        $search = trim((string)Request::input('search', ''));
        $fromDate = Request::input('from_date');
        $toDate = Request::input('to_date');

        $where = ["id > 0"];
        $params = [];

        if (!empty($fromDate)) {
            $where[] = "purchage_date >= :from_date";
            $params[':from_date'] = apiDateInSQLFormat($fromDate);
        }
        if (!empty($toDate)) {
            $where[] = "purchage_date <= :to_date";
            $params[':to_date'] = apiDateInSQLFormat($toDate);
        }
        if ($search !== '') {
            $where[] = "(bill_number LIKE :s1 OR invoice_number LIKE :s2 OR account_name LIKE :s3 OR gst_type LIKE :s4)";
            $params[':s1'] = "%$search%";
            $params[':s2'] = "%$search%";
            $params[':s3'] = "%$search%";
            $params[':s4'] = "%$search%";
        }

        $whereSql = implode(' AND ', $where);

        // Fetch totals
        $totalsSql = "SELECT 
                        COUNT(*) as total_bills,
                        SUM(b.total_qty) as overall_qty,
                        SUM(b.total_gst) as overall_gst,
                        SUM(b.total_amt) as overall_amount
                      FROM (
                          SELECT 
                              bill_number,
                              SUM(CAST(qty AS UNSIGNED)) as total_qty,
                              SUM(CAST(gst_amount AS DECIMAL(10,2))) as total_gst,
                              SUM(CAST(amount AS DECIMAL(10,2))) as total_amt
                          FROM tbl_purchase
                          WHERE $whereSql
                          GROUP BY bill_number
                      ) b";
        $totals = ApiDB::fetchOne($totalsSql, $params) ?: [];
        $totalRecords = (int)($totals['total_bills'] ?? 0);

        // Fetch paginated purchase bills
        $sql = "SELECT 
                    MAX(id) as id,
                    MAX(purchage_date) as purchage_date,
                    bill_number,
                    MAX(invoice_number) as invoice_number,
                    MAX(invoice_date) as invoice_date,
                    MAX(account_name) as account_name,
                    MAX(mobile_number) as mobile_number,
                    MAX(gst_type) as gst_type,
                    MAX(bill_type) as bill_type,
                    SUM(CAST(qty AS UNSIGNED)) as total_qty,
                    SUM(CAST(gst_amount AS DECIMAL(10,2))) as gst_amount,
                    SUM(CAST(amount AS DECIMAL(10,2))) as amount,
                    MAX(payment_mode) as payment_mode,
                    MAX(status) as status
                FROM tbl_purchase
                WHERE $whereSql
                GROUP BY bill_number
                ORDER BY MAX(id) DESC
                LIMIT $offset, $limit";

        $records = ApiDB::fetchAll($sql, $params);
        foreach ($records as &$r) {
            $r['purchage_date_formatted'] = apiDateFormat($r['purchage_date']);
        }

        Response::success($records, "Purchase bills retrieved.", [
            'totals'     => [
                'total_qty'    => (float)($totals['overall_qty'] ?? 0),
                'total_gst'    => (float)($totals['overall_gst'] ?? 0),
                'total_amount' => (float)($totals['overall_amount'] ?? 0),
            ],
            'pagination' => apiPaginate($totalRecords, $page, $limit),
        ]);
        break;

    case 'itemwise':
        $page = max((int)Request::input('page', 1), 1);
        $limit = max((int)Request::input('limit', 20), 1);
        $offset = ($page - 1) * $limit;
        $search = trim((string)Request::input('search', ''));
        $fromDate = Request::input('from_date');
        $toDate = Request::input('to_date');

        $where = ["id > 0"];
        $params = [];

        if (!empty($fromDate)) {
            $where[] = "purchage_date >= :from_date";
            $params[':from_date'] = apiDateInSQLFormat($fromDate);
        }
        if (!empty($toDate)) {
            $where[] = "purchage_date <= :to_date";
            $params[':to_date'] = apiDateInSQLFormat($toDate);
        }
        if ($search !== '') {
            $where[] = "(bill_number LIKE :s1 OR invoice_number LIKE :s2 OR barcode_number LIKE :s3 OR item_name LIKE :s4 OR account_name LIKE :s5)";
            $params[':s1'] = "%$search%";
            $params[':s2'] = "%$search%";
            $params[':s3'] = "%$search%";
            $params[':s4'] = "%$search%";
            $params[':s5'] = "%$search%";
        }

        $whereSql = implode(' AND ', $where);

        $totalRecords = (int)ApiDB::fetchColumn("SELECT COUNT(*) FROM tbl_purchase WHERE $whereSql", $params);

        $sql = "SELECT * FROM tbl_purchase WHERE $whereSql ORDER BY id DESC LIMIT $offset, $limit";
        $records = ApiDB::fetchAll($sql, $params);
        foreach ($records as &$r) {
            $r['purchage_date_formatted'] = apiDateFormat($r['purchage_date']);
        }

        Response::success($records, "Item-wise purchases retrieved.", [
            'pagination' => apiPaginate($totalRecords, $page, $limit),
        ]);
        break;

    case 'details':
    case 'get':
        $billNo = Request::input('bill_no');
        $id = Request::input('id');

        if (empty($billNo) && empty($id)) {
            Response::error("bill_no or id parameter is required.", 422);
        }

        $whereClause = !empty($billNo) ? "bill_number = :val" : "id = :val";
        $paramVal = !empty($billNo) ? $billNo : $id;

        $items = ApiDB::fetchAll("SELECT * FROM tbl_purchase WHERE $whereClause ORDER BY id ASC", [':val' => $paramVal]);
        if (empty($items)) {
            Response::notFound("Purchase bill not found.");
        }

        $header = $items[0];
        $billHeader = [
            'bill_number'            => $header['bill_number'],
            'invoice_number'         => $header['invoice_number'],
            'purchage_date'          => $header['purchage_date'],
            'purchage_date_formatted'=> apiDateFormat($header['purchage_date']),
            'account_name'           => $header['account_name'],
            'mobile_number'          => $header['mobile_number'],
            'payment_mode'           => $header['payment_mode'],
            'total_items'            => count($items),
            'total_qty'              => array_sum(array_column($items, 'qty')),
            'total_amount'           => round(array_sum(array_map(fn($it) => (float)($it['amount'] ?? 0), $items)), 2),
            'status'                 => $header['status'],
        ];

        Response::success([
            'bill'  => $billHeader,
            'items' => $items,
        ], "Purchase bill details retrieved.");
        break;

    case 'next_bill_no':
        $nextNo = apiGenerateBillNo('tbl_purchase', 'bill_number', 'PB');
        Response::success(['next_bill_no' => $nextNo]);
        break;

    case 'create':
        if ($method !== 'POST') {
            Response::error("Method not allowed. Use POST.", 405);
        }

        $input = Request::all();
        $items = $input['items'] ?? [];

        if (empty($items) || !is_array($items)) {
            Response::error("At least one item is required in 'items' array.", 422);
        }

        $billNo = !empty($input['bill_number']) ? trim($input['bill_number']) : apiGenerateBillNo('tbl_purchase', 'bill_number', 'PB');
        $purchageDate = apiDateInSQLFormat($input['purchage_date'] ?? date('Y-m-d'));
        $createdDate = date('Y-m-d H:i:s');

        ApiDB::beginTransaction();
        try {
            $insertedIds = [];
            foreach ($items as $item) {
                $row = [
                    'purchage_date'          => $purchageDate,
                    'bill_number'            => $billNo,
                    'invoice_number'         => $input['invoice_number'] ?? '',
                    'invoice_date'           => apiDateInSQLFormat($input['invoice_date'] ?? date('Y-m-d')),
                    'gst_type'               => $input['gst_type'] ?? 'Inclusive',
                    'under_gst'              => $input['under_gst'] ?? 'Local',
                    'bill_type'              => $input['bill_type'] ?? 'Tax Invoice',
                    'mobile_number'          => $input['mobile_number'] ?? '',
                    'account_name'           => $input['account_name'] ?? '',
                    'ac_code'                => $input['ac_code'] ?? '',
                    'payment_mode'           => $input['payment_mode'] ?? 'CASH',
                    'pay_cash'               => $input['pay_cash'] ?? '0.00',
                    'pay_upi'                => $input['pay_upi'] ?? '0.00',
                    'pay_card'               => $input['pay_card'] ?? '0.00',
                    'pay_credit'             => $input['pay_credit'] ?? '0.00',
                    'item_name'              => $item['item_name'] ?? 'Item',
                    'barcode_number'         => $item['barcode_number'] ?? ($item['barcode_no'] ?? apiGenerateAutoBarcode()),
                    'article_no'             => $item['article_no'] ?? '',
                    'item_type'              => $item['item_type'] ?? '',
                    'category'               => $item['category'] ?? '',
                    'brand'                  => $item['brand'] ?? '',
                    'color'                  => $item['color'] ?? '',
                    'size'                   => $item['size'] ?? '',
                    'qty'                    => (int)($item['qty'] ?? 1),
                    'mou_name'               => $item['mou_name'] ?? 'EA',
                    'rate'                   => (float)($item['rate'] ?? ($item['purchase_price'] ?? 0)),
                    'purchase_price'         => (float)($item['purchase_price'] ?? 0),
                    'mrp'                    => (float)($item['mrp'] ?? 0),
                    'sp'                     => (float)($item['sp'] ?? ($item['mrp'] ?? 0)),
                    'selling_price'          => (float)($item['selling_price'] ?? ($item['sp'] ?? 0)),
                    'net_price'              => (float)($item['net_price'] ?? 0),
                    'gst'                    => (float)($item['gst'] ?? 0),
                    'cgst'                   => round((float)($item['gst'] ?? 0) / 2, 2),
                    'sgst'                   => round((float)($item['gst'] ?? 0) / 2, 2),
                    'gst_amount'             => (float)($item['gst_amount'] ?? 0),
                    'taxable_amount'         => (float)($item['taxable_amount'] ?? 0),
                    'amount'                 => (float)($item['amount'] ?? 0),
                    'status'                 => $input['status'] ?? 'success',
                    'created_date'           => $createdDate,
                    'updated_date'           => $createdDate,
                ];

                $id = ApiDB::insert('tbl_purchase', $row);
                $insertedIds[] = $id;

                // Also maintain stock item in tbl_store_items if store provided
                if (!empty($input['store_name']) || !empty($input['store_id'])) {
                    ApiDB::insert('tbl_store_items', [
                        'purchase_id'    => $id,
                        'store_id'       => (int)($input['store_id'] ?? 1),
                        'store_name'     => $input['store_name'] ?? '',
                        'bill_number'    => $billNo,
                        'invoice_number' => $input['invoice_number'] ?? '',
                        'account_name'   => $input['account_name'] ?? '',
                        'barcode_number' => $row['barcode_number'],
                        'item_name'      => $row['item_name'],
                        'item_type'      => $row['item_type'],
                        'category'       => $row['category'],
                        'brand'          => $row['brand'],
                        'size'           => $row['size'],
                        'color'          => $row['color'],
                        'qty'            => $row['qty'],
                        'mou_name'       => $row['mou_name'],
                        'rate'           => $row['rate'],
                        'purchase_price' => $row['purchase_price'],
                        'mrp'            => $row['mrp'],
                        'selling_price'  => $row['selling_price'],
                        'gst'            => $row['gst'],
                        'gst_amount'     => $row['gst_amount'],
                        'taxable_amount' => $row['taxable_amount'],
                        'amount'         => $row['amount'],
                        'status'         => 'Active',
                        'created_date'   => $createdDate,
                        'updated_date'   => $createdDate,
                    ]);
                }
            }

            ApiDB::commit();

            Response::success([
                'bill_number'  => $billNo,
                'inserted_ids' => $insertedIds,
                'items_count'  => count($insertedIds),
            ], "Purchase bill created successfully.", [], 201);
        } catch (Exception $e) {
            ApiDB::rollBack();
            Response::error("Failed to create purchase: " . $e->getMessage(), 500);
        }
        break;

    case 'delete':
        if ($method !== 'POST' && $method !== 'DELETE') {
            Response::error("Method not allowed. Use POST or DELETE.", 405);
        }

        $billNo = Request::input('bill_no');
        $id = Request::input('id');

        if (empty($billNo) && empty($id)) {
            Response::error("bill_no or id is required to delete.", 422);
        }

        if (!empty($billNo)) {
            $deleted = ApiDB::delete('tbl_purchase', 'bill_number = :bno', [':bno' => $billNo]);
        } else {
            $deleted = ApiDB::delete('tbl_purchase', 'id = :id', [':id' => $id]);
        }

        if ($deleted > 0) {
            Response::success(['affected_rows' => $deleted], "Purchase bill deleted successfully.");
        } else {
            Response::notFound("Purchase record not found or already deleted.");
        }
        break;

    default:
        Response::error("Unknown action. Valid actions: list, itemwise, details, next_bill_no, create, delete", 400);
        break;
}

