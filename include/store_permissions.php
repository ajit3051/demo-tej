<?php
/**
 * Store-Wise Permission and Context Management
 * Tejas ERP
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Returns complete catalog of ERP modules and their pages.
 */
function get_erp_modules_catalog(): array
{
    return [
        'erp_masters' => [
            'label' => 'ERP Master Management',
            'icon'  => 'fa fa-users',
            'sections' => [
                'user_master' => [
                    'label' => 'User Master',
                    'icon'  => 'fa fa-user-circle',
                    'pages' => [
                        'fh_usermastercreation.php'      => 'User Master Creation',
                        'fh_usermastercreation_list.php' => 'User Master List',
                    ]
                ],
                'user_permission' => [
                    'label' => 'Store & User Permission',
                    'icon'  => 'fa fa-key',
                    'pages' => [
                        'fh_store_permission.php'        => 'Store Menu Permissions',
                    ]
                ],
                'store_master' => [
                    'label' => 'Store Master',
                    'icon'  => 'fa fa-building',
                    'pages' => [
                        'fh_storemastercreation.php'      => 'Store Master Creation',
                        'fh_storemastercreation_list.php' => 'Store Master List',
                    ]
                ],
                'store_profile' => [
                    'label' => 'Store Profile',
                    'icon'  => 'fa fa-id-card',
                    'pages' => [
                        'fh_storeprofilecreation.php'      => 'Store Profile Creation',
                        'fh_storeprofilecreation_list.php' => 'Store Profile List',
                    ]
                ]
            ]
        ],
        'item_masters' => [
            'label' => 'Item Master Management',
            'icon'  => 'fa fa-cube',
            'sections' => [
                'category_group' => [
                    'label' => 'Category / Group Master',
                    'icon'  => 'fa fa-tags',
                    'pages' => [
                        'fh_groupmastercreation.php'      => 'Category/Group Creation',
                        'fh_groupmastercreation_list.php' => 'Category/Group List',
                    ]
                ],
                'subcategory_group' => [
                    'label' => 'SubCategory / SubGroup Master',
                    'icon'  => 'fa fa-tag',
                    'pages' => [
                        'fh_subgroupmastercreation.php'      => 'SubCategory Creation',
                        'fh_subgroupmastercreation_list.php' => 'SubCategory List',
                    ]
                ],
                'brand_master' => [
                    'label' => 'Brand Master',
                    'icon'  => 'fa fa-bookmark',
                    'pages' => [
                        'fh_brandmastercreation.php'      => 'Brand Master Creation',
                        'fh_brandmastercreation_list.php' => 'Brand Master List',
                    ]
                ],
                'color_master' => [
                    'label' => 'Color Master',
                    'icon'  => 'fa fa-paint-brush',
                    'pages' => [
                        'fh_colormastercreation.php'      => 'Color Master Creation',
                        'fh_colormastercreation_list.php' => 'Color Master List',
                    ]
                ],
                'size_master' => [
                    'label' => 'Size Master',
                    'icon'  => 'fa fa-expand',
                    'pages' => [
                        'fh_sizemastercreation.php'      => 'Size Master Creation',
                        'fh_sizemastercreation_list.php' => 'Size Master List',
                    ]
                ],
                'style_design_master' => [
                    'label' => 'Style / Design Master',
                    'icon'  => 'fa fa-scissors',
                    'pages' => [
                        'fh_styledesignmastercreation.php'      => 'Style/Design Creation',
                        'fh_styledesignmastercreation_list.php' => 'Style/Design List',
                    ]
                ],
                'item_master' => [
                    'label' => 'Item Master',
                    'icon'  => 'fa fa-boxes',
                    'pages' => [
                        'fh_itemmastercreation.php'      => 'Item Master Creation',
                        'fh_itemmastercreation_list.php' => 'Item Master List',
                    ]
                ]
            ]
        ],
        'gst_management' => [
            'label' => 'GST Master Management',
            'icon'  => 'fa fa-percent',
            'sections' => [
                'gst_master' => [
                    'label' => 'GST Master',
                    'icon'  => 'fa fa-percent',
                    'pages' => [
                        'fh_gstmastercreation.php'      => 'GST Master Creation',
                        'fh_gstmastercreation_list.php' => 'GST Master List',
                    ]
                ]
            ]
        ],
        'payment_management' => [
            'label' => 'Payment Mode Management',
            'icon'  => 'fa fa-credit-card',
            'sections' => [
                'paymode_master' => [
                    'label' => 'Payment Mode',
                    'icon'  => 'fa fa-credit-card',
                    'pages' => [
                        'fh_paymodemastercreation.php'      => 'Payment Mode Creation',
                        'fh_paymodemastercreation_list.php' => 'Payment Mode List',
                    ]
                ]
            ]
        ],
        'discount_management' => [
            'label' => 'Discount Master Management',
            'icon'  => 'fa fa-gift',
            'sections' => [
                'festival_master' => [
                    'label' => 'Festival Master',
                    'icon'  => 'fa fa-calendar',
                    'pages' => [
                        'fh_festivalmastercreation.php'      => 'Festival Master Creation',
                        'fh_festivalmastercreation_list.php' => 'Festival Master List',
                    ]
                ],
                'point_master' => [
                    'label' => 'Point Master',
                    'icon'  => 'fa fa-star',
                    'pages' => [
                        'fh_pointmastercreation.php'      => 'Point Master Creation',
                        'fh_pointmastercreation_list.php' => 'Point Master List',
                    ]
                ],
                'coupon_master' => [
                    'label' => 'Coupon Master',
                    'icon'  => 'fa fa-ticket',
                    'pages' => [
                        'fh_couponmastercreation.php'      => 'Coupon Master Creation',
                        'fh_couponmastercreation_list.php' => 'Coupon Master List',
                        'fh_couponmastercreation_view.php' => 'Coupon Master View',
                    ]
                ],
                'discount_master' => [
                    'label' => 'Discount Master',
                    'icon'  => 'fa fa-percent',
                    'pages' => [
                        'fh_discountmastercreation.php'      => 'Discount Master Creation',
                        'fh_discountmastercreation_list.php' => 'Discount Master List',
                    ]
                ]
            ]
        ],
        'inventory_purchase' => [
            'label' => 'Inventory | Purchase Management',
            'icon'  => 'fa fa-cubes',
            'sections' => [
                'purchase_order' => [
                    'label' => 'Purchase Order (PO)',
                    'icon'  => 'fa fa-cart-plus',
                    'pages' => [
                        'purchase-master-creation.php'          => 'Purchase Order Creation',
                        'purchase-master-billwise-list.php'    => 'Purchase BillWise List',
                        'purchase-master-itemwise-list.php'    => 'Purchase ItemWise List',
                        'purchase-master-report.php'           => 'Purchase Report',
                    ]
                ]
            ]
        ],
        'barcode_management' => [
            'label' => 'Barcode Master Management',
            'icon'  => 'fa fa-barcode',
            'sections' => [
                'barcode_print' => [
                    'label' => 'Barcode Print',
                    'icon'  => 'fa fa-barcode',
                    'pages' => [
                        'tejaserp-inventorybarcodeprint.php'          => 'Barcode Print',
                        'tejaserp-inventorybarcodeprint-list.php'     => 'Barcode Print List',
                        'tejaserp-inventorybarcodeprint-re-print.php' => 'Barcode Re-Print',
                        'tejaserp-inventorybarcodeprint-report.php'   => 'Barcode Report',
                    ]
                ]
            ]
        ],
        'stock_transfer' => [
            'label' => 'Stock Transfer Management',
            'icon'  => 'fa fa-exchange',
            'sections' => [
                'transfer' => [
                    'label' => 'Stock Transfer',
                    'icon'  => 'fa fa-truck',
                    'pages' => [
                        'stock-transfer.php'           => 'Stock Transfer Creation',
                        'store-items-list.php'         => 'Store Items List',
                        'returned-store-items-list.php'=> 'Returned Store Items List',
                    ]
                ]
            ]
        ],
        'bill_sale' => [
            'label' => 'Bill | Sale Management',
            'icon'  => 'fa fa-file-text-o',
            'sections' => [
                'billing' => [
                    'label' => 'Billing & Sales',
                    'icon'  => 'fa fa-shopping-cart',
                    'pages' => [
                        'sale-creations.php'              => 'Sale / Bill Creation',
                        'tejaserp-sale-billwise-list.php' => 'Sale BillWise List',
                        'tejaserp-sale-itemwise-list.php' => 'Sale ItemWise List',
                        'tejaserp-sale-dsr-1.php'         => 'Sale DSR-1 Report',
                        'tejaserp-sale-dsr-2.php'         => 'Sale DSR-2 Report',
                    ]
                ]
            ]
        ],
        'accounts_management' => [
            'label' => 'Accounts Master Management',
            'icon'  => 'fa fa-book',
            'sections' => [
                'expense_head' => [
                    'label' => 'Expense Head Master',
                    'icon'  => 'fa fa-list',
                    'pages' => [
                        'fh_expenses_headmastercreation.php'     => 'Expense Head Creation',
                        'fh_expensesheadmastercreation_list.php' => 'Expense Head List',
                    ]
                ],
                'customer_type' => [
                    'label' => 'Customer Type Master',
                    'icon'  => 'fa fa-user-tag',
                    'pages' => [
                        'fh_customertypemastercreation.php'      => 'Customer Type Creation',
                        'fh_customertypemastercreation_list.php' => 'Customer Type List',
                    ]
                ],
                'party_type' => [
                    'label' => 'Party Type Master',
                    'icon'  => 'fa fa-handshake-o',
                    'pages' => [
                        'fh_partytypemastercreation.php'      => 'Party Type Creation',
                        'fh_partytypemastercreation_list.php' => 'Party Type List',
                    ]
                ],
                'account_master' => [
                    'label' => 'Account Master',
                    'icon'  => 'fa fa-address-book-o',
                    'pages' => [
                        'fh_accountmastercreation.php'      => 'Account Master Creation',
                        'fh_accountmastercreation_list.php' => 'Account Master List',
                    ]
                ],
                'expense_entry' => [
                    'label' => 'Expense Entry',
                    'icon'  => 'fa fa-money',
                    'pages' => [
                        'fh_expensesmastercreation.php'      => 'Expense Entry Creation',
                        'fh_expensesmastercreation_list.php' => 'Expense Entry List',
                    ]
                ],
                'payment_entry' => [
                    'label' => 'Payment Entry',
                    'icon'  => 'fa fa-credit-card',
                    'pages' => [
                        'fh_paymentmastercreation.php'      => 'Payment Entry Creation',
                        'fh_paymentmastercreation_list.php' => 'Payment Entry List',
                    ]
                ],
                'ledger' => [
                    'label' => 'Ledger & Balance Sheet',
                    'icon'  => 'fa fa-balance-scale',
                    'pages' => [
                        'fh_expensesmasterbalancesheet.php' => 'Expenses Balance Sheet',
                    ]
                ]
            ]
        ],
        'crm_management' => [
            'label' => 'CRM Management',
            'icon'  => 'fa fa-address-book',
            'sections' => [
                'salesman_master' => [
                    'label' => 'SalesMan Master',
                    'icon'  => 'fa fa-user-tie',
                    'pages' => [
                        'fh_SalesManmastercreation.php'          => 'SalesMan Creation',
                        'fh_SalesManmastercreation_list.php'     => 'SalesMan List',
                        'fh_Salesman_target_mastercreation.php'      => 'SalesMan Target Creation',
                        'fh_Salesman_target_mastercreation_list.php' => 'SalesMan Target List',
                    ]
                ]
            ]
        ],
        'reports_management' => [
            'label' => 'Reports Management',
            'icon'  => 'fa fa-bar-chart',
            'sections' => [
                'stock_report' => [
                    'label' => 'Stock Report',
                    'icon'  => 'fa fa-chart-line',
                    'pages' => [
                        'tejaserp-stockreport-indetails.php'  => 'Stock Report in Detail',
                        'tejaserp-stockreport-inshortcut.php' => 'Stock Report in Shortcut',
                    ]
                ]
            ]
        ],
        'website_management' => [
            'label' => 'WebSite Management',
            'icon'  => 'fa fa-globe',
            'sections' => [
                'homepage' => [
                    'label' => 'HomePage & Order Manage',
                    'icon'  => 'fa fa-laptop',
                    'pages' => [
                        'fh_web_1strowcreation.php'      => 'HomePage 1st Row Creation',
                        'fh_web_1strowcreation_list.php' => 'HomePage 1st Row List',
                    ]
                ]
            ]
        ]
    ];
}

/**
 * Initializes and gets the active store context.
 */
function get_active_store(): array
{
    global $conn;
    if (!isset($conn) || !($conn instanceof mysqli)) {
        require_once(__DIR__ . '/config.php');
        $conn = connect();
    }

    // Handle explicit store switch (via GET/POST by Admin/Super Admin)
    $userType = strtolower(trim((string)($_SESSION['user_type'] ?? '')));
    $isAdmin = in_array($userType, ['super admin', 'admin'], true);

    if (isset($_GET['switch_store']) && $isAdmin) {
        $reqStore = trim((string)$_GET['switch_store']);
        if ($reqStore === 'ALL') {
            $_SESSION['active_store'] = 'ALL';
            $_SESSION['active_store_code'] = 'ALL';
            $_SESSION['active_store_id'] = 0;
        } else {
            $stmt = mysqli_prepare($conn, "SELECT id, store_code, store_name FROM tbl_store_master WHERE store_code = ? OR store_name = ? LIMIT 1");
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, 'ss', $reqStore, $reqStore);
                mysqli_stmt_execute($stmt);
                $res = mysqli_stmt_get_result($stmt);
                if ($row = mysqli_fetch_assoc($res)) {
                    $_SESSION['active_store'] = $row['store_name'];
                    $_SESSION['active_store_code'] = $row['store_code'];
                    $_SESSION['active_store_id'] = (int)$row['id'];
                }
            }
        }
        // Redirect cleanly to remove ?switch_store
        $currentUri = preg_replace('/(\?|&)switch_store=[^&]*/', '', $_SERVER['REQUEST_URI'] ?? '');
        if (empty($currentUri)) $currentUri = 'fh_dashboard.php';
        header("Location: $currentUri");
        exit();
    }

    // Default if not set in session
    if (empty($_SESSION['active_store'])) {
        $userStoreCode = trim((string)($_SESSION['store_code'] ?? ''));
        $userStoreName = trim((string)($_SESSION['store_name'] ?? ''));

        if (!empty($userStoreCode) && $userStoreCode !== 'null') {
            $stmt = mysqli_prepare($conn, "SELECT id, store_code, store_name FROM tbl_store_master WHERE store_code = ? LIMIT 1");
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, 's', $userStoreCode);
                mysqli_stmt_execute($stmt);
                $res = mysqli_stmt_get_result($stmt);
                if ($row = mysqli_fetch_assoc($res)) {
                    $_SESSION['active_store'] = $row['store_name'];
                    $_SESSION['active_store_code'] = $row['store_code'];
                    $_SESSION['active_store_id'] = (int)$row['id'];
                }
            }
        } elseif (!empty($userStoreName) && $userStoreName !== 'null') {
            $stmt = mysqli_prepare($conn, "SELECT id, store_code, store_name FROM tbl_store_master WHERE store_name = ? LIMIT 1");
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, 's', $userStoreName);
                mysqli_stmt_execute($stmt);
                $res = mysqli_stmt_get_result($stmt);
                if ($row = mysqli_fetch_assoc($res)) {
                    $_SESSION['active_store'] = $row['store_name'];
                    $_SESSION['active_store_code'] = $row['store_code'];
                    $_SESSION['active_store_id'] = (int)$row['id'];
                }
            }
        } elseif ($isAdmin) {
            $_SESSION['active_store'] = 'ALL';
            $_SESSION['active_store_code'] = 'ALL';
            $_SESSION['active_store_id'] = 0;
        } else {
            $_SESSION['active_store'] = '';
            $_SESSION['active_store_code'] = '';
            $_SESSION['active_store_id'] = 0;
        }
    }

    // Resolve store id and code if needed
    if (!isset($_SESSION['active_store_id']) || $_SESSION['active_store_id'] === 0) {
        if (($_SESSION['active_store'] ?? '') === 'ALL') {
            $_SESSION['active_store_id'] = 0;
            $_SESSION['active_store_code'] = 'ALL';
        } elseif (!empty($_SESSION['active_store'])) {
            $stmt = mysqli_prepare($conn, "SELECT id, store_code FROM tbl_store_master WHERE store_name = ? OR store_code = ? LIMIT 1");
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, 'ss', $_SESSION['active_store'], $_SESSION['active_store']);
                mysqli_stmt_execute($stmt);
                $res = mysqli_stmt_get_result($stmt);
                if ($row = mysqli_fetch_assoc($res)) {
                    $_SESSION['active_store_id'] = (int)$row['id'];
                    $_SESSION['active_store_code'] = $row['store_code'];
                }
            }
        }
    }

    return [
        'store_code' => $_SESSION['active_store_code'] ?? '',
        'store_name' => $_SESSION['active_store'] ?? '',
        'store_id'   => (int)($_SESSION['active_store_id'] ?? 0),
        'is_admin'   => $isAdmin
    ];
}

/**
 * Loads all permitted page URLs for a specific store from tbl_store_menu_permissions.
 * Cached per request in static array.
 */
function get_store_permissions(int $storeId): array
{
    global $conn;
    static $cache = [];

    if (isset($cache[$storeId])) {
        return $cache[$storeId];
    }

    if ($storeId <= 0) {
        $cache[$storeId] = [];
        return [];
    }

    if (!isset($conn) || !($conn instanceof mysqli)) {
        require_once(__DIR__ . '/config.php');
        $conn = connect();
    }

    $perms = [];
    $stmt = mysqli_prepare($conn, "SELECT page_url, can_view FROM tbl_store_menu_permissions WHERE store_id = ?");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'i', $storeId);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        while ($row = mysqli_fetch_assoc($res)) {
            $perms[$row['page_url']] = (int)$row['can_view'];
        }
    }

    $cache[$storeId] = $perms;
    return $perms;
}

/**
 * Checks whether a given page URL is accessible for the current active store.
 */
function has_store_page_access(string $pageUrl, ?int $storeId = null): bool
{
    $cleanPage = strtolower(trim(basename(explode('?', $pageUrl)[0])));

    // Pages that are universally accessible by all authenticated users
    $alwaysAllowed = [
        'fh_dashboard.php',
        'fh_dashboard-old.php',
        'profile.php',
        'logout.php',
        'login.php',
        'index.php'
    ];

    if (in_array($cleanPage, $alwaysAllowed, true)) {
        return true;
    }

    $userType = strtolower(trim((string)($_SESSION['user_type'] ?? '')));
    $isAdmin = in_array($userType, ['super admin', 'admin'], true);

    // Permission setup page is always accessible to Admin
    if ($cleanPage === 'fh_store_permission.php' && $isAdmin) {
        return true;
    }

    $active = get_active_store();
    $activeStoreName = $active['store_name'];
    $currentStoreId = ($storeId !== null) ? $storeId : $active['store_id'];

    // Super Admin with ALL stores selected has full access
    if ($isAdmin && $activeStoreName === 'ALL') {
        return true;
    }

    // If storeId is 0 or empty and user is Admin, allow
    if ($currentStoreId <= 0) {
        return $isAdmin;
    }

    $perms = get_store_permissions($currentStoreId);

    // If store has NO configured permissions yet, default to allow for Admin
    if (empty($perms)) {
        return $isAdmin ? true : false;
    }

    // If explicitly configured for this page
    if (isset($perms[$cleanPage])) {
        return $perms[$cleanPage] === 1;
    }

    // Default deny if permissions have been configured for store but this page isn't in it
    return false;
}

/**
 * Checks whether any of the given pages is accessible.
 */
function has_any_page_access(array $pages, ?int $storeId = null): bool
{
    foreach ($pages as $p) {
        if (has_store_page_access($p, $storeId)) {
            return true;
        }
    }
    return false;
}

/**
 * Enforces store page access guard for the current executing page.
 */
function enforce_store_page_access(): void
{
    $currentScript = strtolower(trim(basename($_SERVER['SCRIPT_NAME'] ?? $_SERVER['PHP_SELF'] ?? '')));
    
    // Ignore AJAX / API / CLI / include files
    if (strpos($_SERVER['REQUEST_URI'] ?? '', '/ajax/') !== false || 
        strpos($_SERVER['REQUEST_URI'] ?? '', '/api/') !== false ||
        empty($currentScript) || 
        php_sapi_name() === 'cli') {
        return;
    }

    if (!has_store_page_access($currentScript)) {
        $active = get_active_store();
        $storeLabel = !empty($active['store_name']) ? $active['store_name'] : 'Current Store';
        
        $msg = "Access Denied: You do not have permission to access '{$currentScript}' for store '{$storeLabel}'.";
        $_SESSION['showMessages'] = $msg;
        $_SESSION['errorMessages'] = 1;
        
        // Prevent infinite loop if already on dashboard
        if ($currentScript !== 'fh_dashboard.php') {
            header("Location: fh_dashboard.php?error=unauthorized_store_page");
            exit();
        }
    }
}
