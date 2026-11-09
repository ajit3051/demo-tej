<?php
include_once('include/config.php');

if (!isset($_SESSION['login_user'])) {
    header("location:index.php");
    die();
}

$user_check = mysqli_real_escape_string($conn, $_SESSION['login_user']);
$ses_sql = mysqli_query($conn, "SELECT u.*, s.store_name FROM tbl_user_master u LEFT JOIN tbl_store_master s ON u.store_code = s.store_code WHERE u.user_name = '$user_check' OR u.user_code = '$user_check'");
$row = mysqli_fetch_array($ses_sql, MYSQLI_ASSOC);
$login_session = $row['user_name'] ?? $user_check;
$_SESSION['ROLE'] = $row['user_type'] ?? '';
if (!empty($row['store_code'])) {
    $_SESSION['store_code'] = $row['store_code'];
}
if (!empty($row['store_name'])) {
    $_SESSION['store_name'] = $row['store_name'];
}
?>