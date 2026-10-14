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
   $columns = '
    TP.purchage_date as stock_date, 
    TB.purchage_date AS sale_date,
    TP.barcode_number, 
    TP.item_name, 
    TP.category,  
    TP.rate, 
    TP.total_gst_amount AS gst_amount, 
    TP.total_qty AS stock_qty,
    COALESCE(SUM(TB.qty), 0) AS sale_qty, 
    (TP.total_qty - COALESCE(SUM(TB.qty), 0)) AS balance_qty,
    TP.total_amount AS stock_bill_amount, 
    SUM(CAST(TB.amount AS DECIMAL(10,2))) AS total_bill_amount,
    TP.total_amount - SUM(CAST(TB.amount AS DECIMAL(10,2))) AS balance_amount';
} else {
   $columns = 'TP.id, 
    TP.purchage_date, 
    TP.barcode_number, 
    TP.invoice_number, 
    TP.account_name, 
    TP.gst_type, 
    TP.item_name, 
    TP.category, 
    TP.item_type, 
    TP.rate, 
    TP.status, 
    TP.total_qty AS qty, 
    TP.total_gst_amount AS gst_amount, 
    TP.total_amount AS amount, 
    COALESCE(SUM(TB.qty), 0) AS used_qty, 
    (TP.total_qty - COALESCE(SUM(TB.qty), 0)) AS bal_qty,
    TB.purchage_date AS sale_date,
    SUM(CAST(TB.gst_amount AS DECIMAL(10,2))) AS total_bill_gst_amount, 
    SUM(CAST(TB.amount AS DECIMAL(10,2))) AS total_bill_amount,
    TP.total_amount - SUM(CAST(TB.amount AS DECIMAL(10,2))) AS balance_amount';
}

$query = "SELECT 
    $columns
FROM 
    (SELECT 
        id, 
        purchage_date, 
        barcode_number, 
        invoice_number, 
        account_name, 
        gst_type, 
        item_name, 
        category, 
        item_type, 
        rate, 
        status, 
        SUM(qty) AS total_qty, 
        CAST(gst_amount AS DECIMAL(10,2)) AS total_gst_amount, 
        SUM(CAST(amount AS DECIMAL(10,2))) AS total_amount 
     FROM tbl_purchase 
     GROUP BY barcode_number) AS TP 
LEFT JOIN tbl_bill AS TB ON TB.barcode_number = TP.barcode_number 
WHERE TP.id > 0";

if ($formId == 'latestsearchForm') {
   $query .= " AND DATE(TP.created_date) = ?";
   $params .= 's';
   $fields[] = $today;
}
/* if ($from_date) {
   $query .= " AND (TP.purchage_date >= ? OR TB.purchage_date >= ?)";
   $params .= 'ss';
   $fields[] = dateInSQLFormat($from_date);
   $fields[] = dateInSQLFormat($from_date);
}
if ($to_date) {
   $query .= " AND (TP.purchage_date <= ? OR TB.purchage_date <= ?)";
   $params .= 'ss';
   $fields[] = dateInSQLFormat($to_date);
   $fields[] = dateInSQLFormat($to_date);
} */
if ($from_date) {
   $query .= " AND TB.purchage_date >= ?";
   $params .= 's';
   $fields[] = dateInSQLFormat($from_date);
}
if ($to_date) {
   $query .= " AND TB.purchage_date <= ?";
   $params .= 's';
   $fields[] = dateInSQLFormat($to_date);
}

$query .= " GROUP BY TP.barcode_number";

if ($searchKey) {
   $query .= " HAVING (TP.barcode_number LIKE CONCAT('%', ?, '%') OR TP.invoice_number LIKE CONCAT('%', ?, '%') OR TP.account_name LIKE CONCAT('%', ?, '%') OR TP.gst_type LIKE CONCAT('%', ?, '%') OR TP.item_name LIKE CONCAT('%', ?, '%') OR TP.category LIKE CONCAT('%', ?, '%') OR TP.item_type LIKE CONCAT('%', ?, '%') OR TP.rate LIKE CONCAT('%', ?, '%') OR qty LIKE CONCAT('%', ?, '%') OR used_qty LIKE CONCAT('%', ?, '%') OR bal_qty LIKE CONCAT('%', ?, '%') OR gst_amount LIKE CONCAT('%', ?, '%') OR amount LIKE CONCAT('%', ?, '%'))";

   $params .= 'sssssssssssss';
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
      exportResult('purchase-report', $stmt); 
      exit();
   }

   if($exportType == 'pdf'){
      exportResultPdf('purchase-report', $stmt); 
      exit();
   }
  
  
   


// Total Calculation
$total_qty = 0;
$total_used_qty = 0;
$total_bal_qty = 0;
$total_gst = 0.00;
$total_amt = 0.00;
while($t_row = $stmt->fetch_assoc()){
   $total_qty += $t_row['qty'];
   $total_used_qty += $t_row['used_qty'];
   $total_bal_qty += $t_row['bal_qty'];
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
         <td>' . dateformat($row['sale_date']) . '</td>
         <td>' . $row['barcode_number'] . '</td>
         <td>' . $row['item_name'] . '</td>
         <td>' . $row['category'] . '</td>
         <td>' . $row['rate'] . '</td>
         <td>' . $row['total_bill_gst_amount'] . '</td>
         <td>' . $row['qty'] . '</td>
         <td>' . $row['used_qty'] . '</td>
         <td>' . $row['bal_qty'] . '</td>
         <td>' . $row['amount'] . '</td>
         <td>' . $row['total_bill_amount'] . '</td>
         <td>' . $row['balance_amount'] . '</td>
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
$data['total_used_qty'] = number_format($total_used_qty);
$data['total_bal_qty'] = number_format($total_bal_qty);
$data['total_gst'] = number_format($total_gst, 2);
$data['total_amt'] = number_format($total_amt, 2);
echo json_encode($data);
die;
