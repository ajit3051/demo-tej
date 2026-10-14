<style>
.button-btn {
    background-color: #2A3F54;
    border: none;
    color: white;
    padding: 4px 6px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 12px;
    cursor: pointer;
    border-radius: 25px;
}
.button-btn:hover {
    background-color: #009688;
}
.button-btn-btn {
    background-color: #009688;
    border: none;
    color: white;
    padding: 4px 20px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 12px;
    cursor: pointer;
    border-radius: 25px;
}
.button-btn-btn:hover {
    background-color: #2A3F54;
}

/* BREADCRUMB HEADER STYLES */
.tej-breadcrumb-header {
    background: #ffffff;
    padding: 12px 18px;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 5px;
    margin-bottom: 15px;
    width: 100%;
}
.tej-breadcrumb-title-box {
    display: flex;
    align-items: center;
    gap: 12px;
}
.tej-breadcrumb-icon {
    width: 40px;
    height: 40px;
    background: #e0f2f1;
    color: #009688;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    font-weight: bold;
    flex-shrink: 0;
}
.tej-breadcrumb-heading {
    margin: 0;
    font-size: 18px;
    color: #1e293b;
    font-weight: 700;
    line-height: 1.2;
}
.tej-breadcrumb-subheading {
    font-size: 12px;
    color: #64748b;
    font-weight: 500;
    margin-top: 2px;
    display: inline-block;
}
.tej-breadcrumb-nav {
    margin: 0;
    padding: 6px 12px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    font-size: 12px;
    display: inline-flex;
    align-items: center;
    list-style: none;
    flex-wrap: wrap;
}
.tej-breadcrumb-nav li {
    display: inline-flex;
    align-items: center;
    color: #475569;
}
.tej-breadcrumb-nav li + li:before {
    content: "/";
    padding: 0 6px;
    color: #94a3b8;
    font-size: 11px;
}
.tej-breadcrumb-nav li a {
    color: #475569;
    text-decoration: none;
    font-weight: 500;
}
.tej-breadcrumb-nav li a:hover {
    color: #009688;
}
.tej-breadcrumb-nav li.active {
    color: #009688;
    font-weight: 700;
}
</style>

<?php
if (!function_exists('render_breadcrumb')) {
    function render_breadcrumb() {
        $currentPath = $_SERVER['REQUEST_URI'] ?? $_SERVER['SCRIPT_NAME'] ?? $_SERVER['PHP_SELF'] ?? '';
        $pageName = basename(parse_url($currentPath, PHP_URL_PATH));
        $cleanPage = strtolower(preg_replace('/\.php$/i', '', $pageName));

        // Page Icon & Breadcrumb Trail Map
        $pageMap = [
            'profile' => ['fa fa-user-circle', 'Account Settings', 'User Profile', 'User Profile'],
            'fh_dashboard' => ['fa fa-home', 'ERP Master Management', 'Dashboard', 'Dashboard'],
            'fh_usermastercreation' => ['fa fa-user-plus', 'ERP Master Management', 'User Master', 'User Master Creation'],
            'fh_usermastercreation_list' => ['fa fa-users', 'ERP Master Management', 'User Master', 'User Master List'],
            'fh_usermastercreation_update' => ['fa fa-pencil-square-o', 'ERP Master Management', 'User Master', 'User Master Update'],
            'fh_storemastercreation' => ['fa fa-building', 'ERP Master Management', 'Store Master', 'Store Master Creation'],
            'fh_storemastercreation_list' => ['fa fa-list-alt', 'ERP Master Management', 'Store Master', 'Store Master List'],
            'fh_storemastercreation_update' => ['fa fa-pencil-square-o', 'ERP Master Management', 'Store Master', 'Store Master Update'],
            'fh_groupmastercreation' => ['fa fa-folder-open', 'Item Master', 'Category|Group Master', 'Category|Group Creation'],
            'fh_groupmastercreation_list' => ['fa fa-list', 'Item Master', 'Category|Group Master', 'Category|Group List'],
            'fh_subgroupmastercreation' => ['fa fa-folder-open-o', 'Item Master', 'SubCategory|SubGroup Master', 'SubCategory|SubGroup Creation'],
            'fh_subgroupmastercreation_list' => ['fa fa-list', 'Item Master', 'SubCategory|SubGroup Master', 'SubCategory|SubGroup List'],
            'fh_brandmastercreation' => ['fa fa-tags', 'Item Master', 'Brand Master', 'Brand Master Creation'],
            'fh_brandmastercreation_list' => ['fa fa-list', 'Item Master', 'Brand Master', 'Brand Master List'],
            'fh_colormastercreation' => ['fa fa-paint-brush', 'Item Master', 'Color Master', 'Color Master Creation'],
            'fh_colormastercreation_list' => ['fa fa-list', 'Item Master', 'Color Master', 'Color Master List'],
            'fh_sizemastercreation' => ['fa fa-arrows-alt', 'Item Master', 'Size Master', 'Size Master Creation'],
            'fh_sizemastercreation_list' => ['fa fa-list', 'Item Master', 'Size Master', 'Size Master List'],
            'fh_styledesignmastercreation' => ['fa fa-magic', 'Item Master', 'Style|Design Master', 'Style|Design Creation'],
            'fh_styledesignmastercreation_list' => ['fa fa-list', 'Item Master', 'Style|Design Master', 'Style|Design List'],
            'fh_itemmastercreation' => ['fa fa-cube', 'Item Master', 'Item Master', 'Item Master Creation'],
            'fh_itemmastercreation_list' => ['fa fa-cubes', 'Item Master', 'Item Master', 'Item Master List'],
            'fh_gstmastercreation' => ['fa fa-percent', 'GST Master Management', 'GST Master', 'GST Creation'],
            'fh_gstmastercreation_list' => ['fa fa-list', 'GST Master Management', 'GST Master', 'GST List'],
            'fh_paymodemastercreation' => ['fa fa-credit-card', 'Payment Mode Master Management', 'Payment Mode', 'Payment Mode Creation'],
            'fh_paymodemastercreation_list' => ['fa fa-list', 'Payment Mode Master Management', 'Payment Mode', 'Payment Mode List'],
            'fh_discountmastercreation' => ['fa fa-gift', 'Discount Master Management', 'Discount Master', 'Discount Master Creation'],
            'fh_discountmastercreation_list' => ['fa fa-list', 'Discount Master Management', 'Discount Master', 'Discount Master List'],
            'fh_festivalmastercreation' => ['fa fa-star', 'Discount Master Management', 'Festival Master', 'Festival Master Creation'],
            'fh_festivalmastercreation_list' => ['fa fa-list', 'Discount Master Management', 'Festival Master', 'Festival Master List'],
            'fh_pointmastercreation' => ['fa fa-database', 'Discount Master Management', 'Point Master', 'Point Master Creation'],
            'fh_pointmastercreation_list' => ['fa fa-list', 'Discount Master Management', 'Point Master', 'Point Master List'],
            'fh_couponmastercreation' => ['fa fa-ticket', 'Discount Master Management', 'Coupon Master', 'Coupon Master Creation'],
            'fh_couponmastercreation_list' => ['fa fa-list', 'Discount Master Management', 'Coupon Master', 'Coupon Master List'],
            'purchase-master-creation' => ['fa fa-shopping-cart', 'Inventory | Purchase Master', 'Purchase', 'Purchase Creation'],
            'purchase-master-billwise-list' => ['fa fa-file-text-o', 'Inventory | Purchase Master', 'Purchase', 'Purchase BillWise List'],
            'purchase-master-itemwise-list' => ['fa fa-list', 'Inventory | Purchase Master', 'Purchase', 'Purchase ItemWise List'],
            'sale-creations' => ['fa fa-shopping-bag', 'Bill | Sale Management', 'Bill | Sale', 'Bill | Sale Creation'],
            'sale-billwise-list' => ['fa fa-file-text-o', 'Bill | Sale Management', 'Bill | Sale', 'Sale BillWise List'],
            'sale-itemwise-list' => ['fa fa-list', 'Bill | Sale Management', 'Bill | Sale', 'Sale ItemWise List'],
            'tejaserp-sale-billwise-list' => ['fa fa-file-text-o', 'Bill | Sale Management', 'Bill | Sale', 'Sale BillWise List'],
            'tejaserp-sale-itemwise-list' => ['fa fa-list', 'Bill | Sale Management', 'Bill | Sale', 'Sale ItemWise List'],
            'tejaserp-sale-dsr-1' => ['fa fa-bar-chart', 'Bill || Sale Management', 'Bill || Sale', 'DSR-1'],
            'tejaserp-sale-dsr-2' => ['fa fa-line-chart', 'Bill || Sale Management', 'Bill || Sale', 'DSR-2'],
            'fh_expenses_headmastercreation' => ['fa fa-money', 'Accounts Master Management', 'Expense Head Master', 'Expense Head Master Creation'],
            'fh_expensesheadmastercreation_list' => ['fa fa-list', 'Accounts Master Management', 'Expense Head Master', 'Expense Head Master List'],
            'fh_customertypemastercreation' => ['fa fa-id-card', 'Accounts Master Management', 'Customer Type Master', 'Customer Type Master Creation'],
            'fh_customertypemastercreation_list' => ['fa fa-list', 'Accounts Master Management', 'Customer Type Master', 'Customer Type Master List'],
            'fh_partytypemastercreation' => ['fa fa-handshake-o', 'Accounts Master Management', 'Party Type', 'Party Type Creation'],
            'fh_partytypemastercreation_list' => ['fa fa-list', 'Accounts Master Management', 'Party Type', 'Party Type List'],
            'fh_accountmastercreation' => ['fa fa-book', 'Accounts Master Management', 'Account Master', 'Account Master Creation'],
            'fh_accountmastercreation_list' => ['fa fa-list', 'Accounts Master Management', 'Account Master', 'Account Master List'],
            'fh_expensesmastercreation' => ['fa fa-calculator', 'Accounts Master Management', 'Expense Entry', 'Expense Entry Creation'],
            'fh_expensesmastercreation_list' => ['fa fa-list', 'Accounts Master Management', 'Expense Entry', 'Expense Entry List'],
            'fh_paymentmastercreation' => ['fa fa-inr', 'Accounts Master Management', 'Payment Entry', 'Payment Entry Creation'],
            'fh_paymentmastercreation_list' => ['fa fa-list', 'Accounts Master Management', 'Payment Entry', 'Payment Entry List'],
            'fh_salesmanmastercreation' => ['fa fa-user-secret', 'CRM Management', 'SalesMan Master', 'SalesMan Master Creation'],
            'fh_salesmanmastercreation_list' => ['fa fa-list', 'CRM Management', 'SalesMan Master', 'SalesMan Master List'],
            'fh_salesman_target_mastercreation' => ['fa fa-bullseye', 'CRM Management', 'SalesMan Target Master', 'SalesMan Target Master Creation'],
            'fh_salesman_target_mastercreation_list' => ['fa fa-list', 'CRM Management', 'SalesMan Target Master', 'SalesMan Target Master List'],
            'tejaserp-stockreport-inshortcut' => ['fa fa-pie-chart', 'Reports Management', 'Stock Report', 'Stock Report (ShortCut)'],
            'tejaserp-stockreport-indetails' => ['fa fa-table', 'Reports Management', 'Stock Report', 'Stock Report (In Details)'],
            'tejaserp-inventorybarcodeprint' => ['fa fa-barcode', 'Barcode Master Management', 'Barcode Print', 'Barcode Print'],
            'tejaserp-inventorybarcodeprint-list' => ['fa fa-list', 'Barcode Master Management', 'Barcode Print', 'Barcode Print List'],
            'tejaserp-inventorybarcodeprint-re-print' => ['fa fa-print', 'Barcode Master Management', 'Barcode Re Print', 'Barcode Re Print'],
            'tejaserp-stocktransfer-creation' => ['fa fa-exchange', 'Stock Transfer Management', 'Stock Transfer', 'Stock Transfer Creation']
        ];

        if (isset($pageMap[$cleanPage])) {
            $data = $pageMap[$cleanPage];
            $icon = $data[0];
            $category = $data[1];
            $subcategory = $data[2];
            $pageTitle = $data[3];
        } else {
            $icon = 'fa fa-th-large';
            $title = ucwords(str_replace(['fh_', '_', '-'], ' ', $cleanPage));
            $category = 'Management';
            $subcategory = 'Master';
            $pageTitle = $title;
        }

        echo '<div class="col-xs-12" style="padding: 0;">';
        echo '  <div class="tej-breadcrumb-header">';
        echo '      <div class="tej-breadcrumb-title-box">';
        echo '          <div class="tej-breadcrumb-icon"><i class="' . htmlspecialchars($icon) . '"></i></div>';
        echo '          <div>';
        echo '              <h1 class="tej-breadcrumb-heading">' . htmlspecialchars($pageTitle) . '</h1>';
        echo '              <span class="tej-breadcrumb-subheading">' . htmlspecialchars($category);
        if ($subcategory && $subcategory !== $category && $subcategory !== $pageTitle) {
            echo ' &bull; ' . htmlspecialchars($subcategory);
        }
        echo '              </span>';
        echo '          </div>';
        echo '      </div>';
        echo '      <div>';
        echo '          <ul class="tej-breadcrumb-nav">';
        echo '              <li><a href="fh_dashboard.php"><i class="fa fa-home" style="color: #009688; margin-right: 4px;"></i> Home</a></li>';
        echo '              <li><a href="#">' . htmlspecialchars($category) . '</a></li>';
        if ($subcategory && $subcategory !== $category && $subcategory !== $pageTitle) {
            echo '          <li><a href="#">' . htmlspecialchars($subcategory) . '</a></li>';
        }
        echo '              <li class="active">' . htmlspecialchars($pageTitle) . '</li>';
        echo '          </ul>';
        echo '      </div>';
        echo '  </div>';
        echo '</div>';
    }
}
render_breadcrumb();
?>
