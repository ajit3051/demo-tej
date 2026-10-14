<?php include('top.php');?>
<?php
   $msg = '';
   $is_edit = false;
   $edit_id = '';
   $edit_row = array(
      'dis_code' => '',
      'distype_name' => '',
      'group_name' => '',
      'brand_name' => '',
      'barcode_no' => '',
      'item_name' => '',
      'store_name' => '',
      'location_name' => '',
      'discount_per' => '',
      'discount_amt' => '',
      'mrp' => '',
      'sp' => '',
      'status' => 'Active'
   );

   if (isset($_GET['update_id']) && !empty($_GET['update_id'])) {
      $is_edit = true;
      $edit_id = mysqli_real_escape_string($conn, $_GET['update_id']);
      $run = mysqli_query($conn, "SELECT * FROM tbl_discount_master WHERE id='$edit_id'");
      if ($run && mysqli_num_rows($run) > 0) {
         $edit_row = mysqli_fetch_assoc($run);
      }
   }

   // Update Record
   if (isset($_POST['update']) && !empty($edit_id)) {
      $dis_code = mysqli_real_escape_string($conn, $_POST['dis_code']);
      $store_name = mysqli_real_escape_string($conn, $_POST['store_name']);
      $location_name = mysqli_real_escape_string($conn, $_POST['location_name']);
      $distype_name = mysqli_real_escape_string($conn, $_POST['fest_name'] ?? '');
      $group_name = mysqli_real_escape_string($conn, $_POST['group_name']);
      $brand_name = mysqli_real_escape_string($conn, $_POST['brand_name']);
      $barcode_no = mysqli_real_escape_string($conn, $_POST['barcode_no']);
      $item_name = mysqli_real_escape_string($conn, $_POST['item_name']);
      $discount_per = mysqli_real_escape_string($conn, $_POST['discount_per']);
      $discount_amt = mysqli_real_escape_string($conn, $_POST['discount_amt']);
      $mrp = mysqli_real_escape_string($conn, $_POST['mrp']);
      $sp = mysqli_real_escape_string($conn, $_POST['sp']);
      $status = mysqli_real_escape_string($conn, $_POST['status']);

      $query = "UPDATE tbl_discount_master SET 
         dis_code='$dis_code', 
         distype_name='$distype_name', 
         group_name='$group_name', 
         brand_name='$brand_name', 
         barcode_no='$barcode_no', 
         item_name='$item_name', 
         store_name='$store_name', 
         location_name='$location_name', 
         discount_per='$discount_per', 
         discount_amt='$discount_amt', 
         mrp='$mrp', 
         sp='$sp', 
         status='$status' 
         WHERE id='$edit_id'";

      $run = mysqli_query($conn, $query);
      if ($run) {
         echo "
         <script>
         document.addEventListener('DOMContentLoaded', function() {
             showPopup(
                 'Success!',
                 'Discount Master Updated Successfully 🎉'
             );
             setTimeout(function(){ window.location.href = 'fh_discountmastercreation.php'; }, 1500);
         });
         </script>
         ";
      } else {
         $msg = '<div class="alert alert-danger alert-dismissible fade1 show">
             <button type="button" class="close" data-dismiss="alert">&times;</button>
             <strong>Discount Master Update Failed!</strong>
         </div>';
      }
   }

   // Create Record
   if (isset($_POST['submit'])) {
      $dis_code = mysqli_real_escape_string($conn, $_POST['dis_code']);
      $store_name = mysqli_real_escape_string($conn, $_POST['store_name']);
      $location_name = mysqli_real_escape_string($conn, $_POST['location_name']);
      $distype_name = mysqli_real_escape_string($conn, $_POST['fest_name'] ?? '');
      $group_name = mysqli_real_escape_string($conn, $_POST['group_name']);
      $brand_name = mysqli_real_escape_string($conn, $_POST['brand_name']);
      $barcode_no = mysqli_real_escape_string($conn, $_POST['barcode_no']);
      $item_name = mysqli_real_escape_string($conn, $_POST['item_name']);
      $discount_per = mysqli_real_escape_string($conn, $_POST['discount_per']);
      $discount_amt = mysqli_real_escape_string($conn, $_POST['discount_amt']);
      $mrp = mysqli_real_escape_string($conn, $_POST['mrp']);
      $sp = mysqli_real_escape_string($conn, $_POST['sp']);
      $status = mysqli_real_escape_string($conn, $_POST['status']);

      $dup = mysqli_query($conn, "SELECT * FROM tbl_discount_master WHERE group_name='$group_name' AND item_name='$item_name' AND dis_code='$dis_code'");
      if (mysqli_num_rows($dup) > 0) {
          $msg = '<div class="alert alert-danger alert-dismissible fade1 show">
              <button type="button" class="close" data-dismiss="alert">&times;</button>
              <strong>Discount Master Already exists!</strong>
          </div>';
      } else {
          $query = "INSERT INTO tbl_discount_master (dis_code, distype_name, group_name, brand_name, barcode_no, item_name, store_name, location_name, discount_per, discount_amt, mrp, sp, status) 
          VALUES ('$dis_code', '$distype_name', '$group_name', '$brand_name', '$barcode_no', '$item_name', '$store_name', '$location_name', '$discount_per', '$discount_amt', '$mrp', '$sp', '$status')";
          $run = mysqli_query($conn, $query);

          if ($run) {
              echo "
              <script>
              document.addEventListener('DOMContentLoaded', function() {
                  showPopup(
                      'Success!',
                      'Discount Master Saved Successfully 🎉'
                  );
                  setTimeout(function(){ window.location.href = 'fh_discountmastercreation.php'; }, 1500);
              });
              </script>
              ";
          } else {
              $msg = '<div class="alert alert-danger alert-dismissible fade1 show">
                  <button type="button" class="close" data-dismiss="alert">&times;</button>
                  <strong>Discount Master Creation Failed!</strong>
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
      <title>Discount Master <?php echo $is_edit ? 'Edit' : 'Creation'; ?> || TEJASERP</title>
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

            <!-- Form Section -->
            <section class="">
               <div class="row">
                  <!-- Form controls -->
                  <div class="col-sm-12">
                     <div class="panel panel-bd lobidisable">
                        <div class="panel-heading">
                           <span class="fs-15 panel-header-text">
                              <i class="fa fa-pencil mr-5"></i>
                              <span>Discount Master <?php echo $is_edit ? 'Edit' : 'Creation'; ?> !</span>
                           </span>
                        </div>
                        <div class="panel-body">
                            <div class="btn-group m-b-15">
                              <a href="fh_discountmastercreation_list.php" class="btn btn-exp btn-sm"><i class="fa fa-bars mr-5"></i>Discount Master List</a>
                              <?php if ($is_edit): ?>
                              <a href="fh_discountmastercreation.php" class="btn btn-success btn-sm"><i class="fa fa-plus mr-5"></i>Add New Discount Master</a>
                              <?php endif; ?>
                           </div>
                           <form method="post">
                              <div class="form-group">
                                 <?php echo $msg; ?>
                              </div>
                              <div class="row">
                                 <div class="col-lg-3">
                                    <div class="form-group">
                                       <label>Discount Code</label><label class="text-required">*</label>
                                       <input type="text" name="dis_code" class="form-control" id="dis_code" readonly value="<?php 
                                          if ($is_edit) {
                                             echo htmlspecialchars($edit_row['dis_code'] ?? '');
                                          } else {
                                             @$no = mysqli_query($conn, "SELECT id FROM tbl_discount_master ORDER BY id DESC LIMIT 1");
                                             $po_no = mysqli_fetch_assoc($no);
                                             $next_id = ($po_no['id'] ?? 0) + 1;
                                             echo 'DC-000' . $next_id;
                                          }
                                       ?>">
                                    </div>
                                 </div>
                                  <div class="col-lg-4">
                                    <div class="form-group">
                                       <label>Store Name</label><label class="text-required">*</label>
                                       <select name="store_name" class="form-control" required>
                                          <option value="">Select Store Name</option>
                                          <?php
                                             $query = mysqli_query($conn, "SELECT * FROM tbl_store_master");
                                             while ($row = mysqli_fetch_array($query)) {
                                                $selected = ($is_edit && ($edit_row['store_name'] ?? '') == $row['store_name']) ? 'selected' : '';
                                                echo "<option value='" . htmlspecialchars($row['store_name']) . "' $selected>" . htmlspecialchars($row['store_name']) . "</option>";
                                             }
                                          ?>
                                       </select>
                                    </div>
                                    <a href="#" data-toggle="modal" data-target="#addtrain1">
                                    <span class="fs-11 pull-left">
                                    <i class="fa fa-plus"></i> Add New Store Master
                                    </span>
                                    </a>
                                 </div>
                                 <div class="col-lg-5">
                                    <div class="form-group">
                                       <label>Location Name</label><label class="text-required">*</label>
                                       <input type="text" class="form-control" name="location_name" id="location_name" placeholder="Enter Your Location Name" required value="<?php echo htmlspecialchars($edit_row['location_name'] ?? ''); ?>">
                                    </div>
                                 </div>
                              </div>

                              <div class="row">
                                 <div class="col-lg-3">
                                    <div class="form-group">
                                       <label>Festival / Type Name</label><label class="text-required">*</label>
                                       <select name="fest_name" class="form-control" required>
                                          <option value="">Select Festival Name</option>
                                          <?php
                                             $query = mysqli_query($conn, "SELECT * FROM tbl_festival_master");
                                             while ($row = mysqli_fetch_array($query)) {
                                                $selected = ($is_edit && ($edit_row['distype_name'] ?? '') == $row['fest_name']) ? 'selected' : '';
                                                echo "<option value='" . htmlspecialchars($row['fest_name']) . "' $selected>" . htmlspecialchars($row['fest_name']) . "</option>";
                                             }
                                          ?> 
                                       </select>
                                    </div>
                                    <a href="#" data-toggle="modal" data-target="#festival">
                                    <span class="fs-11 pull-left"><i class="fa fa-plus"></i> Add New Festival Name</span>
                                    </a>
                                 </div>
                                 <div class="col-lg-4">
                                    <div class="form-group">
                                       <label>Group || Category Name</label><label class="text-required">*</label>
                                       <select name="group_name" class="form-control" required>
                                          <option value="">Select Group || Category Name</option>
                                          <?php
                                             $query = mysqli_query($conn, "SELECT * FROM tbl_group_master");
                                             while ($row = mysqli_fetch_array($query)) {
                                                $selected = ($is_edit && ($edit_row['group_name'] ?? '') == $row['group_name']) ? 'selected' : '';
                                                echo "<option value='" . htmlspecialchars($row['group_name']) . "' $selected>" . htmlspecialchars($row['group_name']) . "</option>";
                                             }
                                          ?> 
                                       </select>
                                    </div>
                                    <a href="#" data-toggle="modal" data-target="#addtrain">
                                    <span class="fs-11 pull-left"><i class="fa fa-plus"></i> Add New Group Master</span>
                                    </a>
                                 </div>
                                 <div class="col-lg-5">
                                    <div class="form-group">
                                       <label>Brand Name</label>
                                       <select name="brand_name" class="form-control">
                                          <option value="">Select Brand Name</option>
                                          <?php
                                             $query = mysqli_query($conn, "SELECT * FROM tbl_brand_master");
                                             while ($row = mysqli_fetch_array($query)) {
                                                $selected = ($is_edit && ($edit_row['brand_name'] ?? '') == $row['brand_name']) ? 'selected' : '';
                                                echo "<option value='" . htmlspecialchars($row['brand_name']) . "' $selected>" . htmlspecialchars($row['brand_name']) . "</option>";
                                             }
                                          ?> 
                                       </select>
                                    </div>
                                    <a href="#" data-toggle="modal" data-target="#addtrain">
                                    <span class="fs-11 pull-left"><i class="fa fa-plus"></i> Add New Brand Master</span>
                                    </a>
                                 </div>
                              </div>

                              <div class="row">
                                 <div class="col-lg-3">
                                    <div class="form-group">
                                       <label>Barcode No</label><label class="text-required">*</label>
                                       <input type="text" class="form-control" name="barcode_no" id="barcode_no" placeholder="Enter Your Barcode No" required value="<?php echo htmlspecialchars($edit_row['barcode_no'] ?? ''); ?>">
                                    </div>
                                 </div>
                                 <div class="col-lg-4">
                                    <div class="form-group">
                                       <label>Item Name</label><label class="text-required">*</label>
                                       <select name="item_name" class="form-control" required>
                                          <option value="">Select Item Name</option>
                                          <?php
                                             $query = mysqli_query($conn, "SELECT * FROM tbl_item_master");
                                             while ($row = mysqli_fetch_array($query)) {
                                                $selected = ($is_edit && ($edit_row['item_name'] ?? '') == $row['item_name']) ? 'selected' : '';
                                                echo "<option value='" . htmlspecialchars($row['item_name']) . "' $selected>" . htmlspecialchars($row['item_name']) . "</option>";
                                             }
                                          ?> 
                                       </select>
                                    </div>
                                 </div>
                                 <div class="col-lg-5">
                                    <div class="form-group">
                                       <label>Discount Percentage (%)</label><label class="text-required">*</label>
                                       <input type="text" class="form-control" name="discount_per" id="discount_per" placeholder="Enter Your Discount Percentage" required value="<?php echo htmlspecialchars($edit_row['discount_per'] ?? ''); ?>">
                                    </div>
                                 </div>
                              </div>

                              <div class="row">
                                 <div class="col-lg-4">
                                    <div class="form-group">
                                       <label>Discount Amount</label><label class="text-required">*</label>
                                       <input type="text" class="form-control" name="discount_amt" id="discount_amt" placeholder="Enter Your Discount Amount" required value="<?php echo htmlspecialchars($edit_row['discount_amt'] ?? ''); ?>">
                                    </div>
                                 </div>
                                 <div class="col-lg-4">
                                    <div class="form-group">
                                       <label>MRP</label><label class="text-required">*</label>
                                       <input type="text" class="form-control" name="mrp" id="mrp" placeholder="Enter Your MRP" required value="<?php echo htmlspecialchars($edit_row['mrp'] ?? ''); ?>">
                                    </div>
                                 </div>
                                 <div class="col-lg-4">
                                    <div class="form-group">
                                       <label>SP</label><label class="text-required">*</label>
                                       <input type="text" class="form-control" name="sp" id="sp" placeholder="Enter Your SP" required value="<?php echo htmlspecialchars($edit_row['sp'] ?? ''); ?>">
                                    </div>
                                 </div>
                              </div>

                              <div class="row">
                                 <div class="col-sm-6">
                                    <div class="form-group">
                                       <label>Status</label><label class="text-required">*</label><br>
                                       <label class="radio-inline">
                                          <input type="radio" name="status" value="Active" <?php echo (($edit_row['status'] ?? 'Active') === 'Active' || ($edit_row['status'] ?? '') === 'true') ? 'checked' : ''; ?>> Active
                                       </label>
                                       <label class="radio-inline">
                                          <input type="radio" name="status" value="Inactive" <?php echo (($edit_row['status'] ?? '') === 'Inactive' || ($edit_row['status'] ?? '') === 'false') ? 'checked' : ''; ?>> Inactive
                                       </label>
                                    </div>
                                 </div>
                              </div>

                              <div class="row m-t-10">
                                 <div class="col-sm-12">
                                    <?php if ($is_edit): ?>
                                       <button type="submit" name="update" class="btn btn-success btn-theme-teal">Update</button>
                                       <a href="fh_discountmastercreation.php" class="btn btn-warning">Cancel</a>
                                    <?php else: ?>
                                       <button type="submit" name="submit" class="btn btn-success btn-theme-teal">Save</button>
                                       <button type="reset" class="btn btn-warning">Reset</button>
                                    <?php endif; ?>
                                 </div>
                              </div>
                           </form>
                        </div>
                     </div>
                  </div>
               </div>
            </section>

            <!-- Listing Section below Form -->
            <section class="">
               <div class="row">
                  <div class="col-sm-12">
                     <div class="panel panel-bd lobidisable">
                         <div class="panel-heading">
                            <span class="fs-15 panel-header-text">
                               <i class="fa fa-th-list mr-5"></i>
                               <span>Discount Master List !</span>
                            </span>
                         </div>
                         <div class="panel-body form-scroll">
                            <!-- Toolbar & Search Box Row under Header Panel -->
                            <div class="row m-b-15">
                               <div class="col-sm-6 col-xs-12 m-b-5">
                                  <div class="btn-group">
                                     <a href="fh_discountmastercreation.php" class="btn btn-exp btn-sm"><i class="fa fa-plus mr-5"></i>Add New Discount Master Creation</a>
                                  </div>
                                  <?php include('include/button-export-to-data.php');?>
                               </div>
                               <div class="col-sm-6 col-xs-12">
                                  <form id="discountMasterSearchForm" class="search-form-wrap pull-right" onsubmit="return false;" style="width: 100%; max-width: 360px;">
                                     <input type="hidden" name="page" id="page" value="1">
                                     <div class="input-group search-box-group">
                                        <input type="text" class="form-control search-input search-input-field" name="search" id="company_name" placeholder="Search Discount..." autocomplete="off">
                                        <span class="input-group-addon search-addon-btn">
                                           <i class="fa fa-search search-icon-sm"></i>
                                        </span>
                                     </div>
                                  </form>
                               </div>
                            </div>
                           <div class="table-responsive">
                              <table id="dataTableExample1" class="table table-bordered table-striped table-hover">
                                 <thead>
                                    <tr class="info">
                                       <th class="th-width-40-center">#</th>
                                       <th class="th-minwidth-100-center">Action</th>
                                       <th class="text-center-align">Status</th>
                                       <th>Discount Code</th>
                                       <th>Type Name</th>
                                       <th>Group Name</th>
                                       <th>Brand Name</th>
                                       <th>Barcode No</th>
                                       <th>Item Name</th>
                                       <th>Store Name</th>
                                       <th>Location</th>
                                       <th>Discount Per</th>
                                       <th>Discount Amt</th>
                                       <th>MRP</th>
                                       <th>SP</th>
                                    </tr>
                                 </thead>
                                 <tbody id="discountmaster-list-results">
                                    <!-- Populated dynamically via AJAX -->
                                 </tbody>
                              </table>
                           </div>
                           <div class="row pagination-wrapper-row">
                              <div class="col-sm-6">
                                 <div class="pagination-info-text">
                                    Total Records: <strong id="total-records-count">0</strong>
                                 </div>
                              </div>
                              <div class="col-sm-6">
                                 <div id="discountmaster-pagination"></div>
                              </div>
                           </div>
                         </div>
                     </div>
                  </div>
               </div>
            </section>

            <!-- modal Modal1 -->
            <?php include('include/fh_modal_dialog.php');?>   
         </div>
         <!-- /.content-wrapper -->

         <!-- Details Modal Container at root level -->
         <div id="discount-modals-container">
            <div class="modal fade" id="viewDiscountModal" tabindex="-1" role="dialog" aria-hidden="true">
               <div class="modal-dialog modal-md">
                  <div class="modal-content">
                     <div class="modal-header modal-header-teal">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title"><i class="fa fa-gift mr-5"></i> Discount Master Details</h4>
                     </div>
                     <div class="modal-body">
                        <table class="table table-bordered table-striped">
                           <tr>
                              <th class="th-width-150">Discount Code</th>
                              <td id="modal-dis-code">-</td>
                           </tr>
                           <tr>
                              <th>Festival / Type Name</th>
                              <td id="modal-distype-name">-</td>
                           </tr>
                           <tr>
                              <th>Group / Category</th>
                              <td id="modal-group-name">-</td>
                           </tr>
                           <tr>
                              <th>Brand Name</th>
                              <td id="modal-brand-name">-</td>
                           </tr>
                           <tr>
                              <th>Barcode No</th>
                              <td id="modal-barcode-no">-</td>
                           </tr>
                           <tr>
                              <th>Item Name</th>
                              <td id="modal-item-name">-</td>
                           </tr>
                           <tr>
                              <th>Store Name</th>
                              <td id="modal-store-name">-</td>
                           </tr>
                           <tr>
                              <th>Location Name</th>
                              <td id="modal-location-name">-</td>
                           </tr>
                           <tr>
                              <th>Discount (%)</th>
                              <td id="modal-discount-per">-</td>
                           </tr>
                           <tr>
                              <th>Discount Amount</th>
                              <td id="modal-discount-amt">-</td>
                           </tr>
                           <tr>
                              <th>MRP</th>
                              <td id="modal-mrp">-</td>
                           </tr>
                           <tr>
                              <th>Selling Price (SP)</th>
                              <td id="modal-sp">-</td>
                           </tr>
                           <tr>
                              <th>Status</th>
                              <td id="modal-discount-status">-</td>
                           </tr>
                        </table>
                     </div>
                     <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                     </div>
                  </div>
               </div>
            </div>
         </div>

         <?php include('include/footer.php');?> 
      </div>
      <!-- ./wrapper -->
      <?php include('include/js.php');?>
      <script src="assets/dist/js/page/discountmaster-list.js"></script>
   </body>
</html>