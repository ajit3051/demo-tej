<?php include_once("../include/config.php"); ?>
<?php
$validationHelper = new validation();

$db = connect();

$id = $_GET['id'];

$pur_stmt = $db->select("SELECT * FROM tbl_bill WHERE id=?", 'i', $id);

$res = $pur_stmt->fetch_assoc();

foreach ($res as $key => $value) {
	$$key = $validationHelper->filterText($value);
}
$pur_stmt->close();
?>
<div class="modal-dialog modal-lg">
	<div class="modal-content">
		<div class="modal-header modal-header-primary">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
			<h3>View Order Deatils</h3>
		</div>
		<div class="modal-body">
			<div class="row">
				<div class="col-md-12">
					<form class="form-horizontal">
						<fieldset>
							<!-- Text input-->
							<div class="col-md-3">
								<label class="control-label">Date:</label>
								<p><?= dateformat($purchage_date) ?></p>
							</div>
							<!-- Text input-->
							<div class="col-md-3">
								<label class="control-label">Bill No:</label>
								<p><?= $bill_number ?></p>
							</div>
							<!-- Text input-->
							<div class="col-md-3">
								<label class="control-label">GST Type</label>
								<p><?= $gst_type ?></p>
							</div>
							<div class="col-md-3">
								<label class="control-label">Under GST</label><br>
								<p><?= $under_gst ?></p>
							</div>
							<div class="col-md-3">
								<label class="control-label">Bill Type</label>
								<p><?= $bill_type ?></p>
							</div>
							<div class="col-md-3">
								<label class="control-label">Mobile No</label>
								<p><?= $mobile_number ?></p>
							</div>
							<div class="col-md-3">
								<label class="control-label">Account Name</label>
								<p><?= $account_name ?></p>
							</div>
							<div class="col-md-3">
								<label class="control-label">Barcode No</label>
								<p><?= $barcode_number ?></p>
							</div>
							<div class="col-md-3">
								<label class="control-label">Item Name</label>
								<p><?= $item_name ?></p>
							</div>
							<div class="col-md-3">
								<label class="control-label">Brand:</label>
								<p><?= $brand ?></p>
							</div>
							<div class="col-md-3">
								<label class="control-label">Color:</label>
								<p><?= $color ?></p>
							</div>
							<div class="col-md-3">
								<label class="control-label">Size:</label>
								<p><?= $size ?></p>
							</div>
							<div class="col-md-3">
								<label class="control-label">QTY:</label>
								<p><?= $qty ?></p>
							</div>
							<div class="col-md-3">
								<label class="control-label">MOU:</label>
								<p><?= $mou_name ?></p>
							</div>
							<div class="col-md-3">
								<label class="control-label">RATE:</label>
								<p><?= $rate ?></p>
							</div>
							<div class="col-md-3">
								<label class="control-label">Purchase Price:</label>
								<p><?= $purchase_price ?></p>
							</div>
							<div class="col-md-3">
								<label class="control-label">Discount %:</label>
								<p><?= $percent_discount ?></p>
							</div>
							<div class="col-md-3">
								<label class="control-label">Discount Amountt:</label>
								<p><?= $discount_amt ?></p>
							</div>
							<div class="col-md-3">
								<label class="control-label">Net Amt:</label>
								<p><?= $net_price ?></p>
							</div>
							<div class="col-md-3">
								<label class="control-label">GST %:</label>
								<p><?= $gst ?></p>
							</div>
							<div class="col-md-3">
								<label class="control-label">GST Amt:</label>
								<p><?= $gst_amount ?></p>
							</div>
							<div class="col-md-3">
								<label class="control-label">Taxable Amt:</label>
								<p><?= $taxable_amount ?></p>
							</div>
							<div class="col-md-3">
								<label class="control-label">Amount:</label>
								<p><?= $amount ?></p>
							</div>
							<div class="col-md-3">
								<label class="control-label">Packing Amt:</label>
								<p><?= $packing_amount ?></p>
							</div>
							<div class="col-md-3">
								<label class="control-label">Delivery Amt:</label>
								<p><?= $delivery_amount ?></p>
							</div>
							<div class="col-md-3">
								<label class="control-label">Other Amt:</label>
								<p><?= $other_amount ?></p>
							</div>
							<div class="col-md-3">
								<label class="control-label">Labour Amt:</label>
								<p><?= $labour_amount ?></p>
							</div>
							<div class="col-md-3">
								<label class="control-label">Discount %:</label>
								<p><?= $percent_total_discount ?></p>
							</div>
							<div class="col-md-3">
								<label class="control-label">Discount Amt:</label>
								<p><?= $discount_total_amount ?></p>
							</div>
							<div class="col-md-3">
								<label class="control-label">Invoice No:</label>
								<p><?= $invoice_number ?></p>
							</div>
							<div class="col-md-3">
								<label class="control-label">Invoice Date:</label>
								<p><?= dateformat($invoice_date) ?></p>
							</div>
							<div class="col-md-3">
								<label class="control-label">Picture uploaded:</label>
								<p><?php if ($picture) { ?>
										<img src="<?= _UPLOAD_FILE_URL . 'purchase/' . $picture; ?>" width="300">
									<?php } ?>
								</p>
							</div>
							<div class="col-md-3">
								<label class="control-label">Remarks:</label>
								<p><?= $remarks ?></p>
							</div>
							<div class="col-md-3">
								<label class="control-label">Status:</label>
								<p> <?php if ($status == 'success') { ?>
                                    <span class="label label-success"><?= $status; ?></span>
                                 <?php } else { ?>
                                    <span class="label label-warning"><?= $status; ?></span>
                                 <?php } ?></p>
							</div>
						</fieldset>
					</form>
				</div>
			</div>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
		</div>
	</div>
	<!-- /.modal-content -->
</div>