<?php include('top.php');?>
<?php
   $msg = '';
   $is_edit = false;
   $edit_id = '';
   $edit_row = array(
      'gst_code' => '',
      'group_name' => '',
      'hsn_code' => '',
      'gst_per' => '',
      'gst_range' => '',
      'status' => 'true'
   );

   if (isset($_GET['update_id']) && !empty($_GET['update_id'])) {
      $is_edit = true;
      $edit_id = $_GET['update_id'];
      $run = mysqli_query($conn, "SELECT * FROM tbl_gst_master WHERE id='$edit_id'");
      if ($run && mysqli_num_rows($run) > 0) {
         $edit_row = mysqli_fetch_assoc($run);
      }
   }

   // Update Record
   if (isset($_POST['update']) && !empty($edit_id)) {
      $gst_range = $_POST['gst_range'];
      $gst_code  = $_POST['gst_code'];
      $gst_per   = $_POST['gst_per'];
      $group_name = $_POST['group_name'];
      $hsn_code  = $_POST['hsn_code'];
      $status    = $_POST['status'];

      $group_name = str_replace("'", "\'", $group_name);
      $group_name = str_replace('"', '\"', $group_name);

      $query = "UPDATE tbl_gst_master SET gst_range='$gst_range', gst_code='$gst_code', gst_per='$gst_per', group_name='$group_name', hsn_code='$hsn_code', status='$status' WHERE id='$edit_id'";
      $run = mysqli_query($conn, $query);
      if ($run) {
         echo "
         <script>
         document.addEventListener('DOMContentLoaded', function() {
             showPopup(
                 'Success!',
                 'GST Master Updated Successfully 🎉'
             );
             setTimeout(function(){ window.location.href = 'fh_gstmastercreation.php'; }, 1500);
         });
         </script>
         ";
      } else {
         $msg = '<div class="alert alert-danger alert-dismissible fade1 show">
             <button type="button" class="close" data-dismiss="alert">&times;</button>
             <strong>GST Master Update Failed!</strong>
         </div>';
      }
   }

   // Create Record
   if (isset($_POST['submit'])) {
      $gst_range = $_POST['gst_range'];
      $gst_code  = $_POST['gst_code'];
      $gst_per   = $_POST['gst_per'];
      $group_name = $_POST['group_name'];
      $hsn_code  = $_POST['hsn_code'];
      $status    = $_POST['status'];

      $dup = mysqli_query($conn, "SELECT * FROM tbl_gst_master WHERE group_name='$group_name'");
      if (mysqli_num_rows($dup) > 0) {
          $msg = '<div class="alert alert-danger alert-dismissible fade1 show">
              <button type="button" class="close" data-dismiss="alert">&times;</button>
              <strong>GST percentage Already exist!</strong>
          </div>';
      } else {
          $query = "INSERT INTO tbl_gst_master(gst_range,gst_code,gst_per,group_name,hsn_code,status) VALUES('$gst_range','$gst_code','$gst_per','$group_name','$hsn_code','$status')";
          $run = mysqli_query($conn, $query);
          if ($run) {
              echo "
              <script>
              document.addEventListener('DOMContentLoaded', function() {
                  showPopup(
                      'Success!',
                      'GST Percentage Saved Successfully 🎉'
                  );
                  setTimeout(function(){ window.location.href = 'fh_gstmastercreation.php'; }, 1500);
              });
              </script>
              ";
          } else {
              $msg = '<div class="alert alert-danger alert-dismissible fade1 show">
                  <button type="button" class="close" data-dismiss="alert">&times;</button>
                  <strong>GST Creation Failed!</strong>
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
      <title>GST Master <?php echo $is_edit ? 'Edit' : 'Creation'; ?> || TEJASERP</title>
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
                              <span><?php echo $is_edit ? 'GST Master Edit !' : 'GST Master Creations !'; ?></span>
                           </span>
                        </div>
                        <div class="panel-body form-scroll">
                           <div class="btn-group">
                              <a href="fh_gstmastercreation_list.php" class="btn btn-exp btn-sm">
                                 <i class="fa fa-list-alt mr-5"></i>GST Master List !
                              </a>
                              <?php if ($is_edit): ?>
                                 <a href="fh_gstmastercreation.php" class="btn btn-info btn-sm ml-5">
                                    <i class="fa fa-plus mr-5"></i>Add New GST
                                 </a>
                              <?php endif; ?>
                           </div>
                           <form method="post">
                              <div class="form-group">
                                 <?php echo $msg; ?>
                              </div>
                              <div class="row">
                                 <div class="col-sm-3">
                                    <div class="form-group">
                                       <label>GST Code</label><label class="text-danger">*</label>
                                       <input type="text" name="gst_code" class="form-control" id="gst_code" readonly value="<?php 
                                          if ($is_edit) {
                                              echo htmlspecialchars($edit_row['gst_code']);
                                          } else {
                                              @$no = mysqli_query($conn, "SELECT id FROM tbl_gst_master ORDER BY id DESC LIMIT 1");
                                              $po_no = mysqli_fetch_assoc($no);
                                              echo 'GST-000'.(($po_no['id'] ?? 0) + 1);
                                          }
                                       ?>">
                                    </div>
                                 </div>
                                 <div class="col-sm-3">
                                    <div class="form-group">
                                       <label>Group || Category Name</label><label class="text-danger">*</label>
                                       <select name="group_name" class="form-control" required>
                                          <option value="">Select Group Name</option>
                                          <?php
                                             $query = mysqli_query($conn, "SELECT * FROM tbl_group_master");
                                             while ($row = mysqli_fetch_array($query)) {
                                                $selected = ($edit_row['group_name'] == $row['group_name']) ? 'selected' : '';
                                                echo '<option ' . $selected . '>' . htmlspecialchars($row['group_name']) . '</option>';
                                             }
                                          ?>
                                       </select>
                                    </div>
                                 </div>
                                 <div class="col-sm-3">
                                    <div class="form-group">
                                       <label>HSN Code</label><label class="text-danger">*</label>
                                       <input type="text" class="form-control" name="hsn_code" id="hsn_code" placeholder="Enter HSN Code" required value="<?php echo htmlspecialchars($edit_row['hsn_code']); ?>">
                                    </div>
                                 </div>
                                 <div class="col-sm-3">
                                    <div class="form-group">
                                       <label>GST (%)</label><label class="text-danger">*</label>
                                       <input type="text" class="form-control" name="gst_per" id="gst_per" placeholder="Enter GST %" required value="<?php echo htmlspecialchars($edit_row['gst_per']); ?>">
                                    </div>
                                 </div>
                              </div>
                              <div class="row">
                                 <div class="col-sm-6">
                                    <div class="form-group">
                                       <label>GST Range</label>
                                       <input type="text" class="form-control" name="gst_range" id="gst_range" placeholder="Enter GST Range (e.g. 0 to 1000)" value="<?php echo htmlspecialchars($edit_row['gst_range']); ?>">
                                    </div>
                                 </div>
                                 <div class="col-sm-6">
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
                              <br><br>
                              <div class="container-fluid">
                                 <div class="row">
                                    <?php if ($is_edit): ?>
                                       <a href="fh_gstmastercreation.php" class="btn btn-default btn-w-100">Cancel</a>
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
                              <span>GST Master List !</span>
                           </span>
                        </div>
                        <div class="panel-body form-scroll">
                           <!-- Toolbar & Search Box Row under Header Panel -->
                           <div class="row m-b-15">
                              <div class="col-sm-6 col-xs-12 m-b-5">
                                 <div class="btn-group">
                                    <a href="fh_gstmastercreation.php" class="btn btn-exp btn-sm">
                                       <i class="fa fa-plus mr-5"></i>Add New GST Master Creation !
                                    </a>
                                 </div>
                                 <?php include('include/button-export-to-data.php');?>
                              </div>
                              <div class="col-sm-6 col-xs-12">
                                 <!-- Search Form Box under Header Panel -->
                                 <form id="gstMasterSearchForm" class="search-form-wrap pull-right" onsubmit="return false;" style="width: 100%; max-width: 360px;">
                                    <input type="hidden" name="page" value="1">
                                    <div class="input-group search-box-group">
                                       <input type="text" name="search" class="form-control search-input search-input-field" placeholder="Search GST Code, Group, HSN, %...">
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
                                       <th>GST Code</th>
                                       <th>Group Name</th>
                                       <th>HSN Code</th>
                                       <th>GST (%)</th>
                                       <th>GST Range</th>
                                    </tr>
                                 </thead>
                                 <tbody id="gstmaster-list-results">
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
         <div id="gst-modals-container"></div>

         <?php include('include/footer.php');?> 
         <?php include('include/Sidenavbuttons.php');?>
      </div>

      <?php include('include/js.php');?>
      <!-- GST Master AJAX Pagination & Search JS -->
      <script src="assets/dist/js/page/gstmaster-list.js"></script>
   </body>
</html>