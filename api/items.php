<?php
/**
 * Tejas ERP - Items & Inventory API
 * 
 * Endpoints:
 *   GET  /api/items.php?action=list             - List items with filters & pagination
 *   GET  /api/items.php?action=get              - Get item by id, barcode, or sku
 *   GET  /api/items.php?action=lookup           - Quick scan/lookup for POS billing (barcode/sku/name)
 *   GET  /api/items.php?action=generate_barcode - Generate next auto barcode
 *   POST /api/items.php?action=create           - Create new item in tbl_item_master
 *   POST /api/items.php?action=update           - Update item
 *   POST /api/items.php?action=delete           - Delete item
 */

require_once __DIR__ . '/config.php';

// Strict authentication enforcement
$authUser = Auth::requireAuth();

$action = Request::input('action') ?? Request::input('route') ?? 'list';
$method = Request::method();

switch ($action) {
    case 'list':
        $page = max((int)Request::input('page', 1), 1);
        $limit = max((int)Request::input('limit', 20), 1);
        $offset = ($page - 1) * $limit;
        $search = trim((string)Request::input('search', ''));
        $category = Request::input('category') ?? Request::input('group_name');
        $brand = Request::input('brand') ?? Request::input('brand_name');
        $status = Request::input('status');

        $where = ["1=1"];
        $params = [];

        if (!empty($category)) {
            $where[] = "group_name = :cat";
            $params[':cat'] = $category;
        }
        if (!empty($brand)) {
            $where[] = "brand_name = :brand";
            $params[':brand'] = $brand;
        }
        if (!empty($status)) {
            $where[] = "status = :st";
            $params[':st'] = $status;
        }
        if ($search !== '') {
            $where[] = "(item_name LIKE :s1 OR barcode_no LIKE :s2 OR sku_no LIKE :s3 OR item_code LIKE :s4 OR group_name LIKE :s5 OR brand_name LIKE :s6)";
            $params[':s1'] = "%$search%";
            $params[':s2'] = "%$search%";
            $params[':s3'] = "%$search%";
            $params[':s4'] = "%$search%";
            $params[':s5'] = "%$search%";
            $params[':s6'] = "%$search%";
        }

        $whereSql = implode(' AND ', $where);

        $totalRecords = (int)ApiDB::fetchColumn("SELECT COUNT(*) FROM tbl_item_master WHERE $whereSql", $params);

        $sql = "SELECT id, item_code, barcode_no, sku_no, item_name, description, head_name, subhead_name, 
                       group_name, subgroup_name, brand_name, color_name, style_name, size_name, mou_name, 
                       hsn_code, gst_per, purchase_price, dis_per, dis_amt, mrp, sp, profit_amt, min_qty, 
                       reorder_qty, rac_no, self_no, status, created_date, update_date 
                FROM tbl_item_master 
                WHERE $whereSql 
                ORDER BY id DESC 
                LIMIT $offset, $limit";

        $records = ApiDB::fetchAll($sql, $params);

        Response::success($records, "Items retrieved successfully.", [
            'pagination' => apiPaginate($totalRecords, $page, $limit),
        ]);
        break;

    case 'get':
        $id = Request::input('id');
        $barcode = Request::input('barcode') ?? Request::input('barcode_no');
        $sku = Request::input('sku') ?? Request::input('sku_no');

        if (empty($id) && empty($barcode) && empty($sku)) {
            Response::error("id, barcode, or sku parameter is required.", 422);
        }

        $where = [];
        $params = [];
        if (!empty($id)) {
            $where[] = "id = :id";
            $params[':id'] = $id;
        } elseif (!empty($barcode)) {
            $where[] = "barcode_no = :bc";
            $params[':bc'] = $barcode;
        } elseif (!empty($sku)) {
            $where[] = "sku_no = :sku";
            $params[':sku'] = $sku;
        }

        $item = ApiDB::fetchOne("SELECT * FROM tbl_item_master WHERE " . implode(' AND ', $where) . " LIMIT 1", $params);
        if (!$item) {
            Response::notFound("Item not found.");
        }

        // Fetch item images if any
        $images = ApiDB::fetchAll("SELECT * FROM tbl_item_images WHERE item_id = :id", [':id' => $item['id']]);
        $item['images'] = $images;

        Response::success($item, "Item retrieved successfully.");
        break;

    case 'lookup':
        // Fast scanner / autocomplete lookup for POS & Billing
        $term = trim((string)(Request::input('term') ?? Request::input('barcode') ?? Request::input('q') ?? ''));
        if (empty($term)) {
            Response::error("Search term / barcode required.", 422);
        }

        // 1. First check in tbl_purchase (where stock is punched)
        $purchItem = ApiDB::fetchOne(
            "SELECT * FROM tbl_purchase 
             WHERE barcode_number = :t1 OR article_no = :t2 OR bill_number = :t3 
             ORDER BY id DESC LIMIT 1",
            [':t1' => $term, ':t2' => $term, ':t3' => $term]
        );

        // 2. Also check in tbl_item_master
        $masterItem = ApiDB::fetchOne(
            "SELECT * FROM tbl_item_master 
             WHERE barcode_no = :t1 OR sku_no = :t2 OR item_name LIKE :t3 
             ORDER BY id DESC LIMIT 1",
            [':t1' => $term, ':t2' => $term, ':t3' => "%$term%"]
        );

        if (!$purchItem && !$masterItem) {
            Response::notFound("No item found matching '$term'.");
        }

        // Calculate available stock
        $barcodeVal = $purchItem['barcode_number'] ?? ($masterItem['barcode_no'] ?? $term);
        $purchQty = (float)ApiDB::fetchColumn("SELECT SUM(CAST(qty AS DECIMAL(10,2))) FROM tbl_purchase WHERE barcode_number = ?", [$barcodeVal]);
        $saleQty  = (float)ApiDB::fetchColumn("SELECT SUM(qty) FROM tbl_bill WHERE barcode_number = ?", [$barcodeVal]);
        $stockQty = $purchQty - $saleQty;

        // Merge info
        $itemData = [
            'barcode_number' => $barcodeVal,
            'article_no'     => $purchItem['article_no'] ?? ($masterItem['sku_no'] ?? ''),
            'item_name'      => $purchItem['item_name'] ?? ($masterItem['item_name'] ?? ''),
            'category'       => $purchItem['category'] ?? ($masterItem['group_name'] ?? ''),
            'brand'          => $purchItem['brand'] ?? ($masterItem['brand_name'] ?? ''),
            'color'          => $purchItem['color'] ?? ($masterItem['color_name'] ?? ''),
            'size'           => $purchItem['size'] ?? ($masterItem['size_name'] ?? ''),
            'mou_name'       => $purchItem['mou_name'] ?? ($masterItem['mou_name'] ?? 'EA'),
            'purchase_price' => (float)($purchItem['purchase_price'] ?? ($masterItem['purchase_price'] ?? 0)),
            'mrp'            => (float)($purchItem['mrp'] ?? ($masterItem['mrp'] ?? 0)),
            'sp'             => (float)($purchItem['sp'] ?? ($masterItem['sp'] ?? ($purchItem['mrp'] ?? 0))),
            'rate'           => (float)($purchItem['rate'] ?? ($masterItem['sp'] ?? ($purchItem['mrp'] ?? 0))),
            'gst'            => (float)($purchItem['gst'] ?? ($masterItem['gst_per'] ?? 0)),
            'stock_qty'      => $stockQty,
            'source'         => $purchItem ? 'purchase_record' : 'item_master',
        ];

        Response::success($itemData, "Item lookup successful.");
        break;

    case 'generate_barcode':
        $barcode = apiGenerateAutoBarcode();
        Response::success(['barcode' => $barcode], "New auto barcode generated.");
        break;

    case 'create':
        if ($method !== 'POST') {
            Response::error("Method not allowed. Use POST.", 405);
        }

        $input = Request::all();
        $name = trim((string)($input['item_name'] ?? ''));
        if (empty($name)) {
            Response::error("Item name is required.", 422);
        }

        $barcode = !empty($input['barcode_no']) ? trim($input['barcode_no']) : apiGenerateAutoBarcode();
        $code = !empty($input['item_code']) ? trim($input['item_code']) : ('ITM-' . sprintf('%04d', rand(100, 9999)));

        $row = [
            'item_code'      => $code,
            'barcode_no'     => $barcode,
            'sku_no'         => $input['sku_no'] ?? '',
            'item_name'      => $name,
            'description'    => $input['description'] ?? '',
            'head_name'      => $input['head_name'] ?? '',
            'subhead_name'   => $input['subhead_name'] ?? '',
            'group_name'     => $input['group_name'] ?? ($input['category'] ?? ''),
            'subgroup_name'  => $input['subgroup_name'] ?? '',
            'brand_name'     => $input['brand_name'] ?? ($input['brand'] ?? ''),
            'color_name'     => $input['color_name'] ?? ($input['color'] ?? ''),
            'style_name'     => $input['style_name'] ?? '',
            'size_name'      => $input['size_name'] ?? ($input['size'] ?? ''),
            'mou_name'       => $input['mou_name'] ?? 'EA',
            'hsn_code'       => $input['hsn_code'] ?? '',
            'gst_per'        => (string)($input['gst_per'] ?? '0.00'),
            'purchase_price' => (string)($input['purchase_price'] ?? '0.00'),
            'dis_per'        => (string)($input['dis_per'] ?? '0.00'),
            'dis_amt'        => (string)($input['dis_amt'] ?? '0.00'),
            'mrp'            => (string)($input['mrp'] ?? '0.00'),
            'sp'             => (string)($input['sp'] ?? ($input['mrp'] ?? '0.00')),
            'profit_amt'     => (string)($input['profit_amt'] ?? '0.00'),
            'min_qty'        => (string)($input['min_qty'] ?? '0'),
            'reorder_qty'    => (string)($input['reorder_qty'] ?? '0'),
            'rac_no'         => $input['rac_no'] ?? '',
            'self_no'        => $input['self_no'] ?? '',
            'status'         => $input['status'] ?? 'true',
            'date'           => date('Y-m-d'),
            'created_date'   => date('Y-m-d H:i:s'),
            'update_date'    => date('Y-m-d H:i:s'),
        ];

        $id = ApiDB::insert('tbl_item_master', $row);
        $row['id'] = $id;

        Response::success($row, "Item created successfully.", [], 201);
        break;

    case 'update':
        if ($method !== 'POST' && $method !== 'PUT') {
            Response::error("Method not allowed. Use POST or PUT.", 405);
        }

        $id = Request::input('id');
        if (empty($id)) {
            Response::error("Item id is required for update.", 422);
        }

        $existing = ApiDB::fetchOne("SELECT * FROM tbl_item_master WHERE id = :id", [':id' => $id]);
        if (!$existing) {
            Response::notFound("Item not found.");
        }

        $input = Request::all();
        $fields = [
            'item_code', 'barcode_no', 'sku_no', 'item_name', 'description', 'head_name',
            'subhead_name', 'group_name', 'subgroup_name', 'brand_name', 'color_name',
            'style_name', 'size_name', 'mou_name', 'hsn_code', 'gst_per', 'purchase_price',
            'dis_per', 'dis_amt', 'mrp', 'sp', 'profit_amt', 'min_qty', 'reorder_qty',
            'rac_no', 'self_no', 'status'
        ];

        $updateData = [];
        foreach ($fields as $f) {
            if (isset($input[$f])) {
                $updateData[$f] = $input[$f];
            }
        }
        $updateData['update_date'] = date('Y-m-d H:i:s');

        ApiDB::update('tbl_item_master', $updateData, 'id = :id', [':id' => $id]);
        $updated = ApiDB::fetchOne("SELECT * FROM tbl_item_master WHERE id = :id", [':id' => $id]);

        Response::success($updated, "Item updated successfully.");
        break;

    case 'delete':
        if ($method !== 'POST' && $method !== 'DELETE') {
            Response::error("Method not allowed. Use POST or DELETE.", 405);
        }

        $id = Request::input('id');
        if (empty($id)) {
            Response::error("Item id is required to delete.", 422);
        }

        $affected = ApiDB::delete('tbl_item_master', 'id = :id', [':id' => $id]);
        if ($affected > 0) {
            Response::success(['affected_rows' => $affected], "Item deleted successfully.");
        } else {
            Response::notFound("Item not found or already deleted.");
        }
        break;

    default:
        Response::error("Unknown action. Valid actions: list, get, lookup, generate_barcode, create, update, delete", 400);
        break;
}

