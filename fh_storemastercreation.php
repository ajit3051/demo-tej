<?php include('top.php');?>
<?php
   $msg = '';
   $is_edit = false;
   $edit_id = '';
   $edit_row = array(
      'store_code' => '',
      'store_name' => '',
      'company_name' => '',
      'location_name' => '',
      'status' => 'true',
      'picture' => ''
   );

   if (isset($_GET['update_id']) && !empty($_GET['update_id'])) {
      $is_edit = true;
      $edit_id = $_GET['update_id'];
      $run = mysqli_query($conn, "SELECT * FROM tbl_store_master WHERE id='$edit_id'");
      if ($run && mysqli_num_rows($run) > 0) {
         $edit_row = mysqli_fetch_assoc($run);
      }
   }

   $path = "uploads/store-master/";
   if (!is_dir($path)) {
      mkdir($path, 0777, true);
   }

   if (!function_exists('uploadStoreFile')) {
      function uploadStoreFile($file, $path) {
         if ($file && !empty($file['name']) && $file['error'] === 0) {
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg','jpeg','png','gif','webp'];
            if (!in_array($ext, $allowed)) {
               return "";
            }
            $filename = time() . "_" . rand(1000, 9999) . "." . $ext;
            if (move_uploaded_file($file['tmp_name'], $path . $filename)) {
               return $filename;
            }
         }
         return "";
      }
   }

   // Update Store Record
   if (isset($_POST['update']) && !empty($edit_id)) {
      $store_code = $_POST['store_code'];
      $store_name = $_POST['store_name'];
      $location_name = $_POST['location_name'];
      $company_name = $_POST['company_name'];
      $status = $_POST['status'];

      $new_picture = uploadStoreFile($_FILES['picture'] ?? null, $path);
      $picture = !empty($new_picture) ? $new_picture : ($edit_row['picture'] ?? '');

      $store_name = str_replace("'", "\'", $store_name);
      $store_name = str_replace('"', '\"', $store_name);

      $query = "UPDATE tbl_store_master SET store_code='$store_code', store_name='$store_name', location_name='$location_name', company_name='$company_name', status='$status', picture='$picture' WHERE id='$edit_id'";
      $run = mysqli_query($conn, $query);
      if ($run) {
         echo "
         <script>
         document.addEventListener('DOMContentLoaded', function() {
             showPopup(
                 'Success!',
                 'Store Details Updated Successfully 🎉'
             );
             setTimeout(function(){ window.location.href = 'fh_storemastercreation.php'; }, 1500);
         });
         </script>
         ";
      } else {
         $msg = '<div class="alert alert-danger alert-dismissible fade1 show">
             <button type="button" class="close" data-dismiss="alert">&times;</button>
             <strong>Store Update Failed!</strong>
         </div>';
      }
   }

   // Create Store Record
   if (isset($_POST['submit'])) {
      $store_code = $_POST['store_code'];
      $store_name = $_POST['store_name'];
      $location_name = $_POST['location_name'];
      $company_name = $_POST['company_name'];
      $status = $_POST['status'];

      $new_picture = uploadStoreFile($_FILES['picture'] ?? null, $path);

      $dup = mysqli_query($conn, "SELECT * FROM tbl_store_master WHERE store_name='$store_name'");
      if (mysqli_num_rows($dup) > 0) {
          $msg = '<div class="alert alert-danger alert-dismissible fade1 show">
              <button type="button" class="close" data-dismiss="alert">&times;</button>
              <strong>Store Name Already exist!</strong>
          </div>';
      } else {
          $query = "INSERT INTO tbl_store_master(store_code,store_name,location_name,company_name,status,picture) VALUES('$store_code','$store_name','$location_name','$company_name','$status','$new_picture')";
          $run = mysqli_query($conn, $query);
          if ($run) {
              echo "
              <script>
              document.addEventListener('DOMContentLoaded', function() {
                  showPopup(
                      'Success!',
                      'Store Saved Successfully 🎉'
                  );
                  setTimeout(function(){ window.location.href = 'fh_storemastercreation.php'; }, 1500);
              });
              </script>
              ";
          } else {
              $msg = '<div class="alert alert-danger alert-dismissible fade1 show">
                  <button type="button" class="close" data-dismiss="alert">&times;</button>
                  <strong>Store Creation Failed!</strong>
              </div>';
          }
      } 
   }
?>
<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <title>Store Master <?php echo $is_edit ? 'Edit' : 'Creation'; ?> || TEJASERP</title>
      <!-- Favicon and touch icons -->
      <?php include('include/css.php');?>  
   </head>
   <body class="hold-transition sidebar-mini">
      <!-- Site wrapper -->
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
            
            <!-- Main content - Creation/Update Form -->
            <section class="">
               <div class="row">
                  <div class="col-sm-12">
                     <div class="panel panel-bd lobidisable">
                        <div class="panel-heading">
                           <span class="fs-15 panel-header-text">
                              <i class="fa fa-pencil mr-5"></i>
                              <span><?php echo $is_edit ? 'Store Master Edit !' : 'Store Master Creations !'; ?></span>
                           </span>
                        </div>

                        <div class="panel-body form-scroll">
                           <div class="btn-group">
                              <a href="fh_storemastercreation_list.php">
                                 <button type="button" class="button-btn-btn btn-exp btn-sm"><i class="fa fa-bars mr-5"></i>Store Master List</button>
                              </a>
                              <?php if ($is_edit) { ?>
                              <a href="fh_storemastercreation.php" class="ml-5">
                                 <button type="button" class="btn btn-primary btn-sm"><i class="fa fa-plus mr-5"></i>Add New Store</button>
                              </a>
                              <?php } ?>
                           </div>

                           <form method="post" enctype="multipart/form-data">
                              <div class="form-group">
                                 <?php echo $msg; ?>
                              </div>
                              <div class="row">
                                 <div class="col-sm-4">
                                    <div class="form-group">
                                       <label>Store Code</label>
                                       <input type="text" name="store_code" class="form-control" id="store_code" readonly value="<?php 
                                          if ($is_edit) {
                                             echo htmlspecialchars($edit_row['store_code'] ?? '');
                                          } else {
                                             @$no = mysqli_query($conn, "SELECT id FROM tbl_store_master ORDER BY id DESC");
                                             $po_no = mysqli_fetch_assoc($no);
                                             echo 'SC'.(($po_no['id'] ?? 0)+1);
                                          }
                                       ?>">
                                    </div>
                                 </div>
                                 <div class="col-sm-4">
                                    <div class="form-group">
                                       <label>Store Name</label><label class="text-required">*</label>
                                       <input type="text" class="form-control" name="store_name" id="store_name" value="<?php echo htmlspecialchars($edit_row['store_name'] ?? ''); ?>" placeholder="Enter Your Store Name" required>
                                    </div>
                                 </div>
                                 <div class="col-sm-4">
                                    <div class="form-group">
                                       <label>Company Name</label><label class="text-required">*</label>
                                       <input type="text" class="form-control" name="company_name" id="company_name" value="<?php echo htmlspecialchars($edit_row['company_name'] ?? ''); ?>" placeholder="Enter Your Company Name" required>
                                    </div>
                                 </div>
                              </div>
                              <div class="row">
                                 <div class="col-sm-4">
                                    <div class="form-group">
                                       <label>Location Name</label><label class="text-required">*</label>
                                       <input type="text" class="form-control" name="location_name" id="location_name" value="<?php echo htmlspecialchars($edit_row['location_name'] ?? ''); ?>" placeholder="Enter Your Location Name" required>
                                    </div>
                                 </div>
                                 <div class="col-sm-4">
                                    <div class="form-group">
                                       <label>Store Image / Logo</label>
                                       <input type="file" name="picture" id="picture" class="form-control" accept="image/*">
                                       <?php if ($is_edit && !empty($edit_row['picture']) && file_exists("uploads/store-master/" . $edit_row['picture'])) { ?>
                                          <div class="mt-5">
                                             <img src="uploads/store-master/<?php echo htmlspecialchars($edit_row['picture']); ?>" alt="Store Image" style="width: 48px; height: 48px; object-fit: cover; border-radius: 4px; border: 1px solid #ccc; vertical-align: middle;">
                                             <span class="text-muted fs-12 ml-5"><?php echo htmlspecialchars($edit_row['picture']); ?></span>
                                          </div>
                                       <?php } ?>
                                    </div>
                                 </div>
                                 <div class="col-sm-4">
                                    <div class="form-check">
                                       <label>Status</label><br>
                                       <label class="radio-inline">
                                          <input type="radio" name="status" value="true" <?php if (!$is_edit || $edit_row['status'] == 'true' || strtolower($edit_row['status']) == 'active') echo 'checked="checked"'; ?>>Active
                                       </label>
                                       <label class="radio-inline">
                                          <input type="radio" name="status" value="false" <?php if ($is_edit && ($edit_row['status'] == 'false' || strtolower($edit_row['status']) == 'inactive')) echo 'checked="checked"'; ?>>Inactive
                                       </label>
                                    </div>
                                 </div>
                              </div>
                              <br>
                              <div class="container-fluid">
                                 <div class="row">
                                    <?php if ($is_edit) { ?>
                                       <a href="fh_storemastercreation.php" class="btn btn-warning btn-w-100">Cancel</a>
                                       <button type="submit" name="update" class="btn btn-success btn-teal-100">Update</button>
                                    <?php } else { ?>
                                       <button type="reset" class="btn btn-warning btn-w-100">Reset</button>
                                       <button type="submit" name="submit" class="btn btn-success btn-teal-100">Save</button>
                                    <?php } ?>
                                 </div>
                              </div>
                              <br>
                           </form>
                        </div>
                     </div>
                  </div>
               </div>
            </section>
            
            <!-- Main content - Store Master List -->
             <section class="">
                <div class="row">
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
                                    <a href="fh_storemastercreation.php" class="btn btn-exp btn-sm"><i class="fa fa-plus mr-5"></i>Add New Store Master Creation</a>
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
                                       foreach ($store_list as $row) {
                                      ?>
                                    <tr>
                                       <td class="td-center-middle"><?php echo $i++; ?></td>
                                       <td class="td-center-middle-nowrap">
                                          <a class="btn btn-info btn-xs" data-toggle="tooltip" title="Edit Store" href="fh_storemastercreation.php?update_id=<?php echo $row['id']; ?>">
                                             <i class="fa fa-pencil"></i> Edit
                                          </a>
                                          <button type="button" class="btn btn-warning btn-xs" data-toggle="modal" data-target="#viewStoreModal<?php echo $row['id']; ?>" title="View Details">
                                             <i class="fa fa-eye"></i> Details
                                          </button>
                                          <a class="btn btn-danger btn-xs" data-toggle="tooltip" title="Delete Store" onClick="return confirm('Are you sure you want to Delete this Store?')" href="fh_storemastercreation_list.php?delete_id=<?php echo $row['id']; ?>">
                                             <i class="fa fa-trash-o"></i> Delete
                                          </a>
                                       </td>
                                       <td class="td-center-middle">
                                          <?php if ($row['status'] == 'true' || strtolower($row['status']) == 'active') { ?>
                                             <span class="label label-success badge-status-sm">Active</span>
                                          <?php } else { ?>
                                             <span class="label label-danger badge-status-sm">Inactive</span>
                                          <?php } ?>
                                       </td>
                                       <td class="td-center-middle">
                                          <?php if (!empty($row['picture']) && file_exists("uploads/store-master/" . $row['picture'])) { ?>
                                             <img src="uploads/store-master/<?php echo htmlspecialchars($row['picture']); ?>" alt="Store" style="width: 36px; height: 36px; object-fit: cover; border-radius: 50%; border: 1px solid #009688;">
                                          <?php } else { ?>
                                             <span style="display:inline-block; width:36px; height:36px; line-height:36px; border-radius:50%; background:#e0f2f1; color:#009688; text-align:center; font-size:14px;">
                                                <i class="fa fa-shopping-bag"></i>
                                             </span>
                                          <?php } ?>
                                       </td>
                                       <td class="td-middle"><code><?php echo htmlspecialchars($row['store_code'] ?? ''); ?></code></td>
                                       <td class="td-middle-bold"><?php echo htmlspecialchars(substr($row['store_name'] ?? '', 0, 50)); ?></td>
                                       <td class="td-middle"><?php echo htmlspecialchars(substr($row['company_name'] ?? '', 0, 50)); ?></td>
                                       <td class="td-middle"><?php echo htmlspecialchars(substr($row['location_name'] ?? '', 0, 50)); ?></td>
                                    </tr>
                                    <?php } ?>
                                 </tbody>
                              </table>
                           </div>

                           <!-- Modals Container for Store Details -->
                           <div id="store-modals-container">
                              <?php foreach ($store_list as $row) { ?>
                              <div class="modal fade" id="viewStoreModal<?php echo $row['id']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                 <div class="modal-dialog modal-md">
                                    <div class="modal-content modal-content-rounded">
                                       <div class="modal-header modal-header-teal">
                                          <button type="button" class="close modal-close-white" data-dismiss="modal" aria-hidden="true">×</button>
                                          <h4 class="modal-title modal-title-bold">
                                             <i class="fa fa-shopping-bag mr-8"></i>Store Details - <?php echo htmlspecialchars($row['store_name'] ?? ''); ?>
                                          </h4>
                                       </div>
                                       <div class="modal-body modal-body-padded">
                                          <?php if (!empty($row['picture']) && file_exists("uploads/store-master/" . $row['picture'])) { ?>
                                             <div class="text-center mb-15">
                                                <img src="uploads/store-master/<?php echo htmlspecialchars($row['picture']); ?>" alt="Store Image" style="max-height: 120px; border-radius: 8px; border: 1px solid #ddd; padding: 4px;">
                                             </div>
                                          <?php } ?>
                                          <table class="table table-bordered table-striped mb-0">
                                             <tbody>
                                                <tr>
                                                   <th class="th-w-35">Store Code</th>
                                                   <td><code><?php echo htmlspecialchars($row['store_code'] ?? ''); ?></code></td>
                                                </tr>
                                                <tr>
                                                   <th>Store Name</th>
                                                   <td><strong><?php echo htmlspecialchars($row['store_name'] ?? ''); ?></strong></td>
                                                </tr>
                                                <tr>
                                                   <th>Company Name</th>
                                                   <td><?php echo htmlspecialchars($row['company_name'] ?? ''); ?></td>
                                                </tr>
                                                <tr>
                                                   <th>Location Name</th>
                                                   <td><?php echo htmlspecialchars($row['location_name'] ?? ''); ?></td>
                                                </tr>
                                                <tr>
                                                   <th>Status</th>
                                                   <td>
                                                      <?php if ($row['status'] == 'true' || strtolower($row['status']) == 'active') { ?>
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
                                          <a href="fh_storemastercreation.php?update_id=<?php echo $row['id']; ?>" class="btn btn-info btn-sm">
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
         </div>
         <!-- /.content-wrapper -->
         <?php include('include/footer.php');?> 
         <?php include('include/Sidenavbuttons.php');?>
      </div>
      <!-- ./wrapper -->
      <?php include('include/js.php');?>
      <script src="assets/dist/js/page/storemaster-list.js"></script>
   </body>
</html>