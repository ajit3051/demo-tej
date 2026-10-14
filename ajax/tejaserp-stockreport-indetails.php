<?php include_once("../include/config.php"); ?>

<?php
$validationHelper = new validation();

$db = connect();

$today = date("Y-m-d");

$page        = isset($_REQUEST['page']) ? (int)$_REQUEST['page'] : 1;
$sortOrder   = isset($_REQUEST['sortOrder']) && in_array(strtoupper($_REQUEST['sortOrder']), ['ASC', 'DESC']) ? strtoupper($_REQUEST['sortOrder']) : 'DESC';
$sortField   = isset($_REQUEST['sortField']) ? $_REQUEST['sortField'] : 'id';
$formId      = isset($_REQUEST['formId']) ? $_REQUEST['formId'] : '';
$searchKey   = isset($_REQUEST['search']) ? trim($_REQUEST['search']) : '';
$from_date   = isset($_REQUEST['from_date']) && !empty($_REQUEST['from_date']) ? dateInSQLFormat($_REQUEST['from_date']) : '';
$to_date     = isset($_REQUEST['to_date']) && !empty($_REQUEST['to_date']) ? dateInSQLFormat($_REQUEST['to_date']) : '';
$exportType  = isset($_REQUEST['export']) ? $_REQUEST['export'] : '';

// Determine effective date range
if ($formId == 'latestsearchForm') {
    $effective_from_date = !empty($from_date) ? $from_date : $today;
    $effective_to_date   = !empty($to_date) ? $to_date : $today;
} else {
    $effective_from_date = $from_date;
    $effective_to_date   = $to_date;
}

$params = '';
$fields = array();

// 1. Prior purchases subquery (purchases BEFORE effective_from_date)
if (!empty($effective_from_date)) {
    $tp_prior_sql = "SELECT barcode_number, SUM(qty) AS prior_purch_qty FROM tbl_purchase WHERE DATE(purchage_date) < ? GROUP BY barcode_number";
    $params .= 's';
    $fields[] = $effective_from_date;
} else {
    $tp_prior_sql = "SELECT barcode_number, 0 AS prior_purch_qty FROM tbl_purchase WHERE 1=0 GROUP BY barcode_number";
}

// 2. Prior sales subquery (sales BEFORE effective_from_date)
if (!empty($effective_from_date)) {
    $tb_prior_sql = "SELECT barcode_number, SUM(qty) AS prior_sale_qty FROM tbl_bill WHERE DATE(purchage_date) < ? GROUP BY barcode_number";
    $params .= 's';
    $fields[] = $effective_from_date;
} else {
    $tb_prior_sql = "SELECT barcode_number, 0 AS prior_sale_qty FROM tbl_bill WHERE 1=0 GROUP BY barcode_number";
}

// 3. Current purchases subquery (purchases DURING effective date range)
$tp_curr_where = "WHERE 1=1";
if (!empty($effective_from_date)) {
    $tp_curr_where .= " AND DATE(purchage_date) >= ?";
    $params .= 's';
    $fields[] = $effective_from_date;
}
if (!empty($effective_to_date)) {
    $tp_curr_where .= " AND DATE(purchage_date) <= ?";
    $params .= 's';
    $fields[] = $effective_to_date;
}
$tp_curr_sql = "SELECT barcode_number, SUM(qty) AS purch_qty FROM tbl_purchase $tp_curr_where GROUP BY barcode_number";

// 4. Current sales subquery (sales DURING effective date range)
$tb_curr_where = "WHERE 1=1";
if (!empty($effective_from_date)) {
    $tb_curr_where .= " AND DATE(purchage_date) >= ?";
    $params .= 's';
    $fields[] = $effective_from_date;
}
if (!empty($effective_to_date)) {
    $tb_curr_where .= " AND DATE(purchage_date) <= ?";
    $params .= 's';
    $fields[] = $effective_to_date;
}
$tb_curr_sql = "SELECT barcode_number, SUM(qty) AS sale_qty, SUM(amount) AS sale_amt, MAX(purchage_date) AS sale_date FROM tbl_bill $tb_curr_where GROUP BY barcode_number";

// Construct Base Report Query
$reportQuery = "SELECT 
    TP.id, 
    TP.created_date,
    TP.purchage_date, 
    TP.barcode_number, 
    TP.invoice_number, 
    TP.account_name, 
    TP.gst_type, 
    TRIM(TP.item_name) AS item_name, 
    TP.article_no, 
    IM.item_code,
    TP.category, 
    TP.size,
    TP.brand,
    TP.color,
    TP.style,
    COALESCE(CAST(TP.purchase_price AS DECIMAL(10,2)), 0) AS purchase_price,
    COALESCE(CAST(TP.gst_amount AS DECIMAL(10,2)), 0) AS gst_amount,
    TP.picture,
    TP.item_type, 
    TP.rate, 
    TP.status, 
    TP.s_code,
    TP.store_name,
    TP.location,
    GREATEST(0, COALESCE(TP_PRIOR.prior_purch_qty, 0) - COALESCE(TB_PRIOR.prior_sale_qty, 0)) AS opening_qty,
    (GREATEST(0, COALESCE(TP_PRIOR.prior_purch_qty, 0) - COALESCE(TB_PRIOR.prior_sale_qty, 0)) * COALESCE(CAST(TP.purchase_price AS DECIMAL(10,2)), 0)) AS opening_val,
    COALESCE(TP_CURR.purch_qty, 0) AS purchase_qty, 
    (COALESCE(TP_CURR.purch_qty, 0) * COALESCE(CAST(TP.purchase_price AS DECIMAL(10,2)), 0)) AS purchase_bill_amt,
    0 AS audit_qty,
    COALESCE(TB_CURR.sale_qty, 0) AS sale_qty, 
    COALESCE(TB_CURR.sale_amt, 0) AS sale_amt,
    TB_CURR.sale_date,
    (GREATEST(0, COALESCE(TP_PRIOR.prior_purch_qty, 0) - COALESCE(TB_PRIOR.prior_sale_qty, 0)) + COALESCE(TP_CURR.purch_qty, 0) - COALESCE(TB_CURR.sale_qty, 0)) AS bal_qty,
    ((GREATEST(0, COALESCE(TP_PRIOR.prior_purch_qty, 0) - COALESCE(TB_PRIOR.prior_sale_qty, 0)) + COALESCE(TP_CURR.purch_qty, 0) - COALESCE(TB_CURR.sale_qty, 0)) * COALESCE(CAST(TP.purchase_price AS DECIMAL(10,2)), 0)) AS bal_amt
FROM (
    SELECT 
        barcode_number,
        MIN(id) AS id, 
        MIN(created_date) AS created_date,
        MIN(purchage_date) AS purchage_date, 
        MAX(invoice_number) AS invoice_number, 
        MAX(account_name) AS account_name, 
        MAX(gst_type) AS gst_type, 
        MAX(item_name) AS item_name, 
        MAX(article_no) AS article_no,
        MAX(category) AS category, 
        MAX(size) AS size,
        MAX(brand) AS brand,
        MAX(color) AS color,
        MAX(style) AS style,
        MAX(purchase_price) AS purchase_price,
        MAX(gst_amount) AS gst_amount,
        MAX(picture) AS picture,
        MAX(item_type) AS item_type, 
        MAX(rate) AS rate, 
        MAX(status) AS status, 
        MAX(s_code) AS s_code, 
        MAX(store_name) AS store_name,
        MAX(location) AS location
    FROM tbl_purchase 
    GROUP BY barcode_number
) AS TP
LEFT JOIN tbl_item_master AS IM ON TRIM(IM.item_name) = TRIM(TP.item_name)
LEFT JOIN ($tp_prior_sql) AS TP_PRIOR ON TP_PRIOR.barcode_number = TP.barcode_number
LEFT JOIN ($tb_prior_sql) AS TB_PRIOR ON TB_PRIOR.barcode_number = TP.barcode_number
LEFT JOIN ($tp_curr_sql) AS TP_CURR ON TP_CURR.barcode_number = TP.barcode_number
LEFT JOIN ($tb_curr_sql) AS TB_CURR ON TB_CURR.barcode_number = TP.barcode_number";

// Outer Query for Search Filtering
$query = "SELECT * FROM ($reportQuery) AS report WHERE 1=1";

if (!empty($searchKey)) {
    $query .= " AND (
        report.barcode_number LIKE CONCAT('%', ?, '%') OR 
        report.item_code LIKE CONCAT('%', ?, '%') OR 
        report.article_no LIKE CONCAT('%', ?, '%') OR 
        report.item_name LIKE CONCAT('%', ?, '%') OR 
        report.category LIKE CONCAT('%', ?, '%') OR 
        report.brand LIKE CONCAT('%', ?, '%') OR 
        report.color LIKE CONCAT('%', ?, '%') OR 
        report.style LIKE CONCAT('%', ?, '%') OR 
        report.size LIKE CONCAT('%', ?, '%') OR 
        report.s_code LIKE CONCAT('%', ?, '%') OR 
        report.store_name LIKE CONCAT('%', ?, '%')
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

// Handle Export
if ($exportType == 'excel') {
    exportResult('tejaserp-stockreport-indetails', $stmt); 
    exit();
}

if ($exportType == 'pdf') {
    exportResultPdf('tejaserp-stockreport-indetails', $stmt); 
    exit();
}

// Calculate Summary Totals
$total_opening_qty = 0;
$total_qty         = 0;
$total_sale_qty    = 0;
$total_bal_qty     = 0;
$total_opening_val = 0;
$total_purch_amt   = 0;
$total_sale_amt    = 0;
$total_bal_amt     = 0;

if ($stmt && $stmt->num_rows > 0) {
    while ($t_row = $stmt->fetch_assoc()) {
        $total_opening_qty += (float)$t_row['opening_qty'];
        $total_qty         += (float)$t_row['purchase_qty'];
        $total_sale_qty    += (float)$t_row['sale_qty'];
        $total_bal_qty     += (float)$t_row['bal_qty'];
        $total_opening_val += (float)$t_row['opening_val'];
        $total_purch_amt   += (float)$t_row['purchase_bill_amt'];
        $total_sale_amt    += (float)$t_row['sale_amt'];
        $total_bal_amt     += (float)$t_row['bal_amt'];
    }
    $totalRecords = $stmt->num_rows();
} else {
    $totalRecords = 0;
}

// Pagination
$recordsPerPage = isset($_SESSION["_RECORDPERPAGE_"]) ? $_SESSION["_RECORDPERPAGE_"] : _RECORDPERPAGE_;
$offSet = ($page - 1) * $recordsPerPage;

$allowedSortFields = ['id', 'purchage_date', 'sale_date', 'item_code', 'barcode_number', 'article_no', 'item_name', 'category', 'opening_qty', 'purchase_qty', 'sale_qty', 'bal_qty'];
if (!in_array($sortField, $allowedSortFields)) {
    $sortField = 'id';
}

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
        $imgSrc = !empty($row['picture']) ? '<img src="' . $row['picture'] . '" width="40" height="40" />' : '';
        
        $html .= '<tr>
            <td>' . $i++ . '</td>
            <td>' . dateformat($row['purchage_date']) . '</td>
            <td>' . dateformat($row['sale_date']) . '</td>
            <td>' . $row['item_code'] . '</td>
            <td>' . $row['barcode_number'] . '</td>
            <td>' . $row['article_no'] . '</td>
            <td>' . $imgSrc . '</td>
            <td>' . $row['item_name'] . '</td>
            <td>' . $row['category'] . '</td>
            <td>' . $row['size'] . '</td>
            <td>' . $row['brand'] . '</td>
            <td>' . $row['color'] . '</td>
            <td>' . $row['style'] . '</td>
            <td>' . number_format($row['opening_qty']) . '</td>
            <td>' . number_format($row['opening_val'], 2) . '</td>
            <td>' . number_format($row['purchase_price'], 2) . '</td>
            <td>' . number_format($row['gst_amount'], 2) . '</td>
            <td>' . number_format($row['purchase_qty']) . '</td>
            <td>' . number_format($row['audit_qty']) . '</td>
            <td>' . number_format($row['sale_qty']) . '</td>
            <td>' . number_format($row['bal_qty']) . '</td>
            <td>' . number_format($row['purchase_bill_amt'], 2) . '</td>
            <td>' . number_format($row['sale_amt'], 2) . '</td>
            <td>' . number_format($row['bal_amt'], 2) . '</td>
            <td>' . $row['s_code'] . '</td>
            <td>' . $row['store_name'] . '</td>
            <td>' . $row['location'] . '</td>
        </tr>';
    }

    $data['pagination'] = include_pagination_component($page, $recordsPerPage, $totalRecords);
} else {
    $html = '<tr>
        <td colspan="27" style="text-align:center;">No record found</td>
    </tr>';
    $data['pagination'] = '';
}

// Return Payload
$data['htmlData']          = $html;
$data['total_opening_qty'] = number_format($total_opening_qty);
$data['total_qty']         = number_format($total_qty);
$data['total_used_qty']    = number_format($total_sale_qty);
$data['total_bal_qty']     = number_format($total_bal_qty);
$data['total_opening_val'] = number_format($total_opening_val, 2);
$data['total_purch_amt']   = number_format($total_purch_amt, 2);
$data['total_sale_amt']    = number_format($total_sale_amt, 2);
$data['total_bal_amt']     = number_format($total_bal_amt, 2);

echo json_encode($data);
die;