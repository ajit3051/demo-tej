<?php include('top.php'); ?>
<?php
$msg = '';
$user_check = mysqli_real_escape_string($conn, $_SESSION['login_user'] ?? '');

// Fetch current user details from tbl_user_master
$user_query = mysqli_query($conn, "SELECT * FROM tbl_user_master WHERE user_name = '$user_check' OR user_code = '$user_check'");
$user_data = mysqli_fetch_assoc($user_query);

$display_name   = $user_data['user_name'] ?? $user_check;
$user_code      = $user_data['user_code'] ?? 'N/A';
$user_role      = $user_data['user_type'] ?? 'Administrator';
$mobile_no      = $user_data['mobile_no'] ?? 'N/A';
$store_name     = $user_data['store_name'] ?? 'Main Store';
$location       = $user_data['location'] ?? 'Headquarters';
$status         = $user_data['status'] ?? 'Active';
$date_joining   = $user_data['created_date'] ?? date('Y-m-d');
$picture        = !empty($user_data['picture']) ? 'uploads/item-master/' . $user_data['picture'] : '';

// HANDLE PROFILE & PASSWORD UPDATE
if (isset($_POST['update_profile'])) {
    $new_password = mysqli_real_escape_string($conn, $_POST['new_password'] ?? '');
    $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password'] ?? '');

    if (!empty($new_password)) {
        if ($new_password !== $confirm_password) {
            $msg = '<div class="alert alert-danger alert-dismissible fade in">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <strong>Error!</strong> Passwords do not match.
            </div>';
        } else {
            $hashed_pass = password_hash($new_password, PASSWORD_DEFAULT);
            $update_sql = "UPDATE tbl_user_master SET password = '$hashed_pass' WHERE user_name = '$user_check' OR user_code = '$user_check'";
            if (mysqli_query($conn, $update_sql)) {
                $msg = '<div class="alert alert-success alert-dismissible fade in">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <strong>Success!</strong> Password updated successfully 🎉
                </div>';
            } else {
                $msg = '<div class="alert alert-danger alert-dismissible fade in">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <strong>Error!</strong> Unable to update password.
                </div>';
            }
        }
    } else {
        $msg = '<div class="alert alert-info alert-dismissible fade in">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            Profile details updated successfully.
        </div>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <title>User Profile || TEJASERP</title>
      <?php include('include/css.php');?>
      <style>
      .profile-card {
          background: #ffffff;
          border-radius: 8px;
          box-shadow: 0 4px 12px rgba(0,0,0,0.06);
          padding: 25px 20px;
          text-align: center;
          border: 1px solid #e2e8f0;
          margin-bottom: 20px;
      }
      .profile-avatar-wrapper {
          width: 90px;
          height: 90px;
          margin: 0 auto 15px;
          border-radius: 50%;
          background: linear-gradient(135deg, #009688, #0284c7);
          color: #ffffff;
          display: flex;
          align-items: center;
          justify-content: center;
          font-size: 38px;
          box-shadow: 0 4px 15px rgba(0,150,136,0.3);
          border: 4px solid #ffffff;
      }
      .profile-name {
          font-size: 20px;
          font-weight: 700;
          color: #1e293b;
          margin: 0 0 4px 0;
      }
      .profile-role-badge {
          display: inline-block;
          background: #e0f2f1;
          color: #009688;
          font-weight: 600;
          font-size: 12px;
          padding: 3px 12px;
          border-radius: 20px;
          margin-bottom: 20px;
      }
      .profile-info-list {
          list-style: none;
          padding: 0;
          margin: 0;
          text-align: left;
      }
      .profile-info-list li {
          padding: 10px 0;
          border-bottom: 1px dashed #e2e8f0;
          display: flex;
          align-items: center;
          justify-content: space-between;
          font-size: 13px;
      }
      .profile-info-list li:last-child {
          border-bottom: none;
      }
      .profile-info-list .label-title {
          color: #64748b;
          font-weight: 500;
          display: flex;
          align-items: center;
          gap: 8px;
      }
      .profile-info-list .value-text {
          color: #1e293b;
          font-weight: 600;
      }
      .panel-custom-profile {
          border-radius: 8px;
          border: 1px solid #e2e8f0;
          box-shadow: 0 4px 12px rgba(0,0,0,0.06);
      }
      .panel-custom-profile .panel-heading {
          background: #f8fafc;
          border-bottom: 1px solid #e2e8f0;
          padding: 15px 20px;
          font-weight: 700;
          font-size: 15px;
          color: #1e293b;
      }
      </style>
   </head>
   <body class="hold-transition sidebar-mini">
      <div class="wrapper">
         <?php include('include/header-top-bar.php');?>
         
         <!-- Left side column. contains the sidebar -->
         <aside class="main-sidebar">
            <?php include('include/sidebar-left.php');?>
         </aside>

         <!-- Content Wrapper -->
         <div class="content-wrapper">
            <div class="container-fluid">
               <div class="row">
                  <?php include('include/menu-header.php');?>
               </div>
            </div>

            <!-- Main content -->
            <section class="container-fluid">
               <div class="row">
                  <!-- Left Column: User Card -->
                  <div class="col-md-4 col-sm-12">
                     <div class="profile-card">
                        <div class="profile-avatar-wrapper">
                           <i class="fa fa-user"></i>
                        </div>
                        <h3 class="profile-name"><?php echo htmlspecialchars($display_name); ?></h3>
                        <span class="profile-role-badge"><i class="fa fa-shield"></i> <?php echo htmlspecialchars($user_role); ?></span>

                        <ul class="profile-info-list">
                           <li>
                              <span class="label-title"><i class="fa fa-user-circle text-primary"></i> Username</span>
                              <span class="value-text"><?php echo htmlspecialchars($display_name); ?></span>
                           </li>
                           <li>
                              <span class="label-title"><i class="fa fa-building text-info"></i> Store Name</span>
                              <span class="value-text"><?php echo htmlspecialchars($store_name); ?></span>
                           </li>
                           <li>
                              <span class="label-title"><i class="fa fa-map-marker text-danger"></i> Location</span>
                              <span class="value-text"><?php echo htmlspecialchars($location); ?></span>
                           </li>
                           <li>
                              <span class="label-title"><i class="fa fa-check-circle text-success"></i> Status</span>
                              <span class="label label-success"><?php echo htmlspecialchars($status); ?></span>
                           </li>
                        </ul>
                     </div>
                  </div>

                  <!-- Right Column: Edit Profile & Password Form -->
                  <div class="col-md-8 col-sm-12">
                     <div class="panel panel-bd panel-custom-profile">
                        <div class="panel-heading">
                           <i class="fa fa-sliders" style="color: #009688; margin-right: 8px;"></i>
                           Account Settings & Security
                        </div>
                        <div class="panel-body">
                           <?php echo $msg; ?>
                           <form method="post">
                              <h4 style="font-size: 14px; font-weight: 700; color: #1e293b; border-bottom: 2px solid #009688; padding-bottom: 6px; margin-bottom: 18px;">
                                 <i class="fa fa-user text-success" style="margin-right: 6px;"></i> Profile Information
                              </h4>
                              
                              <div class="row">
                                 <div class="col-sm-6">
                                    <div class="form-group">
                                       <label>Username</label>
                                       <input type="text" class="form-control" value="<?php echo htmlspecialchars($display_name); ?>" readonly style="background-color: #f1f5f9;">
                                    </div>
                                 </div>
                                 <div class="col-sm-6">
                                    <div class="form-group">
                                       <label>User Role</label>
                                       <input type="text" class="form-control" value="<?php echo htmlspecialchars($user_role); ?>" readonly style="background-color: #f1f5f9;">
                                    </div>
                                 </div>
                              </div>

                              <div class="row">
                                 <div class="col-sm-6">
                                    <div class="form-group">
                                       <label>Assigned Store</label>
                                       <input type="text" class="form-control" value="<?php echo htmlspecialchars($store_name); ?>" readonly style="background-color: #f1f5f9;">
                                    </div>
                                 </div>
                                 <div class="col-sm-6">
                                    <div class="form-group">
                                       <label>Location</label>
                                       <input type="text" class="form-control" value="<?php echo htmlspecialchars($location); ?>" readonly style="background-color: #f1f5f9;">
                                    </div>
                                 </div>
                              </div>

                              <h4 style="font-size: 14px; font-weight: 700; color: #1e293b; border-bottom: 2px solid #009688; padding-bottom: 6px; margin-top: 25px; margin-bottom: 18px;">
                                 <i class="fa fa-lock text-warning" style="margin-right: 6px;"></i> Change Password
                              </h4>

                              <div class="row">
                                 <div class="col-sm-6">
                                    <div class="form-group">
                                       <label>New Password</label>
                                       <input type="password" name="new_password" class="form-control" placeholder="Enter New Password">
                                    </div>
                                 </div>
                                 <div class="col-sm-6">
                                    <div class="form-group">
                                       <label>Confirm New Password</label>
                                       <input type="password" name="confirm_password" class="form-control" placeholder="Confirm New Password">
                                    </div>
                                 </div>
                              </div>

                              <div style="margin-top: 20px;">
                                 <button type="submit" name="update_profile" class="btn btn-add" style="background-color: #009688; border: none; padding: 8px 24px; font-weight: 600;">
                                    <i class="fa fa-save" style="margin-right: 6px;"></i> Save Changes
                                 </button>
                              </div>
                           </form>
                        </div>
                     </div>
                  </div>
               </div>
            </section>
         </div>
      </div>
      <?php include('include/js.php');?>
   </body>
</html>
