<?php include('top.php');?>
<?php
   $msg = '';
   $is_edit = false;
   $edit_id = '';
   $edit_row = array(
      'exphead_code' => '',
      'exphead_name' => '',
      'status' => 'true'
   );

   if (isset($_GET['update_id']) && !empty($_GET['update_id'])) {
      $is_edit = true;
      $edit_id = $_GET['update_id'];
      $run = mysqli_query($conn, "SELECT * FROM tbl_expenseshead_master WHERE id='$edit_id'");
      if ($run && mysqli_num_rows($run) > 0) {
         $edit_row = mysqli_fetch_assoc($run);
      }
   }

   // Update Expenses Head Record
   if (isset($_POST['update']) && !empty($edit_id)) {
      $exphead_code = $_POST['exphead_code'];
      $exphead_name = $_POST['exphead_name'];
      $status = $_POST['status'];

      $exphead_name = str_replace("'", "\'", $exphead_name);
      $exphead_name = str_replace('"', '\"', $exphead_name);

      $query = "UPDATE tbl_expenseshead_master SET exphead_code='$exphead_code', exphead_name='$exphead_name', status='$status' WHERE id='$edit_id'";
      $run = mysqli_query($conn, $query);
      if ($run) {
         echo "
         <script>
         document.addEventListener('DOMContentLoaded', function() {
             showPopup(
                 'Success!',
                 'Expenses Head Details Updated Successfully 🎉'
             );
             setTimeout(function(){ window.location.href = 'fh_expenses_headmastercreation.php'; }, 1500);
         });
         </script>
         ";
      } else {
         $msg = '<div class="alert alert-danger alert-dismissible fade1 show">
             <button type="button" class="close" data-dismiss="alert">&times;</button>
             <strong>Expenses Head Update Failed!</strong>
         </div>';
      }
   }

   // Create Expenses Head Record
   if (isset($_POST['submit'])) {
      $exphead_code = $_POST['exphead_code'];
      $exphead_name = $_POST['exphead_name'];
      $status = $_POST['status'];

      $dup = mysqli_query($conn, "SELECT * FROM tbl_expenseshead_master WHERE exphead_name='$exphead_name'");
      if (mysqli_num_rows($dup) > 0) {
          $msg = '<div class="alert alert-danger alert-dismissible fade1 show">
              <button type="button" class="close" data-dismiss="alert">&times;</button>
              <strong>Expenses Head Name Already exist!</strong>
          </div>';
      } else {
          $query = "INSERT INTO tbl_expenseshead_master(exphead_code,exphead_name,status) VALUES('$exphead_code','$exphead_name','$status')";
          $run = mysqli_query($conn, $query);
          if ($run) {
              echo "
              <script>
              document.addEventListener('DOMContentLoaded', function() {
                  showPopup(
                      'Success!',
                      'Expenses Head Details Saved Successfully 🎉'
                  );
                  setTimeout(function(){ window.location.href = 'fh_expenses_headmastercreation.php'; }, 1500);
              });
              </script>
              ";
          } else {
              $msg = '<div class="alert alert-danger alert-dismissible fade1 show">
                  <button type="button" class="close" data-dismiss="alert">&times;</button>
                  <strong>Expenses Head Creation Failed!</strong>
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
      <title>Expenses Head Master <?php echo $is_edit ? 'Edit' : 'Creation'; ?> || TEJASERP</title>
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
            
            <!-- Main content (Form Controls) -->
            <section class="">
               <div class="row">
                  <div class="col-sm-12">
                     <div class="panel panel-bd lobidisable">
                        <div class="panel-heading">
                           <span class="fs-15 panel-header-text">
                              <i class="fa fa-pencil mr-5"></i>
                              <span><?php echo $is_edit ? 'Expenses Head Edit !' : 'Expenses Head Creations !'; ?></span>
                           </span>
                        </div>
                        <div class="panel-body form-scroll">
                           <div class="btn-group">
                              <a href="fh_expensesheadmastercreation_list.php">
                                 <button type="button" class="button-btn-btn btn-exp btn-sm"><i class="fa fa-bars mr-5"></i>Expenses Head List</button>
                              </a>
                              <?php if ($is_edit) { ?>
                              <a href="fh_expenses_headmastercreation.php" class="ml-5">
                                 <button type="button" class="btn btn-primary btn-sm"><i class="fa fa-plus mr-5"></i>Add New Expenses Head</button>
                              </a>
                              <?php } ?>
                           </div>
                           <form method="post">
                              <div class="form-group">
                                 <?php echo $msg; ?>
                              </div>
                              <div class="row">
                                 <div class="col-sm-4">
                                    <div class="form-group">
                                       <label>Expenses Head Code</label><label class="text-required">*</label>
                                       <input type="text" name="exphead_code" class="form-control" id="exphead_code" readonly value="<?php 
                                          if ($is_edit) {
                                              echo htmlspecialchars($edit_row['exphead_code'] ?? '');
                                          } else {
                                              @$no = mysqli_query($conn, "SELECT id FROM tbl_expenseshead_master ORDER BY id DESC LIMIT 1");
                                              $po_no = mysqli_fetch_assoc($no);
                                              echo 'EHC-000'.(($po_no['id'] ?? 0)+1);
                                          }
                                       ?>">
                                    </div>
                                 </div>
                                 <div class="col-sm-8">
                                    <div class="form-group">
                                       <label>Expenses Head Name</label><label class="text-required">*</label>
                                       <input type="text" class="form-control" name="exphead_name" id="exphead_name" value="<?php echo htmlspecialchars($edit_row['exphead_name'] ?? ''); ?>" placeholder="Enter Your Expenses Head Name" required>
                                    </div>
                                 </div>
                              </div>
                              <div class="row">
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
                                       <a href="fh_expenses_headmastercreation.php" class="btn btn-warning btn-w-100">Cancel</a>
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

            <!-- Main content (List Section) -->
            <section class="">
               <div class="row">
                  <div class="col-sm-12">
                     <div class="panel panel-bd lobidisable">
                        <div class="panel-heading">
                           <span class="fs-15 panel-header-text">
                              <i class="fa fa-th-list mr-5"></i>
                              <span>Expenses Head Master List !</span>
                           </span>
                        </div>
                        <div class="panel-body form-scroll">
                           <div class="row m-b-15">
                              <div class="col-sm-6 pull-right">
                                 <form id="expensesHeadMasterSearchForm" class="search-form-wrap" onsubmit="return false;">
                                    <input type="hidden" name="page" id="page" value="1">
                                    <div class="input-group search-box-group">
                                       <input type="text" class="form-control search-input search-input-field" name="search" placeholder="Search Expenses Head..." autocomplete="off">
                                       <span class="input-group-addon search-addon-btn">
                                          <i class="fa fa-search search-icon-sm"></i>
                                       </span>
                                    </div>
                                 </form>
                              </div>
                           </div>
                           <div class="mb-10">
                              <div class="btn-group">
                                 <a href="fh_expenses_headmastercreation.php">
                                    <button type="button" class="button-btn-btn btn-exp btn-sm"><i class="fa fa-plus mr-5"></i>Add New Expenses Head Master Creation</button>
                                 </a>
                              </div>
                              <?php include('include/button-export-to-data.php');?>
                           </div>
                           <div class="table-responsive">
                              <table class="table table-bordered table-striped table-hover">
                                 <thead>
                                    <tr class="info">
                                       <th class="th-width-40-center">#</th>
                                       <th class="th-minwidth-100-center">Action</th>
                                       <th class="text-center-align">Status</th>
                                       <th>Expenses Head Code</th>
                                       <th>Expenses Head Name</th>
                                    </tr>
                                 </thead>
                                 <tbody id="expensesheadmaster-list-results">
                                    <!-- AJAX rows -->
                                 </tbody>
                              </table>
                           </div>

                           <div id="pagination-result"></div>
                        </div>
                     </div>
                  </div>
               </div>
            </section>
         </div>

         <div id="expenseshead-modals-container"></div>

         <?php include('include/footer.php');?> 
         <?php include('include/Sidenavbuttons.php');?>
      </div>
      <?php include('include/js.php');?>
      <script src="assets/dist/js/page/expensesheadmaster-list.js"></script>
   </body>
</html>
