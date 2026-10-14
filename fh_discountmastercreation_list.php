<?php include('top.php');?>
<?php

if(isset($_GET['delete_id']))
{
   $id=mysqli_real_escape_string($conn, $_GET['delete_id']);
   $sqld="DELETE FROM tbl_discount_master WHERE id='$id'";
   $res=mysqli_query($conn,$sqld);
   if($res)
   {
    header('refresh:.5; url=fh_discountmastercreation_list.php');
   }else{
    echo "<script>alert('Discount Master Record Deletion Failed');</script>";
   }
}
?>

<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <title>Discount Master List || TEJASERP</title>
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
                                    <?php
                                       $init_page = 1;
                                       $init_perpage = isset($_SESSION["_RECORDPERPAGE_"]) && $_SESSION["_RECORDPERPAGE_"] ? (int)$_SESSION["_RECORDPERPAGE_"] : 20;
                                       $init_offset = ($init_page - 1) * $init_perpage;
                                       $query = mysqli_query($conn, "SELECT * FROM tbl_discount_master ORDER BY id DESC LIMIT $init_offset, $init_perpage"); 
                                       $countQuery = mysqli_query($conn, "SELECT COUNT(*) as total FROM tbl_discount_master");
                                       $countRow = mysqli_fetch_assoc($countQuery);
                                       $totalRecords = $countRow['total'] ?? 0;
                                       $discount_list = array();
                                       while ($d = mysqli_fetch_array($query)) {
                                          $discount_list[] = $d;
                                       }
                                       $i = $init_offset + 1;
                                       foreach ($discount_list as $user) {
                                          $statusBadge = ($user['status'] ?? 'Active') === 'Active' 
                                              ? '<span class="label label-success badge-status-sm">Active</span>' 
                                              : '<span class="label label-danger badge-status-sm">Inactive</span>';
                                      ?>
                                    <tr>
                                       <td class="td-center-middle"><?php echo $i++; ?></td>
                                       <td class="td-center-middle-nowrap">
                                          <button type="button" class="btn btn-primary btn-xs view-discount-btn" data-toggle="modal" data-target="#viewDiscountModal" 
                                              data-id="<?php echo $user['id']; ?>"
                                              data-discode="<?php echo htmlspecialchars($user['dis_code'] ?? ''); ?>"
                                              data-type="<?php echo htmlspecialchars($user['distype_name'] ?? ''); ?>"
                                              data-group="<?php echo htmlspecialchars($user['group_name'] ?? ''); ?>"
                                              data-brand="<?php echo htmlspecialchars($user['brand_name'] ?? ''); ?>"
                                              data-barcode="<?php echo htmlspecialchars($user['barcode_no'] ?? ''); ?>"
                                              data-item="<?php echo htmlspecialchars($user['item_name'] ?? ''); ?>"
                                              data-store="<?php echo htmlspecialchars($user['store_name'] ?? ''); ?>"
                                              data-location="<?php echo htmlspecialchars($user['location_name'] ?? ''); ?>"
                                              data-per="<?php echo htmlspecialchars($user['discount_per'] ?? ''); ?>"
                                              data-amt="<?php echo htmlspecialchars($user['discount_amt'] ?? ''); ?>"
                                              data-mrp="<?php echo htmlspecialchars($user['mrp'] ?? ''); ?>"
                                              data-sp="<?php echo htmlspecialchars($user['sp'] ?? ''); ?>"
                                              data-status="<?php echo htmlspecialchars($user['status'] ?? 'Active'); ?>">
                                              <i class="fa fa-eye"></i> View
                                          </button>
                                          <a class="btn btn-info btn-xs" data-toggle="tooltip" title="Edit Discount" href="fh_discountmastercreation.php?update_id=<?php echo $user['id']; ?>">
                                             <i class="fa fa-pencil"></i> Edit
                                          </a>
                                          <a class="btn btn-danger btn-xs" data-toggle="tooltip" title="Delete Discount" href="fh_discountmastercreation_list.php?delete_id=<?php echo $user['id']; ?>" onclick="return confirm('Are you sure you want to delete this record?');">
                                             <i class="fa fa-trash-o"></i> Delete
                                          </a>
                                       </td>
                                       <td class="td-center-middle"><?php echo $statusBadge; ?></td>
                                       <td><?php echo htmlspecialchars($user['dis_code'] ?? ''); ?></td>
                                       <td><?php echo htmlspecialchars($user['distype_name'] ?? ''); ?></td>
                                       <td><?php echo htmlspecialchars($user['group_name'] ?? ''); ?></td>
                                       <td><?php echo htmlspecialchars($user['brand_name'] ?? ''); ?></td>
                                       <td><?php echo htmlspecialchars($user['barcode_no'] ?? ''); ?></td>
                                       <td><?php echo htmlspecialchars($user['item_name'] ?? ''); ?></td>
                                       <td><?php echo htmlspecialchars($user['store_name'] ?? ''); ?></td>
                                       <td><?php echo htmlspecialchars($user['location_name'] ?? ''); ?></td>
                                       <td><?php echo htmlspecialchars($user['discount_per'] ?? ''); ?></td>
                                       <td><?php echo htmlspecialchars($user['discount_amt'] ?? ''); ?></td>
                                       <td><?php echo htmlspecialchars($user['mrp'] ?? ''); ?></td>
                                       <td><?php echo htmlspecialchars($user['sp'] ?? ''); ?></td>
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
                                 <div id="discountmaster-pagination">
                                    <?php
                                       $totalPages = ceil($totalRecords / $init_perpage);
                                       if ($totalPages > 1) {
                                          echo '<ul class="pagination pagination-sm pagination-no-margin pull-right">';
                                          echo '<li class="disabled"><span>&laquo; Prev</span></li>';
                                          for ($p = 1; $p <= min(5, $totalPages); $p++) {
                                             if ($p == 1) {
                                                echo '<li class="active"><span>' . $p . '</span></li>';
                                             } else {
                                                echo '<li><a href="javascript:void(0)" onclick="loadDiscountMasterData(' . $p . ')">' . $p . '</a></li>';
                                             }
                                          }
                                          if ($totalPages > 5) {
                                             echo '<li class="disabled"><span>...</span></li>';
                                             echo '<li><a href="javascript:void(0)" onclick="loadDiscountMasterData(' . $totalPages . ')">' . $totalPages . '</a></li>';
                                          }
                                          echo '<li><a href="javascript:void(0)" onclick="loadDiscountMasterData(' . ($totalPages > 1 ? 2 : 1) . ')">Next &raquo;</a></li>';
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
