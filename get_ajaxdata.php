<?php
include_once("include/config.php");
$function = $_GET['function'];

$validationHelper = new validation();
// ini_set('display_errors', 1);
// error_reporting(E_ALL);

if($function=='setPerpageValue'){
	
    $_SESSION["_RECORDPERPAGE_"] = $_GET['selectedval']; die;

} else if ($function == 'get_account_details') {

    $type = $_POST['type'];
    $searchKey = $_POST['term'];

    $params = '';
    $fields = array();

    $db = connect();
    
    $query = "SELECT * FROM tbl_account_master WHERE id > 0";
    if ($type == 'mobile') {

        $query .= " AND (mobile_no_1 LIKE CONCAT('%', ?, '%') OR mobile_no_2 LIKE CONCAT('%', ?, '%') OR  mobile_no_3 LIKE CONCAT('%', ?, '%'))";

        $params .= 'sss';
        $fields[] = $searchKey;
        $fields[] = $searchKey;
        $fields[] = $searchKey;
    }

    if ($type == 'account_name') {

        $query .= " AND (account_name LIKE CONCAT('%', ?, '%'))";

        $params .= 's';
        $fields[] = $searchKey;
    }

    if ($type == 'ac_code') {

        $query .= " AND (account_code LIKE CONCAT('%', ?, '%'))";

        $params .= 's';
        $fields[] = $searchKey;
    }

    if ($type == 'id') {

        $query .= " AND id =?";

        $params .= 'i';
        $fields[] = $searchKey;
    }

    if (empty($fields)) {

        $stmt = $db->select($query);
    } else {
        $stmt = $db->select($query, $params, $fields);
    }

    $res = $stmt->num_rows();

    if ($res) {
        $suggestions = [];
        while ($row = $stmt->fetch_assoc()) {

            if ($type == 'id') {
                $suggestions[] = [
                    'Account Code' => $row['account_code'],
                    'Account Name' => $row['account_name'],
                    'Contact Name' => $row['contact_name'],
                    'Group Name' => $row['group_name'],
                    'Mobile No. 1' => $row['mobile_no_1'],
                    'Mobile No. 2' => $row['mobile_no_2']
                ];
            } else {
                $suggestions[$row['id']] = [
                    'display'      => $row['account_code'] . ' | ' . $row['account_name'] . ' | ' . $row['mobile_no_1'],
                    'account_code' => $row['account_code'],
                    'account_name' => $row['account_name'],
                    'mobile_no'    => $row['mobile_no_1'],
                ];
            }
            
        }

        echo json_encode(array('status' => true, 'suggestions' => $suggestions));
        die;
    } else {
        echo json_encode(array('status' => false));
        die;
    }
} else if ($function == 'get_item_details') {

    $type = $_POST['type'];
    $searchKey = $_POST['term'];

    $params = '';
    $fields = array();

    $db = connect();
    $query = "SELECT * FROM tbl_item_master WHERE id > 0";

    if ($type == 'item_name') {

        $query .= " AND (item_name LIKE CONCAT('%', ?, '%'))";

        $params .= 's';
        $fields[] = $searchKey;
    }
    if ($type == 'article_no') {

        $query .= " AND (sku_no LIKE CONCAT('%', ?, '%'))";

        $params .= 's';
        $fields[] = $searchKey;
    }

    if ($type == 'id') {

        $query .= " AND id =?";

        $params .= 'i';
        $fields[] = $searchKey;
    }

    if (empty($fields)) {

        $stmt = $db->select($query);
    } else {
        $stmt = $db->select($query, $params, $fields);
    }

    $res = $stmt->num_rows();

    if ($res) {
        $suggestions = [];
        while ($row = $stmt->fetch_assoc()) {

            if ($type == 'id') {
                $suggestions[] = [
                    'Item Name' => $row['item_name'],
                    'Barcode No.' => $row['barcode_no'],
                    'Brand Name' => $row['brand_name'],
                    'Rate' => $row['mrp']
                ];
            } else {
                $suggestions[$row['id']] = $row['item_name'] . ', ' . $row['description'] . ', ' . $row['barcode_no'] . ', ' . $row['mrp'];
            }
            
        }

        echo json_encode(array('status' => true, 'suggestions' => $suggestions));
        die;
    } else {
        echo json_encode(array('status' => false));
        die;
    }
} else if ($function == 'get_purchase_details') {

    $type = $_POST['type'];
    $searchKey = $_POST['term'];

    $params = '';
    $fields = array();

    $db = connect();
    $query = "SELECT * FROM tbl_purchase WHERE status = 'success'";

    if ($type == 'item_name') {

        $query .= " AND (item_name LIKE CONCAT('%', ?, '%'))";

        $params .= 's';
        $fields[] = $searchKey;
    }

    if ($type == 'id') {

        $query .= " AND id =?";

        $params .= 'i';
        $fields[] = $searchKey;
    }

    if (empty($fields)) {

        $stmt = $db->select($query);
    } else {
        $stmt = $db->select($query, $params, $fields);
    }

    $res = $stmt->num_rows();

    if ($res) {
        $suggestions = [];
        while ($row = $stmt->fetch_assoc()) {

            if ($type == 'id') {
                $suggestions[] = [
                    'Item Name' => $row['item_name'],
                    'Barcode No.' => $row['barcode_number'],
                    'Brand Name' => $row['brand'],
                    'Rate' => $row['rate']
                ];
            } else {
                $suggestions[$row['id']] = $row['item_name'] . ', ' . $row['category'] . ', ' . $row['barcode_number'] . ', ' . $row['rate'];
            }
            
        }

        echo json_encode(array('status' => true, 'suggestions' => $suggestions));
        die;
    } else {
        echo json_encode(array('status' => false));
        die;
    }
} else if ($function == 'get_item_info') {

    $db = connect();

    $html = "";
    $gst_type = $validationHelper->filterText($_POST['gst_type']);
    $under_gst = $validationHelper->filterText($_POST['under_gst']);
    $trcount = $validationHelper->filterText($_POST['trcount']);
    $barcode = $validationHelper->filterText($_POST['barcode']);
    $query = $db->select("SELECT * FROM tbl_item_master WHERE barcode_no =? AND status=?", 'ss', $barcode, 'true');
  
    $row = $query->fetch_assoc();
    $count = $query->num_rows; 


    // Gst % from gst master
    $gst_per = 0.0;
    
    if (!empty($row['group_name'])) {
        $gst_query = $db->select("SELECT * FROM tbl_gst_master WHERE group_name = ? AND status = ?", 'ss', $row['group_name'], 'true');
     
        if ($gst_query && $gst_row = $gst_query->fetch_assoc()) {
            $gst_per = (float) $gst_row['gst_per'];
        }
    }
   $net_price = $row['purchase_price'];
    if ($gst_type == 'Inclusive') {
        $net_price = (float) $net_price;
        
        
        $gst_amount_temp = ($net_price * $gst_per) / (100 + $gst_per);
        $gst_amount = sprintf('%0.2f', $gst_amount_temp);
        $amount = sprintf('%0.2f', $net_price);
    
    } else if ($gst_type == 'Exclusive') {
        $net_price = (float) $net_price;
    
        $gst_amount = sprintf('%0.2f', $net_price * $gst_per / 100);
        $amount = sprintf('%0.2f', $net_price + $gst_amount);
    
    } else {
        $gst_amount = '0.00';
        $amount = sprintf('%0.2f', (float) $net_price);
    }

    $taxable_amount = $amount - $gst_amount;

    
    if($under_gst == 'Local'){
        $gstdevide = getGSTValueBygstType($gst_per);
        $gstTitle = "CGST: ".$gstdevide."%, SGST: ".$gstdevide."%";
     } else {
        $gstTitle = "IGST: ".$gst_per."%";
     }

    if ($count > 0) {
        $html .= '<tr id="row_' . $barcode . '">';
        $html .= '<td><input type="hidden" id="' . $row['id'] . '" name="item_id[]" value="" /><div class="checkbox checkbox-info" style="display:flex;align-items:center;gap:6px;"><input id="chk_' . ((int)$trcount + 1) . '" type="checkbox" name="chk[]" class="chk-box" value=""/><label class="s_no" for="chk_' . ((int)$trcount + 1) . '"></label><button type="button" class="row-delete-btn" onclick="deleteTableRow(this)" title="Delete Row"><i class="fa fa-trash"></i></button></div></td>';
        $html .= '<td>' . ((int)$trcount + 1) . '</td>';
        $html .= '<td><input type="text" class="form-control" value="' . ($row['barcode_no'] ?? '') . '" name="barcode_no[]" required readonly></td>';
        $html .= '<td><input type="text" class="form-control" value="' . ($row['sku_no'] ?? '') . '" name="article_no[]" required readonly></td>';
        $html .= '<td><input type="text" class="form-control" value="' . ($row['item_name'] ?? '') . '" name="item_name[]" required readonly><input type="hidden" class="form-control" value="' . ($row['item_type'] ?? $row['iten_type'] ?? '') . '" name="item_type[]" required readonly></td>';
        $html .= '<td class="group-td"><div style="display:flex;gap:6px;align-items:center;"><input type="text" class="form-control group-category-display" value="' . ($row['group_name'] ?? $row['group_name'] ?? '') . '" name="category[]" readonly><button type="button" class="plus-btn" onclick="openPopup(this)" title="Add Group Details"><i class="fa fa-plus"></i></button></div><input type="hidden" name="sub_category[]" value=""><input type="hidden" name="brand[]" value=""><input type="hidden" name="color[]" value=""><input type="hidden" name="style[]" value=""></td>';
        $html .= '<td><input type="text" value="' . ($row['size_name'] ?? $row['size'] ?? '') . '" class="form-control" name="size[]" required readonly></td>';
        $html .= '<td><input type="number" value="0" class="form-control" name="bbr_qty[]" required min="0"></td>';
        $html .= '<td><input type="number" value="1" class="form-control qtyval qty_array" name="qty[]" oninput="updateBillType()" required></td>';
        $html .= '<td><input type="text" value="' . ($row['mou_name'] ?? '') . '" class="form-control" name="mou_name[]" required readonly></td>';
        $html .= '<td><div style="display:flex;gap:6px;align-items:center;"><input type="text" price="' . ($row['purchase_price'] ?? '') . '" value="' . ($row['purchase_price'] ?? '') . '" class="form-control pprice" id="purchase_' . $row['id'] . '" name="purchase_price[]" oninput="updateBillType()" required><button type="button" class="plus-btn" onclick="openPopup1(this)" title="Add PP Discount"><i class="fa fa-plus"></i></button></div><input type="hidden" class="after_pp" name="after_pp[]" value="' . ($row['purchase_price'] ?? '0') . '"><input type="hidden" class="percent_discount" name="percent_discount[]" value="0"><input type="hidden" class="discount_amt" name="discount_amt[]" value="0"></td>';
        $html .= '<td><div style="display:flex;gap:6px;align-items:center;"><input type="number" class="form-control mrp" value="' . ($row['mrp'] ?? '') . '" id="mrp_' . $row['id'] . '" name="mrp[]" readonly><button type="button" class="plus-btn" onclick="openPopupmrp(this)" title="Add MRP Discount"><i class="fa fa-plus"></i></button></div><input type="hidden" class="discount_percent2" name="discount_percent2[]" value="0"><input type="hidden" class="discount_amount2" name="discount_amount2[]" value="0"></td>';
        $html .= '<td><input type="text" value="' . ($row['sp'] ?? '') . '" class="form-control selling_price" id="selling_price_' . $row['id'] . '" name="selling_price[]" readonly></td>';
        $html .= '<td><input type="text" value="' . $taxable_amount . '" class="form-control taxable_amount" id="taxable_amount' . $row['id'] . '" name="taxable_amount[]" required readonly><input type="hidden" value="' . $net_price . '" class="form-control net_array" id="net_price_' . $row['id'] . '" name="net_price[]" required readonly></td>';
        $html .= '<td><input type="text" class="form-control gst_array" id="gst_' . $row['id'] . '" value="' . ($gst_per ?? $row['gst'] ?? '') . '" name="gst[]" readonly  data-toggle="tooltip" title="' . $gstTitle . '"></td>';
        $html .= '<td><input type="text" value="' . $gst_amount . '" class="form-control gstval" id="gst_amount_' . $row['id'] . '" name="gst_amount[]" required readonly></td>';
        $html .= '<td><input type="text" value="' . $amount . '" class="form-control amount_array" id="amount' . $row['id'] . '" name="amount[]" required readonly></td>';
        $html .= '</tr>';


        echo json_encode(array('status' => true, 'html' => $html, 'id' => $row['id'], 'barcode_no' => $barcode));
    } else {
        echo json_encode(array('status' => false, 'message' => ''));
    }
} else if ($function == 'get_purchage_info') {

    $db = connect();

    $html = "";
    $gst_type = $validationHelper->filterText($_POST['gst_type']);
    $under_gst = $validationHelper->filterText($_POST['under_gst']);
    $trcount = $validationHelper->filterText($_POST['trcount']);
    $barcode = $validationHelper->filterText($_POST['barcode']);

    $query = $db->select(
        "SELECT p.*, 
                COALESCE((SELECT SUM(qty) FROM tbl_purchase WHERE barcode_number = ? AND status = ?), 0)
                - COALESCE((SELECT SUM(qty) FROM tbl_bill WHERE barcode_number = ?), 0) AS total_qty
        FROM tbl_purchase p 
        WHERE p.barcode_number = ? AND p.status = ?",
        'sssss',
        $barcode, 'success',
        $barcode,
        $barcode, 'success'
    );

    $row = $query->fetch_assoc();

    if ($row && $row['id'] > 0) {
        $net_price = $row['purchase_price'];

        if ($gst_type == 'Inclusive') {
            $gst_amount_temp = ($net_price * $row['gst']) / (100 + $row['gst']);
            $gst_amount = sprintf('%0.2f', $gst_amount_temp);
            $amount = sprintf('%0.2f', $net_price);
        } else if ($gst_type == 'Exclusive') {
            $gst_amount = sprintf('%0.2f', $net_price * $row['gst'] / 100);
            $amount = sprintf('%0.2f', $net_price + $gst_amount);
        } else {
            $gst_amount = sprintf('%0.2f', 0);
            $amount = sprintf('%0.2f', $net_price);
        }

        $taxable_amount = sprintf('%0.2f', $amount - $gst_amount);

        // Clean up empty/null string values for cleaner data passing
        $group_val    = ($row['group_name'] ?? '') === 'null' ? '' : ($row['group_name'] ?? '');
        $subgroup_val = ($row['subgroup'] ?? '') === 'null' ? '' : ($row['subgroup'] ?? '');
        $brand_val    = ($row['brand'] ?? '') === 'null' ? '' : ($row['brand'] ?? '');
        $color_val    = ($row['color'] ?? '') === 'null' ? '' : ($row['color'] ?? '');
        $size_val     = ($row['size'] ?? '') === 'null' ? '' : ($row['size'] ?? '');
        $style_val    = ($row['style'] ?? '') === 'null' ? '' : ($row['style'] ?? '');

        $trIndex = (int)$trcount + 1;

        $html .= '<tr id="row_' . $row['id'] . '"  data-barcode="'. htmlspecialchars($row['barcode_number']) .'">';
        $html .= '<td><input type="hidden" name="item_id[]" value="" /><div class="checkbox checkbox-info"><input id="chk_' . $trIndex . '" type="checkbox" name="chk[]" class="chk-box" value=""/> <button type="button" class="row-delete-btn" onclick="deleteTableRow(this)" title="Delete Row"><i class="fa fa-trash"></i></button></div></td>';
        $html .= '<td><label class="s_no" for="chk_' . $trIndex . '">' . $trIndex . '</label></td>';
        $html .= '<td><input type="text" class="form-control" value="' . $row['barcode_number'] . '" name="barcode_no[]" required readonly></td>';
        $html .= '<td><input type="text" class="form-control" value="' . ($row['article_no'] ?? '') . '" name="article_no[]" required readonly></td>';
        
        // Add hidden inputs to store the selected modal options per row
        $html .= '<td>';
        $html .= '<div style="display:flex;gap:6px;align-items:center;">';
        $html .= '  <input type="text" class="form-control" value="' . $row['item_name'] . '" name="item_name[]" required readonly>';
        $html .= '  <input type="hidden" value="' . $row['item_type'] . '" name="item_type[]">';
        $html .= '  <input type="hidden" class="row-group" name="group_name[]" value="' . htmlspecialchars($group_val) . '">';
        $html .= '  <input type="hidden" class="row-subgroup" name="subgroup[]" value="' . htmlspecialchars($subgroup_val) . '">';
        $html .= '  <input type="hidden" class="row-brand" name="brand[]" value="' . htmlspecialchars($brand_val) . '">';
        $html .= '  <input type="hidden" class="row-color" name="color[]" value="' . htmlspecialchars($color_val) . '">';
        $html .= '  <input type="hidden" class="row-size" name="size[]" value="' . htmlspecialchars($size_val) . '">';
        $html .= '  <input type="hidden" class="row-style" name="style[]" value="' . htmlspecialchars($style_val) . '">';
        
        // Attach current row data to button via data attributes
        $html .= '  <button type="button" class="plus-btn" ';
        $html .= '          data-id="' . $row['id'] . '" ';
        $html .= '          data-group="' . htmlspecialchars($group_val) . '" ';
        $html .= '          data-subgroup="' . htmlspecialchars($subgroup_val) . '" ';
        $html .= '          data-brand="' . htmlspecialchars($brand_val) . '" ';
        $html .= '          data-color="' . htmlspecialchars($color_val) . '" ';
        $html .= '          data-size="' . htmlspecialchars($size_val) . '" ';
        $html .= '          data-style="' . htmlspecialchars($style_val) . '" ';
        $html .= '          onclick="openCategoryPopup(this)" title="Add Category Details">';
        $html .= '      <i class="fa fa-plus"></i>';
        $html .= '  </button>';
        $html .= '</div>';
        $html .= '</td>';

        $html .= '<td><input type="number" value="0" class="form-control" name="bbr_qty[]" required readonly></td>';
        $html .= '<td><input type="number" class="form-control stock_qty" value="' . $row['total_qty'] . '" id="stock_id_' . $row['id'] . '" name="stock_qty[]" required readonly></td>';
        $html .= '<td>
        <input type="number" min="1" value="1" class="form-control qtyval qty_array" name="qty[]" oninput="validateQty(this); updateBillType()" required>
        <input type="hidden" class="form-control bal_qty" value="' . ($row['total_qty'] - 1) . '" id="balance_id_' . $row['id'] . '" name="balance_qty[]" required readonly>
        </td>';
        $html .= '<td><input type="text" value="' . ($row['mou_name'] ?? '') . '" class="form-control" name="mou_name[]" required readonly></td>';
        $html .= '<td><div style="display:flex;gap:6px;align-items:center;">';
        $html .= '<input type="hidden" price="' . ($row['sp'] ?? $net_price) . '" value="' . ($row['sp'] ?? $net_price) . '" class="form-control pprice valid" name="purchase_price[]">';
        $html .= '<input type="hidden" class="mrp" name="mrp[]" value="' . ($row['sp'] ?? $net_price) . '">';
        $html .= '<input type="hidden" class="discount_percent2" name="discount_percent2[]" value="0">';
        $html .= '<input type="hidden" class="discount_amount2" name="discount_amount2[]" value="0">';
        $html .= '<input type="text" value="' . ($row['sp'] ?? '') . '" class="form-control selling_price" id="selling_price_' . $row['id'] . '" name="selling_price[]" readonly>';
        $html .= '<button type="button" class="plus-btn" onclick="openPopupsp(this)" title="Edit MRP"><i class="fa fa-plus"></i></button>';
        $html .= '</div></td>';
        $html .= '<td><input type="text" value="' . $net_price . '" class="form-control net_array" id="net_price_' . $row['id'] . '" name="net_price[]" required readonly></td>';
        $html .= '<td><input type="hidden" class="form-control gst_array" id="gst_' . $row['id'] . '" value="' . ($row['gst'] ?? '') . '" name="gst[]" readonly data-toggle="tooltip">
        <input type="text" value="' . $gst_amount . '" class="form-control gstval" id="gst_amount_' . $row['id'] . '" name="gst_amount[]" required readonly></td>';
        $html .= '<td><input type="text" value="' . $taxable_amount . '" class="form-control taxable_amount" id="taxable_amount' . $row['id'] . '" name="taxable_amount[]" required readonly></td>';
        $html .= '<td><input type="text" value="' . $amount . '" class="form-control amount_array" id="amount' . $row['id'] . '" name="amount[]" required readonly></td>';
        $html .= '</tr>';

        echo json_encode(array('status' => true, 'html' => $html, 'id' => $row['id'], 'barcode_no' => $barcode));
    } else {
        echo json_encode(array('status' => false, 'message' => 'No item found.'));
    }
} else if ($function == "deletePurchase") {

    $db = connect();

    $id = implode(',', $_POST['id']);
    $res = $db->delete("DELETE FROM tbl_purchase WHERE id IN ($id)");
    if ($res) {
        echo json_encode(array('status' => true));
    } else {
        echo json_encode(array('status' => false));
    }

    $db->close();
} else if ($function == "deleteSale") {

    $db = connect();

    $id = implode(',', $_POST['id']);
    $res = $db->delete("DELETE FROM tbl_bill WHERE id IN ($id)");
    if ($res) {
        echo json_encode(array('status' => true));
    } else {
        echo json_encode(array('status' => false));
    }

    $db->close();

} else if ($function == 'get_item_info_by_sku') {

    $db = connect();
    $sku      = $validationHelper->filterText($_POST['sku']);
    $gst_type = $validationHelper->filterText($_POST['gst_type']);
    $under_gst = $validationHelper->filterText($_POST['under_gst']);
    $trcount  = $validationHelper->filterText($_POST['trcount']);

    $query = $db->select("SELECT * FROM tbl_item_master WHERE sku_no =? AND status=?", 'ss', $sku, 'true');
    $row   = $query->fetch_assoc();

    if (!$row || !$row['id']) {
        echo json_encode(['status' => false, 'message' => 'Item not found']);
        die;
    }

    // Reuse barcode to call get_item_info logic inline
    $_POST['barcode'] = $row['barcode_no'];
    $_POST['act']     = 'get_item_info';

    $net_price = $row['purchase_price'];
    if ($gst_type == 'Inclusive') {
        $gst_amount_temp = ($net_price * $row['gst_per']) / (100 + $row['gst_per']);
        $gst_amount = sprintf('%0.2f', $gst_amount_temp);
        $amount_temp = $net_price;
        $amount = sprintf('%0.2f', $amount_temp);
    } else if ($gst_type == 'Exclusive') {
        $gst_amount = $net_price * $row['gst_per'] / 100;
        $amount = $net_price + $gst_amount;
    } else {
        $gst_amount = 0;
        $amount = $net_price;
    }
    $taxable_amount = $amount - $gst_amount;

    if ($under_gst == 'Local') {
        $gstdevide = getGSTValueBygstType($row['gst_per']);
        $gstTitle = "CGST: " . $gstdevide . "%, SGST: " . $gstdevide . "%";
    } else {
        $gstTitle = "IGST: " . $row['gst_per'] . "%";
    }

    $barcode = $row['barcode_no'];
    $html  = '<tr id="row_' . $barcode . '">';
    $html .= '<td><input type="hidden" id="' . $row['id'] . '" name="item_id[]" value="" /><div class="checkbox checkbox-info" style="display:flex;align-items:center;gap:6px;"><input id="chk_' . ((int)$trcount + 1) . '" type="checkbox" name="chk[]" class="chk-box" value=""/><label class="s_no" for="chk_' . ((int)$trcount + 1) . '"></label><button type="button" class="row-delete-btn" onclick="deleteTableRow(this)" title="Delete Row"><i class="fa fa-trash"></i></button></div></td>';
    $html .= '<td>' . ((int)$trcount + 1) . '</td>';
    $html .= '<td><input type="text" class="form-control" value="' . ($row['barcode_no'] ?? '') . '" name="barcode_no[]" required readonly></td>';
    $html .= '<td><input type="text" class="form-control" value="' . ($row['sku_no'] ?? '') . '" name="article_no[]" required readonly></td>';
    $html .= '<td><input type="text" class="form-control" value="' . ($row['item_name'] ?? '') . '" name="item_name[]" required readonly><input type="hidden" class="form-control" value="' . ($row['item_type'] ?? '') . '" name="item_type[]" required readonly></td>';
    $html .= '<td class="group-td"><div style="display:flex;gap:6px;align-items:center;"><input type="text" class="form-control group-category-display" value="' . ($row['group_name'] ?? '') . '" name="category[]" readonly><button type="button" class="plus-btn" onclick="openPopup(this)" title="Add Group Details"><i class="fa fa-plus"></i></button></div><input type="hidden" name="sub_category[]" value=""><input type="hidden" name="brand[]" value=""><input type="hidden" name="color[]" value=""><input type="hidden" name="style[]" value=""></td>';
    $html .= '<td><input type="text" value="' . ($row['size_name'] ?? '') . '" class="form-control" name="size[]" required readonly></td>';
    $html .= '<td><input type="number" value="0" class="form-control" name="bbr_qty[]" required min="0"></td>';
    $html .= '<td><input type="number" value="1" class="form-control qtyval qty_array" name="qty[]" oninput="updateBillType()" required></td>';
    $html .= '<td><input type="text" value="' . ($row['mou_name'] ?? '') . '" class="form-control" name="mou_name[]" required readonly></td>';
    $html .= '<td><div style="display:flex;gap:6px;align-items:center;"><input type="text" price="' . ($row['purchase_price'] ?? '') . '" value="' . ($row['purchase_price'] ?? '') . '" class="form-control pprice" id="purchase_' . $row['id'] . '" name="purchase_price[]" oninput="updateBillType()" required><button type="button" class="plus-btn" onclick="openPopup1(this)" title="Add PP Discount"><i class="fa fa-plus"></i></button></div><input type="hidden" class="after_pp" name="after_pp[]" value="' . ($row['purchase_price'] ?? '0') . '"><input type="hidden" class="percent_discount" name="percent_discount[]" value="0"><input type="hidden" class="discount_amt" name="discount_amt[]" value="0"></td>';
    $html .= '<td><div style="display:flex;gap:6px;align-items:center;"><input type="number" class="form-control mrp" value="' . ($row['mrp'] ?? '') . '" id="mrp_' . $row['id'] . '" name="mrp[]" readonly><button type="button" class="plus-btn" onclick="openPopupmrp(this)" title="Add MRP Discount"><i class="fa fa-plus"></i></button></div><input type="hidden" class="discount_percent2" name="discount_percent2[]" value="0"><input type="hidden" class="discount_amount2" name="discount_amount2[]" value="0"></td>';
    $html .= '<td><input type="text" value="' . ($row['rate'] ?? '') . '" class="form-control selling_price" id="selling_price_' . $row['id'] . '" name="selling_price[]" readonly></td>';
    $html .= '<td><input type="text" value="' . $net_price . '" class="form-control net_array" id="net_price_' . $row['id'] . '" name="net_price[]" required readonly></td>';
    $html .= '<td><input type="text" class="form-control gst_array" id="gst_' . $row['id'] . '" value="' . ($row['gst_per'] ?? '') . '" name="gst[]" readonly data-toggle="tooltip" title="' . $gstTitle . '"></td>';
    $html .= '<td><input type="text" value="' . $gst_amount . '" class="form-control gstval" id="gst_amount_' . $row['id'] . '" name="gst_amount[]" required readonly></td>';
    $html .= '<td><input type="text" value="' . $taxable_amount . '" class="form-control taxable_amount" id="taxable_amount' . $row['id'] . '" name="taxable_amount[]" required readonly></td>';
    $html .= '<td><input type="text" value="' . $amount . '" class="form-control amount_array" id="amount' . $row['id'] . '" name="amount[]" required readonly></td>';
    $html .= '</tr>';

    echo json_encode(['status' => true, 'html' => $html, 'id' => $row['id'], 'barcode_no' => $barcode]);

} else if ($function == 'get_purchase_item_details') {
    $db = connect();
    $id = (int)($_POST['id'] ?? 0);
    $barcode = trim($_POST['barcode'] ?? '');
    
    $row = null;
    if ($id > 0) {
        $stmt = $db->select("SELECT * FROM tbl_purchase WHERE id = ?", 'i', array($id));
        if ($stmt && $stmt->num_rows() > 0) { $row = $stmt->fetch_assoc(); }
    } else if ($barcode !== '') {
        $stmt = $db->select("SELECT * FROM tbl_purchase WHERE barcode_number = ? LIMIT 1", 's', array($barcode));
        if ($stmt && $stmt->num_rows() > 0) { $row = $stmt->fetch_assoc(); }
    }
    
    if ($row) {
        echo json_encode(['status' => true, 'data' => $row]);
    } else {
        echo json_encode(['status' => false, 'message' => 'Item not found']);
    }
    exit;
} else if ($function == 'process_stock_transfer') {
    $db = connect();
    $purchase_id = (int)($_POST['purchase_id'] ?? 0);
    $stock_qty = (int)($_POST['stock_qty'] ?? 0);
    $store_id = (int)($_POST['store_id'] ?? 0);
    $barcode_number = trim($_POST['barcode_number'] ?? '');
    $item_name = trim($_POST['item_name'] ?? '');
    
    if ($stock_qty <= 0) {
        echo json_encode(['status' => false, 'message' => 'Please enter a valid stock quantity']);
        exit;
    }
    if ($store_id <= 0) {
        echo json_encode(['status' => false, 'message' => 'Please select a target store']);
        exit;
    }
    
    // Fetch store details
    $store_name = '';
    $s_stmt = $db->select("SELECT store_name FROM tbl_store_master WHERE id = ? LIMIT 1", 'i', array($store_id));
    if ($s_stmt && $s_row = $s_stmt->fetch_assoc()) {
        $store_name = $s_row['store_name'];
    }
    
    // Fetch purchase record
    $purch_row = null;
    if ($purchase_id > 0) {
        $p_stmt = $db->select("SELECT * FROM tbl_purchase WHERE id = ? LIMIT 1", 'i', array($purchase_id));
        if ($p_stmt && $p_stmt->num_rows() > 0) { $purch_row = $p_stmt->fetch_assoc(); }
    }
    if (!$purch_row && $barcode_number !== '') {
        $p_stmt = $db->select("SELECT * FROM tbl_purchase WHERE barcode_number = ? LIMIT 1", 's', array($barcode_number));
        if ($p_stmt && $p_stmt->num_rows() > 0) { $purch_row = $p_stmt->fetch_assoc(); }
    }
    if (!$purch_row && $item_name !== '') {
        $p_stmt = $db->select("SELECT * FROM tbl_purchase WHERE item_name = ? LIMIT 1", 's', array($item_name));
        if ($p_stmt && $p_stmt->num_rows() > 0) { $purch_row = $p_stmt->fetch_assoc(); }
    }
    
    if (!$purch_row) {
        echo json_encode(['status' => false, 'message' => 'Purchase record not found']);
        exit;
    }

    $current_qty = (int)($purch_row['qty'] ?? 0);
    if ($current_qty <= 0) {
        echo json_encode(['status' => false, 'message' => 'This purchase item has 0 available quantity']);
        exit;
    }
    if ($stock_qty > $current_qty) {
        echo json_encode(['status' => false, 'message' => "Transfer quantity ($stock_qty) cannot exceed available purchase quantity ($current_qty)"]);
        exit;
    }
    
    $bill_number = $purch_row['bill_number'] ?? '';
    $invoice_number = $purch_row['invoice_number'] ?? '';
    $account_name = $purch_row['account_name'] ?? '';
    $bc = $barcode_number !== '' ? $barcode_number : ($purch_row['barcode_number'] ?? '');
    $iname = $item_name !== '' ? $item_name : ($purch_row['item_name'] ?? '');
    $item_type = $purch_row['item_type'] ?? '';
    $category = $purch_row['category'] ?? '';
    $brand = $purch_row['brand'] ?? '';
    $size = $purch_row['size'] ?? '';
    $color = $purch_row['color'] ?? '';
    $style = $purch_row['style'] ?? '';
    $mou_name = $purch_row['mou_name'] ?? '';
    $rate = $purch_row['rate'] ?? '';
    $purchase_price = $purch_row['purchase_price'] ?? '';
    $mrp = $purch_row['mrp'] ?? '';
    $selling_price = $purch_row['selling_price'] ?? '';
    $gst = $purch_row['gst'] ?? '';
    $gst_amount = $purch_row['gst_amount'] ?? '';
    $taxable_amount = $purch_row['taxable_amount'] ?? '';
    $amount = $purch_row['amount'] ?? '';
    
    $now = date('Y-m-d H:i:s');
    
    $query = "INSERT INTO tbl_store_items (
        purchase_id, store_id, store_name, bill_number, invoice_number, account_name,
        barcode_number, item_name, item_type, category, brand, size, color, style,
        qty, mou_name, rate, purchase_price, mrp, selling_price, gst, gst_amount,
        taxable_amount, amount, status, created_date, updated_date
    ) VALUES (
        ?, ?, ?, ?, ?, ?,
        ?, ?, ?, ?, ?, ?, ?, ?,
        ?, ?, ?, ?, ?, ?, ?, ?,
        ?, ?, 'active', ?, ?
    )";
    
    $params = 'iisssssssssssissssssssssss';
    $fields = array(
        $purchase_id, $store_id, $store_name, $bill_number, $invoice_number, $account_name,
        $bc, $iname, $item_type, $category, $brand, $size, $color, $style,
        $stock_qty, $mou_name, $rate, $purchase_price, $mrp, $selling_price, $gst, $gst_amount,
        $taxable_amount, $amount, $now, $now
    );
    
    $res = $db->insert($query, $params, $fields);
    if ($res) {
        // Deduct stock quantity from tbl_purchase
        $new_qty = $current_qty - $stock_qty;
        $pid = (int)($purch_row['id'] ?? 0);

        if ($pid > 0) {
            $pp = (float)($purch_row['purchase_price'] ?? 0);
            $gst_per = (float)($purch_row['gst'] ?? 0);

            if ($pp > 0) {
                $new_net = $pp * $new_qty;
                if (($purch_row['gst_type'] ?? '') == 'Inclusive' && $gst_per > 0) {
                    $new_gst_amount = round(($new_net * $gst_per) / (100 + $gst_per), 2);
                    $new_amount = round($new_net, 2);
                    $new_taxable = round($new_amount - $new_gst_amount, 2);
                } else if (($purch_row['gst_type'] ?? '') == 'Exclusive' && $gst_per > 0) {
                    $new_gst_amount = round(($new_net * $gst_per) / 100, 2);
                    $new_taxable = round($new_net, 2);
                    $new_amount = round($new_taxable + $new_gst_amount, 2);
                } else {
                    $new_gst_amount = 0;
                    $new_taxable = round($new_net, 2);
                    $new_amount = round($new_net, 2);
                }

                $db->query("UPDATE tbl_purchase SET qty = '$new_qty', gst_amount = '$new_gst_amount', taxable_amount = '$new_taxable', amount = '$new_amount', updated_date = '$now' WHERE id = $pid");
            } else {
                $db->query("UPDATE tbl_purchase SET qty = '$new_qty', updated_date = '$now' WHERE id = $pid");
            }
        }

        echo json_encode(['status' => true, 'message' => "Stock ($stock_qty qty) transferred successfully to $store_name! Remaining purchase qty: $new_qty"]);
    } else {
        echo json_encode(['status' => false, 'message' => 'Failed to process stock transfer']);
    }
    exit;
} else if ($function == 'process_return_store_item') {
    $db = connect();
    $id = (int)($_REQUEST['id'] ?? $_POST['id'] ?? $_GET['id'] ?? 0);
    
    if ($id <= 0) {
        echo json_encode(['status' => false, 'message' => 'Invalid store item ID']);
        exit;
    }
    
    $stmt = $db->select("SELECT * FROM tbl_store_items WHERE id = ? LIMIT 1", 'i', array($id));
    if (!$stmt || $stmt->num_rows() == 0) {
        echo json_encode(['status' => false, 'message' => 'Store item record not found']);
        exit;
    }
    
    $store_item = $stmt->fetch_assoc();
    if (($store_item['status'] ?? '') == 'moved in stock') {
        echo json_encode(['status' => false, 'message' => 'This item has already been returned/moved in stock']);
        exit;
    }
    
    $now = date('Y-m-d H:i:s');
    $purchase_id = (int)($store_item['purchase_id'] ?? 0);
    $return_qty = (int)($store_item['qty'] ?? 0);
    $store_name = $store_item['store_name'] ?? '';
    
    // Check if original purchase record exists in tbl_purchase
    $purch_row = null;
    if ($purchase_id > 0) {
        $p_stmt = $db->select("SELECT * FROM tbl_purchase WHERE id = ? LIMIT 1", 'i', array($purchase_id));
        if ($p_stmt && $p_stmt->num_rows() > 0) {
            $purch_row = $p_stmt->fetch_assoc();
        }
    }
    
    if ($purch_row) {
        // Increase quantity in existing tbl_purchase record
        $orig_qty = (int)($purch_row['qty'] ?? 0);
        $new_qty = $orig_qty + $return_qty;
        $pid = (int)$purch_row['id'];
        
        $pp = (float)($purch_row['purchase_price'] ?? 0);
        $gst_per = (float)($purch_row['gst'] ?? 0);
        
        if ($pp > 0) {
            $new_net = $pp * $new_qty;
            if (($purch_row['gst_type'] ?? '') == 'Inclusive' && $gst_per > 0) {
                $new_gst_amount = round(($new_net * $gst_per) / (100 + $gst_per), 2);
                $new_amount = round($new_net, 2);
                $new_taxable = round($new_amount - $new_gst_amount, 2);
            } else if (($purch_row['gst_type'] ?? '') == 'Exclusive' && $gst_per > 0) {
                $new_gst_amount = round(($new_net * $gst_per) / 100, 2);
                $new_taxable = round($new_net, 2);
                $new_amount = round($new_taxable + $new_gst_amount, 2);
            } else {
                $new_gst_amount = 0;
                $new_taxable = round($new_net, 2);
                $new_amount = round($new_net, 2);
            }
            
            $db->query("UPDATE tbl_purchase SET qty = '$new_qty', gst_amount = '$new_gst_amount', taxable_amount = '$new_taxable', amount = '$new_amount', transfer_by = '$store_name', updated_date = '$now' WHERE id = $pid");
        } else {
            $db->query("UPDATE tbl_purchase SET qty = '$new_qty', transfer_by = '$store_name', updated_date = '$now' WHERE id = $pid");
        }
    } else {
        // Insert a new purchase record in tbl_purchase copied from tbl_store_items
        $query = "INSERT INTO tbl_purchase (
            purchage_date, bill_number, invoice_number, store_name, transfer_by, account_name,
            item_name, barcode_number, item_type, category, brand, size, color, style,
            qty, mou_name, rate, purchase_price, mrp, sp, selling_price, gst, gst_amount,
            taxable_amount, amount, total_amount2, status, created_date, updated_date
        ) VALUES (
            ?, ?, ?, ?, ?, ?,
            ?, ?, ?, ?, ?, ?, ?, ?,
            ?, ?, ?, ?, ?, ?, ?, ?, ?,
            ?, ?, ?, 'active', ?, ?
        )";
        
        $params = 'ssssssssssssssisssssssssssss';
        $pdate = date('Y-m-d');
        $sp_val = $store_item['selling_price'] ?? ($store_item['rate'] ?? '0');
        $amt_val = $store_item['amount'] ?? '0';
        $fields = array(
            $pdate, $store_item['bill_number'] ?? '', $store_item['invoice_number'] ?? '', $store_name, $store_name, $store_item['account_name'] ?? '',
            $store_item['item_name'] ?? '', $store_item['barcode_number'] ?? '', $store_item['item_type'] ?? '', $store_item['category'] ?? '', $store_item['brand'] ?? '', $store_item['size'] ?? '', $store_item['color'] ?? '', $store_item['style'] ?? '',
            $return_qty, $store_item['mou_name'] ?? '', $store_item['rate'] ?? '', $store_item['purchase_price'] ?? '', $store_item['mrp'] ?? '', $sp_val, $store_item['selling_price'] ?? '', $store_item['gst'] ?? '', $store_item['gst_amount'] ?? '',
            $store_item['taxable_amount'] ?? '', $amt_val, $amt_val, $now, $now
        );
        $db->insert($query, $params, $fields);
    }
    
    // Mark status as 'moved in stock' in tbl_store_items
    $db->query("UPDATE tbl_store_items SET status = 'moved in stock', updated_date = '$now' WHERE id = $id");
    
    echo json_encode(['status' => true, 'message' => 'Item returned successfully! Stock updated in purchase master and status marked as moved in stock.']);
    exit;
}
