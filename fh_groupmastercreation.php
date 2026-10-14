<?php include('top.php');?>
<?php
   $msg = '';
   $is_edit = false;
   $edit_id = '';
   $edit_row = array(
      'group_code' => '',
      'group_name' => '',
      'hsn_code' => '',
      'status' => 'true',
      'picture' => ''
   );

   $targetDir = "uploads/group-master/";
   if (!file_exists($targetDir)) {
      mkdir($targetDir, 0777, true);
   }

   function uploadGroupFile($file, $targetDir) {
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
      $run = mysqli_query($conn, "SELECT * FROM tbl_group_master WHERE id='$edit_id'");
      if ($run && mysqli_num_rows($run) > 0) {
         $edit_row = mysqli_fetch_assoc($run);
      }
   }

   // Update Group Record
   if (isset($_POST['update']) && !empty($edit_id)) {
      $group_code = $_POST['group_code'];
      $group_name = $_POST['group_name'];
      $hsn_code = $_POST['hsn_code'];
      $status = $_POST['status'];

      $group_name = str_replace("'", "\'", $group_name);
      $group_name = str_replace('"', '\"', $group_name);

      $new_picture = uploadGroupFile($_FILES['picture'] ?? null, $targetDir);
      $picture = !empty($new_picture) ? $new_picture : ($edit_row['picture'] ?? '');

      $query = "UPDATE tbl_group_master SET group_code='$group_code', group_name='$group_name', hsn_code='$hsn_code', status='$status', picture='$picture' WHERE id='$edit_id'";
      $run = mysqli_query($conn, $query);
      if ($run) {
         echo "
         <script>
         document.addEventListener('DOMContentLoaded', function() {
             showPopup(
                 'Success!',
                 'Group Name Updated Successfully 🎉'
             );
             setTimeout(function(){ window.location.href = 'fh_groupmastercreation.php'; }, 1500);
         });
         </script>
         ";
      } else {
         $msg = '<div class="alert alert-danger alert-dismissible fade1 show">
             <button type="button" class="close" data-dismiss="alert">&times;</button>
             <strong>Group Master Update Failed!</strong>
         </div>';
      }
   }

   // Create Group Record
   if (isset($_POST['submit'])) {
      $group_code = $_POST['group_code'];
      $group_name = $_POST['group_name'];
      $hsn_code = $_POST['hsn_code'];
      $status = $_POST['status'];

      $dup = mysqli_query($conn, "SELECT * FROM tbl_group_master WHERE group_name='$group_name'");
      if (mysqli_num_rows($dup) > 0) {
          $msg = '<div class="alert alert-danger alert-dismissible fade1 show">
              <button type="button" class="close" data-dismiss="alert">&times;</button>
              <strong>Group Master Already exist!</strong>
          </div>';
      } else {
          $new_picture = uploadGroupFile($_FILES['picture'] ?? null, $targetDir);
          $query = "INSERT INTO tbl_group_master(group_code,group_name,hsn_code,status,picture) VALUES('$group_code','$group_name','$hsn_code','$status','$new_picture')";
          $run = mysqli_query($conn, $query);
          if ($run) {
              echo "
              <script>
              document.addEventListener('DOMContentLoaded', function() {
                  showPopup(
                      'Success!',
                      'Group Name Saved Successfully 🎉'
                  );
                  setTimeout(function(){ window.location.href = 'fh_groupmastercreation.php'; }, 1500);
              });
              </script>
              ";
          } else {
              $msg = '<div class="alert alert-danger alert-dismissible fade1 show">
                  <button type="button" class="close" data-dismiss="alert">&times;</button>
                  <strong>Group Creation Failed!</strong>
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
      <title>Group Master <?php echo $is_edit ? 'Edit' : 'Creation'; ?> || TEJASERP</title>
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
            
            <!-- Main content - Creation/Update Form -->
            <section class="">
               <div class="row">
                  <div class="col-sm-12">
                     <div class="panel panel-bd lobidisable">
                        <div class="panel-heading">
                           <span class="fs-15 panel-header-text">
                              <i class="fa fa-pencil mr-5"></i>
                              <span><?php echo $is_edit ? 'Group Master Edit !' : 'Group Master Creations !'; ?></span>
                           </span>
                        </div>

                        <div class="panel-body form-scroll">
                           <div class="btn-group">
                              <a href="fh_groupmastercreation_list.php">
                                 <button type="button" class="button-btn-btn btn-exp btn-sm"><i class="fa fa-bars mr-5"></i>Group Master List</button>
                              </a>
                              <?php if ($is_edit) { ?>
                              <a href="fh_groupmastercreation.php" class="ml-5">
                                 <button type="button" class="btn btn-primary btn-sm"><i class="fa fa-plus mr-5"></i>Add New Group</button>
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
                                       <label>Group Code</label>
                                       <input type="text" name="group_code" class="form-control" id="group_code" readonly value="<?php 
                                          if ($is_edit) {
                                             echo htmlspecialchars($edit_row['group_code'] ?? '');
                                          } else {
                                             @$no = mysqli_query($conn, "SELECT id FROM tbl_group_master ORDER BY id DESC");
                                             $po_no = mysqli_fetch_assoc($no);
                                             echo 'GC-000'.(($po_no['id'] ?? 0)+1);
                                          }
                                       ?>">
                                    </div>
                                 </div>
                                 <div class="col-sm-4">
                                    <div class="form-group">
                                       <label>Group Name</label><label class="text-required">*</label>
                                       <input type="text" class="form-control" name="group_name" id="group_name" value="<?php echo htmlspecialchars($edit_row['group_name'] ?? ''); ?>" placeholder="Enter Your Group Name" required>
                                    </div>
                                 </div>
                                 <div class="col-sm-4">
                                    <div class="form-group">
                                       <label>HSN Code</label><label class="text-required">*</label>
                                       <input type="text" class="form-control" name="hsn_code" id="hsn_code" value="<?php echo htmlspecialchars($edit_row['hsn_code'] ?? ''); ?>" placeholder="Enter Your HSN Code" required>
                                    </div>
                                 </div>
                              </div>
                              <div class="row">
                                 <div class="col-sm-4">
                                    <div class="form-group">
                                       <label>Group Image</label>
                                       <input type="file" name="picture" id="picture" class="form-control" accept="image/*">
                                       <?php if ($is_edit && !empty($edit_row['picture']) && file_exists("uploads/group-master/" . $edit_row['picture'])) { ?>
                                          <div class="mt-5">
                                             <img src="uploads/group-master/<?php echo htmlspecialchars($edit_row['picture']); ?>" alt="Group Image" class="avatar-img-table" style="width: 48px; height: 48px; object-fit: cover; border-radius: 4px; border: 1px solid #ccc; vertical-align: middle;">
                                             <span class="text-muted fs-11 ml-5"><?php echo htmlspecialchars($edit_row['picture']); ?></span>
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
                                       <a href="fh_groupmastercreation.php" class="btn btn-warning btn-w-100">Cancel</a>
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
            
            <!-- Main content - Group Master List -->
            <section class="">
               <div class="row">
                  <div class="col-sm-12">
                     <div class="panel panel-bd lobidisable">
                        <div class="panel-heading">
                           <span class="fs-15 panel-header-text">
                              <i class="fa fa-th-list mr-5"></i>
                              <span>Group Master List !</span>
                           </span>
                        </div>
                        <div class="panel-body form-scroll">
                           <!-- Toolbar & Search Box Row under Header Panel -->
                           <div class="row m-b-15">
                              <div class="col-sm-6 col-xs-12 m-b-5">
                                 <div class="btn-group">
                                    <a href="fh_groupmastercreation.php"> <button type="button" class="button-btn-btn btn-exp btn-sm"><i class="fa fa-plus mr-5"></i>Add New Group Master Creation</button></a>
                                 </div>
                                 <?php include('include/button-export-to-data.php');?>
                              </div>
                              <div class="col-sm-6 col-xs-12">
                                 <!-- Search Form Box under Header Panel -->
                                 <form id="groupMasterSearchForm" class="search-form-wrap pull-right" onsubmit="return false;" style="width: 100%; max-width: 360px;">
                                    <input type="hidden" name="page" id="page" value="1">
                                    <div class="input-group search-box-group">
                                       <input type="text" class="form-control search-input search-input-field" name="search" id="company_name" placeholder="Search Groups..." autocomplete="off">
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
                                       <th>Group Code</th>
                                       <th>Group Name</th>
                                       <th>HSN CODE</th>
                                    </tr>
                                 </thead>
                                 <tbody id="groupmaster-list-results">
                                    <?php
                                       $init_page = 1;
                                       $init_perpage = isset($_SESSION["_RECORDPERPAGE_"]) && $_SESSION["_RECORDPERPAGE_"] ? (int)$_SESSION["_RECORDPERPAGE_"] : 20;
                                       $init_offset = ($init_page - 1) * $init_perpage;
                                       $query = mysqli_query($conn, "SELECT * FROM tbl_group_master ORDER BY id DESC LIMIT $init_offset, $init_perpage"); 
                                       $countQuery = mysqli_query($conn, "SELECT COUNT(*) as total FROM tbl_group_master");
                                       $countRow = mysqli_fetch_assoc($countQuery);
                                       $totalRecords = $countRow['total'] ?? 0;
                                       $group_list = array();
                                       while ($s = mysqli_fetch_array($query)) {
                                          $group_list[] = $s;
                                       }
                                       $i = $init_offset + 1;
                                       foreach ($group_list as $row) {
                                      ?>
                                    <tr>
                                       <td class="td-center-middle"><?php echo $i++; ?></td>
                                       <td class="td-center-middle-nowrap">
                                          <a class="btn btn-info btn-xs" data-toggle="tooltip" title="Edit Group" href="fh_groupmastercreation.php?update_id=<?php echo $row['id']; ?>">
                                             <i class="fa fa-pencil"></i> Edit
                                          </a>
                                          <button type="button" class="btn btn-warning btn-xs" data-toggle="modal" data-target="#viewGroupModal<?php echo $row['id']; ?>" title="View Details">
                                             <i class="fa fa-eye"></i> Details
                                          </button>
                                          <a class="btn btn-danger btn-xs" data-toggle="tooltip" title="Delete Group" onClick="return confirm('Are you sure you want to Delete this Group?')" href="fh_groupmastercreation_list.php?delete_id=<?php echo $row['id']; ?>">
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
                                          <?php if (!empty($row['picture']) && file_exists('uploads/group-master/' . $row['picture'])) { ?>
                                             <img src="uploads/group-master/<?php echo htmlspecialchars($row['picture']); ?>" class="img-circle avatar-img-table" alt="Group Image" width="40" height="40">
                                          <?php } else { ?>
                                             <div class="avatar-fallback-table">
                                                <i class="fa fa-folder"></i>
                                             </div>
                                          <?php } ?>
                                       </td>
                                       <td class="td-middle"><code><?php echo htmlspecialchars($row['group_code'] ?? ''); ?></code></td>
                                       <td class="td-middle-bold"><?php echo htmlspecialchars(substr($row['group_name'] ?? '', 0, 50)); ?></td>
                                       <td class="td-middle"><?php echo htmlspecialchars(substr($row['hsn_code'] ?? '', 0, 50)); ?></td>
                                    </tr>
                                    <?php } ?>
                                 </tbody>
                              </table>
                           </div>

                           <!-- Modals Container for Group Details -->
                           <div id="group-modals-container">
                              <?php foreach ($group_list as $row) { ?>
                              <div class="modal fade" id="viewGroupModal<?php echo $row['id']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                 <div class="modal-dialog modal-md">
                                    <div class="modal-content modal-content-rounded">
                                       <div class="modal-header modal-header-teal">
                                          <button type="button" class="close modal-close-white" data-dismiss="modal" aria-hidden="true">×</button>
                                          <h4 class="modal-title modal-title-bold">
                                             <i class="fa fa-folder-open mr-8"></i>Group Details - <?php echo htmlspecialchars($row['group_name'] ?? ''); ?>
                                          </h4>
                                       </div>
                                       <div class="modal-body modal-body-padded">
                                          <?php if (!empty($row['picture']) && file_exists('uploads/group-master/' . $row['picture'])) { ?>
                                             <div class="text-center mb-15">
                                                <img src="uploads/group-master/<?php echo htmlspecialchars($row['picture']); ?>" class="avatar-img-modal" alt="Group Image" style="max-height: 120px; border-radius: 8px; border: 1px solid #ddd; padding: 4px;">
                                             </div>
                                          <?php } ?>
                                          <table class="table table-bordered table-striped mb-0">
                                             <tbody>
                                                <tr>
                                                   <th class="th-w-35">Group Code</th>
                                                   <td><code><?php echo htmlspecialchars($row['group_code'] ?? ''); ?></code></td>
                                                </tr>
                                                <tr>
                                                   <th>Group Name</th>
                                                   <td><strong><?php echo htmlspecialchars($row['group_name'] ?? ''); ?></strong></td>
                                                </tr>
                                                <tr>
                                                   <th>HSN Code</th>
                                                   <td><?php echo htmlspecialchars($row['hsn_code'] ?? ''); ?></td>
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
                                          <a href="fh_groupmastercreation.php?update_id=<?php echo $row['id']; ?>" class="btn btn-info btn-sm">
                                             <i class="fa fa-pencil"></i> Edit Group
                                          </a>
                                          <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                              <?php } ?>
                           </div>

                           <div class="row pagination-wrapper-row">
                              <div class="col-sm-6">
                                 <div class="pagination-info-text">
                                    Total Records: <strong id="total-records-count"><?php echo $totalRecords; ?></strong>
                                 </div>
                              </div>
                              <div class="col-sm-6">
                                 <div id="groupmaster-pagination">
                                    <?php
                                       $totalPages = ceil($totalRecords / $init_perpage);
                                       if ($totalPages > 1) {
                                          echo '<ul class="pagination pagination-sm pagination-no-margin pull-right">';
                                          echo '<li class="disabled"><span>&laquo; Prev</span></li>';
                                          for ($p = 1; $p <= min(5, $totalPages); $p++) {
                                             if ($p == 1) {
                                                echo '<li class="active"><span>' . $p . '</span></li>';
                                             } else {
                                                echo '<li><a href="javascript:void(0)" onclick="loadGroupMasterData(' . $p . ')">' . $p . '</a></li>';
                                             }
                                          }
                                          if ($totalPages > 5) {
                                             echo '<li class="disabled"><span>...</span></li>';
                                             echo '<li><a href="javascript:void(0)" onclick="loadGroupMasterData(' . $totalPages . ')">' . $totalPages . '</a></li>';
                                          }
                                          echo '<li><a href="javascript:void(0)" onclick="loadGroupMasterData(2)">Next &raquo;</a></li>';
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
      <script src="assets/dist/js/page/groupmaster-list.js"></script>
   </body>
</html>