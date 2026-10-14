<aside class="main-sidebar">
   <!-- sidebar -->
   <div class="sidebar">
      <!-- sidebar menu -->
      <ul class="sidebar-menu">
         <li class="active">
            <a href="fh_dashboard.php"><i class="hvr-buzz-out fa fa-home"></i><span style="font-style: !important; font-size: 13px;">Dashboard</span>
            <span class="pull-right-container">
            </span>
            </a>
         </li>
         <li class="active" style="background-color:#009688">
            <label><i class="hvr-buzz-out fa fa-home" style="margin-left:5px; padding: 12px; font-size:14px; color: white;"></i><span style="font-style: !important; font-size: 14px; margin-left: 0px; color: white;">Master Managment</span>
            <span class="pull-right-container">
            </span>
            </label>
         </li>
          <li class="treeview">
            <a href="#">
            <i class="fa fa-users"></i><span style="font-style: !important; font-size: 13px;">User Master</span>
            <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
            </span>
            </a>
            <ul class="treeview-menu" >
               <li><a href="fh_usermastercreation.php">User Master Creation</a></li>
               <li><a href="fh_usermastercreation_list.php">User Master List</a></li>
            </ul>
         </li>
         <li class="treeview">
            <a href="#">
            <i class="hvr-buzz-out fa fa-user-plus"></i><span style="font-style: !important; font-size: 13px;">User Permission</span>
            </a>
         </li>
         <li class="treeview">
            <a href="#">
            <i class="fa fa-users"></i><span style="font-style: !important; font-size: 13px;">Store Master</span>
            <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
            </span>
            </a>
            <ul class="treeview-menu" >
               <li><a href="fh_storemastercreation.php">Store Creation</a></li>
               <li><a href="fh_storemastercreation_list.php">Store List</a></li>
            </ul>
         </li>
         <li class="treeview">
            <a href="#">
            <i class="fa fa-users"></i><span style="font-style: !important; font-size: 13px;">Store Profile Master</span>
            <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
            </span>
            </a>
            <ul class="treeview-menu" >
               <li><a href="fh_storeprofilecreation.php">Store Profile Creation</a></li>
               <li><a href="fh_storeprofilecreation_list.php">Store Profile List</a></li>
            </ul>
         </li>
        
    
         
         
         <li class="treeview">
            <a href="#">
            <i class="fa fa-users"></i><span>Item Master</span>
            <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
            </span>
            </a>
            <ul class="treeview-menu">
               <li>
                  <a href="#"><span style="background-color: #009688; color: white; padding: 6px; padding-right: 10px; padding-left: 10px;">Head Master</span></a>
                  <ul class="treeview-menu">
                     <li><a href="fh_headmastercreation.php">Head Master Creation</a></li>
               <li><a href="fh_headmastercreation_list.php">Head Master List</a></li>
                     
                  </ul>
                  <?php include('include/divider-dotted.php');?>
               </li>
               
               <li>
                  <a href="#"><span style="background-color: #009688; color: white; padding: 6px; padding-right: 10px; padding-left: 10px;">SubHead Master</span></a>
                  <ul class="treeview-menu">
                     <li><a href="fh_subheadmastercreation.php">SubHead Master Creation</a></li>
               <li><a href="fh_subheadmastercreation_list.php">SubHead Master List</a></li>
                  </ul>
                  <?php include('include/divider-dotted.php');?>
               </li>
               <li>
                  <a href="#"><span style="background-color: #009688; color: white; padding: 6px; padding-right: 10px; padding-left: 10px;">Category || Group Master</span></a>
                  <ul class="treeview-menu">
                     <li><a href="fh_groupmastercreation.php">Category || Group <br>Creation</a></li>
               <li><a href="fh_groupmastercreation_list.php">Category || Group List</a></li>
                  </ul>
                  <?php include('include/divider-dotted.php');?>
               </li>
               <li>
                  <a href="#"><span style="background-color: #009688; color: white; padding: 6px; padding-right: 10px; padding-left: 10px; margin-bottom: 35px;">SubCategory || SubGroup</span>
                     <br><span style="margin-left:10px;">Master</span></a>
                  <ul class="treeview-menu">
                     <li><a href="fh_subgroupmastercreation.php">SubCategory || SubGroup<br>Creation</a></li>
               <li><a href="fh_subgroupmastercreation_list.php">SubCategory || SubGroup<br>   List</a></li>
                  </ul>
                  <?php include('include/divider-dotted.php');?>
               </li>
               <li>
                  <a href="#"><span style="background-color: #009688; color: white; padding: 6px; padding-right: 10px; padding-left: 10px;">Brand Master</span></a>
                  <ul class="treeview-menu">
                     <li><a href="fh_brandmastercreation.php">Brand Master Creation</a></li>
               <li><a href="fh_brandmastercreation_list.php">Brand Master List</a></li>
                  </ul>
                  <?php include('include/divider-dotted.php');?>
               </li>
               <li>
                  <a href="#"><span style="background-color: #009688; color: white; padding: 6px; padding-right: 10px; padding-left: 10px;">Color Master</span></a>
                  <ul class="treeview-menu">
                     <li><a href="fh_colormastercreation.php">Color Master Creation</a></li>
               <li><a href="fh_colormastercreation_list.php">Color Master List</a></li>
                  </ul>
                  <?php include('include/divider-dotted.php');?>
               </li>
               <li>
                  <a href="#"><span style="background-color: #009688; color: white; padding: 6px; padding-right: 10px; padding-left: 10px;">Size Master</span></a>
                  <ul class="treeview-menu">
                     <li><a href="fh_sizemastercreation.php">Size Master Creation</a></li>
               <li><a href="fh_sizemastercreation_list.php">Size Master List</a></li>
                  </ul>
                  <?php include('include/divider-dotted.php');?>
               </li>
               <li>
                  <a href="#"><span style="background-color: #009688; color: white; padding: 6px; padding-right: 10px; padding-left: 10px;">Style || Design Master</span></a>
                  <ul class="treeview-menu">
                     <li><a href="fh_styledesignmastercreation.php">Style || Design Creation</a></li>
               <li><a href="fh_styledesignmastercreation_list.php">Style || Design List</a></li>
                  </ul>
                  
               </li>
                <?php include('include/divider-dotted.php');?>
                 <li>
                  <a href="#"><span style="background-color: #009688; color: white; padding: 6px; padding-right: 10px; padding-left: 10px;">Item Master</span></a>
                  <ul class="treeview-menu">
                     <li><a href="fh_itemmastercreation.php">Item Master Creation</a></li>
               <li><a href="fh_itemmastercreation_list.php">Item Master List</a></li>
                  </ul>
                  
               </li>
            </ul>

         </li>
         <li class="treeview">
            <a href="#">
            <i class="fa fa-users"></i><span style="font-style: !important; font-size: 13px;">GST Master</span>
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
            <a href="#">
            <i class="fa fa-users"></i><span style="font-style: !important; font-size: 13px;">PayMode Master</span>
            <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
            </span>
            </a>
            <ul class="treeview-menu" >
               <li><a href="fh_paymodemastercreation.php">PayMode Creation</a></li>
               <li><a href="fh_paymodemastercreation_list.php">PayMode List</a></li>
            </ul>
         </li>
         <li class="treeview">
            <a href="#">
            <i class="fa fa-users"></i><span style="font-style: !important; font-size: 13px;">Rate Master</span>
            <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
            </span>
            </a>
            <ul class="treeview-menu" >
               <li><a href="ssenterprises_usermastercreation.php">Rate Creation</a></li>
               <li><a href="ssenterprises_usermastercreation_list.php">Rate List</a></li>
            </ul>
         </li>
         <li class="treeview">
            <a href="#">
            <i class="fa fa-users"></i><span style="font-style: !important; font-size: 13px;">Festival Master</span>
            <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
            </span>
            </a>
            <ul class="treeview-menu" >
               
               <li><a href="fh_festivalmastercreation.php">Festival Creation</a></li>
               <li><a href="fh_festivalmastercreation_list.php">Festival List</a></li>
               
               
            </ul>
         </li>
         
         <li class="treeview">
            <a href="#">
            <i class="fa fa-users"></i><span style="font-style: !important; font-size: 13px;">Point Master</span>
            <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
            </span>
            </a>
            <ul class="treeview-menu" >
               <li><a href="fh_pointmastercreation.php">Point Creation</a></li>
               <li><a href="fh_pointmastercreation_list.php">Point List</a></li>
            </ul>
         </li>
         <li class="treeview">
            <a href="#">
            <i class="fa fa-users"></i><span style="font-style: !important; font-size: 13px;">Coupon Master</span>
            <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
            </span>
            </a>
            <ul class="treeview-menu" >
               <li><a href="fh_couponmastercreation.php">Coupon Creation</a></li>
               <li><a href="fh_couponmastercreation_list.php">Coupon List</a></li>
               <li><a href="fh_couponmastercreation_view.php">Coupon View</a></li>
            </ul>
         </li>
         <li class="treeview">
            <a href="#">
            <i class="fa fa-users"></i><span style="font-style: !important; font-size: 13px;">Discount Master</span>
            <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
            </span>
            </a>
            <ul class="treeview-menu" >
               
               <li><a href="fh_discountmastercreation.php">Discount Creation</a></li>
               <li><a href="fh_discountmastercreation_list.php">Discount List</a></li>
            </ul>
         </li>
         
         <!--inventory management-->
         <li class="active" style="background-color:#009688">
            <label><i class="hvr-buzz-out fa fa-home" style="margin-left:5px; padding: 12px; font-size:14px; color: white;"></i><span style="font-style: !important; font-size: 14px; margin-left: 0px; color: white;">Inventory Management</span>
            <span class="pull-right-container">
            </span>
            </label>
         </li>
         <li class="treeview">
            <a href="#">
            <i class="fa fa-users"></i><span style="font-style: !important; font-size: 13px;">Inventory || Purchase<br><span style="margin-left: 30px;">Order (PO)</span></span>
            <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
            </span>
            </a>
            <ul class="treeview-menu" >
               <li><a href="ssenterprises_usermastercreation.php">Inventory || Purchase<br><span>Order Creations</span></a></li>
               <li><a href="ssenterprises_usermastercreation_list.php">Inventory || Purchase<br><span>Order List</span> </a></li>
               <li><a href="ssenterprises_usermastercreation_update_list.php">Inventory || Purchase<br><span>Order Edit</span> </a></li>
               <li><a href="ssenterprises_usermastercreation_listdelete.php">Inventory || Purchase<br><span>Order Delete</span> </a></li>
            </ul>
         </li>
         <li class="treeview">
            <a href="#">
            <i class="fa fa-users"></i><span style="font-style: !important; font-size: 13px;">Inventory || Purchase</span>
            <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
            </span>
            </a>
            <ul class="treeview-menu" >
               <li><a href="purchase-master-creation-new.php">Inventory || Purchase <br> Creation</a></li>
               <li><a href="purchase-master-billwise-list.php">Inventory || Purchase<br> Bill Wise List</a></li>
               <li><a href="purchase-master-itemwise-list.php">Inventory || Purchase<br> Item Wise List</a></li>
               <li><a href="purchase-master-report.php">Inventory || Purchase<br> Report</a></li>
            </ul>
         </li>
         <li class="treeview">
            <a href="#">
            <i class="fa fa-users"></i><span style="font-style: !important; font-size: 13px;">Inventory || Purchase<br><span style="margin-left: 30px;">Return</span></span>
            <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
            </span>
            </a>
            <ul class="treeview-menu" >
               <li><a href="ssenterprises_usermastercreation.php">Inventory || Purchase<br><span>Return Creations</span></a></li>
               <li><a href="ssenterprises_usermastercreation_list.php">Inventory || Purchase<br><span>Return List</span> </a></li>
               <li><a href="ssenterprises_usermastercreation_update_list.php">Inventory || Purchase<br><span>Return Edit</span> </a></li>
               <li><a href="ssenterprises_usermastercreation_listdelete.php">Inventory || Purchase<br><span>Return Delete</span> </a></li>
            </ul>
         </li>
         <!--barcode management-->
         <li class="active" style="background-color:#009688">
            <label><i class="hvr-buzz-out fa fa-home" style="margin-left:5px; padding: 12px; font-size:14px; color: white;"></i><span style="font-style: !important; font-size: 14px; margin-left: 0px; color: white;">Barcode Management</span>
            <span class="pull-right-container">
            </span>
            </label>
         </li>
         <li class="treeview">
            <a href="#">
            <i class="fa fa-users"></i><span>Barcode Print</span>
            <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
            </span>
            </a>
            <ul class="treeview-menu">
               <li>
                  <a href="#"><span style="background-color: #009688; color: white;">Inventory || Purchase<br>Barcode Print</span></a>
                  <ul class="treeview-menu">
                     <li><a href="tejaserp-inventorybarcodeprint.php">Barcode Print</a></li>
                     <li><a href="tejaserp-inventorybarcodeprint-list.php">Barcode Print List</a></li>
                     <li><a href="tejaserp-inventorybarcodeprint-re-print.php">Barcode Re-Print</a></li>
                     <li><a href="#">Barcode Re-Print List</a></li>
                  </ul>
                  <?php include('include/divider-dotted.php');?>
               </li>
               <li>
                  <a href="#"><span style="background-color: #009688; color: white; padding: 6px; padding-right: 10px; padding-left: 10px;">Regular Barcode Print</span></a>
                  <ul class="treeview-menu">
                     <li><a href="#">Regular Barcode Print</a></li>
                     <li><a href="#">Regular Barcode Print List</a></li>
                  </ul>
                  <?php include('include/divider-dotted.php');?>
               </li>
            </ul>
         </li>
         <!--stock transfer management-->
          <!--stock transfer management-->
         <li class="active" style="background-color:#009688">
            <label><i class="hvr-buzz-out fa fa-home" style="margin-left:5px; padding: 12px; font-size:14px; color: white;"></i><span style="font-style: !important; font-size: 14px; margin-left: 0px; color: white;">Stock Transfer Management</span>
            <span class="pull-right-container">
            </span>
            </label>
         </li>
        <li class="treeview">
            <a href="#">
            <i class="fa fa-users"></i><span style="font-style: !important; font-size: 13px;">Stock Transfer</span>
            <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
            </span>
            </a>
            <ul class="treeview-menu" >
               <li><a href="ssenterprises_usermastercreation.php">Stock Transfer Creation</a></li>
               <li><a href="ssenterprises_usermastercreation_list.php">Stock Transfer Bill Wise List</a></li>
               <li><a href="ssenterprises_usermastercreation_update_list.php">Stock Transfer Item Wise List</a></li>
               <li><a href="ssenterprises_usermastercreation_listdelete.php">Stock Transfer Report</a></li>
            </ul>
         </li>
         <li class="treeview">
            <a href="#">
            <i class="fa fa-users"></i><span style="font-style: !important; font-size: 13px;">Stock Transfer Return</span>
            <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
            </span>
            </a>
            <ul class="treeview-menu" >
               <li><a href="ssenterprises_usermastercreation.php">Stock Transfer Return<br> Creation</a></li>
               <li><a href="ssenterprises_usermastercreation_list.php">Stock Transfer Return<br> Bill Wise List</a></li>
               <li><a href="ssenterprises_usermastercreation_update_list.php">Stock Transfer Return<br> Item Wise List</a></li>
               <li><a href="ssenterprises_usermastercreation_listdelete.php">Stock Transfer Return<br> Report</a></li>
            </ul>
         </li>
         <!--bill sale management-->>
         <li class="active" style="background-color:#009688">
            <label><i class="hvr-buzz-out fa fa-home" style="margin-left:5px; padding: 12px; font-size:14px; color: white;"></i><span style="font-style: !important; font-size: 14px; margin-left: 0px; color: white;">Bill || Sale Management</span>
            <span class="pull-right-container"></span>
            </label>
         </li>
         <li class="treeview">
            <a href="#">
            <i class="fa fa-users"></i><span style="font-style: !important; font-size: 13px;">Bill || Sale </span>
            <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
            </span>
            </a>
            <ul class="treeview-menu" >
               <li><a href="sale-creations.php">Bill || Sale Creations</a></li>
               <li><a href="ssenterprises_usermastercreation_list.php">Bill || Sale List</a></li>
               <li><a href="ssenterprises_usermastercreation_update_list.php">Bill || Sale Edit</span> </a></li>
               <li><a href="ssenterprises_usermastercreation_listdelete.php">Bill || Sale Delete</span> </a></li>
            </ul>
         </li>
         <!--accounting management-->
         <li class="active" style="background-color:#009688">
            <label><i class="hvr-buzz-out fa fa-home" style="margin-left:5px; padding: 12px; font-size:14px; color: white;"></i><span style="font-style: !important; font-size: 14px; margin-left: 0px; color: white;">Accounting Management</span>
            <span class="pull-right-container">
            </span>
            </label>
         </li>
         <li class="treeview">
            <a href="tejashotels-confirm-check-in.php">
            <i class="hvr-buzz-out fa fa-check"></i><span style="font-style: !important; font-size: 13px;">Expense Head Master</span>
            <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
            </span>
            </a>
            <ul class="treeview-menu">
               <li><a href="fh_expenses_headmastercreation.php">Expense Head Creations</a></li>
               <li><a href="fh_expenses_headmastercreation_list.php">Expense Head List</a></li>
            </ul>
         </li>
         <li class="treeview">
            <a href="tejashotels-confirm-check-in.php">
            <i class="hvr-buzz-out fa fa-check"></i><span style="font-style: !important; font-size: 13px;">Expense Manage</span>
            <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
            </span>
            </a>
            <ul class="treeview-menu">
               <li><a href="fh_expensesmastercreation.php">Expenses Creations</a></li>
               <li><a href="fh_expensesmastercreation_list.php">Expenses List</a></li>
               <li><a href="fh_expensesmasterbalancesheet.php">Expenses Balance Sheet</a></li>
            </ul>
         </li>
         <li class="treeview">
            <a href="tejashotels-confirm-check-in.php">
            <i class="hvr-buzz-out fa fa-check"></i><span style="font-style: !important; font-size: 13px;">Payment Manage</span>
            <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
            </span>
            </a>
            <ul class="treeview-menu">
               <li><a href="fh_paymentmastercreation.php">Payment Creations</a></li>
               <li><a href="fh_paymentmastercreation_list.php">Payment List</a></li>
               <li><a href="size-master-list.php">Payment Balance Sheet</a></li>
            </ul>
         </li>
         <li class="treeview">
            <a href="tejashotels-confirm-check-in.php">
            <i class="hvr-buzz-out fa fa-check"></i><span style="font-style: !important; font-size: 13px;">Ladger</span>
            </a>
         </li>
      </ul>
      <!-- /.crm management -->
      <ul class="sidebar-menu">
         <li class="active" style="background-color:#009688">
            <label><i class="hvr-buzz-out fa fa-home" style="margin-left:5px; padding: 12px; font-size:14px; color: white;"></i><span style="font-style: !important; font-size: 14px; margin-left: 0px; color: white;">CRM</span>
            <span class="pull-right-container">
            </span>
            </label>
         </li>
         <li class="treeview">
            <a href="tejashotels-confirm-check-in.php">
            <i class="hvr-buzz-out fa fa-check"></i><span style="font-style: !important; font-size: 13px;">Customer Type Master</span>
            <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
            </span>
            </a>
            <ul class="treeview-menu">
               <li><a href="fh_customertypemastercreation.php">Customer Type Master<br> Creations</a></li>
               <li><a href="fh_customertypemastercreation_list.php">Customer Type Master List</a></li>
            </ul>
         </li>
         <li class="treeview">
            <a href="">
            <i class="hvr-buzz-out fa fa-check"></i><span style="font-style: !important; font-size: 13px;">Party Type Master</span>
            <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
            </span>
            </a>
            <ul class="treeview-menu">
               <li><a href="fh_partytypemastercreation.php">Party Type Master<br> Creations</a></li>
               <li><a href="fh_partytypemastercreation_list.php">Party Type Master List</a></li>
            </ul>
         </li>
         <li class="treeview">
            <a href="tejashotels-confirm-check-in.php">
            <i class="hvr-buzz-out fa fa-check"></i><span style="font-style: !important; font-size: 13px;">Account Master</span>
            <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
            </span>
            </a>
            <ul class="treeview-menu">
               <li><a href="fh_accountmastercreation.php">Account Master Creations</a></li>
               <li><a href="fh_accountmastercreation_list.php">Account Master List</a></li>
            </ul>
         </li>
         <li class="treeview">
            <a href="#">
            <i class="fa fa-users"></i><span style="font-style: !important; font-size: 13px;">SalesMan Master</span>
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
            <a href="#">
            <i class="fa fa-users"></i><span style="font-style: !important; font-size: 13px;">SalesMan Target Master</span>
            <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
            </span>
            </a>
            <ul class="treeview-menu" >
               <li><a href="fh_Salesman_target_mastercreation.php">SalesMan Target Master<br> Creation</a></li>
               <li><a href="fh_Salesman_target_mastercreation_list.php">SalesMan Master Target List</a></li>
            </ul>
         </li>
      </ul>
      <!-- /.reports management -->
      <!-- sidebar menu -->
      <ul class="sidebar-menu">
         <li class="active" style="background-color:#009688">
            <label><i class="hvr-buzz-out fa fa-home" style="margin-left:5px; padding: 12px; font-size:14px; color: white;"></i><span style="font-style: !important; font-size: 14px; margin-left: 0px; color: white;">Reports Management</span>
            <span class="pull-right-container">
            </span>
            </label>
         </li>
         <li class="treeview">
            <a href="#">
            <i class="fa fa-users"></i><span>Inventory || Purchase<br><span style="margin-left:30px;"> Reports</span></span>
            <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
            </span>
            </a>
            <ul class="treeview-menu">
               <li>
                  <a href="#"><span style="background-color: #009688; color: white; padding: 6px; padding-right: 10px; padding-left: 10px;">Reports</span></a>
                  <ul class="treeview-menu">
                     <li><a href="#">Bill Wise</a></li>
                     <li><a href="#">Item Wise</a></li>
                     <li><a href="#">Mobile No. || Party Name<br> Wise</a></li>
                     <li><a href="#">Pay Mode Wise</a></li>
                     <li><a href="#">DPR</a></li>
                     <li><a href="#">Category || Group Wise</a></li>
                     <li><a href="#">Brand Wise</a></li>
                     <li><a href="#">Color Wise</a></li>
                     <li><a href="#">Size Wise</a></li>
                     <li><a href="#">Style || Design Wise</a></li>
                     <li><a href="#">Article Wise</a></li>
                  </ul>
                  <?php include('include/divider-dotted.php');?>
               </li>
               <li>
                  <a href="#"><span style="background-color: #009688; color: white; padding: 6px; padding-right: 10px; padding-left: 10px;">Graph Reports</span></a>
                  <ul class="treeview-menu">
                     <li class="treeview">
                        <a href="#">
                        <span style="font-style: !important; font-size: 13px;">Bill || Graph Wise</span>
                        </a>
                     </li>
                     <li class="treeview">
                        <a href="#">
                        <span style="font-style: !important; font-size: 13px;">Mobile No. - Party Name ||<br>Graph Wise</span>
                        </a>
                     </li>
                     <li class="treeview">
                        <a href="#">
                        <span style="font-style: !important; font-size: 13px;">Pay Mode || Graph Wise</span>
                        </a>
                     </li>
                     <li class="treeview">
                        <a href="#">
                        <span style="font-style: !important; font-size: 13px;">DPR || Graph Wise</span>
                        </a>
                     </li>
                     <li class="treeview">
                        <a href="#">
                        <span style="font-style: !important; font-size: 13px;">Item Name || Grapg Wise</span>
                        </a>
                     </li>
                     <li class="treeview">
                        <a href="#">
                        <span style="font-style: !important; font-size: 13px;">Category-Group || Graph<br> Wise</span>
                        </a>
                     </li>
                     <li class="treeview">
                        <a href="#">
                        <span style="font-style: !important; font-size: 13px;">Brand || Graph Wise</span>
                        </a>
                     </li>
                     <li class="treeview">
                        <a href="#">
                        <span style="font-style: !important; font-size: 13px;">Color || Graph Wise</span>
                        </a>
                     </li>
                     <li class="treeview">
                        <a href="#">
                        <span style="font-style: !important; font-size: 13px;">Size || Graph Wise</span>
                        </a>
                     </li>
                     <li class="treeview">
                        <a href="#">
                        <span style="font-style: !important; font-size: 13px;">Style-Design || Graph Wise</span>
                        </a>
                     </li>
                     <li class="treeview">
                        <a href="#">
                        <span style="font-style: !important; font-size: 13px;">Article || Graph Wise</span>
                        </a>
                     </li>
                  </ul>
                  <?php include('include/divider-dotted.php');?>
               </li>
            </ul>
         </li>
         <li class="treeview">
            <a href="#">
            <i class="fa fa-users"></i><span>Stock Transfer Reports</span>
            <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
            </span>
            </a>
            <ul class="treeview-menu">
               <li>
                  <a href="#"><span style="background-color: #009688; color: white; padding: 6px; padding-right: 10px; padding-left: 10px;">Reports</span></a>
                  <ul class="treeview-menu">
                     <li><a href="#">Bill Wise</a></li>
                     <li><a href="#">Item Wise Wise</a></li>
                     <li><a href="#">Pay Mode Wise</a></li>
                     <li><a href="#">DSR</a></li>
                     <li><a href="#">Mobile No. || Customer<br> Wise</a></li>
                     <li><a href="#">Category || Group Wise</a></li>
                     <li><a href="#">Brand Wise</a></li>
                     <li><a href="#">Color Wise</a></li>
                     <li><a href="#">Size Wise</a></li>
                     <li><a href="#">Style || Design Wise</a></li>
                     <li><a href="#">Article Wise</a></li>
                  </ul>
                  <?php include('include/divider-dotted.php');?>
               </li>
               <li>
                  <a href="#"><span style="background-color: #009688; color: white; padding: 6px; padding-right: 10px; padding-left: 10px;">Graph Reports</span></a>
                  <ul class="treeview-menu">
                     <li class="treeview">
                        <a href="#">
                        <span style="font-style: !important; font-size: 13px;">Bill || Graph Wise</span>
                        </a>
                     </li>
                     <li class="treeview">
                        <a href="#">
                        <span style="font-style: !important; font-size: 13px;">Item Name || Graph Wise</span>
                        </a>
                     </li>
                     <li class="treeview">
                        <a href="#">
                        <span style="font-style: !important; font-size: 13px;">Pay Mode || Graph Wise</span>
                        </a>
                     </li>
                     <li class="treeview">
                        <a href="#">
                        <span style="font-style: !important; font-size: 13px;">DSR || Graph Wise</span>
                        </a>
                     </li>
                     <li class="treeview">
                        <a href="#">
                        <span style="font-style: !important; font-size: 13px;">Mobile No. - Customer<br> Name || Graph Wise</span>
                        </a>
                     </li>
                     <li class="treeview">
                        <a href="#">
                        <span style="font-style: !important; font-size: 13px;">Category-Group || Graph<br> Wise</span>
                        </a>
                     </li>
                     <li class="treeview">
                        <a href="#">
                        <span style="font-style: !important; font-size: 13px;">Brand || Graph Wise</span>
                        </a>
                     </li>
                     <li class="treeview">
                        <a href="#">
                        <span style="font-style: !important; font-size: 13px;">Color || Graph Wise</span>
                        </a>
                     </li>
                     <li class="treeview">
                        <a href="#">
                        <span style="font-style: !important; font-size: 13px;">Size || Graph Wise</span>
                        </a>
                     </li>
                     <li class="treeview">
                        <a href="#">
                        <span style="font-style: !important; font-size: 13px;">Style-Design || Graph Wise</span>
                        </a>
                     </li>
                     <li class="treeview">
                        <a href="#">
                        <span style="font-style: !important; font-size: 13px;">Article || Graph Wise</span>
                        </a>
                     </li>
                  </ul>
                  <?php include('include/divider-dotted.php');?>
               </li>
            </ul>
         </li>
         <li class="treeview">
            <a href="#">
            <i class="fa fa-users"></i><span>Stock Transfer Return<br><span style="margin-left:30px;"> Reports</span></span>
            <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
            </span>
            </a>
            <ul class="treeview-menu">
               <li>
                  <a href="#"><span style="background-color: #009688; color: white; padding: 6px; padding-right: 10px; padding-left: 10px;">Reports</span></a>
                  <ul class="treeview-menu">
                     <li><a href="#">Bill Wise</a></li>
                     <li><a href="#">Item Wise Wise</a></li>
                     <li><a href="#">Pay Mode Wise</a></li>
                     <li><a href="#">DSR</a></li>
                     <li><a href="#">Mobile No. || Customer<br> Wise</a></li>
                     <li><a href="#">Category || Group Wise</a></li>
                     <li><a href="#">Brand Wise</a></li>
                     <li><a href="#">Color Wise</a></li>
                     <li><a href="#">Size Wise</a></li>
                     <li><a href="#">Style || Design Wise</a></li>
                     <li><a href="#">Article Wise</a></li>
                  </ul>
                  <?php include('include/divider-dotted.php');?>
               </li>
               <li>
                  <a href="#"><span style="background-color: #009688; color: white; padding: 6px; padding-right: 10px; padding-left: 10px;">Graph Reports</span></a>
                  <ul class="treeview-menu">
                     <li class="treeview">
                        <a href="#">
                        <span style="font-style: !important; font-size: 13px;">Bill || Graph Wise</span>
                        </a>
                     </li>
                     <li class="treeview">
                        <a href="#">
                        <span style="font-style: !important; font-size: 13px;">Item Name || Graph Wise</span>
                        </a>
                     </li>
                     <li class="treeview">
                        <a href="#">
                        <span style="font-style: !important; font-size: 13px;">Pay Mode || Graph Wise</span>
                        </a>
                     </li>
                     <li class="treeview">
                        <a href="#">
                        <span style="font-style: !important; font-size: 13px;">DSR || Graph Wise</span>
                        </a>
                     </li>
                     <li class="treeview">
                        <a href="#">
                        <span style="font-style: !important; font-size: 13px;">Mobile No. - Customer<br> Name || Graph Wise</span>
                        </a>
                     </li>
                     <li class="treeview">
                        <a href="#">
                        <span style="font-style: !important; font-size: 13px;">Category-Group || Graph<br> Wise</span>
                        </a>
                     </li>
                     <li class="treeview">
                        <a href="#">
                        <span style="font-style: !important; font-size: 13px;">Brand || Graph Wise</span>
                        </a>
                     </li>
                     <li class="treeview">
                        <a href="#">
                        <span style="font-style: !important; font-size: 13px;">Color || Graph Wise</span>
                        </a>
                     </li>
                     <li class="treeview">
                        <a href="#">
                        <span style="font-style: !important; font-size: 13px;">Size || Graph Wise</span>
                        </a>
                     </li>
                     <li class="treeview">
                        <a href="#">
                        <span style="font-style: !important; font-size: 13px;">Style-Design || Graph Wise</span>
                        </a>
                     </li>
                     <li class="treeview">
                        <a href="#">
                        <span style="font-style: !important; font-size: 13px;">Article || Graph Wise</span>
                        </a>
                     </li>
                  </ul>
                  <?php include('include/divider-dotted.php');?>
               </li>
            </ul>
         </li>
         <li class="treeview">
            <a href="#">
            <i class="fa fa-users"></i><span>Bill || Sale Reports</span>
            <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
            </span>
            </a>
            <ul class="treeview-menu">
               <li>
                  <a href="#"><span style="background-color: #009688; color: white; padding: 6px; padding-right: 10px; padding-left: 10px;">Reports</span></a>
                  <ul class="treeview-menu">
                     <li><a href="#">Bill Wise</a></li>
                     <li><a href="#">Item Wise Wise</a></li>
                     <li><a href="#">Pay Mode Wise</a></li>
                     <li><a href="#">DSR</a></li>
                     <li><a href="#">Mobile No. || Customer<br> Wise</a></li>
                     <li><a href="#">Category || Group Wise</a></li>
                     <li><a href="#">Brand Wise</a></li>
                     <li><a href="#">Color Wise</a></li>
                     <li><a href="#">Size Wise</a></li>
                     <li><a href="#">Style || Design Wise</a></li>
                     <li><a href="#">Article Wise</a></li>
                  </ul>
                  <?php include('include/divider-dotted.php');?>
               </li>
               <li>
                  <a href="#"><span style="background-color: #009688; color: white; padding: 6px; padding-right: 10px; padding-left: 10px;">Graph Reports</span></a>
                  <ul class="treeview-menu">
                     <li class="treeview">
                        <a href="#">
                        <span style="font-style: !important; font-size: 13px;">Bill || Graph Wise</span>
                        </a>
                     </li>
                     <li class="treeview">
                        <a href="#">
                        <span style="font-style: !important; font-size: 13px;">Item Name || Graph Wise</span>
                        </a>
                     </li>
                     <li class="treeview">
                        <a href="#">
                        <span style="font-style: !important; font-size: 13px;">Pay Mode || Graph Wise</span>
                        </a>
                     </li>
                     <li class="treeview">
                        <a href="#">
                        <span style="font-style: !important; font-size: 13px;">DSR || Graph Wise</span>
                        </a>
                     </li>
                     <li class="treeview">
                        <a href="#">
                        <span style="font-style: !important; font-size: 13px;">Mobile No. - Customer<br> Name || Graph Wise</span>
                        </a>
                     </li>
                     <li class="treeview">
                        <a href="#">
                        <span style="font-style: !important; font-size: 13px;">Category-Group || Graph<br> Wise</span>
                        </a>
                     </li>
                     <li class="treeview">
                        <a href="#">
                        <span style="font-style: !important; font-size: 13px;">Brand || Graph Wise</span>
                        </a>
                     </li>
                     <li class="treeview">
                        <a href="#">
                        <span style="font-style: !important; font-size: 13px;">Color || Graph Wise</span>
                        </a>
                     </li>
                     <li class="treeview">
                        <a href="#">
                        <span style="font-style: !important; font-size: 13px;">Size || Graph Wise</span>
                        </a>
                     </li>
                     <li class="treeview">
                        <a href="#">
                        <span style="font-style: !important; font-size: 13px;">Style-Design || Graph Wise</span>
                        </a>
                     </li>
                     <li class="treeview">
                        <a href="#">
                        <span style="font-style: !important; font-size: 13px;">Article || Graph Wise</span>
                        </a>
                     </li>
                  </ul>
                  <?php include('include/divider-dotted.php');?>
               </li>
            </ul>
         </li>
      </ul>
      <!-- /.payroll management -->
      <ul class="sidebar-menu">
         <li class="active" style="background-color:#009688">
            <label><i class="hvr-buzz-out fa fa-home" style="margin-left:5px; padding: 12px; font-size:14px; color: white;"></i><span style="font-style: !important; font-size: 14px; margin-left: 0px; color: white;">Attendance Management</span>
            <span class="pull-right-container"></span>
            </label>
         </li>
         <li class="treeview">
            <a href="#">
            <i class="fa fa-users"></i><span>Inventory || Purchase<br><span style="margin-left:30px;"> Reports</span></span>
            <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
            </span>
            </a>
            <ul class="treeview-menu">
               <li>
                  <a href="#"><span style="background-color: #009688; color: white; padding: 6px; padding-right: 10px; padding-left: 10px;">Reports</span>
                  </a>
                  <ul class="treeview-menu">
                     <li><a href="#">Bill Wise</a></li>
                     <li><a href="#">Item Wise</a></li>
                     <li><a href="#">Mobile No. || Party Name<br> Wise</a></li>
                     <li><a href="#">Pay Mode Wise</a></li>
                     <li><a href="#">DPR</a></li>
                     <li><a href="#">Category || Group Wise</a></li>
                     <li><a href="#">Brand Wise</a></li>
                     <li><a href="#">Color Wise</a></li>
                     <li><a href="#">Size Wise</a></li>
                     <li><a href="#">Style || Design Wise</a></li>
                     <li><a href="#">Article Wise</a></li>
                  </ul>
                  <?php include('include/divider-dotted.php');?>
               </li>
               <li>
                  <a href="#"><span style="background-color: #009688; color: white; padding: 6px; padding-right: 10px; padding-left: 10px;">Graph Reports</span>
                  </a>
                  <ul class="treeview-menu">
                     <li class="treeview">
                        <a href="#">
                        <span style="font-style: !important; font-size: 13px;">Bill || Graph Wise
                        </span>
                        </a>
                     </li>
                     <li class="treeview">
                        <a href="#">
                        <span style="font-style: !important; font-size: 13px;">Mobile No. - Party Name ||<br>Graph Wise</span>
                        </a>
                     </li>
                     <li class="treeview">
                        <a href="#">
                        <span style="font-style: !important; font-size: 13px;">Pay Mode || Graph Wise</span>
                        </a>
                     </li>
                     <li class="treeview">
                        <a href="#">
                        <span style="font-style: !important; font-size: 13px;">DPR || Graph Wise</span>
                        </a>
                     </li>
                     <li class="treeview">
                        <a href="#">
                        <span style="font-style: !important; font-size: 13px;">Item Name || Grapg Wise</span>
                        </a>
                     </li>
                     <li class="treeview">
                        <a href="#">
                        <span style="font-style: !important; font-size: 13px;">Category-Group || Graph<br> Wise</span>
                        </a>
                     </li>
                     <li class="treeview">
                        <a href="#">
                        <span style="font-style: !important; font-size: 13px;">Brand || Graph Wise</span>
                        </a>
                     </li>
                     <li class="treeview">
                        <a href="#">
                        <span style="font-style: !important; font-size: 13px;">Color || Graph Wise</span>
                        </a>
                     </li>
                     <li class="treeview">
                        <a href="#">
                        <span style="font-style: !important; font-size: 13px;">Size || Graph Wise</span>
                        </a>
                     </li>
                     <li class="treeview">
                        <a href="#">
                        <span style="font-style: !important; font-size: 13px;">Style-Design || Graph Wise</span>
                        </a>
                     </li>
                     <li class="treeview">
                        <a href="#">
                        <span style="font-style: !important; font-size: 13px;">Article || Graph Wise</span>
                        </a>
                     </li>
                  </ul>
                  <?php include('include/divider-dotted.php');?>
               </li>
            </ul>
         </li>
      </ul>
      <!-- /.payroll management -->
      <ul class="sidebar-menu">
         <li class="active" style="background-color:#009688">
            <label><i class="hvr-buzz-out fa fa-home" style="margin-left:5px; padding: 12px; font-size:14px; color: white;"></i><span style="font-style: !important; font-size: 14px; margin-left: 0px; color: white;">Payroll Management</span>
            <span class="pull-right-container"></span>
            </label>
         </li>
         <li class="treeview">
            <a href="#">
            <i class="fa fa-users"></i><span>Inventory || Purchase<br><span style="margin-left:30px;"> Reports</span></span>
            <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
            </span>
            </a>
            <ul class="treeview-menu">
               <li>
                  <a href="#"><span style="background-color: #009688; color: white; padding: 6px; padding-right: 10px; padding-left: 10px;">Reports</span>
                  </a>
                  <ul class="treeview-menu">
                     <li><a href="#">Bill Wise</a></li>
                     <li><a href="#">Item Wise</a></li>
                     <li><a href="#">Mobile No. || Party Name<br> Wise</a></li>
                     <li><a href="#">Pay Mode Wise</a></li>
                     <li><a href="#">DPR</a></li>
                     <li><a href="#">Category || Group Wise</a></li>
                     <li><a href="#">Brand Wise</a></li>
                     <li><a href="#">Color Wise</a></li>
                     <li><a href="#">Size Wise</a></li>
                     <li><a href="#">Style || Design Wise</a></li>
                     <li><a href="#">Article Wise</a></li>
                  </ul>
                  <?php include('include/divider-dotted.php');?>
               </li>
               <li>
                  <a href="#"><span style="background-color: #009688; color: white; padding: 6px; padding-right: 10px; padding-left: 10px;">Graph Reports</span>
                  </a>
                  <ul class="treeview-menu">
                     <li class="treeview">
                        <a href="#">
                        <span style="font-style: !important; font-size: 13px;">Bill || Graph Wise
                        </span>
                        </a>
                     </li>
                     <li class="treeview">
                        <a href="#">
                        <span style="font-style: !important; font-size: 13px;">Mobile No. - Party Name ||<br>Graph Wise</span>
                        </a>
                     </li>
                     <li class="treeview">
                        <a href="#">
                        <span style="font-style: !important; font-size: 13px;">Pay Mode || Graph Wise</span>
                        </a>
                     </li>
                     <li class="treeview">
                        <a href="#">
                        <span style="font-style: !important; font-size: 13px;">DPR || Graph Wise</span>
                        </a>
                     </li>
                     <li class="treeview">
                        <a href="#">
                        <span style="font-style: !important; font-size: 13px;">Item Name || Grapg Wise</span>
                        </a>
                     </li>
                     <li class="treeview">
                        <a href="#">
                        <span style="font-style: !important; font-size: 13px;">Category-Group || Graph<br> Wise</span>
                        </a>
                     </li>
                     <li class="treeview">
                        <a href="#">
                        <span style="font-style: !important; font-size: 13px;">Brand || Graph Wise</span>
                        </a>
                     </li>
                     <li class="treeview">
                        <a href="#">
                        <span style="font-style: !important; font-size: 13px;">Color || Graph Wise</span>
                        </a>
                     </li>
                     <li class="treeview">
                        <a href="#">
                        <span style="font-style: !important; font-size: 13px;">Size || Graph Wise</span>
                        </a>
                     </li>
                     <li class="treeview">
                        <a href="#">
                        <span style="font-style: !important; font-size: 13px;">Style-Design || Graph Wise</span>
                        </a>
                     </li>
                     <li class="treeview">
                        <a href="#">
                        <span style="font-style: !important; font-size: 13px;">Article || Graph Wise</span>
                        </a>
                     </li>
                  </ul>
                  <?php include('include/divider-dotted.php');?>
               </li>
            </ul>
         </li>
      </ul>
      <!-- /.website management -->
      <!-- sidebar menu -->
      <ul class="sidebar-menu">
         <li class="active" style="background-color:#009688">
            <label><i class="hvr-buzz-out fa fa-home" style="margin-left:5px; padding: 12px; font-size:14px; color: white;"></i><span style="font-style: !important; font-size: 14px; margin-left: 0px; color: white;">Website Management</span>
            <span class="pull-right-container">
            </span>
            </label>
         </li>
         <li class="treeview">
            <a href="tejashotels-confirm-check-in.php">
            <i class="hvr-buzz-out fa fa-check"></i><span style="font-style: !important; font-size: 13px;">Dashboard Manage</span>
            <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
            </span>
            </a>
            <ul class="treeview-menu">
               <li><a href="fh_web_1strowcreation.php">1st Row Creations</a></li>
               <li><a href="fh_web_1strowcreation_list.php">1st Row List</a></li>
            </ul>
            <ul class="treeview-menu">
               <li><a href="fh_customertypemastercreation.php">2nd Row Creations</a></li>
               <li><a href="fh_customertypemastercreation_list.php">2nd Row List</a></li>
            </ul>
            <ul class="treeview-menu">
               <li><a href="fh_customertypemastercreation.php">3rd Row Creations</a></li>
               <li><a href="fh_customertypemastercreation_list.php">3rd Row List</a></li>
            </ul>
            <ul class="treeview-menu">
               <li><a href="fh_customertypemastercreation.php">4th Row Creations</a></li>
               <li><a href="fh_customertypemastercreation_list.php">4th Row List</a></li>
            </ul>
            <ul class="treeview-menu">
               <li><a href="fh_customertypemastercreation.php">5th Row Creations</a></li>
               <li><a href="fh_customertypemastercreation_list.php">5th Row List</a></li>
            </ul>
            <ul class="treeview-menu">
               <li><a href="fh_customertypemastercreation.php">6th Row Creations</a></li>
               <li><a href="fh_customertypemastercreation_list.php">6th Row List</a></li>
            </ul>
            <ul class="treeview-menu">
               <li><a href="fh_customertypemastercreation.php">7th Row Creations</a></li>
               <li><a href="fh_customertypemastercreation_list.php">7th Row List</a></li>
            </ul>
            <ul class="treeview-menu">
               <li><a href="fh_customertypemastercreation.php">8th Row Creations</a></li>
               <li><a href="fh_customertypemastercreation_list.php">8th Row List</a></li>
            </ul>
            <ul class="treeview-menu">
               <li><a href="fh_customertypemastercreation.php">9th Row Creations</a></li>
               <li><a href="fh_customertypemastercreation_list.php">9th Row List</a></li>
            </ul>
            <ul class="treeview-menu">
               <li><a href="fh_customertypemastercreation.php">10th Row Creations</a></li>
               <li><a href="fh_customertypemastercreation_list.php">10th Row List</a></li>
            </ul>
            <ul class="treeview-menu">
               <li><a href="fh_customertypemastercreation.php">11th Row Creations</a></li>
               <li><a href="fh_customertypemastercreation_list.php">11th Row List</a></li>
            </ul>
            <ul class="treeview-menu">
               <li><a href="fh_customertypemastercreation.php">12th Row Creations</a></li>
               <li><a href="fh_customertypemastercreation_list.php">12th Row List</a></li>
            </ul>
            <ul class="treeview-menu">
               <li><a href="fh_customertypemastercreation.php">13th Row Creations</a></li>
               <li><a href="fh_customertypemastercreation_list.php">13th Row List</a></li>
            </ul>
            <ul class="treeview-menu">
               <li><a href="fh_customertypemastercreation.php">14th Row Creations</a></li>
               <li><a href="fh_customertypemastercreation_list.php">14th Row List</a></li>
            </ul>
            <ul class="treeview-menu">
               <li><a href="fh_customertypemastercreation.php">15th Row Creations</a></li>
               <li><a href="fh_customertypemastercreation_list.php">15th Row List</a></li>
            </ul>
            <ul class="treeview-menu">
               <li><a href="fh_customertypemastercreation.php">16th Row Creations</a></li>
               <li><a href="fh_customertypemastercreation_list.php">16th Row List</a></li>
            </ul>
            <ul class="treeview-menu">
               <li><a href="fh_customertypemastercreation.php">17th Row Creations</a></li>
               <li><a href="fh_customertypemastercreation_list.php">17th Row List</a></li>
            </ul>
            <ul class="treeview-menu">
               <li><a href="fh_customertypemastercreation.php">18th Row Creations</a></li>
               <li><a href="fh_customertypemastercreation_list.php">18th Row List</a></li>
            </ul>
            <ul class="treeview-menu">
               <li><a href="fh_customertypemastercreation.php">19th Row Creations</a></li>
               <li><a href="fh_customertypemastercreation_list.php">19th Row List</a></li>
            </ul>
            <ul class="treeview-menu">
               <li><a href="fh_customertypemastercreation.php">21ss Row Creations</a></li>
               <li><a href="fh_customertypemastercreation_list.php">21st Row List</a></li>
            </ul>
            <ul class="treeview-menu">
               <li><a href="fh_customertypemastercreation.php">22nd Row Creations</a></li>
               <li><a href="fh_customertypemastercreation_list.php">22nd Row List</a></li>
            </ul>
            <ul class="treeview-menu">
               <li><a href="fh_customertypemastercreation.php">23rd Row Creations</a></li>
               <li><a href="fh_customertypemastercreation_list.php">23rd Row List</a></li>
            </ul>
            <ul class="treeview-menu">
               <li><a href="fh_customertypemastercreation.php">24th Row Creations</a></li>
               <li><a href="fh_customertypemastercreation_list.php">24th Row List</a></li>
            </ul>
            <ul class="treeview-menu">
               <li><a href="fh_customertypemastercreation.php">25th Row Creations</a></li>
               <li><a href="fh_customertypemastercreation_list.php">25th Row List</a></li>
            </ul>
         </li>
         <li class="treeview">
            <a href="#">
            <i class="fa fa-users"></i><span>Order Manage</span>
            <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
            </span>
            </a>
            <ul class="treeview-menu">
               <li>
                  <a href="#"><span style="background-color: #009688; color: white; padding: 6px; padding-right: 10px; padding-left: 10px;">All Order Manage</span>
                  </a>
                  <ul class="treeview-menu">
                     <li><a href="#">Upcoming Order</a></li>
                     <li><a href="#">Confirm Order</a></li>
                     <li><a href="#">Cancel Order</a></li>
                     <li><a href="#">Deliverd Order</a></li>
                     <li><a href="#">On the Way Deliverd Order</a></li>
                  </ul>
                  <?php include('include/divider-dotted.php');?>
               </li>
               <li>
                  <a href="#"><span style="background-color: #009688; color: white; padding: 6px; padding-right: 10px; padding-left: 10px;">State Wise || Order Manage</span>
                  </a>
                  <ul class="treeview-menu">
                     <li><a href="#">Upcoming Order</a></li>
                     <li><a href="#">Confirm Order</a></li>
                     <li><a href="#">Cancel Order</a></li>
                     <li><a href="#">Deliverd Order</a></li>
                     <li><a href="#">On the Way Deliverd Order</a></li>
                  </ul>
                  <?php include('include/divider-dotted.php');?>
               </li>
               <li>
                  <a href="#"><span style="background-color: #009688; color: white; padding: 6px; padding-right: 10px; padding-left: 10px;">City Wise || Order Manage</span>
                  </a>
                  <ul class="treeview-menu">
                     <li><a href="#">Upcoming Order</a></li>
                     <li><a href="#">Confirm Order</a></li>
                     <li><a href="#">Cancel Order</a></li>
                     <li><a href="#">Deliverd Order</a></li>
                     <li><a href="#">On the Way Deliverd Order</a></li>
                  </ul>
                  <?php include('include/divider-dotted.php');?>
               </li>
               <li>
                  <a href="#"><span style="background-color: #009688; color: white; padding: 6px; padding-right: 10px; padding-left: 10px;">Area Wise || Order Manage</span>
                  </a>
                  <ul class="treeview-menu">
                     <li><a href="#">Upcoming Order</a></li>
                     <li><a href="#">Confirm Order</a></li>
                     <li><a href="#">Cancel Order</a></li>
                     <li><a href="#">Deliverd Order</a></li>
                     <li><a href="#">On the Way Deliverd Order</a></li>
                  </ul>
                  <?php include('include/divider-dotted.php');?>
               </li>
               <li>
                  <a href="#"><span style="background-color: #009688; color: white; padding: 6px; padding-right: 10px; padding-left: 10px;">Pincode Wise || Order<br><span style="margin-left:10px;">Manage</span></span>
                  </a>
                  <ul class="treeview-menu">
                     <li><a href="#">Upcoming Order</a></li>
                     <li><a href="#">Confirm Order</a></li>
                     <li><a href="#">Cancel Order</a></li>
                     <li><a href="#">Deliverd Order</a></li>
                     <li><a href="#">On the Way Deliverd Order</a></li>
                  </ul>
                  <?php include('include/divider-dotted.php');?>
               </li>
            </ul>
         <li class="treeview">
            <a href="tejashotels-confirm-check-in.php">
            <i class="hvr-buzz-out fa fa-check"></i><span style="font-style: !important; font-size: 13px;">Reports Manage</span>
            <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
            </span>
            </a>
            <ul class="treeview-menu">
               <li><a href="#">Bill Wise</a></li>
               <li><a href="#">Item Wise</a></li>
               <li><a href="#">Mobile No. || Party Name<br> Wise</a></li>
               <li><a href="#">Pay Mode Wise</a></li>
               <li><a href="#">DPR</a></li>
               <li><a href="#">Category || Group Wise</a></li>
               <li><a href="#">Brand Wise</a></li>
               <li><a href="#">Color Wise</a></li>
               <li><a href="#">Size Wise</a></li>
               <li><a href="#">Style || Design Wise</a></li>
               <li><a href="#">Article Wise</a></li>
            </ul>
         </li>
         <li class="treeview">
            <a href="tejashotels-confirm-check-in.php">
            <i class="hvr-buzz-out fa fa-check"></i><span style="font-style: !important; font-size: 13px;">Ladger Manage</span>
            <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
            </span>
            </a>
            <ul class="treeview-menu">
               <li><a href="#">Bill Wise</a></li>
               <li><a href="#">Item Wise</a></li>
            </ul>
         </li>
         </li>
      </ul>
      <!-- /.manufacturing management -->
      <ul class="sidebar-menu">
         <li class="active" style="background-color:#009688">
            <label><i class="hvr-buzz-out fa fa-home" style="margin-left:5px; padding: 12px; font-size:15px; color: white;"></i><span style="font-style: !important; font-size: 14px; margin-left: 0px; color: white;">Manufacturing Management</span>
            <span class="pull-right-container"></span>
            </label>
         </li>
         <li class="treeview">
            <a href="#">
            <i class="fa fa-users"></i><span>Inventory || Purchase<br><span style="margin-left:30px;"> Reports</span></span>
            <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
            </span>
            </a>
            <ul class="treeview-menu">
               <li>
                  <a href="#"><span style="background-color: #009688; color: white; padding: 6px; padding-right: 10px; padding-left: 10px;">Reports</span>
                  </a>
                  <ul class="treeview-menu">
                     <li><a href="#">Bill Wise</a></li>
                     <li><a href="#">Item Wise</a></li>
                     <li><a href="#">Mobile No. || Party Name<br> Wise</a></li>
                     <li><a href="#">Pay Mode Wise</a></li>
                     <li><a href="#">DPR</a></li>
                     <li><a href="#">Category || Group Wise</a></li>
                     <li><a href="#">Brand Wise</a></li>
                     <li><a href="#">Color Wise</a></li>
                     <li><a href="#">Size Wise</a></li>
                     <li><a href="#">Style || Design Wise</a></li>
                     <li><a href="#">Article Wise</a></li>
                  </ul>
                  <?php include('include/divider-dotted.php');?>
               </li>
               <li>
                  <a href="#"><span style="background-color: #009688; color: white; padding: 6px; padding-right: 10px; padding-left: 10px;">Graph Reports</span>
                  </a>
                  <ul class="treeview-menu">
                     <li class="treeview">
                        <a href="#">
                        <span style="font-style: !important; font-size: 13px;">Bill || Graph Wise
                        </span>
                        </a>
                     </li>
                     <li class="treeview">
                        <a href="#">
                        <span style="font-style: !important; font-size: 13px;">Mobile No. - Party Name ||<br>Graph Wise</span>
                        </a>
                     </li>
                     <li class="treeview">
                        <a href="#">
                        <span style="font-style: !important; font-size: 13px;">Pay Mode || Graph Wise</span>
                        </a>
                     </li>
                     <li class="treeview">
                        <a href="#">
                        <span style="font-style: !important; font-size: 13px;">DPR || Graph Wise</span>
                        </a>
                     </li>
                     <li class="treeview">
                        <a href="#">
                        <span style="font-style: !important; font-size: 13px;">Item Name || Grapg Wise</span>
                        </a>
                     </li>
                     <li class="treeview">
                        <a href="#">
                        <span style="font-style: !important; font-size: 13px;">Category-Group || Graph<br> Wise</span>
                        </a>
                     </li>
                     <li class="treeview">
                        <a href="#">
                        <span style="font-style: !important; font-size: 13px;">Brand || Graph Wise</span>
                        </a>
                     </li>
                     <li class="treeview">
                        <a href="#">
                        <span style="font-style: !important; font-size: 13px;">Color || Graph Wise</span>
                        </a>
                     </li>
                     <li class="treeview">
                        <a href="#">
                        <span style="font-style: !important; font-size: 13px;">Size || Graph Wise</span>
                        </a>
                     </li>
                     <li class="treeview">
                        <a href="#">
                        <span style="font-style: !important; font-size: 13px;">Style-Design || Graph Wise</span>
                        </a>
                     </li>
                     <li class="treeview">
                        <a href="#">
                        <span style="font-style: !important; font-size: 13px;">Article || Graph Wise</span>
                        </a>
                     </li>
                  </ul>
                  <?php include('include/divider-dotted.php');?>
               </li>
            </ul>
         </li>
      </ul>
   </div>
   <!-- /.sidebar -->
</aside>