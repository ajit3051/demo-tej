<?php
include_once(__DIR__ . '/../include/config.php');

$search = isset($_POST['search']) ? trim($_POST['search']) : '';
$page = isset($_POST['page']) && (int)$_POST['page'] > 0 ? (int)$_POST['page'] : 1;
$perPage = isset($_SESSION["_RECORDPERPAGE_"]) && $_SESSION["_RECORDPERPAGE_"] ? (int)$_SESSION["_RECORDPERPAGE_"] : 20;
$offset = ($page - 1) * $perPage;

$whereSql = '';
if ($search !== '') {
    $s = mysqli_real_escape_string($conn, $search);
    $whereSql = " WHERE (account_code LIKE '%$s%' OR account_name LIKE '%$s%' OR contact_name LIKE '%$s%' OR group_name LIKE '%$s%' OR mobile_no_1 LIKE '%$s%' OR gst_no LIKE '%$s%' OR location_1 LIKE '%$s%' OR party_type LIKE '%$s%')";
}

// Count total
$countQuery = mysqli_query($conn, "SELECT COUNT(*) as total FROM tbl_account_master" . $whereSql);
$countRow = mysqli_fetch_assoc($countQuery);
$totalRecords = isset($countRow['total']) ? (int)$countRow['total'] : 0;
$totalPages = ceil($totalRecords / $perPage);

// Fetch page results
$dataQuery = mysqli_query($conn, "SELECT * FROM tbl_account_master" . $whereSql . " ORDER BY id DESC LIMIT $offset, $perPage");

ob_start();
if ($dataQuery && mysqli_num_rows($dataQuery) > 0) {
    $i = $offset + 1;
    while ($user = mysqli_fetch_assoc($dataQuery)) {
        $statusBadge = ($user['status'] ?? 'Active') === 'Active' 
            ? '<span class="label label-success badge-status-sm">Active</span>' 
            : '<span class="label label-danger badge-status-sm">Inactive</span>';
        ?>
        <tr>
            <td class="td-center-middle"><?php echo $i++; ?></td>
            <td class="td-center-middle-nowrap">
                <a class="btn btn-info btn-xs" data-toggle="tooltip" title="Edit Account" href="fh_accountmastercreation.php?update_id=<?php echo $user['id']; ?>">
                    <i class="fa fa-pencil"></i> Edit
                </a>
                <a class="btn btn-warning btn-xs" data-toggle="tooltip" title="View Ledger" href="fh_account_ledger.php?account_code=<?php echo urlencode($user['account_code'] ?? ''); ?>&account_id=<?php echo $user['id']; ?>">
                    <i class="fa fa-book"></i> Ledger
                </a>
                <a class="btn btn-danger btn-xs" data-toggle="tooltip" title="Delete Account" href="fh_accountmastercreation_list.php?delete_id=<?php echo $user['id']; ?>" onclick="return confirm('Are you sure you want to delete this record?');">
                    <i class="fa fa-trash-o"></i> Delete
                </a>
            </td>
            <td class="td-center-middle"><?php echo $statusBadge; ?></td>
            <td><?php echo htmlspecialchars($user['account_code'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($user['account_name'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($user['contact_name'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($user['group_name'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($user['mobile_no_1'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($user['gst_no'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($user['location_1'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($user['party_type'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($user['opening_balance'] ?? ''); ?></td>
        </tr>
        <?php
    }
} else {
    echo '<tr><td colspan="12" class="text-center text-muted td-padded-20">No Account Records Found</td></tr>';
}
$htmlData = ob_get_clean();

// Pagination HTML
ob_start();
if ($totalPages > 1) {
    echo '<ul class="pagination pagination-sm pagination-no-margin pull-right">';
    if ($page > 1) {
        echo '<li><a href="javascript:void(0)" onclick="loadAccountMasterData(' . ($page - 1) . ')">&laquo; Prev</a></li>';
    } else {
        echo '<li class="disabled"><span>&laquo; Prev</span></li>';
    }

    $start = max(1, $page - 2);
    $end = min($totalPages, $page + 2);

    if ($start > 1) {
        echo '<li><a href="javascript:void(0)" onclick="loadAccountMasterData(1)">1</a></li>';
        if ($start > 2) echo '<li class="disabled"><span>...</span></li>';
    }

    for ($p = $start; $p <= $end; $p++) {
        if ($p == $page) {
            echo '<li class="active"><span>' . $p . '</span></li>';
        } else {
            echo '<li><a href="javascript:void(0)" onclick="loadAccountMasterData(' . $p . ')">' . $p . '</a></li>';
        }
    }

    if ($end < $totalPages) {
        if ($end < $totalPages - 1) echo '<li class="disabled"><span>...</span></li>';
        echo '<li><a href="javascript:void(0)" onclick="loadAccountMasterData(' . $totalPages . ')">' . $totalPages . '</a></li>';
    }

    if ($page < $totalPages) {
        echo '<li><a href="javascript:void(0)" onclick="loadAccountMasterData(' . ($page + 1) . ')">Next &raquo;</a></li>';
    } else {
        echo '<li class="disabled"><span>Next &raquo;</span></li>';
    }
    echo '</ul>';
}
$paginationData = ob_get_clean();

header('Content-Type: application/json');
echo json_encode(array(
    'htmlData' => $htmlData,
    'pagination' => $paginationData,
    'totalRecords' => $totalRecords
));
