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

$query = "SELECT * FROM tbl_gst_master WHERE id > 0";

if ($searchKey !== '') {
   $query .= " AND (gst_code LIKE CONCAT('%', ?, '%') 
               OR gst_per LIKE CONCAT('%', ?, '%') 
               OR group_name LIKE CONCAT('%', ?, '%') 
               OR hsn_code LIKE CONCAT('%', ?, '%') 
               OR gst_range LIKE CONCAT('%', ?, '%') 
               OR status LIKE CONCAT('%', ?, '%'))";
   $params .= 'ssssss';
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
      $statusHtml = ($user['status'] == 'true' || strtolower($user['status']) == 'active')
         ? '<span class="label label-success badge-status-sm">Active</span>'
         : '<span class="label label-danger badge-status-sm">Inactive</span>';

      // Table Row
      $html .= '<tr>
         <td class="td-center-middle">' . $i++ . '</td>
         <td class="td-center-middle-nowrap">
            <a class="btn btn-info btn-xs" data-toggle="tooltip" title="Edit GST" href="fh_gstmastercreation.php?update_id=' . $user['id'] . '">
               <i class="fa fa-pencil"></i> Edit
            </a>
            <button type="button" class="btn btn-warning btn-xs" data-toggle="modal" data-target="#viewGstModal' . $user['id'] . '" title="View Details">
               <i class="fa fa-eye"></i> Details
            </button>
            <a class="btn btn-danger btn-xs" data-toggle="tooltip" title="Delete GST" onClick="return confirm(\'Are you sure you want to Delete this GST record?\')" href="fh_gstmastercreation_list.php?delete_id=' . $user['id'] . '">
               <i class="fa fa-trash-o"></i> Delete
            </a>
         </td>
         <td class="td-center-middle">' . $statusHtml . '</td>
         <td class="td-middle"><code>' . htmlspecialchars($user['gst_code'] ?? '') . '</code></td>
         <td class="td-middle-bold">' . htmlspecialchars($user['group_name'] ?? '') . '</td>
         <td class="td-middle">' . htmlspecialchars($user['hsn_code'] ?? '') . '</td>
         <td class="td-middle">' . htmlspecialchars($user['gst_per'] ?? '0') . '%</td>
         <td class="td-middle">' . htmlspecialchars($user['gst_range'] ?? '') . '</td>
      </tr>';

      // Modal Details Window
      $modalsHtml .= '<div class="modal fade" id="viewGstModal' . $user['id'] . '" tabindex="-1" role="dialog" aria-hidden="true">
         <div class="modal-dialog modal-md">
            <div class="modal-content modal-content-rounded">
               <div class="modal-header modal-header-teal">
                  <button type="button" class="close modal-close-white" data-dismiss="modal" aria-hidden="true">×</button>
                  <h4 class="modal-title modal-title-bold">
                     <i class="fa fa-percent mr-8"></i>GST Details - ' . htmlspecialchars($user['group_name'] ?? '') . '
                  </h4>
               </div>
               <div class="modal-body modal-body-padded">
                  <table class="table table-bordered table-striped mb-0">
                     <tbody>
                        <tr>
                           <th class="th-w-35">GST Code</th>
                           <td><code>' . htmlspecialchars($user['gst_code'] ?? '') . '</code></td>
                        </tr>
                        <tr>
                           <th>Group / Category Name</th>
                           <td><strong>' . htmlspecialchars($user['group_name'] ?? '') . '</strong></td>
                        </tr>
                        <tr>
                           <th>HSN Code</th>
                           <td>' . htmlspecialchars($user['hsn_code'] ?? '') . '</td>
                        </tr>
                        <tr>
                           <th>GST (%)</th>
                           <td>' . htmlspecialchars($user['gst_per'] ?? '0') . '%</td>
                        </tr>
                        <tr>
                           <th>GST Range</th>
                           <td>' . htmlspecialchars($user['gst_range'] ?? 'N/A') . '</td>
                        </tr>
                        <tr>
                           <th>Status</th>
                           <td>' . $statusHtml . '</td>
                        </tr>
                     </tbody>
                  </table>
               </div>
               <div class="modal-footer modal-footer-light">
                  <a href="fh_gstmastercreation.php?update_id=' . $user['id'] . '" class="btn btn-info btn-sm">
                     <i class="fa fa-pencil"></i> Edit GST
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
      <td colspan="8" class="text-center td-padded-20">No GST records found.</td>
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
