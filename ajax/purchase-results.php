<?php include_once("../include/config.php"); ?>

<?php
$validationHelper = new validation();

$db = connect();

$today = date("Y-m-d");

$page 		= $_POST['page'] ?? 1;
$sortOrder 	= $_POST['sortOrder'] ?? 'DESC';
$sortField 	= $_POST['sortField'] ?? 'id';
$exportType = $_REQUEST['export'] ?? '';
$search     = trim($_REQUEST['search'] ?? '');

$params = '';
$fields = array();

if ($exportType) {
   $columns = "ANY_VALUE(purchage_date) as purchage_date, 
               bill_number, 
               ANY_VALUE(invoice_number) as invoice_number, 
               ANY_VALUE(account_name) as account_name, 
               ANY_VALUE(gst_type) as gst_type, 
               SUM(gst_amount) as gst_amount, 
               SUM(amount) as amount, 
               ANY_VALUE(status) as status";
} else {
   $columns = "ANY_VALUE(id) as id, 
               ANY_VALUE(invoice_number) as invoice_number, 
               ANY_VALUE(purchage_date) as purchage_date, 
               bill_number, 
               ANY_VALUE(account_name) as account_name, 
               ANY_VALUE(gst_type) as gst_type, 
               SUM(gst_amount) as gst_amount, 
               SUM(amount) as amount, 
               ANY_VALUE(status) as status";
}

$whereClause = "";
if ($search !== '') {
   $safeSearch = $db->real_escape_string($search);
   $whereClause = " WHERE bill_number LIKE '%$safeSearch%' OR invoice_number LIKE '%$safeSearch%' OR account_name LIKE '%$safeSearch%' OR gst_type LIKE '%$safeSearch%' ";
}

$query = "SELECT $columns FROM tbl_purchase $whereClause GROUP BY bill_number";

if (empty($fields)) {
   $stmt = $db->select($query);
} else {
   $stmt = $db->select($query, $params, $param_fields);
}

if ($exportType == 'excel') {
   exportResult('purchase-item', $stmt);
   exit();
}
if ($exportType == 'pdf'){
   exportResultPdf('purchase-report', $stmt); 
   exit();
}

$totalRecords = $stmt ? $stmt->num_rows() : 0;

$recordsPerPage = !empty($_SESSION["_RECORDPERPAGE_"]) ? $_SESSION["_RECORDPERPAGE_"] : (defined('_RECORDPERPAGE_') ? _RECORDPERPAGE_ : 20);
$offSet = ($page - 1) * $recordsPerPage;

$query .= " ORDER BY $sortField $sortOrder LIMIT $offSet,$recordsPerPage";

if (empty($fields)) {
   $stmt = $db->select($query);
} else {
   $stmt = $db->select($query, $params, $param_fields);
}

$totalRecordsWithLimit = $stmt ? $stmt->num_rows() : 0;

if ($totalRecordsWithLimit > 0) {
   $html = '';
   for ($i = 1; $i <= $totalRecordsWithLimit; $i++) {
      $row = $stmt->fetch_assoc();

      $html .= '<tr>
         <td>
            <div class="checkbox checkbox-info">
               <input id="checkbox'. $i .'" name="recent-chk[]" type="checkbox" value="'. ($row['id'] ?? 0) .'">
            </div>
         </td>
         <td> <label class="s_no" for="checkbox'. $i .'">'. $i .'</label></td>
         <td>'. dateformat($row['purchage_date'] ?? '') .'</td>
         <td>'. ($row['bill_number'] ?? '') .'</td>
         <td>'. ($row['invoice_number'] ?? '') .'</td>
         <td>'. ($row['account_name'] ?? '') .'</td>
         <td>'. ($row['gst_type'] ?? '') .'</td>
         <td>'. ($row['gst_amount'] ?? 0) .'</td>
         <td>'. ($row['amount'] ?? 0) .'</td>
         <td>';
             if (($row['status'] ?? '') == 'success') { 
               $html .= '<span class="label label-success">'. $row['status'] .'</span>';
             } else { 
               $html .= '<span class="label label-warning">'. ($row['status'] ?? 'pending') .'</span>';
            } 
            $html .= '</td>
         <td style="white-space: nowrap;">
            <a class="btn-add btn-xs" onclick="view_details(' . ($row['id'] ?? 0) . ');" href="javascript:void(0);" data-toggle="tooltip" title="View Details"><i class="fa fa-eye"></i></a>
            <a href="bill-invoice.php?type=purchase&bill_no=' . urlencode($row['bill_number'] ?? '') . '" class="btn-add btn-xs" data-toggle="tooltip" title="View Invoice"><i class="fa fa-file-text-o"></i></a>
            <a href="https://api.whatsapp.com/send?text=' . urlencode('Purchase Bill No: ' . ($row['bill_number'] ?? '')) . '" target="_blank" class="btn-add btn-xs" style="background-color:#25D366; border-color:#25D366;" data-toggle="tooltip" title="WhatsApp"><i class="fa fa-whatsapp"></i></a>
            <a role="button" class="btn-add btn-xs" style="background-color:#0072c6; border-color:#0072c6;" onclick="sendMail(\'' . htmlspecialchars($row['bill_number'] ?? '') . '\');" data-toggle="tooltip" title="Send Email"><i class="fa fa-envelope"></i></a>
            <a href="bill-invoice.php?type=purchase&bill_no=' . urlencode($row['bill_number'] ?? '') . '&auto_print=1" target="_blank" class="btn-add btn-xs" data-toggle="tooltip" title="Print Invoice"><i class="fa fa-print"></i></a>
         </td>
         <td style="white-space: nowrap;">
            <a onClick="return confirm(\'Are you sure you want to Update Bill?\')" href="purchase-master-creation.php?bill_no=' . urlencode($row['bill_number'] ?? '') . '" class="btn-add btn-xs" style="background-color:#337ab7; border-color:#2e6da4;" data-toggle="tooltip" title="Update Bill"><i class="fa fa-pencil"></i></a>
            <a class="btn btn-danger btn-xs" onclick="deleterecord(' . ($row['id'] ?? 0) . ')" href="javascript:void(0);" data-toggle="tooltip" title="Delete"><i class="fa fa-trash"></i></a>
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