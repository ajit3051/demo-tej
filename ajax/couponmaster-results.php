<?php
ob_start();
include_once("../include/config.php");
error_reporting(0);
ini_set('display_errors', 0);

$validationHelper = new validation();
$db = connect();

$page       = isset($_REQUEST['page']) && (int)$_REQUEST['page'] > 0 ? (int)$_REQUEST['page'] : 1;
$searchKey  = isset($_REQUEST['search']) ? trim($_REQUEST['search']) : '';

$params = '';
$fields = array();

$query = "SELECT * FROM tbl_coupon_master WHERE id > 0";

if ($searchKey !== '') {
   $query .= " AND (coupon_code LIKE CONCAT('%', ?, '%') 
               OR company_name LIKE CONCAT('%', ?, '%') 
               OR store_name LIKE CONCAT('%', ?, '%') 
               OR description_1 LIKE CONCAT('%', ?, '%') 
               OR status LIKE CONCAT('%', ?, '%'))";
   $params .= 'sssss';
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
   while ($row = $stmt->fetch_assoc()) {
      $statusHtml = ($row['status'] == 'true' || strtolower($row['status']) == 'active')
         ? '<span class="label label-success badge-status-sm">Active</span>'
         : '<span class="label label-danger badge-status-sm">Inactive</span>';

      $html .= '<tr>
         <td class="td-center-middle">' . $i++ . '</td>
         <td class="td-center-middle-nowrap">
            <a class="btn btn-info btn-xs" data-toggle="tooltip" title="Edit Coupon" href="fh_couponmastercreation.php?update_id=' . $row['id'] . '">
               <i class="fa fa-pencil"></i> Edit
            </a>
            <button type="button" class="btn btn-warning btn-xs" data-toggle="modal" data-target="#viewCouponModal' . $row['id'] . '" title="View Details">
               <i class="fa fa-eye"></i> Details
            </button>
            <a class="btn btn-danger btn-xs" data-toggle="tooltip" title="Delete Coupon" onClick="return confirm(\'Are you sure you want to Delete this Coupon?\')" href="fh_couponmastercreation_list.php?delete_id=' . $row['id'] . '">
               <i class="fa fa-trash-o"></i> Delete
            </a>
         </td>
         <td class="td-center-middle">' . $statusHtml . '</td>
         <td class="td-middle"><code>' . htmlspecialchars($row['coupon_code'] ?? '') . '</code></td>
         <td class="td-middle-bold">' . htmlspecialchars(substr($row['company_name'] ?? '', 0, 50)) . '</td>
         <td class="td-middle">' . htmlspecialchars(substr($row['store_name'] ?? '', 0, 50)) . '</td>
         <td class="td-middle">' . htmlspecialchars($row['discount_per'] ?? '0') . '%</td>
         <td class="td-middle">' . htmlspecialchars($row['discount_amt'] ?? '0') . '</td>
         <td class="td-middle">' . htmlspecialchars($row['expiry_date'] ?? '-') . '</td>
      </tr>';

      $modalsHtml .= '<div class="modal fade" id="viewCouponModal' . $row['id'] . '" tabindex="-1" role="dialog" aria-hidden="true">
         <div class="modal-dialog modal-md">
            <div class="modal-content modal-content-rounded">
               <div class="modal-header modal-header-teal">
                  <button type="button" class="close modal-close-white" data-dismiss="modal" aria-hidden="true">×</button>
                  <h4 class="modal-title modal-title-bold">
                     <i class="fa fa-ticket mr-8"></i>Coupon Details - ' . htmlspecialchars($row['coupon_code'] ?? '') . '
                  </h4>
               </div>
               <div class="modal-body modal-body-padded">
                  <table class="table table-bordered table-striped mb-0">
                     <tbody>
                        <tr>
                           <th class="th-w-35">Coupon Code</th>
                           <td><code>' . htmlspecialchars($row['coupon_code'] ?? '') . '</code></td>
                        </tr>
                        <tr>
                           <th>Company Name</th>
                           <td><strong>' . htmlspecialchars($row['company_name'] ?? '') . '</strong></td>
                        </tr>
                        <tr>
                           <th>Store Name</th>
                           <td>' . htmlspecialchars($row['store_name'] ?? 'N/A') . '</td>
                        </tr>
                        <tr>
                           <th>Discount (%)</th>
                           <td>' . htmlspecialchars($row['discount_per'] ?? '0') . '%</td>
                        </tr>
                        <tr>
                           <th>Discount Amount</th>
                           <td>' . htmlspecialchars($row['discount_amt'] ?? '0') . '</td>
                        </tr>
                        <tr>
                           <th>Min Shopping Amount</th>
                           <td>' . htmlspecialchars($row['minshopping_amt'] ?? '0') . '</td>
                        </tr>
                        <tr>
                           <th>Expiry Date</th>
                           <td>' . htmlspecialchars($row['expiry_date'] ?? 'N/A') . '</td>
                        </tr>
                        <tr>
                           <th>Status</th>
                           <td>' . $statusHtml . '</td>
                        </tr>
                     </tbody>
                  </table>
               </div>
               <div class="modal-footer modal-footer-light">
                  <a href="fh_couponmastercreation.php?update_id=' . $row['id'] . '" class="btn btn-info btn-sm">
                     <i class="fa fa-pencil"></i> Edit Coupon
                  </a>
                  <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
               </div>
            </div>
         </div>
      </div>';
   }

   $pagination = include_pagination_component($page, $recordsPerPage, $totalRecords);

} else {
   $html = '<tr>
      <td colspan="9" class="text-center td-padded-20">No coupon records found.</td>
   </tr>';
   $pagination = '';
}

$data = array(
   'htmlData' => $html,
   'modalData' => $modalsHtml,
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
