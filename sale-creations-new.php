<?php include_once("include/config.php"); ?>
<?php require_once(_BASEPATH . 'include/generatepdf.php'); ?>
<?php
   $validationHelper = new validation();
   
   $db = connect();
   
   $bill_no = $_GET['bill_no'];
   
   if (isset($_POST['submit'])) {
   
      /* echo "<pre>";
      print_r($_POST);
      die; */
      $purchage_date          = dateInSQLFormat($_POST['purchage_date']);
      $bill_number            = $_POST['bill_number'];
      $gst_type               = $_POST['gst_type'];
      $under_gst              = $_POST['under_gst'];
      $bill_type              = $_POST['bill_type'];
      $mobile_number          = $_POST['mobile_number'];
      $account_name           = $_POST['account_name'];
   
      // Item data
      $item_name              = $_POST['item_name'];
      $barcode_no             = $_POST['barcode_no'];
      $item_type              = $_POST['item_type'];
      $category               = $_POST['category'];
      $brand                  = $_POST['brand'];
      $color                  = $_POST['color'];
      $size                   = $_POST['size'];
      $stock_qty              = $_POST['stock_qty'];
      $qty                    = $_POST['qty'];
      $balance_qty            = $_POST['balance_qty'];
      $mou_name               = $_POST['mou_name'];
      $rate                   = $_POST['rate'];
      $purchase_price         = $_POST['purchase_price'];
      $percent_discount       = $_POST['percent_discount'];
      $discount_amt           = $_POST['discount_amt'];
      $net_price              = $_POST['net_price'];
      $gst                    = $_POST['gst'];
      $gst_amount             = $_POST['gst_amount'];
      $taxable_amount         = $_POST['taxable_amount'];
      $amount                 = $_POST['amount'];
      $packing_amount         = $_POST['packing_amount'];
      $delivery_amount        = $_POST['delivery_amount'];
      $other_amount           = $_POST['other_amount'];
      $labour_amount          = $_POST['labour_amount'];
      $percent_total_discount = $_POST['percent_total_discount'];
      $discount_total_amount  = $_POST['discount_total_amount'];
      $pay_mode               = $_POST['pay_mode'];
      $remarks                = $_POST['remarks'];
      $status                 = $_POST['status'];
   
   
      $current_date           = date("Y-m-d H:i:s");
   
      if ($_POST['submit'] == 'save') {
   
         foreach ($barcode_no as $k => $barcode) {
   
            //get GST value
            $othergstvalue = getGSTValueBygstType($gst[$k]);
            $cgst = $othergstvalue;
            $sgst = $othergstvalue;
   
            $insertid = $db->insert('INSERT INTO tbl_bill (purchage_date, bill_number, gst_type, under_gst, bill_type, mobile_number, account_name, item_name, barcode_number, item_type, category, brand, color, size, stock_qty, qty, balance_qty, mou_name, rate, purchase_price, percent_discount, discount_amt, net_price, gst, cgst, sgst,  gst_amount, taxable_amount, amount, packing_amount, delivery_amount, other_amount, labour_amount, percent_total_discount, discount_total_amount, pay_mode, remarks, status, created_date) values(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)', 'sssssssssssssssssssssssssssssssssssssss', $purchage_date, $bill_number, $gst_type, $under_gst, $bill_type, $mobile_number, $account_name, $item_name[$k], $barcode, $item_type[$k], $category[$k], $brand[$k], $color[$k], $size[$k], $stock_qty[$k], $qty[$k], $balance_qty[$k], $mou_name[$k], $rate[$k], $purchase_price[$k], $percent_discount[$k], $discount_amt[$k], $net_price[$k], $gst[$k], $cgst, $sgst, $gst_amount[$k], $taxable_amount[$k], $amount[$k], $packing_amount, $delivery_amount, $other_amount, $labour_amount, $percent_total_discount, $discount_total_amount, $pay_mode, $remarks, $status, $current_date);
         }
   
         if ($insertid) {
            $msg = 'Record added successfully';
            $return = '';
         } else {
            $msg = 'Failed!';
            $return = 1;
         }
   
         redirectTo('sale-creations.php', $msg, $return);
      } else if ($_POST['submit'] == 'update') {
   
         $item_id                = $_POST['item_id'];
         foreach ($barcode_no as $k => $barcode) {
   
            //get GST value
            $othergstvalue = getGSTValueBygstType($gst[$k]);
            $cgst = $othergstvalue;
            $sgst = $othergstvalue;
   
            $isExist = $item_id[$k];
   
            if ($isExist) {
   
               $insertid = $db->update("UPDATE tbl_bill SET purchage_date=?, gst_type=?, under_gst=?, bill_type=?, mobile_number=?, account_name=?, item_name=?, barcode_number=?, item_type=?, category=?, brand=?, color=?, size=?, stock_qty=?, qty=?, balance_qty=?, mou_name=?, rate=?, purchase_price=?, percent_discount=?, discount_amt=?, net_price=?, gst=?, cgst=?, sgst=?, gst_amount=?, taxable_amount=?, amount=?, packing_amount=?, delivery_amount=?, other_amount=?, labour_amount=?, percent_total_discount=?, discount_total_amount=?, pay_mode=?, remarks=?, status=?, updated_date=? WHERE id=?", 'ssssssssssssssssssssssssssssssssssssssi', $purchage_date, $gst_type, $under_gst, $bill_type, $mobile_number, $account_name, $item_name[$k], $barcode, $item_type[$k], $category[$k], $brand[$k], $color[$k], $size[$k], $stock_qty[$k], $qty[$k], $balance_qty[$k], $mou_name[$k], $rate[$k], $purchase_price[$k], $percent_discount[$k], $discount_amt[$k], $net_price[$k], $gst[$k], $cgst, $sgst, $gst_amount[$k], $taxable_amount[$k], $amount[$k], $packing_amount, $delivery_amount, $other_amount, $labour_amount, $percent_total_discount, $discount_total_amount, $pay_mode, $remarks, $status, $current_date, $item_id[$k]);
            } else {
   
   
               $insertid = $db->insert('INSERT INTO tbl_bill (purchage_date, bill_number, gst_type, under_gst, bill_type, mobile_number, account_name, item_name, barcode_number, item_type, category, brand, color, size, stock_qty, qty, balance_qty, mou_name, rate, purchase_price, percent_discount, discount_amt, net_price, gst, cgst, sgst,  gst_amount, taxable_amount, amount, packing_amount, delivery_amount, other_amount, labour_amount, percent_total_discount, discount_total_amount, pay_mode, remarks, status, created_date) values(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)', 'sssssssssssssssssssssssssssssssssssssss', $purchage_date, $bill_number, $gst_type, $under_gst, $bill_type, $mobile_number, $account_name, $item_name[$k], $barcode, $item_type[$k], $category[$k], $brand[$k], $color[$k], $size[$k], $stock_qty[$k], $qty[$k], $balance_qty[$k], $mou_name[$k], $rate[$k], $purchase_price[$k], $percent_discount[$k], $discount_amt[$k], $net_price[$k], $gst[$k], $cgst, $sgst, $gst_amount[$k], $taxable_amount[$k], $amount[$k], $packing_amount, $delivery_amount, $other_amount, $labour_amount, $percent_total_discount, $discount_total_amount, $pay_mode, $remarks, $status, $current_date);
            }
         }
   
         if ($insertid) {
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
      <!-- CLOSE -->
      <button class="close-popup" onclick="closePopupsave()">
      <i class="fa-solid fa-xmark"></i>
      </button>
      <h3 class="popup-title" style="text-align:center;">Payment mode details</h3>
      <div class="row">
         <div class="col-md-6">
            <div class="form-group-modern">
               <label>A/C Code</label>
               <input type="text"
                  class="popup-input"
                  placeholder="Enter your A/C Code">
            </div>
         </div>
         <div class="col-md-6">
            <div class="form-group-modern">
               <label>Mobile No</label>
               <input type="text"
                  class="popup-input"
                  placeholder="Enter your Mobile No">
            </div>
         </div>
      </div>
      <div class="form-group-modern">
         <label>A/C Name</label>
         <input type="text"
            class="popup-input"
            placeholder="Enter your A/C Code">
      </div>
      <div class="form-group-modern" style="text-align: center;">
         <label>Your Bill Amount</label>
         <span>1200.00</span>
      </div>
      <div class="row">
         <div class="form-group-modern">
            <label class="popup-text">Payment Mode</label>
            <select class="popup-select">
               <option>Select Option</option>
               <option>CASH</option>
               <option>UPI</option>
               <option>CARD</option>
               <option>CREDIT</option>
            </select>
         </div>
      </div>
      <div class="row">
         <div class="col-md-6">
            <div class="form-group-modern">
               <label>CASH</label>
               <input type="text"
                  class="popup-input"
                  placeholder="Enter your A/C Code">
            </div>
         </div>
         <div class="col-md-6">
            <div class="form-group-modern">
               <label>UPI</label>
               <input type="text"
                  class="popup-input"
                  placeholder="Enter your Mobile No">
            </div>
         </div>
      </div>
      <div class="row">
         <div class="col-md-6">
            <div class="form-group-modern">
               <label>CARD</label>
               <input type="text"
                  class="popup-input"
                  placeholder="Enter your A/C Code">
            </div>
         </div>
         <div class="col-md-6">
            <div class="form-group-modern">
               <label>CREDIT</label>
               <input type="text"
                  class="popup-input"
                  placeholder="Enter your Mobile No">
            </div>
         </div>
      </div>
      <!-- BUTTONS -->
      <div class="popup-actions">
         <button class="popup-btn cancel-btn"
            onclick="closePopupsave()">
         Cancel
         </button>
         <button class="popup-btn save-btn">
         Save
         </button>
      </div>
   </div>
</div>
<!--end popup-save button--> 
<!--start plus icon-->
<!-- FONT AWESOME -->
<link rel="stylesheet"
   href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
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
      <!-- CLOSE -->
      <button class="close-popup" onclick="closePopup()">
      <i class="fa-solid fa-xmark"></i>
      </button>
      <h3 class="popup-title">Details</h3>
      <!-- TEXTBOX  -->
      <div class="row">
         <div class="col-md-6">
            <div class="form-group-modern">
               <label>Groupe</label>
               <input type="text"
                  class="popup-input"
                  placeholder="Enter your name">
            </div>
         </div>
         <div class="col-md-6">
            <div class="form-group-modern">
               <label>SubGroup</label>
               <input type="text"
                  class="popup-input"
                  placeholder="Enter your name">
            </div>
         </div>
      </div>
      <div class="row">
         <div class="col-md-6">
            <div class="form-group-modern">
               <label>Brand</label>
               <input type="text"
                  class="popup-input"
                  placeholder="Enter your name">
            </div>
         </div>
         <div class="col-md-6">
            <div class="form-group-modern">
               <label>Color</label>
               <input type="text"
                  class="popup-input"
                  placeholder="Enter your name">
            </div>
         </div>
      </div>
      <div class="row">
         <div class="col-md-6">
            <div class="form-group-modern">
               <label>Size</label>
               <input type="text"
                  class="popup-input"
                  placeholder="Enter your name">
            </div>
         </div>
         <div class="col-md-6">
            <div class="form-group-modern">
               <label>Style</label>
               <input type="text"
                  class="popup-input"
                  placeholder="Enter your name">
            </div>
         </div>
      </div>
      <!-- DROPDOWN -->
      <!-- BUTTONS -->
      <div class="popup-actions">
         <button class="popup-btn cancel-btn"
            onclick="closePopup()">
         Cancel
         </button>
      </div>
   </div>
</div>
<script>
   function openPopup(){
       document.getElementById("modernPopup")
       .classList.add("active");
   }
   
   function closePopup(){
       document.getElementById("modernPopup")
       .classList.remove("active");
   }
   
</script>
<!--end plus icon--> 
<div class="modern-popupsp" id="modernPopupsp">
   <div class="popup-box">
      <!-- CLOSE -->
      <button class="close-popup" onclick="closePopupsp()">
      <i class="fa-solid fa-xmark"></i>
      </button>
      <h3 class="popup-title">MRP Details</h3>
      <!-- TEXTBOX  -->
      <div class="row">
         <div class="col-md-12">
            <div class="form-group-modern">
               <label>MRP</label>
               <input type="text"
                  class="popup-input"
                  placeholder="Enter your name">
            </div>
         </div>
      </div>
      <div class="row">
         <div class="col-md-6">
            <div class="form-group-modern">
               <label>Dis.(%)</label>
               <input type="text"
                  class="popup-input"
                  placeholder="Enter your name">
            </div>
         </div>
         <div class="col-md-6">
            <div class="form-group-modern">
               <label>Dis.Amt.</label>
               <input type="text"
                  class="popup-input"
                  placeholder="Enter your name">
            </div>
         </div>
      </div>
      <div class="row">
         <div class="col-md-12">
            <div class="form-group-modern">
               <label>SP</label>
               <input type="text"
                  class="popup-input"
                  placeholder="Enter your name">
            </div>
         </div>
      </div>
      <!-- DROPDOWN -->
      <!-- BUTTONS -->
      <div class="popup-actions">
         <button class="popup-btn cancel-btn"
            onclick="closePopupsp()">
         Cancel
         </button>
      </div>
   </div>
</div>
<script>
   function openPopupsp(){
       document.getElementById("modernPopupsp")
       .classList.add("active");
   }
   
   function closePopupsp(){
       document.getElementById("modernPopupsp")
       .classList.remove("active");
   }
   
</script>
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
                     <!-- DATE TIME -->
                     <div class="col-md-6">
                        <div class="modern-form-group">
                           <label>
                           Date & Time
                           </label>
                           <input type="text"
                              class="modern-input"
                              name="date_time"
                              placeholder="Enter Date & Time">
                        </div>
                     </div>
                     <!-- BILL NO -->
                     <div class="col-md-6">
                        <div class="modern-form-group">
                           <label>
                           Bill No.
                           </label>
                           <input type="text"
                              class="modern-input"
                              name="bill_no"
                              placeholder="Enter Bill Number">
                        </div>
                     </div>
                  </div>
                  <div class="row">
                     <!-- DATE TIME -->
                     <div class="col-md-2">
                        <div class="modern-form-group">
                           <label>
                           S.code
                           </label>
                           <input type="text"
                              class="modern-input"
                              name="date_time"
                              placeholder="Enter Date & Time">
                        </div>
                     </div>
                     <!-- BILL NO -->
                     <div class="col-md-5">
                        <div class="modern-form-group">
                           <label>
                           Store name
                           </label>
                           <input type="text"
                              class="modern-input"
                              name="bill_no"
                              placeholder="Enter Bill Number">
                        </div>
                     </div>
                     <div class="col-md-5">
                        <div class="modern-form-group">
                           <label>
                           Location
                           </label>
                           <input type="text"
                              class="modern-input"
                              name="bill_no"
                              placeholder="Enter Bill Number">
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
                           <select name="gst_type" class="modern-select" required>
                              <option value="">Select GST Type</option>
                              <option>Inclusive</option>
                              <option>Exclusive</option>
                              <option>None</option>
                           </select>
                        </div>
                     </div>
                     <!-- UNDER GST -->
                     <div class="col-md-4 col-12">
                        <div class="modern-form-group">
                           <label>Under GST</label>
                           <select name="under_gst" class="modern-select" required>
                              <option value="">Select Under GST</option>
                              <option>Local</option>
                              <option>Inter</option>
                           </select>
                        </div>
                     </div>
                     <!-- BILL TYPE -->
                     <div class="col-md-4 col-12">
                        <div class="modern-form-group">
                           <label>Bill Type</label>
                           <select name="bill_type" class="modern-select" required>
                              <option value="">Select Bill Type</option>
                              <option>Tax Invoice</option>
                              <option>Estimate</option>
                              <option>Cash Memo</option>
                           </select>
                        </div>
                     </div>
                  </div>
                  <div class="row">
                     <!-- DATE TIME -->
                     <div class="col-md-4">
                        <div class="modern-form-group">
                           <label>
                           Packing Charge
                           </label>
                           <input type="text"
                              class="modern-input"
                              name="date_time"
                              placeholder="Enter Date & Time">
                        </div>
                     </div>
                     <div class="col-md-4">
                        <div class="modern-form-group">
                           <label>
                           Delivery Charge
                           </label>
                           <input type="text"
                              class="modern-input"
                              name="date_time"
                              placeholder="Enter Date & Time">
                        </div>
                     </div>
                     <!-- BILL NO -->
                     <div class="col-md-4">
                        <div class="modern-form-group">
                           <label>
                           Handling charge
                           </label>
                           <input type="text"
                              class="modern-input"
                              name="bill_no"
                              placeholder="Enter Bill Number">
                        </div>
                     </div>
                  </div>
                   <div class="row">
                     <div class="col-md-12">
                        <div class="modern-form-group">
                           <label>
                           Labour Charge
                           </label>
                           <input type="text"
                              class="modern-input"
                              name="bill_no"
                              placeholder="Enter Bill Number">
                        </div>
                     </div>
                  </div>
                  <div class="row">
                     <div class="col-md-12">
                        <div class="modern-form-group">
                           <label>
                           Adjustment amount
                           </label>
                           <input type="text"
                              class="modern-input"
                              name="bill_no"
                              placeholder="Enter Bill Number">
                        </div>
                     </div>
                  </div>
                  <div class="row">
                     <!-- DATE TIME -->
                     <div class="col-md-12">
                        <div class="modern-form-group">
                           <label>
                           Remarks
                           </label>
                           <input type="text"
                              class="modern-input"
                              name="date_time"
                              placeholder="Enter Date & Time">
                        </div>
                     </div>
                     <!-- BILL NO -->
                  </div>
                  <!-- PUT YOUR FILTER CODE HERE -->
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
                              <th>SN
                                 <button type="button" class="row-delete-btn" onclick="deleteTableRow(this)" title="Delete Row" style="background:none;border:none;color:#e53935;cursor:pointer;padding:2px 5px;font-size:14px;"><i class="fa-solid fa-trash"></i></button>
                              </th>
                              <th>Barcode No</th>
                              <th>Article No</th>
                              <th>item Name
                                 <button class="plus-btn" onclick="openPopup()">
                                 <i class="fa-solid fa-plus"></i>
                                 </button>
                              </th>
                              <th>BBR Qty</th>
                              <th>stock Qty</th>
                              <th>QTY</th>
                              <th>MOU</th>
                              <th>SP
                                 <button class="plus-btn" onclick="openPopupsp()">
                                 <i class="fa-solid fa-plus"></i>
                                 </button>
                              </th>
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
                              
                                 if ($item['under_gst'] == 'Local') {
                                    $gstTitle = "CGST: " . $item['cgst'] . "%, SGST: " . $item['sgst'] . "%";
                                 } else {
                                    $gstTitle = "IGST: " . $item['gst'] . "%";
                                 }
                              
                              ?>
                           <tr id="row_<?= $item['barcode_number']; ?>">
                              <td>
                                 <div class="checkbox checkbox-info">
                                    <input id="checkbox<?php echo $i; ?>" type="checkbox" value="<?= $item['id'] ?>">
                                    <label for="checkbox<?php echo $i; ?>"><?php echo $i; ?></label>
                                    <input type="hidden" id="" name="item_id[]" value="<?= $item['id'] ?>" />
                                 </div>
                              </td>
                              <td><input type="text" class="form-control valid" value="<?= $item['barcode_number']; ?>" name="barcode_no[]" required="" readonly=""></td>
                              <td><input type="text" class="form-control valid" value="<?= $item['item_name']; ?>" name="item_name[]" required readonly><input type="hidden" class="form-control" value="<?= $item['item_type']; ?>" name="item_type[]" required="" readonly=""></td>
                              <td><input type="text" value="<?= $item['brand']; ?>" class="form-control valid" name="brand[]" readonly=""></td>
                              <td><input type="text" value="<?= $item['color']; ?>" class="form-control valid" name="color[]" readonly=""></td>
                              <td><input type="text" value="<?= $item['size']; ?>" class="form-control valid" name="size[]" readonly=""></td>
                              <td><input type="number" class="form-control stock_qty" value="<?= $item['stock_qty'] ?>" id="stock_id_<?= $item['id'] ?>" name="stock_qty[]" required readonly></td>
                              <td width="5%"><input type="number" value="<?= $item['qty']; ?>" class="form-control qtyval qty_array valid" name="qty[]" oninput="updateBillType()" required=""></td>
                              <td><input type="number" class="form-control bal_qty" value="<?= $item['balance_qty'] ?>" id="balance_id_<?= $item['id'] ?>" name="balance_qty[]" required readonly></td>
                              <td><input type="text" value="<?= $item['mou_name']; ?>" class="form-control valid" name="mou_name[]" required="" readonly=""></td>
                              <td><input type="number" class="form-control rate valid" value="<?= $item['rate']; ?>" name="rate[]" readonly=""></td>
                              <td><input type="text" price="<?= $item['purchase_price']; ?>" class="form-control pprice valid" id="purchase_<?= $item['id']; ?>" name="purchase_price[]" oninput="updateBillType()" required="" value="<?= $item['purchase_price']; ?>"></td>
                              <td><input type="number" value="<?= $item['percent_discount']; ?>" class="form-control percent_discount valid" id="percent_discount_<?= $item['id']; ?>" name="percent_discount[]" oninput="calculateFromPercentage('<?= $item['barcode_number']; ?>')" required=""></td>
                              <td><input type="number" value="<?= $item['discount_amt']; ?>" class="form-control discount_amt valid" id="discountamt_<?= $item['id']; ?>" name="discount_amt[]" oninput="calculateFromAmount('<?= $item['barcode_number']; ?>')" required=""></td>
                              <td><input type="text" value="<?= $item['net_price']; ?>" class="form-control net_array valid" id="net_price_<?= $item['id']; ?>" name="net_price[]" required="" readonly=""></td>
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
                              <td colspan="6">&nbsp;</td>
                              <td id="total_net_amount2">00.00</td>
                              <td></td>
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
                           <button class="btn btn-success" onclick="openPopupsave()" id="confirmbtn" style="width:110px;background-color: #009688; color: white;"><?= (!empty($bill_no) ? 'Update' : 'Save') ?></button>
                        </div>
                     </div>
                  </div>
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
<script src="assets/dist/js/page/sale-creation.js" type="text/javascript"></script>
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
<script>
   function openPopupsave(){
       document.getElementById("modernPopup-save")
       .classList.add("active");
   }
   
   function closePopupsave(){
       document.getElementById("modernPopup-save")
       .classList.remove("active");
   }
   
</script>
<?php include('include/footer-2.php'); ?>