<?php include('top.php');?>
<?php
    $msg=''; 
     if(isset($_POST['submit']))
     {
      $date=$_POST['date'];
      $account_code=$_POST['account_code'];
      $account_name=$_POST['account_name'];
      $contact_name=$_POST['contact_name'];
      $mobile_no1=$_POST['mobile_no1'];
      $mobile_no2=$_POST['mobile_no2'];
      $phone_no=$_POST['phone_no'];
      $email_id=$_POST['email_id'];
      $gst_no=$_POST['gst_no'];
      $partytype_name=$_POST['partytype_name'];
      $customertype_name=$_POST['customertype_name'];
      $location_name=$_POST['location_name'];
      $address_1=$_POST['address_1'];
      $address_2=$_POST['address_2'];
      $opn_bal=$_POST['opn_bal'];
      $picture=$_POST['picture'];
      $gender=$_POST['gender'];
      $status=$_POST['status'];

      
    
          $dup=mysqli_query($conn,"select * from tbl_account_master where account_name='$account_name'");
            if(mysqli_num_rows($dup)>0)
            {
                $msg='<div class="alert alert-danger alert-dismissible fade1 show">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <strong>Account Master Already exist!</strong>
                    </div>';
            }
            else{
                $query="Insert into tbl_account_master(date,account_code,account_name,contact_name,mobile_no1,mobile_no2,phone_no,email_id,gst_no,partytype_name,customertype_name,location_name,address_1,address_2,opn_bal,picture,gender,status)values('$date','$account_code','$account_name','$contact_name','$mobile_no1','$mobile_no2','$phone_no','$email_id','$gst_no','$partytype_name','$customertype_name','$location_name','$address_1','$address_2','$opn_bal','$picture', '$gender', '$status')";
                if(mysqli_query($conn,$query)){
                    $msg='<div class="alert alert-success alert-dismissible fade1 show">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <strong>Account Maste Creations Success..!</strong>
                  </div>';
                }
                else{
                    $msg='<div class="alert alert-danger alert-dismissible fade1 show">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <strong>Account Creations Failed!</strong>
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
      <title>Account Master Creations || TEJASERP</title>
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
                              <span>Account Master Creations !</span>
                           </span>
                        </div>
                        <div class="panel-body">
                              <div class="btn-group">
                              <a href="fh_accountmastercreation_list.php"> <button class="button-btn-btn btn-exp btn-sm"><i class="fa fa-bars" style="margin-right: 5px;"></i>Account Master List</button></a>
                           </div>
                           <form method="post">
                              
                             

                              <div class="row">
                                 
                                 <div class="col-sm-2">
                                    <div class="form-group">
                                       <label>Code</label>
                                       <input type="text" name="account_code" class="form-control" id="account_code" readonly value="<?php @$no = mysqli_query($conn, "SELECT id FROM  tbl_account_master ORDER BY id DESC");
                                          $po_no = mysqli_fetch_assoc($no);
                                          echo 'AC-000'.($po_no['id']+1);
                                          ?>">
                                       </div>
                                    </div>
                                    <div class="col-sm-5">
                                       <div class="form-group">
                                          <label>Account Name</label>
                                          <input type="text" class="form-control" name="account_name" id="account_name" placeholder="Enter Your Account Name" required>
                                       </div>
                                    </div>
                                    <div class="col-sm-5">
                                       <div class="form-group">
                                         <label>Contact Name</label>
                                         <input type="text" class="form-control" name="contact_name" id="contact_name" placeholder="Enter Your Contact Name" required>
                                      </div>
                                   </div>
                                </div>
                                <div class="row">
                                 <div class="col-sm-2">
                                    <div class="form-group">
                                       <label>Date</label>
                                       <input id='minMaxExample' type="text" name="date" id="date" class="form-control" placeholder="Enter Date...">
                                    </div>
                                 </div>
                                 <div class="col-sm-5">
                                    <div class="form-group">
                                                   <label>Mobile No.1</label>
                                                   <input type="text" class="form-control" name="mobile_no1" id="mobile_no1" placeholder="Enter Your Mobile No" required>
                                                </div>
                                 </div>
                                 <div class="col-sm-5">
                                    <div class="form-group">
                                                   <label>Mobile No.2</label>
                                                   <input type="text" class="form-control" name="mobile_no2" id="mobile_no2" placeholder="Enter Your Mobile No" required>
                                                </div>
                                             </div>
                                          
                                          </div>
                                          
                                          <div class="row">
                                             
                                             <div class="col-sm-4">
                                                <div class="form-group">
                                                   <label>Phone No.</label>
                                                   <input type="text" class="form-control" name="phone_no" id="phone_no" placeholder="Enter Your Phone No" required>
                                                </div>
                                             </div>
                                             <div class="col-sm-4">
                                                <div class="form-group">
                                                   <label>Email-ID</label>
                                                   <input type="text" class="form-control" name="email_id" id="email_id" placeholder="Enter Your Email-ID" required>
                                                </div>
                                             </div>

                                             <div class="col-sm-4">
                                                <div class="form-group">
                                                   <label>GST No</label>
                                                   <input type="text" class="form-control" name="gst_no" id="gst_no" placeholder="Enter Your GST No" required>
                                                </div>
                                             </div>
                                             
                                             
                                             </div>

                                          

                                          <div class="row">
                                             
                                             <div class="col-lg-4">
                                                <div class="form-group">
                                                   <label>Party Type</label>
                                                    <select name="partytype_name" class="form-control" required>
                                                        <option value="null">Select Party Type</option>
                                                        <?php
                                                        $query = mysqli_query($conn, "SELECT * FROM tbl_partytype_master");
                                                        while ($row = mysqli_fetch_array($query)) {
                                                        ?>
                                                            <option><?php echo $row['partytype_name']; ?></option>
                                                        <?php } ?> 
                                                    </select>
                                                </div>
                                             </div>

                                             <div class="col-lg-4">
                                                <div class="form-group">
                                                   <label>Customer Type</label>
                                                    <select name="customertype_name" class="form-control" required>
                                                        <option value="null">Select Customer Type Name</option>
                                                        <?php
                                                        $query = mysqli_query($conn, "SELECT * FROM tbl_customertype_master");
                                                        while ($row = mysqli_fetch_array($query)) {
                                                        ?>
                                                            <option><?php echo $row['customertype_name']; ?></option>
                                                        <?php } ?> 
                                                    </select>
                                                </div>
                                             </div>

                                             <div class="col-lg-4">
                                                <div class="form-group">
                                                   <label>Location</label>
                                                    <select name="location_name" class="form-control" required>
                                                        <option value="null">Select Location Name</option>
                                                        <?php
                                                        $query = mysqli_query($conn, "SELECT * FROM tbl_location_master");
                                                        while ($row = mysqli_fetch_array($query)) {
                                                        ?>
                                                            <option><?php echo $row['location_name']; ?></option>
                                                        <?php } ?> 
                                                    </select>
                                                </div>
                                             </div>


                                          </div>

                                          <div class="row">
                                             
                                             <div class="col-lg-6">
                                                <div class="form-group">
                                                   <label>Address 1</label>
                                                   <input type="text" class="form-control" name="address_1" id="address_1" placeholder="Enter Your Address" required>
                                                </div>
                                             </div>
                                             <div class="col-lg-6">
                                                <div class="form-group">
                                                   <label>Address 2</label>
                                                   <input type="text" class="form-control" name="address_2" id="address_2" placeholder="Enter Your Address" required>
                                                </div>
                                             </div>
                                             
                                          </div>

                                          <div class="row">
                                             
                                             <div class="col-lg-6">
                                                <div class="form-group">
                                                   <label>Opening Balance</label>
                                                   <input type="text" class="form-control" name="opn_bal" id="opn_bal" placeholder="Enter Opening Bal" required>
                                                </div>
                                             </div>
                                             
                                          </div><br>
                                          
                                          
                                          
                                          
                                          
                                          <div class="row">
                                             <div class="col-sm-4">
                                                <div class="form-group">
                                                   <label>Picture upload</label>
                                                   <input type="file" name="picture" id="picture">
                                                   <!-- <input type="hidden" name="old_picture"> -->
                                                </div>
                                             </div>
                                             <div class="col-sm-4">
                                                <div class="form-group">
                                                   <label>Gender</label><br>
                                                   <label class="radio-inline"><input name="gender" value="1" checked="checked" type="radio">Male</label> 
                                                   <label class="radio-inline"><input name="gender" value="0" type="radio">Female</label>
                                                </div>
                                             </div>
                                             <div class="col-sm-4">
                                                <div class="form-check">
                                                   <label>Status</label><br>
                                                   <label class="radio-inline">
                                                      <input type="radio" name="status" value="true" checked="checked">Active</label>
                                                      <label class="radio-inline"><input type="radio" name="status" value="false" >Inctive</label>
                                                   </div>
                                                </div>
                                             </div><br>
                                             <div class="container-fluid">
                                                <div class="row">
                                                   <button type="submit" name="submit" class="btn btn-warning" style="width:100px;">Reset</button>
                                                <button type="submit" name="submit" class="btn btn-success" style="width:100px;background-color: #009688; color: white;">Save</button>
                                                </div><br>
                                                
                                             </div>
                                          </form>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </section>
             <?php include('include/fh-popup-message-successfully.php');?>
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
                               <span>Account Master List !</span>
                            </span>
                         </div>
                         <div class="panel-body form-scroll">
                            <!-- Toolbar & Search Box Row under Header Panel -->
                            <div class="row m-b-15">
                               <div class="col-sm-6 col-xs-12 m-b-5">
                                  <div class="btn-group">
                                     <a href="fh_accountmastercreation.php" class="btn btn-exp btn-sm"><i class="fa fa-plus mr-5"></i>Add New Account Master Creation</a>
                                  </div>
                                  <?php include('include/button-export-to-data.php');?>
                               </div>
                               <div class="col-sm-6 col-xs-12">
                                  <form id="accountMasterSearchForm" class="search-form-wrap pull-right" onsubmit="return false;" style="width: 100%; max-width: 360px;">
                                     <input type="hidden" name="page" id="page" value="1">
                                     <div class="input-group search-box-group">
                                        <input type="text" class="form-control search-input search-input-field" name="search" id="company_name" placeholder="Search Accounts..." autocomplete="off">
                                        <span class="input-group-addon search-addon-btn" id="accountMasterSearchBtn" style="cursor: pointer;" title="Click to search">
                                           <i class="fa fa-search search-icon-sm"></i>
                                        </span>
                                     </div>
                                  </form>
                                  <div class="clearfix"></div>
                               </div>
                            </div>
                            <div class="table-responsive">
                               <table id="dataTableExample1" class="table table-bordered table-striped table-hover">
                                  <thead>
                                     <tr class="info">
                                        <th class="th-width-40-center">#</th>
                                        <th style="min-width: 175px; text-align: center;">Action</th>
                                        <th class="text-center-align">Status</th>
                                        <th>Account Code</th>
                                        <th>Account Name</th>
                                        <th>Contact Name</th>
                                        <th>Group Name</th>
                                        <th>Mobile No</th>
                                        <th>GST No</th>
                                        <th>Location</th>
                                        <th>Party Type</th>
                                        <th>Opening Bal</th>
                                     </tr>
                                  </thead>
                                  <tbody id="accountmaster-list-results">
                                     <?php
                                        $init_page = 1;
                                        $init_perpage = isset($_SESSION["_RECORDPERPAGE_"]) && $_SESSION["_RECORDPERPAGE_"] ? (int)$_SESSION["_RECORDPERPAGE_"] : 20;
                                        $init_offset = ($init_page - 1) * $init_perpage;
                                        $query = mysqli_query($conn, "SELECT * FROM tbl_account_master ORDER BY id DESC LIMIT $init_offset, $init_perpage"); 
                                        $countQuery = mysqli_query($conn, "SELECT COUNT(*) as total FROM tbl_account_master");
                                        $countRow = mysqli_fetch_assoc($countQuery);
                                        $totalRecords = $countRow['total'] ?? 0;
                                        $account_list = array();
                                        while ($a = mysqli_fetch_array($query)) {
                                           $account_list[] = $a;
                                        }
                                        $i = $init_offset + 1;
                                        foreach ($account_list as $user) {
                                           $statusBadge = ($user['status'] ?? 'Active') === 'Active' 
                                               ? '<span class="label label-success badge-status-sm">Active</span>' 
                                               : '<span class="label label-danger badge-status-sm">Inactive</span>';
                                       ?>
                                     <tr>
                                        <td class="td-center-middle"><?php echo $i++; ?></td>
                                        <td class="td-center-middle-nowrap">
                                           <a class="btn btn-info btn-xs" data-toggle="tooltip" title="Edit Account" href="fh_accountmastercreation.php?update_id=<?php echo $user['id']; ?>">
                                              <i class="fa fa-pencil"></i> Edit
                                           </a>
                                           <a class="btn btn-warning btn-xs" data-toggle="tooltip" title="View Ledger" href="fh_account_ledger.php?account_code=<?php echo urlencode($user['account_code'] ?? ''); ?>&account_id=<?php echo $user['id']; ?>">
                                              <i class="fa fa-book"></i> Ledger
                                           </a>
                                           <a class="btn btn-danger btn-xs" data-toggle="tooltip" title="Delete Account" href="fh_accountmastercreation_list.php?delete_id=<?php echo $user['id']; ?>" onclick="return confirm('Are you sure you want to delete this record?');">
                                              <i class="fa fa-trash-o"></i> Delete
                                           </a>
                                        </td>
                                        <td class="td-center-middle"><?php echo $statusBadge; ?></td>
                                        <td><?php echo htmlspecialchars($user['account_code'] ?? ''); ?></td>
                                        <td><?php echo htmlspecialchars($user['account_name'] ?? ''); ?></td>
                                        <td><?php echo htmlspecialchars($user['contact_name'] ?? ''); ?></td>
                                        <td><?php echo htmlspecialchars($user['group_name'] ?? ''); ?></td>
                                        <td><?php echo htmlspecialchars($user['mobile_no_1'] ?? ''); ?></td>
                                        <td><?php echo htmlspecialchars($user['gst_no'] ?? ''); ?></td>
                                        <td><?php echo htmlspecialchars($user['location_1'] ?? ''); ?></td>
                                        <td><?php echo htmlspecialchars($user['party_type'] ?? ''); ?></td>
                                        <td><?php echo htmlspecialchars($user['opening_balance'] ?? ''); ?></td>
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
                                  <div id="accountmaster-pagination">
                                     <?php
                                        $totalPages = ceil($totalRecords / $init_perpage);
                                        if ($totalPages > 1) {
                                           echo '<ul class="pagination pagination-sm pagination-no-margin pull-right">';
                                           echo '<li class="disabled"><span>&laquo; Prev</span></li>';
                                           for ($p = 1; $p <= min(5, $totalPages); $p++) {
                                              if ($p == 1) {
                                                 echo '<li class="active"><span>' . $p . '</span></li>';
                                              } else {
                                                 echo '<li><a href="javascript:void(0)" onclick="loadAccountMasterData(' . $p . ')">' . $p . '</a></li>';
                                              }
                                           }
                                           if ($totalPages > 5) {
                                              echo '<li class="disabled"><span>...</span></li>';
                                              echo '<li><a href="javascript:void(0)" onclick="loadAccountMasterData(' . $totalPages . ')">' . $totalPages . '</a></li>';
                                           }
                                           echo '<li><a href="javascript:void(0)" onclick="loadAccountMasterData(2)">Next &raquo;</a></li>';
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
       <script src="assets/dist/js/page/accountmaster-list.js?v=<?= time(); ?>"></script>
    </body>

<!-- Mirrored from thememinister.com/crm/add-customer.html by HTTrack Website Copier/3.x [XR&CO'2014], Fri, 05 Aug 2022 06:10:42 GMT -->
</html>

