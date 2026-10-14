<?php include_once("include/config.php"); ?>
<?php require_once(_BASEPATH . 'include/generatepdf.php'); 
?>
<?php
/* ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL); */
?>
<?php
$validationHelper = new validation();

$db = connect();

$bill_no = $_GET['bill_no'] ?? '';


if (isset($_POST['form_action'])) {

/* echo "<pre>";
print_r($_POST);
die; */

   $purchage_date          = dateInSQLFormat($_POST['purchage_date']);
   $bill_number            = $_POST['bill_number'];
   $gst_type               = $_POST['gst_type'];
   $under_gst              = $_POST['under_gst'];
   $bill_type              = $_POST['bill_type'];
   $mobile_number          = $_POST['mobile_number']          ?? '';
   $account_name           = $_POST['account_name']           ?? '';

   // Item data (array fields)
   $item_name              = $_POST['item_name'];
   $barcode_no             = $_POST['barcode_no'];
   $item_type              = $_POST['item_type'];
   $article_no             = $_POST['article_no'];
   $category               = $_POST['category'];
   $sub_category           = $_POST['sub_category'];
   $brand                  = $_POST['brand'];
   $color                  = $_POST['color'];
   $style                  = $_POST['style'];
   $size                   = $_POST['size'];
   $bbr_qty                = $_POST['bbr_qty']                ?? [];
   $qty                    = $_POST['qty'];
   $mou_name               = $_POST['mou_name'];
   $rate                   = $_POST['rate']                   ?? [];   // DB col exists, no form input
   $purchase_price         = $_POST['purchase_price'];
   $percent_discount       = $_POST['percent_discount'];
   $discount_amt           = $_POST['discount_amt'];
   $after_pp               = $_POST['after_pp'];
   $mrp                    = $_POST['mrp'];
   $discount_percent2      = $_POST['discount_percent2'];
   $discount_amount2       = $_POST['discount_amount2'];
   $selling_price          = $_POST['selling_price'];
   $net_price              = $_POST['net_price'];
   $gst                    = $_POST['gst'];
   $gst_amount             = $_POST['gst_amount'];
   $taxable_amount         = $_POST['taxable_amount'];
   $amount                 = $_POST['amount'];

   // Header-level amounts
   $packing_amount         = $_POST['packing_amount']         ?? '';
   $delivery_amount        = $_POST['delivery_amount']        ?? '';   // DB col exists, no form input
   $other_amount           = $_POST['other_amount']           ?? '';
   $labour_amount          = $_POST['labour_amount']          ?? '';
   $percent_total_discount = $_POST['percent_total_discount'] ?? '';
   $discount_total_amount  = $_POST['discount_total_amount']  ?? '';
   $invoice_number         = $_POST['invoice_number']         ?? '';
   $invoice_date           = dateInSQLFormat($_POST['invoice_date'] ?? '');
   $remarks                = $_POST['remarks']                ?? '';
   $status                 = $_POST['status']                 ?? '';

   // Payment popup fields
   $ac_code                = $_POST['ac_code']                ?? '';
   $payment_mode           = $_POST['payment_mode']           ?? '';
   $pay_cash               = $_POST['pay_cash']               ?? '0';
   $pay_upi                = $_POST['pay_upi']                ?? '0';
   $pay_card               = $_POST['pay_card']               ?? '0';
   $pay_credit             = $_POST['pay_credit']             ?? '0';

   // Store/sidebar fields
   $s_code                 = $_POST['s_code']                 ?? '';
   $store_name             = $_POST['store_name']             ?? '';
   $location               = $_POST['location']               ?? '';

   $current_date           = date("Y-m-d H:i:s");

   if ($_POST['form_action'] == 'save') {


      if (!empty($_FILES['picture']['name'])) {

         $uploadPath = _UPLOAD_DIR . 'purchase/';

         $fileData['name'] = $_FILES['picture']['name'];
         $fileData['size'] = $_FILES['picture']['size'];
         $fileData['tmp_name'] = $_FILES['picture']['tmp_name'];
         $fileName = $_FILES['picture']['name'];
         $picture = uploadValidatedFile($fileData, $uploadPath, $fileName);
      } else {
         $picture = '';
      }

      foreach ($barcode_no as $k => $barcode) {

         //get GST value
         $gst_val = !empty($gst[$k]) ? $gst[$k] : 0;
         $othergstvalue = getGSTValueBygstType($gst[$k]);
         
         $cgst = $othergstvalue;
         $sgst = $othergstvalue;

         $insertid = $db->insert('INSERT INTO tbl_purchase (
            purchage_date, bill_number, gst_type, under_gst, bill_type,
            account_name, mobile_number, ac_code, payment_mode, pay_cash,
            pay_upi, pay_card, pay_credit, s_code, store_name, location,
            packing_amount, delivery_amount, other_amount, labour_amount,
            percent_total_discount, discount_total_amount,
            invoice_number, invoice_date, remarks, status, picture,
            item_name, barcode_number, item_type, article_no,
            category, sub_category, brand, color, style, size,
            bbr_qty, qty, mou_name, rate,
            purchase_price, percent_discount, discount_amt, after_pp,
            mrp, discount_percent2, discount_amount2, selling_price,
            net_price, gst, cgst, sgst, gst_amount, taxable_amount, amount,
            created_date
            ) VALUES (
               ?,?,?,?,?,
               ?,?,?,?,?,
               ?,?,?,?,?,?,
               ?,?,?,?,
               ?,?,
               ?,?,?,?,?,
               ?,?,?,?,
               ?,?,?,?,?,?,
               ?,?,?,?,
               ?,?,?,?,
               ?,?,?,?,
               ?,?,?,?,?,?,?,
               ?
            )',
            str_repeat('s', 57),
            $purchage_date, $bill_number, $gst_type, $under_gst, $bill_type,
            $account_name, $mobile_number, $ac_code, $payment_mode, $pay_cash,
            $pay_upi, $pay_card, $pay_credit, $s_code, $store_name, $location,
            $packing_amount, $delivery_amount, $other_amount, $labour_amount,
            $percent_total_discount, $discount_total_amount,
            $invoice_number, $invoice_date, $remarks, $status, $picture,
            $item_name[$k], $barcode, $item_type[$k], $article_no[$k],
            $category[$k], $sub_category[$k], $brand[$k], $color[$k], $style[$k], $size[$k],
            $bbr_qty[$k], $qty[$k], $mou_name[$k], $rate[$k] ?? '',
            $purchase_price[$k], $percent_discount[$k], $discount_amt[$k], $after_pp[$k],
            $mrp[$k], $discount_percent2[$k], $discount_amount2[$k], $selling_price[$k],
            $net_price[$k], $gst[$k], $cgst, $sgst, $gst_amount[$k], $taxable_amount[$k], $amount[$k],
            $current_date
         );
      }

      if ($insertid) {
         $msg = 'Item added successfully';
         $return = '';
      } else {
         $msg = 'Failed!';
         $return = 1;
      }

      redirectTo('purchase-master-creation.php', $msg, $return);
   } else if ($_POST['form_action'] == 'update') {

      if (!empty($_FILES['picture']['name'])) {

         $uploadPath = _UPLOAD_DIR . 'purchase/';

         $fileData['name'] = $_FILES['picture']['name'];
         $fileData['size'] = $_FILES['picture']['size'];
         $fileData['tmp_name'] = $_FILES['picture']['tmp_name'];
         $fileName = $_FILES['picture']['name'];
         $picture = uploadValidatedFile($fileData, $uploadPath, $fileName);
      } else {
         $picture = $docPath = $_POST['picture_name'];
      }

      $item_id                = $_POST['item_id'];
      foreach ($barcode_no as $k => $barcode) {

         //get GST value
         $othergstvalue = getGSTValueBygstType($gst[$k]);
         $cgst = $othergstvalue;
         $sgst = $othergstvalue;

         $isExist = $item_id[$k];

         if ($isExist) {

            $insertid = $db->update(
               "UPDATE tbl_purchase SET
                  purchage_date=?, gst_type=?, under_gst=?, bill_type=?,
                  account_name=?, mobile_number=?, ac_code=?, payment_mode=?, pay_cash=?,
                  pay_upi=?, pay_card=?, pay_credit=?, s_code=?, store_name=?, location=?,
                  packing_amount=?, delivery_amount=?, other_amount=?, labour_amount=?,
                  percent_total_discount=?, discount_total_amount=?,
                  invoice_number=?, invoice_date=?, remarks=?, status=?, picture=?,
                  item_name=?, barcode_number=?, item_type=?, article_no=?,
                  category=?, sub_category=?, brand=?, color=?, style=?, size=?,
                  bbr_qty=?, qty=?, mou_name=?, rate=?,
                  purchase_price=?, percent_discount=?, discount_amt=?, after_pp=?,
                  mrp=?, discount_percent2=?, discount_amount2=?, selling_price=?,
                  net_price=?, gst=?, cgst=?, sgst=?, gst_amount=?, taxable_amount=?, amount=?,
                  updated_date=?
               WHERE id=?",
               str_repeat('s', 56) . 'i',
               $purchage_date, $gst_type, $under_gst, $bill_type,
               $account_name, $mobile_number, $ac_code, $payment_mode, $pay_cash,
               $pay_upi, $pay_card, $pay_credit, $s_code, $store_name, $location,
               $packing_amount, $delivery_amount, $other_amount, $labour_amount,
               $percent_total_discount, $discount_total_amount,
               $invoice_number, $invoice_date, $remarks, $status, $picture,
               $item_name[$k], $barcode, $item_type[$k], $article_no[$k],
               $category[$k], $sub_category[$k], $brand[$k], $color[$k], $style[$k], $size[$k],
               $bbr_qty[$k], $qty[$k], $mou_name[$k], $rate[$k] ?? '',
               $purchase_price[$k], $percent_discount[$k], $discount_amt[$k], $after_pp[$k],
               $mrp[$k], $discount_percent2[$k], $discount_amount2[$k], $selling_price[$k],
               $net_price[$k], $gst[$k], $cgst, $sgst, $gst_amount[$k], $taxable_amount[$k], $amount[$k],
               $current_date,
               $item_id[$k]
               );
         } else {


            $insertid = $db->insert('INSERT INTO tbl_purchase (
               purchage_date, bill_number, gst_type, under_gst, bill_type,
               account_name, mobile_number, ac_code, payment_mode, pay_cash,
               pay_upi, pay_card, pay_credit, s_code, store_name, location,
               packing_amount, delivery_amount, other_amount, labour_amount,
               percent_total_discount, discount_total_amount,
               invoice_number, invoice_date, remarks, status, picture,
               item_name, barcode_number, item_type, article_no,
               category, sub_category, brand, color, style, size,
               bbr_qty, qty, mou_name, rate,
               purchase_price, percent_discount, discount_amt, after_pp,
               mrp, discount_percent2, discount_amount2, selling_price,
               net_price, gst, cgst, sgst, gst_amount, taxable_amount, amount,
               created_date
               ) VALUES (
                  ?,?,?,?,?,
                  ?,?,?,?,?,
                  ?,?,?,?,?,?,
                  ?,?,?,?,
                  ?,?,
                  ?,?,?,?,?,
                  ?,?,?,?,
                  ?,?,?,?,?,?,
                  ?,?,?,?,
                  ?,?,?,?,
                  ?,?,?,?,
                  ?,?,?,?,?,?,?,
                  ?
               )',
               str_repeat('s', 57),
               $purchage_date, $bill_number, $gst_type, $under_gst, $bill_type,
               $account_name, $mobile_number, $ac_code, $payment_mode, $pay_cash,
               $pay_upi, $pay_card, $pay_credit, $s_code, $store_name, $location,
               $packing_amount, $delivery_amount, $other_amount, $labour_amount,
               $percent_total_discount, $discount_total_amount,
               $invoice_number, $invoice_date, $remarks, $status, $picture,
               $item_name[$k], $barcode, $item_type[$k], $article_no[$k],
               $category[$k], $sub_category[$k], $brand[$k], $color[$k], $style[$k], $size[$k],
               $bbr_qty[$k], $qty[$k], $mou_name[$k], $rate[$k] ?? '',
               $purchase_price[$k], $percent_discount[$k], $discount_amt[$k], $after_pp[$k],
               $mrp[$k], $discount_percent2[$k], $discount_amount2[$k], $selling_price[$k],
               $net_price[$k], $gst[$k], $cgst, $sgst, $gst_amount[$k], $taxable_amount[$k], $amount[$k],
               $current_date
            );
         }
      }

      if ($insertid) {
         $msg = 'Item Updated successfully';
         $return = '';
      } else {
         $msg = 'Failed!';
         $return = 1;
      }

      redirectTo('purchase-master-creation.php?bill_no=' . $bill_no, $msg, $return);
   }
}


if ($_POST['action'] == 'delete' && $_POST['id'] != '') {

   $id = $_POST['id'];

   if ($id) {

      $res = $db->delete("DELETE FROM tbl_purchase WHERE id=?", 'i', $id);

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

   redirectTo('purchase-master-creation.php', $msg, $code);
}else if ($_POST['action'] == 'send-mail' && $_POST['id'] != '') {

   $sendid = '';
   if ($bill_no) {
      $sendid = '?bill_no=' . $bill_no;
   }
   $bill_no = $_POST['id'];
   $filename = generatePDF($bill_no);

   $mail_to = $_POST['account_email'];
   if($mail_to){
   $mail_subject = "bill invoice";
   $mail_body = "<p>Dear Customer,
Thank you for choosing Ramalaya to enrich your shopping experience. We are delighted to share the invoice for your recent purchase with us. Please find it attached to this email for your reference.
At Ramalaya, we strive to bring you the finest products that blend tradition with elegance. We hope your purchase brings joy and fulfillment to your space.
If you have any questions or require assistance, feel free to connect with us:
</p>";

   send_mail($mail_to, $mail_subject, $mail_body, '', '', $filename);
   redirectTo('purchase-master-creation.php' . $sendid, 'Mail has been sent successfully');
   } else {
      redirectTo('purchase-master-creation.php' . $sendid, 'Email id not found.', 1);
   }
}

if ($bill_no) {

   $pur_stmt = $db->select("SELECT * FROM tbl_purchase WHERE bill_number=?", 's', $bill_no);

   $res = $pur_stmt->fetch_assoc();

   foreach ($res as $key => $value) {
      $$key = $validationHelper->filterText($value);
   }
   $pur_stmt->close();
}

?>

<?php include('include/header.php'); ?>
<?php include('include/fh-form-scrolling-data-list.php');?>
<!--start plus icon-->
                              <!-- FONT AWESOME -->
<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>

/* PLUS BUTTON */
.plus-btn{
    width:18px;
    height:18px;
    border:none;
    border-radius:16px;
    background:#009688 ;
    color:#fff;
    display:flex;
    align-items:center;
    justify-content:center;
    cursor:pointer;
    font-size:14px;
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
                                 
                                 .modern-popup-mrp{
                                 position:fixed;
                                 inset:0;
                                 background:rgba(15,23,42,0.55);
                                 display:none;
                                 align-items:center;
                                 justify-content:center;
                                 z-index:9999;
                                 padding:15px;
                                 }
                                 
                                 .modern-popup-mrp.active{
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
                                 overflow-y:auto;
                                 }
                                 .modern-popup-save.active{
                                 display:flex;
                                 }

/* POPUP BOX */
.popup-box{
    width:100%;
    max-width:430px;
    max-height:90vh;
    overflow-y:auto;
    background:#fff;
    border-radius:24px;
    padding:26px;
    position:relative;
    animation:popupScale 0.3s ease;
    box-shadow:0 25px 60px rgba(0,0,0,0.15);
    margin:auto;
}
/* Custom scrollbar inside popup */
.popup-box::-webkit-scrollbar{width:4px;}
.popup-box::-webkit-scrollbar-track{background:transparent;}
.popup-box::-webkit-scrollbar-thumb{background:#cbd5e1;border-radius:10px;}

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

    .modern-popup-save{
        align-items:flex-start;
        padding:10px;
    }

    .popup-box{
        padding:18px 14px;
        border-radius:18px;
        max-height:92vh;
        margin:auto 0;
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



<!-- GROUP DETAILS POPUP -->
<div class="modern-popup" id="modernPopup">
    <div class="popup-box">
        <button type="button" class="close-popup" onclick="closePopup()">
            <i class="fa-solid fa-xmark"></i>
        </button>
        <h3 class="popup-title">Add Group Details</h3>

        <!-- Error banner -->
        <div id="grp_error_msg" style="display:none;background:#fff0f0;border:1px solid #f5c2c2;border-left:4px solid #e53935;border-radius:10px;padding:10px 14px;margin-bottom:12px;font-size:13px;color:#b71c1c;gap:8px;align-items:center;">
            <i class="fa-solid fa-circle-exclamation"></i>
            <span id="grp_error_text"></span>
        </div>

        <div class="form-group-modern">
            <label class="popup-text">SubGroup Name <span style="color:red;">*</span></label>
            <select class="popup-input" id="grp_sub_category">
                <option value="">-- Select SubGroup --</option>
                <?php
                $sgStmt = $db->select("SELECT subgroup_name FROM tbl_subgroup_master WHERE status = 'true' ORDER BY subgroup_name ASC");
while ($sg = $sgStmt->fetch_assoc()) {
    echo '<option value="' . htmlspecialchars($sg['subgroup_name']) . '"' . ($sg['subgroup_name'] == ($sub_category ?? '') ? ' selected' : '') . '>' . htmlspecialchars($sg['subgroup_name']) . '</option>';
}
$sgStmt->close();
                ?>
            </select>
        </div>
        
        <div class="form-group-modern">
            <label class="popup-text">Brand Name</label>
            <select class="popup-input" id="grp_brand">
                <option value="">-- Select Brand --</option>
                <?php
                $brandStmt = $db->select("SELECT brand_name FROM tbl_brand_master WHERE status = 'true' ORDER BY brand_name ASC");
while ($brand = $brandStmt->fetch_assoc()) {
    echo '<option value="' . htmlspecialchars($brand['brand_name']) . '"' . ($brand['brand_name'] == ($brand ?? '') ? ' selected' : '') . '>' . htmlspecialchars($brand['brand_name']) . '</option>';
}
$brandStmt->close();
                ?>
            </select>
        </div>
        <div class="form-group-modern">
            <label class="popup-text">Color Name</label>
            <select class="popup-input" id="grp_color">
                <option value="">-- Select Color --</option>
                <?php
                $colorStmt = $db->select("SELECT color_name FROM tbl_color_master WHERE status = 'true' ORDER BY color_name ASC");
while ($color = $colorStmt->fetch_assoc()) {
    echo '<option value="' . htmlspecialchars($color['color_name']) . '"' . ($color['color_name'] == ($color ?? '') ? ' selected' : '') . '>' . htmlspecialchars($color['color_name']) . '</option>';
}
$colorStmt->close();
                ?>
            </select>
        </div>
        <div class="form-group-modern">
            <label class="popup-text">Style Name</label>
            <select class="popup-input" id="grp_style">
                <option value="">-- Select Style --</option>
                <?php
                $styleStmt = $db->select("SELECT style_name FROM tbl_styledesign_master WHERE status = 'true' ORDER BY style_name ASC");
while ($style = $styleStmt->fetch_assoc()) {
    echo '<option value="' . htmlspecialchars($style['style_name']) . '"' . ($style['style_name'] == ($style ?? '') ? ' selected' : '') . '>' . htmlspecialchars($style['style_name']) . '</option>';
}
$styleStmt->close();
                ?>
            </select>
        </div>

        <div class="popup-actions">
            <button type="button" class="popup-btn cancel-btn" onclick="closePopup()">Cancel</button>
            <button type="button" class="popup-btn save-btn" onclick="saveGroupDetails()">Save</button>
        </div>
    </div>
</div>

                              <!--end plus icon-->
                              
                              <!--start pur-dis plus icon-->
                              <!-- PP DISCOUNT POPUP -->
                              <div class="modern-popup-pur" id="modernPopup-pur">
                                 <div class="popup-box">
                                    <button type="button" class="close-popup" onclick="closePopup1()">
                                       <i class="fa-solid fa-xmark"></i>
                                    </button>
                                    <h3 class="popup-title" style="text-align:center;">Add PP Discount</h3>

                                    <!-- Error banner -->
                                    <div id="pp_error_msg" style="display:none;background:#fff0f0;border:1px solid #f5c2c2;border-left:4px solid #e53935;border-radius:10px;padding:10px 14px;margin-bottom:12px;font-size:13px;color:#b71c1c;gap:8px;align-items:center;">
                                       <i class="fa-solid fa-circle-exclamation"></i>
                                       <span id="pp_error_text"></span>
                                    </div>

                                    <div class="form-group-modern">
                                       <label>Purchase Price</label>
                                       <input type="text" class="popup-input" id="pp_purchase_price" placeholder="Purchase Price" readonly style="background:#f5f5f5;">
                                    </div>
                                    <div class="form-group-modern">
                                       <label>Discount (%) <span style="color:red;">*</span></label>
                                       <input type="number" class="popup-input" id="pp_discount_pct" placeholder="Enter Discount %" min="0" max="100"
                                          oninput="syncPPFromPercent()">
                                    </div>
                                    <div class="form-group-modern">
                                       <label>Discount Amount <span style="color:red;">*</span></label>
                                       <input type="number" class="popup-input" id="pp_discount_amt" placeholder="Enter Discount Amount" min="0"
                                          oninput="syncPPFromAmount()">
                                    </div>
                                    <div class="form-group-modern">
                                       <label>After Discount PP</label>
                                       <input type="text" class="popup-input" id="pp_after_pp" placeholder="After Discount PP" readonly style="background:#f5f5f5;font-weight:600;color:#009688;">
                                    </div>

                                    <div class="popup-actions">
                                       <button type="button" class="popup-btn cancel-btn" onclick="closePopup1()">Cancel</button>
                                       <button type="button" class="popup-btn save-btn" onclick="savePPDetails()">Save</button>
                                    </div>
                                 </div>
                              </div>

                              <script>
                              var _activePPRow = null;

                              function openPopup1(btn) {
                                 _activePPRow = btn ? $(btn).closest('tr') : null;

                                 // Reset error
                                 document.getElementById('pp_error_msg').style.display = 'none';
                                 ['pp_discount_pct','pp_discount_amt'].forEach(function(id){
                                    document.getElementById(id).style.borderColor = '';
                                 });

                                 if (_activePPRow) {
                                    var pp  = parseFloat(_activePPRow.find('.pprice').val()) || 0;
                                    var qty = parseFloat(_activePPRow.find('.qtyval').val()) || 1;
                                    var pct = parseFloat(_activePPRow.find('.percent_discount').val()) || 0;
                                    var amt = parseFloat(_activePPRow.find('.discount_amt').val()) || 0;
                                    var totalPP  = pp * qty;
                                    var afterPP  = amt > 0 ? (totalPP - amt).toFixed(2) : totalPP.toFixed(2);

                                    document.getElementById('pp_purchase_price').value = totalPP.toFixed(2);
                                    document.getElementById('pp_discount_pct').value   = pct > 0 ? pct : '';
                                    document.getElementById('pp_discount_amt').value   = amt > 0 ? amt : '';
                                    document.getElementById('pp_after_pp').value       = afterPP;
                                 }

                                 document.getElementById('modernPopup-pur').classList.add('active');
                                 document.getElementById('pp_discount_pct').focus();
                              }

                              function closePopup1() {
                                 document.getElementById('modernPopup-pur').classList.remove('active');
                              }

                              /* Live sync: typing % -> recalc amount */
                              function syncPPFromPercent() {
                                 var pp  = parseFloat(document.getElementById('pp_purchase_price').value) || 0;
                                 var pct = parseFloat(document.getElementById('pp_discount_pct').value)   || 0;
                                 var amt = (pct / 100) * pp;
                                 document.getElementById('pp_discount_amt').value = amt.toFixed(2);
                                 document.getElementById('pp_after_pp').value     = (pp - amt).toFixed(2);
                              }

                              /* Live sync: typing amount -> recalc % */
                              function syncPPFromAmount() {
                                 var pp  = parseFloat(document.getElementById('pp_purchase_price').value) || 0;
                                 var amt = parseFloat(document.getElementById('pp_discount_amt').value)   || 0;
                                 var pct = pp > 0 ? (amt / pp) * 100 : 0;
                                 document.getElementById('pp_discount_pct').value = pct.toFixed(2);
                                 document.getElementById('pp_after_pp').value     = (pp - amt).toFixed(2);
                              }

                              function savePPDetails() {
                                 var pct = document.getElementById('pp_discount_pct').value.trim();
                                 var amt = document.getElementById('pp_discount_amt').value.trim();

                                 // Validate: at least one must be filled
                                 document.getElementById('pp_error_msg').style.display = 'none';
                                 ['pp_discount_pct','pp_discount_amt'].forEach(function(id){
                                    document.getElementById(id).style.borderColor = '';
                                 });

                                 if (pct === '' && amt === '') {
                                    document.getElementById('pp_error_text').textContent = 'Please enter Discount % or Discount Amount.';
                                    document.getElementById('pp_error_msg').style.display = 'flex';
                                    document.getElementById('pp_discount_pct').style.borderColor = '#e53935';
                                    document.getElementById('pp_discount_amt').style.borderColor = '#e53935';
                                    return;
                                 }

                                 if (!_activePPRow || _activePPRow.length === 0) {
                                    alert('Row not found. Please try again.');
                                    return;
                                 }

                                 // Write discount values into row hidden inputs
                                 _activePPRow.find('.percent_discount').val(pct !== '' ? parseFloat(pct).toFixed(2) : '0');
                                 _activePPRow.find('.discount_amt').val(amt !== '' ? parseFloat(amt).toFixed(2) : '0');

                                 // Get After Discount PP from popup (already live-synced)
                                 var afterPP = parseFloat(document.getElementById('pp_after_pp').value) || 0;
                                 _activePPRow.find('.after_pp').val(afterPP.toFixed(2));

                                 // Write After Discount PP directly into Net Amount column
                                 _activePPRow.find('.net_array').val(afterPP.toFixed(2));

                                 // Cascade: recalculate GST, Taxable Amount, Amount from Net Amount
                                 var bill_type = $('#gst_type').find(':selected').val();
                                 var gst = parseFloat(_activePPRow.find('.gst_array').val()) || 0;
                                 var gst_amount, amount;
                                 if (bill_type == 'Inclusive') {
                                    gst_amount = parseFloat(((afterPP * gst) / (100 + gst)).toFixed(2));
                                    amount     = parseFloat(afterPP.toFixed(2));
                                 } else if (bill_type == 'Exclusive') {
                                    gst_amount = parseFloat(((afterPP * gst) / 100).toFixed(2));
                                    amount     = parseFloat((afterPP + gst_amount).toFixed(2));
                                 } else {
                                    gst_amount = 0;
                                    amount     = parseFloat(afterPP.toFixed(2));
                                 }
                                 var taxable_amount = parseFloat((amount - gst_amount).toFixed(2));

                                 _activePPRow.find('.gstval').val(gst_amount.toFixed(2));
                                 _activePPRow.find('.taxable_amount').val(taxable_amount.toFixed(2));
                                 _activePPRow.find('.amount_array').val(amount.toFixed(2));

                                 updatePopupBillAmount();
                                 total_hidden_posted();

                                 // Flash green on Net Amount to confirm
                                 _activePPRow.find('.net_array').css('border-color','#009688');
                                 setTimeout(function(){ _activePPRow.find('.net_array').css('border-color',''); }, 1500);

                                 closePopup1();
                              }
                              </script>
                              <!--end pur-dis plus icon-->
                              
                              <!--start mrp-dis plus icon-->
                              <div class="modern-popup-mrp" id="modernPopup-mrp">
                                 <div class="popup-box">
                                    <button type="button" class="close-popup" onclick="closePopupmrp()">
                                       <i class="fa-solid fa-xmark"></i>
                                    </button>
                                    <h3 class="popup-title" style="text-align:center;">Add MRP Discount</h3>
                                    <div id="mrp_error_msg" style="display:none;background:#fff0f0;border:1px solid #f5c2c2;border-left:4px solid #e53935;border-radius:10px;padding:10px 14px;margin-bottom:12px;font-size:13px;color:#b71c1c;gap:8px;align-items:center;">
                                       <i class="fa-solid fa-circle-exclamation"></i>
                                       <span id="mrp_error_text"></span>
                                    </div>
                                    <div class="form-group-modern">
                                       <label>MRP</label>
                                       <input type="text" class="popup-input" id="mrp_current" placeholder="Current MRP" readonly style="background:#f5f5f5;">
                                    </div>
                                    <div class="form-group-modern">
                                       <label>Discount (%) <span style="color:red;">*</span></label>
                                       <input type="number" class="popup-input" id="mrp_discount_pct" placeholder="Enter Discount %" min="0" max="100" oninput="syncMRPFromPercent()">
                                    </div>
                                    <div class="form-group-modern">
                                       <label>Discount Amount <span style="color:red;">*</span></label>
                                       <input type="number" class="popup-input" id="mrp_discount_amt" placeholder="Enter Discount Amount" min="0" oninput="syncMRPFromAmount()">
                                    </div>
                                    <div class="form-group-modern">
                                       <label>Selling Price (After Discount)</label>
                                       <input type="text" class="popup-input" id="mrp_selling_price" placeholder="Selling Price" readonly style="background:#f5f5f5;font-weight:600;color:#009688;">
                                    </div>
                                    <div class="popup-actions">
                                       <button type="button" class="popup-btn cancel-btn" onclick="closePopupmrp()">Cancel</button>
                                       <button type="button" class="popup-btn save-btn" onclick="saveMRPDetails()">Save</button>
                                    </div>
                                 </div>
                              </div>

                              <script>
                              var _activeMRPRow = null;

                              function openPopupmrp(btn) {
                                 _activeMRPRow = btn ? $(btn).closest('tr') : null;
                                 document.getElementById('mrp_error_msg').style.display = 'none';
                                 ['mrp_discount_pct','mrp_discount_amt'].forEach(function(id){
                                    document.getElementById(id).value = '';
                                    document.getElementById(id).style.borderColor = '';
                                 });
                                 if (_activeMRPRow) {
                                    var mrp = parseFloat(_activeMRPRow.find('.mrp').val()) || 0;
                                    var dp  = parseFloat(_activeMRPRow.find('.discount_percent2').val()) || 0;
                                    var da  = parseFloat(_activeMRPRow.find('.discount_amount2').val()) || 0;
                                    var sp  = parseFloat(_activeMRPRow.find('.selling_price').val()) || mrp;
                                    document.getElementById('mrp_current').value = mrp.toFixed(2);
                                    if (dp > 0) document.getElementById('mrp_discount_pct').value = dp;
                                    if (da > 0) document.getElementById('mrp_discount_amt').value = da;
                                    document.getElementById('mrp_selling_price').value = sp.toFixed(2);
                                 }
                                 document.getElementById('modernPopup-mrp').classList.add('active');
                                 document.getElementById('mrp_discount_pct').focus();
                              }

                              function closePopupmrp() {
                                 document.getElementById('modernPopup-mrp').classList.remove('active');
                              }

                              function syncMRPFromPercent() {
                                 var mrp = parseFloat(document.getElementById('mrp_current').value) || 0;
                                 var pct = parseFloat(document.getElementById('mrp_discount_pct').value) || 0;
                                 var amt = (pct / 100) * mrp;
                                 document.getElementById('mrp_discount_amt').value = amt.toFixed(2);
                                 document.getElementById('mrp_selling_price').value = (mrp - amt).toFixed(2);
                              }

                              function syncMRPFromAmount() {
                                 var mrp = parseFloat(document.getElementById('mrp_current').value) || 0;
                                 var amt = parseFloat(document.getElementById('mrp_discount_amt').value) || 0;
                                 var pct = mrp > 0 ? (amt / mrp) * 100 : 0;
                                 document.getElementById('mrp_discount_pct').value = pct.toFixed(2);
                                 document.getElementById('mrp_selling_price').value = (mrp - amt).toFixed(2);
                              }

                              function saveMRPDetails() {
                                 var pct = document.getElementById('mrp_discount_pct').value.trim();
                                 var amt = document.getElementById('mrp_discount_amt').value.trim();
                                 document.getElementById('mrp_error_msg').style.display = 'none';
                                 ['mrp_discount_pct','mrp_discount_amt'].forEach(function(id){
                                    document.getElementById(id).style.borderColor = '';
                                 });

                                 if (pct === '' && amt === '') {
                                    document.getElementById('mrp_error_text').textContent = 'Please enter Discount % or Discount Amount.';
                                    document.getElementById('mrp_error_msg').style.display = 'flex';
                                    document.getElementById('mrp_discount_pct').style.borderColor = '#e53935';
                                    document.getElementById('mrp_discount_amt').style.borderColor = '#e53935';
                                    return;
                                 }
                                 if (!_activeMRPRow || _activeMRPRow.length === 0) { closePopupmrp(); return; }

                                 var sp = parseFloat(document.getElementById('mrp_selling_price').value) || 0;

                                 // Write discount values to row hidden inputs
                                 _activeMRPRow.find('.discount_percent2').val(pct !== '' ? parseFloat(pct).toFixed(2) : '0');
                                 _activeMRPRow.find('.discount_amount2').val(amt !== '' ? parseFloat(amt).toFixed(2) : '0');

                                 // Write Selling Price (after MRP discount) into SP column
                                 _activeMRPRow.find('.selling_price').val(sp.toFixed(2));

                                 // Flash green on SP column to confirm
                                 _activeMRPRow.find('.selling_price').css('border-color','#009688');
                                 setTimeout(function(){ _activeMRPRow.find('.selling_price').css('border-color',''); }, 1500);

                                 closePopupmrp();
                              }
                              </script>
                              <!--end mrp-dis plus icon-->
                              
                              <!--start popup-save button (popup div moved inside form below)--> 
                              <script>
                                 function openPopupsave(){
                                     updatePopupBillAmount();
                                     document.getElementById("modernPopup-save")
                                     .classList.add("active");
                                 }
                                 
                                 function closePopupsave(){
                                     document.getElementById("modernPopup-save")
                                     .classList.remove("active");
                                 }
                              </script>
                              <!--end popup-save button script-->
                              
                              
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




<!-- Main content -->

<section class="container-fluid">
   <div class="row">
      <!-- Form controls -->
      <div class="col-sm-12">
         <div class="panel lobidisable panel-bd">
            <div class="panel-heading sidebar-toggle" data-toggle="offcanvas">
               <div class="btn-group" id="buttonexport">
                  <a href="#">
                     <h5>Purchase Master Creations</h5>
                  </a>
               </div>
               <!-- <div class="btn-group" id="buttonlist"> 
                              <a class="btn btn-add " href="clist.html"> 
                              <span style="font-size: 13px;"><i class="fa fa-plus"></i>  User master list</span> </a>

                           </div> -->

            </div>
            <!--start sidebar-toggle-styles-->
                        <!-- SIDEPANEL -->
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
<!--end sidebar-toggle-styles-->

            <div class="panel-body form-scroll">
               <!-- <div class="btn-group">
                              <a href="Purchase-master-list.php"><button class="btn btn-exp btn-sm dropdown-toggle" data-toggle=""><i class="fa fa-users"></i><span style="margin-left:3px;">Purchase Master List !</span></button></a>
                           </div> -->
               <form method="post" name="purchage_form" id="purchage_form" enctype="multipart/form-data">

                  <!--start sidebar-toggle-->
                  <!-- TOGGLE BUTTON -->
                  <button type="button" class="filter-toggle-btn" id="filterToggle">
                  <i class="fa fa-sliders"></i> Additional Data
                  </button>
                  <!-- OVERLAY -->
                  <div class="filter-overlay" id="filterOverlay"></div>
                  <!-- SIDEPANEL (inside form so its inputs are submitted together) -->
                  <div class="leftside-panel" id="leftPanel">
                     <!-- CLOSE BUTTON -->
                     <div class="panel-top">
                        <h4>Additional Data</h4>
                        <button type="button" class="close-panel" id="closePanel">
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
                                       <input type="text" name="purchage_date" id="purchage_date" class="form-control datetimepicker" autocomplete="off" placeholder="Enter Date..." value="<?= dateformat($purchage_date); ?>">
                                    </div>
                                 </div>
                                 <!-- BILL NO -->
                                 <div class="col-md-6">
                                    <div class="modern-form-group">
                                       <label>
                                       Bill No.
                                       </label>
                                       <?php if ($bill_number) { ?>
                              <input type="text" name="bill_number" class="form-control" id="bill_number" readonly value="<?= $bill_number ?>">
                           <?php } else { ?>
                              <input type="text" name="bill_number" class="form-control" id="bill_number" readonly value="<?= generateBillNo('tbl_purchase', 'bill_number', 'BC') ?>">
                           <?php } ?>
                                    </div>
                                 </div>
                              </div>
                              <div class="row">
                                 <!-- S.code -->
                                 <div class="col-md-2">
                                    <div class="modern-form-group">
                                       <label>
                                       S.code
                                       </label>
                                       <input type="text"
                                          class="modern-input"
                                          name="s_code"
                                          placeholder="Enter S.Code">
                                    </div>
                                 </div>
                                 <!-- Store name -->
                                 <div class="col-md-5">
                                    <div class="modern-form-group">
                                       <label>
                                       Store name
                                       </label>
                                       <input type="text"
                                          class="modern-input"
                                          name="store_name"
                                          placeholder="Enter Store Name">
                                    </div>
                                 </div>
                                 <!-- Location -->
                                 <div class="col-md-5">
                                    <div class="modern-form-group">
                                       <label>
                                       Location
                                       </label>
                                       <input type="text"
                                          class="modern-input"
                                          name="location"
                                          placeholder="Enter Location">
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
                                       <select name="gst_type" id="gst_type" class="modern-select" required onchange="updateBillType()">
                                          <option value="Inclusive">Inclusive</option>
                                          <option value="Exclusive">Exclusive</option>
                                          <option value="None">None</option>
                                       </select>
                                    </div>
                                 </div>
                                 <!-- UNDER GST -->
                                 <div class="col-md-4 col-12">
                                    <div class="modern-form-group">
                                       <label>Under GST</label>
                                       <select name="under_gst" id="under_gst" class="modern-select" required>
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
                                          <option>Tax Invoice</option>
                                          <option>Estimate</option>
                                          <option>Cash Memo</option>
                                       </select>
                                    </div>
                                 </div>
                              </div>
                              <div class="row">
                                 <!-- DATE TIME -->
                                 <div class="col-md-6">
                                    <div class="modern-form-group">
                                       <label>
                                       Invoice Date & Time
                                       </label>
                                       <input type="text"
                                          class="modern-input"
                                          name="invoice_date"
                                          placeholder="Enter Invoice Date"
                                          value="<?= $invoice_date ?>">
                                    </div>
                                 </div>
                                 <!-- BILL NO -->
                                 <div class="col-md-6">
                                    <div class="modern-form-group">
                                       <label>
                                       Invoice No.
                                       </label>
                                       <input type="text"
                                          class="modern-input"
                                          name="invoice_number"
                                          placeholder="Enter Invoice Number"
                                          value="<?= $invoice_number ?>">
                                    </div>
                                 </div>
                              </div>
                             
                              <div class="row">
                                 <!-- DATE TIME -->
                                 <div class="col-md-4">
                                    <div class="modern-form-group">
                                       <label>
                                       Other Charge
                                       </label>
                                       <input type="text"
                                          class="modern-input"
                                          name="other_amount"
                                          placeholder="Enter Other Charge"
                                          value="<?= $other_amount ?>">
                                    </div>
                                 </div>
                                 <!-- BILL NO -->
                                 <div class="col-md-4">
                                    <div class="modern-form-group">
                                       <label>
                                       Labour charge
                                       </label>
                                       <input type="text"
                                          class="modern-input"
                                          name="labour_amount"
                                          placeholder="Enter Labour Charge"
                                          value="<?= $labour_amount ?>">
                                    </div>
                                 </div>
                                 <div class="col-md-4">
                                    <div class="modern-form-group">
                                       <label>
                                       Adjustment amount
                                       </label>
                                       <input type="text"
                                          class="modern-input"
                                          name="packing_amount"
                                          placeholder="Enter Adjustment Amount"
                                          value="<?= $packing_amount ?>">
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
                                          name="remarks"
                                          placeholder="Enter Remarks"
                                          value="<?= $remarks ?>">
                                    </div>
                                 </div>
                                 <!-- BILL NO -->
                              </div>
                              <!-- PUT YOUR FILTER CODE HERE -->
                           </div>
                        </div>

                  <div class="row">
                     <div class="col-sm-2">
                        <div class="form-group">
                           <label>Barcode No</label>
                           <input type="text" class="form-control" id="barcode_number" name="barcode_number" placeholder="Enter Barcode Number" autocomplete="off" value="<?= $barcode_number ?>">
                        </div>
                     </div>
                     <div class="col-sm-2">
                        <div class="form-group">
                           <label>Article No</label>
                           <input type="text" class="form-control" id="article_no" name="article_no" placeholder="Enter Article Number" autocomplete="off" value="<?= $article_number ?>">
                           <div id="suggestions_article_no" class="autocomplete-suggestions"></div>
                        </div>
                     </div>
                     <div class="col-sm-8">
                        <div class="form-group">
                           <label>Item Search / Name</label>
                           <input type="text" class="form-control" name="item_name" id="item_name" placeholder="Enter Item Name" autocomplete="off" value="<?= $item_name ?>">
                           <div id="suggestions_item_name" class="autocomplete-suggestions"></div>
                        </div>
                     </div>
                  </div>



                  <!-- Plugin content:powerpoint,txt,pdf,png,word,xl -->
                  <div class="table-responsive ">
                     <table class="custom-table table table-bordered table-striped table-hover">
                        <thead>
                           <tr class="info">
                              <th><input type="checkbox"></th>
                              <th>SN</th>
                              <th>Barcode No</th>
                              <th>Article No</th>
                              <th>Item Name</th>
                              <th>Group</th>
                              <th>Size</th>
                              <th>BBR QTY</th>
                              <th>QTY</th>
                              <th>MOU</th>
                              <th>PP</th>
                              <th>MRP</th>
                              <th>SP</th>
                              <th>Taxable Amount</th>
                              <th>GST %</th>
                              <th>GST Amount</th>
                              <th>Amount</th>
                           </tr>
                        </thead>
                        <tbody id="dataTable">
                           <?php
                           
                           $query = $db->select("SELECT * FROM tbl_purchase WHERE bill_number=?", 's', $bill_no);
                           $rowcount = $query->num_rows();
                           if ($rowcount > 0) {
                              
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
                                    <div class="checkbox checkbox-info" style="display:flex;align-items:center;gap:6px;">
                                       <input id="checkbox<?php echo $i; ?>" type="checkbox" value="<?= $item['id'] ?>">
                                       <label for="checkbox<?php echo $i; ?>">&nbsp;</label>
                                       <button type="button" class="row-delete-btn" onclick="deleteTableRow(this)" title="Delete Row" style="background:none;border:none;color:#e53935;cursor:pointer;padding:2px 5px;font-size:14px;"><i class="fa-solid fa-trash"></i></button>
                                    </div>
                                 </td>
                                 <td class="align-middle">
                                    <?= $i; ?>
                                    <input type="hidden" name="item_id[]" value="<?= $item['id'] ?>">
                                 </td>
                                 <td><input type="text" class="form-control valid" value="<?= $item['barcode_number']; ?>" name="barcode_no[]" required readonly></td>
                                 <td><input type="text" class="form-control valid" value="<?= $item['article_no']; ?>" name="article_no[]" required readonly></td>
                                 <td><input type="text" class="form-control valid" value="<?= $item['item_name']; ?>" name="item_name[]" required readonly></td>
                                  <td class="group-td">
                                     <div style="display:flex;gap:6px;align-items:center;">
                                        <input type="text" value="<?= $item['category']; ?>" class="form-control valid group-category-display" name="category[]" readonly title="<?= htmlspecialchars(($item['sub_category'] ?? '').' | '.($item['brand'] ?? '').' | '.($item['color'] ?? '').' | '.($item['style'] ?? '')) ?>">
                                        <button type="button" class="plus-btn" onclick="openPopup(this)" title="Add Group Details" style="margin-left:6px;"><i class="fa-solid fa-plus"></i></button>
                                     </div>
                                     <input type="hidden" name="sub_category[]" value="<?= htmlspecialchars($item['sub_category'] ?? '') ?>">
                                     <input type="hidden" name="brand[]"        value="<?= htmlspecialchars($item['brand'] ?? '') ?>">
                                     <input type="hidden" name="color[]"        value="<?= htmlspecialchars($item['color'] ?? '') ?>">
                                     <input type="hidden" name="style[]"        value="<?= htmlspecialchars($item['style'] ?? '') ?>">
                                  </td>
                                 <td><input type="text" value="<?= $item['size']; ?>" class="form-control valid" name="size[]" required readonly></td>
                                 <td><input type="number" value="" class="form-control valid" name="bbr_qty[]" autocomplete="off"></td>
                                 <td width="5%"><input type="number" value="<?= $item['qty']; ?>" class="form-control qtyval qty_array valid" name="qty[]" oninput="updateBillType()" required></td>
                                 <td>
                                    <select class="form-control" name="mou_name[]">
                                       <option value="<?= $item['mou_name']; ?>"><?= $item['mou_name']; ?></option>
                                       <option value="PCS">PCS</option>
                                       <option value="DOZ">DOZ</option>
                                       <option value="SET">SET</option>
                                    </select>
                                 </td>
                                  <td>
                                    <div style="display:flex;gap:6px;align-items:center;">
                                       <input type="text" class="form-control pprice valid" id="purchase_<?= $item['id']; ?>" name="purchase_price[]" oninput="updateBillType()" required value="<?= $item['purchase_price']; ?>">
                                       <button type="button" class="plus-btn" onclick="openPopup1(this)" title="Add PP Discount" style="margin-left:6px;"><i class="fa-solid fa-plus"></i></button>
                                    </div>
                                    <input type="hidden" class="after_pp" name="after_pp[]" value="<?= htmlspecialchars($item['after_pp'] ?? $item['purchase_price'] ?? '0') ?>">
                                    <input type="hidden" class="percent_discount" name="percent_discount[]" value="<?= htmlspecialchars($item['percent_discount'] ?? '0') ?>">
                                    <input type="hidden" class="discount_amt" name="discount_amt[]" value="<?= htmlspecialchars($item['discount_amt'] ?? '0') ?>">
                                  </td>
                                  <td>
                                    <div style="display:flex;gap:6px;align-items:center;">
                                       <input type="text" value="<?= $item['mrp']; ?>" class="form-control mrp valid" name="mrp[]" autocomplete="off">
                                       <button type="button" class="plus-btn" onclick="openPopupmrp(this)" title="Add MRP Discount" style="margin-left:6px;"><i class="fa-solid fa-plus"></i></button>
                                    </div>
                                    <input type="hidden" class="discount_percent2" name="discount_percent2[]" value="<?= htmlspecialchars($item['discount_percent2'] ?? '0') ?>">
                                    <input type="hidden" class="discount_amount2" name="discount_amount2[]" value="<?= htmlspecialchars($item['discount_amount2'] ?? '0') ?>">
                                  </td>
                                 <td><input type="text" value="<?= $item['selling_price'] ?? ''; ?>" class="form-control valid" name="selling_price[]" autocomplete="off"></td>
                                 <td><input type="text" value="<?= $item['taxable_amount']; ?>" class="form-control taxable_amount valid" id="taxable_amount<?= $item['id']; ?>" name="taxable_amount[]" required readonly></td>
                                 <td><input type="text" class="form-control gst_array valid" id="gst_<?= $item['id']; ?>" value="<?= $item['gst']; ?>" name="gst[]" readonly data-toggle="tooltip" title="<?= $gstTitle ?>"></td>
                                 <td><input type="text" value="<?= $item['gst_amount']; ?>" class="form-control gstval valid" id="gst_amount_<?= $item['id']; ?>" name="gst_amount[]" required readonly></td>
                                 
                                 <td><input type="text" value="<?= $item['amount']; ?>" class="form-control amount_array valid" id="amount<?= $item['id']; ?>" name="amount[]" required readonly></td>
                              </tr>
                           <?php } } ?>
                        </tbody>
                        <tfoot>
                           <tr class="success">
                              <td colspan="7"></td>
                              <td><label>Grand Total</label></td>
                              <td id="quantity_val">00.00</td>
                              <td colspan="4">&nbsp;</td>
                              <td>&nbsp;</td>
                              <td id="total_gst_amount2">00.00</td>
                              <td id="total_taxable_amount2">00.00</td>
                              <td>

                                 <div id="total_net_amount2">00.00</div>
                                 <hr>
                                 <div id="total_amount2">00.00</div>
                              </td>
                           </tr>
                        </tfoot>
                     </table>
                  </div>
                 
                  <div class="row">
                     
                      <div class="col-sm-3">
                        <div class="form-check">
                           <label>Status</label><br>
                           <label class="radio-inline">
                              <input type="radio" name="status" value="success" checked="checked">Success</label>
                           <label class="radio-inline"><input type="radio" name="status" value="hold">Hold</label>
                           <label class="radio-inline"><input type="radio" name="status" value="hold">Show Hold</label>
                        </div>
                     </div>
                     
                      <div class="col-sm-3">
                        <div class="form-group">
                           <label>Invoice Pdf upload</label>
                           <input type="file" name="picture" id="picture">

                        </div>
                     </div>
                     <?php if ($picture) { ?>
                        <div class="col col-3">
                           <div class="form-group">
                              <img src="<?= _UPLOAD_FILE_URL . 'purchase/' . $picture; ?>" width="300">
                              <input type="hidden" name="picture_name" id="" value="<?= $picture; ?>" class="">
                           </div>
                        </div>
                     <?php } ?>
                    
                     <div class="col-sm-3">
                        <div class="form-group">
                           <label>Discount %</label>
                           <input type="text" class="form-control" name="percent_total_discount" id="percent_total_discount" placeholder="Enter Discount %" oninput="discountTotalPercent()" value="<?= $percent_total_discount ?>">
                        </div>
                     </div>
                     <div class="col-sm-3">
                        <div class="form-group">
                           <label>Discount amount</label>
                           <input type="text" class="form-control" name="discount_total_amount" id="discount_total_amount" placeholder="Enter Discount amount" oninput="discountTotalAmount()" value="<?= $discount_total_amount ?>">
                        </div>
                     </div>
                  </div>
                
                 

                  <div class="row">
                      <div class="col-sm-4"></div>
                    
                     <div class="col-sm-8" style="text-align:right;">
                        <div class="reset-button">
                           <button type="reset" class="btn btn-warning" style="width:110px;">Reset</button>
                           <a class="btn btn-danger" style="width:110px;" onclick="deleteRow('dataTable')">Row Remove</a>
                           <button type="button" class="btn btn-success" onclick="openPopupsave()" id="confirmbtn" style="width:110px;background-color: #009688; color: white;"><?= (!empty($bill_no) ? 'Update' : 'Save') ?></button>
                        </div>
                     </div>
                  </div>

                  <!-- ===== PAYMENT MODE POPUP (inside form so data POSTs with main form) ===== -->
                  <div class="modern-popup-save" id="modernPopup-save">
                     <div class="popup-box">
                        <!-- CLOSE -->
                        <button type="button" class="close-popup" onclick="closePopupsave()">
                        <i class="fa-solid fa-xmark"></i>
                        </button>
                        <h3 class="popup-title" style="text-align:center;">Payment mode details</h3>

                        <!-- VALIDATION ERROR BANNER -->
                        <div id="popup_error_msg" style="display:none;background:#fff0f0;border:1px solid #f5c2c2;border-left:4px solid #e53935;border-radius:10px;padding:12px 14px;margin-bottom:14px;font-size:13px;color:#b71c1c;gap:10px;line-height:1.7;">
                           <div style="display:flex;align-items:flex-start;gap:8px;">
                              <i class="fa-solid fa-circle-exclamation" style="font-size:15px;flex-shrink:0;margin-top:3px;"></i>
                              <span id="popup_error_text"></span>
                           </div>
                        </div>

                        <!-- A/C suggestions dropdown -->
                        <div id="popup_ac_suggestions" style="display:none;position:absolute;z-index:9999;background:#fff;border:1px solid #ddd;border-radius:8px;max-height:160px;overflow-y:auto;width:90%;box-shadow:0 4px 12px rgba(0,0,0,0.1);font-size:13px;"></div>

                        <?php
                        $ac_stmt = $db->select("SELECT * FROM tbl_account_master WHERE mobile_no_1 =? OR account_name=?", 'ss', $mobile_number, $account_name);
                        $row_ac = $ac_stmt->fetch_assoc();

                        ?>

                  <div class="row">
   <div class="col-md-6">
      <div class="form-group-modern" style="position: relative;">
         <label>A/C Code</label>
         <input type="text"
            class="popup-input"
            id="popup_account_code"
            name="ac_code"
            placeholder="Enter A/C Code"
            autocomplete="off" value="<?= $row_ac['account_code']; ?>">
         <div id="suggestions_code" class="autocomplete-suggestions" style="display:none; position:absolute; left:0; top:100%; width:100%; z-index:1050; background:#fff; border:1px solid #ccc; max-height:200px; overflow-y:auto;"></div>
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
            autocomplete="off" value="<?= $row_ac['mobile_no_1']; ?>">
         <div id="suggestions_mobile" class="autocomplete-suggestions" style="display:none; position:absolute; left:0; top:100%; width:100%; z-index:1050; background:#fff; border:1px solid #ccc; max-height:200px; overflow-y:auto;"></div>
      </div>
   </div>
</div>

<div class="form-group-modern" style="position: relative; margin-top: 15px;">
   <label>A/C Name</label>
   <input type="text"
      class="popup-input"
      id="popup_account_name"
      name="account_name"
      placeholder="Enter A/C Name"
      autocomplete="off" value="<?= $row_ac['account_name']; ?>">
   <div id="suggestions_account_name" class="autocomplete-suggestions" style="display:none; position:absolute; left:0; top:100%; width:100%; z-index:1050; background:#fff; border:1px solid #ccc; max-height:200px; overflow-y:auto;"></div>
</div>

                        <div class="form-group-modern" style="text-align: center;">
                           <label>Your Bill Amount</label>
                           <span id="popup_bill_amount">0.00</span>
                        </div>
                        <div class="row">
                           <div class="form-group-modern">
                              <label class="popup-text">Payment Mode</label>
                              <select class="popup-select" name="payment_mode" id="payment_mode_select" onchange="handlePaymentMode(this.value)">
                                 <option value="">Select Option</option>
                                 <option value="CASH">CASH</option>
                                 <option value="UPI">UPI</option>
                                 <option value="CARD">CARD</option>
                                 <option value="CREDIT">CREDIT</option>
                                 <option value="DUAL">DUAL PAYMENT</option>
                              </select>
                           </div>
                        </div>
                        <div class="row">
                           <div class="col-md-6">
                              <div class="form-group-modern" id="field_cash">
                                 <label>CASH</label>
                                 <input type="text"
                                    class="popup-input"
                                    name="pay_cash"
                                    id="pay_cash"
                                    placeholder="Enter Cash Amount"
                                    disabled>
                              </div>
                           </div>
                           <div class="col-md-6">
                              <div class="form-group-modern" id="field_upi">
                                 <label>UPI</label>
                                 <input type="text"
                                    class="popup-input"
                                    name="pay_upi"
                                    id="pay_upi"
                                    placeholder="Enter UPI Amount"
                                    disabled>
                              </div>
                           </div>
                        </div>
                        <div class="row">
                           <div class="col-md-6">
                              <div class="form-group-modern" id="field_card">
                                 <label>CARD</label>
                                 <input type="text"
                                    class="popup-input"
                                    name="pay_card"
                                    id="pay_card"
                                    placeholder="Enter Card Amount"
                                    disabled>
                              </div>
                           </div>
                           <div class="col-md-6">
                              <div class="form-group-modern" id="field_credit">
                                 <label>CREDIT</label>
                                 <input type="text"
                                    class="popup-input"
                                    name="pay_credit"
                                    id="pay_credit"
                                    placeholder="Enter Credit Amount"
                                    disabled>
                              </div>
                           </div>
                        </div>
                        <script>
                        function handlePaymentMode(val) {
                           // disable & clear all first
                           ['pay_cash','pay_upi','pay_card','pay_credit'].forEach(function(id) {
                              var el = document.getElementById(id);
                              el.disabled = true;
                              el.value = '';
                              el.closest('.form-group-modern').style.opacity = '0.4';
                           });
                           // enable based on selection
                           var map = {
                              'CASH':   ['pay_cash'],
                              'UPI':    ['pay_upi'],
                              'CARD':   ['pay_card'],
                              'CREDIT': ['pay_credit'],
                              'DUAL':   ['pay_cash','pay_upi','pay_card','pay_credit']
                           };
                           if (map[val]) {
                              map[val].forEach(function(id) {
                                 var el = document.getElementById(id);
                                 el.disabled = false;
                                 el.closest('.form-group-modern').style.opacity = '1';
                                 if (map[val].length === 1) el.focus();
                              });
                           }
                        }
                        </script>
                        <!-- BUTTONS -->
                        <div class="popup-actions">
                           <button type="button" class="popup-btn cancel-btn" onclick="closePopupsave()">
                           Cancel
                           </button>
                           <button type="button" class="popup-btn save-btn" onclick="validateAndSubmitPopup()">
                           <?= (!empty($bill_no) ? 'Update' : 'Save') ?>
                           </button>
                        </div>
                        <!-- hidden submit trigger -->
                        <input type="hidden" id="popup_submit_val" name="form_action" value="<?= (!empty($bill_no) ? 'update' : 'save') ?>">
                     </div>
                  </div>
                  <!-- ===== END PAYMENT MODE POPUP ===== -->

                  <script>
                  function validateAndSubmitPopup() {
                     var errors = [];
                     var errorBanner = document.getElementById('popup_error_msg');
                     var errorText   = document.getElementById('popup_error_text');

                     // Reset all highlights
                     ['payment_mode_select','pay_cash','pay_upi','pay_card','pay_credit'].forEach(function(id){
                        var el = document.getElementById(id);
                        if (el) el.style.borderColor = '';
                     });
                     ['gst_type','under_gst','bill_type','purchage_date','bill_number','invoice_number'].forEach(function(nm){
                        var el = document.querySelector('[name="' + nm + '"]');
                        if (el) el.style.borderColor = '';
                     });

                     // ── 1. Main form required fields ──────────────────────────────────
                     var mainFields = [
                        { name: 'purchage_date',    label: 'Purchase Date'   },
                        { name: 'bill_number',      label: 'Bill No.'        },
                        // { name: 'invoice_number',   label: 'Invoice No.'     }
                     ];
                     var hasMain = false;
                     mainFields.forEach(function(f){
                        var el = document.querySelector('[name="' + f.name + '"]');
                        if (!el || el.value.trim() === '') {
                           errors.push(f.label + ' is required');
                           if (el) el.style.borderColor = '#e53935';
                           hasMain = true;
                        }
                     });

                     // ── 2. Additional Data sidebar: required selects ──────────────────
                     var sidebarFields = [
                        { name: 'gst_type',  label: 'GST Type'  },
                        { name: 'under_gst', label: 'Under GST' },
                        { name: 'bill_type', label: 'Bill Type' }
                     ];
                     var hasSidebar = false;
                     sidebarFields.forEach(function(f){
                        var el = document.querySelector('[name="' + f.name + '"]');
                        if (!el || el.value === '') {
                           errors.push(f.label + ' is required (Additional Data)');
                           if (el) el.style.borderColor = '#e53935';
                           hasSidebar = true;
                        }
                     });

                     // ── 3. At least one item row must exist ───────────────────────────
                     var rowCount = $('#dataTable tr').length;
                     if (rowCount < 1) {
                        errors.push('Please add at least one item');
                     }

                     // ── 4. Payment Mode mandatory ─────────────────────────────────────
                     var payMode = document.getElementById('payment_mode_select');
                     if (!payMode || payMode.value === '') {
                        errors.push('Payment Mode is required');
                        if (payMode) payMode.style.borderColor = '#e53935';
                     } else {
                        var modeMap = {
                           'CASH':   ['pay_cash'],
                           'UPI':    ['pay_upi'],
                           'CARD':   ['pay_card'],
                           'CREDIT': ['pay_credit'],
                           'DUAL':   ['pay_cash','pay_upi','pay_card','pay_credit']
                        };
                        var activeFields = modeMap[payMode.value] || [];
                        var anyFilled = false;
                        activeFields.forEach(function(id){
                           var el = document.getElementById(id);
                           if (el && el.value.trim() !== '') anyFilled = true;
                        });
                        if (!anyFilled) {
                           errors.push('Please enter at least one payment amount');
                           activeFields.forEach(function(id){
                              var el = document.getElementById(id);
                              if (el && !el.disabled) el.style.borderColor = '#e53935';
                           });
                        }
                     }

                     // ── Show banner or submit ─────────────────────────────────────────
                     if (errors.length > 0) {
                        var msg = errors.map(function(e){ return '• ' + e; }).join('<br>');
                        if (hasSidebar) {
                           msg += '<br><span style="font-size:12px;color:#777;margin-top:4px;display:block;">👉 Click <b>Additional Data</b> button to fill missing fields.</span>';
                        }
                        if (hasMain) {
                           msg += '<br><span style="font-size:12px;color:#777;margin-top:4px;display:block;">👉 Close this popup and fill the highlighted fields on the main form.</span>';
                        }
                        errorText.innerHTML = msg;
                        errorBanner.style.display = 'flex';
                        errorBanner.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                        return;
                     }

                     // All good — submit
                     errorBanner.style.display = 'none';
                     document.getElementById('purchage_form').submit();
                  }
                  </script>
               </form>
            </div>
         </div>
      </div>
   </div>
</section>
<!-- Main content -->
<section class="container-fluid">

   <div class="row">
      <div class="col-sm-12">
         <div class="panel panel-bd lobidrag">
            <div class="panel-heading">

               <div class="btn-group" id="buttonexport">
                  <a href="#">
                     <h5>Recent Purchase Bill</h5>
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
                  <a href="brand-master-creation.php"><button class="btn btn-exp btn-sm dropdown-toggle" data-toggle=""><i class="fa fa-users"></i><span style="margin-left:3px;">Purchase Bill Wise List !</span></button></a>
               </div>
               <div class="btn-group">
                  <button class="btn btn-danger btn-sm" onclick="deleteRow('latestRecord')"><i class="fa fa-trash"></i><span style="margin-left:3px;">Delete with Checkbox</span></button>
               </div>
               <!-- Plugin content:powerpoint,txt,pdf,png,word,xl -->
               <div class="table-responsive ">
                  <table id="dataTableEnable" class="table table-bordered table-striped table-hover">
                     <thead>
                        <tr class="info">
                           <th><input type="checkbox"></th>
                           <th>SrNo</th>
                           <th>Date</th>
                           <th>Bill No</th>
                           <th>Invoice No</th>
                           <th>Account Name</th>
                          <!--  <th>PayMode</th> -->
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

<script src="assets/dist/js/page/purchage-master.js" type="text/javascript"></script>
<!-- /.content -->
<script>
   function view_details(id) {
      $('#common-popup').load('popup/view-purchase-details.php?id=' + id,
         function() {

            $('#common-popup').modal('show');
         });
   }

   function deleterecord(id) {
      $('#common-popup').load('popup/deleterecord.php',
         function() {

            $('#common-popup').modal('show');
            $('#common-popup').modal('show').one('click', '#confirm', function() {

               $('#deleteid').val(id);
               $('#deleteform').submit();
            });
         });
   }
   function sendMail(id) {
      $('#common-popup').load('popup/send-mail.php?id='+id,
         function() {

            $('#common-popup').modal('show');
            $('#common-popup').modal('show').one('click', '#confirm', function() {

               $('#mailid').val(id);
               $('#sendmailform').submit();
            });
         });
   }
</script>

<!-- start sidebar-toggle-->
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
<!--end sidebar-toggle-->