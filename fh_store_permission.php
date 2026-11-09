<?php
include_once('include/config.php');
include_once('include/store_permissions.php');

$activeContext = get_active_store();
if (!$activeContext['is_admin']) {
    header("Location: fh_dashboard.php?error=unauthorized");
    exit();
}

$msg = '';
$allModules = get_erp_modules_catalog();

// Fetch all active stores
$storesQuery = mysqli_query($conn, "SELECT id, store_name, location_name FROM tbl_store_master WHERE status = 'true' ORDER BY store_name ASC");
$allStores = [];
while ($s = mysqli_fetch_assoc($storesQuery)) {
    $allStores[] = $s;
}

// Selected store to edit permissions for
$selectedStoreId = isset($_REQUEST['store_id']) ? (int)$_REQUEST['store_id'] : (isset($allStores[0]['id']) ? (int)$allStores[0]['id'] : 0);
$selectedStoreName = '';
foreach ($allStores as $s) {
    if ((int)$s['id'] === $selectedStoreId) {
        $selectedStoreName = $s['store_name'];
        break;
    }
}

// Handle Form Submission: Save Permissions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_permissions'])) {
    $targetStoreId = (int)$_POST['store_id'];
    $allowedPages = $_POST['pages'] ?? []; // array of page_url => 1

    if ($targetStoreId > 0) {
        // Resolve target store name
        $sNameStmt = mysqli_prepare($conn, "SELECT store_name FROM tbl_store_master WHERE id = ? LIMIT 1");
        mysqli_stmt_bind_param($sNameStmt, 'i', $targetStoreId);
        mysqli_stmt_execute($sNameStmt);
        $sNameRes = mysqli_stmt_get_result($sNameStmt);
        $tStoreName = '';
        if ($sRow = mysqli_fetch_assoc($sNameRes)) {
            $tStoreName = $sRow['store_name'];
        }

        // Delete existing permissions for target store
        $delStmt = mysqli_prepare($conn, "DELETE FROM tbl_store_menu_permissions WHERE store_id = ?");
        mysqli_stmt_bind_param($delStmt, 'i', $targetStoreId);
        mysqli_stmt_execute($delStmt);

        // Batch insert selected permissions
        if (!empty($allowedPages) && is_array($allowedPages)) {
            $insStmt = mysqli_prepare($conn, "INSERT INTO tbl_store_menu_permissions (store_id, store_name, module_key, page_url, can_view, can_add, can_edit, can_delete) VALUES (?, ?, ?, ?, 1, 1, 1, 1)");
            
            foreach ($allModules as $modKey => $module) {
                foreach ($module['sections'] as $secKey => $sec) {
                    foreach ($sec['pages'] as $pageFile => $pageLabel) {
                        $cleanPage = strtolower(trim($pageFile));
                        if (isset($allowedPages[$cleanPage])) {
                            mysqli_stmt_bind_param($insStmt, 'isss', $targetStoreId, $tStoreName, $modKey, $cleanPage);
                            mysqli_stmt_execute($insStmt);
                        }
                    }
                }
            }
        }

        $msg = '<div class="alert alert-success alert-dismissible fade in" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
            <strong>Success!</strong> Permissions for <b>' . htmlspecialchars($tStoreName) . '</b> have been updated successfully.
        </div>';
        $selectedStoreId = $targetStoreId;
        $selectedStoreName = $tStoreName;
    }
}

// Handle Copy Permissions from another store
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['copy_permissions'])) {
    $fromStoreId = (int)$_POST['copy_from_store_id'];
    $toStoreId   = (int)$_POST['copy_to_store_id'];

    if ($fromStoreId > 0 && $toStoreId > 0 && $fromStoreId !== $toStoreId) {
        // Resolve store name
        $sNameStmt = mysqli_prepare($conn, "SELECT store_name FROM tbl_store_master WHERE id = ? LIMIT 1");
        mysqli_stmt_bind_param($sNameStmt, 'i', $toStoreId);
        mysqli_stmt_execute($sNameStmt);
        $sNameRes = mysqli_stmt_get_result($sNameStmt);
        $tStoreName = '';
        if ($sRow = mysqli_fetch_assoc($sNameRes)) {
            $tStoreName = $sRow['store_name'];
        }

        // Delete existing
        $del = mysqli_prepare($conn, "DELETE FROM tbl_store_menu_permissions WHERE store_id = ?");
        mysqli_stmt_bind_param($del, 'i', $toStoreId);
        mysqli_stmt_execute($del);

        // Copy
        $copySql = "INSERT INTO tbl_store_menu_permissions (store_id, store_name, module_key, page_url, can_view, can_add, can_edit, can_delete)
                    SELECT ?, ?, module_key, page_url, can_view, can_add, can_edit, can_delete
                    FROM tbl_store_menu_permissions WHERE store_id = ?";
        $copyStmt = mysqli_prepare($conn, $copySql);
        mysqli_stmt_bind_param($copyStmt, 'isi', $toStoreId, $tStoreName, $fromStoreId);
        mysqli_stmt_execute($copyStmt);

        $msg = '<div class="alert alert-info alert-dismissible fade in" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
            <strong>Copied!</strong> Successfully copied permissions to <b>' . htmlspecialchars($tStoreName) . '</b>.
        </div>';
        $selectedStoreId = $toStoreId;
        $selectedStoreName = $tStoreName;
    }
}

// Fetch current permissions for selected store
$currentPermissions = get_store_permissions($selectedStoreId);
$hasConfigured = !empty($currentPermissions);

// Count total pages vs permitted pages
$totalPagesCount = 0;
$permittedPagesCount = 0;
foreach ($allModules as $module) {
    foreach ($module['sections'] as $sec) {
        foreach ($sec['pages'] as $pageFile => $pLabel) {
            $totalPagesCount++;
            $cleanP = strtolower(trim($pageFile));
            if ($hasConfigured) {
                if (!empty($currentPermissions[$cleanP])) {
                    $permittedPagesCount++;
                }
            } else {
                // If not configured yet, default is all allowed for admin
                $permittedPagesCount++;
            }
        }
    }
}

include('include/header.php');
?>

<style>
.perm-card {
    background: #fff;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 2px 6px rgba(0,0,0,0.04);
    margin-bottom: 22px;
    transition: transform 0.2s, box-shadow 0.2s;
}
.perm-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}
.perm-card-header {
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    padding: 12px 18px;
    border-top-left-radius: 8px;
    border-top-right-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.perm-card-title {
    font-size: 14.5px;
    font-weight: 700;
    color: #1e293b;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 8px;
}
.perm-card-title i {
    color: #009688;
}
.perm-card-body {
    padding: 16px 18px;
}
.perm-section-title {
    font-size: 13px;
    font-weight: 600;
    color: #475569;
    margin-bottom: 8px;
    border-bottom: 1px dashed #e2e8f0;
    padding-bottom: 4px;
}
.perm-page-item {
    margin-bottom: 8px;
}
.perm-page-item label {
    font-weight: 400;
    cursor: pointer;
    margin-bottom: 0;
    display: flex;
    align-items: center;
    gap: 7px;
    font-size: 12.5px;
    color: #334155;
}
.perm-page-item input[type="checkbox"] {
    margin: 0;
    width: 16px;
    height: 16px;
    cursor: pointer;
}
.stat-box-modern {
    background: #fff;
    border-radius: 8px;
    padding: 15px 20px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 2px 6px rgba(0,0,0,0.03);
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.stat-box-modern .val {
    font-size: 22px;
    font-weight: 700;
    color: #0f172a;
}
.stat-box-modern .lbl {
    font-size: 12px;
    color: #64748b;
    text-transform: uppercase;
    font-weight: 600;
}
.btn-preset {
    margin-right: 6px;
    margin-bottom: 6px;
    font-size: 12px;
    padding: 5px 12px;
    border-radius: 20px;
}
.sticky-save-bar {
    position: sticky;
    bottom: 15px;
    background: #1e293b;
    color: #fff;
    padding: 12px 20px;
    border-radius: 8px;
    box-shadow: 0 6px 20px rgba(0,0,0,0.25);
    z-index: 1000;
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: 20px;
}
</style>

<div class="content">
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-bd lobidisable">
                <div class="panel-heading">
                    <div class="panel-title" style="display:flex; justify-content:space-between; align-items:center;">
                        <h4><i class="fa fa-key text-primary"></i> Store-Wise Menu & Page Permissions</h4>
                        <div>
                            <button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#copyPermModal">
                                <i class="fa fa-copy"></i> Copy Permissions to Store
                            </button>
                        </div>
                    </div>
                </div>

                <div class="panel-body">
                    <?= $msg ?>

                    <!-- Store Selector Row -->
                    <div class="row" style="margin-bottom: 20px;">
                        <div class="col-md-5">
                            <label style="font-size: 13.5px; font-weight:600; color:#1e293b;">
                                <i class="fa fa-building text-primary"></i> Select Store to Configure:
                            </label>
                            <form method="get" id="storeSelectForm" action="fh_store_permission.php">
                                <select name="store_id" class="form-control" style="font-size:14px; font-weight:600; height:40px;" onchange="document.getElementById('storeSelectForm').submit();">
                                    <?php foreach ($allStores as $s): ?>
                                        <option value="<?= $s['id'] ?>" <?= ((int)$s['id'] === $selectedStoreId) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($s['store_name']) ?> (<?= htmlspecialchars($s['location_name']) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </form>
                        </div>
                        <div class="col-md-7">
                            <div class="row">
                                <div class="col-xs-6 col-sm-4">
                                    <div class="stat-box-modern">
                                        <div>
                                            <div class="lbl">Configured Store</div>
                                            <div class="val" style="font-size:16px;"><?= htmlspecialchars($selectedStoreName) ?></div>
                                        </div>
                                        <i class="fa fa-store fa-2x" style="color:#0284c7; opacity:0.3;"></i>
                                    </div>
                                </div>
                                <div class="col-xs-6 col-sm-4">
                                    <div class="stat-box-modern">
                                        <div>
                                            <div class="lbl">Allowed Pages</div>
                                            <div class="val" style="color:#10b981;" id="stat_allowed_count"><?= $permittedPagesCount ?></div>
                                        </div>
                                        <i class="fa fa-check-circle fa-2x" style="color:#10b981; opacity:0.3;"></i>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-4">
                                    <div class="stat-box-modern">
                                        <div>
                                            <div class="lbl">Total ERP Pages</div>
                                            <div class="val" style="color:#64748b;"><?= $totalPagesCount ?></div>
                                        </div>
                                        <i class="fa fa-cubes fa-2x" style="color:#64748b; opacity:0.3;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Preset Toolbar -->
                    <div class="well well-sm" style="background:#f1f5f9; border:1px solid #cbd5e1; border-radius:6px; margin-bottom:20px;">
                        <span style="font-weight:700; font-size:12px; color:#475569; margin-right:10px;">QUICK ACTIONS:</span>
                        <button type="button" class="btn btn-default btn-xs btn-preset" onclick="toggleAllPages(true);"><i class="fa fa-check-square"></i> Select All</button>
                        <button type="button" class="btn btn-default btn-xs btn-preset" onclick="toggleAllPages(false);"><i class="fa fa-square-o"></i> Deselect All</button>
                        <span style="border-left:1px solid #cbd5e1; margin:0 8px;"></span>
                        <span style="font-weight:700; font-size:12px; color:#475569; margin-right:6px;">PRESETS:</span>
                        <button type="button" class="btn btn-primary btn-xs btn-preset" onclick="applyPreset('pos_sales');"><i class="fa fa-shopping-cart"></i> POS & Billing Only</button>
                        <button type="button" class="btn btn-info btn-xs btn-preset" onclick="applyPreset('inventory_stock');"><i class="fa fa-cubes"></i> Inventory & Stock Only</button>
                        <button type="button" class="btn btn-warning btn-xs btn-preset" onclick="applyPreset('sales_accounts');"><i class="fa fa-book"></i> Billing + Accounts</button>
                    </div>

                    <!-- Permissions Form -->
                    <form method="post" id="permissionsForm" action="fh_store_permission.php">
                        <input type="hidden" name="store_id" value="<?= $selectedStoreId ?>">
                        <input type="hidden" name="save_permissions" value="1">

                        <div class="row">
                            <?php foreach ($allModules as $modKey => $module): ?>
                                <div class="col-md-6 col-lg-4">
                                    <div class="perm-card" id="card_<?= $modKey ?>">
                                        <div class="perm-card-header">
                                            <h5 class="perm-card-title">
                                                <i class="<?= $module['icon'] ?>"></i> <?= htmlspecialchars($module['label']) ?>
                                            </h5>
                                            <label style="font-size:11.5px; font-weight:600; color:#64748b; cursor:pointer; margin:0;">
                                                <input type="checkbox" class="mod-toggle" data-module="<?= $modKey ?>" onchange="toggleModuleGroup('<?= $modKey ?>', this.checked)"> All
                                            </label>
                                        </div>
                                        <div class="perm-card-body">
                                            <?php foreach ($module['sections'] as $secKey => $sec): ?>
                                                <div class="perm-section-title">
                                                    <i class="<?= $sec['icon'] ?>" style="font-size:11px; margin-right:4px;"></i> <?= htmlspecialchars($sec['label']) ?>
                                                </div>
                                                <div style="margin-bottom:12px; padding-left:4px;">
                                                    <?php foreach ($sec['pages'] as $pageFile => $pLabel): 
                                                        $cleanP = strtolower(trim($pageFile));
                                                        $isChecked = $hasConfigured ? (!empty($currentPermissions[$cleanP])) : true;
                                                    ?>
                                                        <div class="perm-page-item">
                                                            <label>
                                                                <input type="checkbox" 
                                                                       name="pages[<?= $cleanP ?>]" 
                                                                       value="1" 
                                                                       class="page-checkbox mod-item-<?= $modKey ?>" 
                                                                       data-module="<?= $modKey ?>"
                                                                       data-page="<?= $cleanP ?>"
                                                                       <?= $isChecked ? 'checked' : '' ?>
                                                                       onchange="updateCounters();">
                                                                <span><?= htmlspecialchars($pLabel) ?></span>
                                                                <small style="color:#94a3b8; font-size:10px;">(<?= htmlspecialchars($pageFile) ?>)</small>
                                                            </label>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Sticky Footer Save Bar -->
                        <div class="sticky-save-bar">
                            <div>
                                <i class="fa fa-info-circle text-info"></i> Saving permissions for <b><?= htmlspecialchars($selectedStoreName) ?></b>. Users assigned to this store will only see and access these checked pages.
                            </div>
                            <div>
                                <button type="submit" class="btn btn-success" style="font-weight:700; padding:8px 24px;">
                                    <i class="fa fa-save"></i> Save Permissions
                                </button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Copy Permissions -->
<div class="modal fade" id="copyPermModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="post" action="fh_store_permission.php">
                <input type="hidden" name="copy_permissions" value="1">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">×</button>
                    <h4 class="modal-title"><i class="fa fa-copy"></i> Copy Store Permissions</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Copy From Source Store:</label>
                        <select name="copy_from_store_id" class="form-control" required>
                            <option value="">-- Choose Source Store --</option>
                            <?php foreach ($allStores as $s): ?>
                                <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['store_name']) ?> (<?= htmlspecialchars($s['location_name']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Copy To Target Store:</label>
                        <select name="copy_to_store_id" class="form-control" required>
                            <option value="">-- Choose Target Store --</option>
                            <?php foreach ($allStores as $s): ?>
                                <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['store_name']) ?> (<?= htmlspecialchars($s['location_name']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <p class="text-muted" style="font-size:12px;">
                        <i class="fa fa-warning text-warning"></i> Warning: This will overwrite existing permissions on the target store.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="fa fa-check"></i> Copy Permissions Now</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include('include/footer-2.php'); ?>

<script>
function toggleAllPages(status) {
    document.querySelectorAll('.page-checkbox').forEach(function(cb) {
        cb.checked = status;
    });
    document.querySelectorAll('.mod-toggle').forEach(function(cb) {
        cb.checked = status;
    });
    updateCounters();
}

function toggleModuleGroup(modKey, status) {
    document.querySelectorAll('.mod-item-' + modKey).forEach(function(cb) {
        cb.checked = status;
    });
    updateCounters();
}

function updateCounters() {
    var totalChecked = document.querySelectorAll('.page-checkbox:checked').length;
    var countEl = document.getElementById('stat_allowed_count');
    if (countEl) countEl.innerText = totalChecked;

    // Sync module header checkboxes
    document.querySelectorAll('.mod-toggle').forEach(function(mToggle) {
        var modKey = mToggle.getAttribute('data-module');
        var items = document.querySelectorAll('.mod-item-' + modKey);
        var checkedItems = document.querySelectorAll('.mod-item-' + modKey + ':checked');
        mToggle.checked = (items.length > 0 && items.length === checkedItems.length);
    });
}

function applyPreset(presetType) {
    toggleAllPages(false);
    var allowedPages = [];

    if (presetType === 'pos_sales') {
        allowedPages = [
            'sale-creations.php',
            'tejaserp-sale-billwise-list.php',
            'tejaserp-sale-itemwise-list.php',
            'tejaserp-sale-dsr-1.php',
            'tejaserp-sale-dsr-2.php',
            'tejaserp-stockreport-indetails.php',
            'tejaserp-stockreport-inshortcut.php'
        ];
    } else if (presetType === 'inventory_stock') {
        allowedPages = [
            'purchase-master-creation.php',
            'purchase-master-billwise-list.php',
            'purchase-master-itemwise-list.php',
            'purchase-master-report.php',
            'stock-transfer.php',
            'store-items-list.php',
            'returned-store-items-list.php',
            'tejaserp-stockreport-indetails.php',
            'tejaserp-stockreport-inshortcut.php',
            'tejaserp-inventorybarcodeprint.php',
            'tejaserp-inventorybarcodeprint-list.php'
        ];
    } else if (presetType === 'sales_accounts') {
        allowedPages = [
            'sale-creations.php',
            'tejaserp-sale-billwise-list.php',
            'tejaserp-sale-itemwise-list.php',
            'tejaserp-sale-dsr-1.php',
            'tejaserp-sale-dsr-2.php',
            'fh_accountmastercreation.php',
            'fh_accountmastercreation_list.php',
            'fh_expensesmastercreation.php',
            'fh_expensesmastercreation_list.php',
            'fh_paymentmastercreation.php',
            'fh_paymentmastercreation_list.php',
            'fh_expensesmasterbalancesheet.php',
            'tejaserp-stockreport-indetails.php',
            'tejaserp-stockreport-inshortcut.php'
        ];
    }

    allowedPages.forEach(function(page) {
        var cb = document.querySelector('.page-checkbox[data-page="' + page.toLowerCase() + '"]');
        if (cb) cb.checked = true;
    });

    updateCounters();
}

// Initial sync on load
document.addEventListener('DOMContentLoaded', function() {
    updateCounters();
});
</script>

