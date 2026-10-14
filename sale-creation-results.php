<?php include_once("../include/config.php"); ?>

<?php
$validationHelper = new validation();

$db = connect();

$today = date("Y-m-d");

$page       = $_POST['page'];
$sortOrder    = $_POST['sortOrder'];
$sortField    = $_POST['sortField'];
$exportType       = $_REQUEST['export'];

$params = '';
$fields = array();

if ($exportType) {
   $columns = 'ANY_VALUE(purchage_date) as purchage_date, bill_number, ANY_VALUE(account_name) as account_name, ANY_VALUE(pay_mode) as pay_mode, ANY_VALUE(gst_type) as gst_type, SUM(gst_amount) as gst_amount, SUM(amount) as amount, ANY_VALUE(status) as status';
} else {
   $columns = 'ANY_VALUE(id) as id, ANY_VALUE(purchage_date) as purchage_date, bill_number, ANY_VALUE(account_name) as account_name, ANY_VALUE(pay_mode) as pay_mode, ANY_VALUE(gst_type) as gst_type, SUM(gst_amount) as gst_amount, SUM(amount) as amount, ANY_VALUE(status) as status';
}

$query = "SELECT $columns FROM tbl_bill GROUP BY bill_number";

if (empty($fields)) {

   $stmt = $db->select($query);
} else {

   $stmt = $db->select($query, $params, $param_fields);
}

if ($exportType == 'excel') {
   exportResult('sale-item', $stmt);
   exit();
}
if($exportType == 'pdf'){
   exportResultPdf('sale-item', $stmt); 
   exit();
}

$totalRecords = $stmt->num_rows();

$recordsPerPage = $_SESSION["_RECORDPERPAGE_"] ? $_SESSION["_RECORDPERPAGE_"] : _RECORDPERPAGE_;
$offSet = ($page - 1) * $recordsPerPage;

$query .= " ORDER BY $sortField $sortOrder LIMIT $offSet,$recordsPerPage";

if (empty($fields)) {

   $stmt = $db->select($query);
} else {

   $stmt = $db->select($query, $params, $param_fields);
}

$totalRecordsWithLimit = $stmt->num_rows();

if ($totalRecordsWithLimit > 0) {
   $html = '';
   for ($i = 1; $i <= $totalRecordsWithLimit; $i++) {
      $row = $stmt->fetch_assoc();

      $html .= '<tr>
         <td>
            <div class="checkbox checkbox-info">
               <input id="checkbox' . $i . '" name="recent-chk[]" type="checkbox" value="' . $row['id'] . '">
               <label class="s_no" for="checkbox' . $i . '">' . $i . '</label>
            </div>
         </td>
         <td>' . dateformat($row['purchage_date']) . '</td>
         <td>' . $row['bill_number'] . '</td>
         <td>' . $row['account_name'] . '</td>
         <td>' . getPayModeByCode($row['pay_mode']) . '</td>
         <td>' . $row['gst_type'] . '</td>
         <td>' . $row['gst_amount'] . '</td>
         <td>' . $row['amount'] . '</td>
         <td>';
      if ($row['status'] == 'success') {
         $html .= '<span class="label label-success">' . $row['status'] . '</span>';
      } else {
         $html .= '<span class="label label-warning">' . $row['status'] . '</span>';
      }
      $html .= '</td>
         <td>
            <a class="btn-add btn-xs" onclick="view_details(' . $row['id'] . ');" href="javascript:void(0);" data-toggle="tooltip" title="View Details">
               <span class="fa fa-list-alt"></span></a>
            <a class="btn-add btn-xs" onclick="view_invoice(' . $row['id'] . ');" href="javascript:void(0);" data-toggle="tooltip" title="View Invoice"><span class="fa fa-file"></span></a>
            <a href="https://api.whatsapp.com/send?text=' . urlencode('Bill No: ' . $row['bill_number']) . '" target="_blank" class="btn-add btn-xs" style="background-color:#25D366; border-color:#25D366;" data-toggle="tooltip" title="WhatsApp"><span class="fa fa-whatsapp"></span></a>
            <a role="button" class="btn-add btn-xs" onclick="sendMail(\'' . $row['bill_number'] . '\');" data-toggle="tooltip" title="Send Email"><span class="fa fa-envelope"></span></a>
            <a href="bill-invoice.php?type=sale&bill_no=' . urlencode($row['bill_number']) . '&auto_print=1" target="_blank" class="btn-add btn-xs" data-toggle="tooltip" title="Print Invoice"><span class="fa fa-print"></span></a>
         </td>
         <td>
            <a class="" onClick="return confirm(\'Are you sure you want to Update Bill?\')" href="sale-creations.php?bill_no=' . $row['bill_number'] . '"><button data-toggle="tooltip" title="Update Bill" type="button" class="btn-add btn-xs"><i class="fa fa-pencil"></i><span style=""></span></button></a>

            <a class="btn-danger btn btn-xs" onclick="deleterecord(' . $row['id'] . ')" href="javascript:void(0);" data-toggle="tooltip" title="Delete">

               <i class="fa fa-trash-o"></i><span></span></button></a>

         </td>
      </tr>';
   }

   $data['pagination'] = include_pagination_component($page, $recordsPerPage, $totalRecords);
} else {
   $html = '<tr>
      <td colspan="12"> No Recently added</td>
   </tr>';
}
$data['htmlData'] = $html;
echo json_encode($data);
die;
