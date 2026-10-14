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

$query = "SELECT * FROM tbl_item_master WHERE id > 0";

if ($searchKey !== '') {
   $query .= " AND (item_code LIKE CONCAT('%', ?, '%') 
               OR barcode_no LIKE CONCAT('%', ?, '%') 
               OR sku_no LIKE CONCAT('%', ?, '%') 
               OR item_name LIKE CONCAT('%', ?, '%') 
               OR group_name LIKE CONCAT('%', ?, '%') 
               OR subgroup_name LIKE CONCAT('%', ?, '%') 
               OR brand_name LIKE CONCAT('%', ?, '%') 
               OR status LIKE CONCAT('%', ?, '%'))";
   $params .= 'ssssssss';
   $fields[] = $searchKey;
   $fields[] = $searchKey;
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

      // Fetch images from tbl_item_images table
      $allPics = array();
      $imgStmt = $db->select("SELECT * FROM tbl_item_images WHERE item_id = ? ORDER BY sort_order ASC, id ASC", "i", array($user['id']));
      if ($imgStmt && $imgStmt->num_rows() > 0) {
         while ($imgRow = $imgStmt->fetch_assoc()) {
            if (!empty($imgRow['image_name']) && file_exists("../uploads/item-master/" . $imgRow['image_name'])) {
               $allPics[] = array(
                  'url' => "uploads/item-master/" . $imgRow['image_name'],
                  'title' => $imgRow['image_title'] ?: 'Product Image'
               );
            }
         }
      }

      if (!empty($allPics)) {
         $mainImg = $allPics[0]['url'];
         $imgHtml = '<img src="' . $mainImg . '" class="img-thumbnail thumb-img-40" alt="Item Image">';
         $galleryHtml = '<div class="item-gallery-wrap text-center mb-15">
            <img src="' . $mainImg . '" id="modalMainImg' . $user['id'] . '" class="img-responsive img-thumbnail modal-thumb-lg" style="max-height: 220px; margin: 0 auto 10px auto;" alt="Item Image">';
         if (count($allPics) > 1) {
            $galleryHtml .= '<div class="item-thumbs-grid" style="display: flex; flex-wrap: wrap; gap: 6px; justify-content: center; margin-top: 8px;">';
            foreach ($allPics as $pIdx => $pInfo) {
               $galleryHtml .= '<img src="' . $pInfo['url'] . '" class="img-thumbnail" style="width: 45px; height: 45px; object-fit: cover; cursor: pointer; border: 2px solid ' . ($pIdx === 0 ? '#3f51b5' : '#e0e0e0') . ';" onclick="document.getElementById(\'modalMainImg' . $user['id'] . '\').src=\'' . $pInfo['url'] . '\';" title="' . htmlspecialchars($pInfo['title']) . '" alt="Thumb ' . ($pIdx + 1) . '">';
            }
            $galleryHtml .= '</div>';
         }
         $galleryHtml .= '</div>';
      } else {
         $imgHtml = '<span class="text-muted"><i class="fa fa-image fa-2x"></i></span>';
         $galleryHtml = '<div class="no-img-box text-center" style="padding: 30px 15px; background: #f8f9fa; border: 1px dashed #ccc; border-radius: 4px;"><i class="fa fa-image fa-4x text-muted"></i><br><span class="text-muted" style="font-weight:500; display:inline-block; margin-top:8px;">No Images Uploaded</span></div>';
      }

      // Table Row
      $html .= '<tr>
         <td class="td-center-middle">' . $i++ . '</td>
         <td class="td-center-middle-nowrap">
            <a class="btn btn-info btn-xs" data-toggle="tooltip" title="Edit Item" href="fh_itemmastercreation.php?update_id=' . $user['id'] . '">
               <i class="fa fa-pencil"></i> Edit
            </a>
            <button type="button" class="btn btn-warning btn-xs" data-toggle="modal" data-target="#viewItemModal' . $user['id'] . '" title="View Details">
               <i class="fa fa-eye"></i> Details
            </button>
            <a class="btn btn-danger btn-xs" data-toggle="tooltip" title="Delete Item" onClick="return confirm(\'Are you sure you want to Delete this Item?\')" href="fh_itemmastercreation_list.php?delete_id=' . $user['id'] . '">
               <i class="fa fa-trash-o"></i> Delete
            </a>
         </td>
         <td class="td-center-middle">' . $statusHtml . '</td>
         <td class="td-center-middle">' . $imgHtml . '</td>
         <td class="td-middle"><code>' . htmlspecialchars($user['item_code'] ?? '') . '</code></td>
         <td class="td-middle-bold">' . htmlspecialchars(substr($user['item_name'] ?? '', 0, 50)) . '</td>
         <td class="td-middle">' . htmlspecialchars($user['barcode_no'] ?? '') . '</td>
         <td class="td-middle">' . htmlspecialchars($user['sku_no'] ?? '') . '</td>
         <td class="td-middle">' . htmlspecialchars($user['group_name'] ?? '') . '</td>
         <td class="td-middle">' . htmlspecialchars($user['subgroup_name'] ?? '') . '</td>
         <td class="td-middle">' . htmlspecialchars($user['brand_name'] ?? '') . '</td>
         <td class="td-middle">' . htmlspecialchars($user['mrp'] ?? '') . '</td>
         <td class="td-middle">' . htmlspecialchars($user['sp'] ?? '') . '</td>
      </tr>';

      // Fetch item variations
      $varHtml = '';
      $varStmt = $db->select("SELECT * FROM tbl_item_variations WHERE item_id = ? ORDER BY id ASC", "i", array($user['id']));
      if ($varStmt && $varStmt->num_rows() > 0) {
         $varHtml .= '<div class="row mt-15"><div class="col-sm-12"><h5 class="text-primary"><strong>Item Variations & Variant Prices</strong></h5><table class="table table-bordered table-condensed table-striped mb-0"><thead><tr class="info"><th>Color</th><th>Size</th><th>Style / Design</th><th>Qty</th><th>CP</th><th>MRP</th><th>Disc %</th><th>Disc Amt</th><th>SP</th></tr></thead><tbody>';
         while ($vRow = $varStmt->fetch_assoc()) {
            $varHtml .= '<tr>
               <td>' . htmlspecialchars($vRow['color_name'] ?: '-') . '</td>
               <td>' . htmlspecialchars($vRow['size_name'] ?: '-') . '</td>
               <td>' . htmlspecialchars($vRow['style_name'] ?: '-') . '</td>
               <td>' . htmlspecialchars($vRow['qty'] ?: '0') . '</td>
               <td>₹' . htmlspecialchars($vRow['purchase_price'] ?: '0') . '</td>
               <td>₹' . htmlspecialchars($vRow['mrp'] ?: '0') . '</td>
               <td>' . htmlspecialchars($vRow['dis_per'] ?: '0') . '%</td>
               <td>₹' . htmlspecialchars($vRow['dis_amt'] ?: '0') . '</td>
               <td>₹' . htmlspecialchars($vRow['sp'] ?: '0') . '</td>
            </tr>';
         }
         $varHtml .= '</tbody></table></div></div>';
      }

      // Modal Details Window
      $modalsHtml .= '<div class="modal fade" id="viewItemModal' . $user['id'] . '" tabindex="-1" role="dialog" aria-hidden="true">
         <div class="modal-dialog modal-lg">
            <div class="modal-content modal-content-rounded">
               <div class="modal-header modal-header-teal">
                  <button type="button" class="close modal-close-white" data-dismiss="modal" aria-hidden="true">×</button>
                  <h4 class="modal-title modal-title-bold">
                     <i class="fa fa-cubes mr-8"></i>Item Details - ' . htmlspecialchars($user['item_name'] ?? '') . '
                  </h4>
               </div>
               <div class="modal-body modal-body-padded">
                  <div class="row">
                     <div class="col-sm-4 text-center mb-15">
                        ' . $galleryHtml . '
                     </div>
                     <div class="col-sm-8">
                        <table class="table table-bordered table-striped mb-0">
                           <tbody>
                              <tr>
                                 <th class="th-w-35">Item Code</th>
                                 <td><code>' . htmlspecialchars($user['item_code'] ?? '') . '</code></td>
                              </tr>
                              <tr>
                                 <th>Item Name</th>
                                 <td><strong>' . htmlspecialchars($user['item_name'] ?? '') . '</strong></td>
                              </tr>
                              <tr>
                                 <th>Barcode No / SKU</th>
                                 <td>' . htmlspecialchars($user['barcode_no'] ?? '') . ' / ' . htmlspecialchars($user['sku_no'] ?? '') . '</td>
                              </tr>
                              <tr>
                                 <th>Category / SubGroup</th>
                                 <td>' . htmlspecialchars($user['group_name'] ?? '') . ' &rarr; ' . htmlspecialchars($user['subgroup_name'] ?? '') . '</td>
                              </tr>
                              <tr>
                                 <th>Brand / Style / Color / Size</th>
                                 <td>' . htmlspecialchars($user['brand_name'] ?? '') . ' | ' . htmlspecialchars($user['style_name'] ?? '') . ' | ' . htmlspecialchars($user['color_name'] ?? '') . ' | ' . htmlspecialchars($user['size_name'] ?? '') . '</td>
                              </tr>
                              <tr>
                                 <th>MRP / SP / Purchase Price</th>
                                 <td><strong>MRP:</strong> ₹' . htmlspecialchars($user['mrp'] ?? '0') . ' | <strong>SP:</strong> ₹' . htmlspecialchars($user['sp'] ?? '0') . ' | <strong>CP:</strong> ₹' . htmlspecialchars($user['purchase_price'] ?? '0') . '</td>
                              </tr>
                              <tr>
                                 <th>Discount / Profit</th>
                                 <td>' . htmlspecialchars($user['dis_per'] ?? '0') . '% (₹' . htmlspecialchars($user['dis_amt'] ?? '0') . ') | Profit: ₹' . htmlspecialchars($user['profit_amt'] ?? '0') . '</td>
                              </tr>
                              <tr>
                                 <th>Minimum Quantity</th>
                                 <td>' . htmlspecialchars($user['min_qty'] ?? '0') . '</td>
                              </tr>
                              <tr>
                                 <th>Measurement of Units</th>
                                 <td>' . htmlspecialchars($user['mou_name'] ?? 'N/A') . '</td>
                              </tr>
                              <tr>
                                 <th>Created / Updated</th>
                                 <td>Created: ' . htmlspecialchars($user['created_date'] ?? 'N/A') . ' | Updated: ' . htmlspecialchars($user['update_date'] ?? 'N/A') . '</td>
                              </tr>
                              <tr>
                                 <th>Status</th>
                                 <td>' . $statusHtml . '</td>
                              </tr>
                           </tbody>
                        </table>
                     </div>
                  </div>
                  ' . $varHtml . '
               </div>
               <div class="modal-footer modal-footer-light">
                  <a href="fh_itemmastercreation.php?update_id=' . $user['id'] . '" class="btn btn-info btn-sm">
                     <i class="fa fa-pencil"></i> Edit Item
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
      <td colspan="13" class="text-center td-padded-20">No item records found.</td>
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
