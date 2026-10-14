<?php include_once("include/config.php"); ?>
<?php require_once(_BASEPATH . 'include/generatepdf.php'); 
?>
<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
?>
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
   $article_no             = $_POST['article_no'];
   $category               = $_POST['category'];
   $sub_category           = $_POST['sub_category'];
   $brand                  = $_POST['brand'];
   $color                  = $_POST['color'];
   $style                  = $_POST['style'];
   $size                   = $_POST['size'];
   $bbr_qty                = $_POST['bbr_qty'];
   $qty                    = $_POST['qty'];
   $mou_name               = $_POST['mou_name'];
   $rate                   = $_POST['rate'];
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
   $packing_amount         = $_POST['packing_amount'];
   $delivery_amount        = $_POST['delivery_amount'];
   $other_amount           = $_POST['other_amount'];
   $labour_amount          = $_POST['labour_amount'];
   $percent_total_discount = $_POST['percent_total_discount'];
   $discount_total_amount  = $_POST['discount_total_amount'];
   $invoice_number         = $_POST['invoice_number'];
   $invoice_date           = dateInSQLFormat($_POST['invoice_date']);
   $remarks                = $_POST['remarks'];
   $status                 = $_POST['status'];


   $current_date           = date("Y-m-d H:i:s");

   if ($_POST['submit'] == 'save') {


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
       
         $othergstvalue = getGSTValueBygstType($gst[$k]);
         $cgst = $othergstvalue;
         $sgst = $othergstvalue;

         $insertid = $db->insert('INSERT INTO tbl_purchase (
            purchage_date, bill_number, gst_type, under_gst, bill_type,
            mobile_number, account_name, item_name, barcode_number, item_type,
            article_no, category, sub_category, brand, color,
            style, size, bbr_qty, qty, mou_name,
            rate, purchase_price, percent_discount, discount_amt, after_pp,
            mrp, discount_percent2, discount_amount2, selling_price, net_price,
            gst, cgst, sgst, gst_amount, taxable_amount,
            amount, packing_amount, delivery_amount, other_amount, labour_amount,
            percent_total_discount, discount_total_amount, invoice_number, invoice_date,
            remarks, status, picture, created_date
            ) VALUES (
               ?,?,?,?,?,
               ?,?,?,?,?,
               ?,?,?,?,?,
               ?,?,?,?,?,
               ?,?,?,?,?,
               ?,?,?,?,?,
               ?,?,?,?,?,
               ?,?,?,?,?,
               ?,?,?,?,
               ?,?,?,?
            )',
            str_repeat('s', 48),
            $purchage_date, $bill_number, $gst_type, $under_gst, $bill_type,
            $mobile_number, $account_name, $item_name[$k], $barcode, $item_type[$k],
            $article_no[$k], $category[$k], $sub_category[$k], $brand[$k], $color[$k],
            $style[$k], $size[$k], $bbr_qty[$k], $qty[$k], $mou_name[$k],
            $rate[$k], $purchase_price[$k], $percent_discount[$k], $discount_amt[$k], $after_pp[$k],
            $mrp[$k], $discount_percent2[$k], $discount_amount2[$k], $selling_price[$k], $net_price[$k],
            $gst[$k], $cgst, $sgst, $gst_amount[$k], $taxable_amount[$k],
            $amount[$k], $packing_amount, $delivery_amount, $other_amount, $labour_amount,
            $percent_total_discount, $discount_total_amount, $invoice_number, $invoice_date,
            $remarks, $status, $picture, $current_date
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
   } else if ($_POST['submit'] == 'update') {

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
               mobile_number=?, account_name=?, item_name=?, barcode_number=?, item_type=?,
               article_no=?, category=?, sub_category=?, brand=?, color=?,
               style=?, size=?, bbr_qty=?, qty=?, mou_name=?,
               rate=?, purchase_price=?, percent_discount=?, discount_amt=?, after_pp=?,
               mrp=?, discount_percent2=?, discount_amount2=?, selling_price=?, net_price=?,
               gst=?, cgst=?, sgst=?, gst_amount=?, taxable_amount=?,
               amount=?, packing_amount=?, delivery_amount=?, other_amount=?, labour_amount=?,
               percent_total_discount=?, discount_total_amount=?, invoice_number=?, invoice_date=?,
               remarks=?, status=?, picture=?, updated_date=?
            WHERE id=?",
            str_repeat('s', 48) . 'i',
            $purchage_date, $gst_type, $under_gst, $bill_type,
            $mobile_number, $account_name, $item_name[$k], $barcode, $item_type[$k],
            $article_no[$k], $category[$k], $sub_category[$k], $brand[$k], $color[$k],
            $style[$k], $size[$k], $bbr_qty[$k], $qty[$k], $mou_name[$k],
            $rate[$k], $purchase_price[$k], $percent_discount[$k], $discount_amt[$k], $after_pp[$k],
            $mrp[$k], $discount_percent2[$k], $discount_amount2[$k], $selling_price[$k], $net_price[$k],
            $gst[$k], $cgst, $sgst, $gst_amount[$k], $taxable_amount[$k],
            $amount[$k], $packing_amount, $delivery_amount, $other_amount, $labour_amount,
            $percent_total_discount, $discount_total_amount, $invoice_number, $invoice_date,
            $remarks, $status, $picture, $current_date,
            $item_id[$k]
         );
         } else {


            $insertid = $db->insert(
            'INSERT INTO tbl_purchase (
               purchage_date, bill_number, gst_type, under_gst, bill_type,
               mobile_number, account_name, item_name, barcode_number, item_type,
               article_no, category, sub_category, brand, color,
               style, size, bbr_qty, qty, mou_name,
               rate, purchase_price, percent_discount, discount_amt, after_pp,
               mrp, discount_percent2, discount_amount2, selling_price, net_price,
               gst, cgst, sgst, gst_amount, taxable_amount,
               amount, packing_amount, delivery_amount, other_amount, labour_amount,
               percent_total_discount, discount_total_amount, invoice_number, invoice_date,
               remarks, status, picture, created_date
            ) VALUES (
               ?,?,?,?,?,
               ?,?,?,?,?,
               ?,?,?,?,?,
               ?,?,?,?,?,
               ?,?,?,?,?,
               ?,?,?,?,?,
               ?,?,?,?,?,
               ?,?,?,?,?,
               ?,?,?,?,
               ?,?,?,?
            )',
            str_repeat('s', 48),
            $purchage_date, $bill_number, $gst_type, $under_gst, $bill_type,
            $mobile_number, $account_name, $item_name[$k], $barcode, $item_type[$k],
            $article_no[$k], $category[$k], $sub_category[$k], $brand[$k], $color[$k],
            $style[$k], $size[$k], $bbr_qty[$k], $qty[$k], $mou_name[$k],
            $rate[$k], $purchase_price[$k], $percent_discount[$k], $discount_amt[$k], $after_pp[$k],
            $mrp[$k], $discount_percent2[$k], $discount_amount2[$k], $selling_price[$k], $net_price[$k],
            $gst[$k], $cgst, $sgst, $gst_amount[$k], $taxable_amount[$k],
            $amount[$k], $packing_amount, $delivery_amount, $other_amount, $labour_amount,
            $percent_total_discount, $discount_total_amount, $invoice_number, $invoice_date,
            $remarks, $status, $picture, $current_date
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

<div class="pull-right" style=" margin-right: 30px;margin-bottom: 10px; margin-top: 6px;">
   <a href="purchase-master-list.php"><button data-toggle="tooltip" title="Purchase Master List !" class="button-btn-btn-btn">Next !</button></a>
</div>
<div class="pull-right" style="margin-bottom: 10px; margin-top: 6px;">
   <a href="purchase-master-list.php"><button data-toggle="tooltip" title="Purchase Master List !" class="button-btn-btn">Previous !</button></a>
</div>


<!-- Main content -->
<section class="content">
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
            <div class="panel-body form-scroll">
               <!-- <div class="btn-group">
                              <a href="Purchase-master-list.php"><button class="btn btn-exp btn-sm dropdown-toggle" data-toggle=""><i class="fa fa-users"></i><span style="margin-left:3px;">Purchase Master List !</span></button></a>
                           </div> -->
               <form method="post" name="purchage_form" id="purchage_form" enctype="multipart/form-data">
                  <div class="form-group">
                     <?php echo $msg; ?>
                  </div>
                  <div class="row">
                     <div class="col-sm-2">
                        <div class="form-group">
                           <label>Date</label>
                           <input type="text" name="purchage_date" id="purchage_date" class="form-control datetimepicker" autocomplete="off" placeholder="Enter Date..." value="<?= dateformat($purchage_date); ?>">
                        </div>
                     </div>
                     <div class="col-sm-2">
                        <div class="form-group">
                           <label>Bill No</label>
                           <?php if ($bill_no) { ?>
                              <input type="text" name="bill_number" class="form-control" id="bill_number" readonly value="<?= $bill_number ?>">
                           <?php } else { ?>
                              <input type="text" name="bill_number" class="form-control" id="bill_number" readonly value="<?= generateBillNo('tbl_purchase', 'bill_number', 'BC') ?>">
                           <?php } ?>
                        </div>
                     </div>
                     <div class="col-sm-2">
                        <div class="form-group">
                           <label>Store Code</label>
                           <input type="text" name="store_code" class="form-control" id="store_code" placeholder="Store Code" autocomplete="off" value="<?= $store_code ?>">
                        </div>
                     </div>
                     <div class="col-sm-3">
                        <div class="form-group">
                           <label>Store Name</label>
                           <input type="text" name="store_name" class="form-control" id="store_name" placeholder="Store Name" autocomplete="off" value="<?= $store_name ?>">
                        </div>
                     </div>
                     <div class="col-sm-3">
                        <div class="form-group">
                           <label>Location</label>
                           <input type="text" name="location" class="form-control" id="location" placeholder="Location" autocomplete="off" value="<?= $location ?>">
                        </div>
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
                           <input type="text" class="form-control" id="article_number" name="article_number" placeholder="Enter Article Number" autocomplete="off" value="<?= $article_number ?>">
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
                              <th>SrNo</th>
                              <th>Barcode No</th>
                              <th>Article No</th>
                              <th>Item Name</th>
                              <th>Category</th>
                              <th>Sub Category</th>
                              <th>Brand</th>
                              <th>Colour</th>
                              <th>Size</th>
                              <th>Style</th>
                              <th>BBR QTY</th>
                              <th>QTY</th>
                              <th>MOU</th>
                              <th>Purchase Price</th>
                              <th>Discount %</th>
                              <th>Discount Amount</th>
                              <th>After PP</th>
                              <th>MRP</th>
                              <th>Discount %</th>
                              <th>Discount Amount</th>
                              <th>Selling Price</th>
                              <th>Net Amount</th>
                              <th>GST %</th>
                              <th>GST Amount</th>
                              <th>Taxable Amount</th>
                              <th>Amount</th>
                           </tr>
                        </thead>
                        <tbody id="dataTable">
                           <?php
                           $query = $db->select("SELECT * FROM tbl_purchase WHERE bill_number=?", 's', $bill_no);
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
                                       <label for="checkbox<?php echo $i; ?>">&nbsp;</label>
                                    </div>
                                 </td>
                                 <td class="align-middle">
                                    <?= $i; ?>
                                    <input type="hidden" name="item_id[]" value="<?= $item['id'] ?>">
                                 </td>
                                 <td><input type="text" class="form-control valid" value="<?= $item['barcode_number']; ?>" name="barcode_no[]" required readonly></td>
                                 <td><input type="text" class="form-control valid" value="<?= $item['item_type']; ?>" name="item_type[]" required readonly></td>
                                 <td><input type="text" class="form-control valid" value="<?= $item['item_name']; ?>" name="item_name[]" required readonly></td>
                                 <td><input type="text" value="<?= $item['category']; ?>" class="form-control valid" name="category[]" required readonly></td>
                                 <td><input type="text" value="" class="form-control valid" name="sub_category[]" autocomplete="off"></td>
                                 <td><input type="text" value="<?= $item['brand']; ?>" class="form-control valid" name="brand[]" required readonly></td>
                                 <td><input type="text" value="<?= $item['color']; ?>" class="form-control valid" name="color[]" required readonly></td>
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
                                 <td><input type="text" class="form-control pprice valid" id="purchase_<?= $item['id']; ?>" name="purchase_price[]" oninput="updateBillType()" required value="<?= $item['purchase_price']; ?>"></td>
                                 <td><input type="number" value="<?= $item['percent_discount']; ?>" class="form-control percent_discount valid" id="percent_discount_<?= $item['id']; ?>" name="percent_discount[]" oninput="calculateFromPercentage('<?= $item['barcode_number']; ?>')" required></td>
                                 <td><input type="number" value="<?= $item['discount_amt']; ?>" class="form-control discount_amt valid" id="discountamt_<?= $item['id']; ?>" name="discount_amt[]" oninput="calculateFromAmount('<?= $item['barcode_number']; ?>')" required></td>
                                 <td><input type="text" value="" class="form-control valid" name="after_pp[]" readonly></td>
                                 <td><input type="text" value="" class="form-control valid" name="mrp[]" autocomplete="off"></td>
                                 <td><input type="text" value="" class="form-control valid" name="discount_percent2[]" autocomplete="off"></td>
                                 <td><input type="text" value="" class="form-control valid" name="discount_amount2[]" autocomplete="off"></td>
                                 <td><input type="text" value="" class="form-control valid" name="selling_price[]" autocomplete="off"></td>
                                 <td><input type="text" value="<?= $item['net_price']; ?>" class="form-control net_array valid" id="net_price_<?= $item['id']; ?>" name="net_price[]" required readonly></td>
                                 <td><input type="text" class="form-control gst_array valid" id="gst_<?= $item['id']; ?>" value="<?= $item['gst']; ?>" name="gst[]" readonly data-toggle="tooltip" title="<?= $gstTitle ?>"></td>
                                 <td><input type="text" value="<?= $item['gst_amount']; ?>" class="form-control gstval valid" id="gst_amount_<?= $item['id']; ?>" name="gst_amount[]" required readonly></td>
                                 <td><input type="text" value="<?= $item['taxable_amount']; ?>" class="form-control taxable_amount valid" id="taxable_amount<?= $item['id']; ?>" name="taxable_amount[]" required readonly></td>
                                 <td><input type="text" value="<?= $item['amount']; ?>" class="form-control amount_array valid" id="amount<?= $item['id']; ?>" name="amount[]" required readonly></td>
                              </tr>
                           <?php } ?>
                        </tbody>
                        <tfoot>
                           <tr class="success">
                              <td colspan="10"></td>
                              <td colspan="2"><label>Grand Total<label></td>
                              <td id="quantity_val">00.00</td>
                              <td colspan="9">&nbsp;</td>
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
                           <label>GST Type</label>
                           <?php
                           $gst_type_arr = ['Exclusive', 'Inclusive', 'None'];
                           ?>
                           <select name="gst_type" id="gst_type" class="form-control" onchange="updateBillType()">
                              <?php foreach ($gst_type_arr as $key => $val) { ?>
                                 <option value="<?= $val ?>" <?= ($val == $gst_type ? 'selected' : '') ?>><?= $val ?></option>
                              <?php } ?>
                           </select>
                        </div>
                     </div>
                     <div class="col-sm-2">
                        <div class="form-group">
                           <label>Under GST</label>
                           <?php
                           $under_gst_arr = ['Local', 'Inter'];
                           ?>
                           <select name="under_gst" id="under_gst" class="form-control" onchange="change_under_gst()">
                              <?php foreach ($under_gst_arr as $key => $val) { ?>
                                 <option value="<?= $val ?>" <?= ($val == $under_gst ? 'selected' : '') ?>><?= $val ?></option>
                              <?php } ?>
                           </select>
                        </div>
                     </div>
                     <div class="col-sm-2">
                        <div class="form-group">
                           <label>Bill Type</label>
                           <?php
                           $bill_type_arr = ['Tax-Bill', 'Estimate', 'Cash-Memo'];
                           ?>
                           <select name="bill_type" id="bill_type" class="form-control">
                              <?php foreach ($bill_type_arr as $key => $val) { ?>
                                 <option value="<?= $val ?>" <?= ($val == $bill_type ? 'selected' : '') ?>><?= beautify($val) ?></option>
                              <?php } ?>
                           </select>
                        </div>
                     </div>
                     <div class="col-sm-3">
                        <div class="form-group">
                           <label>Invoice Date</label>
                           <div class="input-group date form_date redate">
                              <input id='invoice_date' type="text" name="invoice_date" class="form-control datetimepicker" value="<?= dateformat($invoice_date) ?>" autocomplete="off">
                           </div>
                        </div>
                     </div>
                     <div class="col-sm-3">
                        <div class="form-group">
                           <label>Invoice No</label>
                           <input type="text" class="form-control" name="invoice_number" id="invoice_number" placeholder="Enter Invoice No" value="<?= $invoice_number ?>">
                        </div>
                     </div>
                  </div>

                  <div class="row">
                     <div class="col-sm-4">
                        <div class="form-group">
                           <label>Account Code</label>
                           <input type="text" class="form-control" name="account_code" id="account_code" placeholder="Enter Account Code" autocomplete="off" value="<?= $account_code ?>">
                        </div>
                     </div>
                     <div class="col-sm-4">
                        <div class="form-group">
                           <label>Account Name</label>
                           <input type="text" class="form-control" name="account_name" id="account_name" placeholder="Enter Account Name" autocomplete="off" value="<?= $account_name ?>">
                           <div id="suggestions_account_name" class="autocomplete-suggestions"></div>
                        </div>
                     </div>
                     <div class="col-sm-4">
                        <div class="form-group">
                           <label>Mobile No</label>
                           <input type="text" class="form-control" name="mobile_number" id="mobile_number" placeholder="Enter Mobile No" autocomplete="off" value="<?= $mobile_number ?>">
                           <div id="suggestions_mobile" class="autocomplete-suggestions"></div>
                        </div>
                     </div>
                  </div>
                  <div class="row pt-2" id="emp-details">
                     <?php if (!empty($bill_no)) { ?>
                        <?php
                        $ac_stmt = $db->select("SELECT * FROM tbl_account_master WHERE mobile_no_1 =? OR account_name=?", 'ss', $mobile_number, $account_name);
                        $row_ac = $ac_stmt->fetch_assoc();

                        ?>

                        <div class="col-md-4"><label for="">Account Code: </label> <?= $row_ac['account_code']; ?></div>
                        <div class="col-md-4"><label for="">Account Name: </label> <?= $row_ac['account_name']; ?></div>
                        <div class="col-md-4"><label for="">Contact Name: </label> <?= $row_ac['contact_name']; ?></div>
                        <div class="col-md-4"><label for="">Group Name: </label> <?= $row_ac['group_name']; ?></div>
                        <div class="col-md-4"><label for="">Mobile No. 1: </label><?= $row_ac['mobile_no_1']; ?></div>
                        <div class="col-md-4"><label for="">Mobile No. 2: </label><?= $row_ac['mobile_no_2']; ?></div>

                     <?php } ?>
                  </div>
                  <div class="row">
                     <input type="hidden" name="packing_amount" value="<?= isset($packing_amount) ? $packing_amount : '0' ?>">
                     <div class="col-sm-2">
                        <div class="form-group">
                           <label>Other Charge</label>
                           <input type="text" class="form-control extra-charge" name="other_amount" id="other_amount" placeholder="Enter Other Charge" value="<?= $other_amount ?>">
                        </div>
                     </div>
                     <div class="col-sm-2">
                        <div class="form-group">
                           <label>Labour charge</label>
                           <input type="text" class="form-control extra-charge" name="labour_amount" id="labour_amount" placeholder="Enter Labour charge" value="<?= $labour_amount ?>">
                        </div>
                     </div>
                     <div class="col-sm-2">
                        <div class="form-group">
                           <label>Adjustment amount</label>
                           <input type="text" class="form-control extra-charge" name="delivery_amount" id="delivery_amount" placeholder="Enter Adjustment amount" value="<?= $delivery_amount ?>">
                        </div>
                     </div>
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
                     <div class="col-sm-4">
                        <div class="form-group">
                           <label>Invoice No</label>
                           <input type="text" class="form-control" name="invoice_number" id="invoice_number" placeholder="Enter Invoice No" value="<?= $invoice_number ?>">
                        </div>
                     </div>
                     <div class="col-sm-4">
                        <div class="form-group">
                           <label>Invoice Date</label>
                           <div class=" input-group date form_date redate">
                              <input id='invoice_date' type="text" name="invoice_date" class="form-control datetimepicker" value="<?= dateformat($invoice_date) ?>" autocomplete="off">
                           </div>
                        </div>
                     </div>
                     <div class="col-sm-4">
                        <div class="form-group">
                           <label>Picture upload</label>
                           <input type="file" name="picture" id="picture">

                        </div>
                     </div>
                     <?php if ($picture) { ?>
                        <div class="col col-4">
                           <div class="form-group">
                              <img src="<?= _UPLOAD_FILE_URL . 'purchase/' . $picture; ?>" width="300">
                              <input type="hidden" name="picture_name" id="" value="<?= $picture; ?>" class="">
                           </div>
                        </div>
                     <?php } ?>
                  </div>
                  <div class="row">

                     <div class="col-sm-12">
                        <div class="form-group">
                           <label>Remarks</label>
                           <input type="text" class="form-control" name="remarks" id="remarks" placeholder="Enter Brand Name" value="<?= $remarks ?>">
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
                        </div>
                     </div>
                     <div class="col-sm-8" style="text-align:right;">
                        <div class="reset-button">
                           <input type="hidden" name="submit" value="<?= (!empty($bill_no) ? 'update' : 'save') ?>">
                           <button type="reset" name="submit" class="btn btn-warning" style="width:110px;">Reset</button>
                           <a class="btn btn-danger" style="width:110px;" onclick="deleteRow('dataTable')">Row Remove</a>
                           <button class="btn btn-success" id="confirmbtn" style="width:110px;background-color: #009688; color: white;"><?= (!empty($bill_no) ? 'Update' : 'Save') ?></button>
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
<section class="content">

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
