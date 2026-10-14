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

$query = "SELECT id, purchage_date, barcode_number, invoice_number, account_name, gst_type, item_name, category, item_type, rate, SUM(qty) as qty, SUM(CAST(gst_amount AS DECIMAL(10,2))) as gst_amount, SUM(CAST(amount AS DECIMAL(10,2))) as amount, status FROM tbl_purchase WHERE id > 0";

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

$query .= " GROUP BY barcode_number";

if ($searchKey) {
   $query .= " HAVING (barcode_number LIKE CONCAT('%', ?, '%') OR invoice_number LIKE CONCAT('%', ?, '%') OR account_name LIKE CONCAT('%', ?, '%') OR gst_type LIKE CONCAT('%', ?, '%') OR item_name LIKE CONCAT('%', ?, '%') OR category LIKE CONCAT('%', ?, '%') OR item_type LIKE CONCAT('%', ?, '%') OR rate LIKE CONCAT('%', ?, '%') OR CAST(SUM(qty) AS CHAR) LIKE CONCAT('%', ?, '%') OR CAST(SUM(gst_amount) AS CHAR) LIKE CONCAT('%', ?, '%') OR CAST(SUM(amount) AS CHAR) LIKE CONCAT('%', ?, '%'))";

   $params .= 'sssssssssss';
   $fields[] = $searchKey;
   $fields[] = $searchKey;
   $fields[] = $searchKey;
   $fields[] = $searchKey;
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
   exportResult('sale-report', $stmt); 
   exit();
}
if($exportType == 'pdf'){
   exportResultPdf('sale-report', $stmt); 
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
         <td>' . $i . ' </td>
         <td>' . dateformat($row['purchage_date']) . '</td>
         <td>' . $row['barcode_number'] . '</td>
         <td>' . $row['item_name'] . '</td>
         <td>' . $row['category'] . '</td>
         <td>' . $row['item_type'] . '</td>
         <td>' . $row['rate'] . '</td>
         <td>' . $row['qty'] . '</td>
         <td>' . $row['amount'] . '</td>
      </tr>';
   }

   $data['pagination'] = include_pagination_component($page, $recordsPerPage, $totalRecords);
} else {
   $html = '<tr>
      <td colspan="11"> No record found</td>
   </tr>';
}
$data['htmlData'] = $html;
$data['total_qty'] = number_format($total_qty);
$data['total_gst'] = number_format($total_gst, 2);
$data['total_amt'] = number_format($total_amt, 2);
echo json_encode($data);
die;
