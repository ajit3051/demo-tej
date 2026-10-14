<?php include('top.php');?>
<?php

if(isset($_GET['delete_id']))
{
   $id=$_GET['delete_id'];
   $sqld="DELETE FROM tbl_storeprofile_master WHERE id='$id'";
   $res=mysqli_query($conn,$sqld);
   if($res)
   {
    header('refresh:.5; url=fh_storeprofilecreation_list.php');
   }else{
    echo "<script>alert('Store Profile Record Deletion Failed');</script>";
   }
}
?>

<!DOCTYPE html>
<html lang="en">
   
   <head>
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <title>Store Profile Creation || TEJASERP</title>
      <!-- Favicon and touch icons -->
      <?php include('include/css.php');?>  
   </head>
   <body class="hold-transition sidebar-mini">
      <div class="wrapper">
         <?php include('include/header-top-bar.php');?>

         <!-- Left side column. contains the sidebar -->
         <aside class="main-sidebar">
             <?php include('include/sidebar-left.php');?>
             <?php include('include/toggle_switch_list.php');?> 
         </aside>
         
         <!-- Content Wrapper. Contains page content -->
         <div class="content-wrapper">
           <div class="container-fluid">
               <div class="row">
               <?php include('include/menu-header.php');?>
               </div>
            </div> 
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
                               <span>Store Profile List !</span>
                            </span>
                         </div>
                         <div class="panel-body form-scroll">
                            <!-- Toolbar & Search Box Row under Header Panel -->
                            <div class="row m-b-15">
                               <div class="col-sm-6 col-xs-12 m-b-5">
                                  <div class="btn-group">
                                     <a href="fh_storeprofilecreation.php" class="btn btn-exp btn-sm">
                                        <i class="fa fa-plus mr-5"></i>Add New Store Profile Creation
                                     </a>
                                  </div>
                                  <?php include('include/button-export-to-data.php');?>
                               </div>
                               <div class="col-sm-6 col-xs-12">
                                  <!-- Search Form Box under Header Panel -->
                                  <form id="storeProfileCreationSearchForm" class="search-form-wrap pull-right" onsubmit="return false;" style="width: 100%; max-width: 360px;">
                                     <input type="hidden" name="page" id="page" value="1">
                                     <div class="input-group search-box-group">
                                        <input type="text" class="form-control search-input search-input-field" name="search" id="company_name" placeholder="Search Store Profile..." autocomplete="off">
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
                                       <th>Profile Code</th>
                                       <th>Store Code</th>
                                       <th>Store Name</th>
                                       <th>Company Name</th>
                                       <th>Mobile No</th>
                                       <th>GST No</th>
                                       <th>Email ID</th>
                                       <th>Invoice Type</th>
                                    </tr>
                                 </thead>
                                 <tbody id="storeprofilecreation-list-results">
                                    <?php
                                       $init_page = 1;
                                       $init_perpage = isset($_SESSION["_RECORDPERPAGE_"]) && $_SESSION["_RECORDPERPAGE_"] ? (int)$_SESSION["_RECORDPERPAGE_"] : 20;
                                       $init_offset = ($init_page - 1) * $init_perpage;
                                       $query = mysqli_query($conn, "SELECT * FROM tbl_storeprofile_master ORDER BY id DESC LIMIT $init_offset, $init_perpage"); 
                                       $countQuery = mysqli_query($conn, "SELECT COUNT(*) as total FROM tbl_storeprofile_master");
                                       $countRow = mysqli_fetch_assoc($countQuery);
                                       $totalRecords = $countRow['total'] ?? 0;
                                       $profile_list = array();
                                       while ($sp = mysqli_fetch_array($query)) {
                                          $profile_list[] = $sp;
                                       }
                                       $i = $init_offset + 1;
                                       foreach ($profile_list as $user) {
                                          $statusBadge = ($user['status'] ?? 'Active') === 'Active' 
                                              ? '<span class="label label-success badge-status-sm">Active</span>' 
                                              : '<span class="label label-danger badge-status-sm">Inactive</span>';
                                      ?>
                                    <tr>
                                       <td class="td-center-middle"><?php echo $i++; ?></td>
                                       <td class="td-center-middle-nowrap">
                                          <a class="btn btn-info btn-xs" data-toggle="tooltip" title="Edit Store Profile" href="fh_storeprofilecreation.php?update_id=<?php echo $user['id']; ?>">
                                             <i class="fa fa-pencil"></i> Edit
                                          </a>
                                          <button type="button" class="btn btn-warning btn-xs" data-toggle="modal" data-target="#viewStoreProfileModal<?php echo $user['id']; ?>" title="View Details">
                                             <i class="fa fa-eye"></i> Details
                                          </button>
                                          <a class="btn btn-danger btn-xs" data-toggle="tooltip" title="Delete Store Profile" href="fh_storeprofilecreation_list.php?delete_id=<?php echo $user['id']; ?>" onclick="return confirm('Are you sure you want to delete this record?');">
                                             <i class="fa fa-trash-o"></i> Delete
                                          </a>
                                       </td>
                                       <td class="td-center-middle"><?php echo $statusBadge; ?></td>
                                       <td><?php echo htmlspecialchars($user['code'] ?? ''); ?></td>
                                       <td><?php echo htmlspecialchars($user['store_code'] ?? ''); ?></td>
                                       <td><?php echo htmlspecialchars($user['store_name'] ?? ''); ?></td>
                                       <td><?php echo htmlspecialchars($user['company_name'] ?? ''); ?></td>
                                       <td><?php echo htmlspecialchars($user['mobile_no1'] ?? ''); ?></td>
                                       <td><?php echo htmlspecialchars($user['gst_no'] ?? ''); ?></td>
                                       <td><?php echo htmlspecialchars($user['email_id'] ?? ''); ?></td>
                                       <td><?php echo htmlspecialchars($user['invoice_type'] ?? ''); ?></td>
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
                                 <div id="storeprofilecreation-pagination">
                                    <?php
                                       $totalPages = ceil($totalRecords / $init_perpage);
                                       if ($totalPages > 1) {
                                          echo '<ul class="pagination pagination-sm pagination-no-margin pull-right">';
                                          echo '<li class="disabled"><span>&laquo; Prev</span></li>';
                                          for ($p = 1; $p <= min(5, $totalPages); $p++) {
                                             if ($p == 1) {
                                                echo '<li class="active"><span>' . $p . '</span></li>';
                                             } else {
                                                echo '<li><a href="javascript:void(0)" onclick="loadStoreProfileCreationData(' . $p . ')">' . $p . '</a></li>';
                                             }
                                          }
                                          if ($totalPages > 5) {
                                             echo '<li class="disabled"><span>...</span></li>';
                                             echo '<li><a href="javascript:void(0)" onclick="loadStoreProfileCreationData(' . $totalPages . ')">' . $totalPages . '</a></li>';
                                          }
                                          echo '<li><a href="javascript:void(0)" onclick="loadStoreProfileCreationData(2)">Next &raquo;</a></li>';
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
               <div id="storeprofile-modals-container">
                  <?php foreach ($profile_list as $user) { ?>
                  <div class="modal fade" id="viewStoreProfileModal<?php echo $user['id']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
                     <div class="modal-dialog modal-lg">
                        <div class="modal-content modal-content-rounded">
                           <div class="modal-header modal-header-teal">
                              <button type="button" class="close modal-close-white" data-dismiss="modal" aria-hidden="true">×</button>
                              <h4 class="modal-title modal-title-bold">
                                 <i class="fa fa-folder-open mr-8"></i>Store Profile Details - <?php echo htmlspecialchars($user['company_name'] ?? $user['store_name'] ?? ''); ?>
                              </h4>
                           </div>
                           <div class="modal-body modal-body-padded">
                              <div class="row">
                                 <div class="col-md-6">
                                    <table class="table table-bordered table-striped mb-0">
                                       <tbody>
                                          <tr>
                                             <th class="th-w-35">Profile Code</th>
                                             <td><code><?php echo htmlspecialchars($user['code'] ?? ''); ?></code></td>
                                          </tr>
                                          <tr>
                                             <th>Store Code</th>
                                             <td><?php echo htmlspecialchars($user['store_code'] ?? ''); ?></td>
                                          </tr>
                                          <tr>
                                             <th>Store Name</th>
                                             <td><strong><?php echo htmlspecialchars($user['store_name'] ?? ''); ?></strong></td>
                                          </tr>
                                          <tr>
                                             <th>Company Name</th>
                                             <td><?php echo htmlspecialchars($user['company_name'] ?? ''); ?></td>
                                          </tr>
                                          <tr>
                                             <th>Mobile No 1</th>
                                             <td><?php echo htmlspecialchars($user['mobile_no1'] ?? ''); ?></td>
                                          </tr>
                                          <tr>
                                             <th>Mobile No 2</th>
                                             <td><?php echo htmlspecialchars($user['mobile_no2'] ?? ''); ?></td>
                                          </tr>
                                          <tr>
                                             <th>Email ID</th>
                                             <td><?php echo htmlspecialchars($user['email_id'] ?? ''); ?></td>
                                          </tr>
                                          <tr>
                                             <th>Website</th>
                                             <td><?php echo htmlspecialchars($user['website'] ?? ''); ?></td>
                                          </tr>
                                       </tbody>
                                    </table>
                                 </div>
                                 <div class="col-md-6">
                                    <table class="table table-bordered table-striped mb-0">
                                       <tbody>
                                          <tr>
                                             <th class="th-w-35">GST No</th>
                                             <td><?php echo htmlspecialchars($user['gst_no'] ?? ''); ?></td>
                                          </tr>
                                          <tr>
                                             <th>FSSAI License No</th>
                                             <td><?php echo htmlspecialchars($user['fssai_lic_no'] ?? ''); ?></td>
                                          </tr>
                                          <tr>
                                             <th>Invoice Type</th>
                                             <td><span class="label label-info"><?php echo htmlspecialchars($user['invoice_type'] ?? ''); ?></span></td>
                                          </tr>
                                          <tr>
                                             <th>Status</th>
                                             <td><?php echo (($user['status'] ?? 'Active') === 'Active') ? '<span class="label label-success">Active</span>' : '<span class="label label-danger">Inactive</span>'; ?></td>
                                          </tr>
                                          <tr>
                                             <th>Address 1</th>
                                             <td><?php echo htmlspecialchars($user['address1'] ?? ''); ?></td>
                                          </tr>
                                          <tr>
                                             <th>Address 2</th>
                                             <td><?php echo htmlspecialchars($user['address2'] ?? ''); ?></td>
                                          </tr>
                                          <tr>
                                             <th>City / State</th>
                                             <td><?php echo htmlspecialchars(($user['city_name'] ?? '') . ' / ' . ($user['state_name'] ?? '')); ?></td>
                                          </tr>
                                          <tr>
                                             <th>Pincode</th>
                                             <td><?php echo htmlspecialchars($user['pincode'] ?? ''); ?></td>
                                          </tr>
                                       </tbody>
                                    </table>
                                 </div>
                              </div>
                              <?php if (!empty($user['terms_and_condition'])) { ?>
                              <div class="row mt-15">
                                 <div class="col-xs-12">
                                    <div class="panel panel-default mb-0">
                                       <div class="panel-heading"><strong>Terms &amp; Conditions</strong></div>
                                       <div class="panel-body"><?php echo nl2br(htmlspecialchars($user['terms_and_condition'])); ?></div>
                                    </div>
                                 </div>
                              </div>
                              <?php } ?>
                           </div>
                           <div class="modal-footer">
                              <button type="button" class="btn btn-danger pull-right" data-dismiss="modal">Close</button>
                           </div>
                        </div>
                     </div>
                  </div>
                  <?php } ?>
               </div>
            </section>
         </div>
         <!-- /.content-wrapper -->
         <?php include('include/footer.php');?>
      </div>
      <!-- ./wrapper -->
      <?php include('include/js.php');?>
      <script src="assets/dist/js/page/storeprofilecreation-list.js"></script>
   </body>
</html>