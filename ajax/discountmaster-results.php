<?php
ob_start();
include_once("../include/config.php");
error_reporting(0);
ini_set('display_errors', 0);

$validationHelper = new validation();
$db = connect();

$page       = isset($_REQUEST['page']) && (int)$_REQUEST['page'] > 0 ? (int)$_REQUEST['page'] : 1;
$searchKey  = isset($_REQUEST['search']) ? trim($_REQUEST['search']) : '';
$exportType = isset($_REQUEST['export']) ? $_REQUEST['export'] : '';

$params = '';
$fields = array();

$query = "SELECT * FROM tbl_discount_master WHERE id > 0";

if ($searchKey !== '') {
   $query .= " AND (dis_code LIKE CONCAT('%', ?, '%') 
               OR distype_name LIKE CONCAT('%', ?, '%') 
               OR group_name LIKE CONCAT('%', ?, '%') 
               OR brand_name LIKE CONCAT('%', ?, '%') 
               OR barcode_no LIKE CONCAT('%', ?, '%') 
               OR item_name LIKE CONCAT('%', ?, '%') 
               OR store_name LIKE CONCAT('%', ?, '%') 
               OR location_name LIKE CONCAT('%', ?, '%') 
               OR status LIKE CONCAT('%', ?, '%'))";
   $params .= 'sssssssss';
   for ($k = 0; $k < 9; $k++) {
       $fields[] = $searchKey;
   }
}

if (empty($fields)) {
   $stmt = $db->select($query);
} else {
   $stmt = $db->select($query, $params, $fields);
}

$totalRecords = $stmt ? $stmt->num_rows() : 0;

$recordsPerPage = isset($_SESSION["_RECORDPERPAGE_"]) && $_SESSION["_RECORDPERPAGE_"] ? (int)$_SESSION["_RECORDPERPAGE_"] : 20;
$offSet = ($page - 1) * $recordsPerPage;

$query .= " ORDER BY id DESC LIMIT $offSet, $recordsPerPage";

if (empty($fields)) {
   $stmt = $db->select($query);
} else {
   $stmt = $db->select($query, $params, $fields);
}

$totalRecordsWithLimit = $stmt ? $stmt->num_rows() : 0;

$html = '';
$modalsHtml = '';

if ($totalRecordsWithLimit > 0) {
   $i = $offSet + 1;
   while ($user = $stmt->fetch_assoc()) {
      $statusBadge = ($user['status'] === 'Active' || $user['status'] === 'true')
          ? '<span class="label label-success badge-status-sm">Active</span>'
          : '<span class="label label-danger badge-status-sm">Inactive</span>';

      $html .= '<tr>
          <td class="td-center-middle">' . $i++ . '</td>
          <td class="td-center-middle-nowrap">
              <button type="button" class="btn btn-primary btn-xs view-discount-btn" data-toggle="modal" data-target="#viewDiscountModal" 
                  data-id="' . $user['id'] . '"
                  data-discode="' . htmlspecialchars($user['dis_code'] ?? '') . '"
                  data-type="' . htmlspecialchars($user['distype_name'] ?? '') . '"
                  data-group="' . htmlspecialchars($user['group_name'] ?? '') . '"
                  data-brand="' . htmlspecialchars($user['brand_name'] ?? '') . '"
                  data-barcode="' . htmlspecialchars($user['barcode_no'] ?? '') . '"
                  data-item="' . htmlspecialchars($user['item_name'] ?? '') . '"
                  data-store="' . htmlspecialchars($user['store_name'] ?? '') . '"
                  data-location="' . htmlspecialchars($user['location_name'] ?? '') . '"
                  data-per="' . htmlspecialchars($user['discount_per'] ?? '') . '"
                  data-amt="' . htmlspecialchars($user['discount_amt'] ?? '') . '"
                  data-mrp="' . htmlspecialchars($user['mrp'] ?? '') . '"
                  data-sp="' . htmlspecialchars($user['sp'] ?? '') . '"
                  data-status="' . htmlspecialchars($user['status'] ?? 'Active') . '">
                  <i class="fa fa-eye"></i> View
              </button>
              <a class="btn btn-info btn-xs" data-toggle="tooltip" title="Edit Discount" href="fh_discountmastercreation.php?update_id=' . $user['id'] . '">
                  <i class="fa fa-pencil"></i> Edit
              </a>
              <a class="btn btn-danger btn-xs" data-toggle="tooltip" title="Delete Discount" href="fh_discountmastercreation_list.php?delete_id=' . $user['id'] . '" onclick="return confirm(\'Are you sure you want to delete this record?\');">
                  <i class="fa fa-trash-o"></i> Delete
              </a>
          </td>
          <td class="td-center-middle">' . $statusBadge . '</td>
          <td>' . htmlspecialchars($user['dis_code'] ?? '') . '</td>
          <td>' . htmlspecialchars($user['distype_name'] ?? '') . '</td>
          <td>' . htmlspecialchars($user['group_name'] ?? '') . '</td>
          <td>' . htmlspecialchars($user['brand_name'] ?? '') . '</td>
          <td>' . htmlspecialchars($user['barcode_no'] ?? '') . '</td>
          <td>' . htmlspecialchars($user['item_name'] ?? '') . '</td>
          <td>' . htmlspecialchars($user['store_name'] ?? '') . '</td>
          <td>' . htmlspecialchars($user['location_name'] ?? '') . '</td>
          <td>' . htmlspecialchars($user['discount_per'] ?? '') . '</td>
          <td>' . htmlspecialchars($user['discount_amt'] ?? '') . '</td>
          <td>' . htmlspecialchars($user['mrp'] ?? '') . '</td>
          <td>' . htmlspecialchars($user['sp'] ?? '') . '</td>
      </tr>';
   }

   $pagination = include_pagination_component($page, $recordsPerPage, $totalRecords);
} else {
   $html = '<tr><td colspan="15" class="text-center text-muted td-padded-20">No Discount Records Found</td></tr>';
   $pagination = '';
}

$data = array(
   'htmlData' => $html,
   'pagination' => $pagination,
   'totalRecords' => $totalRecords
);

if (ob_get_length()) {
    ob_clean();
}
header('Content-Type: application/json');
echo json_encode($data);
exit();
?>
