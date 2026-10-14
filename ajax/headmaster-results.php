<?php
include_once('../include/config.php');

$search = isset($_POST['search']) ? trim($_POST['search']) : '';
$page = isset($_POST['page']) && (int)$_POST['page'] > 0 ? (int)$_POST['page'] : 1;
$perPage = isset($_SESSION["_RECORDPERPAGE_"]) && $_SESSION["_RECORDPERPAGE_"] ? (int)$_SESSION["_RECORDPERPAGE_"] : 20;
$offset = ($page - 1) * $perPage;

$whereClauses = array();
$params = array();

if ($search !== '') {
    $whereClauses[] = "(head_code LIKE ? OR head_name LIKE ?)";
    $searchTerm = '%' . $search . '%';
    $params[] = $searchTerm;
    $params[] = $searchTerm;
}

$whereSql = '';
if (count($whereClauses) > 0) {
    $whereSql = ' WHERE ' . implode(' AND ', $whereClauses);
}

// Count total
$countQuery = "SELECT COUNT(*) as total FROM tbl_head_master" . $whereSql;
$countRes = $db->select($countQuery, $params, 'COUNT');
$totalRecords = isset($countRes[0]['total']) ? (int)$countRes[0]['total'] : 0;
$totalPages = ceil($totalRecords / $perPage);

// Fetch page results
$dataQuery = "SELECT * FROM tbl_head_master" . $whereSql . " ORDER BY id DESC LIMIT $offset, $perPage";
$rows = $db->select($dataQuery, $params, 'FETCH');

ob_start();
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
                <a class="btn btn-info btn-xs" data-toggle="tooltip" title="Edit Head" href="fh_headmastercreation.php?update_id=<?php echo $user['id']; ?>">
                    <i class="fa fa-pencil"></i> Edit
                </a>
                <a class="btn btn-danger btn-xs" data-toggle="tooltip" title="Delete Head" href="fh_headmastercreation_list.php?delete_id=<?php echo $user['id']; ?>" onclick="return confirm('Are you sure you want to delete this record?');">
                    <i class="fa fa-trash-o"></i> Delete
                </a>
            </td>
            <td class="td-center-middle"><?php echo $statusBadge; ?></td>
            <td><?php echo htmlspecialchars($user['head_code'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($user['head_name'] ?? ''); ?></td>
        </tr>
        <?php
    }
} else {
    echo '<tr><td colspan="5" class="text-center text-muted td-padded-20">No Head Records Found</td></tr>';
}
$htmlData = ob_get_clean();

// Pagination HTML
ob_start();
if ($totalPages > 1) {
    echo '<ul class="pagination pagination-sm pagination-no-margin pull-right">';
    if ($page > 1) {
        echo '<li><a href="javascript:void(0)" onclick="loadHeadMasterData(' . ($page - 1) . ')">&laquo; Prev</a></li>';
    } else {
        echo '<li class="disabled"><span>&laquo; Prev</span></li>';
    }

    $start = max(1, $page - 2);
    $end = min($totalPages, $page + 2);

    if ($start > 1) {
        echo '<li><a href="javascript:void(0)" onclick="loadHeadMasterData(1)">1</a></li>';
        if ($start > 2) echo '<li class="disabled"><span>...</span></li>';
    }

    for ($p = $start; $p <= $end; $p++) {
        if ($p == $page) {
            echo '<li class="active"><span>' . $p . '</span></li>';
        } else {
            echo '<li><a href="javascript:void(0)" onclick="loadHeadMasterData(' . $p . ')">' . $p . '</a></li>';
        }
    }

    if ($end < $totalPages) {
        if ($end < $totalPages - 1) echo '<li class="disabled"><span>...</span></li>';
        echo '<li><a href="javascript:void(0)" onclick="loadHeadMasterData(' . $totalPages . ')">' . $totalPages . '</a></li>';
    }

    if ($page < $totalPages) {
        echo '<li><a href="javascript:void(0)" onclick="loadHeadMasterData(' . ($page + 1) . ')">Next &raquo;</a></li>';
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
