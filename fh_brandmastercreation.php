<?php include('top.php');?>
<?php
   $msg = '';
   $is_edit = false;
   $edit_id = '';
   $edit_row = array(
      'brand_code' => '',
      'brand_name' => '',
      'status' => 'true',
      'picture' => ''
   );

   $targetDir = "uploads/brand-master/";
   if (!file_exists($targetDir)) {
      mkdir($targetDir, 0777, true);
   }

   function uploadBrandFile($file, $targetDir) {
      if (isset($file['name']) && !empty($file['name']) && $file['error'] == 0) {
         $fileName = time() . '_' . rand(1000, 9999) . '_' . preg_replace("/[^a-zA-Z0-9\._-]/", "", basename($file['name']));
         $targetFilePath = $targetDir . $fileName;
         $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
         $allowTypes = array('jpg', 'png', 'jpeg', 'gif', 'webp');
         if (in_array($fileType, $allowTypes)) {
            if (move_uploaded_file($file['tmp_name'], $targetFilePath)) {
               return $fileName;
            }
         }
      }
      return '';
   }

   if (isset($_GET['update_id']) && !empty($_GET['update_id'])) {
      $is_edit = true;
      $edit_id = $_GET['update_id'];
      $run = mysqli_query($conn, "SELECT * FROM tbl_brand_master WHERE id='$edit_id'");
      if ($run && mysqli_num_rows($run) > 0) {
         $edit_row = mysqli_fetch_assoc($run);
      }
   }

   // Update Record
   if (isset($_POST['update']) && !empty($edit_id)) {
      $brand_code = $_POST['brand_code'];
      $brand_name = $_POST['brand_name'];
      $status = $_POST['status'];

      $brand_name = str_replace("'", "\'", $brand_name);
      $brand_name = str_replace('"', '\"', $brand_name);

      $new_picture = uploadBrandFile($_FILES['picture'] ?? null, $targetDir);
      $picture = !empty($new_picture) ? $new_picture : ($edit_row['picture'] ?? '');

      $query = "UPDATE tbl_brand_master SET brand_code='$brand_code', brand_name='$brand_name', status='$status', picture='$picture' WHERE id='$edit_id'";
      $run = mysqli_query($conn, $query);
      if ($run) {
         echo "
         <script>
         document.addEventListener('DOMContentLoaded', function() {
             showPopup(
                 'Success!',
                 'Brand Name Updated Successfully 🎉'
             );
             setTimeout(function(){ window.location.href = 'fh_brandmastercreation.php'; }, 1500);
         });
         </script>
         ";
      } else {
         $msg = '<div class="alert alert-danger alert-dismissible fade1 show">
             <button type="button" class="close" data-dismiss="alert">&times;</button>
             <strong>Brand Master Update Failed!</strong>
         </div>';
      }
   }

   // Create Record
   if (isset($_POST['submit'])) {
      $brand_code = $_POST['brand_code'];
      $brand_name = $_POST['brand_name'];
      $status = $_POST['status'];

      $dup = mysqli_query($conn, "SELECT * FROM tbl_brand_master WHERE brand_name='$brand_name'");
      if (mysqli_num_rows($dup) > 0) {
          $msg = '<div class="alert alert-danger alert-dismissible fade1 show">
              <button type="button" class="close" data-dismiss="alert">&times;</button>
              <strong>Brand Name Already exist!</strong>
          </div>';
      } else {
          $new_picture = uploadBrandFile($_FILES['picture'] ?? null, $targetDir);
          $query = "INSERT INTO tbl_brand_master(brand_code,brand_name,status,picture) VALUES('$brand_code','$brand_name','$status','$new_picture')";
          $run = mysqli_query($conn, $query);
          if ($run) {
              echo "
              <script>
              document.addEventListener('DOMContentLoaded', function() {
                  showPopup(
                      'Success!',
                      'Brand Name Saved Successfully 🎉'
                  );
                  setTimeout(function(){ window.location.href = 'fh_brandmastercreation.php'; }, 1500);
              });
              </script>
              ";
          } else {
              $msg = '<div class="alert alert-danger alert-dismissible fade1 show">
                  <button type="button" class="close" data-dismiss="alert">&times;</button>
                  <strong>Brand Creation Failed!</strong>
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
      <title>Brand Master <?php echo $is_edit ? 'Edit' : 'Creation'; ?> || TEJASERP</title>
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
            <?php include('include/fh-form-scrolling-data-list.php');?> 
            <?php include('include/fh-popup-message-successfully.php');?> 
         </aside>
         
         <!-- Content Wrapper. Contains page content -->
         <div class="content-wrapper">
            <div class="container-fluid">
               <div class="row">
                  <?php include('include/menu-header.php');?>
               </div>
            </div>
            
            <!-- Main content (Form Panel) -->
            <section class="">
               <div class="row">
                  <div class="col-sm-12">
                     <div class="panel panel-bd lobidisable">
                        <div class="panel-heading">
                           <span class="fs-15 panel-header-text">
                              <i class="fa fa-pencil mr-5"></i>
                              <span><?php echo $is_edit ? 'Brand Master Edit !' : 'Brand Master Creations !'; ?></span>
                           </span>
                        </div>
                        <div class="panel-body form-scroll">
                           <div class="btn-group">
                              <a href="fh_brandmastercreation_list.php" class="btn btn-exp btn-sm">
                                 <i class="fa fa-list-alt mr-5"></i>Brand Master List !
                              </a>
                              <?php if ($is_edit): ?>
                                 <a href="fh_brandmastercreation.php" class="btn btn-info btn-sm ml-5">
                                    <i class="fa fa-plus mr-5"></i>Add New Brand
                                 </a>
                              <?php endif; ?>
                           </div>
                           <form method="post" enctype="multipart/form-data">
                              <div class="form-group">
                                 <?php echo $msg; ?>
                              </div>
                              <div class="row">
                                 <div class="col-sm-4">
                                    <div class="form-group">
                                       <label>Brand Code</label><label class="text-danger">*</label>
                                       <input type="text" name="brand_code" class="form-control" id="brand_code" readonly value="<?php 
                                          if ($is_edit) {
                                              echo htmlspecialchars($edit_row['brand_code'] ?? '');
                                          } else {
                                              @$no = mysqli_query($conn, "SELECT id FROM tbl_brand_master ORDER BY id DESC LIMIT 1");
                                              $po_no = mysqli_fetch_assoc($no);
                                              echo 'BC-000'.(($po_no['id'] ?? 0) + 1);
                                          }
                                       ?>">
                                    </div>
                                 </div>
                                 <div class="col-sm-8">
                                    <div class="form-group">
                                       <label>Brand Name</label><label class="text-danger">*</label>
                                       <input type="text" class="form-control" name="brand_name" id="brand_name" placeholder="Enter Your Brand Name" required value="<?php echo htmlspecialchars($edit_row['brand_name'] ?? ''); ?>">
                                    </div>
                                 </div>
                              </div>
                              <div class="row">
                                 <div class="col-sm-4">
                                    <div class="form-group">
                                       <label>Brand Image</label>
                                       <input type="file" name="picture" id="picture" class="form-control" accept="image/*">
                                       <?php if ($is_edit && !empty($edit_row['picture']) && file_exists("uploads/brand-master/" . $edit_row['picture'])) { ?>
                                          <div class="mt-5">
                                             <img src="uploads/brand-master/<?php echo htmlspecialchars($edit_row['picture']); ?>" alt="Brand Image" class="avatar-img-table" style="width: 48px; height: 48px; object-fit: cover; border-radius: 4px; border: 1px solid #ccc; vertical-align: middle;">
                                             <span class="text-muted fs-11 ml-5"><?php echo htmlspecialchars($edit_row['picture']); ?></span>
                                          </div>
                                       <?php } ?>
                                    </div>
                                 </div>
                                 <div class="col-sm-4">
                                    <div class="form-check">
                                       <label>Status</label><label class="text-danger">*</label><br>
                                       <label class="radio-inline">
                                          <input type="radio" name="status" value="true" <?php echo ($edit_row['status'] == 'true' || strtolower($edit_row['status']) == 'active') ? 'checked="checked"' : ''; ?>>Active
                                       </label>
                                       <label class="radio-inline">
                                          <input type="radio" name="status" value="false" <?php echo ($edit_row['status'] == 'false' || strtolower($edit_row['status']) == 'inactive') ? 'checked="checked"' : ''; ?>>Inactive
                                       </label>
                                    </div>
                                 </div>
                              </div>
                              <br>
                              <div class="container-fluid">
                                 <div class="row">
                                    <?php if ($is_edit): ?>
                                       <a href="fh_brandmastercreation.php" class="btn btn-default btn-w-100">Cancel</a>
                                       <button type="submit" name="update" class="btn btn-teal-100">Update</button>
                                    <?php else: ?>
                                       <button type="reset" class="btn btn-warning btn-w-100">Reset</button>
                                       <button type="submit" name="submit" class="btn btn-teal-100">Save</button>
                                    <?php endif; ?>
                                 </div>
                              </div>
                              <br>
                           </form>
                        </div>
                     </div>
                  </div>
               </div>
            </section>

            <!-- Main content (List Section) -->
            <section class="">
               <div class="row">
                  <div class="col-sm-12">
                     <div class="panel panel-bd lobidisable">
                        <div class="panel-heading">
                           <span class="fs-15 panel-header-text">
                              <i class="fa fa-th-list mr-5"></i>
                              <span>Brand Master List !</span>
                           </span>
                        </div>
                        <div class="panel-body form-scroll">
                           <!-- Toolbar & Search Box Row under Header Panel -->
                           <div class="row m-b-15">
                              <div class="col-sm-6 col-xs-12 m-b-5">
                                 <div class="btn-group">
                                    <a href="fh_brandmastercreation.php" class="btn btn-exp btn-sm">
                                       <i class="fa fa-plus mr-5"></i>Add New Brand Master Creation !
                                    </a>
                                 </div>
                                 <?php include('include/button-export-to-data.php');?>
                              </div>
                              <div class="col-sm-6 col-xs-12">
                                 <!-- Search Form Box under Header Panel -->
                                 <form id="brandMasterSearchForm" class="search-form-wrap pull-right" onsubmit="return false;" style="width: 100%; max-width: 360px;">
                                    <input type="hidden" name="page" value="1">
                                    <div class="input-group search-box-group">
                                       <input type="text" name="search" class="form-control search-input search-input-field" placeholder="Search Brand Code, Name, Status...">
                                       <span class="input-group-addon search-addon-btn">
                                          <i class="fa fa-search search-icon-sm"></i>
                                       </span>
                                    </div>
                                 </form>
                                 <div class="clearfix"></div>
                              </div>
                           </div>

                           <div class="table-responsive">
                              <table class="table table-bordered table-striped table-hover">
                                 <thead>
                                    <tr class="info">
                                       <th width="50px">SrNo</th>
                                       <th width="150px">Action</th>
                                       <th width="80px">Status</th>
                                       <th class="text-center-align">Image</th>
                                       <th>Brand Code</th>
                                       <th>Brand Name</th>
                                    </tr>
                                 </thead>
                                 <tbody id="brandmaster-list-results">
                                    <!-- Populated via AJAX -->
                                 </tbody>
                              </table>
                           </div>
                           
                           <!-- Pagination -->
                           <div id="pagination-result"></div>
                        </div>
                     </div>
                  </div>
               </div>
            </section>
         </div>
         
         <!-- Container for View Modals -->
         <div id="brand-modals-container"></div>

         <?php include('include/footer.php');?> 
         <?php include('include/Sidenavbuttons.php');?>
      </div>

      <?php include('include/js.php');?>
      <!-- Brand Master AJAX Pagination & Search JS -->
      <script src="assets/dist/js/page/brandmaster-list.js"></script>
   </body>
</html>