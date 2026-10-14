<?php
include_once('include/config.php');

if (!isset($_SESSION['login_user'])) {
    header("location:index.php");
    die();
}

$user_check = mysqli_real_escape_string($conn, $_SESSION['login_user']);
$ses_sql = mysqli_query($conn, "SELECT * FROM tbl_user_master WHERE user_name = '$user_check' OR user_code = '$user_check'");
$row = mysqli_fetch_array($ses_sql, MYSQLI_ASSOC);
$login_session = $row['user_name'] ?? $user_check;
$_SESSION['ROLE'] = $row['user_type'] ?? '';
?>