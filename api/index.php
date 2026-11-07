<?php
/**
 * Master API Gateway & Router
 * Completely self-contained under api/
 */
require_once __DIR__ . '/config.php';

// Parse route from REQUEST_URI or 'route' / 'endpoint' parameter
$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$path = parse_url($requestUri, PHP_URL_PATH);

// Remove project base path if present
$basePath = '/demo-tej/api';
if (strpos($path, $basePath) === 0) {
    $path = substr($path, strlen($basePath));
} elseif (strpos($path, '/api') === 0) {
    $path = substr($path, 4);
}

$path = trim($path, '/');
$segments = $path !== '' ? explode('/', $path) : [];

// If index.php is the first segment, remove it
if (!empty($segments) && $segments[0] === 'index.php') {
    array_shift($segments);
}

// Module routing
$module = $segments[0] ?? Request::get('endpoint', Request::get('module', ''));
$subAction = $segments[1] ?? null;

if (!empty($subAction) && !isset($_GET['action'])) {
    $_GET['action'] = $subAction;
}

// For masters routing: /api/masters/{entity}/{action}
if ($module === 'masters' && isset($segments[1])) {
    $_GET['entity'] = $segments[1];
    if (isset($segments[2]) && !isset($_GET['action'])) {
        $_GET['action'] = $segments[2];
    }
}

// Route to module
switch ($module) {
    case 'auth':
        require __DIR__ . '/auth.php';
        exit;

    case 'sales':
        require __DIR__ . '/sales.php';
        exit;

    case 'purchases':
        require __DIR__ . '/purchases.php';
        exit;

    case 'items':
        require __DIR__ . '/items.php';
        exit;

    case 'accounts':
        require __DIR__ . '/accounts.php';
        exit;

    case 'stock':
        require __DIR__ . '/stock.php';
        exit;

    case 'masters':
        require __DIR__ . '/masters.php';
        exit;

    case 'dashboard':
        require __DIR__ . '/dashboard.php';
        exit;

    case '':
        // Return full API documentation and directory
        Response::success([
            'api_name' => 'Tejas ERP RESTful API',
            'version' => '1.0.0',
            'status' => 'online',
            'timestamp' => date('Y-m-d H:i:s'),
            'base_url' => 'http://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . '/demo-tej/api',
            'modules' => [
                'auth' => [
                    'description' => 'User authentication & JWT session management',
                    'endpoints' => [
                        'POST /auth.php?action=login' => 'Authenticate user (username, password)',
                        'POST /auth.php?action=logout' => 'Clear token/session',
                        'GET  /auth.php?action=me' => 'Get current authenticated user profile',
                        'POST /auth.php?action=change_password' => 'Update password (old_password, new_password)'
                    ]
                ],
                'sales' => [
                    'description' => 'Sales billing, Day-wise reports, and DSR',
                    'endpoints' => [
                        'GET  /sales.php?action=recent' => 'Recent sales (filter_date, search, page, limit)',
                        'GET  /sales.php?action=billwise' => 'Billwise sales list with totals (from_date, to_date)',
                        'GET  /sales.php?action=itemwise' => 'Itemwise sales transactions',
                        'GET  /sales.php?action=dsr' => 'Daily Sales Report (DSR) with rate calculations',
                        'GET  /sales.php?action=details' => 'Full bill header and item list (bill_no)',
                        'GET  /sales.php?action=next_bill_no' => 'Generate next sequential bill number',
                        'POST /sales.php?action=create' => 'Create new bill with multiple items',
                        'POST /sales.php?action=delete' => 'Delete bill and items (bill_no)'
                    ]
                ],
                'purchases' => [
                    'description' => 'Purchase bills and supplier inward transactions',
                    'endpoints' => [
                        'GET  /purchases.php?action=list' => 'List purchase bills with totals',
                        'GET  /purchases.php?action=itemwise' => 'Itemwise purchase rows',
                        'GET  /purchases.php?action=details' => 'Purchase bill header and items (bill_no)',
                        'GET  /purchases.php?action=next_bill_no' => 'Generate next purchase bill number',
                        'POST /purchases.php?action=create' => 'Create purchase bill & update store stock',
                        'POST /purchases.php?action=delete' => 'Delete purchase bill (bill_no)'
                    ]
                ],
                'items' => [
                    'description' => 'Item master catalog, barcodes & stock lookups',
                    'endpoints' => [
                        'GET  /items.php?action=list' => 'Item list with search and pagination',
                        'GET  /items.php?action=get' => 'Get item by ID, item_code, or barcode',
                        'GET  /items.php?action=lookup' => 'Barcode POS lookup with live stock',
                        'GET  /items.php?action=generate_barcode' => 'Generate next barcode',
                        'POST /items.php?action=create' => 'Create new item',
                        'POST /items.php?action=update' => 'Update item details',
                        'POST /items.php?action=delete' => 'Delete item'
                    ]
                ],
                'accounts' => [
                    'description' => 'Customer/Vendor ledger and account master',
                    'endpoints' => [
                        'GET  /accounts.php?action=list' => 'List customer and vendor accounts',
                        'GET  /accounts.php?action=search' => 'Autocomplete search by name or mobile',
                        'GET  /accounts.php?action=get' => 'Get account by ID or code',
                        'GET  /accounts.php?action=ledger' => 'Running ledger balance & statement',
                        'POST /accounts.php?action=create' => 'Create new account',
                        'POST /accounts.php?action=update' => 'Update account',
                        'POST /accounts.php?action=delete' => 'Delete account'
                    ]
                ],
                'stock' => [
                    'description' => 'Inventory reports, store balances & transfers',
                    'endpoints' => [
                        'GET  /stock.php?action=report_shortcut' => 'Purchased, Sold & Balance quantity',
                        'GET  /stock.php?action=report_details' => 'Item-level detailed stock ledger',
                        'GET  /stock.php?action=store_items' => 'Live store items stock (store_id, item_code)',
                        'GET  /stock.php?action=transfers' => 'Stock transfer history',
                        'POST /stock.php?action=transfer' => 'Execute stock transfer between stores'
                    ]
                ],
                'masters' => [
                    'description' => 'Master data CRUD (brands, groups, sizes, colors, stores, etc.)',
                    'entities' => ['brands', 'groups', 'categories', 'subgroups', 'colors', 'sizes', 'gst', 'paymodes', 'stores', 'store_profiles', 'locations', 'expenses_heads', 'expenses', 'coupons', 'discounts', 'salesmen', 'salesman_targets', 'customer_types', 'party_types', 'styles', 'festivals', 'users', 'company'],
                    'endpoints' => [
                        'GET  /masters.php?entity={entity}&action=list' => 'List master records',
                        'GET  /masters.php?entity={entity}&action=get' => 'Get master record by id or code',
                        'POST /masters.php?entity={entity}&action=create' => 'Create new master record',
                        'POST /masters.php?entity={entity}&action=update' => 'Update master record',
                        'POST /masters.php?entity={entity}&action=delete' => 'Delete master record'
                    ]
                ],
                'dashboard' => [
                    'description' => 'Business KPI metrics and executive summary',
                    'endpoints' => [
                        'GET  /dashboard.php?action=summary' => 'Today/Month/All-time sales, purchases, counts & low stock'
                    ]
                ]
            ]
        ], 'Tejas ERP API is operational');
        break;

    default:
        Response::notFound("Endpoint '$module' not found. Visit /demo-tej/api/ for API directory.");
        break;
}

