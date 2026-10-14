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

$total_taxable_amount = 0;
$total_gst_amount = 0;
$grand_total_amount = 0;
$total_items_qty = 0;

foreach ($items as $item) {
    $total_taxable_amount += (float)($item['taxable_amount'] ?? 0);
    $total_gst_amount += (float)($item['gst_amount'] ?? 0);
    $grand_total_amount += (float)($item['amount'] ?? 0);
    $total_items_qty += (int)($item['qty'] ?? 1);
}
if ($grand_total_amount == 0 && isset($amount)) {
    $grand_total_amount = (float)$amount;
}

$purchage_date = !empty($purchage_date) ? dateformat($purchage_date) : date('d-m-Y');
$account_name = !empty($account_name) ? $account_name : 'Walk-in Customer';
$mobile_number = !empty($mobile_number) ? $mobile_number : 'N/A';
$pay_mode = !empty($pay_mode) ? getPayModeByCode($pay_mode) : 'CASH';
$under_gst = $under_gst ?? 'Local';
?>

<div class="modal-dialog modal-lg" style="max-width: 950px; width: 95%;">
    <div class="modal-content" style="border-radius: 8px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.3);">
        <div class="modal-header modal-header-primary" style="background: linear-gradient(135deg, #37475a, #232f3e); color: #fff; padding: 12px 20px;">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="color: #fff; opacity: 0.8;">×</button>
            <h4 class="modal-title" style="margin:0; font-weight:600;"><i class="fa fa-print"></i> Sale Invoice & Print Preview (Bill No: <?= htmlspecialchars($bill_number) ?>)</h4>
        </div>
        
        <div class="modal-body" style="background-color: #f4f6f9; padding: 15px;">
            <!-- Control Toolbar for Size Configuration -->
            <div class="row" style="margin-bottom: 15px;">
                <div class="col-md-7 col-sm-12">
                    <div class="btn-group" role="group" aria-label="Print Size Configuration">
                        <span style="font-weight: 600; margin-right: 8px; line-height: 30px; font-size: 13px; color: #333;"><i class="fa fa-sliders"></i> Select Print Format:</span>
                        <button type="button" class="btn btn-sm btn-primary print-size-btn active" data-size="80mm" onclick="switchPrintSize('80mm')">
                            <i class="fa fa-print"></i> Thermal 80mm
                        </button>
                        <button type="button" class="btn btn-sm btn-default print-size-btn" data-size="A4" onclick="switchPrintSize('A4')">
                            <i class="fa fa-file-text-o"></i> A4 Standard
                        </button>
                        <button type="button" class="btn btn-sm btn-default print-size-btn" data-size="A5" onclick="switchPrintSize('A5')">
                            <i class="fa fa-file-o"></i> A5 Compact
                        </button>
                    </div>
                </div>
                <div class="col-md-5 col-sm-12 text-right">
                    <button type="button" class="btn btn-sm btn-success" onclick="printInvoiceCurrent()" style="font-weight:600; padding: 6px 18px;">
                        <i class="fa fa-print"></i> Print Invoice Now
                    </button>
                </div>
            </div>

            <!-- Live Invoice Preview Container -->
            <div id="view-mode-preview-container" class="preview-mode-active">
                <div id="invoice-preview-wrapper" style="display: flex; justify-content: center; background: #eaeded; padding: 20px 10px; border-radius: 6px; min-height: 450px; overflow-x: auto;">
                    
                    <div id="printable-invoice-content" class="invoice-box" style="background: #ffffff; box-shadow: 0 2px 10px rgba(0,0,0,0.15); border-radius: 4px; transition: all 0.3s ease;">
                        
                        <!-- ==================== 80mm THERMAL RECEIPT LAYOUT ==================== -->
                        <div class="layout-80mm size-layout" style="width: 290px; padding: 12px; font-family: 'Courier New', Courier, monospace; font-size: 11px; color: #000; line-height: 1.3; display: block;">
                            <div style="text-align: center; margin-bottom: 8px;">
                                <h4 style="margin: 0; font-weight: bold; font-size: 16px;">RAMALAYA</h4>
                                <p style="margin: 2px 0; font-size: 10px;">Varanasi, Uttar Pradesh</p>
                                <p style="margin: 2px 0; font-size: 10px;">Phone: +91-9876543210</p>
                                <div style="border-bottom: 1px dashed #000; margin: 6px 0;"></div>
                                <strong style="font-size: 12px;">TAX INVOICE</strong>
                            </div>

                            <div style="margin-bottom: 6px; font-size: 10px;">
                                <div>Bill No: <strong><?= htmlspecialchars($bill_number) ?></strong></div>
                                <div>Date: <?= $purchage_date ?></div>
                                <div>Customer: <?= htmlspecialchars($account_name) ?></div>
                                <div>Pay Mode: <?= htmlspecialchars($pay_mode) ?></div>
                            </div>

                            <table style="width: 100%; border-collapse: collapse; margin-bottom: 8px; font-size: 10px;">
                                <thead>
                                    <tr style="border-top: 1px dashed #000; border-bottom: 1px dashed #000;">
                                        <th style="text-align: left; padding: 3px 0;">Item</th>
                                        <th style="text-align: center; padding: 3px 0;">Qty</th>
                                        <th style="text-align: right; padding: 3px 0;">Rate</th>
                                        <th style="text-align: right; padding: 3px 0;">Amt</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($items as $itm): ?>
                                    <tr>
                                        <td style="padding: 2px 0; max-width: 110px; word-break: break-word;"><?= htmlspecialchars($itm['item_name'] ?? '') ?></td>
                                        <td style="text-align: center; padding: 2px 0;"><?= $itm['qty'] ?? 1 ?></td>
                                        <td style="text-align: right; padding: 2px 0;"><?= number_format((float)($itm['rate'] ?? 0), 2) ?></td>
                                        <td style="text-align: right; padding: 2px 0;"><?= number_format((float)($itm['amount'] ?? 0), 2) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>

                            <div style="border-top: 1px dashed #000; padding-top: 4px; font-size: 10px;">
                                <div style="display: flex; justify-content: space-between;">
                                    <span>Total Qty:</span>
                                    <span><?= $total_items_qty ?></span>
                                </div>
                                <div style="display: flex; justify-content: space-between;">
                                    <span>Taxable:</span>
                                    <span>₹ <?= number_format($total_taxable_amount, 2) ?></span>
                                </div>
                                <div style="display: flex; justify-content: space-between;">
                                    <span>GST Amt:</span>
                                    <span>₹ <?= number_format($total_gst_amount, 2) ?></span>
                                </div>
                                <div style="display: flex; justify-content: space-between; font-weight: bold; font-size: 12px; margin-top: 3px; border-top: 1px dashed #000; padding-top: 3px;">
                                    <span>Grand Total:</span>
                                    <span>₹ <?= number_format($grand_total_amount, 2) ?></span>
                                </div>
                            </div>

                            <div style="text-align: center; margin-top: 10px; font-size: 9px;">
                                Thank you for shopping with us!<br>
                                Visit Again!
                            </div>
                        </div>

                        <!-- ==================== A4 STANDARD INVOICE LAYOUT ==================== -->
                        <div class="layout-A4 size-layout" style="width: 750px; padding: 30px; font-family: Arial, sans-serif; font-size: 12px; color: #333; display: none;">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #37475a; padding-bottom: 15px; margin-bottom: 15px;">
                                <div>
                                    <h2 style="margin: 0; color: #37475a; font-weight: bold;">RAMALAYA</h2>
                                    <p style="margin: 3px 0 0 0; color: #666;">Varanasi, Uttar Pradesh, India</p>
                                    <p style="margin: 2px 0 0 0; color: #666;">Phone: +91-9876543210 | Email: sales@ramalaya.com</p>
                                </div>
                                <div style="text-align: right;">
                                    <h3 style="margin: 0; color: #37475a;">TAX INVOICE</h3>
                                    <p style="margin: 5px 0 0 0; font-size: 14px; font-weight: bold;">Bill No: <?= htmlspecialchars($bill_number) ?></p>
                                    <p style="margin: 2px 0 0 0; color: #666;">Date: <?= $purchage_date ?></p>
                                </div>
                            </div>

                            <div style="display: flex; justify-content: space-between; margin-bottom: 15px; background: #f8f9fa; padding: 10px; border-radius: 4px;">
                                <div>
                                    <strong>Billed To:</strong><br>
                                    <span style="font-size: 13px; font-weight: bold;"><?= htmlspecialchars($account_name) ?></span><br>
                                    <span>Mobile: <?= htmlspecialchars($mobile_number) ?></span>
                                </div>
                                <div style="text-align: right;">
                                    <strong>Payment Info:</strong><br>
                                    <span>Pay Mode: <strong><?= htmlspecialchars($pay_mode) ?></strong></span><br>
                                    <span>Under GST: <?= htmlspecialchars($under_gst) ?></span>
                                </div>
                            </div>

                            <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
                                <thead>
                                    <tr style="background-color: #37475a; color: #ffffff;">
                                        <th style="padding: 8px; text-align: center; width: 40px;">#</th>
                                        <th style="padding: 8px; text-align: left;">Item Description</th>
                                        <th style="padding: 8px; text-align: center; width: 60px;">Qty</th>
                                        <th style="padding: 8px; text-align: right; width: 100px;">Rate</th>
                                        <th style="padding: 8px; text-align: right; width: 100px;">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $idx = 1; foreach ($items as $itm): ?>
                                    <tr style="border-bottom: 1px solid #ddd;">
                                        <td style="padding: 8px; text-align: center;"><?= $idx++ ?></td>
                                        <td style="padding: 8px; font-weight: bold;"><?= htmlspecialchars($itm['item_name'] ?? '') ?></td>
                                        <td style="padding: 8px; text-align: center;"><?= $itm['qty'] ?? 1 ?></td>
                                        <td style="padding: 8px; text-align: right;"><?= number_format((float)($itm['rate'] ?? 0), 2) ?></td>
                                        <td style="padding: 8px; text-align: right; font-weight: bold;"><?= number_format((float)($itm['amount'] ?? 0), 2) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>

                            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                                <div style="font-size: 11px; color: #666; width: 50%;">
                                    Terms & Conditions:<br>
                                    1. Goods once sold will not be taken back.<br>
                                    2. Subject to Varanasi Jurisdiction.
                                </div>
                                <div style="width: 40%;">
                                    <div style="display: flex; justify-content: space-between; padding: 3px 0;">
                                        <span>Total Qty:</span>
                                        <span><?= $total_items_qty ?></span>
                                    </div>
                                    <div style="display: flex; justify-content: space-between; padding: 3px 0;">
                                        <span>Taxable Amount:</span>
                                        <span>₹ <?= number_format($total_taxable_amount, 2) ?></span>
                                    </div>
                                    <div style="display: flex; justify-content: space-between; padding: 3px 0;">
                                        <span>GST Total:</span>
                                        <span>₹ <?= number_format($total_gst_amount, 2) ?></span>
                                    </div>
                                    <div style="display: flex; justify-content: space-between; padding: 6px 0; font-size: 15px; font-weight: bold; border-top: 2px solid #37475a; border-bottom: 2px solid #37475a; margin-top: 5px;">
                                        <span>Grand Total:</span>
                                        <span>₹ <?= number_format($grand_total_amount, 2) ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ==================== A5 COMPACT INVOICE LAYOUT ==================== -->
                        <div class="layout-A5 size-layout" style="width: 520px; padding: 18px; font-family: Arial, sans-serif; font-size: 11px; color: #333; display: none;">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 1.5px solid #37475a; padding-bottom: 10px; margin-bottom: 10px;">
                                <div>
                                    <h3 style="margin: 0; color: #37475a; font-weight: bold; font-size: 16px;">RAMALAYA</h3>
                                    <p style="margin: 2px 0 0 0; color: #666; font-size: 10px;">Varanasi, UP | Ph: +91-9876543210</p>
                                </div>
                                <div style="text-align: right;">
                                    <h4 style="margin: 0; color: #37475a; font-size: 13px;">TAX INVOICE</h4>
                                    <p style="margin: 2px 0 0 0; font-weight: bold;">Bill No: <?= htmlspecialchars($bill_number) ?></p>
                                    <p style="margin: 1px 0 0 0; color: #666; font-size: 10px;">Date: <?= $purchage_date ?></p>
                                </div>
                            </div>

                            <div style="margin-bottom: 10px; background: #f8f9fa; padding: 6px 8px; border-radius: 4px; font-size: 10px; display: flex; justify-content: space-between;">
                                <span>Customer: <strong><?= htmlspecialchars($account_name) ?></strong> (Mob: <?= htmlspecialchars($mobile_number) ?>)</span>
                                <span>Pay Mode: <strong><?= htmlspecialchars($pay_mode) ?></strong></span>
                            </div>

                            <table style="width: 100%; border-collapse: collapse; margin-bottom: 12px; font-size: 10px;">
                                <thead>
                                    <tr style="background-color: #37475a; color: #ffffff;">
                                        <th style="padding: 5px; text-align: center; width: 30px;">#</th>
                                        <th style="padding: 5px; text-align: left;">Item</th>
                                        <th style="padding: 5px; text-align: center; width: 45px;">Qty</th>
                                        <th style="padding: 5px; text-align: right; width: 75px;">Rate</th>
                                        <th style="padding: 5px; text-align: right; width: 80px;">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $idx2 = 1; foreach ($items as $itm): ?>
                                    <tr style="border-bottom: 1px solid #ddd;">
                                        <td style="padding: 5px; text-align: center;"><?= $idx2++ ?></td>
                                        <td style="padding: 5px; font-weight: 600;"><?= htmlspecialchars($itm['item_name'] ?? '') ?></td>
                                        <td style="padding: 5px; text-align: center;"><?= $itm['qty'] ?? 1 ?></td>
                                        <td style="padding: 5px; text-align: right;"><?= number_format((float)($itm['rate'] ?? 0), 2) ?></td>
                                        <td style="padding: 5px; text-align: right; font-weight: 600;"><?= number_format((float)($itm['amount'] ?? 0), 2) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>

                            <div style="display: flex; justify-content: space-between; align-items: flex-start; font-size: 11px;">
                                <div style="font-size: 10px; color: #666; width: 50%;">
                                    Thank you for shopping with RAMALAYA!<br>
                                    Subject to Varanasi Jurisdiction.
                                </div>
                                <div style="width: 45%;">
                                    <div style="display: flex; justify-content: space-between;">
                                        <span>Taxable:</span>
                                        <span>₹ <?= number_format($total_taxable_amount, 2) ?></span>
                                    </div>
                                    <div style="display: flex; justify-content: space-between;">
                                        <span>GST Total:</span>
                                        <span>₹ <?= number_format($total_gst_amount, 2) ?></span>
                                    </div>
                                    <div style="display: flex; justify-content: space-between; font-weight: bold; font-size: 13px; margin-top: 4px; border-top: 1px solid #37475a; padding-top: 4px;">
                                        <span>Total:</span>
                                        <span>₹ <?= number_format($grand_total_amount, 2) ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <div class="modal-footer" style="background-color: #eef2f5; padding: 12px 20px;">
            <span style="float: left; font-size: 12px; color: #666; line-height: 30px;">
                <i class="fa fa-info-circle"></i> Active Size: <strong id="current-selected-size-label" style="color: #37475a;">80mm (Thermal POS)</strong>
            </span>
            <button type="button" class="btn btn-success" onclick="printInvoiceCurrent()"><i class="fa fa-print"></i> Print Invoice</button>
            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
        </div>
    </div>
</div>

<script>
var currentPrintSize = '80mm';

function switchPrintSize(size) {
    currentPrintSize = size;
    
    var buttons = document.querySelectorAll('.print-size-btn');
    buttons.forEach(function(btn) {
        if (btn.getAttribute('data-size') === size) {
            btn.classList.add('active', 'btn-primary');
            btn.classList.remove('btn-default');
        } else {
            btn.classList.remove('active', 'btn-primary');
            btn.classList.add('btn-default');
        }
    });

    var layouts = document.querySelectorAll('.size-layout');
    layouts.forEach(function(el) {
        el.style.display = 'none';
    });
    var activeLayouts = document.querySelectorAll('.layout-' + size);
    activeLayouts.forEach(function(el) {
        el.style.display = 'block';
    });

    var labelText = '80mm (Thermal POS)';
    if (size === 'A4') labelText = 'A4 Standard (Full Page)';
    if (size === 'A5') labelText = 'A5 Compact (Half Page)';

    var labelEl = document.getElementById('current-selected-size-label');
    if (labelEl) labelEl.textContent = labelText;
}

function printInvoiceCurrent() {
    var size = currentPrintSize;
    var printElement = document.querySelector('.layout-' + size);
    if (!printElement) return;

    var printWindow = window.open('', '_blank', 'width=900,height=750');
    var pageCss = '';

    if (size === '80mm') {
        pageCss = '@page { size: 80mm auto; margin: 0; } body { margin: 0; padding: 5px; font-family: "Courier New", Courier, monospace; width: 78mm; } table { width: 100%; border-collapse: collapse; }';
    } else if (size === 'A5') {
        pageCss = '@page { size: A5 portrait; margin: 8mm; } body { margin: 0; padding: 10px; font-family: Arial, sans-serif; width: 100%; } table { width: 100%; border-collapse: collapse; }';
    } else {
        pageCss = '@page { size: A4 portrait; margin: 10mm; } body { margin: 0; padding: 15px; font-family: Arial, sans-serif; width: 100%; } table { width: 100%; border-collapse: collapse; }';
    }

    printWindow.document.write('<!DOCTYPE html><html><head><title>Print Invoice - ' + size + '</title>');
    printWindow.document.write('<link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">');
    printWindow.document.write('<style>' + pageCss + ' .size-layout { display: block !important; width: 100% !important; box-shadow: none !important; padding: 0 !important; } </style>');
    printWindow.document.write('</head><body>');
    printWindow.document.write(printElement.outerHTML);
    printWindow.document.write('</body></html>');
    
    printWindow.document.close();
    printWindow.focus();
    
    setTimeout(function() {
        printWindow.print();
        printWindow.close();
    }, 400);
}
</script>
