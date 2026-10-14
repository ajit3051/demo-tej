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
   $columns = 'TB.purchage_date, TB.bill_number, TB.pay_mode, TB.account_name, TB.gst_type, SUM(TB.qty) as qty, SUM(CAST(TB.gst_amount AS DECIMAL(10,2))) as gst_amount, SUM(CAST(TB.amount AS DECIMAL(10,2))) as amount, TB.status, COALESCE(TPM.paymode_name, TB.pay_mode) as paymode_name';
} else {
   $columns = 'TB.id, TB.purchage_date, TB.bill_number, TB.pay_mode, TB.account_name, TB.gst_type, SUM(TB.qty) as qty, SUM(CAST(TB.gst_amount AS DECIMAL(10,2))) as gst_amount, SUM(CAST(TB.amount AS DECIMAL(10,2))) as amount, TB.status, COALESCE(TPM.paymode_name, TB.pay_mode) as paymode_name';
}

$query = "SELECT $columns FROM tbl_bill as TB LEFT JOIN tbl_paymode_master as TPM ON (TPM.paymode_code = TB.pay_mode OR COALESCE(TPM.paymode_name, TB.pay_mode) as paymode_name = TB.pay_mode) WHERE TB.id > 0";

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

$query .= " GROUP BY TB.bill_number";

if ($searchKey) {
   $query .= " HAVING (TB.bill_number LIKE CONCAT('%', ?, '%') OR paymode_name LIKE CONCAT('%', ?, '%') OR TB.account_name LIKE CONCAT('%', ?, '%') OR TB.gst_type LIKE CONCAT('%', ?, '%') OR CAST(SUM(TB.qty) AS CHAR) LIKE CONCAT('%', ?, '%') 
        OR CAST(SUM(TB.gst_amount) AS CHAR) LIKE CONCAT('%', ?, '%') 
        OR CAST(SUM(TB.amount) AS CHAR) LIKE CONCAT('%', ?, '%'))";

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
   exportResult('sale-billwise', $stmt); 
   exit();
}

if($exportType == 'pdf'){
   exportResultPdf('sale-billwise', $stmt); 
   exit();
}
// Total Calculation
$total_qty = $cash_qty = $upi_qty = $card_qty = 0;
$total_gst = 0.00;
$total_amt = $cash_amt = $upi_amt = $card_amt = 0.00;

while($t_row = $stmt->fetch_assoc()){
   $total_qty += $t_row['qty'];
   $total_gst += (float)$t_row['gst_amount'];
   $total_amt += (float)$t_row['amount'];

   // CASH
   if($t_row['pay_mode'] == 'PC-0003'){
      $cash_qty += $t_row['qty'];
      $cash_amt += (float)$t_row['amount'];
   }
   // UPI
   if($t_row['pay_mode'] == 'PC-0004'){
      $upi_qty += $t_row['qty'];
      $upi_amt += (float)$t_row['amount'];
   }

   //CARD
   if($t_row['pay_mode'] == 'PC-0005'){
      $card_qty += $t_row['qty'];
      $card_amt += (float)$t_row['amount'];
   }
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
         <td>' . $i . '</td>
         <td>' . dateformat($row['purchage_date']) . '</td>
         <td>' . $row['bill_number'] . '</td>
         <td>' . $row['paymode_name'] . '</td>
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
      <td colspan="12"> No record found</td>
   </tr>';
}
$data['total_qty'] = number_format($total_qty);
$data['total_gst'] = number_format($total_gst, 2);
$data['total_amt'] = number_format($total_amt, 2);
$data['cash_qty'] = number_format($cash_qty);
$data['cash_amt'] = number_format($cash_amt, 2);
$data['upi_qty'] = number_format($upi_qty);
$data['upi_amt'] = number_format($upi_amt, 2);
$data['card_qty'] = number_format($card_qty);
$data['card_amt'] = number_format($card_amt, 2);
$data['htmlData'] = $html;
echo json_encode($data);
die;
