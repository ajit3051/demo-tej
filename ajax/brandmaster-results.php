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

$query = "SELECT * FROM tbl_brand_master WHERE id > 0";

if ($searchKey !== '') {
   $query .= " AND (brand_code LIKE CONCAT('%', ?, '%') 
               OR brand_name LIKE CONCAT('%', ?, '%') 
               OR status LIKE CONCAT('%', ?, '%'))";
   $params .= 'sss';
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

      $imageHtml = (!empty($user['picture']) && file_exists('../uploads/brand-master/' . $user['picture']))
         ? '<img src="uploads/brand-master/' . htmlspecialchars($user['picture']) . '" class="img-circle avatar-img-table" alt="Brand Image" width="40" height="40">'
         : '<div class="avatar-fallback-table"><i class="fa fa-folder"></i></div>';

      $modalImageHtml = (!empty($user['picture']) && file_exists('../uploads/brand-master/' . $user['picture']))
         ? '<div class="text-center mb-15"><img src="uploads/brand-master/' . htmlspecialchars($user['picture']) . '" class="avatar-img-modal" alt="Brand Image" style="max-height: 120px; border-radius: 8px; border: 1px solid #ddd; padding: 4px;"></div>'
         : '';

      // Table Row
      $html .= '<tr>
         <td class="td-center-middle">' . $i++ . '</td>
         <td class="td-center-middle-nowrap">
            <a class="btn btn-info btn-xs" data-toggle="tooltip" title="Edit Brand" href="fh_brandmastercreation.php?update_id=' . $user['id'] . '">
               <i class="fa fa-pencil"></i> Edit
            </a>
            <button type="button" class="btn btn-warning btn-xs" data-toggle="modal" data-target="#viewBrandModal' . $user['id'] . '" title="View Details">
               <i class="fa fa-eye"></i> Details
            </button>
            <a class="btn btn-danger btn-xs" data-toggle="tooltip" title="Delete Brand" onClick="return confirm(\'Are you sure you want to Delete this Brand?\')" href="fh_brandmastercreation_list.php?delete_id=' . $user['id'] . '">
               <i class="fa fa-trash-o"></i> Delete
            </a>
         </td>
         <td class="td-center-middle">' . $statusHtml . '</td>
         <td class="td-center-middle">' . $imageHtml . '</td>
         <td class="td-middle"><code>' . htmlspecialchars($user['brand_code'] ?? '') . '</code></td>
         <td class="td-middle-bold">' . htmlspecialchars(substr($user['brand_name'] ?? '', 0, 50)) . '</td>
      </tr>';

      // Modal Details Window
      $modalsHtml .= '<div class="modal fade" id="viewBrandModal' . $user['id'] . '" tabindex="-1" role="dialog" aria-hidden="true">
         <div class="modal-dialog modal-md">
            <div class="modal-content modal-content-rounded">
               <div class="modal-header modal-header-teal">
                  <button type="button" class="close modal-close-white" data-dismiss="modal" aria-hidden="true">×</button>
                  <h4 class="modal-title modal-title-bold">
                     <i class="fa fa-folder-open mr-8"></i>Brand Details - ' . htmlspecialchars($user['brand_name'] ?? '') . '
                  </h4>
               </div>
               <div class="modal-body modal-body-padded">
                  ' . $modalImageHtml . '
                  <table class="table table-bordered table-striped mb-0">
                     <tbody>
                        <tr>
                           <th class="th-w-35">Brand Code</th>
                           <td><code>' . htmlspecialchars($user['brand_code'] ?? '') . '</code></td>
                        </tr>
                        <tr>
                           <th>Brand Name</th>
                           <td><strong>' . htmlspecialchars($user['brand_name'] ?? '') . '</strong></td>
                        </tr>
                        <tr>
                           <th>Status</th>
                           <td>' . $statusHtml . '</td>
                        </tr>
                     </tbody>
                  </table>
               </div>
               <div class="modal-footer modal-footer-light">
                  <a href="fh_brandmastercreation.php?update_id=' . $user['id'] . '" class="btn btn-info btn-sm">
                     <i class="fa fa-pencil"></i> Edit Brand
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
      <td colspan="6" class="text-center td-padded-20">No brand records found.</td>
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
