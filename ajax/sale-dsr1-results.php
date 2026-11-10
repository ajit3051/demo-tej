<?php include_once("../include/config.php"); ?>

<?php
$validationHelper = new validation();

$db = connect();

$today = date("Y-m-d");

$page 		= isset($_REQUEST['page']) && $_REQUEST['page'] !== '' ? (int)$_REQUEST['page'] : 1;
if ($page < 1) $page = 1;
$sortOrder 	= !empty($_REQUEST['sortOrder']) ? $_REQUEST['sortOrder'] : 'DESC';
$sortField 	= !empty($_REQUEST['sortField']) ? $_REQUEST['sortField'] : 'purchage_date';
if ($sortField === 'purchage_date') {
   $sortField = 'created_date';
}
$formId 	= $_REQUEST['formId'] ?? '';
$searchKey 	= $_REQUEST['search'] ?? '';
$from_date 	= $_REQUEST['from_date'] ?? '';
$to_date 	= $_REQUEST['to_date'] ?? '';
$exportType = $_REQUEST['export'] ?? '';

$params = '';
$fields = array();

$whereClause = "WHERE id > 0";
if ($formId == 'latestsearchForm') {
   $whereClause .= " AND DATE(COALESCE(created_date, purchage_date)) = ?";
   $params .= 's';
   $fields[] = $today;
}
if ($from_date) {
   $whereClause .= " AND COALESCE(DATE(created_date), purchage_date) >= ?";
   $params .= 's';
   $fields[] = dateInSQLFormat($from_date);
}
if ($to_date) {
   $whereClause .= " AND COALESCE(DATE(created_date), purchage_date) <= ?";
   $params .= 's';
   $fields[] = dateInSQLFormat($to_date);
}

$query = "SELECT 
    MAX(b.purchage_date) as purchage_date, 
    MAX(b.created_date) as created_date, 
    SUM(b.total_qty) AS total_qty,
    COALESCE(SUM(b.bill_cash), 0) AS total_cash,
    COALESCE(SUM(b.bill_upi), 0) AS total_upi,
    COALESCE(SUM(b.bill_card), 0) AS total_card,
    COALESCE(SUM(b.bill_credit), 0) AS total_credit,
    COALESCE(SUM(b.total_gst), 0) as gst_amount, 
    COALESCE(SUM(b.total_amt), 0) as amount,
    COALESCE(SUM(b.total_rate), 0) as total_rate
FROM (
    SELECT 
        bill_number,
        MAX(id) as id,
        MAX(purchage_date) as purchage_date,
        MAX(created_date) as created_date,
        MAX(barcode_number) as barcode_number,
        MAX(account_name) as account_name,
        MAX(gst_type) as gst_type,
        MAX(item_name) as item_name,
        SUM(qty) as total_qty,
        SUM(CAST(gst_amount AS DECIMAL(10,2))) as total_gst,
        SUM(CAST(amount AS DECIMAL(10,2))) as total_amt,
        SUM(CAST(COALESCE(NULLIF(taxable_amount, ''), NULLIF(net_price, ''), rate * qty, 0) AS DECIMAL(10,2))) as total_rate,
        MAX(CAST(NULLIF(pay_cash, '') AS DECIMAL(10,2))) as bill_cash,
        MAX(CAST(NULLIF(pay_upi, '') AS DECIMAL(10,2))) as bill_upi,
        MAX(CAST(NULLIF(pay_card, '') AS DECIMAL(10,2))) as bill_card,
        MAX(CAST(NULLIF(pay_credit, '') AS DECIMAL(10,2))) as bill_credit
    FROM tbl_bill 
    $whereClause
    GROUP BY bill_number
) b
GROUP BY DATE(COALESCE(b.created_date, b.purchage_date))";

if ($searchKey) {
   $query .= " HAVING (MAX(b.barcode_number) LIKE CONCAT('%', ?, '%') OR MAX(b.bill_number) LIKE CONCAT('%', ?, '%') OR MAX(b.account_name) LIKE CONCAT('%', ?, '%') OR MAX(b.gst_type) LIKE CONCAT('%', ?, '%') OR MAX(b.item_name) LIKE CONCAT('%', ?, '%') OR CAST(SUM(b.total_qty) AS CHAR) LIKE CONCAT('%', ?, '%') OR CAST(SUM(b.total_gst) AS CHAR) LIKE CONCAT('%', ?, '%') OR CAST(SUM(b.total_amt) AS CHAR) LIKE CONCAT('%', ?, '%'))";

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
     $total_rate += (float)($t_row['total_rate'] ?? 0);
     $total_gst += (float)($t_row['gst_amount'] ?? 0);
     $total_amt += (float)($t_row['amount'] ?? 0);
   }
}

$totalRecords = $stmt ? $stmt->num_rows() : 0;

$recordsPerPage = $_SESSION["_RECORDPERPAGE_"] ? $_SESSION["_RECORDPERPAGE_"] : _RECORDPERPAGE_;
$offSet = ($page - 1) * $recordsPerPage;

$query .= " ORDER BY MAX(b.id) $sortOrder LIMIT $offSet,$recordsPerPage";

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
         <td>' . dateformat(!empty($row['created_date']) && $row['created_date'] != '0000-00-00 00:00:00' ? $row['created_date'] : $row['purchage_date']) . '</td>
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
