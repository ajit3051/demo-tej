<?php include('top.php');?>
<?php
   if(isset($_GET['delete_id']))
   {
      $id=$_GET['delete_id'];
      $sqld="DELETE FROM tbl_user_master WHERE id='$id'";
      $res=mysqli_query($conn,$sqld);
      if($res)
      {
       header('refresh:.5; url=fh_usermastercreation_list.php');
      }else{
       echo "<script>alert('User Master Record Failed');</script>";
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
      <title>User Master List</title>
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
            
            <?php include('include/fh-form-scrolling-data-list.php');?>
            <!-- Main content -->
            <!-- Main content -->
            <section class="">
               <div class="row">
                  <div class="col-sm-12">
                     <div class="panel panel-bd lobidisable">
                        <div class="panel-heading">
                           <span class="fs-15 panel-header-text">
                              <i class="fa fa-th-list mr-5"></i>
                              <span>User Master List !</span>
                           </span>
                        </div>
                         <div class="panel-body form-scroll">
                            <!-- Toolbar & Search Box Row under Header Panel -->
                            <div class="row m-b-15">
                               <div class="col-sm-6 col-xs-12 m-b-5">
                                  <div class="btn-group">
                                     <a href="fh_usermastercreation.php" class="btn btn-exp btn-sm"><i class="fa fa-plus mr-5"></i>Add New User Master Creation</a>
                                  </div>
                                  <?php include('include/button-export-to-data.php');?>
                               </div>
                               <div class="col-sm-6 col-xs-12">
                                  <!-- Search Form Box under Header Panel -->
                                  <form id="userMasterSearchForm" class="search-form-wrap pull-right" onsubmit="return false;" style="width: 100%; max-width: 360px;">
                                     <input type="hidden" name="page" id="page" value="1">
                                     <div class="input-group search-box-group">
                                        <input type="text" class="form-control search-input search-input-field" name="search" id="company_name" placeholder="Search Users..." autocomplete="off">
                                        <span class="input-group-addon search-addon-btn">
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
                                          <th class="th-minwidth-100-center">Action</th>
                                          <th class="text-center-align">Status</th>
                                          <th class="text-center-align">Picture</th>
                                          <th>User Code</th>
                                          <th>User Name</th>
                                          <th>Mobile No</th>
                                          <th>Store Name</th>
                                          <th>Location</th>
                                          <th>User Type</th>
                                          <th>Created Date</th>
                                          <th>Updated Date</th>
                                          <th class="text-center-align">Gender</th>
                                       </tr>
                                    </thead>
                                    <tbody id="usermaster-list-results">
                                       <?php
                                          $init_page = 1;
                                          $init_perpage = isset($_SESSION["_RECORDPERPAGE_"]) && $_SESSION["_RECORDPERPAGE_"] ? (int)$_SESSION["_RECORDPERPAGE_"] : 20;
                                          $init_offset = ($init_page - 1) * $init_perpage;
                                          $query = mysqli_query($conn, "SELECT * FROM tbl_user_master ORDER BY id DESC LIMIT $init_offset, $init_perpage"); 
                                          $countQuery = mysqli_query($conn, "SELECT COUNT(*) as total FROM tbl_user_master");
                                          $countRow = mysqli_fetch_assoc($countQuery);
                                          $totalRecords = $countRow['total'] ?? 0;
                                          $user_list = array();
                                          while ($u = mysqli_fetch_array($query)) {
                                             $user_list[] = $u;
                                          }
                                          $i = $init_offset + 1;
                                          foreach ($user_list as $user) {
                                         ?>
                                       <tr>
                                          <td class="td-center-middle">
                                             <?php echo $i++; ?>
                                          </td>
                                           <td class="td-center-middle-nowrap">
                                              <a class="btn btn-info btn-xs" data-toggle="tooltip" title="Edit User" href="fh_usermastercreation.php?update_id=<?php echo $user['id']; ?>">
                                                 <i class="fa fa-pencil"></i> Edit
                                              </a>
                                              <button type="button" class="btn btn-warning btn-xs" data-toggle="modal" data-target="#viewDetailsModalList<?php echo $user['id']; ?>" title="View Details">
                                                 <i class="fa fa-eye"></i> Details
                                              </button>
                                              <a class="btn btn-danger btn-xs" data-toggle="tooltip" title="Delete User" onClick="return confirm('Are you sure you want to Delete this User?')" href="fh_usermastercreation_list.php?delete_id=<?php echo $user['id']; ?>">
                                                 <i class="fa fa-trash-o"></i> Delete
                                              </a>
                                           </td>
                                           <td class="td-center-middle">
                                              <?php if ($user['status'] == 'true' || strtolower($user['status']) == 'active') { ?>
                                                 <span class="label label-success badge-status-sm">Active</span>
                                              <?php } else { ?>
                                                 <span class="label label-danger badge-status-sm">Inactive</span>
                                              <?php } ?>
                                           </td>
                                           <td class="td-center-middle">
                                              <?php if (!empty($user['picture']) && file_exists('uploads/item-master/' . $user['picture'])) { ?>
                                                 <img src="uploads/item-master/<?php echo htmlspecialchars($user['picture']); ?>" class="img-circle avatar-img-table" alt="User Image" width="40" height="40">
                                              <?php } else { ?>
                                                 <div class="avatar-fallback-table">
                                                    <?php echo strtoupper(substr($user['user_name'] ?? 'U', 0, 1)); ?>
                                                 </div>
                                              <?php } ?>
                                           </td>
                                           <td class="td-middle"><code><?php echo htmlspecialchars($user['user_code'] ?? ''); ?></code></td>
                                           <td class="td-middle-bold"><?php echo htmlspecialchars(substr($user['user_name'] ?? '',0,50)); ?></td>
                                           <td class="td-middle"><?php echo htmlspecialchars(substr($user['mobile_no'] ?? '',0,50)); ?></td>
                                           <td class="td-middle"><?php echo htmlspecialchars(substr($user['store_name'] ?? '',0,50)); ?></td>
                                           <td class="td-middle"><?php echo htmlspecialchars(substr($user['location'] ?? '',0,50)); ?></td>
                                           <td class="td-middle"><span class="label label-info badge-info-sm"><?php echo htmlspecialchars(substr($user['user_type'] ?? '',0,50)); ?></span></td>
                                           <td class="td-middle-muted"><?php echo !empty($user['created_date']) ? date('d-m-Y H:i', strtotime($user['created_date'])) : '-'; ?></td>
                                           <td class="td-middle-muted"><?php echo !empty($user['updated_date']) ? date('d-m-Y H:i', strtotime($user['updated_date'])) : '-'; ?></td>
                                           <td class="td-center-middle"><?php echo ($user['gender'] == '1') ? 'Male' : (($user['gender'] == '0') ? 'Female' : htmlspecialchars($user['gender'] ?? '')); ?></td>
                                        </tr>
                                        <?php } ?>
                                     </tbody>
                                  </table>
                               </div>
                               <div class="row mt-15">
                                  <div class="col-sm-12">
                                     <div id="pagination-result">
                                        <?php echo include_pagination_component($init_page, $init_perpage, $totalRecords); ?>
                                     </div>
                                  </div>
                               </div>
                         </div>
                     </div>
                  </div>
               </div>
               <!-- model Modal1 -->
               <div class="modal fade" id="gst" tabindex="-1" role="dialog" aria-hidden="true">
                  <div class="modal-dialog">
                     <div class="modal-content">
                        <div class="modal-header modal-header-primary">
                           <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                           <h3><i class="fa fa-plus m-r-5"></i> Add New Store Master Creation</h3>
                        </div>
                        <div class="modal-body">
                           <div class="row">
                              <div class="col-md-12">
                                 <form method="post">
                                    <?php
                                       $msg='';
                                        if(isset($_POST['submit_btn']))
                                        {
                                         $store_code=$_POST['store_code'];
                                         $store_name=$_POST['store_name'];
                                         $location_name=$_POST['location_name'];
                                         $company_name=$_POST['company_name'];
                                         $status=$_POST['status'];
                                         
                                             $dup=mysqli_query($conn,"select * from tbl_store_master where store_name='$store_name'");
                                               if(mysqli_num_rows($dup)>0)
                                               {
                                                   $msg='<div class="alert alert-danger alert-dismissible fade1 show">
                                                       <button type="button" class="close" data-dismiss="alert">&times;</button>
                                                       <strong>Store Master Already exist!</strong>
                                                       </div>';
                                               }
                                               else{
                                                   $query="Insert into tbl_store_master(store_code,store_name,location_name,company_name,status)values('$store_code','$store_name','$location_name','$company_name','$status')";
                                                   if(mysqli_query($conn,$query)){
                                                       $msg='<div class="alert alert-success alert-dismissible fade1 show">
                                                       <button type="button" class="close" data-dismiss="alert">&times;</button>
                                                       <strong>Store Master Creations Success..!</strong>
                                                     </div>';
                                                   }
                                                   else{
                                                       $msg='<div class="alert alert-danger alert-dismissible fade1 show">
                                                       <button type="button" class="close" data-dismiss="alert">&times;</button>
                                                       <strong>Store Master Creations Failed!</strong>
                                                       </div>';
                                                   }
                                               } 
                                           }
                                        ?>
                                    <div class="row">
                                       <div class="col-sm-6">
                                          <div class="form">
                                             <label>Store Code</label>
                                             <input type="password" name="store_code" class="form-control" id="store_code" readonly value="<?php @$no = mysqli_query($conn, "SELECT id FROM  tbl_store_master ORDER BY id DESC");
                                                $po_no = mysqli_fetch_assoc($no);
                                                echo 'SC'.($po_no['id']+1);
                                                ?>">
                                          </div>
                                       </div>
                                       <div class="col-sm-6">
                                          <div class="form-group">
                                             <label>Store Name</label>
                                             <input type="text" class="form-control" name="store_name" id="store_name" placeholder="Enter Your Store Name" required>
                                          </div>
                                       </div>
                                    </div>
                                    <div class="row">
                                       <div class="col-sm-6">
                                          <div class="form-group">
                                             <label>Company Name</label>
                                             <input type="text" class="form-control" name="company_name" id="company_name" placeholder="Enter Your Company Name" required>
                                          </div>
                                       </div>
                                       <div class="col-sm-6">
                                          <div class="form-group">
                                             <label>Store Location</label>
                                             <input type="text" class="form-control" name="location_name" id="location_name" placeholder="Enter Your Location Name" required>
                                          </div>
                                       </div>
                                    </div>
                                    <br>
                                    <div class="row">
                                       <div class="col-sm-4">
                                          <div class="form-check">
                                             <label>Status</label><br>
                                             <label class="radio-inline">
                                             <input type="radio" name="status" value="true" checked="checked">Active</label>
                                             <label class="radio-inline"><input type="radio" name="status" value="false" >Inctive</label>
                                          </div>
                                       </div>
                                    </div>
                                    <br>
                                    <div class="container-fluid">
                                       <div class="row">
                                          <button type="reset" name="submit" class="btn btn-warning btn-w-100">Reset</button>
                                          <button type="submit" name="submit_btn" class="btn btn-success btn-teal-100">Save</button>
                                       </div>
                                    </div>
                                    <br>
                                 </form>
                              </div>
                           </div>
                        </div>
                        <div class="modal-footer">
                           <button type="button" class="btn btn-danger pull-right" data-dismiss="modal">Close</button>
                        </div>
                     </div>
                     <!-- /.modal-content -->
                  </div>
                  <!-- /.modal-dialog -->
               </div>
               <!-- model Modal1-end -->
            </section>
            <!-- /.content -->
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
      <script src="assets/dist/js/page/usermaster-list.js"></script>
      <!-- End Theme label Script
         =====================================================================-->
   </body>
   <!-- Mirrored from thememinister.com/crm/add-customer.html by HTTrack Website Copier/3.x [XR&CO'2014], Fri, 05 Aug 2022 06:10:42 GMT -->
</html>