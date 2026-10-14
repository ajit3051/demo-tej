<?php include_once("../include/config.php"); ?>
<?php
$validationHelper = new validation();

$db = connect();

$id = $_GET['id'] ?? 0;
$bill_no = $_GET['bill_no'] ?? $_GET['bill_number'] ?? '';

$res = null;
if (!empty($bill_no)) {
    $pur_stmt = $db->select("SELECT * FROM tbl_bill WHERE bill_number=? ORDER BY id ASC LIMIT 1", 's', $bill_no);
    if ($pur_stmt) {
        $res = $pur_stmt->fetch_assoc();
        $pur_stmt->close();
    }
} elseif ($id > 0) {
    $pur_stmt = $db->select("SELECT * FROM tbl_bill WHERE id=?", 'i', $id);
    if ($pur_stmt) {
        $res = $pur_stmt->fetch_assoc();
        $pur_stmt->close();
    }
}

if ($res) {
    foreach ($res as $key => $value) {
        $$key = $validationHelper->filterText($value);
    }
}

$bill_number = $bill_number ?? $bill_no;
$items = [];
if (!empty($bill_number)) {
    $items_stmt = $db->select("SELECT * FROM tbl_bill WHERE bill_number=? ORDER BY id ASC", 's', $bill_number);
    if ($items_stmt) {
        while ($item_row = $items_stmt->fetch_assoc()) {
            $items[] = $item_row;
        }
        $items_stmt->close();
    }
}
if (empty($items) && $res) {
    $items[] = $res;
}

$total_qty = 0;
$total_net = 0;
$total_gst_amt = 0;
$total_taxable = 0;
$total_amount = 0;

foreach ($items as $item) {
    $total_qty     += (float)($item['qty'] ?? 0);
    $total_net     += (float)($item['net_price'] ?? 0);
    $total_gst_amt += (float)($item['gst_amount'] ?? 0);
    $total_taxable += (float)($item['taxable_amount'] ?? 0);
    $total_amount  += (float)($item['amount'] ?? 0);
}

$purchage_date = !empty($purchage_date) ? dateformat($purchage_date) : date('d-m-Y');
$account_name  = !empty($account_name) ? $account_name : 'Walk-in Customer';
$mobile_number = !empty($mobile_number) ? $mobile_number : 'N/A';
$pay_mode      = !empty($pay_mode) ? getPayModeByCode($pay_mode) : 'CASH';
$under_gst     = $under_gst ?? 'Local';
$gst_type      = $gst_type ?? 'Exclusive';
$bill_type     = $bill_type ?? 'Tax-Bill';
?>

<div class="modal-dialog modal-lg" style="max-width: 980px; width: 95%;">
    <div class="modal-content" style="border-radius: 12px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.25); border: none;">
        <div class="modal-header" style="background: linear-gradient(135deg, #009688, #00796b); color: #fff; padding: 14px 22px;">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="color: #fff; opacity: 0.9; font-size: 22px;">×</button>
            <h4 class="modal-title" style="margin:0; font-weight:700; font-size: 18px; display: flex; align-items: center; gap: 8px;">
                <i class="fa fa-list-alt"></i> View Sale Details (Bill No: <?= htmlspecialchars($bill_number) ?>)
            </h4>
        </div>
        
        <div class="modal-body" style="background-color: #f8fafc; padding: 20px;">
            
            <!-- Bill Header Information Grid -->
            <div style="background: #ffffff; border-radius: 10px; padding: 18px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); margin-bottom: 20px; border: 1px solid #e2e8f0;">
                <h5 style="margin-top:0; margin-bottom: 14px; font-weight:700; color: #1e293b; border-bottom: 2px solid #009688; padding-bottom: 6px; display: inline-block;">
                    <i class="fa fa-info-circle"></i> Bill Summary Information
                </h5>
                <div class="row">
                    <div class="col-md-3 col-sm-6" style="margin-bottom: 12px;">
                        <span style="font-size: 12px; color: #64748b; font-weight: 600; display: block;">Date:</span>
                        <strong style="font-size: 14px; color: #0f172a;"><?= $purchage_date ?></strong>
                    </div>
                    <div class="col-md-3 col-sm-6" style="margin-bottom: 12px;">
                        <span style="font-size: 12px; color: #64748b; font-weight: 600; display: block;">Bill Number:</span>
                        <strong style="font-size: 14px; color: #009688;"><?= htmlspecialchars($bill_number) ?></strong>
                    </div>
                    <div class="col-md-3 col-sm-6" style="margin-bottom: 12px;">
                        <span style="font-size: 12px; color: #64748b; font-weight: 600; display: block;">Account Name:</span>
                        <strong style="font-size: 14px; color: #0f172a;"><?= htmlspecialchars($account_name) ?></strong>
                    </div>
                    <div class="col-md-3 col-sm-6" style="margin-bottom: 12px;">
                        <span style="font-size: 12px; color: #64748b; font-weight: 600; display: block;">Mobile Number:</span>
                        <strong style="font-size: 14px; color: #0f172a;"><?= htmlspecialchars($mobile_number) ?></strong>
                    </div>
                    
                    <div class="col-md-3 col-sm-6" style="margin-bottom: 12px;">
                        <span style="font-size: 12px; color: #64748b; font-weight: 600; display: block;">GST Type:</span>
                        <span class="label label-info" style="font-size: 12px; font-weight:600;"><?= htmlspecialchars($gst_type) ?></span>
                    </div>
                    <div class="col-md-3 col-sm-6" style="margin-bottom: 12px;">
                        <span style="font-size: 12px; color: #64748b; font-weight: 600; display: block;">Under GST:</span>
                        <strong style="font-size: 14px; color: #0f172a;"><?= htmlspecialchars($under_gst) ?></strong>
                    </div>
                    <div class="col-md-3 col-sm-6" style="margin-bottom: 12px;">
                        <span style="font-size: 12px; color: #64748b; font-weight: 600; display: block;">Bill Type:</span>
                        <strong style="font-size: 14px; color: #0f172a;"><?= htmlspecialchars($bill_type) ?></strong>
                    </div>
                    <div class="col-md-3 col-sm-6" style="margin-bottom: 12px;">
                        <span style="font-size: 12px; color: #64748b; font-weight: 600; display: block;">Pay Mode:</span>
                        <span class="label label-primary" style="font-size: 12px; font-weight:600;"><?= htmlspecialchars($pay_mode) ?></span>
                    </div>

                    <?php if (!empty($s_code) || !empty($store_name) || !empty($location)): ?>
                    <div class="col-md-4 col-sm-6" style="margin-bottom: 12px;">
                        <span style="font-size: 12px; color: #64748b; font-weight: 600; display: block;">Store / Location:</span>
                        <strong style="font-size: 13px; color: #0f172a;"><?= htmlspecialchars(trim(($s_code ?? '') . ' ' . ($store_name ?? '') . ' ' . ($location ?? ''))) ?></strong>
                    </div>
                    <?php endif; ?>

                    <div class="col-md-3 col-sm-6" style="margin-bottom: 12px;">
                        <span style="font-size: 12px; color: #64748b; font-weight: 600; display: block;">Status:</span>
                        <?php if (($status ?? 'success') == 'success'): ?>
                            <span class="label label-success" style="font-size: 12px;">Success</span>
                        <?php else: ?>
                            <span class="label label-warning" style="font-size: 12px;"><?= htmlspecialchars($status ?? 'Hold') ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Items Details Table -->
            <div style="background: #ffffff; border-radius: 10px; padding: 18px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); margin-bottom: 20px; border: 1px solid #e2e8f0;">
                <h5 style="margin-top:0; margin-bottom: 14px; font-weight:700; color: #1e293b; border-bottom: 2px solid #009688; padding-bottom: 6px; display: inline-block;">
                    <i class="fa fa-cubes"></i> Purchased Items (<?= count($items) ?> items)
                </h5>
                
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover" style="margin-bottom: 0; font-size: 13px;">
                        <thead>
                            <tr style="background-color: #f1f5f9; color: #334155;">
                                <th style="width: 40px; text-align: center;">#</th>
                                <th>Barcode No</th>
                                <th>Article No</th>
                                <th>Item Name</th>
                                <th>Group / Brand</th>
                                <th style="text-align: center;">Qty</th>
                                <th style="text-align: right;">SP (Rate)</th>
                                <th style="text-align: right;">Net Amt</th>
                                <th style="text-align: right;">GST Amt</th>
                                <th style="text-align: right;">Taxable Amt</th>
                                <th style="text-align: right;">Total Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $sn = 1; foreach ($items as $itm): ?>
                            <tr>
                                <td style="text-align: center; font-weight:600;"><?= $sn++ ?></td>
                                <td><?= htmlspecialchars($itm['barcode_number'] ?? '') ?></td>
                                <td><?= htmlspecialchars($itm['article_no'] ?? '') ?></td>
                                <td style="font-weight: 600; color: #0f172a;"><?= htmlspecialchars($itm['item_name'] ?? '') ?></td>
                                <td><?= htmlspecialchars(trim(($itm['category'] ?? '') . ' ' . ($itm['brand'] ?? ''))) ?></td>
                                <td style="text-align: center; font-weight:600;"><?= $itm['qty'] ?? 1 ?></td>
                                <td style="text-align: right;">₹ <?= number_format((float)($itm['rate'] ?? $itm['purchase_price'] ?? 0), 2) ?></td>
                                <td style="text-align: right;">₹ <?= number_format((float)($itm['net_price'] ?? 0), 2) ?></td>
                                <td style="text-align: right;">₹ <?= number_format((float)($itm['gst_amount'] ?? 0), 2) ?></td>
                                <td style="text-align: right;">₹ <?= number_format((float)($itm['taxable_amount'] ?? 0), 2) ?></td>
                                <td style="text-align: right; font-weight: 700; color: #009688;">₹ <?= number_format((float)($itm['amount'] ?? 0), 2) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr style="background-color: #f8fafc; font-weight: 700;">
                                <td colspan="5" style="text-align: right; color: #334155;">Total Summary:</td>
                                <td style="text-align: center; color: #009688;"><?= $total_qty ?></td>
                                <td></td>
                                <td style="text-align: right;">₹ <?= number_format($total_net, 2) ?></td>
                                <td style="text-align: right;">₹ <?= number_format($total_gst_amt, 2) ?></td>
                                <td style="text-align: right;">₹ <?= number_format($total_taxable, 2) ?></td>
                                <td style="text-align: right; color: #009688; font-size: 14px;">₹ <?= number_format($total_amount, 2) ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Extra Charges & Grand Totals Summary -->
            <div style="background: #ffffff; border-radius: 10px; padding: 18px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); border: 1px solid #e2e8f0;">
                <div class="row">
                    <div class="col-md-7 col-sm-12">
                        <?php if (!empty($remarks)): ?>
                            <div style="margin-bottom: 12px;">
                                <span style="font-size: 12px; color: #64748b; font-weight: 600; display: block;">Remarks:</span>
                                <p style="font-size: 13px; color: #334155; margin:0; background: #f1f5f9; padding: 8px 12px; border-radius: 6px;"><?= htmlspecialchars($remarks) ?></p>
                            </div>
                        <?php endif; ?>

                        <?php 
                            $packing = (float)($packing_amount ?? 0);
                            $delivery = (float)($delivery_amount ?? 0);
                            $other = (float)($other_amount ?? 0);
                            $labour = (float)($labour_amount ?? 0);
                            $extra_total = $packing + $delivery + $other + $labour;
                        ?>
                        <?php if ($extra_total > 0): ?>
                            <div style="display:flex; gap: 15px; font-size: 12px; color: #475569; flex-wrap: wrap; margin-top: 10px;">
                                <?php if ($packing > 0): ?><span>Packing Charge: <strong>₹<?= number_format($packing, 2) ?></strong></span><?php endif; ?>
                                <?php if ($delivery > 0): ?><span>Delivery Charge: <strong>₹<?= number_format($delivery, 2) ?></strong></span><?php endif; ?>
                                <?php if ($other > 0): ?><span>Handling Charge: <strong>₹<?= number_format($other, 2) ?></strong></span><?php endif; ?>
                                <?php if ($labour > 0): ?><span>Labour Charge: <strong>₹<?= number_format($labour, 2) ?></strong></span><?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="col-md-5 col-sm-12">
                        <div style="background: #f8fafc; padding: 14px; border-radius: 8px; border: 1px solid #e2e8f0;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 6px; font-size: 13px; color: #475569;">
                                <span>Sub Total Amount:</span>
                                <strong>₹ <?= number_format($total_amount, 2) ?></strong>
                            </div>
                            <?php if ($extra_total > 0): ?>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 6px; font-size: 13px; color: #475569;">
                                <span>Extra Charges:</span>
                                <strong>+ ₹ <?= number_format($extra_total, 2) ?></strong>
                            </div>
                            <?php endif; ?>
                            <?php $adjust = (float)($discount_total_amount ?? 0); if ($adjust > 0): ?>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 6px; font-size: 13px; color: #e53935;">
                                <span>Adjustment / Discount:</span>
                                <strong>- ₹ <?= number_format($adjust, 2) ?></strong>
                            </div>
                            <?php endif; ?>
                            
                            <hr style="margin: 8px 0; border-top: 1px solid #cbd5e1;">
                            
                            <div style="display: flex; justify-content: space-between; font-size: 16px; font-weight: 700; color: #009688;">
                                <span>Grand Total Amount:</span>
                                <span>₹ <?= number_format(($total_amount + $extra_total) - $adjust, 2) ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="modal-footer" style="background-color: #f1f5f9; padding: 12px 22px; display: flex; justify-content: space-between; align-items: center;">
            <a href="sale-creations.php?bill_no=<?= urlencode($bill_number) ?>" class="btn btn-primary" style="font-weight: 600;">
                <i class="fa fa-pencil"></i> Edit / Update Bill
            </a>
            <button type="button" class="btn btn-danger" data-dismiss="modal" style="font-weight: 600; padding: 6px 20px;">
                <i class="fa fa-times"></i> Close
            </button>
        </div>
    </div>
</div>