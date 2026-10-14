<?php include('top.php');?>
<?php
   $msg = '';
   $update_mode = false;
   $update_id = 0;
   $edit_user = [];

   if (isset($_GET['update_id'])) {
       $update_id = intval($_GET['update_id']);
       $fetch_user = mysqli_query($conn, "SELECT * FROM tbl_user_master WHERE id = '$update_id'");
       if ($fetch_user && mysqli_num_rows($fetch_user) > 0) {
           $edit_user = mysqli_fetch_assoc($fetch_user);
           $update_mode = true;
       }
   }

   if (isset($_POST['submit']) || isset($_POST['update'])) {
       $update_id        = isset($_POST['update_id']) ? intval($_POST['update_id']) : $update_id;
       $user_code        = mysqli_real_escape_string($conn, $_POST['user_code'] ?? '');
       $user_name        = mysqli_real_escape_string($conn, $_POST['user_name'] ?? '');
       $mobile_no        = mysqli_real_escape_string($conn, $_POST['mobile_no'] ?? '');
       $store_name       = mysqli_real_escape_string($conn, $_POST['store_name'] ?? '');
       $location         = mysqli_real_escape_string($conn, $_POST['location'] ?? '');
       $user_type        = mysqli_real_escape_string($conn, $_POST['user_type'] ?? '');
       $password         = $_POST['password'] ?? '';
       $confirm_password = $_POST['confirm_password'] ?? '';
       $current_datetime = date('Y-m-d H:i:s');
       $gender           = mysqli_real_escape_string($conn, $_POST['gender'] ?? '1');
       $status           = mysqli_real_escape_string($conn, $_POST['status'] ?? 'true');

       $is_updating = ($update_mode || $update_id > 0);

       if (!$is_updating && ($password === '' || $password !== $confirm_password)) {
           $msg = '<div class="alert alert-danger alert-dismissible fade1 show">
               <button type="button" class="close" data-dismiss="alert">&times;</button>
               <strong>Password and Confirm Password do not match!</strong>
               </div>';
       } elseif ($is_updating && !empty($password) && $password !== $confirm_password) {
           $msg = '<div class="alert alert-danger alert-dismissible fade1 show">
               <button type="button" class="close" data-dismiss="alert">&times;</button>
               <strong>Password and Confirm Password do not match!</strong>
               </div>';
       } else {
           $path = "uploads/item-master/";
           if (!is_dir($path)) {
               mkdir($path, 0777, true);
           }

           if (!function_exists('uploadFile')) {
               function uploadFile($file, $path) {
                   if (!empty($file['name']) && $file['error'] === 0) {
                       $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                       $allowed = ['jpg','jpeg','png','webp'];
                       if (!in_array($ext, $allowed)) {
                           return "";
                       }
                       $filename = time() . "_" . basename($file['name']);
                       move_uploaded_file($file['tmp_name'], $path . $filename);
                       return $filename;
                   }
                   return "";
               }
           }

           @$new_picture = uploadFile($_FILES['picture'], $path);

           if ($is_updating) {
               // UPDATE MODE
               $picture = !empty($new_picture) ? $new_picture : ($edit_user['picture'] ?? '');

               if (!empty($password)) {
                   $hashed_password = password_hash($password, PASSWORD_DEFAULT);
               } else {
                   $hashed_password = $edit_user['password'] ?? '';
               }

               $dup = mysqli_query($conn, "SELECT id FROM tbl_user_master WHERE user_name='$user_name' AND id != '$update_id'");
               if (mysqli_num_rows($dup) > 0) {
                   $msg = '<div class="alert alert-danger alert-dismissible fade1 show">
                       <button type="button" class="close" data-dismiss="alert">&times;</button>
                       <strong>User Name Already exist!</strong>
                       </div>';
               } else {
                   $query = "UPDATE tbl_user_master SET 
                       user_code='$user_code',
                       user_name='$user_name',
                       mobile_no='$mobile_no',
                       store_name='$store_name',
                       location='$location',
                       user_type='$user_type',
                       password='$hashed_password',
                       updated_date='$current_datetime',
                       picture='$picture',
                       gender='$gender',
                       status='$status' 
                       WHERE id='$update_id'";

                   $run = mysqli_query($conn, $query);
                   if ($run) {
                       echo "
                       <script>
                       document.addEventListener('DOMContentLoaded', function() {
                           showPopup(
                               'Success!',
                               'User Master Updated Successfully 🎉'
                           );
                           setTimeout(function(){
                               window.location.href = 'fh_usermastercreation.php';
                           }, 1500);
                       });
                       </script>
                       ";
                   } else {
                       $msg = '<div class="alert alert-danger alert-dismissible fade1 show">
                           <button type="button" class="close" data-dismiss="alert">&times;</button>
                           <strong>Update Failed!</strong>
                           </div>';
                   }
               }
           } else {
               // CREATE MODE
               $picture = $new_picture;
               $hashed_password = password_hash($password, PASSWORD_DEFAULT);

               $dup = mysqli_query($conn, "SELECT id FROM tbl_user_master WHERE user_name='$user_name'");
               if (mysqli_num_rows($dup) > 0) {
                   $msg = '<div class="alert alert-danger alert-dismissible fade1 show">
                       <button type="button" class="close" data-dismiss="alert">&times;</button>
                       <strong>User Name Already exist!</strong>
                       </div>';
               } else {
                    $query = "INSERT INTO tbl_user_master
                    (user_code,user_name,mobile_no,store_name,location,user_type,password,
                     created_date,updated_date,picture,gender,status)
                    VALUES
                    ('$user_code','$user_name','$mobile_no','$store_name','$location','$user_type',
                     '$hashed_password','$current_datetime','$current_datetime',
                     '$picture','$gender','$status')";

                   $run = mysqli_query($conn, $query);
                   if ($run) {
                       echo "
                       <script>
                       document.addEventListener('DOMContentLoaded', function() {
                           showPopup(
                               'Success!',
                               'User Name Saved Successfully 🎉'
                           );
                       });
                       </script>
                       ";
                   }
               }
           }
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
      <title>User Master Creations || TEJASERP</title>
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
            <!-- Main content -->
            <?php include('include/fh-popup-message-successfully.php');?>
            <?php include('include/fh-form-scrolling-data-list.php');?>
            <section class="">
               <div class="row">
                  <div class="col-sm-12">
                     <div class="panel panel-bd lobidisable">
                        <div class="panel-heading">
                           <span class="fs-15 panel-header-text">
                              <i class="fa fa-pencil mr-5"></i>
                              <span><?php echo $update_mode ? 'User Master Edit !' : 'User Master Creations !'; ?></span>
                           </span>
                        </div>
                        <div class="panel-body form-scroll">
                           <div class="btn-group">
                              <a href="fh_usermastercreation_list.php"> <button class="button-btn-btn btn-exp btn-sm"><i class="fa fa-bars mr-5"></i>User Master List</button></a>
                              <?php if ($update_mode) { ?>
                              <a href="fh_usermastercreation.php"> <button class="button-btn-btn btn-exp btn-sm ml-5"><i class="fa fa-plus mr-5"></i>Add New User</button></a>
                              <?php } ?>
                           </div>
                           <form method="post" enctype="multipart/form-data">
                              <?php echo $msg; ?>
                              <div class="row">
                                 <div class="col-sm-4">
                                    <div class="form-group">
                                       <label>User Code</label>
                                       <input type="text" name="user_code" class="form-control" id="user_code" readonly value="<?php 
                                          if ($update_mode) {
                                              echo htmlspecialchars($edit_user['user_code'] ?? '');
                                          } else {
                                              @$no = mysqli_query($conn, "SELECT id FROM tbl_user_master ORDER BY id DESC");
                                              $po_no = mysqli_fetch_assoc($no);
                                              echo 'UC-000'.(($po_no['id'] ?? 0)+1);
                                          }
                                       ?>">
                                    </div>
                                 </div>
                                 <div class="col-sm-4">
                                    <div class="form-group">
                                       <label>User Name</label><label class="text-required">*</label>
                                       <input type="text" class="form-control" name="user_name" id="user_name" placeholder="Enter Your Name" required value="<?php echo $update_mode ? htmlspecialchars($edit_user['user_name'] ?? '') : ''; ?>">
                                    </div>
                                 </div>
                                 <div class="col-sm-4">
                                    <div class="form-group">
                                       <label>Mobile No</label><label class="text-required">*</label>
                                       <input type="text" class="form-control" name="mobile_no" id="mobile_no" placeholder="Enter Your Mobile No" required value="<?php echo $update_mode ? htmlspecialchars($edit_user['mobile_no'] ?? '') : ''; ?>">
                                    </div>
                                 </div>
                              </div>
                              <div class="row">
                                 <div class="col-sm-4">
                                    <div class="form-group">
                                       <label>Store Name</label><label class="text-required">*</label>
                                       <select name="store_name" class="form-control" required>
                                          <option value="null">Select Store Name </option>
                                          <?php
                                             $query = mysqli_query($conn, "SELECT * FROM tbl_store_master");
                                             while ($row = mysqli_fetch_array($query)) {
                                                $selected = ($update_mode && ($edit_user['store_name'] ?? '') == $row['store_name']) ? 'selected' : '';
                                                echo "<option value='{$row['store_name']}' {$selected}>{$row['store_name']}</option>";
                                             } 
                                          ?>
                                       </select>
                                    </div>
                                    <a href="#" class="" data-toggle="modal" data-target="#gst">
                                    <span class="fs-11 pull-left"><i class="fa fa-plus"></i>Add New Store Master</span>
                                    </a>
                                 </div>
                                 <div class="col-sm-4">
                                    <div class="form-group">
                                       <label>Location</label><label class="text-required">*</label>
                                       <input type="text" class="form-control" name="location" id="location" placeholder="Enter Your Location" required value="<?php echo $update_mode ? htmlspecialchars($edit_user['location'] ?? '') : ''; ?>">
                                    </div>
                                 </div>
                                 <div class="col-sm-4">
                                    <div class="form-group">
                                       <label>User type</label><label class="text-required">*</label>
                                       <select name="user_type" class="form-control" required>
                                          <option value="null">Select User Type</option>
                                          <?php
                                             $user_types = ['Super Admin', 'Admin', 'Manager', 'Power User', 'User'];
                                             foreach ($user_types as $utype) {
                                                $selected = ($update_mode && ($edit_user['user_type'] ?? '') == $utype) ? 'selected' : '';
                                                echo "<option value='{$utype}' {$selected}>{$utype}</option>";
                                             }
                                          ?>
                                       </select>
                                    </div>
                                 </div>
                              </div>
                              <div class="row">
                              </div>
                              <div class="row">
                                 <div class="col-sm-4">
                                    <div class="form-group">
                                       <label>Password</label><?php if (!$update_mode) { ?><label class="text-required">*</label><?php } ?>
                                       <input type="text" class="form-control" name="password" id="password" placeholder="Enter New Password (Optional)" <?php echo $update_mode ? '' : 'required'; ?> value="">
                                    </div>
                                 </div>
                                 <div class="col-sm-4">
                                    <div class="form-group">
                                       <label>Confirm Password</label><?php if (!$update_mode) { ?><label class="text-required">*</label><?php } ?>
                                       <input type="text" class="form-control" name="confirm_password" id="confirm_password" placeholder="Confirm New Password" <?php echo $update_mode ? '' : 'required'; ?> value="">
                                    </div>
                                 </div>
                              </div>
                              <br>
                              <div class="row">
                                 <div class="col-sm-4">
                                    <div class="form-group">
                                       <label>Picture upload</label>
                                       <?php if ($update_mode && !empty($edit_user['picture'])) { ?>
                                          <br><img src="uploads/item-master/<?php echo htmlspecialchars($edit_user['picture']); ?>" width="60" class="edit-thumb-img">
                                       <?php } ?>
                                       <input type="file" name="picture" id="picture">
                                    </div>
                                 </div>
                                 <div class="col-sm-4">
                                    <div class="form-group">
                                       <label>Gender</label><br>
                                       <label class="radio-inline"><input name="gender" value="1" <?php if (!$update_mode || ($edit_user['gender'] ?? '1') == '1') echo 'checked="checked"'; ?> type="radio">Male</label> 
                                       <label class="radio-inline"><input name="gender" value="0" <?php if ($update_mode && ($edit_user['gender'] ?? '') == '0') echo 'checked="checked"'; ?> type="radio">Female</label>
                                    </div>
                                 </div>
                                 <div class="col-sm-4">
                                    <div class="form-check">
                                       <label>Status</label><br>
                                       <label class="radio-inline">
                                       <input type="radio" name="status" value="true" <?php if (!$update_mode || ($edit_user['status'] ?? 'true') == 'true') echo 'checked="checked"'; ?>>Active</label>
                                       <label class="radio-inline"><input type="radio" name="status" value="false" <?php if ($update_mode && ($edit_user['status'] ?? '') == 'false') echo 'checked="checked"'; ?>>Inactive</label>
                                    </div>
                                 </div>
                              </div>
                              <br>
                              <div class="container-fluid">
                                 <div class="row">
                                    <?php if ($update_mode) { ?>
                                       <input type="hidden" name="update_id" value="<?php echo $update_id; ?>">
                                       <a href="fh_usermastercreation.php" class="btn btn-warning btn-w-100">Cancel</a>
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
            <!-- Main content -->
            <section class="">
               <div class="row">
                  <div class="col-sm-12">
                     <div class="panel panel-bd lobidisable">
                        <div class="panel-heading">
                           <span class="fs-15 panel-header-text">
                              <i class="fa fa-th-list mr-5"></i>
                              <span>User Master List !</span>
                           </span>
                        </div>
                        <div class="panel-body form-scroll">
                            <!-- Toolbar & Search Box Row under Header Panel -->
                            <div class="row m-b-15">
                               <div class="col-sm-6 col-xs-12 m-b-5">
                                  <div class="btn-group">
                                     <a href="fh_usermastercreation.php" class="btn btn-exp btn-sm"><i class="fa fa-plus mr-5"></i>Add New User Master Creation</a>
                                  </div>
                                  <?php include('include/button-export-to-data.php');?>
                               </div>
                               <div class="col-sm-6 col-xs-12">
                                  <!-- Search Form Box under Header Panel -->
                                  <form id="userMasterSearchForm" class="search-form-wrap pull-right" onsubmit="return false;" style="width: 100%; max-width: 360px;">
                                     <input type="hidden" name="page" id="page" value="1">
                                     <div class="input-group search-box-group">
                                        <input type="text" class="form-control search-input search-input-field" name="search" id="company_name" placeholder="Search Users..." autocomplete="off">
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
                                        <th class="text-center-align">Picture</th>
                                        <th>User Code</th>
                                        <th>User Name</th>
                                        <th>Mobile No</th>
                                        <th>Store Name</th>
                                        <th>Location</th>
                                        <th>User Type</th>
                                        <th>Created Date</th>
                                        <th>Updated Date</th>
                                        <th class="text-center-align">Gender</th>
                                     </tr>
                                  </thead>
                                  <tbody id="usermaster-list-results">
                                      <?php
                                         $init_page = 1;
                                         $init_perpage = isset($_SESSION["_RECORDPERPAGE_"]) && $_SESSION["_RECORDPERPAGE_"] ? (int)$_SESSION["_RECORDPERPAGE_"] : 20;
                                         $init_offset = ($init_page - 1) * $init_perpage;
                                         $query = mysqli_query($conn, "SELECT * FROM tbl_user_master ORDER BY id DESC LIMIT $init_offset, $init_perpage"); 
                                         $countQuery = mysqli_query($conn, "SELECT COUNT(*) as total FROM tbl_user_master");
                                         $countRow = mysqli_fetch_assoc($countQuery);
                                         $totalRecords = $countRow['total'] ?? 0;
                                         $user_list = array();
                                         while ($u = mysqli_fetch_array($query)) {
                                            $user_list[] = $u;
                                         }
                                         $i = $init_offset + 1;
                                         foreach ($user_list as $user) {
                                        ?>
                                      <tr>
                                         <td class="td-center-middle">
                                            <?php echo $i++; ?>
                                         </td>
                                         <td class="td-center-middle-nowrap">
                                            <a class="btn btn-info btn-xs" data-toggle="tooltip" title="Edit User" href="fh_usermastercreation.php?update_id=<?php echo $user['id']; ?>">
                                               <i class="fa fa-pencil"></i> Edit
                                            </a>
                                            <button type="button" class="btn btn-warning btn-xs" data-toggle="modal" data-target="#viewDetailsModalList<?php echo $user['id']; ?>" title="View Details">
                                               <i class="fa fa-eye"></i> Details
                                            </button>
                                            <a class="btn btn-danger btn-xs" data-toggle="tooltip" title="Delete User" onClick="return confirm('Are you sure you want to Delete this User?')" href="fh_usermastercreation_list.php?delete_id=<?php echo $user['id']; ?>">
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
                                            <?php if (!empty($user['picture']) && file_exists('uploads/item-master/' . $user['picture'])) { ?>
                                               <img src="uploads/item-master/<?php echo htmlspecialchars($user['picture']); ?>" class="img-circle avatar-img-table" alt="User Image" width="40" height="40">
                                            <?php } else { ?>
                                               <div class="avatar-fallback-table">
                                                  <?php echo strtoupper(substr($user['user_name'] ?? 'U', 0, 1)); ?>
                                               </div>
                                            <?php } ?>
                                         </td>
                                         <td class="td-middle"><code><?php echo htmlspecialchars($user['user_code'] ?? ''); ?></code></td>
                                         <td class="td-middle-bold"><?php echo htmlspecialchars(substr($user['user_name'] ?? '',0,50)); ?></td>
                                         <td class="td-middle"><?php echo htmlspecialchars(substr($user['mobile_no'] ?? '',0,50)); ?></td>
                                         <td class="td-middle"><?php echo htmlspecialchars(substr($user['store_name'] ?? '',0,50)); ?></td>
                                         <td class="td-middle"><?php echo htmlspecialchars(substr($user['location'] ?? '',0,50)); ?></td>
                                         <td class="td-middle"><span class="label label-info badge-info-sm"><?php echo htmlspecialchars(substr($user['user_type'] ?? '',0,50)); ?></span></td>
                                         <td class="td-middle-muted"><?php echo !empty($user['created_date']) ? date('d-m-Y H:i', strtotime($user['created_date'])) : '-'; ?></td>
                                         <td class="td-middle-muted"><?php echo !empty($user['updated_date']) ? date('d-m-Y H:i', strtotime($user['updated_date'])) : '-'; ?></td>
                                         <td class="td-center-middle"><?php echo ($user['gender'] == '1') ? 'Male' : (($user['gender'] == '0') ? 'Female' : htmlspecialchars($user['gender'] ?? '')); ?></td>
                                      </tr>
                                      <?php } ?>
                                   </tbody>
                                </table>
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
               <!-- model Modal1 -->
               <div class="modal fade" id="gst" tabindex="-1" role="dialog" aria-hidden="true">
                  <div class="modal-dialog">
                     <div class="modal-content">
                        <div class="modal-header modal-header-primary">
                           <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                           <h3><i class="fa fa-plus m-r-5"></i> Add New Store Master Creation</h3>
                        </div>
                        <div class="modal-body">
                           <div class="row">
                              <div class="col-md-12">
                                 <form method="post">
                                         $company_name=$_POST['company_name'];
                                         $status=$_POST['status'];
                                         
                                             $dup=mysqli_query($conn,"select * from tbl_store_master where store_name='$store_name'");
                                               if(mysqli_num_rows($dup)>0)
                                               {
                                                   $msg='<div class="alert alert-danger alert-dismissible fade1 show">
                                                       <button type="button" class="close" data-dismiss="alert">&times;</button>
                                                       <strong>Store Master Already exist!</strong>
                                                       </div>';
                                               }
                                               else{
                                                   $query="Insert into tbl_store_master(store_code,store_name,location_name,company_name,status)values('$store_code','$store_name','$location_name','$company_name','$status')";
                                                   if(mysqli_query($conn,$query)){
                                                       $msg='<div class="alert alert-success alert-dismissible fade1 show">
                                                       <button type="button" class="close" data-dismiss="alert">&times;</button>
                                                       <strong>Store Master Creations Success..!</strong>
                                                     </div>';
                                                   }
                                                   else{
                                                       $msg='<div class="alert alert-danger alert-dismissible fade1 show">
                                                       <button type="button" class="close" data-dismiss="alert">&times;</button>
                                                       <strong>Store Master Creations Failed!</strong>
                                                       </div>';
                                                   }
                                               } 
                                           }
                                        ?>
                                    <div class="row">
                                       <div class="col-sm-6">
                                          <div class="form">
                                             <label>Store Code</label>
                                             <input type="password" name="store_code" class="form-control" id="store_code" readonly value="<?php @$no = mysqli_query($conn, "SELECT id FROM  tbl_store_master ORDER BY id DESC");
                                                $po_no = mysqli_fetch_assoc($no);
                                                echo 'SC'.($po_no['id']+1);
                                                ?>">
                                          </div>
                                       </div>
                                       <div class="col-sm-6">
                                          <div class="form-group">
                                             <label>Store Name</label>
                                             <input type="text" class="form-control" name="store_name" id="store_name" placeholder="Enter Your Store Name" required>
                                          </div>
                                       </div>
                                    </div>
                                    <div class="row">
                                       <div class="col-sm-6">
                                          <div class="form-group">
                                             <label>Company Name</label>
                                             <input type="text" class="form-control" name="company_name" id="company_name" placeholder="Enter Your Company Name" required>
                                          </div>
                                       </div>
                                       <div class="col-sm-6">
                                          <div class="form-group">
                                             <label>Store Location</label>
                                             <input type="text" class="form-control" name="location_name" id="location_name" placeholder="Enter Your Location Name" required>
                                          </div>
                                       </div>
                                    </div>
                                    <br>
                                    <div class="row">
                                       <div class="col-sm-4">
                                          <div class="form-check">
                                             <label>Status</label><br>
                                             <label class="radio-inline">
                                             <input type="radio" name="status" value="true" checked="checked">Active</label>
                                             <label class="radio-inline"><input type="radio" name="status" value="false" >Inctive</label>
                                          </div>
                                       </div>
                                    </div>
                                    <br>
                                    <div class="container-fluid">
                                       <div class="row">
                                           <button type="reset" name="submit" class="btn btn-warning btn-w-100">Reset</button>
                                           <button type="submit" name="submit_btn" class="btn btn-success btn-teal-100">Save</button>
                                       </div>
                                    </div>
                                    <br>
                                 </form>
                              </div>
                           </div>
                        </div>
                        <div class="modal-footer">
                           <button type="button" class="btn btn-danger pull-right" data-dismiss="modal">Close</button>
                        </div>
                     </div>
                     <!-- /.modal-content -->
                  </div>
                  <!-- /.modal-dialog -->
               </div>
               <!-- model Modal1-end -->
            </section>
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
      <script src="assets/dist/js/page/usermaster-list.js"></script>
      <!-- End Theme label Script
         =====================================================================-->
   </body>
   <!-- Mirrored from thememinister.com/crm/add-customer.html by HTTrack Website Copier/3.x [XR&CO'2014], Fri, 05 Aug 2022 06:10:42 GMT -->
</html>