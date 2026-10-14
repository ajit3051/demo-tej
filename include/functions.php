<?php


function setSytemMailConstants($template_file)
{
    $mail_body_template = file_get_contents(_BASEPATH . "pdf_templates/header.html") .
        file_get_contents(_BASEPATH . "pdf_templates/" . $template_file) .
        file_get_contents(_BASEPATH . "pdf_templates/footer.html");

    $mail_body = str_replace("[GENERATED_DATE]", date('Y-m-d H:i:s'), $mail_body_template);
    // $mail_body = str_replace("[CLOUD_NAME]", _CLOUD_NAME_, $mail_body);
    // $mail_body = str_replace("[CLOUD_URL]", _CLOUD_URL_, $mail_body);
    // $mail_body = str_replace("[_BASEURL]", _BASEURL, $mail_body);
    // $mail_body = str_replace("[_PORTAL_LOGO]", _PORTAL_LOGO, $mail_body);
    return $mail_body;
}

function dateformat($date, $time = 0)
{
    if (!empty($date) && $date != '0000-00-00 00:00:00' && $date != '0000-00-00') {
        if ($time == 1) {
            return date('d-m-Y h:i:s A', strtotime($date));
        } else {
            return date('d-m-Y', strtotime($date));
        }
    }
}

function timeformat($time, $timeformat = 0)
{
    if (!empty($time) && $time != '00:00:00') {
        if ($timeformat == 1) {
            return  date('H:i:s', strtotime($time)); //24 Hrs
        } else {
            return date('h:i:s A', strtotime($time)); //12 Hrs
        }
    } else {
        //return '00:00:00'; 
    }
}

function isDateExist($date)
{
    if (!empty($date) && $date != '0000-00-00 00:00:00' && $date != '0000-00-00') {

        return $date;
    }
}

function getLocation($id)
{
    global $conn;
    $query = mysqli_query($conn, "SELECT description FROM tbl_store_master where id = $id");
    $row = mysqli_fetch_array($query);
    return $row['description'];
}

function getBrandName($id)
{
    global $conn;
    $query = mysqli_query($conn, "SELECT description FROM tbl_brand_master where id = $id");
    $row = mysqli_fetch_array($query);
    return $row['description'];
}

function getGroupName($id)
{
    global $conn;
    $query = mysqli_query($conn, "SELECT description FROM tbl_group_master where id = $id");
    $row = mysqli_fetch_array($query);
    return $row['description'];
}

function generateBillNo($table, $column, $prefix)
{
    $db = connect();
    $query = $db->select("select $column as bill from $table ORDER BY $column DESC LIMIT 0,1");

    $row = $query->fetch_assoc();
    // echo "select $column as bill from $table ORDER BY $column DESC LIMIT 0,1"; die;
    $billArray = explode('-', $row['bill']);
    $billno = (int)$billArray[1] + 1;

    return ucwords($prefix) . '-' . sprintf('%04d', $billno);
}

function countRecords($table, $column)
{
    $db = connect();
    $stmt = $db->select("SELECT SUM($column) AS cnt FROM $table");
    $row = $stmt->fetch_assoc();
    return $row['cnt'];
}
function countLatestRecords($table, $column)
{
    $db = connect();

    $today = date("Y-m-d");
    $stmt = $db->select("SELECT SUM($column) AS cnt FROM $table WHERE DATE(created_date) =?", 's', $today);
    $row = $stmt->fetch_assoc();
    return $row['cnt'];
}

function getTotalValue($table, $column)
{
    $db = connect();
    $stmt = $db->select("SELECT SUM(CAST($column AS DECIMAL(10,2))) AS totalvalue FROM $table");
    $row = $stmt->fetch_assoc();
    return $row['totalvalue'];
}

function getTotalLatestValue($table, $column)
{
    $db = connect();
    $today = date("Y-m-d");
    $stmt = $db->select("SELECT SUM(CAST($column AS DECIMAL(10,2))) AS totalvalue FROM $table WHERE DATE(created_date) =?", 's', $today);
    $row = $stmt->fetch_assoc();
    return $row['totalvalue'];
}

function redirectTo($pageName, $message = '', $error = '')
{
    $_SESSION['showMessages'] = $message;
    $_SESSION['errorMessages'] = $error;
    header("location:" . $pageName);
    exit;
}

function xssSafe($str)
{

    if (!empty($str)) {
        return htmlspecialchars(strip_tags($str));
    } else {
        return $str;
    }
}

function beautify($str)
{

    if (!empty($str)) {
        return ucwords(str_replace('_', ' ', htmlspecialchars(strip_tags($str))));
    } else {
        return $str;
    }
}

function menuFoodBtn($day, $startTime, $endTime)
{

    $currentDate = date('Y-m-d H:i:s');
    $currentDay = date("l");
    $startDate = date('Y-m-d') . ' ' . $startTime;
    $date = new DateTime();

    if ($startTime < $endTime) {
        // Format the date to 'Y-m-d'
        $endDate = $date->format('Y-m-d') . ' ' . $endTime;
    } else {
        // Add one day
        $date->modify('+1 day');

        // Format the date to 'Y-m-d'
        $endDate = $date->format('Y-m-d') . ' ' . $endTime;

        $today = date('Y-m-d');
        $dateTime = new DateTime($today);
        $dateTime->modify('-1 day');
        $currentDay = $dateTime->format('l');
    }

    //$time24 = date('H:i:s'); // current time in 24-hour format
    //$time12 = date('h:i:s', strtotime($time24)); // Convert to 12-hour format

    if ($currentDay == $day && $startDate <= $currentDate && $endDate >= $currentDate) {
        return true;
    }
    return false;
}

function nightAuditDate($nightAudit)
{

    if ($nightAudit == 'yes') {

        //$time24 = date('H:i:s'); // current time in 24-hour format
        $time24 = '06:00:00'; // current time in 24-hour format
        if ($time24 >= '00:00:00' && $time24 <= '07:00:00') {
            return date('Y-m-d', strtotime('-1 day'));
        } else {
            return date('Y-m-d');
        }
    }
    return date('Y-m-d');
}

/* ============== End Restrict Document Access ============== */

function getMIMETypes($allowedFileType)
{
    $validMimeTypeArr = array();
    foreach ($allowedFileType as $fkey => $fvalue) {
        switch ($fvalue) {
            case 'xls':
                $validMimeTypeArr[] = 'text/xml';
                break;
            case 'xlsx':
                $validMimeTypeArr[] = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
                break;

            case 'pdf':
                $validMimeTypeArr[] = 'application/pdf';
                $validMimeTypeArr[] = 'application/octet-stream';
                break;

            case 'png':
                $validMimeTypeArr[] = 'image/png';
                break;

            case 'jpg':
                $validMimeTypeArr[] = 'image/jpg';
                break;

            case 'jpeg':
                $validMimeTypeArr[] = 'image/jpeg';
                break;

            case 'gif':
                $validMimeTypeArr[] = 'image/gif';
                break;

            case 'docx':
                $validMimeTypeArr[] = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
                $validMimeTypeArr[] = 'application/octet-stream';
                break;

            case 'doc':
                $validMimeTypeArr[] = 'application/msword';
                break;
            case 'zip':
                $validMimeTypeArr[] = 'application/zip';
                break;
        }
    }

    return $validMimeTypeArr;
}

function validateUploadedFile($filesData, $allowedFileType, $errorMessageArr, $uploadSize = 5)
{

    if (!($uploadSize <= 5)) {
        $errorMessageArr[] = "Invalid File Upload Size. It should be up to 5MB";
    }

    if ($filesData["name"] == "") {
        $errorMessageArr[] = "Please select a file to upload";
    } else if ($filesData["name"] != "") {
        $file_ext = pathinfo($filesData["name"], PATHINFO_EXTENSION);

        if (!in_array($file_ext, $allowedFileType)) {
            $errorMessageArr[] = ' File extension not allowed, please choose ' . implode('/', $allowedFileType) . ' file.';
        }


        ////////////////Size check /////////////////
        $_1_MB_Bytes = 1048576; // MB in Bytes
        if ($filesData['size'] > ($_1_MB_Bytes * $uploadSize)) {
            $errorMessageArr[] = " File size must be up to $uploadSize MB";
        }


        ////////////////mime type check /////////////////
        if (function_exists(mime_content_type)) {
            $mimeType = mime_content_type($filesData['tmp_name']);

            if (!in_array($mimeType, getMIMETypes($allowedFileType))) {
                $errorMessageArr[] = 'Invalid File:' . $mimeType . '. Please upload ' . implode('/', $allowedFileType) . ' files only.';
            }
        }
    }

    return $errorMessageArr;
}

function generateDocName($temp_filename)
{

    return md5(substr(md5(microtime()), 1, 8)) . '_' . preg_replace('/[\W]/', '', pathinfo($temp_filename, PATHINFO_FILENAME)) . '.' . pathinfo($temp_filename, PATHINFO_EXTENSION);
}

function uploadValidatedFile($filesData, $uploadLocation, $temp_filename = '')
{

    if ($temp_filename == '') {
        $fileName = generateDocName($filesData['name']);
    } else {
        $fileName = generateDocName($temp_filename);
    }
    $targetPath = $uploadLocation . $fileName;

    if ($targetPath) {

        if (file_exists($uploadLocation)) { // check Directory exists
            move_uploaded_file($filesData['tmp_name'], $targetPath);
        } else {
            mkdir($uploadLocation, 0777, true);
            move_uploaded_file($filesData['tmp_name'], $targetPath);
        }
        return $fileName;
    }
    return false;
}

function getSessionUserId()
{
    return $_SESSION['auth_user']['user_id'];
}

function getTotalPrice($cart_id)
{
    $db = connect();
    $user_id = getSessionUserId();

    $cart_stmt = $db->select("SELECT (C.quantity * FO.mrp) as price FROM tbl_cart as C INNER JOIN tbl_food_order as FO ON FO.id = C.item_id WHERE C.user_id=? AND C.id=?", 'ii', $user_id, $cart_id);

    $item_res = $cart_stmt->fetch_assoc();
    $cart_stmt->close();
    return $item_res['price'];
}

function getCartTotalPriceForCheckout($extra_charges = [], $discounts = [])
{
    $db = connect();
    $user_id = getSessionUserId();

    // Add extra charges
    $extraCharges = 0;
    if (!empty($extra_charges)) {

        $extraCharges = array_sum($extra_charges);
    }

    // discount price
    $discountPrice = 0;
    if (!empty($discounts)) {

        $discountPrice = array_sum($discounts);
    }

    $cart_stmt = $db->select("SELECT SUM(C.quantity * FO.mrp) as price FROM tbl_cart as C INNER JOIN tbl_food_order as FO ON FO.id = C.item_id WHERE C.user_id=?", 'i', $user_id);

    $item_res = $cart_stmt->fetch_assoc();
    $cart_stmt->close();
    $price = $item_res['price'];

    $total_price = ($price + $extraCharges) - $discountPrice;

    return number_format($total_price, 2);
}

function getPreparedAddressById($id)
{
    $validationHelper = new validation();

    $db = connect();
    $user_stmt = $db->select("SELECT * FROM tbl_order_address WHERE id=?", 'i', $id);

    $add_res = $user_stmt->fetch_assoc();

    foreach ($add_res as $key => $value) {
        $$key = $validationHelper->filterText($value);
    }
    $user_stmt->close();
    $db->close();

    $address = $name . ', ' . $mobile . ', ' . $address . ', ' . $landmark . ', ' . $country . ', ' . $state . ', ' . $city . ', ' . $pincode;

    return $address;
}

function getGSTValueBygstType($gst)
{
    return (float) $gst / 2;
}

function dateInSQLFormat($date)
{
    return date('Y-m-d', strtotime($date));
}

function getPayModeByCode($code)
{
    if (empty($code)) {
        return '';
    }
    $db = connect();

    $stmt = $db->select("SELECT paymode_name FROM tbl_paymode_master WHERE paymode_code=?", 's', $code);

    if ($stmt) {
        $item_res = $stmt->fetch_assoc();
        $stmt->close();
        if ($item_res && isset($item_res['paymode_name'])) {
            return $item_res['paymode_name'];
        }
    }
    return $code;
}

function getAccountEmailBymobile($mobile)
{
    $db = connect();

    $stmt = $db->select("SELECT email_id_1 FROM tbl_account_master WHERE mobile_no_1=?", 's', $mobile);

    $item_res = $stmt->fetch_assoc();
    $stmt->close();
    return $item_res['email_id_1'];
}

function calculateTotalPrice($extra_charges = [], $discounts = [])
{
    $db = connect();

    // Add extra charges
    $extraCharges = 0;
    if (!empty($extra_charges)) {

        $extraCharges = array_sum($extra_charges);
    }

    // discount price
    $discountPrice = 0;
    if (!empty($discounts)) {

        $discountPrice = array_sum($discounts);
    }

    $total_price = $extraCharges - $discountPrice;

    return number_format($total_price, 2);
}

/*-====Function for Pagination =====-*/
function showPagingInfoTxt($pageno, $perpage, $totalrecords)
{

    if ($totalrecords == 0) {
        return "No records found";
    }

    if ($perpage > $totalrecords) {
        $perpage = $totalrecords;
    }

    $maxpageno = (int)ceil($totalrecords / $perpage);
    if ($pageno > $maxpageno || $pageno < 1) {
        $pageno = 1;
    }

    $offset = $perpage * ($pageno - 1);
    $limit = ($perpage * $pageno);
    if ($limit > $totalrecords) {
        $limit = $totalrecords;
    }

    return "Showing " . ($offset + 1) . " to  $limit of $totalrecords records";
}

function include_pagination_component($offset, $recordsPerPage, $totalRecords)
{
    $htmltext = '
    <div class="row pagination-wrapper-row">
    <div class="col-sm-6 col-md-5">
        <div class="select-pagination select-pagination-box">
            <select class="recordsPerPage form-control recordsPerPage-select">';
    $pagingRecords = _PAGEARRY_;
    foreach ($pagingRecords as $record) {
        $htmltext .= '<option value="' . $record . '"' . ($recordsPerPage == $record ? "Selected" : "") . '>' . $record . '</option>';
    }
    $htmltext .= '</select><span class="pagination-info-text">' . showPagingInfoTxt($offset, $recordsPerPage, $totalRecords) . '</span>
        </div>
    </div>';

    $URL = "{@PAGE}";
    $pager = new pager_v2($URL, $totalRecords, $recordsPerPage, $offset);

    $htmltext .= '<div class="col-sm-6 col-md-7 text-right pull-right">';
    if ($pager->totalpages > 1) {
        $htmltext .= $pager->outputlinks();
    }
    $htmltext .= '</div></div>';

    return $htmltext;
}

require_once(_BASEPATH . 'include/download_excel_report.php');
require_once(_BASEPATH . 'include/download_pdf_report.php');