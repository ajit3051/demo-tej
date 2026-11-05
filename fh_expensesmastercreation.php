<?php include('top.php');?>
<?php
   $msg='';
    if(isset($_POST['submit']))
    {
     $date=$_POST['date'];
     $expcode=$_POST['expcode'];
     $party_code=$_POST['party_code'];
     $account_name=$_POST['account_name'];
     $regular_name=$_POST['regular_name'];
     $mobile_no=$_POST['mobile_no'];
     $exphead_name=$_POST['exphead_name'];
     $remarks=$_POST['remarks'];
     $paymode_name=$_POST['paymode_name'];
     $amount=$_POST['amount'];
     $payment_type="debit";
     
     
       
   
         $dup=mysqli_query($conn,"select * from tbl_expensescreation_master where account_name='$account_name'");
           if(mysqli_num_rows($dup)>0)
           {
               $msg='<div class="alert alert-danger alert-dismissible fade1 show">
                   <button type="button" class="close" data-dismiss="alert">&times;</button>
                   <strong>Party Name Already exist!</strong>
                   </div>';
           }
           else{
               $query="Insert into tbl_expensescreation_master(date,expcode,party_code,account_name,regular_name,mobile_no,exphead_name,remarks,paymode_name,amount,payment_type)values('$date','$expcode','$party_code','$account_name','$regular_name','$mobile_no','$exphead_name','$remarks','$paymode_name','$amount','$payment_type')";
               if(mysqli_query($conn,$query)){
                   $msg='<div class="alert alert-success alert-dismissible fade1 show">
                   <button type="button" class="close" data-dismiss="alert">&times;</button>
                   <strong>Expenses Creations Success..!</strong>
                 </div>';
               }
               else{
                   $msg='<div class="alert alert-danger alert-dismissible fade1 show">
                   <button type="button" class="close" data-dismiss="alert">&times;</button>
                   <strong>Expenses Creations Failed!</strong>
                   </div>';
               }
           } 
       }
    ?>
<!DOCTYPE html>
<html lang="en">
   <!-- Mirrored from thememinister.com/crm/add-customer.html by HTTrack Website Copier/3.x [XR&CO'2014], Fri, 05 Aug 2022 06:10:42 GMT -->
   <head>
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <title>Expenses Creation || TEJASERP</title>
      <!-- Favicon and touch icons -->
      <?php include('include/css.php');?> 
      <!-- End Theme Layout Style
         =====================================================================-->
   </head>
   <body class="hold-transition sidebar-mini">
      <!--preloader-->
      <!-- <div id="preloader">
         <div id="status"></div>
         </div> -->
      <!-- Site wrapper -->
      <div class="wrapper">
         <?php include('include/header-top-bar.php');?>
      <!-- =============================================== -->
      <!-- Left side column. contains the sidebar -->
      <aside class="main-sidebar">
         <!-- sidebar -->
         <?php include('include/sidebar-left.php');?>
         <?php include('include/toggle_switch_list.php');?> 
         <!-- /.sidebar -->
      </aside>
      <!-- =============================================== -->
      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <div class="container-fluid">
         <div class="row">
            <?php include('include/menu-header.php');?>
         </div>
      </div>
      <!-- Main content -->
      <section class="">
         <div class="row">
         <!-- Form controls -->
         <div class="col-sm-12">
         <div class="panel panel-bd lobidisable">
            <div class="panel-heading">
               <span class="fs-15 panel-header-text">
                  <i class="fa fa-pencil mr-5"></i>
                  <span>Expenses Creations !</span>
               </span>
            </div>
            <div class="panel-body">
               <div class="btn-group">
                  <a href="fh_expensesmastercreation_list.php"> <button class="button-btn-btn btn-exp btn-sm"><i class="fa fa-bars" style="margin-right: 5px;"></i>Expenses Master List</button></a>
               </div>
               <?php
               $cardData = [
                   1 => ['mode' => 'CASH', 'qty' => 0, 'amount' => 0.00],
                   2 => ['mode' => 'UPI', 'qty' => 0, 'amount' => 0.00],
                   3 => ['mode' => 'CARD', 'qty' => 0, 'amount' => 0.00],
                   4 => ['mode' => 'CREDIT', 'qty' => 0, 'amount' => 0.00],
               ];

               $bill_pay_sql = "SELECT 
                   pm.mode AS payment_mode,
                   COALESCE(SUM(CASE WHEN b.pay_mode = pm.mode THEN b.total_qty ELSE 0 END), 0) AS total_qty,
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
                   GROUP BY bill_number
               ) b ON (b.pay_mode = pm.mode OR (pm.mode = 'CASH' AND b.bill_cash > 0) OR (pm.mode = 'UPI' AND b.bill_upi > 0) OR (pm.mode = 'CARD' AND b.bill_card > 0) OR (pm.mode = 'CREDIT' AND b.bill_credit > 0))
               GROUP BY pm.mode
               ORDER BY FIELD(pm.mode, 'CASH', 'UPI', 'CARD', 'CREDIT')";

               $bill_res = mysqli_query($conn, $bill_pay_sql);
               $modeIndex = ['CASH' => 1, 'UPI' => 2, 'CARD' => 3, 'CREDIT' => 4];
               if ($bill_res) {
                   while ($brow = mysqli_fetch_assoc($bill_res)) {
                       $m = strtoupper(trim($brow['payment_mode']));
                       if (isset($modeIndex[$m])) {
                           $idx = $modeIndex[$m];
                           $cardData[$idx]['qty'] = (int)$brow['total_qty'];
                           $cardData[$idx]['amount'] = (float)$brow['total_amount'];
                       }
                   }
               }
               ?>
                  <div class="row">
                     <?php foreach ($cardData as $boxId => $card): ?>
                     <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
                        <div id="expenses_cardbox<?= $boxId; ?>">
                           <div class="statistic-box">
                              <i style="font-size:16px;" class="fa fa-user-plus fa-3x"></i>
                              <div class="counter-number pull-right">
                                 <span style="font-size:13px; color:#808080;" class="count-number" data-toggle="tooltip" title="Total Upcoming Booking"><?= $card['qty']; ?></span>
                                 <span class="slight">
                                 <i class="fa fa-play fa-rotate-270"></i>
                                 </span>
                              </div>
                              <br><br>
                              <div class="pull-right">
                                 <span style="font-size:20px; color:#808080;"><b><?= $card['mode']; ?> !</b></span>
                              </div>
                              <div class="pull-left">
                                 <span data-toggle="tooltip" title="Current Date Total Amount"><span style="font-size:20px; color:#808080;"><b><?= number_format($card['amount'], 2, '.', ''); ?></b></span></span>
                              </div>
                           </div>
                        </div>
                     </div>
                     <?php endforeach; ?>
                  </div>
                  <div class="row">
                     <div class="">
                        <div class="panel-body">
                           <!-- Nav tabs -->
                           <ul class="nav nav-tabs">
                              <li class="active" style=""><a href="#tab1" data-toggle="tab">Debit || Payment</a></li>
                              <li><a href="#tab2" data-toggle="tab">Credit || Payment</a></li>
                           </ul>
                           <!-- Tab panels -->
                           <div class="tab-content">
                              <div class="tab-pane fade in active" id="tab1">
                                 <div class="panel-body">
                                    <table class="table table-bordered table-striped table-hover">
               <form method="post">
               <div class="form-group">
               <?php echo $msg; ?>
               </div>
               <div class="row">
               <div class="col-sm-3">
               <div class="form-group">
               <label>Code</label><label style="color:red;">*</label>
               <input type="text" name="expcode" class="form-control" id="expcode" readonly value="<?php @$no = mysqli_query($conn, "SELECT id FROM  tbl_expensescreation_master ORDER BY id DESC");
                  $po_no = mysqli_fetch_assoc($no);
                  echo 'EC-000'.($po_no['id']+1);
                  ?>">
               </div>
               </div>
               <div class="col-sm-4">
               <div class="form-group">
               <label>Date</label><label style="color:red;">*</label>
               <input id='minMaxExample' type="text" name="date" id="date" class="form-control" placeholder="Enter Date...">
               </div>
               </div>
               </div>
               <div class="row">
               <div class="col-sm-3">
               <div class="form-group">
               <label>Party Code</label><label style="color:red;">*</label>
               <input type="text" class="form-control" name="party_code" id="party_code" placeholder="Enter Your Account Name" required>
               </div>
               </div>
               <div class="col-sm-4">
               <div class="form-group">
               <label>Party Name</label><label style="color:red;">*</label>
               <select name="account_name" class="form-control" required>
               <option value="null">Select Party Type</option>
               <?php
                  $query = mysqli_query($conn, "SELECT * FROM tbl_account_master");
                  while ($row = mysqli_fetch_array($query)) {
                  ?>
               <option><?php echo $row['account_name']; ?></option>
               <?php } ?> 
               </select>
               </div>
               </div>
               <div class="col-sm-5">
               <div class="form-group">
               <label>Regural Name</label>
               <input type="text" class="form-control" name="regular_name" id="regular_name" placeholder="Enter Your Regular Name" >
               </div>
               </div>
               </div>
               <div class="row">
               <div class="col-sm-3">
               <div class="form-group">
               <label>Mobile No.</label><label style="color:red;">*</label>
               <input type="text" class="form-control" name="mobile_no" id="mobile_no" placeholder="Enter Your Mobile No" required>
               </div>
               </div>
               <div class="col-sm-4">
               <div class="form-group">
               <label>Expenses Head Name</label><label style="color:red;">*</label>
               <select name="exphead_name" class="form-control" required>
               <option value="null">Select Expenses Head Name</option>
               <?php
                  $query = mysqli_query($conn, "SELECT * FROM tbl_expenseshead_master");
                     while ($row =mysqli_fetch_array($query)) {
                        ?>
               <option><?php echo $row['exphead_name']; ?></option>
               <?php } ?> 
               </select>
               </div>
               </div>
               <div class="col-sm-5">
               <div class="form-group">
               <label>Remarks</label>
               <input type="text" class="form-control" name="remarks" id="remarks" placeholder="Enter Your Remarks" >
               </div>
               </div>
               </div>
               <div class="row">
               <div class="col-sm-3">
               <div class="form-group">
               <label>Payment Mode</label><label style="color:red;">*</label>
               <select name="paymode_name" class="form-control" required>
               <option value="null">Select Payment Mode</option>
               <?php
                  $query = mysqli_query($conn, "SELECT * FROM tbl_paymode_master");
                     while ($row = mysqli_fetch_array($query)) {
                        ?>
               <option><?php echo $row['paymode_name']; ?></option>
               <?php } ?> 
               </select>
               </div>
               </div>
               <div class="col-sm-4">
               <div class="form-group">
               <label>Amount</label><label style="color:red;">*</label>
               <input type="text" class="form-control" name="amount" id="amount" placeholder="Enter Your Amount" required>
               </div>
               </div>
               </div><br>
               <div class="container-fluid">
               <div class="row">
               <button type="submit" name="submit" class="btn btn-warning" style="width:100px;">Reset</button>
               <button type="submit" name="submit" class="btn btn-success" style="width:100px;background-color: #009688; color: white;">Save</button>
               </div>
               </div>
               </form>
               </table>
               </div>
               </div>
               <div class="tab-pane fade" id="tab2">
               <div class="panel-body">
               <form method="post">
               <div class="form-group">
               <?php echo $msg; ?>
               </div>
               <div class="row">
               <div class="col-sm-3">
               <div class="form-group">
               <label>Code</label>
               <input type="text" name="expcode" class="form-control" id="expcode" readonly value="<?php @$no = mysqli_query($conn, "SELECT id FROM  tbl_expensescreation_master ORDER BY id DESC");
                  $po_no = mysqli_fetch_assoc($no);
                  echo 'ECC-000'.($po_no['id']+1);
                  ?>">
               </div>
               </div>
               <div class="col-sm-4">
               <div class="form-group">
               <label>Date</label>
               <input id='minMaxExample' type="text" name="date" id="date" class="form-control" placeholder="Enter Date...">
               </div>
               </div>
               </div>
               <div class="row">
               <div class="col-sm-3">
               <div class="form-group">
               <label>Party Code</label>
               <input type="text" class="form-control" name="party_code" id="party_code" placeholder="Enter Your Account Name" required>
               </div>
               </div>
               <div class="col-sm-4">
               <div class="form-group">
               <label>Party Name</label>
               <select name="account_name" class="form-control" required>
               <option value="null">Select Party Type</option>
               <?php
                  $query = mysqli_query($conn, "SELECT * FROM tbl_account_master");
                  while ($row = mysqli_fetch_array($query)) {
                  ?>
               <option><?php echo $row['account_name']; ?></option>
               <?php } ?> 
               </select>
               </div>
               </div>
               <div class="col-sm-5">
               <div class="form-group">
               <label>Regural Name</label>
               <input type="text" class="form-control" name="regular_name" id="regular_name" placeholder="Enter Your Regular Name" required>
               </div>
               </div>
               </div>
               <div class="row">
               <div class="col-sm-3">
               <div class="form-group">
               <label>Mobile No.</label>
               <input type="text" class="form-control" name="mobile_no" id="mobile_no" placeholder="Enter Your Mobile No" required>
               </div>
               </div>
               <div class="col-sm-4">
               <div class="form-group">
               <label>Expenses Head Name</label>
               <select name="exphead_name" class="form-control" required>
               <option value="null">Select Expenses Head Name</option>
               <?php
                  $query = mysqli_query($conn, "SELECT * FROM tbl_expenseshead_master");
                     while ($row =mysqli_fetch_array($query)) {
                        ?>
               <option><?php echo $row['exphead_name']; ?></option>
               <?php } ?> 
               </select>
               </div>
               </div>
               <div class="col-sm-5">
               <div class="form-group">
               <label>Remarks</label>
               <input type="text" class="form-control" name="remarks" id="remarks" placeholder="Enter Your Remarks" required>
               </div>
               </div>
               </div>
               <div class="row">
               <div class="col-sm-3">
               <div class="form-group">
               <label>Payment Mode</label>
               <select name="paymode_name" class="form-control" required>
               <option value="null">Select Payment Mode</option>
               <?php
                  $query = mysqli_query($conn, "SELECT * FROM tbl_paymode_master");
                     while ($row = mysqli_fetch_array($query)) {
                        ?>
               <option><?php echo $row['paymode_name']; ?></option>
               <?php } ?> 
               </select>
               </div>
               </div>
               <div class="col-sm-4">
               <div class="form-group">
               <label>Amount</label>
               <input type="text" class="form-control" name="amount" id="amount" placeholder="Enter Your Amount" required>
               </div>
               </div>
               </div><br>
               <div class="container-fluid">
               <div class="row">
               <button type="submit" name="submit" class="btn btn-warning" style="width:100px;">Reset</button>
               <button type="submit" name="submit" class="btn btn-success" style="width:100px;background-color: #009688; color: white;">Save</button>
               </div>
               </div>
               </form>
               </div>
               </div>
               </div>
               </div>
               </div>
               </div>
      </section>
             <?php include('include/fh-form-scrolling-data-list.php');?>
            <!-- Main content -->
            <section class="">
               <div class="row">
                  <!-- Form controls -->
                  <div class="col-sm-12">
                     <div class="panel panel-bd lobidisable">
                        <div class="panel-heading">
                           <span class="fs-15 panel-header-text">
                              <i class="fa fa-th-list mr-5"></i>
                              <span>Expenses Creation List !</span>
                           </span>
                        </div>
                        <div class="panel-body form-scroll">
                           <!-- Toolbar & Search Box Row under Header Panel -->
                           <div class="row m-b-15">
                              <div class="col-sm-6 col-xs-12 m-b-5">
                                 <div class="btn-group">
                                    <a href="fh_expensesmastercreation.php" class="btn btn-exp btn-sm"><i class="fa fa-plus mr-5"></i>Add New Expenses Creation</a>
                                 </div>
                                 <?php include('include/button-export-to-data.php');?>
                              </div>
                              <div class="col-sm-6 col-xs-12">
                                 <form id="expensesMasterSearchForm" class="search-form-wrap pull-right" onsubmit="return false;" style="width: 100%; max-width: 360px;">
                                    <input type="hidden" name="page" id="page" value="1">
                                    <div class="input-group search-box-group">
                                       <input type="text" class="form-control search-input search-input-field" name="search" id="company_name" placeholder="Search Expenses..." autocomplete="off">
                                       <span class="input-group-addon search-addon-btn" id="expensesMasterSearchBtn" style="cursor: pointer;" title="Click to search">
                                          <i class="fa fa-search search-icon-sm"></i>
                                       </span>
                                    </div>
                                 </form>
                              </div>
                           </div>
                           <div class="table-responsive">
                              <table id="dataTableExample1" class="table table-bordered table-striped table-hover">
                                 <thead>
                                    <tr class="info">
                                       <th class="th-width-40-center">#</th>
                                       <th class="th-minwidth-100-center">Action</th>
                                       <th class="text-center-align">Status</th>
                                       <th>ExpCode No</th>
                                       <th>Date</th>
                                       <th>Party Code</th>
                                       <th>Party Name</th>
                                       <th>Regular Name</th>
                                       <th>Mobile No</th>
                                       <th>ExpHead Name</th>
                                       <th>Remarks</th>
                                       <th>Payment Mode</th>
                                       <th>Amount</th>
                                    </tr>
                                 </thead>
                                 <tbody id="expensesmaster-list-results">
                                    <?php
                                       $init_page = 1;
                                       $init_perpage = isset($_SESSION["_RECORDPERPAGE_"]) && $_SESSION["_RECORDPERPAGE_"] ? (int)$_SESSION["_RECORDPERPAGE_"] : 20;
                                       $init_offset = ($init_page - 1) * $init_perpage;
                                       $query = mysqli_query($conn, "SELECT * FROM tbl_expensescreation_master ORDER BY id DESC LIMIT $init_offset, $init_perpage"); 
                                       $countQuery = mysqli_query($conn, "SELECT COUNT(*) as total FROM tbl_expensescreation_master");
                                       $countRow = mysqli_fetch_assoc($countQuery);
                                       $totalRecords = $countRow['total'] ?? 0;
                                       $expenses_list = array();
                                       while ($e = mysqli_fetch_array($query)) {
                                          $expenses_list[] = $e;
                                       }
                                       $i = $init_offset + 1;
                                       foreach ($expenses_list as $user) {
                                      ?>
                                    <tr>
                                       <td class="td-center-middle"><?php echo $i++; ?></td>
                                       <td class="td-center-middle-nowrap">
                                          <a class="btn btn-info btn-xs" data-toggle="tooltip" title="Edit Expenses" href="fh_expensesmastercreation.php?update_id=<?php echo $user['id']; ?>">
                                             <i class="fa fa-pencil"></i> Edit
                                          </a>
                                          <a class="btn btn-danger btn-xs" data-toggle="tooltip" title="Delete Expenses" href="fh_expensesmastercreation_list.php?delete_id=<?php echo $user['id']; ?>" onclick="return confirm('Are you sure you want to delete this record?');">
                                             <i class="fa fa-trash-o"></i> Delete
                                          </a>
                                       </td>
                                       <td class="td-center-middle"><span class="label label-success badge-status-sm">Active</span></td>
                                       <td><?php echo htmlspecialchars($user['expcode'] ?? ''); ?></td>
                                       <td><?php echo htmlspecialchars($user['date'] ?? ''); ?></td>
                                       <td><?php echo htmlspecialchars($user['party_code'] ?? ''); ?></td>
                                       <td><?php echo htmlspecialchars($user['account_name'] ?? ''); ?></td>
                                       <td><?php echo htmlspecialchars($user['regular_name'] ?? ''); ?></td>
                                       <td><?php echo htmlspecialchars($user['mobile_no'] ?? ''); ?></td>
                                       <td><?php echo htmlspecialchars($user['exphead_name'] ?? ''); ?></td>
                                       <td><?php echo htmlspecialchars($user['remarks'] ?? ''); ?></td>
                                       <td><?php echo htmlspecialchars($user['paymode_name'] ?? ''); ?></td>
                                       <td><?php echo htmlspecialchars($user['amount'] ?? ''); ?></td>
                                    </tr>
                                    <?php } ?>
                                 </tbody>
                              </table>
                           </div>
                           <div class="row pagination-wrapper-row">
                              <div class="col-sm-6">
                                 <div class="pagination-info-text">
                                    Total Records: <strong id="total-records-count"><?php echo $totalRecords; ?></strong>
                                 </div>
                              </div>
                              <div class="col-sm-6">
                                 <div id="expensesmaster-pagination">
                                    <?php
                                       $totalPages = ceil($totalRecords / $init_perpage);
                                       if ($totalPages > 1) {
                                          echo '<ul class="pagination pagination-sm pagination-no-margin pull-right">';
                                          echo '<li class="disabled"><span>&laquo; Prev</span></li>';
                                          for ($p = 1; $p <= min(5, $totalPages); $p++) {
                                             if ($p == 1) {
                                                echo '<li class="active"><span>' . $p . '</span></li>';
                                             } else {
                                                echo '<li><a href="javascript:void(0)" onclick="loadExpensesMasterData(' . $p . ')">' . $p . '</a></li>';
                                             }
                                          }
                                          if ($totalPages > 5) {
                                             echo '<li class="disabled"><span>...</span></li>';
                                             echo '<li><a href="javascript:void(0)" onclick="loadExpensesMasterData(' . $totalPages . ')">' . $totalPages . '</a></li>';
                                          }
                                          echo '<li><a href="javascript:void(0)" onclick="loadExpensesMasterData(2)">Next &raquo;</a></li>';
                                          echo '</ul>';
                                       }
                                    ?>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </section>
         </div>
         <!-- /.content-wrapper -->
         <?php include('include/footer.php');?>
         <?php include('include/Sidenavbuttons.php');?>
      </div>
      <!-- ./wrapper -->
      <?php include('include/js.php');?>
      <script src="assets/dist/js/page/expensesmaster-list.js"></script>
   </body>
   <!-- Mirrored from thememinister.com/crm/add-customer.html by HTTrack Website Copier/3.x [XR&CO'2014], Fri, 05 Aug 2022 06:10:42 GMT -->
</html>