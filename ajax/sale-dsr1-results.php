<?php include_once("../include/config.php"); ?>

<?php
$validationHelper = new validation();

$db = connect();

$today = date("Y-m-d");

$page 		= isset($_REQUEST['page']) && $_REQUEST['page'] !== '' ? (int)$_REQUEST['page'] : 1;
if ($page < 1) $page = 1;
$sortOrder 	= !empty($_REQUEST['sortOrder']) ? $_REQUEST['sortOrder'] : 'DESC';
$sortField 	= !empty($_REQUEST['sortField']) ? $_REQUEST['sortField'] : 'purchage_date';
$formId 	= $_REQUEST['formId'] ?? '';
$searchKey 	= $_REQUEST['search'] ?? '';
$from_date 	= $_REQUEST['from_date'] ?? '';
$to_date 	= $_REQUEST['to_date'] ?? '';
$exportType = $_REQUEST['export'] ?? '';

$params = '';
$fields = array();

$query = "SELECT 
    ANY_VALUE(purchage_date) as purchage_date, 
    SUM(qty) AS total_qty,
    SUM(CAST(pay_cash AS DECIMAL(10,2))) AS total_cash,
    SUM(CAST(pay_upi AS DECIMAL(10,2))) AS total_upi,
    SUM(CAST(pay_card AS DECIMAL(10,2))) AS total_card,
    SUM(CAST(pay_credit AS DECIMAL(10,2))) AS total_credit,
    SUM(CAST(gst_amount AS DECIMAL(10,2))) as gst_amount, 
    SUM(CAST(amount AS DECIMAL(10,2))) as amount,
    SUM(CAST(purchase_price AS DECIMAL(10,2))) as purchase_price
FROM tbl_bill 
WHERE id > 0 ";

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

$query .= " GROUP BY DATE(purchage_date)";

if ($searchKey) {
   $query .= " HAVING (ANY_VALUE(barcode_number) LIKE CONCAT('%', ?, '%') OR ANY_VALUE(bill_number) LIKE CONCAT('%', ?, '%') OR ANY_VALUE(account_name) LIKE CONCAT('%', ?, '%') OR ANY_VALUE(gst_type) LIKE CONCAT('%', ?, '%') OR ANY_VALUE(item_name) LIKE CONCAT('%', ?, '%') OR CAST(SUM(qty) AS CHAR) LIKE CONCAT('%', ?, '%') OR CAST(SUM(gst_amount) AS CHAR) LIKE CONCAT('%', ?, '%') OR CAST(SUM(amount) AS CHAR) LIKE CONCAT('%', ?, '%'))";

   $params .= 'ssssssss';
   $fields[] = $searchKey;
   $fields[] = $searchKey;
   $fields[] = $searchKey;
   $fields[] = $searchKey;
   $fields[] = $searchKey;
   $fields[] = $searchKey;
   $fields[] = $searchKey;
   $fields[] = $searchKey;
}

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
$total_rate = 0.00;
$total_amt = 0.00;
if ($stmt) {
   while($t_row = $stmt->fetch_assoc()){
     $total_qty += (float)($t_row['total_qty'] ?? 0);
     $total_rate += (float)($t_row['purchase_price'] ?? 0);
     $total_gst += (float)($t_row['gst_amount'] ?? 0);
     $total_amt += (float)($t_row['amount'] ?? 0);
   }
}

$totalRecords = $stmt ? $stmt->num_rows() : 0;

$recordsPerPage = $_SESSION["_RECORDPERPAGE_"] ? $_SESSION["_RECORDPERPAGE_"] : _RECORDPERPAGE_;
$offSet = ($page - 1) * $recordsPerPage;

$query .= " ORDER BY ANY_VALUE(id) $sortOrder LIMIT $offSet,$recordsPerPage";

if (empty($fields)) {
   $stmt = $db->select($query);
} else {
   $stmt = $db->select($query, $params, $fields);
}

$totalRecordsWithLimit = $stmt ? $stmt->num_rows() : 0;

if ($totalRecordsWithLimit > 0) {
   $html = '';
  
   for ($i = 1; $i <= $totalRecordsWithLimit; $i++) {
      $row = $stmt->fetch_assoc();

      $html .= '<tr>
         <td>' . ($offSet + $i) . ' </td>
         <td>' . dateformat($row['purchage_date']) . '</td>
         <td>' . ($row['total_qty'] ?? 0) . '</td>
         <td>' . number_format((float)($row['total_cash'] ?? 0), 2) . '</td>
         <td>' . number_format((float)($row['total_upi'] ?? 0), 2) . '</td>
         <td>' . number_format((float)($row['total_card'] ?? 0), 2) . '</td>
         <td>' . number_format((float)($row['total_credit'] ?? 0), 2) . '</td>
      </tr>';
   }

   $data['pagination'] = include_pagination_component($page, $recordsPerPage, $totalRecords);
} else {
   $html = '<tr>
      <td colspan="7"> No record found</td>
   </tr>';
}
$data['htmlData'] = $html;
$data['total_qty'] = number_format($total_qty);
$data['total_gst'] = number_format($total_gst, 2);
$data['total_rate'] = number_format($total_rate, 2);
$data['total_amt'] = number_format($total_amt, 2);
echo json_encode($data);
die;
