<?php
include_once('include/config.php');
if(isset($_POST['Login']))
{
    $myusername = mysqli_real_escape_string($conn, $_POST['username']);
    $mypassword = mysqli_real_escape_string($conn, $_POST['password']);
    
    // Authenticate user against tbl_user_master
    $result = mysqli_query($conn, "SELECT * FROM tbl_user_master WHERE (user_name = '$myusername' OR user_code = '$myusername')");
    
    $authenticated = false;
    $user_row = null;
    
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $db_pass = $row['password'];
            if (password_verify($mypassword, $db_pass) || $mypassword === $db_pass) {
                $authenticated = true;
                $user_row = $row;
                break;
            }
        }
    }

    if ($authenticated && $user_row) {
        $_SESSION['login_user'] = $user_row['user_name'];
        $_SESSION['user_code']  = $user_row['user_code'];
        $_SESSION['user_type']  = $user_row['user_type'];
        $_SESSION['store_name'] = $user_row['store_name'];
        $_SESSION['IS_LOGIN']   = 'yes';
        header('location: fh_dashboard.php');
        exit();
    } else {
        $error = 'Please enter correct login details';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title> Admin Login</title>
        <link rel="shortcut icon" href="assets/dist/img/ico/favicon.png" type="image/x-icon">
        <link href="assets/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
        <link href="assets/pe-icon-7-stroke/css/pe-icon-7-stroke.css" rel="stylesheet" type="text/css"/>
        <link href="assets/dist/css/stylecrm.css" rel="stylesheet" type="text/css"/>
    </head>
    <body>
        <div class="login-wrapper">
            <div class="container-center">
            <div class="login-area">
                <div class="panel panel-bd panel-custom">
                    <div class="panel-heading">
                        <div class="view-header">
                            <div class="header-icon">
                                <i class="pe-7s-unlock"></i>
                            </div>
                            <div class="header-title">
                                <h3>Login</h3>
                                <small><strong class="text-danger"><?php echo @$error;?> </strong></small>
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <form method="post">
                            <div class="form-group">
                                <label class="control-label" for="username">Username</label>
                                <input type="text" placeholder="Username" title="Please enter you username" name="username" class="form-control">
                               
                            </div>
                            <div class="form-group">
                                <label class="control-label" for="password">Password</label>
                                <input type="password" title="Please enter your password" placeholder="******" name="password" class="form-control">
                                <!-- <span class="help-block small">Your strong password</span> -->
                            </div>
                            <div>
                                <button class="btn btn-add" name="Login">Login</button>
                              <!--   <a class="btn btn-warning" href="register.html">Register</a> -->
                            </div>
                        </form>
                        </div>
                        </div>
                </div>
            </div>
        </div>
        <script src="assets/plugins/jQuery/jquery-1.12.4.min.js" type="text/javascript"></script>
        <script src="assets/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
    </body>
</html>