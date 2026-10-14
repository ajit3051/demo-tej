<?php
$currentScript = basename($_SERVER['SCRIPT_NAME'] ?? $_SERVER['PHP_SELF'] ?? '');
$cleanCurrentPage = strtolower(preg_replace('/\.php$/i', '', $currentScript));
?>

<style>
/* MODERN SIDEBAR MENU LAYOUT & DISTINCT 3-LEVEL ACTIVE COLOR PALETTE */
.sidebar {
    background-color: #1a252f;
    font-family: inherit;
}

.sidebar-menu {
    list-style: none;
    margin: 0;
    padding: 0;
}

.sidebar-menu li {
    position: relative !important;
    margin: 0;
    padding: 0;
}

.sidebar-menu > li {
    border-bottom: 1px solid rgba(255, 255, 255, 0.05);
}

/* Ensure all menu links use flex layout, relative positioning, and fill width */
.sidebar-menu li a {
    position: relative !important;
    display: flex !important;
    align-items: center !important;
    text-decoration: none !important;
    width: 100% !important;
    box-sizing: border-box !important;
    transition: all 0.2s ease;
}

.sidebar-menu > li > a {
    padding: 11px 15px;
    color: #cbd5e1;
    font-size: 13.5px;
    font-weight: 500;
}

.sidebar-menu li a > i:first-child {
    width: 22px;
    font-size: 15px;
    color: #009688;
    margin-right: 8px;
    text-align: center;
    flex-shrink: 0;
}

.sidebar-menu li a > span:not(.pull-right-container) {
    flex: 1 1 auto !important;
    line-height: 1.3;
    padding-right: 25px !important; /* Avoid overlap with right angle icon */
}

.sidebar-menu > li:hover > a {
    background-color: #243442;
    color: #ffffff;
}

/* FONTAWESOME ICON & ANGLE ARROW RENDERING & POSITIONING FIX */
.sidebar-menu .fa,
.sidebar-menu .fas,
.sidebar-menu .fa-solid {
    font-family: "Font Awesome 6 Free", "FontAwesome" !important;
    font-weight: 900 !important;
}

.sidebar-menu li a .pull-right-container {
    position: absolute !important;
    right: 12px !important;
    top: 50% !important;
    transform: translateY(-50%) !important;
    margin-top: 0 !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    flex-shrink: 0 !important;
    width: 20px !important;
    height: 20px !important;
    overflow: visible !important;
    z-index: 5 !important;
    pointer-events: none !important;
}

.sidebar-menu li a .pull-right-container .fa-angle-left,
.sidebar-menu li a .pull-right-container .fa-angle-down,
.sidebar-menu li a .fa-angle-left,
.sidebar-menu li a .fa-angle-down {
    font-family: "Font Awesome 6 Free", "FontAwesome" !important;
    font-weight: 900 !important;
    display: inline-block !important;
    width: 14px !important;
    height: 14px !important;
    line-height: 14px !important;
    text-align: center !important;
    transform-origin: center center !important;
    font-size: 13px !important;
    color: #cbd5e1 !important;
    visibility: visible !important;
    opacity: 1 !important;
    transition: transform 0.25s ease-in-out, color 0.2s ease !important;
}

/* Closed menu arrow state */
.sidebar-menu li:not(.menu-open) > a .pull-right-container .fa-angle-left,
.sidebar-menu li:not(.menu-open) > a .pull-right-container .fa-angle-down,
.sidebar-menu li:not(.menu-open) > a > .fa-angle-left,
.sidebar-menu li:not(.menu-open) > a > .fa-angle-down {
    -webkit-transform: rotate(0deg) !important;
    transform: rotate(0deg) !important;
    color: #cbd5e1 !important;
}

/* Level 0 Active/Open Arrow */
.sidebar-menu > li.menu-open > a .pull-right-container .fa-angle-left,
.sidebar-menu > li.menu-open > a > .fa-angle-left {
    -webkit-transform: rotate(-90deg) !important;
    transform: rotate(-90deg) !important;
    color: #ffffff !important;
}

/* --- LEVEL 0: TOP LEVEL PARENT (ERP Master Management, Item Master, etc.) --- */
.sidebar-menu > li.active > a,
.sidebar-menu > li.menu-open > a {
    background-color: #009688 !important; /* Emerald Teal */
    color: #ffffff !important;
    font-weight: 600 !important;
}

.sidebar-menu > li.active > a > i:first-child,
.sidebar-menu > li.menu-open > a > i:first-child {
    color: #ffffff !important;
}

/* Submenu Containers */
.sidebar-menu .treeview-menu {
    list-style: none;
    padding: 4px 0;
    margin: 0;
    background-color: #0f172a !important;
    border-left: 3px solid #009688;
}

.sidebar-menu .treeview-menu .treeview-menu {
    background-color: #090d16 !important;
    border-left: 2px solid #38bdf8;
    padding-left: 5px;
}

/* Submenu Default Items */
.sidebar-menu .treeview-menu > li > a {
    padding: 8px 12px 8px 22px;
    color: #94a3b8;
    font-size: 13px;
    font-weight: 400;
    border-left: 3px solid transparent;
}

.sidebar-menu .treeview-menu > li:hover > a {
    color: #ffffff;
    background-color: rgba(255, 255, 255, 0.05);
}

/* --- LEVEL 1: FIRST SUB-PARENT (User Master, Store Master, Category|Group Master, etc.) --- */
.sidebar-menu > li > .treeview-menu > li.active > a,
.sidebar-menu > li > .treeview-menu > li.menu-open > a {
    background-color: #1e3a8a !important; /* Deep Royal Navy Blue */
    color: #ffffff !important;
    font-weight: 600 !important;
    border-left: 4px solid #38bdf8 !important; /* Bright Sky Cyan Left Bar */
    box-shadow: inset 0 0 5px rgba(0,0,0,0.3);
}

.sidebar-menu > li > .treeview-menu > li.active > a > i:first-child,
.sidebar-menu > li > .treeview-menu > li.menu-open > a > i:first-child {
    color: #38bdf8 !important;
}

.sidebar-menu > li > .treeview-menu > li.menu-open > a .pull-right-container .fa-angle-left,
.sidebar-menu > li > .treeview-menu > li.menu-open > a > .fa-angle-left {
    color: #38bdf8 !important; /* Bright Sky Cyan Arrow on Level 1 Sub-Parent */
    -webkit-transform: rotate(-90deg) !important;
    transform: rotate(-90deg) !important;
}

/* --- LEVEL 2: SECOND SUB / LEAF SELECTED ITEM (User Master Creation, User Master List, etc.) --- */
.sidebar-menu .treeview-menu .treeview-menu > li.active > a {
    background-color: #2563eb !important; /* Vibrant Electric Royal Blue */
    color: #ffffff !important;
    font-weight: 700 !important;
    border-left: 4px solid #ffeb3b !important; /* Vibrant Gold Left Accent Bar */
    padding-left: 26px !important;
    box-shadow: 0 2px 6px rgba(0,0,0,0.35);
}

/* Single-level sub-menu active leaf items */
.sidebar-menu > li > .treeview-menu > li.active:not(.treeview) > a {
    background-color: #2563eb !important;
    color: #ffffff !important;
    font-weight: 700 !important;
    border-left: 4px solid #ffeb3b !important;
    padding-left: 25px !important;
    box-shadow: 0 2px 6px rgba(0,0,0,0.35);
}
</style>

<div class="sidebar">
   <!-- sidebar menu -->
   <ul class="sidebar-menu">
      <li>
         <a href="fh_dashboard.php"><i class="hvr-buzz-out fa fa-home"></i><span>Dashboard</span>
         <span class="pull-right-container"></span>
         </a>
      </li>
      <li class="treeview">
         <a href="#">
            <i class="fa fa-users"></i><span>ERP Master Management</span>
            <span class="pull-right-container">
               <i class="fa fa-angle-left pull-right"></i>
            </span>
         </a>
         <ul class="treeview-menu">
            <li class="treeview">
               <a href="#">
                  <i class="fa fa-user-circle"></i><span>User Master</span>
                  <span class="pull-right-container">
                     <i class="fa fa-angle-left pull-right"></i>
                  </span>
               </a>
               <ul class="treeview-menu">
                  <li><a href="fh_usermastercreation.php">User Master Creation</a></li>
                  <li><a href="fh_usermastercreation_list.php">User Master List</a></li>
               </ul>
            </li>
            <li>
               <a href="#"><i class="fa fa-key"></i><span>User Permission</span></a>
            </li>
            <li class="treeview">
               <a href="#">
                  <i class="fa fa-building"></i><span>Store Master</span>
                  <span class="pull-right-container">
                     <i class="fa fa-angle-left pull-right"></i>
                  </span>
               </a>
               <ul class="treeview-menu">
                  <li><a href="fh_storemastercreation.php">Store Master Creation</a></li>
                  <li><a href="fh_storemastercreation_list.php">Store Master List</a></li>
               </ul>
            </li>
            
         </ul>
      </li>
      
      <li class="treeview">
         <a href="#">
            <i class="fa fa-cube"></i><span>Item Master Management</span>
            <span class="pull-right-container">
               <i class="fa fa-angle-left pull-right"></i>
            </span>
         </a>
         <ul class="treeview-menu">
            <li class="treeview">
               <a href="#"><span>Category|Group Master</span>
               <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
               </span>
               </a>
               <ul class="treeview-menu">
                  <li><a href="fh_groupmastercreation.php">Category|Group Creation</a></li>
                  <li><a href="fh_groupmastercreation_list.php">Category|Group List</a></li>
               </ul>
            </li>
            
            <li class="treeview">
               <a href="#"><span>SubCategory|SubGroup Master</span>
               <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
               </span>
               </a>
               <ul class="treeview-menu">
                  <li><a href="fh_subgroupmastercreation.php">SubCategory|SubGroup Creation</a></li>
                  <li><a href="fh_subgroupmastercreation_list.php">SubCategory|SubGroup List</a></li>
               </ul>
            </li>
            <li class="treeview">
               <a href="#"><span>Brand Master</span>
               <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
               </span>
               </a>
               <ul class="treeview-menu">
                  <li><a href="fh_brandmastercreation.php">Brand Master Creation</a></li>
                  <li><a href="fh_brandmastercreation_list.php">Brand Master List</a></li>
               </ul>
            </li>
            <li class="treeview">
               <a href="#"><span>Color Master</span>
               <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
               </span>
               </a>
               <ul class="treeview-menu">
                  <li><a href="fh_colormastercreation.php">Color Master Creation</a></li>
                  <li><a href="fh_colormastercreation_list.php">Color Master List</a></li>
               </ul>
            </li>
            <li class="treeview">
               <a href="#"><span>Size Master</span>
               <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
               </span>
               </a>
               <ul class="treeview-menu">
                  <li><a href="fh_sizemastercreation.php">Size Master Creation</a></li>
                  <li><a href="fh_sizemastercreation_list.php">Size Master List</a></li>
               </ul>
            </li>
            <li class="treeview">
               <a href="#"><span>Style|Design Master</span>
               <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
               </span>
               </a>
               <ul class="treeview-menu">
                  <li><a href="fh_styledesignmastercreation.php">Style|Design Creation</a></li>
                  <li><a href="fh_styledesignmastercreation_list.php">Style|Design List</a></li>
               </ul>
            </li>
            
            <li class="treeview">
               <a href="#"><span>Item Master</span>
               <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
               </span>
               </a>
               <ul class="treeview-menu">
                  <li><a href="fh_itemmastercreation.php">Item Master Creation</a></li>
                  <li><a href="fh_itemmastercreation_list.php">Item Master List</a></li>
               </ul>
            </li>
         </ul>
      </li>

      <li class="treeview">
         <a href="#">
            <i class="fa fa-percent"></i><span>GST Master Management</span>
            <span class="pull-right-container">
               <i class="fa fa-angle-left pull-right"></i>
            </span>
         </a>
         <ul class="treeview-menu">
            <li class="treeview">
               <a href="#"><span>GST Master</span>
               <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
               </span>
               </a>
               <ul class="treeview-menu" >
                  <li><a href="fh_gstmastercreation.php">GST Creation</a></li>
                  <li><a href="fh_gstmastercreation_list.php">GST List</a></li>
               </ul>
            </li>
            <li class="treeview">
               <a href="#"><span>GST Reports</span>
               <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
               </span>
               </a>
               <ul class="treeview-menu" >
                  <li><a href="fh_storeprofilecreation.php">GST Report 1</a></li>
                  <li><a href="fh_storeprofilecreation_list.php">GST Report 2</a></li>
               </ul>
            </li>
         </ul>
      </li>

      <li class="treeview">
         <a href="#">
            <i class="fa fa-credit-card"></i><span>Payment Mode Management</span>
            <span class="pull-right-container">
               <i class="fa fa-angle-left pull-right"></i>
            </span>
         </a>
         <ul class="treeview-menu" >
            <li><a href="fh_paymodemastercreation.php">Payment Mode Creation</a></li>
            <li><a href="fh_paymodemastercreation_list.php">Payment Mode List</a></li>
         </ul>
      </li>

      <li class="treeview">
         <a href="#">
            <i class="fa fa-tags"></i><span>Rate Master Management</span>
            <span class="pull-right-container">
               <i class="fa fa-angle-left pull-right"></i>
            </span>
         </a>
         <ul class="treeview-menu" >
            <li><a href="ssenterprises_usermastercreation.php">Rate Master Creation</a></li>
            <li><a href="ssenterprises_usermastercreation_list.php">Rate Master List</a></li>
         </ul>
      </li>

      <li class="treeview">
         <a href="#">
            <i class="fa fa-gift"></i><span>Discount Master Management</span>
            <span class="pull-right-container">
               <i class="fa fa-angle-left pull-right"></i>
            </span>
         </a>
         <ul class="treeview-menu">
            <li class="treeview">
               <a href="#"><span>Festival Master</span>
               <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
               </span>
               </a>
               <ul class="treeview-menu" >
                  <li><a href="fh_festivalmastercreation.php">Festival Master Creation</a></li>
                  <li><a href="fh_festivalmastercreation_list.php">Festival Master List</a></li>
               </ul>
            </li>
            <li class="treeview">
               <a href="#"><span>Point Master</span>
               <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
               </span>
               </a>
               <ul class="treeview-menu" >
                  <li><a href="fh_pointmastercreation.php">Point Master Creation</a></li>
                  <li><a href="fh_pointmastercreation_list.php">Point Master List</a></li>
               </ul>
            </li>
            <li class="treeview">
               <a href="#"><span>Coupon Master</span>
               <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
               </span>
               </a>
               <ul class="treeview-menu" >
                  <li><a href="fh_couponmastercreation.php">Coupon Master Creation</a></li>
                  <li><a href="fh_couponmastercreation_list.php">Coupon Master List</a></li>
                  <li><a href="fh_couponmastercreation_view.php">Coupon Master View</a></li>
               </ul>
            </li>
            <li class="treeview">
               <a href="#"><span>Discount Master</span>
               <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
               </span>
               </a>
               <ul class="treeview-menu" >
                  <li><a href="fh_discountmastercreation.php">Discount Master Creation</a></li>
                  <li><a href="fh_discountmastercreation_list.php">Discount Master List</a></li>
               </ul>
            </li>
         </ul>
      </li>

      <li class="treeview">
         <a href="#">
            <i class="fa fa-cubes"></i><span>Inventory|Purchase Management</span>
            <span class="pull-right-container">
               <i class="fa fa-angle-left pull-right"></i>
            </span>
         </a>
         <ul class="treeview-menu">
            <li class="treeview">
               <a href="#"><span>Inventory || Purchase Order (PO)</span>
               <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
               </span>
               </a>
               <ul class="treeview-menu" >
                  <li><a href="fh_festivalmastercreation.php">Purchase Order (PO) Creation</a></li>
                  <li><a href="fh_festivalmastercreation_list.php">Purchase Order (PO) BillWise List</a></li>
               </ul>
            </li>
            <li class="treeview">
               <a href="#"><span>Inventory || Purchase</span>
               <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
               </span>
               </a>
               <ul class="treeview-menu" >
                  <li><a href="purchase-master-creation.php">Purchase Creation</a></li>
                  <li><a href="purchase-master-billwise-list.php">Purchase BillWise List</a></li>
                  <li><a href="purchase-master-itemwise-list.php">Purchase ItemWise List</a></li>
                  <li><a href="purchase-master-report.php">Purchase Report</a></li>
               </ul>
            </li>
            <li class="treeview">
               <a href="#"><span>Inventory || Purchase Return</span>
               <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
               </span>
               </a>
               <ul class="treeview-menu" >
                  <li><a href="fh_couponmastercreation.php">Purchase Return Creation</a></li>
                  <li><a href="fh_couponmastercreation_list.php">Purchase Return BillWise List</a></li>
                  <li><a href="fh_couponmastercreation_view.php">Purchase Return ItemWise List</a></li>
               </ul>
            </li>
         </ul>
      </li>

      <li class="treeview">
         <a href="#">
            <i class="fa fa-barcode"></i><span>Barcode Master Management</span>
            <span class="pull-right-container">
               <i class="fa fa-angle-left pull-right"></i>
            </span>
         </a>
         <ul class="treeview-menu">
            <li class="treeview">
               <a href="#"><span>Barcode Print</span>
               <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
               </span>
               </a>
               <ul class="treeview-menu" >
                  <li><a href="tejaserp-inventorybarcodeprint.php">Purchase Barcode Print</a></li>
                  <li><a href="tejaserp-inventorybarcodeprint-list.php">Purchase Barcode Print List</a></li>
                  <li><a href="tejaserp-inventorybarcodeprint-report.php">Purchase Barcode Print Report</a></li>
               </ul>
            </li>
            <li class="treeview">
               <a href="#"><span>Barcode Re Print</span>
               <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
               </span>
               </a>
               <ul class="treeview-menu" >
                  <li><a href="tejaserp-inventorybarcodeprint-re-print.php">Purchase Barcode Re Print</a></li>
               </ul>
            </li>
            <li class="treeview">
               <a href="#"><span>Regular Barcode Print</span>
               <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
               </span>
               </a>
               <ul class="treeview-menu" >
                  <li><a href="fh_couponmastercreation.php">Regular Barcode Print</a></li>
                  <li><a href="fh_couponmastercreation_list.php">Regular Barcode Print List</a></li>
               </ul>
            </li>
         </ul>
      </li>

      <li class="treeview">
         <a href="#">
            <i class="fa fa-exchange"></i><span>Stock Transfer Management</span>
            <span class="pull-right-container">
               <i class="fa fa-angle-left pull-right"></i>
            </span>
         </a>
         <ul class="treeview-menu">
            <li class="treeview">
               <a href="#"><span>Stock Transfer</span>
               <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
               </span>
               </a>
               <ul class="treeview-menu" >
                  <li><a href="store-items-list.php">Store Items</a></li>
               </ul>
            </li>
            <li class="treeview">
               <a href="#"><span>Stock Transfer Return</span>
               <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
               </span>
               </a>
               <ul class="treeview-menu" >
                  <li><a href="returned-store-items-list.php">Returned Store Items</a></li>
               </ul>
            </li>
         </ul>
      </li>

      <li class="treeview">
         <a href="#">
            <i class="fa fa-file-text-o"></i><span>Bill || Sale Management</span>
            <span class="pull-right-container">
               <i class="fa fa-angle-left pull-right"></i>
            </span>
         </a>
         <ul class="treeview-menu">
            <li class="treeview">
               <a href="#"><span>Bill || Sale</span>
               <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
               </span>
               </a>
               <ul class="treeview-menu" >
                  <li><a href="sale-creations.php">Bill || Sale Creation</a></li>
                  <li><a href="tejaserp-sale-billwise-list.php">Sale BillWise List</a></li>
                  <li><a href="tejaserp-sale-itemwise-list.php">Sale ItemWise List</a></li>
                  <li><a href="tejaserp-sale-dsr-1.php">DSR-1</a></li>
                  <li><a href="tejaserp-sale-dsr-2.php">DSR-2</a></li>
               </ul>
            </li>
         </ul>
      </li>

      <li class="treeview">
         <a href="#">
            <i class="fa fa-book"></i><span>Accounts Master Management</span>
            <span class="pull-right-container">
               <i class="fa fa-angle-left pull-right"></i>
            </span>
         </a>
         <ul class="treeview-menu">
            <li class="treeview">
               <a href="#"><span>Expense Head Master</span>
               <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
               </span>
               </a>
               <ul class="treeview-menu" >
                  <li><a href="fh_expenses_headmastercreation.php">Expense Head Master Creation</a></li>
                  <li><a href="fh_expensesheadmastercreation_list.php">Expense Head Master List</a></li>
               </ul>
            </li>
            <li class="treeview">
               <a href="#"><span>Customer Type Master</span>
               <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
               </span>
               </a>
               <ul class="treeview-menu" >
                  <li><a href="fh_customertypemastercreation.php">Customer Type Master Creation</a></li>
                  <li><a href="fh_customertypemastercreation_list.php">Customer Type Master List</a></li>
               </ul>
            </li>
            <li class="treeview">
               <a href="#"><span>Party Type</span>
               <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
               </span>
               </a>
               <ul class="treeview-menu" >
                  <li><a href="fh_partytypemastercreation.php">Party Type Creation</a></li>
                  <li><a href="fh_partytypemastercreation_list.php">Party Type List</a></li>
               </ul>
            </li>
            <li class="treeview">
               <a href="#"><span>Account Master</span>
               <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
               </span>
               </a>
               <ul class="treeview-menu" >
                  <li><a href="fh_accountmastercreation.php">Account Master Creation</a></li>
                  <li><a href="fh_accountmastercreation_list.php">Account Master List</a></li>
               </ul>
            </li>
            <li class="treeview">
               <a href="#"><span>Expense Entry</span>
               <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
               </span>
               </a>
               <ul class="treeview-menu" >
                  <li><a href="fh_expensesmastercreation.php">Expense Entry Creation</a></li>
                  <li><a href="fh_expensesmastercreation_list.php">Expense Entry List</a></li>
                  <li><a href="fh_expensesmasterbalancesheet.php">Expense Entry Balance Sheet</a></li>
               </ul>
            </li>
            <li class="treeview">
               <a href="#"><span>Payment Entry</span>
               <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
               </span>
               </a>
               <ul class="treeview-menu" >
                  <li><a href="fh_paymentmastercreation.php">Payment Entry Creation</a></li>
                  <li><a href="fh_paymentmastercreation_list.php">Payment Entry List</a></li>
               </ul>
            </li>
            <li>
               <a href="#"><span>Ledger</span></a>
            </li>
         </ul>
      </li>

      <li class="treeview">
         <a href="#">
            <i class="fa fa-address-book"></i><span>CRM Management</span>
            <span class="pull-right-container">
               <i class="fa fa-angle-left pull-right"></i>
            </span>
         </a>
         <ul class="treeview-menu">
            <li class="treeview">
               <a href="#"><span>SalesMan Master</span>
               <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
               </span>
               </a>
               <ul class="treeview-menu" >
                  <li><a href="fh_SalesManmastercreation.php">SalesMan Master Creation</a></li>
                  <li><a href="fh_SalesManmastercreation_list.php">SalesMan Master List</a></li>
               </ul>
            </li>
            <li class="treeview">
               <a href="#"><span>SalesMan Target Master</span>
               <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
               </span>
               </a>
               <ul class="treeview-menu" >
                  <li><a href="fh_Salesman_target_mastercreation.php">SalesMan Target Master Creation</a></li>
                  <li><a href="fh_Salesman_target_mastercreation_list.php">SalesMan Target Master List</a></li>
               </ul>
            </li>
         </ul>
      </li>

      <li class="treeview">
         <a href="#">
            <i class="fa fa-bar-chart"></i><span>Reports Management</span>
            <span class="pull-right-container">
               <i class="fa fa-angle-left pull-right"></i>
            </span>
         </a>
         <ul class="treeview-menu">
            <li class="treeview">
               <a href="#"><span>Stock Report</span>
               <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
               </span>
               </a>
               <ul class="treeview-menu" >
                  <li><a href="tejaserp-stockreport-inshortcut.php">Stock Report (ShortCut)</a></li>
                  <li><a href="tejaserp-stockreport-indetails.php">Stock Report (In Details)</a></li>
               </ul>
            </li>
         </ul>
      </li>

      <li class="treeview">
         <a href="#">
            <i class="fa fa-calendar-check-o"></i><span>Attendance Management</span>
            <span class="pull-right-container">
               <i class="fa fa-angle-left pull-right"></i>
            </span>
         </a>
         <ul class="treeview-menu">
            <li class="treeview">
               <a href="#"><span>Customer Type Master</span>
               <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
               </span>
               </a>
               <ul class="treeview-menu" >
                  <li><a href="fh_festivalmastercreation.php">Customer Type Master Creation</a></li>
                  <li><a href="fh_festivalmastercreation_list.php">Customer Type Master List</a></li>
               </ul>
            </li>
         </ul>
      </li>

      <li class="treeview">
         <a href="#">
            <i class="fa fa-money"></i><span>PayRoll Management</span>
            <span class="pull-right-container">
               <i class="fa fa-angle-left pull-right"></i>
            </span>
         </a>
         <ul class="treeview-menu">
            <li class="treeview">
               <a href="#"><span>Customer Type Master</span>
               <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
               </span>
               </a>
               <ul class="treeview-menu" >
                  <li><a href="fh_festivalmastercreation.php">Customer Type Master Creation</a></li>
                  <li><a href="fh_festivalmastercreation_list.php">Customer Type Master List</a></li>
               </ul>
            </li>
         </ul>
      </li>

      <li class="treeview">
         <a href="#">
            <i class="fa fa-globe"></i><span>WebSite Management</span>
            <span class="pull-right-container">
               <i class="fa fa-angle-left pull-right"></i>
            </span>
         </a>
         <ul class="treeview-menu">
            <li class="treeview">
               <a href="#"><span>HomePage Manage</span>
               <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
               </span>
               </a>
               <ul class="treeview-menu">
                  <li><a href="fh_web_1strowcreation.php">1st Row Creations</a></li>
                  <li><a href="fh_web_1strowcreation_list.php">1st Row List</a></li>
               </ul>
            </li>
            <li class="treeview">
               <a href="#"><span>Order Manage</span>
               <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
               </span>
               </a>
               <ul class="treeview-menu">
                  <li class="treeview">
                     <a href="#"><span>All Order Manage</span>
                     <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                     </span>
                     </a>
                     <ul class="treeview-menu">
                        <li><a href="#">All Order List</a></li>
                        <li><a href="#">Confirm Order</a></li>
                        <li><a href="#">Processing Order</a></li>
                        <li><a href="#">Upcoming Order</a></li>
                        <li><a href="#">Cancel Order</a></li>
                        <li><a href="#">Pending Order</a></li>
                        <li><a href="#">Delivered Order</a></li>
                     </ul>
                  </li>
               </ul>
            </li>
         </ul>
      </li>

      <li class="treeview">
         <a href="#">
            <i class="fa fa-industry"></i><span>Manufacturing Management</span>
            <span class="pull-right-container">
               <i class="fa fa-angle-left pull-right"></i>
            </span>
         </a>
         <ul class="treeview-menu">
            <li class="treeview">
               <a href="#"><span>Customer Type Master</span>
               <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
               </span>
               </a>
               <ul class="treeview-menu" >
                  <li><a href="fh_festivalmastercreation.php">Customer Type Master Creation</a></li>
                  <li><a href="fh_festivalmastercreation_list.php">Customer Type Master List</a></li>
               </ul>
            </li>
         </ul>
      </li>
   </ul>
</div>

<!-- SIDEBAR ACTIVATION & ACTIVE-STATE PRESERVER SCRIPT -->
<script>
(function() {
    function getActiveLeafLink() {
        var currentPath = window.location.pathname;
        var phpScript = "<?php echo basename($_SERVER['SCRIPT_NAME'] ?? ''); ?>";
        
        var rawPage = phpScript;
        if (!rawPage || rawPage === '' || rawPage === 'index.php') {
            var segments = currentPath.split('/').filter(Boolean);
            if (segments.length > 0) {
                rawPage = segments[segments.length - 1];
            } else {
                rawPage = 'fh_dashboard.php';
            }
        }
        
        var cleanCurrent = rawPage.split('?')[0].split('#')[0].replace(/\.php$/i, '').toLowerCase();
        var sidebarMenu = document.querySelector('.sidebar-menu');
        if (!sidebarMenu) return null;

        var links = sidebarMenu.querySelectorAll('a[href]');
        for (var i = 0; i < links.length; i++) {
            var href = links[i].getAttribute('href');
            if (href && href !== '#' && href !== 'javascript:void(0);') {
                var linkFile = href.split('?')[0].split('#')[0];
                linkFile = linkFile.substring(linkFile.lastIndexOf('/') + 1);
                var cleanHref = linkFile.replace(/\.php$/i, '').toLowerCase();
                if (cleanHref === cleanCurrent) {
                    return links[i];
                }
            }
        }

        var baseCurrent = cleanCurrent.replace(/_update$/, '').replace(/_view$/, '').replace(/_list$/, '').replace(/_creation$/, '');
        if (baseCurrent.length > 3) {
            for (var j = 0; j < links.length; j++) {
                var href2 = links[j].getAttribute('href');
                if (href2 && href2 !== '#' && href2 !== 'javascript:void(0);') {
                    var linkFile2 = href2.split('?')[0].split('#')[0];
                    linkFile2 = linkFile2.substring(linkFile2.lastIndexOf('/') + 1);
                    var cleanHref2 = linkFile2.replace(/\.php$/i, '').toLowerCase();
                    if (cleanHref2.indexOf(baseCurrent) === 0) {
                        return links[j];
                    }
                }
            }
        }

        if (cleanCurrent === 'fh_dashboard' || cleanCurrent === 'index') {
            return sidebarMenu.querySelector('a[href="fh_dashboard.php"]');
        }

        return null;
    }

    function activateAndPreserveSidebarMenu() {
        try {
            var activeLink = getActiveLeafLink();
            var sidebarMenu = document.querySelector('.sidebar-menu');
            if (!sidebarMenu || !activeLink) return;

            var leafLi = activeLink.closest('li');
            if (leafLi) {
                leafLi.classList.add('active');
            }

            var curr = activeLink.parentElement;
            while (curr && curr !== sidebarMenu) {
                if (curr.tagName === 'LI') {
                    curr.classList.add('active');
                    if (!window._sidebarUserInteracted) {
                        curr.classList.add('menu-open');
                    }
                }
                if (curr.tagName === 'UL' && curr.classList.contains('treeview-menu')) {
                    if (curr.contains(activeLink)) {
                        if (!window._sidebarUserInteracted) {
                            curr.style.display = 'block';
                        }
                    }
                }
                curr = curr.parentElement;
            }
        } catch (e) {
            console.error("Sidebar activation error:", e);
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener("DOMContentLoaded", activateAndPreserveSidebarMenu);
    } else {
        activateAndPreserveSidebarMenu();
    }
    window.addEventListener("load", activateAndPreserveSidebarMenu);

    // Dynamic click handler for menu toggle & instant angle icon state update
    if (typeof jQuery !== 'undefined') {
        $(document).on('click', '.sidebar-menu li.treeview > a', function(e) {
            window._sidebarUserInteracted = true;
            var $parentLi = $(this).parent('li');
            var $subMenu = $(this).next('.treeview-menu');

            if ($subMenu.length > 0) {
                // If sub-menu is currently open/visible, toggle it CLOSED
                if ($parentLi.hasClass('menu-open') || $subMenu.is(':visible')) {
                    $parentLi.removeClass('menu-open');
                    $subMenu.removeClass('menu-open');
                } else {
                    // Close sibling treeviews at the same hierarchy level
                    var $siblings = $parentLi.siblings('.treeview');
                    $siblings.removeClass('menu-open');
                    $siblings.find('.treeview').removeClass('menu-open');

                    $parentLi.addClass('menu-open');
                    $subMenu.addClass('menu-open');
                }
            }
        });
    }
})();
</script>