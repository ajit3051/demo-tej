<?php
ob_start();
include_once("../include/config.php");

$validationHelper = new validation();

$db = connect();

$today = date("Y-m-d");

$page       = isset($_REQUEST['page']) && (int)$_REQUEST['page'] > 0 ? (int)$_REQUEST['page'] : 1;
$sortOrder  = !empty($_REQUEST['sortOrder']) && in_array(strtoupper($_REQUEST['sortOrder']), array('ASC', 'DESC')) ? strtoupper($_REQUEST['sortOrder']) : 'DESC';
$sortField  = !empty($_REQUEST['sortField']) ? $_REQUEST['sortField'] : 'id';
$searchKey  = isset($_REQUEST['search']) ? trim($_REQUEST['search']) : '';
$store_id   = isset($_REQUEST['store_id']) ? (int)$_REQUEST['store_id'] : 0;
$from_date  = $_REQUEST['from_date'] ?? '';
$to_date    = $_REQUEST['to_date'] ?? '';
$exportType = $_REQUEST['export'] ?? '';

$params = '';
$fields = array();

$columns = 'id, purchase_id, store_id, store_name, bill_number, invoice_number, account_name, barcode_number, item_name, item_type, category, brand, size, qty, mou_name, rate, purchase_price, mrp, selling_price, gst, gst_amount, taxable_amount, amount, status, created_date, updated_date';

$query = "SELECT $columns FROM tbl_store_items WHERE id > 0";

if ($store_id > 0) {
   $query .= " AND store_id = ?";
   $params .= 'i';
   $fields[] = $store_id;
}
if ($from_date) {
   $query .= " AND DATE(created_date) >= ?";
   $params .= 's';
   $fields[] = dateInSQLFormat($from_date);
}
if ($to_date) {
   $query .= " AND DATE(created_date) <= ?";
   $params .= 's';
   $fields[] = dateInSQLFormat($to_date);
}

if ($searchKey !== '') {
   $query .= " AND (store_name LIKE CONCAT('%', ?, '%') OR bill_number LIKE CONCAT('%', ?, '%') OR invoice_number LIKE CONCAT('%', ?, '%') OR barcode_number LIKE CONCAT('%', ?, '%') OR item_name LIKE CONCAT('%', ?, '%') OR account_name LIKE CONCAT('%', ?, '%') OR category LIKE CONCAT('%', ?, '%') OR brand LIKE CONCAT('%', ?, '%'))";
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
   exportResult('store-items-list', $stmt); 
   exit();
}

if ($exportType == 'pdf') {
   exportResultPdf('store-items-list', $stmt); 
   exit();
}

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

       $status_val = $row['status'] ?? 'active';
       if ($status_val === 'moved in stock') {
           $status_badge = '<span class="label label-warning">moved in stock</span>';
           $return_btn = '<a class="btn btn-default btn-xs disabled" href="javascript:void(0);" data-toggle="tooltip" title="Already Moved in Stock" style="margin-right:3px;"><i class="fa fa-undo"></i></a>';
       } else {
           $status_badge = '<span class="label label-success">' . htmlspecialchars($status_val) . '</span>';
           $return_btn = '<a class="btn btn-warning btn-xs" onclick="returnStoreItem(' . ($row['id'] ?? 0) . ')" href="javascript:void(0);" data-toggle="tooltip" title="Return Item (Move to Stock)" style="margin-right:3px;"><i class="fa fa-undo"></i></a>';
       }

       $html .= '<tr>
          <td>
             <div class="checkbox checkbox-info">
                <input id="checkbox' . $sr_no . '" name="recent-chk[]" class="chk-item" type="checkbox" value="' . ($row['id'] ?? '') . '">
                <label class="s_no" for="checkbox' . $sr_no . '">' . $sr_no . '</label>
             </div>
          </td>
          <td style="white-space: nowrap;">
             <a class="btn btn-info btn-xs" onclick="viewStoreItemDetails(' . ($row['id'] ?? 0) . ')" href="javascript:void(0);" data-toggle="tooltip" title="View Details" style="margin-right:3px;"><i class="fa fa-eye"></i></a>
             ' . $return_btn . '
             <a class="btn btn-danger btn-xs" onclick="deleterecord(' . ($row['id'] ?? 0) . ')" href="javascript:void(0);" data-toggle="tooltip" title="Delete"><i class="fa fa-trash"></i></a>
          </td>
          <td>' . $status_badge . '</td>
          <td>' . htmlspecialchars($row['store_name'] ?? '') . '</td>
          <td>' . dateformat($row['created_date'] ?? '') . '</td>
          <td>' . htmlspecialchars($row['bill_number'] ?? '') . '</td>
          <td>' . htmlspecialchars($row['invoice_number'] ?? '') . '</td>
          <td>' . htmlspecialchars($row['account_name'] ?? '') . '</td>
          <td>' . htmlspecialchars($row['barcode_number'] ?? '') . '</td>
          <td>' . htmlspecialchars($row['item_name'] ?? '') . '</td>
          <td>' . htmlspecialchars($row['category'] ?? '') . '</td>
          <td>' . htmlspecialchars($row['brand'] ?? '') . '</td>
          <td>' . htmlspecialchars($row['size'] ?? '') . '</td>
          <td><strong>' . htmlspecialchars($row['qty'] ?? '') . '</strong></td>
          <td>' . htmlspecialchars($row['mou_name'] ?? '') . '</td>
          <td>' . htmlspecialchars($row['purchase_price'] ?? '') . '</td>
          <td>' . htmlspecialchars($row['gst'] ?? '') . '</td>
          <td>' . htmlspecialchars($row['gst_amount'] ?? '') . '</td>
          <td>' . htmlspecialchars($row['taxable_amount'] ?? '') . '</td>
          <td>' . htmlspecialchars($row['amount'] ?? '') . '</td>
          </tr>';
   }
   $data['pagination'] = include_pagination_component($page, $recordsPerPage, $totalRecords);
} else {
   $html = '<tr><td colspan="20" class="text-center">No Store Transfer Records Found</td></tr>';
   $data['pagination'] = '';
}

$data['htmlData'] = $html;
$data['total_qty'] = $total_qty;
$data['total_gst'] = number_format($total_gst, 2);
$data['total_amt'] = number_format($total_amt, 2);
echo json_encode($data);
die;
