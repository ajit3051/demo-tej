<?php include_once("../include/config.php"); ?>

<?php
$validationHelper = new validation();

$db = connect();

$today = date("Y-m-d");

$page       = $_REQUEST['page'] ?? 1;
$sortOrder  = !empty($_REQUEST['sortOrder']) ? $_REQUEST['sortOrder'] : 'DESC';
$sortField  = !empty($_REQUEST['sortField']) ? $_REQUEST['sortField'] : 'id';
$formId     = $_REQUEST['formId'] ?? '';
$searchKey  = $_REQUEST['search'] ?? '';
$from_date  = $_REQUEST['from_date'] ?? '';
$to_date    = $_REQUEST['to_date'] ?? '';
$exportType = $_REQUEST['export'] ?? '';

$params = '';
$fields = array();

if ($exportType) {
   $columns = 'TB.purchage_date, TB.bill_number, TB.barcode_number, TB.item_name, TB.category, TB.qty, TB.rate, TB.gst_amount, TB.amount, TB.account_name, TB.mobile_number, COALESCE(TPM.paymode_name, TB.pay_mode) as paymode_name';
} else {
   $columns = 'TB.*, COALESCE(TPM.paymode_name, TB.pay_mode) as paymode_name';
}

$query = "SELECT $columns FROM tbl_bill as TB LEFT JOIN tbl_paymode_master as TPM ON (TPM.paymode_code = TB.pay_mode OR TPM.paymode_name = TB.pay_mode) WHERE TB.id > 0";

if ($formId == 'latestsearchForm') {
   $query .= " AND DATE(TB.created_date) = ?";
   $params .= 's';
   $fields[] = $today;
}
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

if ($searchKey) {
   $query .= " AND (TB.bill_number LIKE CONCAT('%', ?, '%') OR TB.account_name LIKE CONCAT('%', ?, '%') OR TB.mobile_number LIKE CONCAT('%', ?, '%') OR TB.item_name LIKE CONCAT('%', ?, '%') OR TB.category LIKE CONCAT('%', ?, '%') OR TB.barcode_number LIKE CONCAT('%', ?, '%') OR CAST(TB.stock_qty AS CHAR) LIKE CONCAT('%', ?, '%') OR CAST(TB.qty AS CHAR) LIKE CONCAT('%', ?, '%') OR CAST(TB.balance_qty AS CHAR) LIKE CONCAT('%', ?, '%') OR CAST(TPM.paymode_name AS CHAR) LIKE CONCAT('%', ?, '%') OR CAST(TB.rate AS CHAR) LIKE CONCAT('%', ?, '%'))";

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

//$query .= " GROUP BY TB.barcode_no";
//echo $query; die;
if (empty($fields)) {

   $stmt = $db->select($query);
} else {

   $stmt = $db->select($query, $params, $fields);
}

if($exportType == 'excel'){
   exportResult('sale-itemwise', $stmt); 
   exit();
}
if($exportType == 'pdf'){
   exportResultPdf('sale-itemwise', $stmt); 
   exit();
}

// Total Calculation
$total_qty = 0;
$qty = 0;
$bal_qty = 0;
$rate = 0.00;
$t_gst = 0.00;
$t_amt = 0.00;
$cash_qty = $cash_amt = $upi_qty = $upi_amt = $card_qty = $card_amt = 0;
while($t_row = $stmt->fetch_assoc()){
   $total_qty += $t_row['stock_qty'] ?? 0;
   $qty += $t_row['qty'] ?? 0;
   $bal_qty += $t_row['balance_qty'] ?? 0;
   $rate += $t_row['rate'] ?? 0;
   $t_gst += $t_row['gst_amount'] ?? 0;
   $t_amt += $t_row['amount'] ?? 0;

   // CASH
   if(($t_row['pay_mode'] ?? '') == 'PC-0003' || strcasecmp(($t_row['pay_mode'] ?? ''), 'CASH') == 0 || strcasecmp(($t_row['paymode_name'] ?? ''), 'CASH') == 0){
      $cash_qty += $t_row['qty'] ?? 0;
      $cash_amt += (float)($t_row['amount'] ?? 0);
   }
   // UPI
   if(($t_row['pay_mode'] ?? '') == 'PC-0004' || strcasecmp(($t_row['pay_mode'] ?? ''), 'UPI') == 0 || strcasecmp(($t_row['paymode_name'] ?? ''), 'UPI') == 0 || strcasecmp(($t_row['paymode_name'] ?? ''), 'UPII') == 0){
      $upi_qty += $t_row['qty'] ?? 0;
      $upi_amt += (float)($t_row['amount'] ?? 0);
   }

   //CARD
   if(($t_row['pay_mode'] ?? '') == 'PC-0005' || strcasecmp(($t_row['pay_mode'] ?? ''), 'CARD') == 0 || strcasecmp(($t_row['paymode_name'] ?? ''), 'CARD') == 0){
      $card_qty += $t_row['qty'] ?? 0;
      $card_amt += (float)($t_row['amount'] ?? 0);
   }
}

$totalRecords = $stmt->num_rows();

$recordsPerPage = (!empty($_SESSION["_RECORDPERPAGE_"])) ? $_SESSION["_RECORDPERPAGE_"] : (defined('_RECORDPERPAGE_') ? _RECORDPERPAGE_ : 20);
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
         <td>' . $i . '</td>
         <td>' . dateformat($row['purchage_date'] ?? '') . '</td>
         <td>' . ($row['bill_number'] ?? '') . '</td>
         <td>' . ($row['barcode_number'] ?? '') . '</td>
         <td>' . ($row['item_name'] ?? '') . '</td>
         <td>' . ($row['category'] ?? '') . '</td>
         <!--td>' . ($row['stock_qty'] ?? '') . '</td-->
         <td>' . ($row['qty'] ?? 0) . '</td>
         <!--td>' . ($row['balance_qty'] ?? '') . '</td-->
         <td>' . ($row['rate'] ?? 0) . '</td>
         <td>' . ($row['gst_amount'] ?? 0) . '</td>
         <td>' . ($row['amount'] ?? 0) . '</td>
         <td>' . ($row['account_name'] ?? '') . '</td>
         <td>' . ($row['mobile_number'] ?? '') . '</td>
         <td>' . ($row['paymode_name'] ?? '') . '</td>
          <td>
            <a class="btn btn-info btn-xs" onclick="view_details(' . ($row['id'] ?? 0) . ')" href="javascript:void(0);" data-toggle="tooltip" title="View & Print Invoice"><i class="fa fa-eye"></i></a>
            <a class="" onClick="return confirm(\'Are you sure you want to Update Bill?\')" href="sale-creations.php?bill_no=' . urlencode($row['bill_number'] ?? '') . '"><button data-toggle="tooltip" title="Update Bill" type="button" class="btn-add btn-xs"><i class="fa fa-pencil"></i><span style=""></span></button></a>

            <a class="btn-danger btn btn-xs" onclick="deleterecord(' . ($row['id'] ?? 0) . ')" href="javascript:void(0);" data-toggle="tooltip" title="Delete">

               <i class="fa fa-trash-o"></i><span></span></button></a>

         </td>
         </tr>';
   }

   $data['pagination'] = include_pagination_component($page, $recordsPerPage, $totalRecords);
} else {
   $html = '<tr>
      <td colspan="25"> No record found</td>
   </tr>';
}
$data['htmlData'] = $html;
$data['total_qty'] = number_format($total_qty);
$data['t_qty'] = number_format($qty);
$data['t_bal_qty'] = number_format($bal_qty);
$data['t_rate'] = number_format($rate, 2);
$data['t_gst'] = number_format($t_gst, 2);
$data['t_amt'] = number_format($t_amt, 2);
$data['cash_qty'] = number_format($cash_qty);
$data['cash_amt'] = number_format($cash_amt, 2);
$data['upi_qty'] = number_format($upi_qty);
$data['upi_amt'] = number_format($upi_amt, 2);
$data['card_qty'] = number_format($card_qty);
$data['card_amt'] = number_format($card_amt, 2);

echo json_encode($data);
die;
