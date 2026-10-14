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
$from_date       = $_REQUEST['from-date'];
$to_date       = $_REQUEST['to-date'];
$exportType       = $_REQUEST['export'];

$params = '';
$fields = array();

if ($formId == 'billwisesearchForm') {
    if ($exportType) {
        if (!isset($_REQUEST['export_data'])) {
            $columns = 'id, status, created_date, bill_number, invoice_number, invoice_date, account_name, barcode_number, article_no, item_name, category, brand, size, qty, mou_name, rate, mrp, percent_discount, selling_price';
        } else {
            $columns =  implode(', ', $_REQUEST['export_data']);
        }
    } else {
       $columns = 'id, status, created_date, bill_number, invoice_number, invoice_date, account_name, barcode_number, article_no, item_name, category, brand, size, qty, mou_name, rate, mrp, percent_discount, selling_price';
    }
} else {
    if ($exportType) {
    
        if (!isset($_REQUEST['export_data'])) {
            $columns = 'created_date, bill_number, invoice_number, invoice_date, account_name, barcode_number, article_no, item_name, category, brand, size, qty, mou_name, rate, mrp, percent_discount, selling_price';
        } else {
            $columns =  implode(', ', $_REQUEST['export_data']);
        }
       
    } else {
       $columns = 'id, status, created_date, bill_number, invoice_number, invoice_date, account_name, barcode_number, article_no, item_name, category, brand, size, qty, mou_name, rate, mrp, percent_discount, selling_price';
    }
}

$query = "SELECT $columns FROM tbl_purchase WHERE id > 0";

if ($formId == 'latestsearchForm') {
   $query .= " AND DATE(created_date) = ?";
   $params .= 's';
   $fields[] = $today;
}
if ($from_date) {
    $formattedFrom = DateTime::createFromFormat('d/m/Y', $from_date);
    if ($formattedFrom) {
        $query .= " AND DATE(created_date) >= ?";
        $params .= 's';
        $fields[] = xssSafe($formattedFrom->format('Y-m-d'));
    }
}

if ($to_date) {
    $formattedTo = DateTime::createFromFormat('d/m/Y', $to_date);
    if ($formattedTo) {
        $query .= " AND DATE(created_date) <= ?";
        $params .= 's';
        $fields[] = xssSafe($formattedTo->format('Y-m-d'));
    }
}

if ($searchKey) {
    $query .= " AND (
        bill_number LIKE CONCAT('%', ?, '%') 
        OR invoice_number LIKE CONCAT('%', ?, '%') 
        OR account_name LIKE CONCAT('%', ?, '%') 
        OR item_name LIKE CONCAT('%', ?, '%') 
        OR category LIKE CONCAT('%', ?, '%') 
        OR brand LIKE CONCAT('%', ?, '%') 
        OR barcode_number LIKE CONCAT('%', ?, '%') 
        OR qty LIKE CONCAT('%', ?, '%') 
        OR rate LIKE CONCAT('%', ?, '%') 
        OR mrp LIKE CONCAT('%', ?, '%') 
        OR selling_price LIKE CONCAT('%', ?, '%')
    )";

    // Append 11 's' characters for string parameters
    $params .= str_repeat('s', 11);

    // Push $searchKey 11 times dynamically
    for ($i = 0; $i < 11; $i++) {
        $fields[] = $searchKey;
    }
}

if ($formId == 'billwisesearchForm') {
    $query .= " GROUP BY bill_number";
}
// echo $query; die;
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
while($t_row = $stmt->fetch_assoc()){
  $total_qty += $t_row['total_qty'];
  $total_rate += (float)$t_row['purchase_price'];
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
         <td>';
        if ($row['status'] == 'success') {
             $html .= '<span class="label label-success">' . $row['status'] . '</span>';
          } else {
             $html .= '<span class="label label-warning">' . $row['status'] . '</span>';
          }
          $html .= '</td>
         <td>' . dateformat($row['created_date'], 1) . '</td>
         <td>' . $row['bill_number'] . '</td>
         <td>' . $row['s_code'] . '</td>
         <td>' . $row['store_name'] . '</td>
         <td>' . $row['location'] . '</td>
         <td>' . $row['barcode_number'] . '</td>
         <td>' . $row['article_no'] . '</td>
         <td>' . $row['item_name'] . '</td>
         <td>' . $row['qty'] . '</td>
         <td>' . $row['category'] . '</td>
         <td>' . $row['brand'] . '</td>
         <td>' . $row['color'] . '</td>
         <td>' . $row['size'] . '</td>
         <td>' . $row['style'] . '</td>
         <td>' . $row['mrp'] . '</td>
         <td>' . $row['percent_discount'] . '</td>
         <td>' . $row['selling_price'] . '</td>
      </tr>';
   }

   $data['pagination'] = include_pagination_component($page, $recordsPerPage, $totalRecords);
} else {
   $html = '<tr>
      <td colspan="19"> No record found</td>
   </tr>';
}
$data['htmlData'] = $html;
$data['total_qty'] = number_format($total_qty);
$data['total_gst'] = number_format($total_gst, 2);
$data['total_rate'] = number_format($total_rate, 2);
$data['total_amt'] = number_format($total_amt, 2);
echo json_encode($data);
die;
