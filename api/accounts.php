<?php
/**
 * Tejas ERP - Accounts & Ledger API
 * 
 * Endpoints:
 *   GET  /api/accounts.php?action=list    - List accounts with pagination & filters
 *   GET  /api/accounts.php?action=search  - Fast search by name, mobile, or code for billing autocomplete
 *   GET  /api/accounts.php?action=get     - Get account details by id or ac_code
 *   GET  /api/accounts.php?action=ledger  - Get customer/supplier ledger transactions & balance
 *   POST /api/accounts.php?action=create  - Create new account in tbl_account_master
 *   POST /api/accounts.php?action=update  - Update account
 *   POST /api/accounts.php?action=delete  - Delete account
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
        $partyType = Request::input('party_type');
        $groupName = Request::input('group_name');

        $where = ["1=1"];
        $params = [];

        if (!empty($partyType)) {
            $where[] = "party_type = :ptype";
            $params[':ptype'] = $partyType;
        }
        if (!empty($groupName)) {
            $where[] = "group_name = :gname";
            $params[':gname'] = $groupName;
        }
        if ($search !== '') {
            $where[] = "(account_name LIKE :s1 OR account_code LIKE :s2 OR mobile_no_1 LIKE :s3 OR mobile_no_2 LIKE :s4 OR email_id_1 LIKE :s5)";
            $params[':s1'] = "%$search%";
            $params[':s2'] = "%$search%";
            $params[':s3'] = "%$search%";
            $params[':s4'] = "%$search%";
            $params[':s5'] = "%$search%";
        }

        $whereSql = implode(' AND ', $where);

        $totalRecords = (int)ApiDB::fetchColumn("SELECT COUNT(*) FROM tbl_account_master WHERE $whereSql", $params);

        $sql = "SELECT id, date, account_code, account_name, contact_name, group_name, mobile_no_1, mobile_no_2, 
                       email_id_1, gst_no, address_1, address_2, party_type, opening_balance, credit_limit, gender, status 
                FROM tbl_account_master 
                WHERE $whereSql 
                ORDER BY id DESC 
                LIMIT $offset, $limit";

        $records = ApiDB::fetchAll($sql, $params);

        Response::success($records, "Accounts retrieved successfully.", [
            'pagination' => apiPaginate($totalRecords, $page, $limit),
        ]);
        break;

    case 'search':
        // Autocomplete search across mobile, name, account_code
        $term = trim((string)(Request::input('term') ?? Request::input('q') ?? ''));
        $type = Request::input('type'); // 'mobile', 'account_name', 'ac_code', or default multi

        if (empty($term)) {
            Response::success([], "Empty search term.");
        }

        $where = ["id > 0"];
        $params = [];

        if ($type === 'mobile') {
            $where[] = "(mobile_no_1 LIKE :s1 OR mobile_no_2 LIKE :s2 OR mobile_no_3 LIKE :s3)";
            $params[':s1'] = "%$term%";
            $params[':s2'] = "%$term%";
            $params[':s3'] = "%$term%";
        } elseif ($type === 'account_name') {
            $where[] = "account_name LIKE :s1";
            $params[':s1'] = "%$term%";
        } elseif ($type === 'ac_code') {
            $where[] = "account_code LIKE :s1";
            $params[':s1'] = "%$term%";
        } else {
            $where[] = "(account_name LIKE :s1 OR account_code LIKE :s2 OR mobile_no_1 LIKE :s3 OR mobile_no_2 LIKE :s4)";
            $params[':s1'] = "%$term%";
            $params[':s2'] = "%$term%";
            $params[':s3'] = "%$term%";
            $params[':s4'] = "%$term%";
        }

        $sql = "SELECT id, account_code, account_name, mobile_no_1, mobile_no_2, email_id_1, gst_no, address_1, party_type, opening_balance, status 
                FROM tbl_account_master 
                WHERE " . implode(' AND ', $where) . " 
                ORDER BY account_name ASC 
                LIMIT 25";

        $records = ApiDB::fetchAll($sql, $params);
        Response::success($records, "Account search results.");
        break;

    case 'get':
        $id = Request::input('id');
        $code = Request::input('ac_code') ?? Request::input('account_code');

        if (empty($id) && empty($code)) {
            Response::error("id or ac_code parameter is required.", 422);
        }

        $where = !empty($id) ? "id = :val" : "account_code = :val";
        $paramVal = !empty($id) ? $id : $code;

        $account = ApiDB::fetchOne("SELECT * FROM tbl_account_master WHERE $where LIMIT 1", [':val' => $paramVal]);
        if (!$account) {
            Response::notFound("Account not found.");
        }

        Response::success($account, "Account details retrieved.");
        break;

    case 'ledger':
        $code = Request::input('ac_code') ?? Request::input('account_code');
        $name = Request::input('account_name');
        $mobile = Request::input('mobile');

        if (empty($code) && empty($name) && empty($mobile)) {
            Response::error("ac_code, account_name, or mobile parameter is required.", 422);
        }

        // Find account record
        $account = null;
        if (!empty($code)) {
            $account = ApiDB::fetchOne("SELECT * FROM tbl_account_master WHERE account_code = :code LIMIT 1", [':code' => $code]);
        } elseif (!empty($mobile)) {
            $account = ApiDB::fetchOne("SELECT * FROM tbl_account_master WHERE mobile_no_1 = :m OR mobile_no_2 = :m LIMIT 1", [':m' => $mobile]);
        } elseif (!empty($name)) {
            $account = ApiDB::fetchOne("SELECT * FROM tbl_account_master WHERE account_name = :name LIMIT 1", [':name' => $name]);
        }

        $openingBal = (float)($account['opening_balance'] ?? 0);
        $acCode = $account['account_code'] ?? $code;
        $acName = $account['account_name'] ?? $name;

        // Fetch Sales for this account
        $sales = ApiDB::fetchAll(
            "SELECT 
                bill_number as ref_no,
                MAX(purchage_date) as date,
                'SALE' as type,
                'Sale Bill' as description,
                SUM(CAST(amount AS DECIMAL(10,2))) as debit,
                0.00 as credit,
                MAX(pay_mode) as pay_mode
             FROM tbl_bill 
             WHERE ac_code = :code OR account_name = :name
             GROUP BY bill_number
             ORDER BY MAX(id) ASC",
            [':code' => $acCode, ':name' => $acName]
        );

        // Fetch Payments / Receipts / Expenses for this account
        $expenses = ApiDB::fetchAll(
            "SELECT 
                expcode as ref_no,
                date,
                payment_type as type,
                remarks as description,
                (CASE WHEN payment_type = 'Payment' THEN CAST(amount AS DECIMAL(10,2)) ELSE 0.00 END) as debit,
                (CASE WHEN payment_type = 'Receipt' OR payment_type = 'Received' THEN CAST(amount AS DECIMAL(10,2)) ELSE 0.00 END) as credit,
                paymode_name as pay_mode
             FROM tbl_expensescreation_master 
             WHERE party_code = :code OR account_name = :name
             ORDER BY id ASC",
            [':code' => $acCode, ':name' => $acName]
        );

        // Merge & Sort chronological transactions
        $transactions = array_merge($sales, $expenses);
        usort($transactions, fn($a, $b) => strtotime($a['date']) <=> strtotime($b['date']));

        $runningBalance = $openingBal;
        foreach ($transactions as &$tx) {
            $tx['date_formatted'] = apiDateFormat($tx['date']);
            $debit = (float)$tx['debit'];
            $credit = (float)$tx['credit'];
            $runningBalance = $runningBalance + $debit - $credit;
            $tx['running_balance'] = round($runningBalance, 2);
        }

        Response::success([
            'account'         => $account,
            'opening_balance' => $openingBal,
            'closing_balance' => round($runningBalance, 2),
            'total_debit'     => round(array_sum(array_column($transactions, 'debit')), 2),
            'total_credit'    => round(array_sum(array_column($transactions, 'credit')), 2),
            'transactions'    => $transactions,
        ], "Account ledger retrieved.");
        break;

    case 'create':
        if ($method !== 'POST') {
            Response::error("Method not allowed. Use POST.", 405);
        }

        $input = Request::all();
        $name = trim((string)($input['account_name'] ?? ''));
        if (empty($name)) {
            Response::error("Account name is required.", 422);
        }

        // Auto generate account code if not provided
        $code = !empty($input['account_code']) ? trim($input['account_code']) : apiGenerateBillNo('tbl_account_master', 'account_code', 'AC');

        $row = [
            'date'            => date('Y-m-d'),
            'account_code'    => $code,
            'account_name'    => $name,
            'contact_name'    => $input['contact_name'] ?? '',
            'group_name'      => $input['group_name'] ?? 'Sundry Debtors',
            'mobile_no_1'     => $input['mobile_no_1'] ?? ($input['mobile'] ?? ''),
            'mobile_no_2'     => $input['mobile_no_2'] ?? '',
            'mobile_no_3'     => $input['mobile_no_3'] ?? '',
            'email_id_1'      => $input['email_id_1'] ?? ($input['email'] ?? ''),
            'email_id_2'      => $input['email_id_2'] ?? '',
            'gst_no'          => $input['gst_no'] ?? '',
            'state_code_1'    => $input['state_code_1'] ?? '',
            'location_1'      => $input['location_1'] ?? '',
            'address_1'       => $input['address_1'] ?? ($input['address'] ?? ''),
            'address_2'       => $input['address_2'] ?? '',
            'party_type'      => $input['party_type'] ?? 'Customer',
            'opening_balance' => (string)($input['opening_balance'] ?? '0.00'),
            'credit_limit'    => (string)($input['credit_limit'] ?? '0.00'),
            'picture'         => $input['picture'] ?? '',
            'gender'          => $input['gender'] ?? '',
            'status'          => $input['status'] ?? 'true',
        ];

        $id = ApiDB::insert('tbl_account_master', $row);
        $row['id'] = $id;

        Response::success($row, "Account created successfully.", [], 201);
        break;

    case 'update':
        if ($method !== 'POST' && $method !== 'PUT') {
            Response::error("Method not allowed. Use POST or PUT.", 405);
        }

        $id = Request::input('id');
        if (empty($id)) {
            Response::error("Account id is required for update.", 422);
        }

        $existing = ApiDB::fetchOne("SELECT * FROM tbl_account_master WHERE id = :id", [':id' => $id]);
        if (!$existing) {
            Response::notFound("Account not found.");
        }

        $input = Request::all();
        $fields = [
            'account_name', 'contact_name', 'group_name', 'mobile_no_1', 'mobile_no_2',
            'mobile_no_3', 'email_id_1', 'email_id_2', 'gst_no', 'state_code_1', 'location_1',
            'address_1', 'address_2', 'party_type', 'opening_balance', 'credit_limit', 'gender', 'status'
        ];

        $updateData = [];
        foreach ($fields as $f) {
            if (isset($input[$f])) {
                $updateData[$f] = $input[$f];
            }
        }

        ApiDB::update('tbl_account_master', $updateData, 'id = :id', [':id' => $id]);
        $updated = ApiDB::fetchOne("SELECT * FROM tbl_account_master WHERE id = :id", [':id' => $id]);

        Response::success($updated, "Account updated successfully.");
        break;

    case 'delete':
        if ($method !== 'POST' && $method !== 'DELETE') {
            Response::error("Method not allowed. Use POST or DELETE.", 405);
        }

        $id = Request::input('id');
        if (empty($id)) {
            Response::error("Account id is required to delete.", 422);
        }

        $affected = ApiDB::delete('tbl_account_master', 'id = :id', [':id' => $id]);
        if ($affected > 0) {
            Response::success(['affected_rows' => $affected], "Account deleted successfully.");
        } else {
            Response::notFound("Account not found or already deleted.");
        }
        break;

    default:
        Response::error("Unknown action. Valid actions: list, search, get, ledger, create, update, delete", 400);
        break;
}

