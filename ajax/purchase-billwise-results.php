<?php include_once("../include/config.php"); ?>

<?php
$validationHelper = new validation();

$db = connect();

$today = date("Y-m-d");

$page       = $_REQUEST['page'];
$sortOrder    = $_REQUEST['sortOrder'];
$sortField    = $_REQUEST['sortField'];
$formId       = $_REQUEST['formId'];
$searchKey       = $_REQUEST['search'];
$from_date       = $_REQUEST['from_date'];
$to_date       = $_REQUEST['to_date'];
$exportType       = $_REQUEST['export'];

$params = '';
$fields = array();

if ($exportType) {
   $columns = 'ANY_VALUE(purchage_date) as purchage_date, bill_number, ANY_VALUE(invoice_number) as invoice_number, ANY_VALUE(account_name) as account_name, ANY_VALUE(gst_type) as gst_type, SUM(qty) as qty, SUM(CAST(gst_amount AS DECIMAL(10,2))) as gst_amount, SUM(CAST(amount AS DECIMAL(10,2))) as amount, ANY_VALUE(status) as status';
} else {
   $columns = 'ANY_VALUE(id) as id, ANY_VALUE(purchage_date) as purchage_date, bill_number, ANY_VALUE(invoice_number) as invoice_number, ANY_VALUE(account_name) as account_name, ANY_VALUE(gst_type) as gst_type, SUM(qty) as qty, SUM(CAST(gst_amount AS DECIMAL(10,2))) as gst_amount, SUM(CAST(amount AS DECIMAL(10,2))) as amount, ANY_VALUE(status) as status';
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

$query .= " GROUP BY bill_number";

if ($searchKey) {
   $query .= " HAVING (bill_number LIKE CONCAT('%', ?, '%') OR invoice_number LIKE CONCAT('%', ?, '%') OR account_name LIKE CONCAT('%', ?, '%') OR gst_type LIKE CONCAT('%', ?, '%') OR CAST(SUM(qty) AS CHAR) LIKE CONCAT('%', ?, '%') 
        OR CAST(SUM(gst_amount) AS CHAR) LIKE CONCAT('%', ?, '%') 
        OR CAST(SUM(amount) AS CHAR) LIKE CONCAT('%', ?, '%'))";

   $params .= 'sssssss';
   $fields[] = $searchKey;
   $fields[] = $searchKey;
   $fields[] = $searchKey;
   $fields[] = $searchKey;
   $fields[] = $searchKey;
   $fields[] = $searchKey;
   $fields[] = $searchKey;
} 
//echo $query; die;
if (empty($fields)) {

   $stmt = $db->select($query);
} else {

   $stmt = $db->select($query, $params, $fields);
}

if($exportType == 'excel'){
   exportResult('purchase-billwise', $stmt); 
   exit();
}
if($exportType == 'pdf'){
   exportResultPdf('purchase-billwise', $stmt); 
   exit();
}
// Total Calculation
$total_qty = 0;
$total_gst = 0.00;
$total_amt = 0.00;
while($t_row = $stmt->fetch_assoc()){
   $total_qty += $t_row['qty'];
   $total_gst += (float)$t_row['gst_amount'];
   $total_amt += (float)$t_row['amount'];
}

$totalRecords = $stmt->num_rows();

$recordsPerPage = $_SESSION["_RECORDPERPAGE_"] ? $_SESSION["_RECORDPERPAGE_"] : _RECORDPERPAGE_;
$offSet = ($page - 1) * $recordsPerPage;

$query .= " ORDER BY $sortField $sortOrder LIMIT $offSet,$recordsPerPage";

if (empty($fields)) {

   $stmt = $db->select($query);
} else {

   $stmt = $db->select($query, $params, $fields);
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
         <td>' . $row['invoice_number'] . '</td>
         <td>' . $row['account_name'] . '</td>
         <td>' . $row['qty'] . '</td>
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
         <td style="white-space: nowrap;">
            <a class="btn-add btn-xs" onclick="view_details(' . ($row['id'] ?? 0) . ');" href="javascript:void(0);" data-toggle="tooltip" title="View Details"><i class="fa fa-eye"></i></a>
            <a href="bill-invoice.php?type=purchase&bill_no=' . urlencode($row['bill_number'] ?? '') . '" class="btn-add btn-xs" data-toggle="tooltip" title="View Invoice"><i class="fa fa-file-text-o"></i></a>
            <a href="https://api.whatsapp.com/send?text=' . urlencode('Bill No: ' . ($row['bill_number'] ?? '')) . '" target="_blank" class="btn-add btn-xs" style="background-color:#25D366; border-color:#25D366;" data-toggle="tooltip" title="WhatsApp"><i class="fa fa-whatsapp"></i></a>
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
      <td colspan="12"> No record found</td>
   </tr>';
}
$data['total_qty'] = number_format($total_qty);
$data['total_gst'] = number_format($total_gst, 2);
$data['total_amt'] = number_format($total_amt, 2);
$data['htmlData'] = $html;
echo json_encode($data);
die;
