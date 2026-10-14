<?php
include_once('../include/config.php');

$search = isset($_POST['search']) ? trim($_POST['search']) : '';
$page = isset($_POST['page']) && (int)$_POST['page'] > 0 ? (int)$_POST['page'] : 1;
$perPage = isset($_SESSION["_RECORDPERPAGE_"]) && $_SESSION["_RECORDPERPAGE_"] ? (int)$_SESSION["_RECORDPERPAGE_"] : 20;
$offset = ($page - 1) * $perPage;

$whereClauses = array();
$params = array();

if ($search !== '') {
    $whereClauses[] = "(code LIKE ? OR store_code LIKE ? OR store_name LIKE ? OR company_name LIKE ? OR mobile_no1 LIKE ? OR gst_no LIKE ? OR email_id LIKE ?)";
    $searchTerm = '%' . $search . '%';
    for ($k = 0; $k < 7; $k++) {
        $params[] = $searchTerm;
    }
}

$whereSql = '';
if (count($whereClauses) > 0) {
    $whereSql = ' WHERE ' . implode(' AND ', $whereClauses);
}

// Count total
$countQuery = "SELECT COUNT(*) as total FROM tbl_storeprofile_master" . $whereSql;
$countRes = $db->select($countQuery, $params, 'COUNT');
$totalRecords = isset($countRes[0]['total']) ? (int)$countRes[0]['total'] : 0;
$totalPages = ceil($totalRecords / $perPage);

// Fetch page results
$dataQuery = "SELECT * FROM tbl_storeprofile_master" . $whereSql . " ORDER BY id DESC LIMIT $offset, $perPage";
$rows = $db->select($dataQuery, $params, 'FETCH');

ob_start();
$modalData = '';
if (!empty($rows)) {
    $i = $offset + 1;
    foreach ($rows as $user) {
        $statusBadge = ($user['status'] ?? 'Active') === 'Active' 
            ? '<span class="label label-success badge-status-sm">Active</span>' 
            : '<span class="label label-danger badge-status-sm">Inactive</span>';
        ?>
        <tr>
            <td class="td-center-middle"><?php echo $i++; ?></td>
            <td class="td-center-middle-nowrap">
                <a class="btn btn-info btn-xs" data-toggle="tooltip" title="Edit Store Profile" href="fh_storeprofilecreation.php?update_id=<?php echo $user['id']; ?>">
                    <i class="fa fa-pencil"></i> Edit
                </a>
                <button type="button" class="btn btn-warning btn-xs" data-toggle="modal" data-target="#viewStoreProfileModal<?php echo $user['id']; ?>" title="View Details">
                    <i class="fa fa-eye"></i> Details
                </button>
                <a class="btn btn-danger btn-xs" data-toggle="tooltip" title="Delete Store Profile" href="fh_storeprofilecreation_list.php?delete_id=<?php echo $user['id']; ?>" onclick="return confirm('Are you sure you want to delete this record?');">
                    <i class="fa fa-trash-o"></i> Delete
                </a>
            </td>
            <td class="td-center-middle"><?php echo $statusBadge; ?></td>
            <td><?php echo htmlspecialchars($user['code'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($user['store_code'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($user['store_name'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($user['company_name'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($user['mobile_no1'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($user['gst_no'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($user['email_id'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($user['invoice_type'] ?? ''); ?></td>
        </tr>
        <?php
        $modalData .= '<div class="modal fade" id="viewStoreProfileModal' . $user['id'] . '" tabindex="-1" role="dialog" aria-hidden="true">
           <div class="modal-dialog modal-lg">
              <div class="modal-content modal-content-rounded">
                 <div class="modal-header modal-header-teal">
                    <button type="button" class="close modal-close-white" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title modal-title-bold">
                       <i class="fa fa-folder-open mr-8"></i>Store Profile Details - ' . htmlspecialchars($user['company_name'] ?? $user['store_name'] ?? '') . '
                    </h4>
                 </div>
                 <div class="modal-body modal-body-padded">
                    <table class="table table-bordered table-striped mb-0">
                       <tbody>
                          <tr>
                             <th class="th-w-25">Profile Code</th>
                             <td><code>' . htmlspecialchars($user['code'] ?? '') . '</code></td>
                             <th class="th-w-25">Store Code</th>
                             <td><code>' . htmlspecialchars($user['store_code'] ?? '') . '</code></td>
                          </tr>
                          <tr>
                             <th>Store Name</th>
                             <td><strong>' . htmlspecialchars($user['store_name'] ?? '') . '</strong></td>
                             <th>Company Name</th>
                             <td><strong>' . htmlspecialchars($user['company_name'] ?? '') . '</strong></td>
                          </tr>
                          <tr>
                             <th>Mobile No-1</th>
                             <td>' . htmlspecialchars($user['mobile_no1'] ?? '') . '</td>
                             <th>Mobile No-2</th>
                             <td>' . htmlspecialchars($user['mobile_no2'] ?? '') . '</td>
                          </tr>
                          <tr>
                             <th>Phone No</th>
                             <td>' . htmlspecialchars($user['phone_no'] ?? '') . '</td>
                             <th>GST No</th>
                             <td><code>' . htmlspecialchars($user['gst_no'] ?? '') . '</code></td>
                          </tr>
                          <tr>
                             <th>Email ID</th>
                             <td>' . htmlspecialchars($user['email_id'] ?? '') . '</td>
                             <th>Website Link</th>
                             <td>' . htmlspecialchars($user['website_link'] ?? '') . '</td>
                          </tr>
                          <tr>
                             <th>Address 1</th>
                             <td>' . htmlspecialchars($user['address1'] ?? '') . '</td>
                             <th>Address 2</th>
                             <td>' . htmlspecialchars($user['address2'] ?? '') . '</td>
                          </tr>
                          <tr>
                             <th>Terms & Conditions 1</th>
                             <td>' . htmlspecialchars($user['tc1'] ?? '') . '</td>
                             <th>Terms & Conditions 2</th>
                             <td>' . htmlspecialchars($user['tc2'] ?? '') . '</td>
                          </tr>
                          <tr>
                             <th>Invoice Type</th>
                             <td>' . htmlspecialchars($user['invoice_type'] ?? '') . '</td>
                             <th>Print Type</th>
                             <td>' . htmlspecialchars($user['print_type'] ?? '') . '</td>
                          </tr>
                          <tr>
                             <th>Default Print Copy</th>
                             <td>' . htmlspecialchars($user['defaultprint_copy'] ?? '') . '</td>
                             <th>Status</th>
                             <td>' . $statusBadge . '</td>
                          </tr>
                          ' . (!empty($user['additional_script']) ? '<tr><th>Additional Script</th><td colspan="3">' . htmlspecialchars($user['additional_script']) . '</td></tr>' : '') . '
                          ' . (!empty($user['welcome_script']) ? '<tr><th>Welcome Script</th><td colspan="3">' . htmlspecialchars($user['welcome_script']) . '</td></tr>' : '') . '
                       </tbody>
                    </table>
                 </div>
                 <div class="modal-footer modal-footer-light">
                    <a href="fh_storeprofilecreation.php?update_id=' . $user['id'] . '" class="btn btn-info btn-sm">
                       <i class="fa fa-pencil"></i> Edit Store Profile
                    </a>
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
                 </div>
              </div>
           </div>
        </div>';
    }
} else {
    echo '<tr><td colspan="11" class="text-center text-muted td-padded-20">No Store Profile Records Found</td></tr>';
}
$htmlData = ob_get_clean();

// Pagination HTML
ob_start();
if ($totalPages > 1) {
    echo '<ul class="pagination pagination-sm pagination-no-margin pull-right">';
    if ($page > 1) {
        echo '<li><a href="javascript:void(0)" onclick="loadStoreProfileCreationData(' . ($page - 1) . ')">&laquo; Prev</a></li>';
    } else {
        echo '<li class="disabled"><span>&laquo; Prev</span></li>';
    }

    $start = max(1, $page - 2);
    $end = min($totalPages, $page + 2);

    if ($start > 1) {
        echo '<li><a href="javascript:void(0)" onclick="loadStoreProfileCreationData(1)">1</a></li>';
        if ($start > 2) echo '<li class="disabled"><span>...</span></li>';
    }

    for ($p = $start; $p <= $end; $p++) {
        if ($p == $page) {
            echo '<li class="active"><span>' . $p . '</span></li>';
        } else {
            echo '<li><a href="javascript:void(0)" onclick="loadStoreProfileCreationData(' . $p . ')">' . $p . '</a></li>';
        }
    }

    if ($end < $totalPages) {
        if ($end < $totalPages - 1) echo '<li class="disabled"><span>...</span></li>';
        echo '<li><a href="javascript:void(0)" onclick="loadStoreProfileCreationData(' . $totalPages . ')">' . $totalPages . '</a></li>';
    }

    if ($page < $totalPages) {
        echo '<li><a href="javascript:void(0)" onclick="loadStoreProfileCreationData(' . ($page + 1) . ')">Next &raquo;</a></li>';
    } else {
        echo '<li class="disabled"><span>Next &raquo;</span></li>';
    }
    echo '</ul>';
}
$paginationData = ob_get_clean();

header('Content-Type: application/json');
echo json_encode(array(
    'htmlData' => $htmlData,
    'modalData' => $modalData,
    'pagination' => $paginationData,
    'totalRecords' => $totalRecords
));
