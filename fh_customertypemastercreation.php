<?php include('top.php');?>
<?php
   $msg = '';
   $is_edit = false;
   $edit_id = '';
   $edit_row = array(
      'customertype_code' => '',
      'customer_type' => '',
      'discount_per' => '',
      'status' => 'true'
   );

   if (isset($_GET['update_id']) && !empty($_GET['update_id'])) {
      $is_edit = true;
      $edit_id = $_GET['update_id'];
      $run = mysqli_query($conn, "SELECT * FROM tbl_customertype_master WHERE id='$edit_id'");
      if ($run && mysqli_num_rows($run) > 0) {
         $edit_row = mysqli_fetch_assoc($run);
      }
   }

   // Update Record
   if (isset($_POST['update']) && !empty($edit_id)) {
      $customertype_code = $_POST['customertype_code'];
      $customer_type = $_POST['customer_type'];
      $discount_per = $_POST['discount_per'];
      $status = $_POST['status'];

      $customer_type = str_replace("'", "\'", $customer_type);
      $customer_type = str_replace('"', '\"', $customer_type);

      $query = "UPDATE tbl_customertype_master SET customertype_code='$customertype_code', customer_type='$customer_type', discount_per='$discount_per', status='$status' WHERE id='$edit_id'";
      $run = mysqli_query($conn, $query);
      if ($run) {
         echo "
         <script>
         document.addEventListener('DOMContentLoaded', function() {
             showPopup(
                 'Success!',
                 'Customer Type Updated Successfully 🎉'
             );
             setTimeout(function(){ window.location.href = 'fh_customertypemastercreation.php'; }, 1500);
         });
         </script>
         ";
      } else {
         $msg = '<div class="alert alert-danger alert-dismissible fade1 show">
             <button type="button" class="close" data-dismiss="alert">&times;</button>
             <strong>Customer Type Master Update Failed!</strong>
         </div>';
      }
   }

   // Create Record
   if (isset($_POST['submit'])) {
      $customertype_code = $_POST['customertype_code'];
      $customer_type = $_POST['customer_type'];
      $discount_per = $_POST['discount_per'];
      $status = $_POST['status'];

      $dup = mysqli_query($conn, "SELECT * FROM tbl_customertype_master WHERE customer_type='$customer_type'");
      if (mysqli_num_rows($dup) > 0) {
          $msg = '<div class="alert alert-danger alert-dismissible fade1 show">
              <button type="button" class="close" data-dismiss="alert">&times;</button>
              <strong>Customer Type Already exist!</strong>
          </div>';
      } else {
          $query = "INSERT INTO tbl_customertype_master(customertype_code,customer_type,discount_per,status) VALUES('$customertype_code','$customer_type','$discount_per','$status')";
          $run = mysqli_query($conn, $query);
          if ($run) {
              echo "
              <script>
              document.addEventListener('DOMContentLoaded', function() {
                  showPopup(
                      'Success!',
                      'Customer Type Saved Successfully 🎉'
                  );
                  setTimeout(function(){ window.location.href = 'fh_customertypemastercreation.php'; }, 1500);
              });
              </script>
              ";
          } else {
              $msg = '<div class="alert alert-danger alert-dismissible fade1 show">
                  <button type="button" class="close" data-dismiss="alert">&times;</button>
                  <strong>Customer Type Creation Failed!</strong>
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
      <title>Customer Type Master <?php echo $is_edit ? 'Edit' : 'Creation'; ?> || TEJASERP</title>
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
                              <span><?php echo $is_edit ? 'Customer Type Master Edit !' : 'Customer Type Master Creations !'; ?></span>
                           </span>
                        </div>
                        <div class="panel-body form-scroll">
                           <div class="btn-group">
                              <a href="fh_customertypemastercreation_list.php" class="btn btn-exp btn-sm">
                                 <i class="fa fa-list-alt mr-5"></i>Customer Type Master List !
                              </a>
                              <?php if ($is_edit): ?>
                                 <a href="fh_customertypemastercreation.php" class="btn btn-info btn-sm ml-5">
                                    <i class="fa fa-plus mr-5"></i>Add New Customer Type
                                 </a>
                              <?php endif; ?>
                           </div>
                           <form method="post">
                              <div class="form-group">
                                 <?php echo $msg; ?>
                              </div>
                              <div class="row">
                                 <div class="col-sm-4">
                                    <div class="form-group">
                                       <label>Customer Type Code</label><label class="text-danger">*</label>
                                       <input type="text" name="customertype_code" class="form-control" id="customertype_code" readonly value="<?php 
                                          if ($is_edit) {
                                              echo htmlspecialchars($edit_row['customertype_code']);
                                          } else {
                                              @$no = mysqli_query($conn, "SELECT id FROM tbl_customertype_master ORDER BY id DESC LIMIT 1");
                                              $po_no = mysqli_fetch_assoc($no);
                                              echo 'CTC-000'.(($po_no['id'] ?? 0) + 1);
                                          }
                                       ?>">
                                    </div>
                                 </div>
                                 <div class="col-sm-5">
                                    <div class="form-group">
                                       <label>Customer Type</label><label class="text-danger">*</label>
                                       <input type="text" class="form-control" name="customer_type" id="customer_type" placeholder="Enter Your Customer Type" required value="<?php echo htmlspecialchars($edit_row['customer_type']); ?>">
                                    </div>
                                 </div>
                                 <div class="col-sm-3">
                                    <div class="form-group">
                                       <label>Discount (%)</label><label class="text-danger">*</label>
                                       <input type="text" class="form-control" name="discount_per" id="discount_per" placeholder="Enter Discount %" required value="<?php echo htmlspecialchars($edit_row['discount_per']); ?>">
                                    </div>
                                 </div>
                              </div>
                              <br>
                              <div class="row">
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
                              <br><br>
                              <div class="container-fluid">
                                 <div class="row">
                                    <?php if ($is_edit): ?>
                                       <a href="fh_customertypemastercreation.php" class="btn btn-default btn-w-100">Cancel</a>
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
                               <span>Customer Type Master List !</span>
                            </span>
                         </div>
                         <div class="panel-body form-scroll">
                            <!-- Toolbar & Search Box Row under Header Panel -->
                            <div class="row m-b-15">
                               <div class="col-sm-6 col-xs-12 m-b-5">
                                  <div class="btn-group">
                                     <a href="fh_customertypemastercreation.php" class="btn btn-exp btn-sm">
                                        <i class="fa fa-plus mr-5"></i>Add New Customer Type Master Creation !
                                     </a>
                                  </div>
                                  <?php include('include/button-export-to-data.php');?>
                               </div>
                               <div class="col-sm-6 col-xs-12">
                                  <!-- Search Form Box under Header Panel -->
                                  <form id="customerTypeMasterSearchForm" class="search-form-wrap pull-right" onsubmit="return false;" style="width: 100%; max-width: 360px;">
                                     <input type="hidden" name="page" value="1">
                                     <div class="input-group search-box-group">
                                        <input type="text" name="search" class="form-control search-input search-input-field" placeholder="Search Customer Type Code, Name, Status...">
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
                                       <th>Customer Type Code</th>
                                       <th>Customer Type</th>
                                       <th>Discount (%)</th>
                                    </tr>
                                 </thead>
                                 <tbody id="customertypemaster-list-results">
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
         <div id="customertype-modals-container"></div>

         <?php include('include/footer.php');?> 
         <?php include('include/Sidenavbuttons.php');?>
      </div>

      <?php include('include/js.php');?>
      <!-- Customer Type Master AJAX Pagination & Search JS -->
      <script src="assets/dist/js/page/customertypemaster-list.js"></script>
   </body>
</html>
