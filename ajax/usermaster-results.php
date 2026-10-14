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

$query = "SELECT * FROM tbl_user_master WHERE id > 0";

if ($searchKey !== '') {
   $query .= " AND (user_code LIKE CONCAT('%', ?, '%') 
               OR user_name LIKE CONCAT('%', ?, '%') 
               OR mobile_no LIKE CONCAT('%', ?, '%') 
               OR store_name LIKE CONCAT('%', ?, '%') 
               OR location LIKE CONCAT('%', ?, '%') 
               OR user_type LIKE CONCAT('%', ?, '%'))";
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

      $pictureHtml = '';
      if (!empty($user['picture']) && file_exists(_BASEPATH . 'uploads/item-master/' . $user['picture'])) {
         $pictureHtml = '<img src="uploads/item-master/' . htmlspecialchars($user['picture']) . '" class="img-circle avatar-img-table" alt="User Image" width="40" height="40">';
      } else {
         $firstChar = strtoupper(substr($user['user_name'] ?? 'U', 0, 1));
         $pictureHtml = '<div class="avatar-fallback-table">' . $firstChar . '</div>';
      }

      $modalPictureHtml = '';
      if (!empty($user['picture']) && file_exists(_BASEPATH . 'uploads/item-master/' . $user['picture'])) {
         $modalPictureHtml = '<img src="uploads/item-master/' . htmlspecialchars($user['picture']) . '" class="img-circle avatar-img-modal" width="90" height="90">';
      } else {
         $firstChar = strtoupper(substr($user['user_name'] ?? 'U', 0, 1));
         $modalPictureHtml = '<div class="avatar-fallback-modal">' . $firstChar . '</div>';
      }

      $genderTxt = ($user['gender'] == '1') ? 'Male' : (($user['gender'] == '0') ? 'Female' : htmlspecialchars($user['gender'] ?? ''));
      $createdDate = !empty($user['created_date']) ? date('d-m-Y H:i', strtotime($user['created_date'])) : '-';
      $updatedDate = !empty($user['updated_date']) ? date('d-m-Y H:i', strtotime($user['updated_date'])) : '-';

      // Table Row
      $html .= '<tr>
         <td class="td-center-middle">' . $i++ . '</td>
         <td class="td-center-middle-nowrap">
            <a class="btn btn-info btn-xs" data-toggle="tooltip" title="Edit User" href="fh_usermastercreation.php?update_id=' . $user['id'] . '">
               <i class="fa fa-pencil"></i> Edit
            </a>
            <button type="button" class="btn btn-warning btn-xs" data-toggle="modal" data-target="#viewDetailsModalList' . $user['id'] . '" title="View Details">
               <i class="fa fa-eye"></i> Details
            </button>
            <a class="btn btn-danger btn-xs" data-toggle="tooltip" title="Delete User" onClick="return confirm(\'Are you sure you want to Delete this User?\')" href="fh_usermastercreation_list.php?delete_id=' . $user['id'] . '">
               <i class="fa fa-trash-o"></i> Delete
            </a>
         </td>
         <td class="td-center-middle">' . $statusHtml . '</td>
         <td class="td-center-middle">' . $pictureHtml . '</td>
         <td class="td-middle"><code>' . htmlspecialchars($user['user_code'] ?? '') . '</code></td>
         <td class="td-middle-bold">' . htmlspecialchars(substr($user['user_name'] ?? '', 0, 50)) . '</td>
         <td class="td-middle">' . htmlspecialchars(substr($user['mobile_no'] ?? '', 0, 50)) . '</td>
         <td class="td-middle">' . htmlspecialchars(substr($user['store_name'] ?? '', 0, 50)) . '</td>
         <td class="td-middle">' . htmlspecialchars(substr($user['location'] ?? '', 0, 50)) . '</td>
         <td class="td-middle"><span class="label label-info badge-info-sm">' . htmlspecialchars(substr($user['user_type'] ?? '', 0, 50)) . '</span></td>
         <td class="td-middle-muted">' . $createdDate . '</td>
         <td class="td-middle-muted">' . $updatedDate . '</td>
         <td class="td-center-middle">' . $genderTxt . '</td>
      </tr>';

      // Separate Modal HTML
      $modalsHtml .= '<div class="modal fade" id="viewDetailsModalList' . $user['id'] . '" tabindex="-1" role="dialog" aria-hidden="true">
         <div class="modal-dialog modal-md">
            <div class="modal-content modal-content-rounded">
               <div class="modal-header modal-header-teal">
                  <button type="button" class="close modal-close-white" data-dismiss="modal" aria-hidden="true">×</button>
                  <h4 class="modal-title modal-title-bold">
                     <i class="fa fa-user-circle-o mr-8"></i>User Details - ' . htmlspecialchars($user['user_name'] ?? '') . '
                  </h4>
               </div>
               <div class="modal-body modal-body-padded">
                  <div class="row mb-15">
                     <div class="col-xs-12 text-center mb-15">
                        ' . $modalPictureHtml . '
                        <h4 class="modal-user-name">' . htmlspecialchars($user['user_name'] ?? '') . '</h4>
                        <span class="label label-info">' . htmlspecialchars($user['user_type'] ?? '') . '</span>
                     </div>
                  </div>
                  <table class="table table-bordered table-striped mb-0">
                     <tbody>
                        <tr>
                           <th class="th-w-35">User Code</th>
                           <td><code>' . htmlspecialchars($user['user_code'] ?? '') . '</code></td>
                        </tr>
                        <tr>
                           <th>Mobile Number</th>
                           <td>' . htmlspecialchars($user['mobile_no'] ?? '') . '</td>
                        </tr>
                        <tr>
                           <th>Store Name</th>
                           <td>' . htmlspecialchars($user['store_name'] ?? '') . '</td>
                        </tr>
                        <tr>
                           <th>Location</th>
                           <td>' . htmlspecialchars($user['location'] ?? '') . '</td>
                        </tr>
                        <tr>
                           <th>Gender</th>
                           <td>' . $genderTxt . '</td>
                        </tr>
                        <tr>
                           <th>Status</th>
                           <td>' . $statusHtml . '</td>
                        </tr>
                        <tr>
                           <th>Created Date</th>
                           <td>' . (!empty($user['created_date']) ? date('d-m-Y H:i:s', strtotime($user['created_date'])) : '-') . '</td>
                        </tr>
                        <tr>
                           <th>Updated Date</th>
                           <td>' . (!empty($user['updated_date']) ? date('d-m-Y H:i:s', strtotime($user['updated_date'])) : '-') . '</td>
                        </tr>
                     </tbody>
                  </table>
               </div>
               <div class="modal-footer modal-footer-light">
                  <a href="fh_usermastercreation.php?update_id=' . $user['id'] . '" class="btn btn-info btn-sm">
                     <i class="fa fa-pencil"></i> Edit User
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
      <td colspan="13" class="text-center td-padded-20">No user records found.</td>
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
