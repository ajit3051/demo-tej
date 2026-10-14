<?php include('top.php');?>
<?php

if(isset($_GET['delete_id']))
{
   $id=$_GET['delete_id'];
   $sqld="DELETE FROM tbl_salesman_target_master WHERE id='$id'";
   $res=mysqli_query($conn,$sqld);
   if($res)
   {
    header('refresh:.5; url=fh_salesman_target_mastercreation_list.php');
   }else{
    echo "<script>alert('Salesman Target Master Record Deletion Failed');</script>";
   }
}
?>

<!DOCTYPE html>
<html lang="en">
   
   <head>
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <title>Salesman Target Master Creation || TEJASERP</title>
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
                              <span>Salesman Target Master List !</span>
                           </span>
                        </div>
                        <div class="panel-body form-scroll">
                           <div class="row m-b-15">
                              <div class="col-sm-6 pull-right">
                                 <form id="salesmanTargetMasterSearchForm" class="search-form-wrap" onsubmit="return false;">
                                    <input type="hidden" name="page" id="page" value="1">
                                    <div class="input-group search-box-group">
                                       <input type="text" class="form-control search-input search-input-field" name="search" id="company_name" placeholder="Search Salesman Target..." autocomplete="off">
                                       <span class="input-group-addon search-addon-btn">
                                          <i class="fa fa-search search-icon-sm"></i>
                                       </span>
                                    </div>
                                 </form>
                              </div>
                           </div>
                           <div class="mb-10">
                              <div class="btn-group">
                                 <a href="fh_salesman_target_mastercreation.php"> <button type="button" class="button-btn-btn btn-exp btn-sm"><i class="fa fa-plus mr-5"></i>Add New Salesman Target Master Creation</button></a>
                              </div>
                              <?php include('include/button-export-to-data.php');?>
                           </div>
                           <div class="table-responsive">
                              <table id="dataTableExample1" class="table table-bordered table-striped table-hover">
                                 <thead>
                                    <tr class="info">
                                       <th class="th-width-40-center">#</th>
                                       <th class="th-minwidth-100-center">Action</th>
                                       <th class="text-center-align">Status</th>
                                       <th>Salesman Code</th>
                                       <th>Salesman Name</th>
                                       <th>Mobile No</th>
                                       <th>Store Name</th>
                                       <th>Location Name</th>
                                       <th>Target Amt</th>
                                       <th>Start Date</th>
                                       <th>End Date</th>
                                       <th>Amount Per</th>
                                       <th>Amount</th>
                                    </tr>
                                 </thead>
                                 <tbody id="salesmantargetmaster-list-results">
                                    <?php
                                       $init_page = 1;
                                       $init_perpage = isset($_SESSION["_RECORDPERPAGE_"]) && $_SESSION["_RECORDPERPAGE_"] ? (int)$_SESSION["_RECORDPERPAGE_"] : 20;
                                       $init_offset = ($init_page - 1) * $init_perpage;
                                       $query = mysqli_query($conn, "SELECT * FROM tbl_salesman_target_master ORDER BY id DESC LIMIT $init_offset, $init_perpage"); 
                                       $countQuery = mysqli_query($conn, "SELECT COUNT(*) as total FROM tbl_salesman_target_master");
                                       $countRow = mysqli_fetch_assoc($countQuery);
                                       $totalRecords = $countRow['total'] ?? 0;
                                       $target_list = array();
                                       while ($t = mysqli_fetch_array($query)) {
                                          $target_list[] = $t;
                                       }
                                       $i = $init_offset + 1;
                                       foreach ($target_list as $user) {
                                          $statusBadge = ($user['status'] ?? 'Active') === 'Active' 
                                              ? '<span class="label label-success badge-status-sm">Active</span>' 
                                              : '<span class="label label-danger badge-status-sm">Inactive</span>';
                                      ?>
                                    <tr>
                                       <td class="td-center-middle"><?php echo $i++; ?></td>
                                       <td class="td-center-middle-nowrap">
                                          <a class="btn btn-info btn-xs" data-toggle="tooltip" title="Edit Salesman Target" href="fh_salesman_target_mastercreation.php?update_id=<?php echo $user['id']; ?>">
                                             <i class="fa fa-pencil"></i> Edit
                                          </a>
                                          <a class="btn btn-danger btn-xs" data-toggle="tooltip" title="Delete Salesman Target" href="fh_salesman_target_mastercreation_list.php?delete_id=<?php echo $user['id']; ?>" onclick="return confirm('Are you sure you want to delete this record?');">
                                             <i class="fa fa-trash-o"></i> Delete
                                          </a>
                                       </td>
                                       <td class="td-center-middle"><?php echo $statusBadge; ?></td>
                                       <td><?php echo htmlspecialchars($user['salesman_code'] ?? ''); ?></td>
                                       <td><?php echo htmlspecialchars($user['salesman_name'] ?? ''); ?></td>
                                       <td><?php echo htmlspecialchars($user['mobile_no'] ?? ''); ?></td>
                                       <td><?php echo htmlspecialchars($user['store_name'] ?? ''); ?></td>
                                       <td><?php echo htmlspecialchars($user['location_name'] ?? ''); ?></td>
                                       <td><?php echo htmlspecialchars($user['target_amt'] ?? ''); ?></td>
                                       <td><?php echo htmlspecialchars($user['start_date'] ?? ''); ?></td>
                                       <td><?php echo htmlspecialchars($user['end_date'] ?? ''); ?></td>
                                       <td><?php echo htmlspecialchars($user['amount_per'] ?? ''); ?></td>
                                       <td><?php echo htmlspecialchars($user['amount'] ?? ''); ?></td>
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
                                 <div id="salesmantargetmaster-pagination">
                                    <?php
                                       $totalPages = ceil($totalRecords / $init_perpage);
                                       if ($totalPages > 1) {
                                          echo '<ul class="pagination pagination-sm pagination-no-margin pull-right">';
                                          echo '<li class="disabled"><span>&laquo; Prev</span></li>';
                                          for ($p = 1; $p <= min(5, $totalPages); $p++) {
                                             if ($p == 1) {
                                                echo '<li class="active"><span>' . $p . '</span></li>';
                                             } else {
                                                echo '<li><a href="javascript:void(0)" onclick="loadSalesmanTargetMasterData(' . $p . ')">' . $p . '</a></li>';
                                             }
                                          }
                                          if ($totalPages > 5) {
                                             echo '<li class="disabled"><span>...</span></li>';
                                             echo '<li><a href="javascript:void(0)" onclick="loadSalesmanTargetMasterData(' . $totalPages . ')">' . $totalPages . '</a></li>';
                                          }
                                          echo '<li><a href="javascript:void(0)" onclick="loadSalesmanTargetMasterData(2)">Next &raquo;</a></li>';
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
         <?php include('include/footer.php');?>
      </div>
      <!-- ./wrapper -->
      <?php include('include/js.php');?>
      <script src="assets/dist/js/page/salesmantargetmaster-list.js"></script>
   </body>
</html>
