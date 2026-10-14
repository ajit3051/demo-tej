<?php
ob_start();
include_once("../include/config.php");

$validationHelper = new validation();

$db = connect();

$today = date("Y-m-d");

$page       = isset($_REQUEST['page']) && (int)$_REQUEST['page'] > 0 ? (int)$_REQUEST['page'] : 1;
$sortOrder  = !empty($_REQUEST['sortOrder']) && in_array(strtoupper($_REQUEST['sortOrder']), array('ASC', 'DESC')) ? strtoupper($_REQUEST['sortOrder']) : 'DESC';
$sortField  = !empty($_REQUEST['sortField']) ? $_REQUEST['sortField'] : 'id';
$formId     = $_REQUEST['formId'] ?? '';
$searchKey  = isset($_REQUEST['search']) ? trim($_REQUEST['search']) : '';
$from_date  = $_REQUEST['from_date'] ?? '';
$to_date    = $_REQUEST['to_date'] ?? '';
$exportType = $_REQUEST['export'] ?? '';

$params = '';
$fields = array();

if ($exportType) {
   $columns = 'purchage_date, bill_number, invoice_number, invoice_date, account_name, barcode_number, item_name, category, brand, size, qty, mou_name, rate, purchase_price, percent_discount, discount_amt, net_price, gst,  gst_amount, taxable_amount, amount';
} else {
   $columns = 'id, purchage_date, bill_number, invoice_number, invoice_date, account_name, barcode_number, item_name, category, brand, size, qty, mou_name, rate, purchase_price, percent_discount, discount_amt, net_price, gst,  gst_amount, taxable_amount, amount';
}

$query = "SELECT $columns FROM tbl_purchase WHERE id > 0";

if ($formId == 'latestsearchForm') {
   $query .= " AND DATE(created_date) = ?";
   $params .= 's';
   $fields[] = $today;
}
if ($from_date) {
   $query .= " AND purchage_date >= ?";
   $params .= 's';
   $fields[] = dateInSQLFormat($from_date);
}
if ($to_date) {
   $query .= " AND purchage_date <= ?";
   $params .= 's';
   $fields[] = dateInSQLFormat($to_date);
}

if ($searchKey !== '') {
   $query .= " AND (bill_number LIKE CONCAT('%', ?, '%') OR invoice_number LIKE CONCAT('%', ?, '%') OR barcode_number LIKE CONCAT('%', ?, '%') OR account_name LIKE CONCAT('%', ?, '%') OR gst_type LIKE CONCAT('%', ?, '%') OR CAST(qty AS CHAR) LIKE CONCAT('%', ?, '%') 
        OR CAST(gst_amount AS CHAR) LIKE CONCAT('%', ?, '%') 
        OR CAST(amount AS CHAR) LIKE CONCAT('%', ?, '%'))";

   $params .= 'ssssssss';
   for ($k = 0; $k < 8; $k++) {
      $fields[] = $searchKey;
   }
}

if (empty($fields)) {
   $stmt = $db->select($query);
} else {
   $stmt = $db->select($query, $params, $fields);
}

if ($exportType == 'excel') {
   exportResult('purchase-itemwise', $stmt); 
   exit();
}

if ($exportType == 'pdf') {
   exportResultPdf('purchase-itemwise', $stmt); 
   exit();
}

// Total Calculation
$total_qty = 0;
$total_gst = 0.00;
$total_amt = 0.00;
if ($stmt) {
   while ($t_row = $stmt->fetch_assoc()) {
      $total_qty += (int)($t_row['qty'] ?? 0);
      $total_gst += (float)($t_row['gst_amount'] ?? 0);
      $total_amt += (float)($t_row['amount'] ?? 0);
   }
}

$totalRecords = $stmt ? $stmt->num_rows() : 0;

$recordsPerPage = isset($_SESSION["_RECORDPERPAGE_"]) && $_SESSION["_RECORDPERPAGE_"] ? (int)$_SESSION["_RECORDPERPAGE_"] : (defined('_RECORDPERPAGE_') ? _RECORDPERPAGE_ : 20);
$offSet = max(0, ($page - 1) * $recordsPerPage);

$query .= " ORDER BY $sortField $sortOrder LIMIT $offSet, $recordsPerPage";

if (empty($fields)) {
   $stmt = $db->select($query);
} else {
   $stmt = $db->select($query, $params, $fields);
}

$totalRecordsWithLimit = $stmt ? $stmt->num_rows() : 0;

$html = '';

if ($totalRecordsWithLimit > 0) {
   for ($i = 1; $i <= $totalRecordsWithLimit; $i++) {
      $row = $stmt->fetch_assoc();
      $sr_no = $offSet + $i;

      $html .= '<tr>
         <td>
            <div class="checkbox checkbox-info">
               <input id="checkbox' . $sr_no . '" name="recent-chk[]" type="checkbox" value="' . ($row['id'] ?? '') . '">
               <label class="s_no" for="checkbox' . $sr_no . '">' . $sr_no . '</label>
            </div>
         </td>
         <td style="white-space: nowrap;">
            <a class="btn-add btn-xs" onclick="view_details(' . ($row['id'] ?? 0) . ');" href="javascript:void(0);" data-toggle="tooltip" title="View Details"><i class="fa fa-eye"></i></a>
            <a href="bill-invoice.php?type=purchase&bill_no=' . urlencode($row['bill_number'] ?? '') . '" class="btn-add btn-xs" data-toggle="tooltip" title="View Invoice"><i class="fa fa-file-text-o"></i></a>
            <a href="bill-invoice.php?type=purchase&bill_no=' . urlencode($row['bill_number'] ?? '') . '&auto_print=1" target="_blank" class="btn-add btn-xs" data-toggle="tooltip" title="Print Invoice"><i class="fa fa-print"></i></a>
         </td>
         <td style="white-space: nowrap;">
            <a href="https://api.whatsapp.com/send?text=' . urlencode('Bill No: ' . ($row['bill_number'] ?? '')) . '" target="_blank" class="btn-add btn-xs" style="background-color:#25D366; border-color:#25D366;" data-toggle="tooltip" title="WhatsApp"><i class="fa fa-whatsapp"></i></a>
            <a role="button" class="btn-add btn-xs" style="background-color:#0072c6; border-color:#0072c6;" onclick="sendMail(\'' . htmlspecialchars($row['bill_number'] ?? '') . '\');" data-toggle="tooltip" title="Send Email"><i class="fa fa-envelope"></i></a>
         </td>
          <td style="white-space: nowrap;">
             <a role="button" class="btn-add btn-xs" style="background-color:#f0ad4e; border-color:#eea236; color:#fff;" onclick="openStockTransferModal(' . ($row['id'] ?? 0) . ');" data-toggle="tooltip" title="Stock Transfer"><i class="fa fa-exchange"></i></a>
             <a onClick="return confirm(\'Are you sure you want to Update Bill?\')" href="purchase-master-creation.php?bill_no=' . urlencode($row['bill_number'] ?? '') . '" class="btn-add btn-xs" style="background-color:#337ab7; border-color:#2e6da4;" data-toggle="tooltip" title="Update Bill"><i class="fa fa-pencil"></i></a>
             <a class="btn btn-danger btn-xs" onclick="deleterecord(' . ($row['id'] ?? 0) . ')" href="javascript:void(0);" data-toggle="tooltip" title="Delete"><i class="fa fa-trash"></i></a>
          </td>
         <td>' . dateformat($row['purchage_date'] ?? '') . '</td>
         <td>' . htmlspecialchars($row['bill_number'] ?? '') . '</td>
         <td>' . htmlspecialchars($row['invoice_number'] ?? '') . '</td>
         <td>' . dateformat($row['invoice_date'] ?? '') . '</td>
         <td>' . htmlspecialchars($row['account_name'] ?? '') . '</td>
         <td>' . htmlspecialchars($row['barcode_number'] ?? '') . '</td>
         <td>' . htmlspecialchars($row['item_name'] ?? '') . '</td>
         <td>' . htmlspecialchars($row['category'] ?? $row['item_type'] ?? '') . '</td>
         <td>' . htmlspecialchars($row['brand'] ?? '') . '</td>
         <td>' . htmlspecialchars($row['size'] ?? '') . '</td>
         <td>' . htmlspecialchars($row['qty'] ?? '') . '</td>
         <td>' . htmlspecialchars($row['mou_name'] ?? '') . '</td>
         <td>' . htmlspecialchars($row['rate'] ?? '') . '</td>
         <td>' . htmlspecialchars($row['purchase_price'] ?? '') . '</td>
         <td>' . htmlspecialchars($row['percent_discount'] ?? '') . '</td>
         <td>' . htmlspecialchars($row['discount_amt'] ?? '') . '</td>
         <td>' . htmlspecialchars($row['net_price'] ?? '') . '</td>
         <td>' . htmlspecialchars($row['gst'] ?? '') . '</td>
         <td>' . htmlspecialchars($row['gst_amount'] ?? '') . '</td>
         <td>' . htmlspecialchars($row['taxable_amount'] ?? '') . '</td>
         <td>' . htmlspecialchars($row['amount'] ?? '') . '</td>
         </tr>';
   }

   $data['pagination'] = include_pagination_component($page, $recordsPerPage, $totalRecords);
} else {
   $html = '<tr>
      <td colspan="25" class="text-center text-muted">No record found</td>
   </tr>';
   $data['pagination'] = '';
}
$data['htmlData'] = $html;
$data['total_qty'] = number_format($total_qty);
$data['total_gst'] = number_format($total_gst, 2);
$data['total_amt'] = number_format($total_amt, 2);

if (ob_get_length()) {
    ob_clean();
}
header('Content-Type: application/json');
echo json_encode($data);
exit();
?>
