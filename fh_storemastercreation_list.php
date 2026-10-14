<?php include('top.php');?>
<?php

if(isset($_GET['delete_id']))
{
   $id=$_GET['delete_id'];
   $sqld="DELETE FROM tbl_store_master WHERE id='$id'";
   $res=mysqli_query($conn,$sqld);
   if($res)
   {
    header('refresh:.5; url=fh_storemastercreation_list.php');
   }else{
    echo "<script>alert('Store Master Record Failed');</script>";
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
      <title>Store Master Creation || TEJASERP</title>
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
                               <span>Store Master List !</span>
                            </span>
                         </div>
                        <div class="panel-body form-scroll">
                           <!-- Toolbar & Search Box Row under Header Panel -->
                           <div class="row m-b-15">
                              <div class="col-sm-6 col-xs-12 m-b-5">
                                 <div class="btn-group">
                                    <a href="fh_storemastercreation.php"> <button type="button" class="button-btn-btn btn-exp btn-sm"><i class="fa fa-plus mr-5"></i>Add New Store Master Creation</button></a>
                                 </div>
                                 <?php include('include/button-export-to-data.php');?>
                              </div>
                              <div class="col-sm-6 col-xs-12">
                                 <!-- Search Form Box under Header Panel -->
                                 <form id="storeMasterSearchForm" class="search-form-wrap pull-right" onsubmit="return false;" style="width: 100%; max-width: 360px;">
                                    <input type="hidden" name="page" id="page" value="1">
                                    <div class="input-group search-box-group">
                                       <input type="text" class="form-control search-input search-input-field" name="search" id="company_name" placeholder="Search Stores..." autocomplete="off">
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
                                       <th class="text-center-align">Image</th>
                                       <th>Store Code</th>
                                       <th>Store Name</th>
                                       <th>Company Name</th>
                                       <th>Location Name</th>
                                    </tr>
                                 </thead>
                                 <tbody id="storemaster-list-results">
                                    <?php
                                       $init_page = 1;
                                       $init_perpage = isset($_SESSION["_RECORDPERPAGE_"]) && $_SESSION["_RECORDPERPAGE_"] ? (int)$_SESSION["_RECORDPERPAGE_"] : 20;
                                       $init_offset = ($init_page - 1) * $init_perpage;
                                       $query = mysqli_query($conn, "SELECT * FROM tbl_store_master ORDER BY id DESC LIMIT $init_offset, $init_perpage"); 
                                       $countQuery = mysqli_query($conn, "SELECT COUNT(*) as total FROM tbl_store_master");
                                       $countRow = mysqli_fetch_assoc($countQuery);
                                       $totalRecords = $countRow['total'] ?? 0;
                                       $store_list = array();
                                       while ($s = mysqli_fetch_array($query)) {
                                          $store_list[] = $s;
                                       }
                                       $i = $init_offset + 1;
                                       foreach ($store_list as $user) {
                                      ?>
                                    <tr>
                                       <td class="td-center-middle"><?php echo $i++; ?></td>
                                       <td class="td-center-middle-nowrap">
                                          <a class="btn btn-info btn-xs" data-toggle="tooltip" title="Edit Store" href="fh_storemastercreation.php?update_id=<?php echo $user['id']; ?>">
                                             <i class="fa fa-pencil"></i> Edit
                                          </a>
                                          <button type="button" class="btn btn-warning btn-xs" data-toggle="modal" data-target="#viewStoreModal<?php echo $user['id']; ?>" title="View Details">
                                             <i class="fa fa-eye"></i> Details
                                          </button>
                                          <a class="btn btn-danger btn-xs" data-toggle="tooltip" title="Delete Store" onClick="return confirm('Are you sure you want to Delete this Store?')" href="fh_storemastercreation_list.php?delete_id=<?php echo $user['id']; ?>">
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
                                          <?php if (!empty($user['picture']) && file_exists("uploads/store-master/" . $user['picture'])) { ?>
                                             <img src="uploads/store-master/<?php echo htmlspecialchars($user['picture']); ?>" alt="Store" style="width: 36px; height: 36px; object-fit: cover; border-radius: 50%; border: 1px solid #009688;">
                                          <?php } else { ?>
                                             <span style="display:inline-block; width:36px; height:36px; line-height:36px; border-radius:50%; background:#e0f2f1; color:#009688; text-align:center; font-size:14px;">
                                                <i class="fa fa-shopping-bag"></i>
                                             </span>
                                          <?php } ?>
                                       </td>
                                       <td class="td-middle"><code><?php echo htmlspecialchars($user['store_code'] ?? ''); ?></code></td>
                                       <td class="td-middle-bold"><?php echo htmlspecialchars(substr($user['store_name'] ?? '', 0, 50)); ?></td>
                                       <td class="td-middle"><?php echo htmlspecialchars(substr($user['company_name'] ?? '', 0, 50)); ?></td>
                                       <td class="td-middle"><?php echo htmlspecialchars(substr($user['location_name'] ?? '', 0, 50)); ?></td>
                                    </tr>
                                    <?php } ?>
                                 </tbody>
                              </table>
                           </div>

                           <!-- Modals Container for Store Details -->
                           <div id="store-modals-container">
                              <?php foreach ($store_list as $user) { ?>
                              <div class="modal fade" id="viewStoreModal<?php echo $user['id']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                 <div class="modal-dialog modal-md">
                                    <div class="modal-content modal-content-rounded">
                                       <div class="modal-header modal-header-teal">
                                          <button type="button" class="close modal-close-white" data-dismiss="modal" aria-hidden="true">×</button>
                                          <h4 class="modal-title modal-title-bold">
                                             <i class="fa fa-shopping-bag mr-8"></i>Store Details - <?php echo htmlspecialchars($user['store_name'] ?? ''); ?>
                                          </h4>
                                       </div>
                                       <div class="modal-body modal-body-padded">
                                          <?php if (!empty($user['picture']) && file_exists("uploads/store-master/" . $user['picture'])) { ?>
                                             <div class="text-center mb-15">
                                                <img src="uploads/store-master/<?php echo htmlspecialchars($user['picture']); ?>" alt="Store Image" style="max-height: 120px; border-radius: 8px; border: 1px solid #ddd; padding: 4px;">
                                             </div>
                                          <?php } ?>
                                          <table class="table table-bordered table-striped mb-0">
                                             <tbody>
                                                <tr>
                                                   <th class="th-w-35">Store Code</th>
                                                   <td><code><?php echo htmlspecialchars($user['store_code'] ?? ''); ?></code></td>
                                                </tr>
                                                <tr>
                                                   <th>Store Name</th>
                                                   <td><?php echo htmlspecialchars($user['store_name'] ?? ''); ?></td>
                                                </tr>
                                                <tr>
                                                   <th>Company Name</th>
                                                   <td><?php echo htmlspecialchars($user['company_name'] ?? ''); ?></td>
                                                </tr>
                                                <tr>
                                                   <th>Location Name</th>
                                                   <td><?php echo htmlspecialchars($user['location_name'] ?? ''); ?></td>
                                                </tr>
                                                <tr>
                                                   <th>Status</th>
                                                   <td>
                                                      <?php if ($user['status'] == 'true' || strtolower($user['status']) == 'active') { ?>
                                                         <span class="label label-success badge-status-sm">Active</span>
                                                      <?php } else { ?>
                                                         <span class="label label-danger badge-status-sm">Inactive</span>
                                                      <?php } ?>
                                                   </td>
                                                </tr>
                                             </tbody>
                                          </table>
                                       </div>
                                       <div class="modal-footer modal-footer-light">
                                          <a href="fh_storemastercreation.php?update_id=<?php echo $user['id']; ?>" class="btn btn-info btn-sm">
                                             <i class="fa fa-pencil"></i> Edit Store
                                          </a>
                                          <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                              <?php } ?>
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
            </section>
             <!-- Main content -->
           
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
       <script src="assets/dist/js/page/storemaster-list.js"></script>
      <!-- End Theme label Script
         =====================================================================-->
   </body>

<!-- Mirrored from thememinister.com/crm/add-customer.html by HTTrack Website Copier/3.x [XR&CO'2014], Fri, 05 Aug 2022 06:10:42 GMT -->
</html>

