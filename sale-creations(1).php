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

   $purchage_date          = dateInSQLFormat($_POST['sidebar_purchage_date']);
   $bill_number            = $_POST['sidebar_bill_number'];
   $gst_type               = $_POST['sidebar_gst_type'];
   $under_gst               = $_POST['sidebar_under_gst'];
   $bill_type               = $_POST['sidebar_bill_type'];
   $mobile_number           = $_POST['mobile_number'];
   $account_name            = $_POST['account_name'];

   // Item data
   $item_name               = $_POST['item_name'];
   $barcode_no              = $_POST['barcode_no'];
   $item_type               = $_POST['item_type'];
   $category                = $_POST['group_name'];
   $brand                   = $_POST['brand'];
   $color                   = $_POST['color'];
   $size                    = $_POST['size'];
   $stock_qty               = $_POST['stock_qty'];
   $qty                     = $_POST['qty'];
   $balance_qty             = $_POST['balance_qty'];
   $mou_name                = $_POST['mou_name'];
   $rate                    = $_POST['mrp'];
   $purchase_price          = $_POST['purchase_price'];
   $percent_discount        = $_POST['discount_percent2'];
   $discount_amt            = $_POST['discount_amount2'];
   $net_price               = $_POST['net_price'];
   $gst                     = $_POST['gst'];
   $gst_amount              = $_POST['gst_amount'];
   $taxable_amount          = $_POST['taxable_amount'];
   $amount                  = $_POST['amount'];
   $packing_amount          = $_POST['sidebar_packing_amount'];
   $delivery_amount         = $_POST['sidebar_delivery_amount'];
   $other_amount            = $_POST['sidebar_other_amount'];
   $labour_amount           = $_POST['sidebar_labour_amount'];
   $percent_total_discount  = $_POST['percent_total_discount'];
   $discount_total_amount   = $_POST['sidebar_discount_total_amount'];
   $pay_mode                = $_POST['pay_mode'] ?? '';
   $remarks                 = $_POST['sidebar_remarks'];
   $status                  = $_POST['status'];

   // Additional Data sidebar fields (store-specific details)
   $s_code                  = $_POST['sidebar_s_code'] ?? '';
   $store_name              = $_POST['sidebar_store_name'] ?? '';
   $location                = $_POST['sidebar_location'] ?? '';

   // Popup payment data
   $popup_ac_code           = $_POST['popup_ac_code'] ?? '';
   $popup_account_name      = $_POST['popup_account_name'] ?? '';
   $popup_mobile_number     = $_POST['popup_mobile_number'] ?? '';
   $popup_pay_mode          = $_POST['popup_pay_mode'] ?? $pay_mode;
   $popup_pay_cash          = $_POST['popup_pay_cash'] ?? '';
   $popup_pay_upi           = $_POST['popup_pay_upi'] ?? '';
   $popup_pay_card          = $_POST['popup_pay_card'] ?? '';
   $popup_pay_credit        = $_POST['popup_pay_credit'] ?? '';

   // item_id[] only ever contains a REAL tbl_bill.id for rows that already
   // existed in this bill when the page was loaded. Rows appended via AJAX
   // (new barcode scans) always submit item_id[] = '' — never trust any
   // other value here as a tbl_bill id.
   $item_id = $_POST['item_id'] ?? [];

   $current_date = date("Y-m-d H:i:s");

   if ($_POST['submit'] == 'save') {

      $allSuccess = true;

      foreach ($barcode_no as $k => $barcode) {

         //get GST value
         $othergstvalue = getGSTValueBygstType($gst[$k]);
         $cgst = $othergstvalue;
         $sgst = $othergstvalue;

         $result = $db->insert(
            'INSERT INTO tbl_bill (purchage_date, bill_number, gst_type, under_gst, bill_type, item_name, barcode_number, item_type, category, brand, color, size, stock_qty, qty, balance_qty, mou_name, rate, purchase_price, percent_discount, discount_amt, net_price, gst, cgst, sgst, gst_amount, taxable_amount, amount, packing_amount, delivery_amount, other_amount, labour_amount, percent_total_discount, discount_total_amount, remarks, status, s_code, store_name, location, ac_code, account_name, mobile_number, pay_mode, pay_cash, pay_upi, pay_card, pay_credit, created_date) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)',
            'sssssssssssssssssssssssssssssssssssssssssssssss',
            $purchage_date, $bill_number, $gst_type, $under_gst, $bill_type, $item_name[$k], $barcode, $item_type[$k], $category[$k], $brand[$k], $color[$k], $size[$k], $stock_qty[$k], $qty[$k], $balance_qty[$k], $mou_name[$k], $rate[$k], $purchase_price[$k], $percent_discount[$k], $discount_amt[$k], $net_price[$k], $gst[$k], $cgst, $sgst, $gst_amount[$k], $taxable_amount[$k], $amount[$k], $packing_amount, $delivery_amount, $other_amount, $labour_amount, $percent_total_discount, $discount_total_amount, $remarks, $status, $s_code, $store_name, $location, $popup_ac_code, $popup_account_name, $popup_mobile_number, $popup_pay_mode, $popup_pay_cash, $popup_pay_upi, $popup_pay_card, $popup_pay_credit, $current_date
         );

         if (!$result) {
            $allSuccess = false;
         }
      }

      if ($allSuccess) {
         $msg = 'Record added successfully';
         $return = '';
      } else {
         $msg = 'Failed!';
         $return = 1;
      }

      redirectTo('sale-creations.php', $msg, $return);

   } else if ($_POST['submit'] == 'update') {

      $allSuccess = true;

      foreach ($barcode_no as $k => $barcode) {

         //get GST value
         $othergstvalue = getGSTValueBygstType($gst[$k]);
         $cgst = $othergstvalue;
         $sgst = $othergstvalue;

         // Only treat as an existing tbl_bill row if item_id[$k] is a real,
         // non-empty value. Newly-added rows always come through as ''.
         $isExist = (!empty($item_id[$k])) ? $item_id[$k] : null;

         if ($isExist) {
            $result = $db->update(
               "UPDATE tbl_bill SET purchage_date=?, gst_type=?, under_gst=?, bill_type=?, item_name=?, barcode_number=?, item_type=?, category=?, brand=?, color=?, size=?, stock_qty=?, qty=?, balance_qty=?, mou_name=?, rate=?, purchase_price=?, percent_discount=?, discount_amt=?, net_price=?, gst=?, cgst=?, sgst=?, gst_amount=?, taxable_amount=?, amount=?, packing_amount=?, delivery_amount=?, other_amount=?, labour_amount=?, percent_total_discount=?, discount_total_amount=?, remarks=?, status=?, s_code=?, store_name=?, location=?, ac_code=?, account_name=?, mobile_number=?, pay_mode=?, pay_cash=?, pay_upi=?, pay_card=?, pay_credit=?, updated_date=? WHERE id=?",
               'ssssssssssssssssssssssssssssssssssssssssssssssi',
               $purchage_date, $gst_type, $under_gst, $bill_type, $item_name[$k], $barcode, $item_type[$k], $category[$k], $brand[$k], $color[$k], $size[$k], $stock_qty[$k], $qty[$k], $balance_qty[$k], $mou_name[$k], $rate[$k], $purchase_price[$k], $percent_discount[$k], $discount_amt[$k], $net_price[$k], $gst[$k], $cgst, $sgst, $gst_amount[$k], $taxable_amount[$k], $amount[$k], $packing_amount, $delivery_amount, $other_amount, $labour_amount, $percent_total_discount, $discount_total_amount, $remarks, $status, $s_code, $store_name, $location, $popup_ac_code, $popup_account_name, $popup_mobile_number, $popup_pay_mode, $popup_pay_cash, $popup_pay_upi, $popup_pay_card, $popup_pay_credit, $current_date, $item_id[$k]
            );
         } else {
            $result = $db->insert(
               'INSERT INTO tbl_bill (purchage_date, bill_number, gst_type, under_gst, bill_type, item_name, barcode_number, item_type, category, brand, color, size, stock_qty, qty, balance_qty, mou_name, rate, purchase_price, percent_discount, discount_amt, net_price, gst, cgst, sgst, gst_amount, taxable_amount, amount, packing_amount, delivery_amount, other_amount, labour_amount, percent_total_discount, discount_total_amount, remarks, status, s_code, store_name, location, ac_code, account_name, mobile_number, pay_mode, pay_cash, pay_upi, pay_card, pay_credit, created_date) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)',
               'sssssssssssssssssssssssssssssssssssssssssssssss',
               $purchage_date, $bill_number, $gst_type, $under_gst, $bill_type, $item_name[$k], $barcode, $item_type[$k], $category[$k], $brand[$k], $color[$k], $size[$k], $stock_qty[$k], $qty[$k], $balance_qty[$k], $mou_name[$k], $rate[$k], $purchase_price[$k], $percent_discount[$k], $discount_amt[$k], $net_price[$k], $gst[$k], $cgst, $sgst, $gst_amount[$k], $taxable_amount[$k], $amount[$k], $packing_amount, $delivery_amount, $other_amount, $labour_amount, $percent_total_discount, $discount_total_amount, $remarks, $status, $s_code, $store_name, $location, $popup_ac_code, $popup_account_name, $popup_mobile_number, $popup_pay_mode, $popup_pay_cash, $popup_pay_upi, $popup_pay_card, $popup_pay_credit, $current_date
            );
         }

         if (!$result) {
            $allSuccess = false;
         }
      }

      if ($allSuccess) {
         $msg = 'Record Updated successfully';
         $return = '';
      } else {
         $msg = 'Failed!';
         $return = 1;
      }

      redirectTo('sale-creations.php?bill_no=' . $bill_no, $msg, $return);
   }
}
   
   
   if ($_POST['action'] == 'delete' && $_POST['id'] != '') {
   
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
   } else if ($_POST['action'] == 'send-mail' && $_POST['id'] != '') {
   
      $sendid = '';
      if ($bill_no) {
         $sendid = '?bill_no=' . $bill_no;
      }
      $bill_number = $_POST['id'];
      $filename = generatePDF($bill_number);
   
      $mail_to = $_POST['account_email'];
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
   
      $res = $pur_stmt->fetch_assoc();
   
      foreach ($res as $key => $value) {
         $$key = $validationHelper->filterText($value);
      }
      $pur_stmt->close();
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
<link rel="stylesheet"
   href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
   .row-delete-btn:hover { color: #b71c1c !important; transform: scale(1.2); }
   .popup-ac-suggestion { padding: 8px 12px; cursor: pointer; border-bottom: 1px solid #f0f0f0; }
   .popup-ac-suggestion:hover { background: #f0faf9; color: #009688; }
</style>
<!--end table delete icon-->
<!-- POPUP -->
<div class="modern-popup-save" id="modernPopup-save">
   <div class="popup-box">
      <button class="close-popup" onclick="closePopupsave()"><i class="fa-solid fa-xmark"></i></button>
      <h3 class="popup-title" style="text-align:center;">Payment mode details</h3>
      <div id="popup_error_msg" style="display:none;background:#fdecea;color:#e53935;padding:10px 14px;border-radius:10px;margin-bottom:14px;font-size:13px;">
         <span id="popup_error_text"></span>
      </div>
      <div class="row">
         <div class="col-md-6"><div class="form-group-modern" style="position:relative;">
            <label>A/C Code</label>
            <input type="text" name="ac_code" class="popup-input" placeholder="Enter your A/C Code" oninput="getPopupAccountDetails(this.value,'ac_code')">
            <div id="popup_ac_suggestions" class="autocomplete-suggestions" style="display:none;position:absolute;background:#fff;border:1px solid #eee;z-index:10;width:100%;"></div>
         </div></div>
         <div class="col-md-6"><div class="form-group-modern">
            <label>Mobile No</label>
            <input type="text" name="mobile_number" class="popup-input" placeholder="Enter your Mobile No" oninput="getPopupAccountDetails(this.value,'mobile')">
         </div></div>
      </div>
      <div class="form-group-modern">
         <label>A/C Name</label>
         <input type="text" name="account_name" class="popup-input" placeholder="Enter your A/C Name" oninput="getPopupAccountDetails(this.value,'account_name')">
      </div>
      <div class="form-group-modern" style="text-align: center;">
         <label>Your Bill Amount</label><br>
         <span id="popup_bill_amount">₹0.00</span>
      </div>
      <div class="row">
         <div class="col-md-12">
         <div class="form-group-modern">
         <label class="popup-text">Payment Mode</label>
         <select name="pay_mode" id="payment_mode_select" class="popup-select" onchange="handlePaymentMode(this.value)">
            <option value="">Select Option</option>
            <option value="CASH">CASH</option>
            <option value="UPI">UPI</option>
            <option value="CARD">CARD</option>
            <option value="CREDIT">CREDIT</option>
            <option value="DUAL">DUAL</option>
         </select>
      </div>
      </div>
   </div>
      <div class="row">
         <div class="col-md-6"><div class="form-group-modern">
            <label>CASH</label>
            <input type="text" id="pay_cash" name="pay_cash" class="popup-input" placeholder="Enter CASH amount" disabled>
         </div></div>
         <div class="col-md-6"><div class="form-group-modern">
            <label>UPI</label>
            <input type="text" id="pay_upi" name="pay_upi" class="popup-input" placeholder="Enter UPI amount" disabled>
         </div></div>
      </div>
      <div class="row">
         <div class="col-md-6"><div class="form-group-modern">
            <label>CARD</label>
            <input type="text" id="pay_card" name="pay_card" class="popup-input" placeholder="Enter CARD amount" disabled>
         </div></div>
         <div class="col-md-6"><div class="form-group-modern">
            <label>CREDIT</label>
            <input type="text" id="pay_credit" name="pay_credit" class="popup-input" placeholder="Enter CREDIT amount" disabled>
         </div></div>
      </div>
      <div class="popup-actions">
         <button class="popup-btn cancel-btn" onclick="closePopupsave()">Cancel</button>
         <button type="button" class="popup-btn save-btn" onclick="validateAndSubmitPopup()">Save</button>
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
      <button class="close-popup" onclick="closePopup()"><i class="fa-solid fa-xmark"></i></button>
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
      <button class="close-popup" onclick="closePopupsp()"><i class="fa-solid fa-xmark"></i></button>
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
                              <?php foreach (['Tax Invoice', 'Estimate', 'Cash Memo'] as $opt): ?>
                                 <?php 
                                    // If $bill_type exists, use its value; otherwise default to 'Local'
                                    $current = $bill_type ?? 'Tax Invoice'; 
                                 ?>
                                 <option value="<?= $opt ?>" <?= ($current == $opt) ? 'selected' : '' ?>><?= $opt ?></option>
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
                  <div class="row">
                     <div class="col-sm-2">
                        <div class="form-group">
                           <label>Barcode No</label>
                           <input type="text" class="form-control" id="barcode_number" placeholder="Enter Barcode Number" autocomplete="offv">
                        </div>
                     </div>
                     <div class="col-sm-2">
                        <div class="form-group">
                           <label>Article No</label>
                           <input type="text" class="form-control" id="barcode_number" placeholder="Enter Barcode Number" autocomplete="offv">
                        </div>
                     </div>
                     <div class="col-sm-8">
                        <div class="form-group">
                           <label>Item Name</label>
                           <input type="text" class="form-control" name="item_name" id="item_name" placeholder="Enter Brand Name" autocomplete="off">
                           <div id="suggestions_item_name" class="autocomplete-suggestions"></div>
                        </div>
                     </div>
                  </div>
                  <!-- Plugin content:powerpoint,txt,pdf,png,word,xl -->
                  <div class="table-responsive ">
                     <table class="custom-table table table-bordered table-striped table-hover">
                        <thead>
                           <tr class="info">
                              <th><input type="checkbox" class="all"></th>
                              <th>SrNo</th>
                              <th>Barcode No</th>
                              <th>Article No</th>
                              <th>item Name</th>
                              <th>BBR Qty</th>
                              <th>stock Qty</th>
                              <th>QTY</th>
                              <th>MOU</th>
                              <th>SP</th>
                              <th>Net Amt</th>
                              <th>GST Amt</th>
                              <th>Taxable Amt</th>
                              <th>Amount</th>
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
                                    <button type="button" class="row-delete-btn" onclick="deleteTableRow(this)" title="Delete Row" style="background:none;border:none;color:#e53935;cursor:pointer;padding:2px 5px;font-size:14px;"><i class="fa-solid fa-trash"></i></button>
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
                                       <i class="fa-solid fa-plus"></i>
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
                                    <button type="button" class="plus-btn" onclick="openPopupsp(this)" title="Edit MRP" style="margin-left:6px;"><i class="fa-solid fa-plus"></i></button>
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
                                 <label>Grand Total
                                 <label>
                              </td>
                              <td id="quantity_val">00.00</td>
                              <td colspan="2">&nbsp;</td>
                              <td id="total_net_amount2">00.00</td>
                              <td id="total_gst_amount2">00.00</td>
                              <td>&nbsp;</td>
                              <td id="total_amount2">00.00</td>
                           </tr>
                        </tfoot>
                     </table>
                  </div>
                  <div class="row">
                  <div class="col-sm-2">
                  <div class="form-group">
                  <label>Discount %</label>
                  <input type="text" class="form-control" name="percent_total_discount" id="percent_total_discount" placeholder="Enter Discount %" oninput="discountTotalPercent()" value="<?= $percent_total_discount ?>">
                  </div>
                  </div>
                  <div class="col-sm-2">
                  <div class="form-group">
                  <label>Discount Amt</label>
                  <input type="text" class="form-control" name="discount_total_amount" id="discount_total_amount" placeholder="Enter Discount Amt" oninput="discountTotalAmount()" value="<?= $discount_total_amount ?>">
                  </div>
                  </div>
                  </div>
                  <div class="row">
                     <div class="col-sm-4">
                        <div class="form-check">
                           <label>Status</label><br>
                           <label class="radio-inline">
                           <input type="radio" name="status" value="success" checked="checked">Success</label>
                           <label class="radio-inline"><input type="radio" name="status" value="hold">Hold</label>
                           <label class="radio-inline"><input type="radio" name="status" value="hold">Show Hold</label>
                        </div>
                     </div>
                     <div class="col-sm-8" style="text-align:right;">
                        <div class="reset-button">
                           <input type="hidden" name="submit" value="<?= (!empty($bill_no) ? 'update' : 'save') ?>">
                           <button type="reset" name="submit" class="btn btn-warning" style="width:110px;">Reset</button>
                           <a class="btn btn-danger" style="width:110px;" onclick="deleteRow('dataTable')">Row Remove</a>
                           <button type="button" class="btn btn-success" onclick="openPopupsave()" id="confirmbtn" style="width:110px;background-color: #009688; color: white;"><?= (!empty($bill_no) ? 'Update' : 'Save') ?></button>
                        </div>
                     </div>
                  </div>
                  
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
<section class="">
   <div class="row">
      <div class="col-sm-12">
         <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
               <div class="btn-group" id="buttonexport">
                  <a href="#">
                     <h5>Recent Bill</h5>
                  </a>
               </div>
            </div>
            <div class="panel-body">
               <form name="searchForm" id="searchForm" method="post" class="search-form" action="" autocomplete="off">
                  <input type="hidden" name="export" value="" />
                  <input type="hidden" name="page" value="1" />
                  <input type="hidden" name="sortOrder" value="DESC" />
                  <input type="hidden" name="sortField" value="id" />
               </form>
               <!-- Plugin content:powerpoint,txt,pdf,png,word,xl -->
               <?php include('include/button-export-to-data.php'); ?>
               <div class="btn-group">
                  <a href="brand-master-creation.php"><button class="btn btn-exp btn-sm dropdown-toggle" data-toggle=""><i class="fa fa-users"></i><span style="margin-left:3px;">Bill Wise List !</span></button></a>
               </div>
               <div class="btn-group">
                  <button class="btn btn-danger btn-sm" onclick="deleteRow('latestRecord')"><i class="fa fa-trash"></i><span style="margin-left:3px;">Delete with Checkbox</span></button>
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
                           <th>Dtails</th>
                           <th>Action</th>
                        </tr>
                     </thead>
                     <tbody id="latestRecord">
                     </tbody>
                  </table>
               </div>
               <div id="pagination-result">
               </div>
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
<script src="assets/dist/js/page/sale-creation.js?v2" type="text/javascript"></script>
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

