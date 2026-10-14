<?php 
if (!function_exists('connect')) {
    include_once(__DIR__ . "/../include/config.php");
}
?>
<?php
$validationHelper = new validation();
$db = connect();

$id = (int)($_GET['id'] ?? 0);
$item = array();
if ($id > 0) {
    $stmt = $db->select("SELECT * FROM tbl_store_items WHERE id = ? LIMIT 1", 'i', array($id));
    if ($stmt && $stmt->num_rows() > 0) {
        $item = $stmt->fetch_assoc();
    }
}
?>
<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header modal-header-primary">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h4 class="modal-title" style="color:#fff; font-size:16px; font-weight:600;"><i class="fa fa-eye" style="margin-right:8px;"></i> View Store Item Details</h4>
        </div>
        <div class="modal-body" style="padding: 20px;">
            <?php if (!empty($item)) { ?>
            <div class="row">
                <div class="col-md-3 col-sm-6" style="margin-bottom:12px;">
                    <label class="control-label" style="font-weight:600; color:#555;">Store Name:</label>
                    <p class="form-control-static"><?= htmlspecialchars($item['store_name'] ?? 'N/A') ?></p>
                </div>
                <div class="col-md-3 col-sm-6" style="margin-bottom:12px;">
                    <label class="control-label" style="font-weight:600; color:#555;">Status:</label>
                    <p class="form-control-static">
                        <?php if (($item['status'] ?? '') == 'moved in stock') { ?>
                            <span class="label label-warning">moved in stock</span>
                        <?php } else { ?>
                            <span class="label label-success"><?= htmlspecialchars($item['status'] ?? 'active') ?></span>
                        <?php } ?>
                    </p>
                </div>
                <div class="col-md-3 col-sm-6" style="margin-bottom:12px;">
                    <label class="control-label" style="font-weight:600; color:#555;">Transfer / Return Date:</label>
                    <p class="form-control-static"><?= dateformat($item['updated_date'] ?? $item['created_date'] ?? '') ?></p>
                </div>
                <div class="col-md-3 col-sm-6" style="margin-bottom:12px;">
                    <label class="control-label" style="font-weight:600; color:#555;">Bill Number:</label>
                    <p class="form-control-static"><?= htmlspecialchars($item['bill_number'] ?? 'N/A') ?></p>
                </div>

                <div class="col-md-3 col-sm-6" style="margin-bottom:12px;">
                    <label class="control-label" style="font-weight:600; color:#555;">Invoice Number:</label>
                    <p class="form-control-static"><?= htmlspecialchars($item['invoice_number'] ?? 'N/A') ?></p>
                </div>
                <div class="col-md-3 col-sm-6" style="margin-bottom:12px;">
                    <label class="control-label" style="font-weight:600; color:#555;">Party / Account Name:</label>
                    <p class="form-control-static"><?= htmlspecialchars($item['account_name'] ?? 'N/A') ?></p>
                </div>
                <div class="col-md-3 col-sm-6" style="margin-bottom:12px;">
                    <label class="control-label" style="font-weight:600; color:#555;">Barcode Number:</label>
                    <p class="form-control-static"><?= htmlspecialchars($item['barcode_number'] ?? 'N/A') ?></p>
                </div>
                <div class="col-md-3 col-sm-6" style="margin-bottom:12px;">
                    <label class="control-label" style="font-weight:600; color:#555;">Item Name:</label>
                    <p class="form-control-static"><strong><?= htmlspecialchars($item['item_name'] ?? 'N/A') ?></strong></p>
                </div>

                <div class="col-md-3 col-sm-6" style="margin-bottom:12px;">
                    <label class="control-label" style="font-weight:600; color:#555;">Category:</label>
                    <p class="form-control-static"><?= htmlspecialchars($item['category'] ?? 'N/A') ?></p>
                </div>
                <div class="col-md-3 col-sm-6" style="margin-bottom:12px;">
                    <label class="control-label" style="font-weight:600; color:#555;">Brand:</label>
                    <p class="form-control-static"><?= htmlspecialchars($item['brand'] ?? 'N/A') ?></p>
                </div>
                <div class="col-md-3 col-sm-6" style="margin-bottom:12px;">
                    <label class="control-label" style="font-weight:600; color:#555;">Size:</label>
                    <p class="form-control-static"><?= htmlspecialchars($item['size'] ?? 'N/A') ?></p>
                </div>
                <div class="col-md-3 col-sm-6" style="margin-bottom:12px;">
                    <label class="control-label" style="font-weight:600; color:#555;">Quantity:</label>
                    <p class="form-control-static"><strong><?= htmlspecialchars($item['qty'] ?? '0') ?></strong> <?= htmlspecialchars($item['mou_name'] ?? '') ?></p>
                </div>

                <div class="col-md-3 col-sm-6" style="margin-bottom:12px;">
                    <label class="control-label" style="font-weight:600; color:#555;">Purchase Price:</label>
                    <p class="form-control-static"><?= htmlspecialchars($item['purchase_price'] ?? '0.00') ?></p>
                </div>
                <div class="col-md-3 col-sm-6" style="margin-bottom:12px;">
                    <label class="control-label" style="font-weight:600; color:#555;">Selling Price:</label>
                    <p class="form-control-static"><?= htmlspecialchars($item['selling_price'] ?? '0.00') ?></p>
                </div>
                <div class="col-md-3 col-sm-6" style="margin-bottom:12px;">
                    <label class="control-label" style="font-weight:600; color:#555;">MRP:</label>
                    <p class="form-control-static"><?= htmlspecialchars($item['mrp'] ?? '0.00') ?></p>
                </div>
                <div class="col-md-3 col-sm-6" style="margin-bottom:12px;">
                    <label class="control-label" style="font-weight:600; color:#555;">GST %:</label>
                    <p class="form-control-static"><?= htmlspecialchars($item['gst'] ?? '0') ?> %</p>
                </div>

                <div class="col-md-3 col-sm-6" style="margin-bottom:12px;">
                    <label class="control-label" style="font-weight:600; color:#555;">GST Amount:</label>
                    <p class="form-control-static"><?= htmlspecialchars($item['gst_amount'] ?? '0.00') ?></p>
                </div>
                <div class="col-md-3 col-sm-6" style="margin-bottom:12px;">
                    <label class="control-label" style="font-weight:600; color:#555;">Taxable Amount:</label>
                    <p class="form-control-static"><?= htmlspecialchars($item['taxable_amount'] ?? '0.00') ?></p>
                </div>
                <div class="col-md-3 col-sm-6" style="margin-bottom:12px;">
                    <label class="control-label" style="font-weight:600; color:#555;">Total Amount:</label>
                    <p class="form-control-static"><strong><?= htmlspecialchars($item['amount'] ?? '0.00') ?></strong></p>
                </div>
                <div class="col-md-3 col-sm-6" style="margin-bottom:12px;">
                    <label class="control-label" style="font-weight:600; color:#555;">Purchase ID:</label>
                    <p class="form-control-static"><?= htmlspecialchars($item['purchase_id'] ?? 'N/A') ?></p>
                </div>
            </div>
            <?php } else { ?>
                <div class="alert alert-warning text-center">No record found.</div>
            <?php } ?>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
    </div>
</div>
