<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
error_reporting(0);


$allowed_domains = array('localhost', '127.0.0.1', 'tej.tejaserp.com', 'demo.tejaserp.com');


if (!in_array($_SERVER['HTTP_HOST'] ?? '', $allowed_domains, TRUE)) {

    header("HTTP/1.1 400 Redirect", true);
    echo '<div style="text-align:center;"> <h4>The Server Has Refused To Fulfill Your Request</h4></div>';
    die();
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

############ General ############################
date_default_timezone_set("Asia/Kolkata");

define("_BASEURL", 'https://demo.tejaserp.com/');
define("_BASEPATH", htmlspecialchars($_SERVER['DOCUMENT_ROOT']) . '/');

// define("_BASEURL", 'http://localhost/demo-tej/');
// define("_BASEPATH", dirname(__DIR__) . '/');


if (!defined('_CLASS_PATH'))
    define("_CLASS_PATH", _BASEPATH . "classes/");

if (!defined('_SENDMAIL_'))
    define("_SENDMAIL_", true);

if (!defined('_MAIL_FROM_EMAIL_'))
    //define("_MAIL_FROM_EMAIL_", 'info@awesomesoft.in');
    define("_MAIL_FROM_EMAIL_", 'ramalayasales@prabhushriram.com');

if (!defined('_MAIL_FROM_NAME_'))
    define("_MAIL_FROM_NAME_", 'Awesomesoft');

/*---=== Assets Path ====----*/
if (!defined('_IMAGE_PATH'))
    define("_IMAGE_PATH", _BASEURL . "assets/images/");

if (!defined('_CSS_PATH'))
    define("_CSS_PATH", _BASEURL . "assets/css/");


############  Upload path ############################	
if (!defined('_UPLOAD_DIR'))
    define("_UPLOAD_DIR", _BASEPATH . "uploads/");


if (!defined('_UPLOAD_FILE_URL'))
    define("_UPLOAD_FILE_URL", _BASEURL . "uploads/");


if (!defined('_PAGEARRY_')) {
    define('_PAGEARRY_', array('10', '20', '50', '100'));
}
if (!defined('_RECORDPERPAGE_')) {
    define("_RECORDPERPAGE_", "20");
}

############  Javascript  path setting ############################	

if (!defined('_JS_PATH'))
    define("_JS_PATH", _BASEURL . "assets/js/");



############  DataBase   setting ############################	
if (!defined('_HOST_'))
    define("_HOST_", "localhost");
if (!defined('_USER_'))
    define("_USER_", "u488042670_demoerp");
if (!defined('_PASS_'))
    define("_PASS_", "Hn3#bcXL|");
if (!defined('_DB_'))
    define("_DB_", "u488042670_demoerp");
    
// if (!defined('_HOST_'))
//     define("_HOST_", "localhost");
// if (!defined('_USER_'))
//     define("_USER_", "admin");
// if (!defined('_PASS_'))
//     define("_PASS_", "admin");
// if (!defined('_DB_'))
//     define("_DB_", "u488042670_demoerp");

require_once(_BASEPATH . "classes/pager_v2.cls.php");
require_once(_BASEPATH . "classes/validation.php");
require_once(_BASEPATH . "classes/mysqli.class.php");
require_once(_BASEPATH . "include/functions.php");
include_once(_BASEPATH . "include/ajax_functions.php");
include_once(_BASEPATH . "include/send_mail.php");


function connect()
{
    global $conn;
    if (!isset($conn) || !$conn || !@$conn->ping()) {
        $conn = new mysqli_ext(_HOST_, _USER_, _PASS_, _DB_);
    }
    return $conn;
}

$conn = connect();
$db = $conn;
