<?php
/**
 * Master Data Management API
 * Completely self-contained under api/
 */
require_once __DIR__ . '/config.php';

// Strict authentication enforcement
$authUser = Auth::requireAuth();

$action = Request::get('action', Request::get('act', 'list'));
$entity = Request::get('entity', Request::get('type', ''));

// Map of supported master entities and their tables/columns
$entityMap = [
    'brands' => [
        'table' => 'tbl_brand_master',
        'id_col' => 'id',
        'code_col' => 'brand_code',
        'name_col' => 'brand_name',
        'fields' => ['brand_code', 'brand_name', 'status', 'created_date']
    ],
    'groups' => [
        'table' => 'tbl_group_master',
        'id_col' => 'id',
        'code_col' => 'group_code',
        'name_col' => 'group_name',
        'fields' => ['group_code', 'group_name', 'status', 'created_date']
    ],
    'categories' => [
        'table' => 'tbl_group_master',
        'id_col' => 'id',
        'code_col' => 'group_code',
        'name_col' => 'group_name',
        'fields' => ['group_code', 'group_name', 'status', 'created_date']
    ],
    'subgroups' => [
        'table' => 'tbl_subgroup_master',
        'id_col' => 'id',
        'code_col' => 'subgroup_code',
        'name_col' => 'subgroup_name',
        'fields' => ['group_id', 'group_name', 'subgroup_code', 'subgroup_name', 'status', 'created_date']
    ],
    'colors' => [
        'table' => 'tbl_color_master',
        'id_col' => 'id',
        'code_col' => 'color_code',
        'name_col' => 'color_name',
        'fields' => ['color_code', 'color_name', 'status', 'created_date']
    ],
    'sizes' => [
        'table' => 'tbl_size_master',
        'id_col' => 'id',
        'code_col' => 'size_code',
        'name_col' => 'size_name',
        'fields' => ['size_code', 'size_name', 'status', 'created_date']
    ],
    'gst' => [
        'table' => 'tbl_gst_master',
        'id_col' => 'id',
        'code_col' => 'hsn_code',
        'name_col' => 'gst_slab',
        'fields' => ['hsn_code', 'gst_slab', 'sgst', 'cgst', 'igst', 'cess', 'status', 'created_date']
    ],
    'paymodes' => [
        'table' => 'tbl_paymode_master',
        'id_col' => 'id',
        'code_col' => 'paymode_code',
        'name_col' => 'paymode_name',
        'fields' => ['paymode_code', 'paymode_name', 'status', 'created_date']
    ],
    'stores' => [
        'table' => 'tbl_store_master',
        'id_col' => 'id',
        'code_col' => 'store_code',
        'name_col' => 'store_name',
        'fields' => ['store_code', 'store_name', 'contact_person', 'mobile_no', 'address', 'city', 'state', 'pincode', 'status', 'created_date']
    ],
    'store_profiles' => [
        'table' => 'tbl_store_profile',
        'id_col' => 'id',
        'code_col' => 'store_code',
        'name_col' => 'store_name',
        'fields' => ['store_code', 'store_name', 'tagline', 'address', 'city', 'state', 'pincode', 'mobile', 'email', 'gstin', 'terms_conditions']
    ],
    'locations' => [
        'table' => 'tbl_location_master',
        'id_col' => 'id',
        'code_col' => 'location_code',
        'name_col' => 'location_name',
        'fields' => ['location_code', 'location_name', 'status', 'created_date']
    ],
    'expenses_heads' => [
        'table' => 'tbl_expenses_head',
        'id_col' => 'id',
        'code_col' => 'expense_code',
        'name_col' => 'expense_head',
        'fields' => ['expense_code', 'expense_head', 'status', 'created_date']
    ],
    'expenses' => [
        'table' => 'tbl_expenses',
        'id_col' => 'id',
        'code_col' => 'voucher_no',
        'name_col' => 'expense_head',
        'fields' => ['voucher_no', 'expense_head', 'amount', 'paymode', 'expense_date', 'remarks', 'created_date']
    ],
    'coupons' => [
        'table' => 'tbl_coupon_master',
        'id_col' => 'id',
        'code_col' => 'coupon_code',
        'name_col' => 'coupon_name',
        'fields' => ['coupon_code', 'coupon_name', 'coupon_type', 'discount_val', 'min_order_amount', 'start_date', 'end_date', 'status']
    ],
    'discounts' => [
        'table' => 'tbl_discount_master',
        'id_col' => 'id',
        'code_col' => 'discount_code',
        'name_col' => 'discount_name',
        'fields' => ['discount_code', 'discount_name', 'discount_type', 'discount_val', 'status']
    ],
    'salesmen' => [
        'table' => 'tbl_salesman_master',
        'id_col' => 'id',
        'code_col' => 'salesman_code',
        'name_col' => 'salesman_name',
        'fields' => ['salesman_code', 'salesman_name', 'mobile_no', 'email', 'commission_perc', 'status', 'created_date']
    ],
    'salesman_targets' => [
        'table' => 'tbl_salesman_target',
        'id_col' => 'id',
        'code_col' => 'salesman_code',
        'name_col' => 'target_month',
        'fields' => ['salesman_code', 'target_month', 'target_year', 'target_amount', 'achieved_amount']
    ],
    'customer_types' => [
        'table' => 'tbl_customer_type',
        'id_col' => 'id',
        'code_col' => 'type_code',
        'name_col' => 'type_name',
        'fields' => ['type_code', 'type_name', 'discount_percent', 'status']
    ],
    'party_types' => [
        'table' => 'tbl_party_type',
        'id_col' => 'id',
        'code_col' => 'type_code',
        'name_col' => 'party_type',
        'fields' => ['type_code', 'party_type', 'status']
    ],
    'styles' => [
        'table' => 'tbl_style_master',
        'id_col' => 'id',
        'code_col' => 'style_code',
        'name_col' => 'style_name',
        'fields' => ['style_code', 'style_name', 'status']
    ],
    'festivals' => [
        'table' => 'tbl_festival_master',
        'id_col' => 'id',
        'code_col' => 'festival_code',
        'name_col' => 'festival_name',
        'fields' => ['festival_code', 'festival_name', 'start_date', 'end_date', 'discount_val', 'status']
    ],
    'users' => [
        'table' => 'tbl_user_master',
        'id_col' => 'id',
        'code_col' => 'username',
        'name_col' => 'full_name',
        'fields' => ['username', 'full_name', 'email', 'mobile_no', 'user_role', 'store_id', 'status', 'created_date']
    ],
    'company' => [
        'table' => 'tbl_company_master',
        'id_col' => 'id',
        'code_col' => 'company_code',
        'name_col' => 'company_name',
        'fields' => ['company_code', 'company_name', 'address', 'city', 'state', 'pincode', 'phone_no', 'email_id', 'gst_no', 'status']
    ]
];

// If entity is not provided, list all available master entities
if (empty($entity)) {
    if ($action === 'entities') {
        Response::success(array_keys($entityMap), 'Available master entities');
    }
    Response::error('Entity parameter is required. Available: ' . implode(', ', array_keys($entityMap)), 400);
}

$entityKey = strtolower(trim($entity));
if (!isset($entityMap[$entityKey])) {
    Response::notFound("Master entity '$entity' not supported. Available: " . implode(', ', array_keys($entityMap)));
}

$cfg = $entityMap[$entityKey];
$table = $cfg['table'];
$idCol = $cfg['id_col'];
$codeCol = $cfg['code_col'] ?? 'code';
$nameCol = $cfg['name_col'] ?? 'name';

switch ($action) {
    case 'list':
        $search = trim(Request::get('search', ''));
        $status = Request::get('status', null);
        $page = (int)Request::get('page', 1);
        $limit = (int)Request::get('limit', 100);
        $offset = ($page - 1) * $limit;

        $where = [];
        $params = [];

        if (!empty($search)) {
            $searchClauses = [];
            if (!empty($nameCol)) {
                $searchClauses[] = "$nameCol LIKE :search_name";
                $params[':search_name'] = "%$search%";
            }
            if (!empty($codeCol) && $codeCol !== $nameCol) {
                $searchClauses[] = "$codeCol LIKE :search_code";
                $params[':search_code'] = "%$search%";
            }
            if (!empty($searchClauses)) {
                $where[] = '(' . implode(' OR ', $searchClauses) . ')';
            }
        }

        if ($status !== null && $status !== '') {
            $where[] = "status = :status";
            $params[':status'] = $status;
        }

        // Subgroups group_id filter
        if ($entityKey === 'subgroups' && Request::get('group_id')) {
            $where[] = "group_id = :group_id";
            $params[':group_id'] = Request::get('group_id');
        }

        $whereSql = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

        $countRow = ApiDB::fetchOne("SELECT COUNT(*) as total FROM $table $whereSql", $params);
        $total = (int)($countRow['total'] ?? 0);

        // Fetch records
        $sql = "SELECT * FROM $table $whereSql ORDER BY $idCol DESC LIMIT $limit OFFSET $offset";
        $records = ApiDB::fetchAll($sql, $params);

        // Sanitize sensitive user data if users table
        if ($entityKey === 'users') {
            foreach ($records as &$u) {
                unset($u['password']);
            }
        }

        Response::success([
            'items' => $records,
            'pagination' => apiPaginate($total, $page, $limit)
        ], ucfirst($entityKey) . ' list retrieved');
        break;

    case 'get':
        $id = Request::get('id');
        $code = Request::get('code');

        if (!$id && !$code) {
            Response::error('ID or code is required', 400);
        }

        if ($id) {
            $record = ApiDB::fetchOne("SELECT * FROM $table WHERE $idCol = ?", [$id]);
        } else {
            $record = ApiDB::fetchOne("SELECT * FROM $table WHERE $codeCol = ?", [$code]);
        }

        if (!$record) {
            Response::notFound('Record not found');
        }

        if ($entityKey === 'users') {
            unset($record['password']);
        }

        Response::success($record, 'Record found');
        break;

    case 'create':
        $data = Request::all();
        $insertData = [];

        foreach ($cfg['fields'] as $field) {
            if (isset($data[$field])) {
                $insertData[$field] = $data[$field];
            }
        }

        // Special handling for user password
        if ($entityKey === 'users' && !empty($data['password'])) {
            $insertData['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        if (empty($insertData)) {
            Response::error('No valid fields provided for creation', 400);
        }

        // Auto created_date if available and not supplied
        if (in_array('created_date', $cfg['fields']) && empty($insertData['created_date'])) {
            $insertData['created_date'] = date('Y-m-d H:i:s');
        }

        $newId = ApiDB::insert($table, $insertData);
        $created = ApiDB::fetchOne("SELECT * FROM $table WHERE $idCol = ?", [$newId]);
        if ($entityKey === 'users' && $created) {
            unset($created['password']);
        }

        Response::success($created, ucfirst($entityKey) . ' created successfully', 201);
        break;

    case 'update':
        $id = Request::get('id', Request::all()['id'] ?? null);
        if (!$id) {
            Response::error('Record ID is required', 400);
        }

        $existing = ApiDB::fetchOne("SELECT * FROM $table WHERE $idCol = ?", [$id]);
        if (!$existing) {
            Response::notFound('Record not found');
        }

        $data = Request::all();
        $updateData = [];

        foreach ($cfg['fields'] as $field) {
            if (isset($data[$field]) && $field !== $idCol) {
                $updateData[$field] = $data[$field];
            }
        }

        // Password hash if updating user
        if ($entityKey === 'users' && !empty($data['password'])) {
            $updateData['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        if (empty($updateData)) {
            Response::error('No fields provided to update', 400);
        }

        ApiDB::update($table, $updateData, "$idCol = :where_id", [':where_id' => $id]);
        $updated = ApiDB::fetchOne("SELECT * FROM $table WHERE $idCol = ?", [$id]);
        if ($entityKey === 'users' && $updated) {
            unset($updated['password']);
        }

        Response::success($updated, ucfirst($entityKey) . ' updated successfully');
        break;

    case 'delete':
        $id = Request::get('id', Request::all()['id'] ?? null);
        if (!$id) {
            Response::error('Record ID is required', 400);
        }

        $existing = ApiDB::fetchOne("SELECT * FROM $table WHERE $idCol = ?", [$id]);
        if (!$existing) {
            Response::notFound('Record not found');
        }

        ApiDB::delete($table, "$idCol = ?", [$id]);
        Response::success(['id' => $id], ucfirst($entityKey) . ' deleted successfully');
        break;

    default:
        Response::error("Unknown action '$action'", 400);
        break;
}
