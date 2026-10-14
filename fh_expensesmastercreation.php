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
               <form method="post">
                  <div class="row">
                     <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
                        <div id="expenses_cardbox1">
                           <div class="statistic-box">
                              <i style="font-size:16px;" class="fa fa-user-plus fa-3x"></i>
                              <div class="counter-number pull-right">
                                 <span style="font-size:13px; color:#808080;" class="count-number" data-toggle="tooltip" title="Total Upcoming Booking">500</span>
                                 <span class="slight">
                                 <i class="fa fa-play fa-rotate-270"></i>
                                 </span>
                              </div>
                              <br><br>
                              <div class="pull-right">
                                 <span style="font-size:20px; color:#808080;"><b>CASH !</b></span>
                              </div>
                              <div class="pull-left">
                                 <span data-toggle="tooltip" title="Current Date Total Amount"><span style="font-size:20px; color:#808080;"><b>40000</b></span></span>
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
                        <div id="expenses_cardbox2">
                           <div class="statistic-box">
                              <i style="font-size:16px;" class="fa fa-user-plus fa-3x"></i>
                              <div class="counter-number pull-right">
                                 <span style="font-size:13px; color:#808080;" class="count-number" data-toggle="tooltip" title="Total Upcoming Booking">500</span>
                                 <span class="slight">
                                 <i class="fa fa-play fa-rotate-270"></i>
                                 </span>
                              </div>
                              <br><br>
                              <div class="pull-right">
                                 <span style="font-size:20px; color:#808080;"><b>UPI !</b></span>
                              </div>
                              <div class="pull-left">
                                 <span data-toggle="tooltip" title="Current Date Total Amount"><span style="font-size:20px; color:#808080;"><b>40000</b></span></span>
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
                        <div id="expenses_cardbox3">
                           <div class="statistic-box">
                              <i style="font-size:16px;" class="fa fa-user-plus fa-3x"></i>
                              <div class="counter-number pull-right">
                                 <span style="font-size:13px; color:#808080;" class="count-number" data-toggle="tooltip" title="Total Upcoming Booking">500</span>
                                 <span class="slight">
                                 <i class="fa fa-play fa-rotate-270"></i>
                                 </span>
                              </div>
                              <br><br>
                              <div class="pull-right">
                                 <span style="font-size:20px; color:#808080;"><b>CARD !</b></span>
                              </div>
                              <div class="pull-left">
                                 <span data-toggle="tooltip" title="Current Date Total Amount"><span style="font-size:20px; color:#808080;"><b>40000</b></span></span>
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
                        <div id="expenses_cardbox4">
                           <div class="statistic-box">
                              <i style="font-size:16px;" class="fa fa-user-plus fa-3x"></i>
                              <div class="counter-number pull-right">
                                 <span style="font-size:13px; color:#808080;" class="count-number" data-toggle="tooltip" title="Total Upcoming Booking">500</span>
                                 <span class="slight">
                                 <i class="fa fa-play fa-rotate-270"></i>
                                 </span>
                              </div>
                              <br><br>
                              <div class="pull-right">
                                 <span style="font-size:20px; color:#808080;"><b>CREDIT !</b></span>
                              </div>
                              <div class="pull-left">
                                 <span data-toggle="tooltip" title="Current Date Total Amount"><span style="font-size:20px; color:#808080;"><b>40000</b></span></span>
                              </div>
                           </div>
                        </div>
                     </div>
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
      <!-- Main content -->
      <section class="">
      <div class="row">
      <!-- Form controls -->
      <div class="col-sm-12">
      <div class="panel panel-bd lobidisable">
      <div class="panel-heading">
         <span class="fs-15 panel-header-text">
            <i class="fa fa-th-list mr-5"></i>
            <span>ExpensesHead Master List !</span>
         </span>
      </div>
      <div class="panel-body">
      <div class="row" >
      <div class="col-sm-4" >
      <div class="btn-group">
      <a href="fh_expensesmastercreation.php"> <button class="button-btn-btn btn-exp btn-sm"><i class="fa fa-plus" style="margin-right: 5px;"></i>Expenses Creation</button></a>
      </div>
      <?php include('include/button-export-to-data.php');?>
      </div>
      <div class="col-sm-4">
      </div>
      <div class="col-sm-2">
      <div class="form-group">
      <div class=" input-group date form_date redate">
      <input id='minMaxExample' type="text" name="year" class="form-control years"placeholder="Date From"><span class="input-group-addon"><a href="#"><i class="fa fa-calendar"></i></a></span>
      </div>
      </div>
      </div>
      <div class="col-sm-2">
      <div class="form-group">
      <div class=" input-group date form_date redate">
      <input id='minMaxExample2' type="text" name="year" class="form-control years"placeholder="Date To"><span class="input-group-addon"><a href="#"><i class="fa fa-calendar"></i></a></span>
      </div>
      </div>
      </div>
      </div>
      <div class="container-fluid">
      <div class="row">
      <input type="text" class="form-control-control" name="user_name" id="user_name" placeholder="Data Search" required>
      </div>
      </div> 
      <div class="table-responsive ">
      <table id="dataTableExample1" class="table table-bordered table-striped table-hover">
      <thead>
      <tr class="info">
      <th>SrNo</th>
      <th>Action</th>
      <th>Status</th>
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
      <tbody>
      <?php
         $query=mysqli_query($conn,"select * from tbl_expensescreation_master"); 
         $rowcount=mysqli_num_rows($query);
         for($i=1; $i<=$rowcount; $i++)
         {
           $user=mysqli_fetch_array($query);
         ?>
      <tr>
      <td>
      <div class="checkbox checkbox-info">
      <input id="checkbox1" type="checkbox">
      <label for="checkbox1"><?php echo $i; ?></label>
      </div>
      </td>
      <td style="background-color:#2A3F54" class="button-btn-btn-btn21">
      <a class=""onClick="return confirm('Are you sure you want to Update Expenses Master')" href="fh_expensesmastercreation_update.php?update_id=<?php echo $user['id']; ?>">
      <span
         data-toggle="tooltip" title="Are you sure you want to Update Expenses Master" type="button" class="" ><i style="color: white;" class="fa fa-pencil"></i><br><span style="margin-left:0px; color: white;">EDIT</span>
      </span></a><br><br>
      <a class="" onClick="return confirm('Are you sure you want to Delete Expenses Master List ?')" href="fh_expensesmastercreation_list.php?delete_id=<?php echo $user['id']; ?>">
      <span data-toggle="tooltip" title="Are you sure you want to Delete Expenses Master List" type="button" class="" ><i style="margin-left: 0px; color:white;" class="fa fa-trash-o"></i><span style="margin-left:0px; color:white;"class="">DELETE</span></span></a>
      <br><br>
      <a class="" onClick="return confirm('Are you sure you want to Details Item Name List ?')" href="fh_Detailsmastercreation_list.php?delete_id=<?php echo $user['id']; ?>">
      <span data-toggle="tooltip" title="Are you sure you want to Delete Expenses Name List" type="button" class="" ><i style="margin-left: 0px; color:white;" class="fa fa fa-list-alt"></i><span style="margin-left:0px; color:white;">DETAILS</span></span></a>
      <br><br>
      <label class="switch1">
      <input type="checkbox">
      <span class="slider1 round"></span>
      </label>
      </td>
      </td>
      <td>
      <label class="switch1">
      <input type="checkbox">
      <span class="slider1 round"></span>
      </label>
      </td>
      <td><?php echo $user['expcode']; ?>
      </td>
      <td><?php echo substr($user['date'],0,50); ?>
      </td>
      <td><?php echo substr($user['party_code'],0,50); ?>
      </td>
      <td><?php echo substr($user['account_name'],0,50); ?>
      </td>
      <td><?php echo substr($user['regular_name'],0,50); ?>
      </td>
      <td><?php echo substr($user['mobile_no'],0,50); ?>
      </td>
      <td><?php echo substr($user['exphead_name'],0,50); ?>
      </td>
      <td><?php echo substr($user['remarks'],0,50); ?>
      </td>
      <td><?php echo substr($user['paymode_name'],0,50); ?>
      </td>
      <td><?php echo substr($user['amount'],0,50); ?>
      </td>
      </tr>
      <?php } ?>
      </tbody>
      </table>
      </div>
      </div>
      </div>
      </div>
      </div>
      </section>
      <!-- /.content -->
      <section class="">
      <div class="row">
      <!-- Form controls -->
      <div class="col-sm-12">
      <div class="panel panel-bd lobidisable">
      <div class="panel-heading">
         <span class="fs-15 panel-header-text">
            <i class="fa fa-calculator mr-5"></i>
            <span>Balance Sheet !</span>
         </span>
      </div>
      <div class="panel-body">
      <div class="row">
      <div class="col-sm-4">
      <div class="btn-group">
      <a href="fh_expensesmasterbalancesheet.php"> <button class="button-btn-btn btn-exp btn-sm"><i class="fa fa-plus" style="margin-right: 5px;"></i>Balance Sheet</button></a>
      </div>
      <?php include('include/button-export-to-data.php');?>
      </div>
      <div class="col-sm-4">
      </div>
      <div class="col-sm-2">
      <div class="form-group">
      <div class=" input-group date form_date redate">
      <input id='minMaxExample' type="text" name="year" class="form-control years"placeholder="Date From"><span class="input-group-addon"><a href="#"><i class="fa fa-calendar"></i></a></span>
      </div>
      </div>
      </div>
      <div class="col-sm-2">
      <div class="form-group">
      <div class=" input-group date form_date redate">
      <input id='minMaxExample2' type="text" name="year" class="form-control years"placeholder="Date To"><span class="input-group-addon"><a href="#"><i class="fa fa-calendar"></i></a></span>
      </div>
      </div>
      </div>
      </div>
      <div class="container-fluid">
      <div class="row">
      <input type="text" class="form-control-control" name="user_name" id="user_name" placeholder="Data Search" required>
      </div>
      </div> 
      <div class="table-responsive ">
      <table id="dataTableExample1" class="table table-bordered table-striped table-hover">
      <thead>
      <tr class="info">
      <th>SrNo</th>
      <th>Date</th>
      <th>Party Name</th>
      <th>Regular Name</th>
      <th>ExpHead Name</th>
      <th>Remarks</th>
      <th>Payment Mode</th>
      <th>Amount</th>
      </tr>
      </thead>
      <tbody>
      <?php
         $query=mysqli_query($conn,"select * from tbl_expensescreation_master"); 
         $rowcount=mysqli_num_rows($query);
         for($i=1; $i<=$rowcount; $i++)
         {
           $user=mysqli_fetch_array($query);
         ?>
      <tr>
      <td>
      <div class="checkbox checkbox-info">
      <input id="checkbox1" type="checkbox">
      <label for="checkbox1"><?php echo $i; ?></label>
      </div>
      </td>
      <td><?php echo substr($user['date'],0,50); ?>
      </td>
      <td><?php echo substr($user['account_name'],0,50); ?>
      </td>
      <td><?php echo substr($user['regular_name'],0,50); ?>
      </td>
      <td><?php echo substr($user['exphead_name'],0,50); ?>
      </td>
      <td><?php echo substr($user['remarks'],0,50); ?>
      </td>
      <td><?php echo substr($user['paymode_name'],0,50); ?>
      </td>
      <td><?php echo substr($user['amount'],0,50); ?>
      </td>
      </tr>
      <?php } ?>
      </tbody>
      </table>
      </div>
      </div>
      </div>
      </div>
      </div>
      </section>
      <!-- /.content -->
      </div>
      <!-- /.content-wrapper -->
      <!-- /.footer -->
      <?php include('include/footer.php');?> 
      <!-- /.footer -->
      <?php include('include/Sidenavbuttons.php');?>
      </div>
      <!-- ./wrapper -->
      <!-- Start Core Plugins
         =====================================================================-->
      <!-- jQuery -->
      <?php include('include/js.php');?>
      <!-- End Theme label Script
         =====================================================================-->
   </body>
   <!-- Mirrored from thememinister.com/crm/add-customer.html by HTTrack Website Copier/3.x [XR&CO'2014], Fri, 05 Aug 2022 06:10:42 GMT -->
</html>