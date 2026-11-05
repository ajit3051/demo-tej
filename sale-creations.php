<?php include_once("include/config.php"); ?>
<?php require_once(_BASEPATH . 'include/generatepdf.php'); ?>
<?php
   $validationHelper = new validation();

$db = connect();

$bill_no = $_GET['bill_no'] ?? '';

if (isset($_POST['submit'])) {
   ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

   $purchage_date          = dateInSQLFormat($_POST['sidebar_purchage_date'] ?? ($_POST['purchage_date'] ?? date('Y-m-d')));
   $bill_number            = $_POST['sidebar_bill_number'] ?? ($_POST['bill_number'] ?? '');
   $gst_type               = $_POST['sidebar_gst_type'] ?? ($_POST['gst_type'] ?? 'Inclusive');
   $under_gst               = $_POST['sidebar_under_gst'] ?? ($_POST['under_gst'] ?? 'Local');
   $bill_type               = $_POST['sidebar_bill_type'] ?? ($_POST['bill_type'] ?? 'Tax-Bill');
   
   if ($bill_type == 'Tax Invoice') $bill_type = 'Tax-Bill';
   if ($bill_type == 'Cash Memo')   $bill_type = 'Cash-Memo';

   $mobile_number           = !empty($_POST['popup_mobile_number']) ? $_POST['popup_mobile_number'] : ($_POST['mobile_number'] ?? '');
   $account_name            = !empty($_POST['popup_account_name']) ? $_POST['popup_account_name'] : ($_POST['account_name'] ?? '');

   // Item data
   $item_name               = $_POST['item_name'] ?? [];
   $barcode_no              = $_POST['barcode_no'] ?? [];
   $article_no              = $_POST['article_no'] ?? [];
   $item_type               = $_POST['item_type'] ?? [];
   $category                = $_POST['group_name'] ?? [];
   $brand                   = $_POST['brand'] ?? [];
   $color                   = $_POST['color'] ?? [];
   $size                    = $_POST['size'] ?? [];
   $stock_qty               = $_POST['stock_qty'] ?? [];
   $qty                     = $_POST['qty'] ?? [];
   $balance_qty             = $_POST['balance_qty'] ?? [];
   $mou_name                = $_POST['mou_name'] ?? [];
   $rate                    = $_POST['mrp'] ?? [];
   $purchase_price          = $_POST['purchase_price'] ?? [];
   $percent_discount        = $_POST['discount_percent2'] ?? [];
   $discount_amt            = $_POST['discount_amount2'] ?? [];
   $net_price               = $_POST['net_price'] ?? [];
   $gst                     = $_POST['gst'] ?? [];
   $gst_amount              = $_POST['gst_amount'] ?? [];
   $taxable_amount          = $_POST['taxable_amount'] ?? [];
   $amount                  = $_POST['amount'] ?? [];
   $packing_amount          = (isset($_POST['sidebar_packing_amount']) && $_POST['sidebar_packing_amount'] !== '') ? $_POST['sidebar_packing_amount'] : ((isset($_POST['packing_amount']) && $_POST['packing_amount'] !== '') ? $_POST['packing_amount'] : '0.00');
   $delivery_amount         = (isset($_POST['sidebar_delivery_amount']) && $_POST['sidebar_delivery_amount'] !== '') ? $_POST['sidebar_delivery_amount'] : ((isset($_POST['delivery_amount']) && $_POST['delivery_amount'] !== '') ? $_POST['delivery_amount'] : '0.00');
   $other_amount            = (isset($_POST['sidebar_other_amount']) && $_POST['sidebar_other_amount'] !== '') ? $_POST['sidebar_other_amount'] : ((isset($_POST['other_amount']) && $_POST['other_amount'] !== '') ? $_POST['other_amount'] : '0.00');
   $labour_amount           = (isset($_POST['sidebar_labour_amount']) && $_POST['sidebar_labour_amount'] !== '') ? $_POST['sidebar_labour_amount'] : ((isset($_POST['labour_amount']) && $_POST['labour_amount'] !== '') ? $_POST['labour_amount'] : '0.00');
   $percent_total_discount  = (isset($_POST['percent_total_discount']) && $_POST['percent_total_discount'] !== '') ? $_POST['percent_total_discount'] : '0.00';
   $discount_total_amount   = (isset($_POST['sidebar_discount_total_amount']) && $_POST['sidebar_discount_total_amount'] !== '') ? $_POST['sidebar_discount_total_amount'] : ((isset($_POST['discount_total_amount']) && $_POST['discount_total_amount'] !== '') ? $_POST['discount_total_amount'] : '0.00');
   $pay_mode                = $_POST['popup_pay_mode'] ?? ($_POST['pay_mode'] ?? '');
   $remarks                 = $_POST['sidebar_remarks'] ?? ($_POST['remarks'] ?? '');
   $status                  = $_POST['status'] ?? 'success';

   // Additional Data sidebar fields (store-specific details)
   $s_code                  = $_POST['sidebar_s_code'] ?? '';
   $store_name              = $_POST['sidebar_store_name'] ?? '';
   $location                = $_POST['sidebar_location'] ?? '';

   // Popup payment data
   $popup_ac_code           = $_POST['popup_ac_code'] ?? '';
   $popup_account_name      = $_POST['popup_account_name'] ?? '';
   $popup_mobile_number     = $_POST['popup_mobile_number'] ?? '';
   $popup_pay_mode          = $_POST['popup_pay_mode'] ?? $pay_mode;
   $popup_pay_cash          = (isset($_POST['popup_pay_cash']) && $_POST['popup_pay_cash'] !== '') ? $_POST['popup_pay_cash'] : '0.00';
   $popup_pay_upi           = (isset($_POST['popup_pay_upi']) && $_POST['popup_pay_upi'] !== '') ? $_POST['popup_pay_upi'] : '0.00';
   $popup_pay_card          = (isset($_POST['popup_pay_card']) && $_POST['popup_pay_card'] !== '') ? $_POST['popup_pay_card'] : '0.00';
   $popup_pay_credit        = (isset($_POST['popup_pay_credit']) && $_POST['popup_pay_credit'] !== '') ? $_POST['popup_pay_credit'] : '0.00';

   // item_id[] only ever contains a REAL tbl_bill.id for rows that already
   // existed in this bill when the page was loaded. Rows appended via AJAX
   // (new barcode scans) always submit item_id[] = '' — never trust any
   // other value here as a tbl_bill id.
   $item_id = $_POST['item_id'] ?? [];

   $current_date = date("Y-m-d H:i:s");

   $is_print = $_POST['is_print'] ?? '0';
   $print_option = $_POST['print_option'] ?? '80mm';

   if ($_POST['submit'] == 'save') {

      $allSuccess = true;
       foreach ($barcode_no as $k => $barcode) {

         $cur_stock_qty        = (isset($stock_qty[$k]) && $stock_qty[$k] !== '') ? $stock_qty[$k] : '0';
         $cur_qty              = (isset($qty[$k]) && $qty[$k] !== '') ? $qty[$k] : '0';
         $cur_balance_qty      = (isset($balance_qty[$k]) && $balance_qty[$k] !== '') ? $balance_qty[$k] : '0';
         $cur_rate             = (isset($rate[$k]) && $rate[$k] !== '') ? $rate[$k] : '0.00';
         $cur_purchase_price   = (isset($purchase_price[$k]) && $purchase_price[$k] !== '') ? $purchase_price[$k] : '0.00';
         $cur_percent_discount = (isset($percent_discount[$k]) && $percent_discount[$k] !== '') ? $percent_discount[$k] : '0.00';
         $cur_discount_amt     = (isset($discount_amt[$k]) && $discount_amt[$k] !== '') ? $discount_amt[$k] : '0.00';
         $cur_net_price        = (isset($net_price[$k]) && $net_price[$k] !== '') ? $net_price[$k] : '0.00';
         $cur_gst              = (isset($gst[$k]) && $gst[$k] !== '') ? $gst[$k] : '0.00';
         $cur_gst_amount       = (isset($gst_amount[$k]) && $gst_amount[$k] !== '') ? $gst_amount[$k] : '0.00';
         $cur_taxable_amount   = (isset($taxable_amount[$k]) && $taxable_amount[$k] !== '') ? $taxable_amount[$k] : '0.00';
         $cur_amount           = (isset($amount[$k]) && $amount[$k] !== '') ? $amount[$k] : '0.00';

         $cur_article          = (isset($article_no[$k]) && $article_no[$k] !== '') ? $article_no[$k] : '';

         //get GST value
         $othergstvalue = getGSTValueBygstType($cur_gst);
         $cgst = (string)($othergstvalue ?? '0.00');
         $sgst = (string)($othergstvalue ?? '0.00');

         $result = $db->insert(
            'INSERT INTO tbl_bill (purchage_date, bill_number, gst_type, under_gst, bill_type, item_name, barcode_number, article_no, item_type, category, brand, color, size, stock_qty, qty, balance_qty, mou_name, rate, purchase_price, percent_discount, discount_amt, net_price, gst, cgst, sgst, gst_amount, taxable_amount, amount, packing_amount, delivery_amount, other_amount, labour_amount, percent_total_discount, discount_total_amount, remarks, status, s_code, store_name, location, ac_code, account_name, mobile_number, pay_mode, pay_cash, pay_upi, pay_card, pay_credit, created_date, updated_date) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)',
            'sssssssssssssssssssssssssssssssssssssssssssssssss',
            $purchage_date, $bill_number, $gst_type, $under_gst, $bill_type, $item_name[$k], $barcode, $cur_article, $item_type[$k], $category[$k], $brand[$k], $color[$k], $size[$k], $cur_stock_qty, $cur_qty, $cur_balance_qty, $mou_name[$k], $cur_rate, $cur_purchase_price, $cur_percent_discount, $cur_discount_amt, $cur_net_price, $cur_gst, $cgst, $sgst, $cur_gst_amount, $cur_taxable_amount, $cur_amount, $packing_amount, $delivery_amount, $other_amount, $labour_amount, $percent_total_discount, $discount_total_amount, $remarks, $status, $s_code, $store_name, $location, $popup_ac_code, $popup_account_name, $popup_mobile_number, $popup_pay_mode, $popup_pay_cash, $popup_pay_upi, $popup_pay_card, $popup_pay_credit, $current_date, $current_date
         );

         if (!$result) {
            $allSuccess = false;
         }
      }

      if ($allSuccess) {
         $msg = 'Record added successfully';
         $return = '';
         if ($is_print == '1') {
            $_SESSION['print_bill_no'] = $bill_number;
            $_SESSION['print_option']  = $print_option;
         }
      } else {
         $msg = 'Failed!';
         $return = 1;
      }

      redirectTo('sale-creations.php', $msg, $return);

   } else if ($_POST['submit'] == 'update') {

      $allSuccess = true;

      foreach ($barcode_no as $k => $barcode) {

         $cur_stock_qty        = (isset($stock_qty[$k]) && $stock_qty[$k] !== '') ? $stock_qty[$k] : '0';
         $cur_qty              = (isset($qty[$k]) && $qty[$k] !== '') ? $qty[$k] : '0';
         $cur_balance_qty      = (isset($balance_qty[$k]) && $balance_qty[$k] !== '') ? $balance_qty[$k] : '0';
         $cur_rate             = (isset($rate[$k]) && $rate[$k] !== '') ? $rate[$k] : '0.00';
         $cur_purchase_price   = (isset($purchase_price[$k]) && $purchase_price[$k] !== '') ? $purchase_price[$k] : '0.00';
         $cur_percent_discount = (isset($percent_discount[$k]) && $percent_discount[$k] !== '') ? $percent_discount[$k] : '0.00';
         $cur_discount_amt     = (isset($discount_amt[$k]) && $discount_amt[$k] !== '') ? $discount_amt[$k] : '0.00';
         $cur_net_price        = (isset($net_price[$k]) && $net_price[$k] !== '') ? $net_price[$k] : '0.00';
         $cur_gst              = (isset($gst[$k]) && $gst[$k] !== '') ? $gst[$k] : '0.00';
         $cur_gst_amount       = (isset($gst_amount[$k]) && $gst_amount[$k] !== '') ? $gst_amount[$k] : '0.00';
         $cur_taxable_amount   = (isset($taxable_amount[$k]) && $taxable_amount[$k] !== '') ? $taxable_amount[$k] : '0.00';
         $cur_amount           = (isset($amount[$k]) && $amount[$k] !== '') ? $amount[$k] : '0.00';

         //get GST value
         $othergstvalue = getGSTValueBygstType($cur_gst);
         $cgst = (string)($othergstvalue ?? '0.00');
         $sgst = (string)($othergstvalue ?? '0.00');

         $cur_article          = (isset($article_no[$k]) && $article_no[$k] !== '') ? $article_no[$k] : '';

         // Only treat as an existing tbl_bill row if item_id[$k] is a real,
         // non-empty value. Newly-added rows always come through as ''.
         $isExist = (!empty($item_id[$k])) ? $item_id[$k] : null;

         if ($isExist) {
            $result = $db->update(
               "UPDATE tbl_bill SET purchage_date=?, gst_type=?, under_gst=?, bill_type=?, item_name=?, barcode_number=?, article_no=?, item_type=?, category=?, brand=?, color=?, size=?, stock_qty=?, qty=?, balance_qty=?, mou_name=?, rate=?, purchase_price=?, percent_discount=?, discount_amt=?, net_price=?, gst=?, cgst=?, sgst=?, gst_amount=?, taxable_amount=?, amount=?, packing_amount=?, delivery_amount=?, other_amount=?, labour_amount=?, percent_total_discount=?, discount_total_amount=?, remarks=?, status=?, s_code=?, store_name=?, location=?, ac_code=?, account_name=?, mobile_number=?, pay_mode=?, pay_cash=?, pay_upi=?, pay_card=?, pay_credit=?, updated_date=? WHERE id=?",
               'sssssssssssssssssssssssssssssssssssssssssssssssi',
               $purchage_date, $gst_type, $under_gst, $bill_type, $item_name[$k], $barcode, $cur_article, $item_type[$k], $category[$k], $brand[$k], $color[$k], $size[$k], $cur_stock_qty, $cur_qty, $cur_balance_qty, $mou_name[$k], $cur_rate, $cur_purchase_price, $cur_percent_discount, $cur_discount_amt, $cur_net_price, $cur_gst, $cgst, $sgst, $cur_gst_amount, $cur_taxable_amount, $cur_amount, $packing_amount, $delivery_amount, $other_amount, $labour_amount, $percent_total_discount, $discount_total_amount, $remarks, $status, $s_code, $store_name, $location, $popup_ac_code, $popup_account_name, $popup_mobile_number, $popup_pay_mode, $popup_pay_cash, $popup_pay_upi, $popup_pay_card, $popup_pay_credit, $current_date, $item_id[$k]
            );
         } else {
            $result = $db->insert(
               'INSERT INTO tbl_bill (purchage_date, bill_number, gst_type, under_gst, bill_type, item_name, barcode_number, article_no, item_type, category, brand, color, size, stock_qty, qty, balance_qty, mou_name, rate, purchase_price, percent_discount, discount_amt, net_price, gst, cgst, sgst, gst_amount, taxable_amount, amount, packing_amount, delivery_amount, other_amount, labour_amount, percent_total_discount, discount_total_amount, remarks, status, s_code, store_name, location, ac_code, account_name, mobile_number, pay_mode, pay_cash, pay_upi, pay_card, pay_credit, created_date, updated_date) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)',
               'sssssssssssssssssssssssssssssssssssssssssssssssss',
               $purchage_date, $bill_number, $gst_type, $under_gst, $bill_type, $item_name[$k], $barcode, $cur_article, $item_type[$k], $category[$k], $brand[$k], $color[$k], $size[$k], $cur_stock_qty, $cur_qty, $cur_balance_qty, $mou_name[$k], $cur_rate, $cur_purchase_price, $cur_percent_discount, $cur_discount_amt, $cur_net_price, $cur_gst, $cgst, $sgst, $cur_gst_amount, $cur_taxable_amount, $cur_amount, $packing_amount, $delivery_amount, $other_amount, $labour_amount, $percent_total_discount, $discount_total_amount, $remarks, $status, $s_code, $store_name, $location, $popup_ac_code, $popup_account_name, $popup_mobile_number, $popup_pay_mode, $popup_pay_cash, $popup_pay_upi, $popup_pay_card, $popup_pay_credit, $current_date, $current_date
            );
         }

         if (!$result) {
            $allSuccess = false;
         }
      }

      if ($allSuccess) {
         $msg = 'Record Updated successfully';
         $return = '';
         if ($is_print == '1') {
            $_SESSION['print_bill_no'] = !empty($bill_no) ? $bill_no : $bill_number;
            $_SESSION['print_option']  = $print_option;
            redirectTo('sale-creations.php?bill_no=' . $bill_no, $msg, $return);
         }
      } else {
         $msg = 'Failed!';
         $return = 1;
      }

      redirectTo('sale-creations.php?bill_no=' . $bill_no, $msg, $return);
   }
}
   
   
   if (isset($_POST['action']) && $_POST['action'] == 'delete' && !empty($_POST['id'])) {
   
      $id = $_POST['id'];
   
      if ($id) {
   
         $res = $db->delete("DELETE FROM tbl_bill WHERE id=?", 'i', $id);
   
         if ($res) {
            $msg = 'Record deleted successfully';
            $code = '';
         } else {
            $msg = 'Failed!';
            $code = '1';
         }
      } else {
         $msg = "Something went wrong, Try again!";
         $code = '1';
      }
   
      redirectTo('sale-creations.php', $msg, $code);
   } else if (isset($_POST['action']) && $_POST['action'] == 'send-mail' && !empty($_POST['id'])) {
   
      $sendid = '';
      if ($bill_no) {
         $sendid = '?bill_no=' . $bill_no;
      }
      $bill_number = $_POST['id'];
      $filename = generatePDF($bill_number);
   
      $mail_to = $_POST['account_email'] ?? '';
      if($mail_to){
      $mail_subject = "bill invoice";
      $mail_body = "<p>Dear Customer,
   Thank you for choosing Ramalaya to enrich your shopping experience. We are delighted to share the invoice for your recent purchase with us. Please find it attached to this email for your reference.
   At Ramalaya, we strive to bring you the finest products that blend tradition with elegance. We hope your purchase brings joy and fulfillment to your space.
   If you have any questions or require assistance, feel free to connect with us:
   </p>";
       
   
      send_mail($mail_to, $mail_subject, $mail_body, '', '', $filename);
      redirectTo('sale-creations.php' . $sendid, 'Mail has been sent successfully');
      } else {
         redirectTo('sale-creations.php' . $sendid, 'Email id not found.', 1);
      }
   }
   
   if ($bill_no) {
   
      $pur_stmt = $db->select("SELECT * FROM tbl_bill WHERE bill_number=?", 's', $bill_no);
      if ($pur_stmt) {
         $res = $pur_stmt->fetch_assoc();
         if (is_array($res)) {
            foreach ($res as $key => $value) {
               $$key = $validationHelper->filterText($value);
            }
         }
         $pur_stmt->close();
      }
   }
   
   ?>

   <?php
$groupOptions = '<option value="">Select Group</option>';
$grpRes = $db->select("SELECT group_name FROM tbl_group_master ORDER BY group_name ASC");
while ($g = $grpRes->fetch_assoc()) {
    $groupOptions .= '<option value="' . htmlspecialchars($g['group_name']) . '">' . htmlspecialchars($g['group_name']) . '</option>';
}

$subgroupOptions = '<option value="">Select SubGroup</option>';
$sgRes = $db->select("SELECT subgroup_name FROM tbl_subgroup_master ORDER BY subgroup_name ASC");
while ($s = $sgRes->fetch_assoc()) {
    $subgroupOptions .= '<option value="' . htmlspecialchars($s['subgroup_name']) . '">' . htmlspecialchars($s['subgroup_name']) . '</option>';
}

$brandOptions = '<option value="">Select Brand</option>';
$brRes = $db->select("SELECT brand_name FROM tbl_brand_master ORDER BY brand_name ASC");
while ($b = $brRes->fetch_assoc()) {
    $brandOptions .= '<option value="' . htmlspecialchars($b['brand_name']) . '">' . htmlspecialchars($b['brand_name']) . '</option>';
}

$colorOptions = '<option value="">Select Color</option>';
$coRes = $db->select("SELECT color_name FROM tbl_color_master ORDER BY color_name ASC");
while ($c = $coRes->fetch_assoc()) {
    $colorOptions .= '<option value="' . htmlspecialchars($c['color_name']) . '">' . htmlspecialchars($c['color_name']) . '</option>';
}

$sizeOptions = '<option value="">Select Size</option>';
$szRes = $db->select("SELECT size_name FROM tbl_size_master ORDER BY size_name ASC");
while ($z = $szRes->fetch_assoc()) {
    $sizeOptions .= '<option value="' . htmlspecialchars($z['size_name']) . '">' . htmlspecialchars($z['size_name']) . '</option>';
}
?>
<?php include('include/header.php'); ?>
<!--start table delete icon-->
<style>
   /* DELETE ICON BUTTON */
   .delete-btn{
   width:18px;
   height:18px;
   border:none;
   border-radius:14px;
   background:#009688;
   color:#fff;
   display:flex;
   align-items:center;
   justify-content:center;
   cursor:pointer;
   transition:all 0.35s ease;
   box-shadow:0 8px 20px rgba(255,30,86,0.25);
   font-size:10px;
   }
   /* HOVER EFFECT */
   .delete-btn:hover{
   transform:translateY(-3px) scale(1.05);

   box-shadow:0 12px 25px rgba(255,30,86,0.35);
   }
   /* CLICK EFFECT */
   .delete-btn:active{
   transform:scale(0.95);
   }
   /* ICON ANIMATION */
   .delete-btn i{
   transition:0.3s ease;
   }
   .delete-btn:hover i{
   transform:rotate(10deg);
   }
   /* MOBILE */
   @media(max-width:767px){
   .delete-btn{
   width:38px;
   height:38px;
   border-radius:12px;
   font-size:14px;
   }
   }
</style>
<style>
   .row-delete-btn:hover { color: #b71c1c !important; transform: scale(1.2); }
   .popup-ac-suggestion { padding: 8px 12px; cursor: pointer; border-bottom: 1px solid #f0f0f0; }
   .popup-ac-suggestion:hover { background: #f0faf9; color: #009688; }
</style>
<!--end table delete icon-->
<!-- POPUP -->
<style>
   #modernPopup-save .popup-box {
      max-width: 600px;
      width: 95%;
      background: #ffffff;
      border-radius: 24px;
      padding: 28px 32px;
      box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.25);
      position: relative;
      max-height: 90vh;
      overflow-y: auto;
   }
   #modernPopup-save .popup-title {
      font-size: 22px;
      font-weight: 700;
      color: #0f172a;
      margin-bottom: 4px;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
   }
   #modernPopup-save .close-popup {
      position: absolute;
      top: 20px;
      right: 20px;
      width: 36px;
      height: 36px;
      border: none;
      border-radius: 12px;
      background: #f1f5f9;
      color: #64748b;
      cursor: pointer;
      transition: all 0.2s ease;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 16px;
   }
   #modernPopup-save .close-popup:hover {
      background: #fee2e2;
      color: #ef4444;
   }
   .popup-bill-card {
      background: linear-gradient(135deg, #0f172a, #1e293b);
      border-radius: 16px;
      padding: 16px 20px;
      margin: 16px 0 20px 0;
      text-align: center;
      color: #ffffff;
      box-shadow: 0 6px 20px rgba(15,23,42,0.15);
   }
   .popup-bill-card .bill-label {
      font-size: 12px;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.8px;
      color: #94a3b8;
      margin-bottom: 4px;
   }
   .popup-bill-card .bill-amount {
      font-size: 32px;
      font-weight: 800;
      color: #38bdf8;
      letter-spacing: -0.5px;
   }
   #modernPopup-save .form-group-modern {
      margin-bottom: 16px;
   }
   #modernPopup-save .form-group-modern label {
      font-size: 13px;
      font-weight: 600;
      color: #334155;
      margin-bottom: 6px;
      display: block;
   }
   #modernPopup-save .popup-input,
   #modernPopup-save .popup-select {
      height: 48px;
      border-radius: 12px;
      border: 1px solid #cbd5e1;
      padding: 0 14px;
      font-size: 14px;
      background: #f8fafc;
      transition: all 0.2s ease;
      color: #0f172a;
      width: 100%;
   }
   #modernPopup-save .popup-input:focus,
   #modernPopup-save .popup-select:focus {
      border-color: #0284c7;
      background: #ffffff;
      box-shadow: 0 0 0 4px rgba(2, 132, 199, 0.12);
   }
   #modernPopup-save .popup-select {
      appearance: none;
      -webkit-appearance: none;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%2364748b' viewBox='0 0 16 16'%3E%3Cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3E%3C/svg%3E");
      background-repeat: no-repeat;
      background-position: right 14px center;
      background-size: 14px;
   }
   #modernPopup-save .popup-actions {
      display: flex;
      gap: 12px;
      margin-top: 24px;
   }
   #modernPopup-save .popup-btn {
      height: 48px;
      border-radius: 12px;
      font-size: 15px;
      font-weight: 700;
      border: none;
      cursor: pointer;
      transition: all 0.2s ease;
      flex: 1;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
   }
   #modernPopup-save .cancel-btn {
      background: #f1f5f9;
      color: #475569;
   }
   #modernPopup-save .cancel-btn:hover {
      background: #e2e8f0;
      color: #0f172a;
   }
   #modernPopup-save .save-btn {
      background: linear-gradient(135deg, #0284c7, #0369a1);
      color: #ffffff;
      box-shadow: 0 4px 12px rgba(2, 132, 199, 0.3);
   }
   #modernPopup-save .save-btn:hover {
      background: linear-gradient(135deg, #0369a1, #075985);
      transform: translateY(-1px);
      box-shadow: 0 6px 16px rgba(2, 132, 199, 0.4);
   }
   #modernPopup-save .print-btn {
      background: linear-gradient(135deg, #059669, #047857);
      color: #ffffff;
      box-shadow: 0 4px 12px rgba(5, 150, 105, 0.3);
   }
   #modernPopup-save .print-btn:hover {
      background: linear-gradient(135deg, #047857, #065f46);
      transform: translateY(-1px);
      box-shadow: 0 6px 16px rgba(5, 150, 105, 0.4);
   }
</style>
<div class="modern-popup-save" id="modernPopup-save">
   <div class="popup-box">
      <!-- CLOSE -->
      <button type="button" class="close-popup" onclick="closePopupsave()">
         <i class="fa fa-times"></i>
      </button>
      <h3 class="popup-title">
         <i class="fa fa-credit-card" style="color: #0284c7;"></i> Payment &amp; Account Details
      </h3>

      <!-- VALIDATION ERROR BANNER -->
      <div id="popup_error_msg" style="display:none;background:#fff0f0;border:1px solid #f5c2c2;border-left:4px solid #e53935;border-radius:12px;padding:12px 14px;margin-top:12px;margin-bottom:14px;font-size:13px;color:#b71c1c;gap:10px;line-height:1.6;">
         <div style="display:flex;align-items:flex-start;gap:8px;">
            <i class="fa fa-exclamation-circle" style="font-size:15px;flex-shrink:0;margin-top:2px;"></i>
            <span id="popup_error_text"></span>
         </div>
      </div>

      <?php
         $row_ac = [
            'account_code' => $ac_code ?? '',
            'mobile_no_1' => $mobile_number ?? '',
            'account_name' => $account_name ?? ''
         ];
         if (!empty($mobile_number) || !empty($account_name)) {
            $ac_stmt = $db->select("SELECT * FROM tbl_account_master WHERE mobile_no_1 =? OR account_name=?", 'ss', $mobile_number, $account_name);
            if ($ac_stmt && $ac_stmt->num_rows > 0) {
               $fetched_ac = $ac_stmt->fetch_assoc();
               if ($fetched_ac) {
                  $row_ac = $fetched_ac;
               }
            }
         }
      ?>

      <!-- Bill Amount Hero Card -->
      <div class="popup-bill-card">
         <div class="bill-label"><i class="fa fa-file-text-o"></i> Total Payable Amount</div>
         <div class="bill-amount" id="popup_bill_amount">₹0.00</div>
      </div>

      <!-- Account Master Inputs -->
      <div class="row">
         <div class="col-md-6">
            <div class="form-group-modern" style="position: relative;">
               <label>A/C Code</label>
               <input type="text"
                  class="popup-input"
                  id="popup_account_code"
                  name="ac_code"
                  placeholder="Enter A/C Code"
                  autocomplete="off"
                  value="<?= htmlspecialchars($row_ac['account_code'] ?? ''); ?>"
                  oninput="getPopupAccountDetails(this.value,'ac_code')">
               <div id="popup_ac_suggestions" class="autocomplete-suggestions" style="display:none; position:absolute; left:0; top:100%; width:100%; z-index:1050; background:#fff; border:1px solid #cbd5e1; border-radius:10px; box-shadow:0 8px 20px rgba(0,0,0,0.12); max-height:180px; overflow-y:auto;"></div>
            </div>
         </div>
         <div class="col-md-6">
            <div class="form-group-modern" style="position: relative;">
               <label>Mobile No</label>
               <input type="text"
                  class="popup-input"
                  id="popup_mobile_number"
                  name="mobile_number"
                  placeholder="Enter Mobile No"
                  autocomplete="off"
                  value="<?= htmlspecialchars($row_ac['mobile_no_1'] ?? ''); ?>"
                  oninput="getPopupAccountDetails(this.value,'mobile')">
               <div id="suggestions_mobile" class="autocomplete-suggestions" style="display:none; position:absolute; left:0; top:100%; width:100%; z-index:1050; background:#fff; border:1px solid #cbd5e1; border-radius:10px; box-shadow:0 8px 20px rgba(0,0,0,0.12); max-height:180px; overflow-y:auto;"></div>
            </div>
         </div>
      </div>
      <div class="form-group-modern" style="position: relative;">
         <label>A/C Name</label>
         <input type="text"
            class="popup-input"
            id="popup_account_name"
            name="account_name"
            placeholder="Enter Account Name"
            autocomplete="off"
            value="<?= htmlspecialchars($row_ac['account_name'] ?? ''); ?>"
            oninput="getPopupAccountDetails(this.value,'account_name')">
         <div id="suggestions_account_name" class="autocomplete-suggestions" style="display:none; position:absolute; left:0; top:100%; width:100%; z-index:1050; background:#fff; border:1px solid #cbd5e1; border-radius:10px; box-shadow:0 8px 20px rgba(0,0,0,0.12); max-height:180px; overflow-y:auto;"></div>
      </div>

      <!-- Payment Mode Selection -->
      <div class="form-group-modern">
         <label>Payment Mode <span style="color:#e53935;">*</span></label>
         <?php
            $default_pay_mode = !empty($DEFAULT_PAYMENT_MODE) ? $DEFAULT_PAYMENT_MODE : (defined('_DEFAULT_PAYMENT_MODE_') ? _DEFAULT_PAYMENT_MODE_ : 'CASH');
            $default_pay_mode = strtoupper(trim($default_pay_mode));
            $active_pay_mode  = !empty($pay_mode) ? strtoupper(trim($pay_mode)) : $default_pay_mode;
         ?>
         <select class="popup-select" name="pay_mode" id="payment_mode_select" onchange="handlePaymentMode(this.value)">
            <option value="">-- Select Payment Mode --</option>
            <option value="CASH" <?= ($active_pay_mode == 'CASH') ? 'selected' : '' ?>>CASH</option>
            <option value="UPI" <?= ($active_pay_mode == 'UPI') ? 'selected' : '' ?>>UPI</option>
            <option value="CARD" <?= ($active_pay_mode == 'CARD') ? 'selected' : '' ?>>CARD</option>
            <option value="CREDIT" <?= ($active_pay_mode == 'CREDIT') ? 'selected' : '' ?>>CREDIT</option>
            <option value="DUAL" <?= ($active_pay_mode == 'DUAL') ? 'selected' : '' ?>>DUAL PAYMENT</option>
         </select>
      </div>

      <!-- Payment Breakdown Fields -->
      <div class="row">
         <div class="col-md-6">
            <div class="form-group-modern" id="field_cash">
               <label>CASH Amount</label>
               <input type="text"
                  class="popup-input"
                  name="pay_cash"
                  id="pay_cash"
                  placeholder="Enter Cash Amount"
                  value="<?= htmlspecialchars($pay_cash ?? '') ?>"
                  disabled>
            </div>
         </div>
         <div class="col-md-6">
            <div class="form-group-modern" id="field_upi">
               <label>UPI Amount</label>
               <input type="text"
                  class="popup-input"
                  name="pay_upi"
                  id="pay_upi"
                  placeholder="Enter UPI Amount"
                  value="<?= htmlspecialchars($pay_upi ?? '') ?>"
                  disabled>
            </div>
         </div>
      </div>
      <div class="row">
         <div class="col-md-6">
            <div class="form-group-modern" id="field_card">
               <label>CARD Amount</label>
               <input type="text"
                  class="popup-input"
                  name="pay_card"
                  id="pay_card"
                  placeholder="Enter Card Amount"
                  value="<?= htmlspecialchars($pay_card ?? '') ?>"
                  disabled>
            </div>
         </div>
         <div class="col-md-6">
            <div class="form-group-modern" id="field_credit">
               <label>CREDIT Amount</label>
               <input type="text"
                  class="popup-input"
                  name="pay_credit"
                  id="pay_credit"
                  placeholder="Enter Credit Amount"
                  value="<?= htmlspecialchars($pay_credit ?? '') ?>"
                  disabled>
            </div>
         </div>
      </div>

      <!-- RECEIPT PRINT FORMAT OPTION IN POPUP -->
      <div style="margin: 10px 0 15px 0; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px 14px;">
         <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 6px;">
            <span style="font-weight: 600; font-size: 13px; color: #334155;"><i class="fa fa-print" style="color: #009688; margin-right: 5px;"></i> Receipt Print Format:</span>
            <span style="font-size: 11px; color: #64748b;">(For Save &amp; Print)</span>
         </div>
         <div style="display: flex; gap: 20px; align-items: center;">
            <label class="radio-inline" style="font-weight: 500; font-size: 13px; color: #334155; margin: 0; cursor: pointer;">
               <input type="radio" name="popup_print_option" value="80mm" checked onchange="syncPrintOption(this.value)"> 80 MM (Thermal)
            </label>
            <label class="radio-inline" style="font-weight: 500; font-size: 13px; color: #334155; margin: 0; cursor: pointer;">
               <input type="radio" name="popup_print_option" value="a4" onchange="syncPrintOption(this.value)"> A4 (Standard)
            </label>
            <label class="radio-inline" style="font-weight: 500; font-size: 13px; color: #334155; margin: 0; cursor: pointer;">
               <input type="radio" name="popup_print_option" value="a5" onchange="syncPrintOption(this.value)"> A5 (Compact)
            </label>
         </div>
      </div>

      <!-- BUTTONS -->
      <div class="popup-actions">
         <button type="button" class="popup-btn cancel-btn" onclick="closePopupsave()">
            <i class="fa fa-times"></i> Cancel
         </button>
         <button type="button" class="popup-btn save-btn" onclick="validateAndSubmitPopup('save')">
            <i class="fa <?= (!empty($bill_no) ? 'fa-save' : 'fa-check') ?>"></i>
            <?= (!empty($bill_no) ? 'Update Bill' : 'Save Bill') ?>
         </button>
         <button type="button" class="popup-btn print-btn" onclick="validateAndSubmitPopup('print')">
            <i class="fa fa-print"></i> Save &amp; Print
         </button>
      </div>
   </div>
</div>
<!--end popup-save button--> 
<!--start plus icon-->
<style>
   /* PLUS BUTTON */
   .plus-btn{
   width:15px;
   height:15px;
   border:none;
   border-radius:16px;
   background:#009688 ;
   color:#fff;
   display:flex;
   align-items:center;
   justify-content:center;
   cursor:pointer;
   font-size:15px;
   transition:0.35s ease;
   box-shadow:0 10px 25px rgba(37,99,235,0.25);
   }
   .plus-btn:hover{
   transform:translateY(-3px);
   }
   /* POPUP */
   .modern-popup{
   position:fixed;
   inset:0;
   background:rgba(15,23,42,0.55);
   display:none;
   align-items:center;
   justify-content:center;
   z-index:9999;
   padding:15px;
   }
   .modern-popup.active{
   display:flex;
   }
   .modern-popup-pur{
   position:fixed;
   inset:0;
   background:rgba(15,23,42,0.55);
   display:none;
   align-items:center;
   justify-content:center;
   z-index:9999;
   padding:15px;
   }
   .modern-popup-pur.active{
   display:flex;
   }
   .modern-popupsp{
   position:fixed;
   inset:0;
   background:rgba(15,23,42,0.55);
   display:none;
   align-items:center;
   justify-content:center;
   z-index:9999;
   padding:15px;
   }
   .modern-popupsp.active{
   display:flex;
   }
   .modern-popup-save{
   position:fixed;
   inset:0;
   background:rgba(15,23,42,0.55);
   display:none;
   align-items:center;
   justify-content:center;
   z-index:9999;
   padding:15px;
   }
   .modern-popup-save.active{
   display:flex;
   }
   /* POPUP BOX */
   .popup-box{
   width:100%;
   max-width:530px;
   background:#fff;
   border-radius:24px;
   padding:26px;
   position:relative;
   animation:popupScale 0.3s ease;
   box-shadow:0 25px 60px rgba(0,0,0,0.15);
   }
   /* CLOSE BUTTON */
   .close-popup{
   position:absolute;
   top:15px;
   right:15px;
   width:36px;
   height:36px;
   border:none;
   border-radius:12px;
   background:#f1f5f9;
   cursor:pointer;
   transition:0.3s ease;
   }
   .close-popup:hover{
   background:#eff6ff;
   color:#2563eb;
   }
   /* TITLE */
   .popup-title{
   font-size:23px;
   font-weight:700;
   color:#0f172a;
   margin-bottom:8px;
   }
   /* TEXT */
   .popup-text{
   font-size:14px;
   color:#64748b;
   margin-bottom:20px;
   }
   /* FORM GROUP */
   .form-group-modern{
   margin-bottom:18px;
   }
   /* LABEL */
   .form-group-modern label{
   display:block;
   font-size:14px;
   font-weight:600;
   margin-bottom:8px;
   color:#1e293b;
   }
   /* INPUT */
   .popup-input{
   width:100%;
   height:56px;
   border:1px solid #dbe2ea;
   border-radius:16px;
   padding:0 16px;
   background:#f8fbff;
   font-size:15px;
   outline:none;
   transition:0.35s ease;
   }
   .popup-input:focus{
   border-color:#0d6efd;
   background:#fff;
   box-shadow:0 0 0 5px rgba(13,110,253,0.10);
   }
   /* SELECT */
   .popup-select{
   width:100%;
   height:56px;
   border:1px solid #dbe2ea;
   border-radius:16px;
   padding:0 16px;
   background:#f8fbff;
   font-size:15px;
   outline:none;
   transition:0.35s ease;
   appearance:none;
   background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='18' height='18' fill='%230d6efd' viewBox='0 0 16 16'%3E%3Cpath fill-rule='evenodd' d='M1.646 5.646a.5.5 0 0 1 .708 0L8 11.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3E%3C/svg%3E");
   background-repeat:no-repeat;
   background-position:right 16px center;
   background-size:16px;
   }
   .popup-select:focus{
   border-color:#0d6efd;
   background-color:#fff;
   box-shadow:0 0 0 5px rgba(13,110,253,0.10);
   }
   /* BUTTON AREA */
   .popup-actions{
   display:flex;
   gap:12px;
   margin-top:22px;
   }
   /* BUTTON */
   .popup-btn{
   flex:1;
   height:52px;
   border:none;
   border-radius:16px;
   font-size:15px;
   font-weight:600;
   cursor:pointer;
   transition:0.3s ease;
   }
   /* CANCEL */
   .cancel-btn{
   background:#eef2f7;
   color:#334155;
   }
   /* SAVE */
   .save-btn{
   background:linear-gradient(135deg,#0d6efd,#2563eb);
   color:#fff;
   }
   .save-btn:hover{
   transform:translateY(-2px);
   }
   /* ANIMATION */
   @keyframes popupScale{
   from{
   transform:scale(0.85);
   opacity:0;
   }
   to{
   transform:scale(1);
   opacity:1;
   }
   }
   /* MOBILE MODE */
   @media(max-width:767px){
   .popup-box{
   padding:22px 18px;
   border-radius:22px;
   }
   .popup-title{
   font-size:20px;
   }
   .popup-input,
   .popup-select{
   height:54px;
   font-size:14px;
   border-radius:14px;
   }
   .popup-btn{
   height:50px;
   font-size:14px;
   border-radius:14px;
   }
   .popup-actions{
   flex-direction:column;
   }
   .plus-btn{
   width:44px;
   height:44px;
   border-radius:14px;
   font-size:16px;
   }
   }
</style>
<!-- PLUS BUTTON -->
<!-- POPUP -->
<div class="modern-popup" id="modernPopup">
   <div class="popup-box">
      <button class="close-popup" onclick="closePopup()"><i class="fa fa-times"></i></button>
      <h3 class="popup-title">Details</h3>
      <div id="grp_error_msg" style="display:none;background:#fdecea;color:#e53935;padding:10px 14px;border-radius:10px;margin-bottom:14px;font-size:13px;">
         <span id="grp_error_text"></span>
      </div>
      <div class="row">
         <div class="col-md-6"><div class="form-group-modern">
            <label>Group</label>
            <select id="grp_group" class="popup-select"><?= $groupOptions ?></select>
         </div></div>
         <div class="col-md-6"><div class="form-group-modern">
            <label>SubGroup</label>
            <select id="grp_sub_category" class="popup-select"><?= $subgroupOptions ?></select>
         </div></div>
      </div>
      <div class="row">
         <div class="col-md-6"><div class="form-group-modern">
            <label>Brand</label>
            <select id="grp_brand" class="popup-select"><?= $brandOptions ?></select>
         </div></div>
         <div class="col-md-6"><div class="form-group-modern">
            <label>Color</label>
            <select id="grp_color" class="popup-select"><?= $colorOptions ?></select>
         </div></div>
      </div>
      <div class="row">
         <div class="col-md-6"><div class="form-group-modern">
            <label>Size</label>
            <select id="grp_size" class="popup-select"><?= $sizeOptions ?></select>
         </div></div>
         <div class="col-md-6"><div class="form-group-modern">
            <label>Style</label>
            <input type="text" id="grp_style" class="popup-input" placeholder="Enter style">
         </div></div>
      </div>
      <div class="popup-actions">
         <button class="popup-btn cancel-btn" onclick="closePopup()">Cancel</button>
         <button class="popup-btn save-btn" onclick="saveGroupDetails()">Save</button>
      </div>
   </div>
</div>

<!--end plus icon--> 
<div class="modern-popupsp" id="modernPopupsp">
   <div class="popup-box">
      <button class="close-popup" onclick="closePopupsp()"><i class="fa fa-times"></i></button>
      <h3 class="popup-title">MRP Details</h3>
      <div id="mrp_error_msg" style="display:none;background:#fdecea;color:#e53935;padding:10px 14px;border-radius:10px;margin-bottom:14px;font-size:13px;">
         <span id="mrp_error_text"></span>
      </div>
      <div class="row"><div class="col-md-12"><div class="form-group-modern">
         <label>MRP</label>
         <input type="text" id="mrp_current" class="popup-input" readonly>
      </div></div></div>
      <div class="row">
         <div class="col-md-6"><div class="form-group-modern">
            <label>Dis.(%)</label>
            <input type="text" id="mrp_discount_pct" name="percent_discount[]" class="popup-input" placeholder="0" oninput="syncMRPFromPercent()">
         </div></div>
         <div class="col-md-6"><div class="form-group-modern">
            <label>Dis.Amt.</label>
            <input type="text" id="mrp_discount_amt" name="discount_amt[]" class="popup-input" placeholder="0" oninput="syncMRPFromAmount()">
         </div></div>
      </div>
      <div class="row"><div class="col-md-12"><div class="form-group-modern">
         <label>SP</label>
         <input type="text" id="mrp_selling_price" class="popup-input" readonly>
      </div></div></div>
      <div class="popup-actions">
         <button class="popup-btn cancel-btn" onclick="closePopupsp()">Cancel</button>
         <button class="popup-btn save-btn" onclick="saveMRPDetails()">Save</button>
      </div>
   </div>
</div>

<!--end plus icon--> 
<!-- Main content -->
<section class="">
   <div class="row">
      <!-- Form controls -->
      <div class="col-sm-12">
         <div class="panel lobidisable panel-bd">
            <div class="panel-heading sidebar-toggle" data-toggle="offcanvas">
               <div class="btn-group" id="buttonexport">
                  <a href="#">
                     <h5>Bill Creations</h5>
                  </a>
               </div>
               <!-- <div class="btn-group" id="buttonlist"> 
                  <a class="btn btn-add " href="clist.html"> 
                  <span style="font-size: 13px;"><i class="fa fa-plus"></i>  User master list</span> </a>
                  
                  </div> -->
            </div>
            <!-- TOGGLE BUTTON -->
            <button class="filter-toggle-btn" id="filterToggle">
            <i class="fa fa-sliders"></i> Additional Data
            </button>
            <!-- OVERLAY -->
            <div class="filter-overlay" id="filterOverlay"></div>
            <!-- SIDEPANEL -->
            <div class="leftside-panel" id="leftPanel">
               <!-- CLOSE BUTTON -->
               <div class="panel-top">
                  <h4>Additional Data</h4>
                  <button class="close-panel" id="closePanel">
                  <i class="fa fa-times"></i>
                  </button>
               </div>
               <!-- FONT AWESOME -->
               <style>
                  /* FORM GROUP */
                  .modern-form-group{
                  position: relative;
                  margin-bottom: 18px;
                  }
                  /* LABEL */
                  .modern-form-group label{
                  display: flex;
                  align-items: center;
                  gap: 0px;
                  font-size: 14px;
                  font-weight: 600;
                  color: #1d3557;
                  margin-bottom: 10px;
                  }
                  /* INPUT */
                  .modern-input{
                  width: 100%;
                  height: 40px;
                  border: 1px solid #e5e7eb;
                  border-radius: 10px;

                  padding: 0 18px;
                  font-size: 14px;
                  background: #f8fbff;
                  transition: all 0.35s ease;
                  outline: none;
                  }
                  /* PLACEHOLDER */
                  .modern-input::placeholder{
                  color: #9ca3af;
                  }
                  /* FOCUS EFFECT */
                  .modern-input:focus{
                  border-color: #009688;
                  background: #fff;
                  box-shadow: 0 0 0 5px rgba(13,110,253,0.10);
                  transform: translateY(-2px);
                  }
                  /* HOVER */
                  .modern-input:hover{
                  border-color: #009688;
                  }
                  /* MOBILE RESPONSIVE */
                  @media (max-width: 767px){
                  .modern-form-wrapper{
                  padding: 15px;
                  border-radius: 18px;
                  }
                  .modern-input{
                  height: 54px;
                  border-radius: 16px;
                  font-size: 14px;
                  }
                  .modern-form-group label{
                  font-size: 13px;
                  }
                  .modern-form-group label i{
                  width: 30px;
                  height: 30px;
                  border-radius: 10px;
                  font-size: 13px;
                  }
                  }
               </style>
               <!-- YOUR FILTER CONTENT -->
               <div class="panel-content">
                  <div class="row">
                     <div class="col-md-6">
                        <div class="modern-form-group">
                           <label>Date & Time</label>
                           <input type="text" class="modern-input datetimepicker" name="purchage_date" id="purchage_date" placeholder="Enter Date & Time" value="<?= $purchage_date ?? '' ?>">
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="modern-form-group">
                           <label>Bill No.</label>
                           <input type="text" class="modern-input" name="bill_number" id="bill_number" placeholder="Enter Bill Number" value="<?= $bill_number ?? generateBillNo('tbl_bill', 'bill_number', 'BC') ?>">
                        </div>
                     </div>
                  </div>
                  <div class="row">
                     <div class="col-md-2">
                        <div class="modern-form-group">
                           <label>S.code</label>
                           <input type="text" class="modern-input" name="s_code" id="s_code" placeholder="Enter S.code" value="<?= $s_code ?? '' ?>">
                        </div>
                     </div>
                     <div class="col-md-5">
                        <div class="modern-form-group">
                           <label>Store name</label>
                           <input type="text" class="modern-input" name="store_name" id="store_name" placeholder="Enter Store name" value="<?= $store_name ?? '' ?>">
                        </div>
                     </div>
                     <div class="col-md-5">
                        <div class="modern-form-group">
                           <label>Location</label>
                           <input type="text" class="modern-input" name="location" id="location" placeholder="Enter Location" value="<?= $location ?? '' ?>">
                        </div>
                     </div>
                  </div>
                  <style>
                     /* MODERN SELECT FORM */
                     /* FORM GROUP */
                     .modern-form-group{
                     margin-bottom:18px;
                     }
                     /* LABEL */
                     .modern-form-group label{
                     display:block;
                     font-size:14px;
                     font-weight:600;
                     color:#1e293b;
                     margin-bottom:10px;
                     letter-spacing:0.3px;
                     }
                     /* SELECT BOX */
                     .modern-select{
                     width:100%;
                     height:40px;
                     border:1px solid #e2e8f0;
                     border-radius:10px;
                     padding:0 18px;
                     background:#f8fbff;
                     font-size:15px;
                     color:#334155;
                     outline:none;
                     transition:all 0.35s ease;
                     appearance:none;
                     -webkit-appearance:none;
                     -moz-appearance:none;
                     /* CUSTOM ARROW */
                     background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='18' height='18' fill='%230d6efd' viewBox='0 0 16 16'%3E%3Cpath fill-rule='evenodd' d='M1.646 5.646a.5.5 0 0 1 .708 0L8 11.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3E%3C/svg%3E");
                     background-repeat:no-repeat;
                     background-position:right 18px center;
                     background-size:16px;
                     }
                     /* HOVER */
                     .modern-select:hover{
                     border-color:#009688;
                     background:#ffffff;
                     }
                     /* FOCUS */
                     .modern-select:focus{
                     border-color:#009688;
                     background:#ffffff;
                     box-shadow:0 0 0 5px rgba(13,110,253,0.10);
                     transform:translateY(-2px);
                     }
                     /* OPTION */
                     .modern-select option{
                     padding:10px;
                     }
                     /* MOBILE DESIGN */
                     @media(max-width:767px){
                     .modern-select-wrapper{
                     padding:15px;
                     border-radius:18px;
                     }
                     .modern-select{
                     height:54px;
                     font-size:14px;
                     border-radius:16px;
                     }
                     .modern-form-group label{
                     font-size:13px;
                     margin-bottom:8px;
                     }
                     }
                  </style>
                  <div class="row">
                     <!-- GST TYPE -->
                     <div class="col-md-4 col-12">
                        <div class="modern-form-group">
                           <label>GST Type</label>
                           <select name="gst_type" class="modern-select" onchange="updateBillType()" id="gst_type" required>
                              <option value="">Select GST Type</option>
                              <?php foreach (['Inclusive', 'Exclusive', 'None'] as $opt): ?>
                                 <?php 
                                    // If $gst_type exists, use its value; otherwise default to 'Exclusive'
                                    $current = $gst_type ?? 'Exclusive'; 
                                 ?>
                                 <option value="<?= $opt ?>" <?= ($current == $opt) ? 'selected' : '' ?>><?= $opt ?></option>
                              <?php endforeach; ?>
                           </select>
                        </div>
                     </div>
                     <!-- UNDER GST -->
                     <div class="col-md-4 col-12">
                        <div class="modern-form-group">
                           <label>Under GST</label>
                           <select name="under_gst" onchange="change_under_gst()" class="modern-select" id="under_gst" required>
                              <option value="">Select Under GST</option>
                              <?php foreach (['Local', 'Inter'] as $opt): ?>
                                 <?php 
                                    // If $under_gst exists, use its value; otherwise default to 'Local'
                                    $current = $under_gst ?? 'Local'; 
                                 ?>
                                 <option value="<?= $opt ?>" <?= ($current == $opt) ? 'selected' : '' ?>><?= $opt ?></option>
                              <?php endforeach; ?>
                           </select>
                        </div>
                     </div>
                     <!-- BILL TYPE -->
                     <div class="col-md-4 col-12">
                        <div class="modern-form-group">
                           <label>Bill Type</label>
                           <select name="bill_type" class="modern-select" id="bill_type" required>
                              <option value="">Select Bill Type</option>
                              <?php 
                               $billTypeOpts = [
                                   'Tax-Bill'  => 'Tax Invoice',
                                   'Estimate'  => 'Estimate',
                                   'Cash-Memo' => 'Cash Memo'
                               ];
                               foreach ($billTypeOpts as $optVal => $optLabel): 
                                  $current = $bill_type ?? 'Tax-Bill'; 
                                  if ($current == 'Tax Invoice') $current = 'Tax-Bill';
                                  if ($current == 'Cash Memo')   $current = 'Cash-Memo';
                               ?>
                                  <option value="<?= $optVal ?>" <?= ($current == $optVal) ? 'selected' : '' ?>><?= $optLabel ?></option>
                               <?php endforeach; ?>
                           </select>
                        </div>
                     </div>
                  </div>
                  <div class="row">
                     <div class="col-md-4">
                        <div class="modern-form-group">
                           <label>Packing Charge</label>
                           <input type="text" class="modern-input" name="packing_amount" id="packing_amount" placeholder="Enter Packing Charge" value="<?= $packing_amount ?? '' ?>" oninput="discountTotalPercent(); total_hidden_posted();">
                        </div>
                     </div>
                     <div class="col-md-4">
                        <div class="modern-form-group">
                           <label>Delivery Charge</label>
                           <input type="text" class="modern-input" name="delivery_amount" id="delivery_amount" placeholder="Enter Delivery Charge" value="<?= $delivery_amount ?? '' ?>" oninput="discountTotalPercent(); total_hidden_posted();">
                        </div>
                     </div>
                     <div class="col-md-4">
                        <div class="modern-form-group">
                           <label>Handling charge</label>
                           <input type="text" class="modern-input" name="other_amount" id="other_amount" placeholder="Enter Handling Charge" value="<?= $other_amount ?? '' ?>" oninput="discountTotalPercent(); total_hidden_posted();">
                        </div>
                     </div>
                  </div>
                  <div class="row">
                     <div class="col-md-4">
                        <div class="modern-form-group">
                           <label>Labour Charge</label>
                           <input type="text" class="modern-input" name="labour_amount" id="labour_amount" placeholder="Enter Labour Charge" value="<?= $labour_amount ?? '' ?>" oninput="discountTotalPercent(); total_hidden_posted();">
                        </div>
                     </div>
                     <div class="col-md-8"></div>
                  </div>
                  <div class="row">
                  <div class="col-md-12">
                     <div class="modern-form-group">
                        <label>Adjustment amount</label>
                        <input type="text" class="modern-input" name="discount_total_amount_display" 
                              id="adjustment_amount" placeholder="Enter Adjustment Amount" 
                              value="<?= $discount_total_amount ?? '' ?>" 
                              oninput="applyAdjustmentAmount()">
                     </div>
                  </div>
               </div>
                  <div class="row">
                     <div class="col-md-12">
                        <div class="modern-form-group">
                           <label>Remarks</label>
                           <input type="text" class="modern-input" name="remarks" id="remarks" placeholder="Enter Remarks" value="<?= $remarks ?? '' ?>">
                        </div>
                     </div>
                  </div>
               </div>
            </div>
            <style>
               /* =========================
               TOGGLE BUTTON
               ========================= */
               .filter-toggle-btn{
               position:fixed;
               bottom:20px;
               right:20px;
               background:linear-gradient(135deg,#aa8453,#cab293);
               color:#fff;
               border:none;
               outline:none;
               padding:14px 20px;
               border-radius:50px;
               font-size:14px;
               font-weight:600;
               cursor:pointer;
               z-index:9999;
               box-shadow:0 8px 25px rgba(0,0,0,0.15);
               transition:0.3s ease;
               }
               .filter-toggle-btn i{
               margin-right:6px;
               }
               .filter-toggle-btn:hover{
               transform:translateY(-3px) scale(1.05);
               }
               /* =========================
               OVERLAY
               ========================= */
               .filter-overlay{
               position:fixed;
               top:0;
               left:0;
               width:100%;
               height:100%;
               background:rgba(0,0,0,0.45);
               opacity:0;
               visibility:hidden;
               transition:0.3s ease;
               z-index:999;
               }
               .filter-overlay.active{
               opacity:1;
               visibility:visible;
               }
               /* =========================
               SIDE PANEL
               ========================= */
               .leftside-panel{
               position:fixed;
               top:0;
               left:-100%;
               width:640px;
               height:100vh;
               background:#f5f7fb;
               overflow-y:auto;
               z-index:9999;
               transition:0.4s ease;
               box-shadow:10px 0 30px rgba(0,0,0,0.12);
               }
               /* ACTIVE */
               .leftside-panel.active{
               left:0;
               }
               /* PANEL TOP */
               .panel-top{
               display:flex;
               align-items:center;
               justify-content:space-between;
               padding:18px 20px;
               background:#fff;
               border-bottom:1px solid #eee;
               position:sticky;
               top:0;
               z-index:10;
               }
               .panel-top h4{
               margin:0;
               font-size:20px;
               font-weight:700;
               }
               /* CLOSE */
               .close-panel{
               width:38px;
               height:38px;
               border:none;
               border-radius:50%;
               background:#f3f4f6;
               cursor:pointer;
               transition:0.3s ease;
               }
               .close-panel:hover{
               background:#aa8453;
               color:#fff;
               transform:rotate(90deg);
               }
               /* CONTENT */
               .panel-content{
               padding:20px;
               }
               /* SCROLLBAR */
               .leftside-panel::-webkit-scrollbar{
               width:5px;
               }
               .leftside-panel::-webkit-scrollbar-thumb{
               background:#aa8453;
               border-radius:20px;
               }
               /* =========================
               MOBILE MODE
               ========================= */
               @media(max-width:767px){
               .leftside-panel{
               width:85%;
               }
               .filter-toggle-btn{
               bottom:15px;
               right:15px;
               padding:12px 18px;
               font-size:13px;
               }
               .panel-top h4{
               font-size:18px;
               }
               }
            </style>
            <div class="panel-body">
               <!-- <div class="btn-group">
                  <a href="Purchase-master-list.php"><button class="btn btn-exp btn-sm dropdown-toggle" data-toggle=""><i class="fa fa-users"></i><span style="margin-left:3px;">Bill List !</span></button></a>
                  </div> -->
               <form method="post" name="purchage_form" id="purchage_form" enctype="multipart/form-data">
                  <div class="row" style="margin-top:-17px;">
                     <div class="col-sm-6">
                        <div class="form-group">
                           <label>Barcode No</label>
                           <input type="text" class="form-control" id="barcode_number" name="barcode_number" placeholder="Enter Barcode Number" autocomplete="off">
                        </div>
                     </div>
                     <div class="col-sm-6">
                        <div class="form-group">
                           <label>Article No</label>
                           <input type="text" class="form-control" id="article_no" name="article_no" placeholder="Enter Article Number" autocomplete="off">
                           <div id="suggestions_article_no" class="autocomplete-suggestions"></div>
                        </div>
                     </div>
                  </div>
                  <div class="row">
                     <div class="col-sm-12" style="margin-top:-15px;">
                        <div class="form-group" style="position: relative; margin-bottom: 8px;">
                           <label>Item Name</label>
                           <div style="position: relative;">
                              <i class="fa fa-search" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 14px; pointer-events: none; z-index: 2;"></i>
                              <input type="text" class="form-control form" name="item_name" id="item_name" placeholder="Search or filter Item Name, Barcode, or Article No..." autocomplete="off" style="padding-left: 38px; height: 42px; border-radius: 8px; font-size: 14px;">
                           </div>
                           <div id="suggestions_item_name" class="autocomplete-suggestions"></div>
                        </div>
                     </div>
                  </div>

                  <!-- Modern Item Cards Block -->
                  <style>
                     .item-card-block-wrapper {
                        background: #f8fafc;
                        border: 1px solid #e2e8f0;
                        border-radius: 14px;
                        padding: 14px 18px;
                        margin-top: 4px;
                        margin-bottom: 18px;
                        box-shadow: 0 1px 4px rgba(0,0,0,0.03);
                     }
                     .item-card-block-header {
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                        margin-bottom: 12px;
                        padding-bottom: 8px;
                        border-bottom: 1px solid #e2e8f0;
                        flex-wrap: wrap;
                        gap: 8px;
                     }
                     .item-card-block-header .block-badge {
                        font-size: 14px;
                        font-weight: 700;
                        color: #1e293b;
                        display: inline-flex;
                        align-items: center;
                        gap: 6px;
                     }
                     .item-card-block-header .block-subtitle {
                        font-size: 12px;
                        color: #64748b;
                        margin-left: 6px;
                     }
                     .item-card-block-header .badge-counter {
                        background: #e2e8f0;
                        color: #334155;
                        font-size: 12px;
                        font-weight: 600;
                        border-radius: 20px;
                        padding: 4px 12px;
                     }
                     .item-cards-grid {
                        display: grid;
                        grid-template-columns: repeat(auto-fill, minmax(230px, 1fr));
                        gap: 12px;
                        max-height: 250px;
                        overflow-y: auto;
                        padding: 4px;
                     }
                     .item-cards-grid::-webkit-scrollbar {
                        width: 6px;
                     }
                     .item-cards-grid::-webkit-scrollbar-thumb {
                        background: #cbd5e1;
                        border-radius: 4px;
                     }
                     .item-card-item {
                        background: #ffffff;
                        border: 1.5px solid #e2e8f0;
                        border-radius: 12px;
                        padding: 12px;
                        cursor: pointer;
                        transition: all 0.22s ease;
                        display: flex;
                        flex-direction: column;
                        justify-content: space-between;
                        user-select: none;
                        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
                     }
                     .item-card-item:hover {
                        border-color: #3b82f6;
                        transform: translateY(-2px);
                        box-shadow: 0 6px 14px rgba(59, 130, 246, 0.12);
                     }
                     .item-card-item:active {
                        transform: scale(0.98);
                     }
                     .item-card-item.card-added-flash {
                        border-color: #10b981 !important;
                        background: #ecfdf5 !important;
                        box-shadow: 0 0 12px rgba(16, 185, 129, 0.35) !important;
                     }
                     .card-item-top {
                        display: flex;
                        justify-content: space-between;
                        align-items: flex-start;
                        gap: 8px;
                        margin-bottom: 8px;
                     }
                     .card-item-name {
                        font-weight: 700;
                        font-size: 14px;
                        color: #0f172a;
                        white-space: nowrap;
                        overflow: hidden;
                        text-overflow: ellipsis;
                        max-width: 145px;
                     }
                     .card-item-price {
                        font-weight: 700;
                        font-size: 14px;
                        color: #059669;
                        white-space: nowrap;
                     }
                     .card-item-meta {
                        display: flex;
                        flex-direction: column;
                        gap: 4px;
                        font-size: 12px;
                        color: #475569;
                        margin-bottom: 10px;
                     }
                     .card-item-meta .meta-line {
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                        gap: 6px;
                     }
                     .card-item-meta .meta-label {
                        color: #64748b;
                        font-size: 11px;
                     }
                     .card-item-meta .meta-val {
                        font-weight: 600;
                        color: #1e293b;
                        white-space: nowrap;
                        overflow: hidden;
                        text-overflow: ellipsis;
                        max-width: 130px;
                     }
                     .card-item-bottom {
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                        padding-top: 8px;
                        border-top: 1px dashed #e2e8f0;
                        font-size: 11px;
                     }
                     .stock-badge {
                        padding: 2px 7px;
                        border-radius: 4px;
                        font-weight: 600;
                        font-size: 11px;
                     }
                     .stock-badge.stock-in {
                        background: #dcfce7;
                        color: #15803d;
                     }
                     .stock-badge.stock-low {
                        background: #fee2e2;
                        color: #b91c1c;
                     }
                     .click-to-add-btn {
                        color: #2563eb;
                        font-weight: 600;
                        display: inline-flex;
                        align-items: center;
                        gap: 4px;
                        font-size: 11px;
                     }
                     .item-card-item:hover .click-to-add-btn {
                        color: #1d4ed8;
                        text-decoration: underline;
                     }
                  </style>

                  <div class="row">
                     <div class="col-sm-12">
                        <div class="item-card-block-wrapper">
                           <div class="item-card-block-header">
                              <div>
                                 <span class="block-badge"><i class="fa fa-cubes text-primary"></i> Available Items</span>
                                 <span class="block-subtitle">Click on any card to add into bill list</span>
                              </div>
                              <div>
                                 <span class="badge badge-counter" id="itemCountBadge">Loading items...</span>
                              </div>
                           </div>
                           <div class="item-cards-grid" id="itemCardsGrid">
                              <?php
                                 $cardsQuery = $db->select("
                                    SELECT 
                                       p.barcode_number,
                                       MAX(p.article_no) AS article_no,
                                       MAX(p.item_name) AS item_name,
                                       MAX(p.sp) AS sp,
                                       MAX(p.mrp) AS mrp,
                                       MAX(p.category) AS category,
                                       COALESCE((SELECT SUM(qty) FROM tbl_purchase WHERE barcode_number = p.barcode_number AND status = 'success'), 0)
                                       - COALESCE((SELECT SUM(qty) FROM tbl_bill WHERE barcode_number = p.barcode_number), 0) AS total_qty
                                    FROM tbl_purchase p
                                    WHERE p.status = 'success' AND p.barcode_number IS NOT NULL AND p.barcode_number != ''
                                    GROUP BY p.barcode_number
                                    ORDER BY MAX(p.id) DESC
                                 ");
                                 $totalCardCount = 0;
                                 if ($cardsQuery && $cardsQuery->num_rows() > 0) {
                                    while ($card = $cardsQuery->fetch_assoc()) {
                                       $totalCardCount++;
                                       $cBar = htmlspecialchars($card['barcode_number']);
                                       $cArt = htmlspecialchars($card['article_no'] ?? '');
                                       $cNam = htmlspecialchars($card['item_name']);
                                       $cSp  = (float)($card['sp'] ?? 0);
                                       $cQty = (int)($card['total_qty'] ?? 0);
                                       ?>
                                       <div class="item-card-item"
                                            data-barcode="<?= strtolower($cBar) ?>"
                                            data-article="<?= strtolower($cArt) ?>"
                                            data-name="<?= strtolower($cNam) ?>"
                                            onclick="selectItemCard('<?= $cBar ?>', this)"
                                            title="Click to add <?= $cNam ?> (Barcode: <?= $cBar ?>, Article: <?= $cArt ?>)">
                                          <div class="card-item-top">
                                             <span class="card-item-name" title="<?= $cNam ?>"><?= $cNam ?></span>
                                             <span class="card-item-price">₹<?= number_format($cSp, 2) ?></span>
                                          </div>
                                          <div class="card-item-meta">
                                             <div class="meta-line">
                                                <span class="meta-label"><i class="fa fa-barcode"></i> Barcode:</span>
                                                <span class="meta-val"><?= $cBar ?></span>
                                             </div>
                                             <div class="meta-line">
                                                <span class="meta-label"><i class="fa fa-tag"></i> Article No:</span>
                                                <span class="meta-val"><?= !empty($cArt) ? $cArt : '-' ?></span>
                                             </div>
                                          </div>
                                          <div class="card-item-bottom">
                                             <span class="stock-badge <?= $cQty > 0 ? 'stock-in' : 'stock-low' ?>">
                                                Stock: <?= $cQty ?>
                                             </span>
                                             <span class="click-to-add-btn">
                                                <i class="fa fa-plus-circle"></i> Add to List
                                             </span>
                                          </div>
                                       </div>
                                       <?php
                                    }
                                 } else {
                                    ?>
                                    <div class="text-center text-muted" style="padding: 16px; width: 100%;">
                                       <i class="fa fa-info-circle"></i> No items available in inventory.
                                    </div>
                                    <?php
                                 }
                              ?>
                           </div>
                           <div id="noMatchMessage" style="display:none; padding:15px; text-align:center; color:#64748b; font-size:13px; width:100%;">
                              <i class="fa fa-search"></i> No items matching search criteria.
                           </div>
                           <script>
                              document.addEventListener('DOMContentLoaded', function() {
                                 var b = document.getElementById('itemCountBadge');
                                 if (b) b.textContent = '<?= $totalCardCount ?> Items Available';
                               });
                           </script>
                        </div>
                     </div>
                  </div>
                  <!-- Plugin content:powerpoint,txt,pdf,png,word,xl -->
                  <div class="table-responsive ">
                     <table class="custom-table table table-bordered table-striped table-hover">
                        <thead>
                           <tr class="info">
                              <th style="width: 60px; min-width: 60px; text-align: center;"><input type="checkbox" class="all"></th>
                              <th style="width: 40px; min-width: 40px; text-align: center;">SrNo</th>
                              <th style="min-width: 120px;">Barcode No</th>
                              <th style="min-width: 110px;">Article No</th>
                              <th style="min-width: 160px;">Item Name</th>
                              <th style="min-width: 80px;">BBR Qty</th>
                              <th style="min-width: 80px;">Stock Qty</th>
                              <th style="min-width: 80px;">QTY</th>
                              <th style="min-width: 80px;">MOU</th>
                              <th style="min-width: 110px;">SP</th>
                              <th style="min-width: 110px;">Net Amt</th>
                              <th style="min-width: 95px;">GST Amt</th>
                              <th style="min-width: 110px;">Taxable Amt</th>
                              <th style="min-width: 110px;">Amount</th>
                           </tr>
                        </thead>
                        <tbody id="dataTable">
                           <?php
                              $query = $db->select("SELECT * FROM tbl_bill WHERE bill_number=?", 's', $bill_no);
                              $rowcount = $query->num_rows();
                              for ($i = 1; $i <= $rowcount; $i++) {
                                 $item = $query->fetch_assoc();

                                 $gst_amount     = $item['gst_amount'] ?? 0;
                                 $taxable_amount = $item['taxable_amount'] ?? 0;
                                 $amount         = $item['amount'] ?? 0;
                                 $net_price      = $item['net_price'] ?? 0;

                                 $group_val    = ($item['category'] ?? '') === 'null' ? '' : ($item['category'] ?? '');
                                 $subgroup_val = ($item['subgroup'] ?? '') === 'null' ? '' : ($item['subgroup'] ?? '');
                                 $brand_val    = ($item['brand'] ?? '') === 'null' ? '' : ($item['brand'] ?? '');
                                 $color_val    = ($item['color'] ?? '') === 'null' ? '' : ($item['color'] ?? '');
                                 $size_val     = ($item['size'] ?? '') === 'null' ? '' : ($item['size'] ?? '');
                                 $style_val    = ($item['style'] ?? '') === 'null' ? '' : ($item['style'] ?? '');

                                 $mrp        = $item['mrp'] ?? $item['purchase_price'];
                                 $disc_pct2  = $item['discount_percent2'] ?? 0;
                                 $disc_amt2  = $item['discount_amount2'] ?? 0;
                                 $sp         = $item['selling_price'] ?? $item['purchase_price'];
                           ?>
                           <tr id="row_<?= $i ?>" data-barcode="<?= htmlspecialchars($item['barcode_number']) ?>">

                              <!-- col1: checkbox -->
                              <td>
                                 <input type="hidden" name="item_id[]" value="<?= $item['id'] ?>" />
                                 <div class="checkbox checkbox-info">
                                    <input id="chk_<?= $i ?>" type="checkbox" name="chk[]" class="chk-box" value="<?= $item['id'] ?>"/>
                                    <button type="button" class="row-delete-btn" onclick="deleteTableRow(this)" title="Delete Row" style="background:none;border:none;color:#e53935;cursor:pointer;padding:2px 5px;font-size:14px;"><i class="fa fa-trash"></i></button>
                                 </div>
                              </td>

                              <!-- col2: SrNo -->
                              <td><label class="s_no" for="chk_<?= $i ?>"><?= $i ?></label></td>

                              <!-- col3: Barcode No -->
                              <td><input type="text" class="form-control" value="<?= htmlspecialchars($item['barcode_number']) ?>" name="barcode_no[]" required readonly></td>

                              <!-- col4: Article No -->
                              <td><input type="text" class="form-control" value="<?= htmlspecialchars($item['article_no'] ?? '') ?>" name="article_no[]" required readonly></td>

                              <!-- col5: item Name -->
                              <td>
                                 <div style="display:flex;gap:6px;align-items:center;">
                                    <input type="text" class="form-control" value="<?= htmlspecialchars($item['item_name']) ?>" name="item_name[]" required readonly>
                                    <input type="hidden" value="<?= htmlspecialchars($item['item_type']) ?>" name="item_type[]">
                                    <input type="hidden" class="row-group" name="group_name[]" value="<?= htmlspecialchars($group_val) ?>">
                                    <input type="hidden" class="row-subgroup" name="subgroup[]" value="<?= htmlspecialchars($subgroup_val) ?>">
                                    <input type="hidden" class="row-brand" name="brand[]" value="<?= htmlspecialchars($brand_val) ?>">
                                    <input type="hidden" class="row-color" name="color[]" value="<?= htmlspecialchars($color_val) ?>">
                                    <input type="hidden" class="row-size" name="size[]" value="<?= htmlspecialchars($size_val) ?>">
                                    <input type="hidden" class="row-style" name="style[]" value="<?= htmlspecialchars($style_val) ?>">
                                    <button type="button" class="plus-btn"
                                          data-id="<?= $item['id'] ?>"
                                          data-group="<?= htmlspecialchars($group_val) ?>"
                                          data-subgroup="<?= htmlspecialchars($subgroup_val) ?>"
                                          data-brand="<?= htmlspecialchars($brand_val) ?>"
                                          data-color="<?= htmlspecialchars($color_val) ?>"
                                          data-size="<?= htmlspecialchars($size_val) ?>"
                                          data-style="<?= htmlspecialchars($style_val) ?>"
                                          onclick="openCategoryPopup(this)" title="Add Category Details" style="margin-left:6px;">
                                       <i class="fa fa-plus"></i>
                                    </button>
                                 </div>
                              </td>

                              <!-- col6: BBR Qty -->
                              <td><input type="number" value="0" class="form-control" name="bbr_qty[]" required readonly></td>

                              <!-- col7: stock Qty -->
                              <td><input type="number" value="<?= $item['stock_qty'] ?? 0 ?>" class="form-control" id="stock_id_<?= $item['id'] ?>" name="stock_qty_display[]" required readonly></td>

                              <!-- col8: QTY (qty visible, stock_qty + balance_qty hidden for calc) -->
                              <td>
                                 <input type="hidden" class="form-control stock_qty" value="<?= $item['stock_qty'] ?? 0 ?>" name="stock_qty[]" required readonly>
                                 <input type="number" min="1" value="<?= $item['qty'] ?>" class="form-control qtyval qty_array" name="qty[]" oninput="validateQty(this); updateBillType()" required>
                                 <input type="hidden" class="form-control bal_qty" value="<?= $item['balance_qty'] ?? 0 ?>" id="balance_id_<?= $item['id'] ?>" name="balance_qty[]" required readonly>
                              </td>

                              <!-- col9: MOU -->
                              <td><input type="text" value="<?= htmlspecialchars($item['mou_name'] ?? '') ?>" class="form-control" name="mou_name[]" required readonly></td>

                              <!-- col10: SP -->
                              <td>
                                 <div style="display:flex;gap:6px;align-items:center;">
                                    <input type="hidden" price="<?= $item['purchase_price'] ?>" value="<?= $item['purchase_price'] ?>" class="form-control pprice valid" name="purchase_price[]">
                                    <input type="hidden" class="mrp" name="mrp[]" value="<?= $mrp ?>">
                                    <input type="hidden" class="discount_percent2" name="discount_percent2[]" value="<?= $disc_pct2 ?>">
                                    <input type="hidden" class="discount_amount2" name="discount_amount2[]" value="<?= $disc_amt2 ?>">
                                    <input type="text" value="<?= $sp ?>" class="form-control selling_price" id="selling_price_<?= $item['id'] ?>" name="selling_price[]" readonly>
                                    <button type="button" class="plus-btn" onclick="openPopupsp(this)" title="Edit MRP" style="margin-left:6px;"><i class="fa fa-plus"></i></button>
                                 </div>
                              </td>

                              <!-- col11: Net Amt -->
                              <td><input type="text" value="<?= $net_price ?>" class="form-control net_array" id="net_price_<?= $item['id'] ?>" name="net_price[]" required readonly></td>

                              <!-- col12: GST Amt -->
                              <td>
                                 <input type="hidden" class="form-control gst_array" id="gst_<?= $item['id'] ?>" value="<?= $item['gst'] ?? '' ?>" name="gst[]" readonly data-toggle="tooltip">
                                 <input type="text" value="<?= $gst_amount ?>" class="form-control gstval" id="gst_amount_<?= $item['id'] ?>" name="gst_amount[]" required readonly>
                              </td>

                              <!-- col13: Taxable Amt -->
                              <td><input type="text" value="<?= $taxable_amount ?>" class="form-control taxable_amount" id="taxable_amount<?= $item['id'] ?>" name="taxable_amount[]" required readonly></td>

                              <!-- col14: Amount -->
                              <td><input type="text" value="<?= $amount ?>" class="form-control amount_array" id="amount<?= $item['id'] ?>" name="amount[]" required readonly></td>

                           </tr>
                           <?php } ?>
                        </tbody>
                        <tfoot>
                           <tr class="success">
                              <td colspan="4"></td>
                              <td colspan="3">
                                 <label>Grand Total</label>
                              </td>
                              <td id="quantity_val">00.00</td>
                              <td colspan="2">&nbsp;</td>
                              <td id="total_net_amount2">00.00</td>
                              <td id="total_gst_amount2">00.00</td>
                              <td>&nbsp;</td>
                              <td>
                                 <div id="sub_total_amount">00.00</div>
                                 <hr>
                                 <div id="total_amount2">00.00</div>
                              </td>
                           </tr>
                        </tfoot>
                     </table>
                  </div>
                   <div class="row">
                      <div class="col-sm-4">
                         <div class="form-group">
                            <label>Invoice Pdf upload</label>
                            <input type="file" name="picture" id="picture">
                         </div>
                      </div>
                      <div class="col-sm-4"></div>
                      <div class="col-sm-2">
                         <div class="form-group">
                            <label>Discount %</label>
                            <input type="text" class="form-control" name="percent_total_discount" id="percent_total_discount" placeholder="Enter Discount %" oninput="discountTotalPercent()" value="<?= $percent_total_discount ?? '' ?>">
                         </div>
                      </div>
                      <div class="col-sm-2">
                         <div class="form-group">
                            <label>Discount amount</label>
                            <input type="text" class="form-control" name="discount_total_amount" id="discount_total_amount" placeholder="Enter Discount amount" oninput="discountTotalAmount()" value="<?= $discount_total_amount ?? '' ?>">
                         </div>
                      </div>
                   </div>
                   <div class="row">
                   <!-- Modern Responsive Action Button Bar -->
                   <style>
                      .sale-action-bar {
                         display: flex;
                         align-items: center;
                         justify-content: space-between;
                         flex-wrap: wrap;
                         gap: 16px;
                         background: #f8fafc;
                         padding: 16px 20px;
                         border-radius: 16px;
                         border: 1px solid #e2e8f0;
                         margin-top: 15px;
                      }
                      .action-group-buttons {
                         display: flex;
                         align-items: center;
                         gap: 10px;
                         flex-wrap: wrap;
                      }
                      .btn-action-pill {
                         border-radius: 50px;
                         font-size: 14px;
                         font-weight: 600;
                         height: 42px;
                         padding: 0 20px;
                         display: inline-flex;
                         align-items: center;
                         justify-content: center;
                         gap: 8px;
                         border: none;
                         transition: all 0.25s ease;
                         cursor: pointer;
                         box-shadow: 0 2px 6px rgba(0,0,0,0.06);
                      }
                      .btn-action-pill:hover {
                         transform: translateY(-2px);
                         box-shadow: 0 4px 12px rgba(0,0,0,0.12);
                      }
                      .btn-action-clear {
                         background: #ffb300;
                         color: #000;
                      }
                      .btn-action-clear:hover {
                         background: #ffa000;
                         color: #000;
                      }
                      .btn-action-remove {
                         background: #e53935;
                         color: #fff;
                      }
                      .btn-action-remove:hover {
                         background: #d32f2f;
                         color: #fff;
                      }
                      .action-option-box {
                         display: flex;
                         flex-direction: column;
                         gap: 4px;
                      }
                      .action-option-box label.option-title {
                         font-size: 12px;
                         font-weight: 700;
                         text-transform: uppercase;
                         letter-spacing: 0.5px;
                         color: #64748b;
                         margin-bottom: 2px;
                      }
                      .action-option-radios {
                         display: flex;
                         align-items: center;
                         gap: 12px;
                         font-size: 13px;
                         font-weight: 600;
                         color: #334155;
                      }
                      .btn-submit-proceed {
                         height: 46px;
                         padding: 0 32px;
                         border-radius: 50px;
                         background: linear-gradient(135deg, #1e293b, #37475a);
                         color: #ffffff;
                         font-size: 16px;
                         font-weight: 700;
                         border: none;
                         cursor: pointer;
                         display: inline-flex;
                         align-items: center;
                         justify-content: center;
                         gap: 10px;
                         box-shadow: 0 4px 14px rgba(30,41,59,0.3);
                         transition: all 0.25s ease;
                      }
                      .btn-submit-proceed:hover {
                         background: linear-gradient(135deg, #0f172a, #1e293b);
                         transform: translateY(-2px);
                         box-shadow: 0 6px 18px rgba(15,23,42,0.4);
                         color: #ffffff;
                      }
                   </style>
                    <div class="sale-action-bar">
                       <!-- Reset & Row Removal -->
                       <div class="action-group-buttons">
                          <button type="reset" class="btn-action-pill btn-action-clear">
                             <i class="fa fa-undo"></i> Clear
                          </button>
                          <button type="button" class="btn-action-pill btn-action-remove" onclick="deleteRow('dataTable')">
                             <i class="fa fa-trash"></i> Row Remove
                          </button>
                       </div>

                       <!-- Status Radio Group -->
                       <div class="action-option-box">
                          <label class="option-title">Status</label>
                          <div class="action-option-radios">
                             <label class="radio-inline"><input type="radio" name="status" value="success" <?= ($status ?? 'success') == 'success' ? 'checked="checked"' : '' ?>> Success</label>
                             <label class="radio-inline"><input type="radio" name="status" value="hold" <?= ($status ?? '') == 'hold' ? 'checked="checked"' : '' ?>> Hold</label>
                          </div>
                       </div>

                       <!-- Receipt Print Option Radio Group -->
                       <div class="action-option-box">
                          <label class="option-title">Receipt Print Option</label>
                          <div class="action-option-radios">
                             <label class="radio-inline"><input type="radio" name="print_option" value="80mm" checked="checked"> 80 MM</label>
                             <label class="radio-inline"><input type="radio" name="print_option" value="a4"> A4</label>
                             <label class="radio-inline"><input type="radio" name="print_option" value="a5"> A5</label>
                          </div>
                       </div>

                       <!-- Primary Action Submit Button -->
                       <div>
                          <input type="hidden" name="submit" value="<?= (!empty($bill_no) ? 'update' : 'save') ?>">
                          <button type="button" class="btn-submit-proceed" onclick="openPopupsave()" id="confirmbtn">
                             <i class="fa <?= (!empty($bill_no) ? 'fa-save' : 'fa-paper-plane') ?>"></i>
                             <?= (!empty($bill_no) ? 'Update Bill' : 'Bill Proceed') ?>
                          </button>
                       </div>
                    </div>
                   <br>
                   </div>
                  
                  <!-- Hidden field for Save and Print option -->
                  <input type="hidden" name="is_print" id="is_print_hidden" value="0">
                  
                  <!-- Hidden fields to capture popup payment data -->
                  <input type="hidden" name="popup_ac_code" id="popup_ac_code_hidden">
                  <input type="hidden" name="popup_account_name" id="popup_account_name_hidden">
                  <input type="hidden" name="popup_mobile_number" id="popup_mobile_number_hidden">
                  <input type="hidden" name="popup_pay_mode" id="popup_pay_mode_hidden">
                  <input type="hidden" name="popup_pay_cash" id="popup_pay_cash_hidden">
                  <input type="hidden" name="popup_pay_upi" id="popup_pay_upi_hidden">
                  <input type="hidden" name="popup_pay_card" id="popup_pay_card_hidden">
                  <input type="hidden" name="popup_pay_credit" id="popup_pay_credit_hidden">
                  
                  <!-- Hidden fields to capture Additional Data sidebar fields -->
                  <input type="hidden" name="sidebar_purchage_date" id="sidebar_purchage_date_hidden">
                  <input type="hidden" name="sidebar_bill_number" id="sidebar_bill_number_hidden">
                  <input type="hidden" name="sidebar_s_code" id="sidebar_s_code_hidden">
                  <input type="hidden" name="sidebar_store_name" id="sidebar_store_name_hidden">
                  <input type="hidden" name="sidebar_location" id="sidebar_location_hidden">
                  <input type="hidden" name="sidebar_gst_type" id="sidebar_gst_type_hidden">
                  <input type="hidden" name="sidebar_under_gst" id="sidebar_under_gst_hidden">
                  <input type="hidden" name="sidebar_bill_type" id="sidebar_bill_type_hidden">
                  <input type="hidden" name="sidebar_packing_amount" id="sidebar_packing_amount_hidden">
                  <input type="hidden" name="sidebar_delivery_amount" id="sidebar_delivery_amount_hidden">
                  <input type="hidden" name="sidebar_other_amount" id="sidebar_other_amount_hidden">
                  <input type="hidden" name="sidebar_labour_amount" id="sidebar_labour_amount_hidden">
                  <input type="hidden" name="sidebar_discount_total_amount" id="sidebar_discount_total_amount_hidden">
                  <input type="hidden" name="sidebar_remarks" id="sidebar_remarks_hidden">
               </form>
            </div>
         </div>
      </div>
   </div>
</section>
<!-- Main content -->
<section class="content">
   <div class="row">
      <div class="col-sm-12">
         <div class="panel panel-bd lobidrag">
            <div class="panel-heading sidebar-toggle" data-toggle="offcanvas">
               <div class="btn-group" id="buttonexport">
                  <a href="#">
                     <h5>Recently Sales/Bills</h5>
                  </a>
               </div>
            </div>
            <div class="panel-body">
               <div class="row" style="margin-bottom:15px;">
                  <div class="col-sm-6 col-xs-12">
                     <div class="btn-group pull-left" style="margin-bottom:10px;">
                        <a href="sale-itemwise-list.php" class="btn btn-exp btn-sm" style="background-color:#37475a; color:#fff; border-radius: 50px 0 0 50px;"><i class="fa fa-list"></i><span style="margin-left:5px;">Sale ItemWise List</span></a>
                        <a href="sale-billwise-list.php" class="btn btn-exp btn-sm" style="background-color:#37475a; color:#fff; margin-left:-2px;"><i class="fa fa-file-text-o"></i><span style="margin-left:5px;">Sale BillWise List</span></a>
                        <div class="btn-group">
                           <button class="btn btn-exp btn-sm dropdown-toggle" data-toggle="dropdown" style="background-color:#37475a; color: #fff; border-radius: 0 50px 50px 0;"><i class="fa fa-download"></i> Export Table Data</button>
                           <ul class="dropdown-menu exp-drop" role="menu">
                              <li>
                                 <a role="button" onclick="exportExcel(this, 'excel');">
                                 <img src="assets/dist/img/xls.png" width="24" alt="logo"> Export To Excel !</a>
                              </li>
                              <li>
                                 <a role="button" onclick="exportExcel(this, 'pdf');">
                                 <img src="assets/dist/img/pdf.png" width="24" alt="logo"><span style="margin-left:5px;">Export To PDF !</span></a>
                              </li>
                           </ul>
                        </div>
                     </div>
                  </div>
                  <div class="col-sm-6 col-xs-12">
                     <!-- Search Form Box matching purchase-master-creation.php -->
                     <form name="searchForm" id="searchForm" method="post" class="search-form-wrap pull-right" action="" autocomplete="off" style="width: 100%; max-width: 360px;">
                        <input type="hidden" name="export" value="" />
                        <input type="hidden" name="page" value="1" />
                        <input type="hidden" name="sortOrder" value="DESC" />
                        <input type="hidden" name="sortField" value="id" />
                        <div class="input-group search-box-group">
                           <input type="text" class="form-control search-input search-input-field" name="search" id="company_name" placeholder="Search Recent Sales..." autocomplete="off">
                           <span class="input-group-addon search-addon-btn" id="saleSearchBtn" onclick="doSaleSearch()" style="cursor: pointer;" title="Click to search">
                              <i class="fa fa-search search-icon-sm"></i>
                           </span>
                        </div>
                     </form>
                     <div class="clearfix"></div>
                  </div>
               </div>
               <!-- Plugin content:powerpoint,txt,pdf,png,word,xl -->
               <div class="table-responsive ">
                  <table id="dataTableEnable" class="table table-bordered table-striped table-hover">
                     <thead>
                        <tr class="info">
                           <th><input type="checkbox" class="all"></th>
                           <th>SrNo</th>
                           <th>Date</th>
                           <th>Bill No</th>
                           <th>Account Name</th>
                           <th>PayMode</th>
                           <th>GST Type</th>
                           <th>GST Amt</th>
                           <th>Bill Amt</th>
                           <th>Status</th>
                           <th>Details</th>
                           <th>Action</th>
                        </tr>
                     </thead>
                     <tbody id="latestRecord">
                     </tbody>
                  </table>
               </div>
               <br>
               <div class="row">
                  <div class="col-sm-8">
                     <div id="pagination-result">
                     </div>
                  </div>
                  <style>
                     .dwc {
                        font-size: 13px;
                        height: 38px;
                        width: auto;
                        padding: 0 18px;
                        font-weight: 600;
                        border-radius: 50px;
                        display: inline-flex;
                        align-items: center;
                        justify-content: center;
                        gap: 6px;
                        box-shadow: 0 2px 6px rgba(229,57,53,0.2);
                        transition: all 0.2s ease;
                     }
                     .dwc:hover {
                        transform: translateY(-1px);
                        box-shadow: 0 4px 10px rgba(229,57,53,0.3);
                     }
                  </style>
                  <div class="col-sm-4" style="text-align:right;">
                     <button class="btn btn-danger btn-sm ppbilllist dwc" onclick="deleteRow('latestRecord')">
                        <i class="fa fa-trash"></i> Delete Selected
                     </button>
                  </div>
               </div>
               <br>
            </div>
         </div>
      </div>
   </div>
</section>
<form action="" id="deleteform" method="post" autocomplete="off">
   <input type="hidden" name="action" value="delete">
   <input type="hidden" name="id" id="deleteid">
</form>

<?php include('include/footer-2.php'); ?>
<script>
   var DEFAULT_PAYMENT_MODE = <?= json_encode($active_pay_mode ?? $default_pay_mode ?? 'CASH') ?>;
</script>
<script src="assets/dist/js/page/sale-creation.js?v=<?= time(); ?>" type="text/javascript"></script>
<script>
   const filterBtn = document.getElementById("filterToggle");
   const closeBtn = document.getElementById("closePanel");
   
   const leftPanel = document.getElementById("leftPanel");
   const overlay = document.getElementById("filterOverlay");
   
   /* OPEN */
   filterBtn.onclick = () => {
   leftPanel.classList.add("active");
   overlay.classList.add("active");
   }
   
   /* CLOSE */
   closeBtn.onclick = () => {
   leftPanel.classList.remove("active");
   overlay.classList.remove("active");
   }
   
   /* OVERLAY CLOSE */
   overlay.onclick = () => {
   leftPanel.classList.remove("active");
   overlay.classList.remove("active");
   }
</script>

<?php 
$print_bill_no = '';
$print_size = '80mm';
if (!empty($_SESSION['print_bill_no'])) {
    $print_bill_no = $_SESSION['print_bill_no'];
    $print_size = $_SESSION['print_option'] ?? '80mm';
    unset($_SESSION['print_bill_no'], $_SESSION['print_option']);
} else if (!empty($_GET['print_bill'])) {
    $print_bill_no = $_GET['print_bill'];
    $print_size = $_GET['print_option'] ?? ($_GET['size'] ?? '80mm');
} else if (isset($_GET['print']) && $_GET['print'] == '1' && !empty($bill_no)) {
    $print_bill_no = $bill_no;
    $print_size = $_GET['print_option'] ?? ($_GET['size'] ?? '80mm');
}

if (!empty($print_bill_no)): 
?>
<iframe id="print_frame" src="bill-invoice?type=sale&bill_no=<?= urlencode($print_bill_no) ?>&size=<?= urlencode($print_size) ?>" style="position:fixed;right:100%;bottom:100%;width:900px;height:900px;border:0;opacity:0;pointer-events:none;"></iframe>
<script>
window.addEventListener('DOMContentLoaded', function() {
    // Strip print parameters from URL so manual F5/refresh won't re-trigger print
    if (window.history && window.history.replaceState) {
        var url = new URL(window.location.href);
        if (url.searchParams.has('print') || url.searchParams.has('print_bill')) {
            url.searchParams.delete('print');
            url.searchParams.delete('print_bill');
            url.searchParams.delete('size');
            url.searchParams.delete('print_option');
            var newSearch = url.searchParams.toString();
            window.history.replaceState({}, document.title, url.pathname + (newSearch ? '?' + newSearch : ''));
        }
    }

    var iframe = document.getElementById('print_frame');
    if (iframe) {
        iframe.onload = function() {
            setTimeout(function() {
                try {
                    iframe.contentWindow.focus();
                    iframe.contentWindow.print();
                } catch(e) {
                    console.error('Print error:', e);
                }
            }, 350);
        };
    }
});
</script>
<?php endif; ?>


