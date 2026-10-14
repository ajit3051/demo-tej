<?php include_once("../include/config.php"); ?>

<?php
$validationHelper = new validation();
// error_reporting(E_ALL);
// ini_set('display_errors', 'on');
$db = connect();

$today = date("Y-m-d");

$page        = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1;
$sortOrder   = isset($_REQUEST['sortOrder']) ? $_REQUEST['sortOrder'] : 'ASC';
$sortField   = isset($_REQUEST['sortField']) ? $_REQUEST['sortField'] : 'id';
$formId      = isset($_REQUEST['formId']) ? $_REQUEST['formId'] : '';
$searchKey   = isset($_REQUEST['search']) ? $_REQUEST['search'] : '';
$from_date   = isset($_REQUEST['from_date']) ? $_REQUEST['from_date'] : '';
$to_date     = isset($_REQUEST['to_date']) ? $_REQUEST['to_date'] : '';
$exportType  = isset($_REQUEST['export']) ? $_REQUEST['export'] : '';

$params = '';
$fields = array();

// Select columns list
if ($exportType) {
    $columns = '
        purchage_date, 
        barcode_number, 
        invoice_number, 
        account_name, 
        gst_type, 
        item_name, 
        article_no, 
        item_code,
        category, 
        item_type, 
        rate, 
        status, 
        s_code,
        store_name,
        location,
        qty, 
        used_qty, 
        bal_qty,
        sale_date';
} else {
    $columns = '
        id, 
        purchage_date, 
        barcode_number, 
        invoice_number, 
        account_name, 
        gst_type, 
        item_name, 
        article_no, 
        item_code,
        category, 
        item_type, 
        rate, 
        status, 
        s_code,
        store_name,
        location,
        qty,  
        used_qty, 
        bal_qty,
        sale_date';
}

// Subquery to compile calculated totals first
$baseQuery = "SELECT 
    TP.id, 
    TP.created_date,
    TP.purchage_date, 
    TP.barcode_number, 
    TP.invoice_number, 
    TP.account_name, 
    TP.gst_type, 
    TP.item_name, 
    TP.article_no, 
    IM.item_code,
    TP.category, 
    TP.item_type, 
    TP.rate, 
    TP.status, 
    TP.s_code,
    TP.store_name,
    TP.location,
    TP.total_qty AS qty, 
    COALESCE(SUM(TB.qty), 0) AS used_qty, 
    (TP.total_qty - COALESCE(SUM(TB.qty), 0)) AS bal_qty,
    TB.purchage_date AS sale_date
FROM (
    SELECT 
        id, 
        created_date,
        purchage_date, 
        barcode_number, 
        invoice_number, 
        account_name, 
        gst_type, 
        item_name, 
        article_no,
        category, 
        item_type, 
        rate, 
        status, 
        s_code, 
        store_name,
        location, 
        SUM(qty) AS total_qty
    FROM tbl_purchase 
    GROUP BY barcode_number
) AS TP 
LEFT JOIN tbl_bill AS TB ON TB.barcode_number = TP.barcode_number 
LEFT JOIN tbl_item_master AS IM ON IM.item_name = TP.item_name
WHERE TP.id > 0";

if ($from_date) {
    $baseQuery .= " AND TB.purchage_date >= ?";
    $params .= 's';
    $fields[] = dateInSQLFormat($from_date);
}

if ($to_date) {
    $baseQuery .= " AND TB.purchage_date <= ?";
    $params .= 's';
    $fields[] = dateInSQLFormat($to_date);
}

$baseQuery .= " GROUP BY TP.barcode_number";

// Wrap in outer query so all columns (including aliases) can be searched together with OR
$query = "SELECT $columns FROM ($baseQuery) AS report WHERE 1=1";

if ($formId == 'latestsearchForm') {
    $query .= " AND DATE(report.created_date) = ?";
    $params .= 's';
    $fields[] = $today;
}

if ($searchKey) {
    $query .= " AND (
        report.barcode_number LIKE CONCAT('%', ?, '%') OR 
        report.invoice_number LIKE CONCAT('%', ?, '%') OR 
        report.account_name LIKE CONCAT('%', ?, '%') OR 
        report.gst_type LIKE CONCAT('%', ?, '%') OR 
        report.item_name LIKE CONCAT('%', ?, '%') OR 
        report.category LIKE CONCAT('%', ?, '%') OR 
        report.item_type LIKE CONCAT('%', ?, '%') OR 
        report.rate LIKE CONCAT('%', ?, '%') OR
        report.qty LIKE CONCAT('%', ?, '%') OR 
        report.used_qty LIKE CONCAT('%', ?, '%') OR 
        report.bal_qty LIKE CONCAT('%', ?, '%')
    )";

    $params .= 'sssssssssss';
    for ($i = 0; $i < 11; $i++) {
        $fields[] = $searchKey;
    }
} 

// Execute Total Query
if (empty($fields)) {
    $stmt = $db->select($query);
} else {
    $stmt = $db->select($query, $params, $fields);
}

// Handle Export Requests
if ($exportType == 'excel') {
    exportResult('tejaserp-stockreport-inshortcut', $stmt); 
    exit();
}

if ($exportType == 'pdf') {
    exportResultPdf('tejaserp-stockreport-inshortcut', $stmt); 
    exit();
}

// Calculate Column Totals
$total_qty = 0;
$total_used_qty = 0;
$total_bal_qty = 0;

if ($stmt && $stmt->num_rows > 0) {
    while ($t_row = $stmt->fetch_assoc()) {
        $total_qty += $t_row['qty'];
        $total_used_qty += $t_row['used_qty'];
        $total_bal_qty += $t_row['bal_qty'];
    }
    $totalRecords = $stmt->num_rows();
} else {
    $totalRecords = 0;
}

// Set up Pagination
$recordsPerPage = isset($_SESSION["_RECORDPERPAGE_"]) ? $_SESSION["_RECORDPERPAGE_"] : _RECORDPERPAGE_;
$offSet = ($page - 1) * $recordsPerPage;

$paginatedQuery = $query . " ORDER BY report.$sortField $sortOrder LIMIT $offSet, $recordsPerPage";

if (empty($fields)) {
    $stmtLimit = $db->select($paginatedQuery);
} else {
    $stmtLimit = $db->select($paginatedQuery, $params, $fields);
}

$totalRecordsWithLimit = ($stmtLimit) ? $stmtLimit->num_rows() : 0;

if ($totalRecordsWithLimit > 0) {
    $html = '';
    $i = $offSet + 1;
    
    while ($row = $stmtLimit->fetch_assoc()) {
        $picture = isset($row['picture']) ? $row['picture'] : '';
        $html .= '<tr>
            <td>' . $i++ . '</td>
            <td>' . dateformat($row['purchage_date']) . '</td>
            <td>' . dateformat($row['sale_date']) . '</td>
            <td>' . $row['item_code'] . '</td>
            <td>' . $row['barcode_number'] . '</td>
            <td>' . $row['article_no'] . '</td>
            <td>' . $picture . '</td>
            <td>' . $row['item_name'] . '</td>
            <td>' . $row['category'] . '</td>
            <td>' . $row['qty'] . '</td>
            <td></td>
            <td></td>
            <td>' . $row['used_qty'] . '</td>
            <td>' . $row['bal_qty'] . '</td>
            <td>' . $row['s_code'] . '</td>
            <td>' . $row['store_name'] . '</td>
            <td>' . $row['location'] . '</td>
        </tr>';
    }

    $data['pagination'] = include_pagination_component($page, $recordsPerPage, $totalRecords);
} else {
    $html = '<tr>
        <td colspan="17"> No record found</td>
    </tr>';
    $data['pagination'] = '';
}

$data['htmlData'] = $html;
$data['total_qty'] = number_format($total_qty);
$data['total_used_qty'] = number_format($total_used_qty);
$data['total_bal_qty'] = number_format($total_bal_qty);

echo json_encode($data);
die;