<?php
/**
 * Dashboard & Analytics API
 * Completely self-contained under api/
 */
require_once __DIR__ . '/config.php';

// Strict authentication enforcement
$authUser = Auth::requireAuth();

$action = Request::get('action', 'summary');

switch ($action) {
    case 'summary':
    default:
        $today = date('Y-m-d');
        $thisMonthStart = date('Y-m-01');
        $thisMonthEnd = date('Y-m-t');

        // 1. Sales Summary (Grouped by bill_number)
        $todaySales = ApiDB::fetchOne(
            "SELECT COUNT(DISTINCT bill_number) as bill_count, 
                    COALESCE(SUM(bill_amount), 0) as total_amount,
                    COALESCE(SUM(pay_cash), 0) as cash_amount,
                    COALESCE(SUM(pay_upi), 0) as upi_amount,
                    COALESCE(SUM(pay_card), 0) as card_amount,
                    COALESCE(SUM(pay_credit), 0) as credit_amount
             FROM (
                 SELECT bill_number, 
                        SUM(amount) as bill_amount,
                        MAX(pay_cash) as pay_cash,
                        MAX(pay_upi) as pay_upi,
                        MAX(pay_card) as pay_card,
                        MAX(pay_credit) as pay_credit
                 FROM tbl_bill
                 WHERE DATE(purchage_date) = ?
                 GROUP BY bill_number
             ) as b",
            [$today]
        );

        $monthSales = ApiDB::fetchOne(
            "SELECT COUNT(DISTINCT bill_number) as bill_count, 
                    COALESCE(SUM(bill_amount), 0) as total_amount
             FROM (
                 SELECT bill_number, SUM(amount) as bill_amount
                 FROM tbl_bill
                 WHERE DATE(purchage_date) BETWEEN ? AND ?
                 GROUP BY bill_number
             ) as b",
            [$thisMonthStart, $thisMonthEnd]
        );

        $totalSales = ApiDB::fetchOne(
            "SELECT COUNT(DISTINCT bill_number) as bill_count, 
                    COALESCE(SUM(bill_amount), 0) as total_amount
             FROM (
                 SELECT bill_number, SUM(amount) as bill_amount
                 FROM tbl_bill
                 GROUP BY bill_number
             ) as b"
        );

        // 2. Purchases Summary (Grouped by bill_number)
        $todayPurchases = ApiDB::fetchOne(
            "SELECT COUNT(DISTINCT bill_number) as bill_count, 
                    COALESCE(SUM(bill_amount), 0) as total_amount
             FROM (
                 SELECT bill_number, SUM(amount) as bill_amount
                 FROM tbl_purchase
                 WHERE DATE(purchage_date) = ?
                 GROUP BY bill_number
             ) as p",
            [$today]
        );

        $monthPurchases = ApiDB::fetchOne(
            "SELECT COUNT(DISTINCT bill_number) as bill_count, 
                    COALESCE(SUM(bill_amount), 0) as total_amount
             FROM (
                 SELECT bill_number, SUM(amount) as bill_amount
                 FROM tbl_purchase
                 WHERE DATE(purchage_date) BETWEEN ? AND ?
                 GROUP BY bill_number
             ) as p",
            [$thisMonthStart, $thisMonthEnd]
        );

        // 3. Inventory & Accounts counts
        $itemsCount = ApiDB::fetchOne("SELECT COUNT(*) as total_items FROM tbl_item_master");
        $customersCount = ApiDB::fetchOne("SELECT COUNT(*) as total_customers FROM tbl_account_master");
        $vendorsCount = ApiDB::fetchOne("SELECT COUNT(*) as total_vendors FROM tbl_account_master WHERE party_type LIKE '%vendor%' OR party_type LIKE '%supplier%'");

        // Stock quantity summary from tbl_store_items
        $stockSummary = ApiDB::fetchOne(
            "SELECT COALESCE(SUM(qty), 0) as total_units_in_stock 
             FROM tbl_store_items"
        );

        // 4. Low stock items (items where stock qty <= 5)
        $lowStock = ApiDB::fetchAll(
            "SELECT i.id, i.item_code, i.item_name, i.barcode_no as barcode, COALESCE(s.stock_qty, 0) as current_stock
             FROM tbl_item_master i
             LEFT JOIN (
                 SELECT barcode_number, SUM(qty) as stock_qty
                 FROM tbl_store_items
                 GROUP BY barcode_number
             ) s ON s.barcode_number = i.barcode_no
             WHERE COALESCE(s.stock_qty, 0) <= 5
             ORDER BY current_stock ASC
             LIMIT 10"
        );

        // 5. Recent Sales (last 5)
        $recentSales = ApiDB::fetchAll(
            "SELECT bill_number as bill_no, 
                    DATE(purchage_date) as bill_date, 
                    account_name as customer_name, 
                    mobile_number as customer_mobile, 
                    SUM(amount) as bill_amount, 
                    MAX(pay_mode) as paymode,
                    MAX(created_date) as created_at
             FROM tbl_bill
             GROUP BY bill_number, DATE(purchage_date), account_name, mobile_number
             ORDER BY MAX(id) DESC
             LIMIT 5"
        );

        // 6. Recent Purchases (last 5)
        $recentPurchases = ApiDB::fetchAll(
            "SELECT bill_number as bill_no, 
                    DATE(purchage_date) as bill_date, 
                    account_name as vendor_name, 
                    SUM(amount) as bill_amount, 
                    MAX(created_date) as created_at
             FROM tbl_purchase
             GROUP BY bill_number, DATE(purchage_date), account_name
             ORDER BY MAX(id) DESC
             LIMIT 5"
        );

        Response::success([
            'sales' => [
                'today' => [
                    'date' => $today,
                    'bills' => (int)($todaySales['bill_count'] ?? 0),
                    'amount' => (float)($todaySales['total_amount'] ?? 0),
                    'cash' => (float)($todaySales['cash_amount'] ?? 0),
                    'upi' => (float)($todaySales['upi_amount'] ?? 0),
                    'card' => (float)($todaySales['card_amount'] ?? 0),
                    'credit' => (float)($todaySales['credit_amount'] ?? 0)
                ],
                'month' => [
                    'bills' => (int)($monthSales['bill_count'] ?? 0),
                    'amount' => (float)($monthSales['total_amount'] ?? 0)
                ],
                'all_time' => [
                    'bills' => (int)($totalSales['bill_count'] ?? 0),
                    'amount' => (float)($totalSales['total_amount'] ?? 0)
                ]
            ],
            'purchases' => [
                'today' => [
                    'bills' => (int)($todayPurchases['bill_count'] ?? 0),
                    'amount' => (float)($todayPurchases['total_amount'] ?? 0)
                ],
                'month' => [
                    'bills' => (int)($monthPurchases['bill_count'] ?? 0),
                    'amount' => (float)($monthPurchases['total_amount'] ?? 0)
                ]
            ],
            'counts' => [
                'total_items' => (int)($itemsCount['total_items'] ?? 0),
                'total_customers' => (int)($customersCount['total_customers'] ?? 0),
                'total_vendors' => (int)($vendorsCount['total_vendors'] ?? 0),
                'total_stock_units' => (float)($stockSummary['total_units_in_stock'] ?? 0)
            ],
            'low_stock_items' => $lowStock,
            'recent_sales' => $recentSales,
            'recent_purchases' => $recentPurchases
        ], 'Dashboard summary retrieved successfully');
        break;
}
