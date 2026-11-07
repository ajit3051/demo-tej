<?php
/**
 * Tejas ERP - Sales & Billing API
 * 
 * Endpoints:
 *   GET  /api/sales.php?action=recent     - Get recent sales day-wise (filter_date=YYYY-MM-DD or all)
 *   GET  /api/sales.php?action=billwise   - Get bill-wise sales with payment breakdown & summary
 *   GET  /api/sales.php?action=itemwise   - Get item-wise sales records
 *   GET  /api/sales.php?action=dsr        - Daily Sales Report (DSR) with payment modes & totals
 *   GET  /api/sales.php?action=details    - Get complete bill details with item lines (bill_no=...)
 *   GET  /api/sales.php?action=next_bill_no - Generate next auto bill number
 *   POST /api/sales.php?action=create     - Create a new sale bill with item lines
 *   POST /api/sales.php?action=update     - Update existing sale bill
 *   POST /api/sales.php?action=delete     - Delete sale bill by bill_no or id
 */

require_once __DIR__ . '/config.php';

// Strict authentication enforcement
$authUser = Auth::requireAuth();

$action = Request::input('action') ?? Request::input('route') ?? 'recent';
$method = Request::method();

switch ($action) {
    case 'recent':
        // Filter by day: defaults to today
        $today = date('Y-m-d');
        $filterDate = Request::input('filter_date');
        if ($filterDate === null || $filterDate === '') {
            $filterDate = $today;
        }

        $page = max((int)Request::input('page', 1), 1);
        $limit = max((int)Request::input('limit', 20), 1);
        $offset = ($page - 1) * $limit;
        $search = trim((string)Request::input('search', ''));

        $where = ["1=1"];
        $params = [];

        if ($filterDate !== 'all') {
            $parsedDate = apiDateInSQLFormat($filterDate);
            $where[] = "DATE(purchage_date) = :fdate";
            $params[':fdate'] = $parsedDate;
        }

        if ($search !== '') {
            $where[] = "(bill_number LIKE :s1 OR account_name LIKE :s2 OR pay_mode LIKE :s3 OR mobile_number LIKE :s4)";
            $params[':s1'] = "%$search%";
            $params[':s2'] = "%$search%";
            $params[':s3'] = "%$search%";
            $params[':s4'] = "%$search%";
        }

        $whereSql = implode(' AND ', $where);

        // Count total unique bills
        $countSql = "SELECT COUNT(DISTINCT bill_number) FROM tbl_bill WHERE $whereSql";
        $totalRecords = (int)ApiDB::fetchColumn($countSql, $params);

        // Fetch aggregated bills
        $sql = "SELECT 
                    MAX(id) as id,
                    MAX(purchage_date) as purchage_date,
                    bill_number,
                    MAX(account_name) as account_name,
                    MAX(mobile_number) as mobile_number,
                    MAX(pay_mode) as pay_mode,
                    MAX(gst_type) as gst_type,
                    MAX(under_gst) as under_gst,
                    MAX(bill_type) as bill_type,
                    SUM(qty) as total_qty,
                    SUM(CAST(COALESCE(NULLIF(taxable_amount, ''), NULLIF(net_price, ''), rate * qty, 0) AS DECIMAL(10,2))) as total_rate,
                    SUM(CAST(gst_amount AS DECIMAL(10,2))) as total_gst,
                    SUM(CAST(amount AS DECIMAL(10,2))) as total_amount,
                    MAX(CAST(NULLIF(pay_cash, '') AS DECIMAL(10,2))) as pay_cash,
                    MAX(CAST(NULLIF(pay_upi, '') AS DECIMAL(10,2))) as pay_upi,
                    MAX(CAST(NULLIF(pay_card, '') AS DECIMAL(10,2))) as pay_card,
                    MAX(CAST(NULLIF(pay_credit, '') AS DECIMAL(10,2))) as pay_credit,
                    MAX(status) as status,
                    MAX(created_date) as created_date
                FROM tbl_bill 
                WHERE $whereSql
                GROUP BY bill_number
                ORDER BY MAX(id) DESC
                LIMIT $offset, $limit";

        $records = ApiDB::fetchAll($sql, $params);
        foreach ($records as &$r) {
            $r['purchage_date_formatted'] = apiDateFormat($r['purchage_date']);
        }

        Response::success($records, "Recent sales retrieved.", [
            'filter_date' => $filterDate,
            'pagination'  => apiPaginate($totalRecords, $page, $limit),
        ]);
        break;

    case 'billwise':
        $page = max((int)Request::input('page', 1), 1);
        $limit = max((int)Request::input('limit', 20), 1);
        $offset = ($page - 1) * $limit;
        $search = trim((string)Request::input('search', ''));
        $fromDate = Request::input('from_date');
        $toDate = Request::input('to_date');
        $isRecent = Request::input('recent');

        $where = ["TB.id > 0"];
        $params = [];

        if (!empty($isRecent)) {
            $where[] = "DATE(TB.created_date) = :today";
            $params[':today'] = date('Y-m-d');
        }
        if (!empty($fromDate)) {
            $where[] = "DATE(TB.purchage_date) >= :from_date";
            $params[':from_date'] = apiDateInSQLFormat($fromDate);
        }
        if (!empty($toDate)) {
            $where[] = "DATE(TB.purchage_date) <= :to_date";
            $params[':to_date'] = apiDateInSQLFormat($toDate);
        }
        if ($search !== '') {
            $where[] = "(TB.bill_number LIKE :s1 OR TB.account_name LIKE :s2 OR TB.pay_mode LIKE :s3 OR TB.mobile_number LIKE :s4)";
            $params[':s1'] = "%$search%";
            $params[':s2'] = "%$search%";
            $params[':s3'] = "%$search%";
            $params[':s4'] = "%$search%";
        }

        $whereSql = implode(' AND ', $where);

        // Fetch overall totals across entire filtered set
        $totalsSql = "SELECT 
                        SUM(b.total_qty) as overall_qty,
                        SUM(b.total_rate) as overall_rate,
                        SUM(b.total_gst) as overall_gst,
                        SUM(b.total_amount) as overall_amount,
                        SUM(CASE WHEN b.pay_mode = 'CASH' OR b.bill_cash > 0 THEN (CASE WHEN b.bill_cash > 0 THEN b.bill_cash ELSE b.total_amount END) ELSE 0 END) as cash_amt,
                        SUM(CASE WHEN b.pay_mode = 'UPI' OR b.bill_upi > 0 THEN (CASE WHEN b.bill_upi > 0 THEN b.bill_upi ELSE b.total_amount END) ELSE 0 END) as upi_amt,
                        SUM(CASE WHEN b.pay_mode = 'CARD' OR b.bill_card > 0 THEN (CASE WHEN b.bill_card > 0 THEN b.bill_card ELSE b.total_amount END) ELSE 0 END) as card_amt,
                        SUM(CASE WHEN b.pay_mode = 'CREDIT' OR b.bill_credit > 0 THEN (CASE WHEN b.bill_credit > 0 THEN b.bill_credit ELSE b.total_amount END) ELSE 0 END) as credit_amt,
                        COUNT(*) as total_bills
                      FROM (
                          SELECT 
                              TB.bill_number,
                              MAX(TB.pay_mode) as pay_mode,
                              SUM(TB.qty) as total_qty,
                              SUM(CAST(COALESCE(NULLIF(TB.taxable_amount, ''), NULLIF(TB.net_price, ''), TB.rate * TB.qty, 0) AS DECIMAL(10,2))) as total_rate,
                              SUM(CAST(TB.gst_amount AS DECIMAL(10,2))) as total_gst,
                              SUM(CAST(TB.amount AS DECIMAL(10,2))) as total_amount,
                              MAX(CAST(NULLIF(TB.pay_cash, '') AS DECIMAL(10,2))) as bill_cash,
                              MAX(CAST(NULLIF(TB.pay_upi, '') AS DECIMAL(10,2))) as bill_upi,
                              MAX(CAST(NULLIF(TB.pay_card, '') AS DECIMAL(10,2))) as bill_card,
                              MAX(CAST(NULLIF(TB.pay_credit, '') AS DECIMAL(10,2))) as bill_credit
                          FROM tbl_bill AS TB
                          WHERE $whereSql
                          GROUP BY TB.bill_number
                      ) b";
        $totals = ApiDB::fetchOne($totalsSql, $params) ?: [];

        $totalRecords = (int)($totals['total_bills'] ?? 0);

        // Fetch paginated bills
        $sql = "SELECT 
                    MAX(TB.id) as id,
                    MAX(TB.purchage_date) as purchage_date,
                    TB.bill_number,
                    MAX(TB.pay_mode) as pay_mode,
                    MAX(TB.account_name) as account_name,
                    MAX(TB.mobile_number) as mobile_number,
                    MAX(TB.gst_type) as gst_type,
                    SUM(TB.qty) as qty,
                    SUM(CAST(COALESCE(NULLIF(TB.taxable_amount, ''), NULLIF(TB.net_price, ''), TB.rate * TB.qty, 0) AS DECIMAL(10,2))) as rate,
                    SUM(CAST(TB.gst_amount AS DECIMAL(10,2))) as gst_amount,
                    SUM(CAST(TB.amount AS DECIMAL(10,2))) as amount,
                    MAX(TB.status) as status,
                    COALESCE(MAX(TPM.paymode_name), MAX(TB.pay_mode)) as paymode_name
                FROM tbl_bill as TB 
                LEFT JOIN tbl_paymode_master as TPM ON (TPM.paymode_code = TB.pay_mode OR TPM.paymode_name = TB.pay_mode) 
                WHERE $whereSql
                GROUP BY TB.bill_number
                ORDER BY MAX(TB.id) DESC
                LIMIT $offset, $limit";

        $records = ApiDB::fetchAll($sql, $params);
        foreach ($records as &$r) {
            $r['purchage_date_formatted'] = apiDateFormat($r['purchage_date']);
        }

        Response::success($records, "Bill-wise sales retrieved.", [
            'totals'     => [
                'total_qty'    => (float)($totals['overall_qty'] ?? 0),
                'total_rate'   => (float)($totals['overall_rate'] ?? 0),
                'total_gst'    => (float)($totals['overall_gst'] ?? 0),
                'total_amount' => (float)($totals['overall_amount'] ?? 0),
                'cash_amount'  => (float)($totals['cash_amt'] ?? 0),
                'upi_amount'   => (float)($totals['upi_amt'] ?? 0),
                'card_amount'  => (float)($totals['card_amt'] ?? 0),
                'credit_amount'=> (float)($totals['credit_amt'] ?? 0),
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

        $where = ["TB.id > 0"];
        $params = [];

        if (!empty($fromDate)) {
            $where[] = "DATE(TB.purchage_date) >= :from_date";
            $params[':from_date'] = apiDateInSQLFormat($fromDate);
        }
        if (!empty($toDate)) {
            $where[] = "DATE(TB.purchage_date) <= :to_date";
            $params[':to_date'] = apiDateInSQLFormat($toDate);
        }
        if ($search !== '') {
            $where[] = "(TB.bill_number LIKE :s1 OR TB.item_name LIKE :s2 OR TB.barcode_number LIKE :s3 OR TB.account_name LIKE :s4)";
            $params[':s1'] = "%$search%";
            $params[':s2'] = "%$search%";
            $params[':s3'] = "%$search%";
            $params[':s4'] = "%$search%";
        }

        $whereSql = implode(' AND ', $where);

        $totalRecords = (int)ApiDB::fetchColumn("SELECT COUNT(*) FROM tbl_bill AS TB WHERE $whereSql", $params);

        $sql = "SELECT 
                    TB.id,
                    TB.purchage_date,
                    TB.bill_number,
                    TB.barcode_number,
                    TB.article_no,
                    TB.item_name,
                    TB.category,
                    TB.brand,
                    TB.color,
                    TB.size,
                    TB.qty,
                    TB.mou_name,
                    TB.rate,
                    TB.purchase_price,
                    TB.discount_amt,
                    TB.taxable_amount,
                    TB.gst,
                    TB.gst_amount,
                    TB.amount,
                    TB.account_name,
                    TB.mobile_number,
                    COALESCE(TPM.paymode_name, TB.pay_mode) as paymode_name,
                    TB.status
                FROM tbl_bill AS TB
                LEFT JOIN tbl_paymode_master AS TPM ON (TPM.paymode_code = TB.pay_mode OR TPM.paymode_name = TB.pay_mode)
                WHERE $whereSql
                ORDER BY TB.id DESC
                LIMIT $offset, $limit";

        $records = ApiDB::fetchAll($sql, $params);
        foreach ($records as &$r) {
            $r['purchage_date_formatted'] = apiDateFormat($r['purchage_date']);
        }

        Response::success($records, "Item-wise sales retrieved.", [
            'pagination' => apiPaginate($totalRecords, $page, $limit),
        ]);
        break;

    case 'dsr':
        $dsrType = strtolower(trim((string)Request::input('type', Request::input('option', Request::input('dsr_type', 'dsr1')))));
        if ($dsrType === 'dsr2' || $dsrType === '2') {
            // Forward to DSR-2
            goto handle_dsr2;
        }
        // Fallthrough to DSR-1

    case 'dsr1':
        $page = max((int)Request::input('page', 1), 1);
        $limit = max((int)Request::input('limit', 20), 1);
        $offset = ($page - 1) * $limit;
        $fromDate = Request::input('from_date');
        $toDate = Request::input('to_date');
        $filterDate = Request::input('filter_date', Request::input('date'));
        $recent = Request::input('recent');
        $search = trim((string)Request::input('search', ''));

        $where = ["1=1"];
        $params = [];

        if (!empty($recent)) {
            $where[] = "DATE(created_date) = :today";
            $params[':today'] = date('Y-m-d');
        } elseif (!empty($filterDate) && $filterDate !== 'all') {
            $where[] = "DATE(purchage_date) = :fdate";
            $params[':fdate'] = apiDateInSQLFormat($filterDate);
        }
        if (!empty($fromDate)) {
            $where[] = "DATE(purchage_date) >= :from_date";
            $params[':from_date'] = apiDateInSQLFormat($fromDate);
        }
        if (!empty($toDate)) {
            $where[] = "DATE(purchage_date) <= :to_date";
            $params[':to_date'] = apiDateInSQLFormat($toDate);
        }
        if ($search !== '') {
            $where[] = "(bill_number LIKE :s1 OR account_name LIKE :s2 OR barcode_number LIKE :s3 OR item_name LIKE :s4)";
            $params[':s1'] = "%$search%";
            $params[':s2'] = "%$search%";
            $params[':s3'] = "%$search%";
            $params[':s4'] = "%$search%";
        }

        $whereSql = implode(' AND ', $where);

        // Date-wise Aggregation (DSR-1)
        $query = "SELECT 
                    DATE(b.purchage_date) as purchage_date, 
                    SUM(b.total_qty) AS total_qty,
                    COALESCE(SUM(b.bill_cash), 0) AS total_cash,
                    COALESCE(SUM(b.bill_upi), 0) AS total_upi,
                    COALESCE(SUM(b.bill_card), 0) AS total_card,
                    COALESCE(SUM(b.bill_credit), 0) AS total_credit,
                    COALESCE(SUM(b.total_gst), 0) as total_gst, 
                    COALESCE(SUM(b.total_amt), 0) as total_amount,
                    COALESCE(SUM(b.total_rate), 0) as total_rate
                FROM (
                    SELECT 
                        bill_number,
                        MAX(purchage_date) as purchage_date,
                        SUM(qty) as total_qty,
                        SUM(CAST(COALESCE(NULLIF(taxable_amount, ''), NULLIF(net_price, ''), rate * qty, 0) AS DECIMAL(10,2))) as total_rate,
                        SUM(CAST(gst_amount AS DECIMAL(10,2))) as total_gst,
                        SUM(CAST(amount AS DECIMAL(10,2))) as total_amt,
                        MAX(CAST(NULLIF(pay_cash, '') AS DECIMAL(10,2))) as bill_cash,
                        MAX(CAST(NULLIF(pay_upi, '') AS DECIMAL(10,2))) as bill_upi,
                        MAX(CAST(NULLIF(pay_card, '') AS DECIMAL(10,2))) as bill_card,
                        MAX(CAST(NULLIF(pay_credit, '') AS DECIMAL(10,2))) as bill_credit
                    FROM tbl_bill 
                    WHERE $whereSql
                    GROUP BY bill_number
                ) b
                GROUP BY DATE(b.purchage_date)
                ORDER BY purchage_date DESC";

        $allRows = ApiDB::fetchAll($query, $params);
        $totalRecords = count($allRows);

        // Overall summary totals
        $sumQty = 0; $sumRate = 0; $sumGst = 0; $sumAmt = 0;
        $sumCash = 0; $sumUpi = 0; $sumCard = 0; $sumCredit = 0;
        foreach ($allRows as $r) {
            $sumQty    += (float)$r['total_qty'];
            $sumRate   += (float)$r['total_rate'];
            $sumGst    += (float)$r['total_gst'];
            $sumAmt    += (float)$r['total_amount'];
            $sumCash   += (float)$r['total_cash'];
            $sumUpi    += (float)$r['total_upi'];
            $sumCard   += (float)$r['total_card'];
            $sumCredit += (float)$r['total_credit'];
        }

        // Payment Mode Summary Cards (CASH, UPI, CARD, CREDIT)
        $paymentCardsSql = "SELECT 
            pm.mode AS payment_mode,
            COALESCE(SUM(CASE WHEN b.pay_mode = pm.mode OR (b.pay_mode = 'DUAL' AND ((pm.mode = 'CASH' AND b.bill_cash > 0) OR (pm.mode = 'UPI' AND b.bill_upi > 0) OR (pm.mode = 'CARD' AND b.bill_card > 0) OR (pm.mode = 'CREDIT' AND b.bill_credit > 0))) THEN b.total_qty ELSE 0 END), 0) AS total_qty,
            COALESCE(SUM(CASE 
                WHEN pm.mode = 'CASH' THEN (CASE WHEN b.bill_cash > 0 THEN b.bill_cash WHEN b.pay_mode = 'CASH' THEN b.bill_amount ELSE 0 END)
                WHEN pm.mode = 'UPI' THEN (CASE WHEN b.bill_upi > 0 THEN b.bill_upi WHEN b.pay_mode = 'UPI' THEN b.bill_amount ELSE 0 END)
                WHEN pm.mode = 'CARD' THEN (CASE WHEN b.bill_card > 0 THEN b.bill_card WHEN b.pay_mode = 'CARD' THEN b.bill_amount ELSE 0 END)
                WHEN pm.mode = 'CREDIT' THEN (CASE WHEN b.bill_credit > 0 THEN b.bill_credit WHEN b.pay_mode = 'CREDIT' THEN b.bill_amount ELSE 0 END)
                ELSE 0 END), 0) AS total_amount
        FROM (
            SELECT 'CASH' AS mode
            UNION ALL SELECT 'UPI'
            UNION ALL SELECT 'CARD'
            UNION ALL SELECT 'CREDIT'
        ) pm
        LEFT JOIN (
            SELECT 
                bill_number,
                MAX(pay_mode) as pay_mode,
                SUM(qty) as total_qty,
                SUM(amount) as bill_amount,
                MAX(CAST(NULLIF(pay_cash, '') AS DECIMAL(10,2))) as bill_cash,
                MAX(CAST(NULLIF(pay_upi, '') AS DECIMAL(10,2))) as bill_upi,
                MAX(CAST(NULLIF(pay_card, '') AS DECIMAL(10,2))) as bill_card,
                MAX(CAST(NULLIF(pay_credit, '') AS DECIMAL(10,2))) as bill_credit
            FROM tbl_bill
            WHERE $whereSql
            GROUP BY bill_number
        ) b ON (b.pay_mode = pm.mode OR (pm.mode = 'CASH' AND b.bill_cash > 0) OR (pm.mode = 'UPI' AND b.bill_upi > 0) OR (pm.mode = 'CARD' AND b.bill_card > 0) OR (pm.mode = 'CREDIT' AND b.bill_credit > 0))
        GROUP BY pm.mode
        ORDER BY FIELD(pm.mode, 'CASH', 'UPI', 'CARD', 'CREDIT')";

        $paymentCards = ApiDB::fetchAll($paymentCardsSql, $params);

        // Apply pagination
        $paginated = array_slice($allRows, $offset, $limit);
        foreach ($paginated as &$row) {
            $row['purchage_date_formatted'] = apiDateFormat($row['purchage_date']);
        }

        Response::success([
            'report_type'   => 'DSR-1 (Date-wise Summary)',
            'items'         => $paginated,
            'payment_cards' => $paymentCards,
            'summary'       => [
                'total_qty'    => $sumQty,
                'total_rate'   => round($sumRate, 2),
                'total_gst'    => round($sumGst, 2),
                'total_amount' => round($sumAmt, 2),
                'total_cash'   => round($sumCash, 2),
                'total_upi'    => round($sumUpi, 2),
                'total_card'   => round($sumCard, 2),
                'total_credit' => round($sumCredit, 2),
            ],
            'pagination'    => apiPaginate($totalRecords, $page, $limit),
        ], "Daily Sales Report (DSR-1) retrieved successfully.");
        break;

    case 'dsr2':
    handle_dsr2:
        // DSR-2: Detailed Item-wise Daily Sales Report
        $page = max((int)Request::input('page', 1), 1);
        $limit = max((int)Request::input('limit', 20), 1);
        $offset = ($page - 1) * $limit;
        $fromDate = Request::input('from_date');
        $toDate = Request::input('to_date');
        $filterDate = Request::input('filter_date', Request::input('date'));
        $recent = Request::input('recent');
        $search = trim((string)Request::input('search', ''));

        $where = ["1=1"];
        $params = [];

        if (!empty($recent)) {
            $where[] = "DATE(created_date) = :today";
            $params[':today'] = date('Y-m-d');
        } elseif (!empty($filterDate) && $filterDate !== 'all') {
            $where[] = "DATE(purchage_date) = :fdate";
            $params[':fdate'] = apiDateInSQLFormat($filterDate);
        }
        if (!empty($fromDate)) {
            $where[] = "DATE(purchage_date) >= :from_date";
            $params[':from_date'] = apiDateInSQLFormat($fromDate);
        }
        if (!empty($toDate)) {
            $where[] = "DATE(purchage_date) <= :to_date";
            $params[':to_date'] = apiDateInSQLFormat($toDate);
        }
        if ($search !== '') {
            $where[] = "(bill_number LIKE :s1 OR account_name LIKE :s2 OR barcode_number LIKE :s3 OR item_name LIKE :s4)";
            $params[':s1'] = "%$search%";
            $params[':s2'] = "%$search%";
            $params[':s3'] = "%$search%";
            $params[':s4'] = "%$search%";
        }

        $whereSql = implode(' AND ', $where);

        // Count total item records
        $totalRecords = (int)ApiDB::fetchColumn("SELECT COUNT(*) FROM tbl_bill WHERE $whereSql", $params);

        // Fetch detailed items
        $itemsSql = "SELECT 
                        id,
                        bill_number,
                        purchage_date,
                        created_date,
                        account_name,
                        mobile_number,
                        barcode_number,
                        article_no,
                        item_name,
                        item_type,
                        category,
                        brand,
                        color,
                        size,
                        qty,
                        rate,
                        purchase_price,
                        percent_discount,
                        discount_amt,
                        net_price,
                        gst,
                        gst_amount,
                        taxable_amount,
                        amount,
                        pay_mode,
                        pay_cash,
                        pay_upi,
                        pay_card,
                        pay_credit,
                        store_name,
                        remarks
                    FROM tbl_bill 
                    WHERE $whereSql
                    ORDER BY purchage_date DESC, id DESC
                    LIMIT $offset, $limit";

        $records = ApiDB::fetchAll($itemsSql, $params);
        foreach ($records as &$row) {
            $row['purchage_date_formatted'] = apiDateFormat($row['purchage_date']);
        }

        // Summary totals across entire filtered set
        $totalsRow = ApiDB::fetchOne("SELECT 
            COALESCE(SUM(qty), 0) as total_qty,
            COALESCE(SUM(CAST(COALESCE(NULLIF(taxable_amount, ''), NULLIF(net_price, ''), rate * qty, 0) AS DECIMAL(10,2))), 0) as total_rate,
            COALESCE(SUM(CAST(gst_amount AS DECIMAL(10,2))), 0) as total_gst,
            COALESCE(SUM(CAST(amount AS DECIMAL(10,2))), 0) as total_amount
        FROM tbl_bill 
        WHERE $whereSql", $params);

        // Payment Mode Summary Cards (matching filter)
        $paymentCardsSql = "SELECT 
            pm.mode AS payment_mode,
            COALESCE(SUM(CASE WHEN b.pay_mode = pm.mode OR (b.pay_mode = 'DUAL' AND ((pm.mode = 'CASH' AND b.bill_cash > 0) OR (pm.mode = 'UPI' AND b.bill_upi > 0) OR (pm.mode = 'CARD' AND b.bill_card > 0) OR (pm.mode = 'CREDIT' AND b.bill_credit > 0))) THEN b.total_qty ELSE 0 END), 0) AS total_qty,
            COALESCE(SUM(CASE 
                WHEN pm.mode = 'CASH' THEN (CASE WHEN b.bill_cash > 0 THEN b.bill_cash WHEN b.pay_mode = 'CASH' THEN b.bill_amount ELSE 0 END)
                WHEN pm.mode = 'UPI' THEN (CASE WHEN b.bill_upi > 0 THEN b.bill_upi WHEN b.pay_mode = 'UPI' THEN b.bill_amount ELSE 0 END)
                WHEN pm.mode = 'CARD' THEN (CASE WHEN b.bill_card > 0 THEN b.bill_card WHEN b.pay_mode = 'CARD' THEN b.bill_amount ELSE 0 END)
                WHEN pm.mode = 'CREDIT' THEN (CASE WHEN b.bill_credit > 0 THEN b.bill_credit WHEN b.pay_mode = 'CREDIT' THEN b.bill_amount ELSE 0 END)
                ELSE 0 END), 0) AS total_amount
        FROM (
            SELECT 'CASH' AS mode
            UNION ALL SELECT 'UPI'
            UNION ALL SELECT 'CARD'
            UNION ALL SELECT 'CREDIT'
        ) pm
        LEFT JOIN (
            SELECT 
                bill_number,
                MAX(pay_mode) as pay_mode,
                SUM(qty) as total_qty,
                SUM(amount) as bill_amount,
                MAX(CAST(NULLIF(pay_cash, '') AS DECIMAL(10,2))) as bill_cash,
                MAX(CAST(NULLIF(pay_upi, '') AS DECIMAL(10,2))) as bill_upi,
                MAX(CAST(NULLIF(pay_card, '') AS DECIMAL(10,2))) as bill_card,
                MAX(CAST(NULLIF(pay_credit, '') AS DECIMAL(10,2))) as bill_credit
            FROM tbl_bill
            WHERE $whereSql
            GROUP BY bill_number
        ) b ON (b.pay_mode = pm.mode OR (pm.mode = 'CASH' AND b.bill_cash > 0) OR (pm.mode = 'UPI' AND b.bill_upi > 0) OR (pm.mode = 'CARD' AND b.bill_card > 0) OR (pm.mode = 'CREDIT' AND b.bill_credit > 0))
        GROUP BY pm.mode
        ORDER BY FIELD(pm.mode, 'CASH', 'UPI', 'CARD', 'CREDIT')";

        $paymentCards = ApiDB::fetchAll($paymentCardsSql, $params);

        Response::success([
            'report_type'   => 'DSR-2 (Item-wise Detailed)',
            'items'         => $records,
            'payment_cards' => $paymentCards,
            'summary'       => [
                'total_qty'    => (float)($totalsRow['total_qty'] ?? 0),
                'total_rate'   => round((float)($totalsRow['total_rate'] ?? 0), 2),
                'total_gst'    => round((float)($totalsRow['total_gst'] ?? 0), 2),
                'total_amount' => round((float)($totalsRow['total_amount'] ?? 0), 2),
            ],
            'pagination'    => apiPaginate($totalRecords, $page, $limit),
        ], "Daily Sales Report (DSR-2) retrieved successfully.");
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

        // Fetch bill items
        $items = ApiDB::fetchAll("SELECT * FROM tbl_bill WHERE $whereClause ORDER BY id ASC", [':val' => $paramVal]);
        if (empty($items)) {
            Response::notFound("Bill not found.");
        }

        // Build header from first item
        $header = $items[0];
        $billHeader = [
            'bill_number'            => $header['bill_number'],
            'purchage_date'          => $header['purchage_date'],
            'purchage_date_formatted'=> apiDateFormat($header['purchage_date']),
            'bill_type'              => $header['bill_type'],
            'gst_type'               => $header['gst_type'],
            'under_gst'              => $header['under_gst'],
            'account_name'           => $header['account_name'],
            'mobile_number'          => $header['mobile_number'],
            'ac_code'                => $header['ac_code'],
            'pay_mode'               => $header['pay_mode'],
            'pay_cash'               => (float)($header['pay_cash'] ?? 0),
            'pay_upi'                => (float)($header['pay_upi'] ?? 0),
            'pay_card'               => (float)($header['pay_card'] ?? 0),
            'pay_credit'             => (float)($header['pay_credit'] ?? 0),
            'packing_amount'         => (float)($header['packing_amount'] ?? 0),
            'delivery_amount'        => (float)($header['delivery_amount'] ?? 0),
            'other_amount'           => (float)($header['other_amount'] ?? 0),
            'labour_amount'          => (float)($header['labour_amount'] ?? 0),
            'discount_total_amount'  => (float)($header['discount_total_amount'] ?? 0),
            'remarks'                => $header['remarks'],
            'status'                 => $header['status'],
            'store_name'             => $header['store_name'],
            'location'               => $header['location'],
            's_code'                 => $header['s_code'],
            'total_items'            => count($items),
            'total_qty'              => array_sum(array_column($items, 'qty')),
            'total_rate'             => round(array_sum(array_map(fn($it) => (float)($it['taxable_amount'] ?: ($it['rate'] * $it['qty'])), $items)), 2),
            'total_gst'              => round(array_sum(array_map(fn($it) => (float)($it['gst_amount'] ?? 0), $items)), 2),
            'total_amount'           => round(array_sum(array_map(fn($it) => (float)($it['amount'] ?? 0), $items)), 2),
        ];

        Response::success([
            'bill'  => $billHeader,
            'items' => $items,
        ], "Bill details retrieved.");
        break;

    case 'next_bill_no':
        $nextNo = apiGenerateBillNo('tbl_bill', 'bill_number', 'BC');
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

        $billNo = !empty($input['bill_number']) ? trim($input['bill_number']) : apiGenerateBillNo('tbl_bill', 'bill_number', 'BC');
        $purchageDate = apiDateInSQLFormat($input['purchage_date'] ?? date('Y-m-d'));
        $createdDate = date('Y-m-d H:i:s');

        // Extract header attributes
        $gstType    = $input['gst_type'] ?? 'Inclusive';
        $underGst   = $input['under_gst'] ?? 'Local';
        $billType   = $input['bill_type'] ?? 'Tax-Bill';
        $mobileNo   = $input['mobile_number'] ?? '';
        $accountName= $input['account_name'] ?? '';
        $acCode     = $input['ac_code'] ?? '';
        $payMode    = $input['pay_mode'] ?? 'CASH';
        $payCash    = $input['pay_cash'] ?? '0.00';
        $payUpi     = $input['pay_upi'] ?? '0.00';
        $payCard    = $input['pay_card'] ?? '0.00';
        $payCredit  = $input['pay_credit'] ?? '0.00';
        $packingAmt = $input['packing_amount'] ?? '0.00';
        $delivAmt   = $input['delivery_amount'] ?? '0.00';
        $otherAmt   = $input['other_amount'] ?? '0.00';
        $labourAmt  = $input['labour_amount'] ?? '0.00';
        $discTotAmt = $input['discount_total_amount'] ?? '0.00';
        $remarks    = $input['remarks'] ?? '';
        $status     = $input['status'] ?? 'success';
        $sCode      = $input['s_code'] ?? '';
        $storeName  = $input['store_name'] ?? '';
        $location   = $input['location'] ?? '';

        ApiDB::beginTransaction();
        try {
            $insertedIds = [];
            foreach ($items as $item) {
                $qty = (int)($item['qty'] ?? 1);
                $rate = (float)($item['rate'] ?? ($item['mrp'] ?? 0));
                $discAmt = (float)($item['discount_amt'] ?? 0);
                $netPrice = (float)($item['net_price'] ?? (($rate * $qty) - $discAmt));
                $gst = (float)($item['gst'] ?? 0);
                $gstAmt = (float)($item['gst_amount'] ?? 0);
                $taxableAmt = (float)($item['taxable_amount'] ?? ($netPrice));
                $amount = (float)($item['amount'] ?? ($taxableAmt + $gstAmt));

                $row = [
                    'purchage_date'          => $purchageDate,
                    'bill_number'            => $billNo,
                    'gst_type'               => $gstType,
                    'under_gst'              => $underGst,
                    'bill_type'              => $billType,
                    'mobile_number'          => $mobileNo,
                    'account_name'           => $accountName,
                    'ac_code'                => $acCode,
                    'barcode_number'         => $item['barcode_number'] ?? ($item['barcode_no'] ?? ''),
                    'article_no'             => $item['article_no'] ?? '',
                    'item_name'              => $item['item_name'] ?? 'Product',
                    'item_type'              => $item['item_type'] ?? '',
                    'category'               => $item['category'] ?? ($item['group_name'] ?? ''),
                    'brand'                  => $item['brand'] ?? '',
                    'color'                  => $item['color'] ?? '',
                    'size'                   => $item['size'] ?? '',
                    'stock_qty'              => (int)($item['stock_qty'] ?? 0),
                    'qty'                    => $qty,
                    'balance_qty'            => (int)($item['balance_qty'] ?? 0),
                    'mou_name'               => $item['mou_name'] ?? 'EA',
                    'rate'                   => $rate,
                    'purchase_price'         => (float)($item['purchase_price'] ?? $rate),
                    'percent_discount'       => (float)($item['percent_discount'] ?? 0),
                    'discount_amt'           => $discAmt,
                    'net_price'              => $netPrice,
                    'gst'                    => $gst,
                    'cgst'                   => round($gst / 2, 2),
                    'sgst'                   => round($gst / 2, 2),
                    'gst_amount'             => $gstAmt,
                    'taxable_amount'         => $taxableAmt,
                    'amount'                 => $amount,
                    'packing_amount'         => $packingAmt,
                    'delivery_amount'        => $delivAmt,
                    'other_amount'           => $otherAmt,
                    'labour_amount'          => $labourAmt,
                    'percent_total_discount' => (float)($input['percent_total_discount'] ?? 0),
                    'discount_total_amount'  => $discTotAmt,
                    'pay_mode'               => $payMode,
                    'pay_cash'               => $payCash,
                    'pay_upi'                => $payUpi,
                    'pay_card'               => $payCard,
                    'pay_credit'             => $payCredit,
                    'remarks'                => $remarks,
                    'status'                 => $status,
                    's_code'                 => $sCode,
                    'store_name'             => $storeName,
                    'location'               => $location,
                    'created_date'           => $createdDate,
                    'updated_date'           => $createdDate,
                ];

                $id = ApiDB::insert('tbl_bill', $row);
                $insertedIds[] = $id;
            }

            ApiDB::commit();

            Response::success([
                'bill_number'  => $billNo,
                'inserted_ids' => $insertedIds,
                'items_count'  => count($insertedIds),
            ], "Sale bill created successfully.", [], 201);
        } catch (Exception $e) {
            ApiDB::rollBack();
            Response::error("Failed to create bill: " . $e->getMessage(), 500);
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
            $deleted = ApiDB::delete('tbl_bill', 'bill_number = :bno', [':bno' => $billNo]);
        } else {
            // Also delete entire bill if item id is given
            $item = ApiDB::fetchOne("SELECT bill_number FROM tbl_bill WHERE id = :id", [':id' => $id]);
            if ($item && !empty($item['bill_number'])) {
                $deleted = ApiDB::delete('tbl_bill', 'bill_number = :bno', [':bno' => $item['bill_number']]);
            } else {
                $deleted = ApiDB::delete('tbl_bill', 'id = :id', [':id' => $id]);
            }
        }

        if ($deleted > 0) {
            Response::success(['affected_rows' => $deleted], "Sale bill deleted successfully.");
        } else {
            Response::notFound("Bill not found or already deleted.");
        }
        break;

    default:
        Response::error("Unknown action. Valid actions: recent, billwise, itemwise, dsr, details, next_bill_no, create, delete", 400);
        break;
}

