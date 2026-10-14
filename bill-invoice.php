<?php include_once("include/config.php"); ?>
<?php
$validationHelper = new validation();

$db = connect();

$bill_no = $_GET['bill_no'] ?? '';
$type = $_GET['type'] ?? 'sale';

$items = [];
$account_name = 'Walk-in Customer';
$mobile_number = 'N/A';
$purchage_date = date('Y-m-d');
$pay_mode = 'CASH';
$under_gst = 'Local';
$gst_type = 'N/A';

if (!empty($bill_no)) {
  if ($type == 'sale') {
    $stmt = $db->select("SELECT * FROM tbl_bill WHERE bill_number=? ORDER BY id ASC", 's', $bill_no);
  } else {
    $stmt = $db->select("SELECT * FROM tbl_purchase WHERE bill_number=? ORDER BY id ASC", 's', $bill_no);
  }
  
  if ($stmt) {
    while ($row = $stmt->fetch_assoc()) {
        $items[] = $row;
    }
    $stmt->close();
  }
}

if (!empty($items)) {
    $first = $items[0];
    foreach ($first as $key => $value) {
        $$key = $validationHelper->filterText($value);
    }
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

$pay_mode_display = !empty($pay_mode) ? getPayModeByCode($pay_mode) : 'CASH';
$under_gst = $under_gst ?? 'Local';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>View & Print Invoice - <?= htmlspecialchars($bill_no) ?></title>
  <link href="assets/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
  <link href="assets/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
  <style>
    body { background-color: #eef2f5; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; }
    .print-size-btn.active, .modal-size-btn.active { background-color: #37475a !important; color: #fff !important; border-color: #232f3e !important; }
    @media print {
      .cs-hide_print, .modal-backdrop, .modal-header, .modal-footer, .control-bar { display: none !important; }
      body { background-color: #fff !important; padding: 0 !important; margin: 0 !important; }
    }
  </style>
</head>
<body>

<!-- Top Control Toolbar -->
<div class="cs-hide_print control-bar" style="background: linear-gradient(135deg, #37475a, #232f3e); padding: 12px 20px; color: #fff; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 10px rgba(0,0,0,0.3); position: sticky; top: 0; z-index: 1000;">
    <div>
        <span style="font-weight: 600; font-size: 14px; margin-right: 15px;"><i class="fa fa-print"></i> Paper Size Configuration:</span>
        <div class="btn-group" role="group">
            <button type="button" class="btn btn-sm btn-default print-size-btn active" data-size="80mm" onclick="switchPrintSize('80mm')">
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
    <div>
        <button type="button" class="btn btn-sm btn-warning" onclick="$('#printModalPopup').modal('show')">
            <i class="fa fa-window-restore"></i> Open Size Options Popup
        </button>
        <button type="button" class="btn btn-sm btn-success" onclick="printInvoiceCurrent()">
            <i class="fa fa-print"></i> Print Invoice Now
        </button>
    </div>
</div>

<!-- On-Screen Preview Area -->
<div class="container cs-hide_print" style="margin-top: 20px; margin-bottom: 40px;">
    <div style="display: flex; justify-content: center; background: #eaeded; padding: 25px 15px; border-radius: 8px; min-height: 500px; box-shadow: inset 0 0 8px rgba(0,0,0,0.1);">
        <div class="invoice-box" style="background: #ffffff; box-shadow: 0 4px 15px rgba(0,0,0,0.15); border-radius: 4px;">
            
            <!-- ==================== 80mm THERMAL RECEIPT LAYOUT ==================== -->
            <div class="layout-80mm size-layout" style="width: 290px; padding: 12px; font-family: 'Courier New', Courier, monospace; font-size: 11px; color: #000; line-height: 1.3; display: block;">
                <div style="text-align: center; margin-bottom: 8px;">
                    <div style="font-size: 18px; font-weight: bold; color: #000; text-transform: uppercase;">RAMALAYA</div>
                    <div style="font-size: 10px; margin-top: 2px;">Lal Bahadur Shastri Int'l Airport</div>
                    <div style="font-size: 10px;">Varanasi, Uttar Pradesh 221006</div>
                    <div style="font-size: 10px;">GSTIN: 09AANCM4929Q1ZG</div>
                    <div style="font-size: 10px;">Ph: +91 766 904 7613</div>
                </div>
                
                <div style="border-top: 1px dashed #000; margin: 6px 0;"></div>
                <div style="font-size: 10px;">
                    <div><strong>Bill No:</strong> <?= htmlspecialchars($bill_no) ?></div>
                    <div><strong>Date:</strong> <?= dateformat($purchage_date) ?> <?= date('h:i A') ?></div>
                    <div><strong>Customer:</strong> <?= htmlspecialchars($account_name) ?></div>
                    <div><strong>Mobile:</strong> <?= htmlspecialchars($mobile_number) ?></div>
                    <div><strong>Pay Mode:</strong> <?= htmlspecialchars($pay_mode_display) ?></div>
                </div>
                <div style="border-top: 1px dashed #000; margin: 6px 0;"></div>
                
                <table style="width: 100%; border-collapse: collapse; font-size: 10px;">
                    <thead>
                        <tr style="border-bottom: 1px dashed #000;">
                            <th style="text-align: left; padding-bottom: 4px;">Item</th>
                            <th style="text-align: center; padding-bottom: 4px;">Qty</th>
                            <th style="text-align: right; padding-bottom: 4px;">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $itm): ?>
                        <tr>
                            <td style="padding: 3px 0; text-align: left;">
                                <?= htmlspecialchars($itm['item_name'] ?? '') ?>
                                <?php if (!empty($itm['size'])): ?>(<?= htmlspecialchars($itm['size']) ?>)<?php endif; ?>
                            </td>
                            <td style="padding: 3px 0; text-align: center; vertical-align: top;"><?= $itm['qty'] ?? 1 ?></td>
                            <td style="padding: 3px 0; text-align: right; vertical-align: top;"><?= number_format((float)($itm['amount'] ?? 0), 2) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <div style="border-top: 1px dashed #000; margin: 6px 0;"></div>
                
                <div style="font-size: 10px;">
                    <div style="display: flex; justify-content: space-between;">
                        <span>Total Qty:</span>
                        <span><?= $total_items_qty ?></span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span>Taxable Value:</span>
                        <span>₹ <?= number_format($total_taxable_amount, 2) ?></span>
                    </div>
                    <?php if ($under_gst == 'Inter'): ?>
                    <div style="display: flex; justify-content: space-between;">
                        <span>IGST 5%:</span>
                        <span>₹ <?= number_format($total_gst_amount, 2) ?></span>
                    </div>
                    <?php else: ?>
                    <div style="display: flex; justify-content: space-between;">
                        <span>CGST 2.5%:</span>
                        <span>₹ <?= number_format($total_gst_amount / 2, 2) ?></span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span>SGST 2.5%:</span>
                        <span>₹ <?= number_format($total_gst_amount / 2, 2) ?></span>
                    </div>
                    <?php endif; ?>
                    <div style="display: flex; justify-content: space-between; font-weight: bold; font-size: 12px; margin-top: 4px; border-top: 1px solid #000; padding-top: 4px;">
                        <span>GRAND TOTAL:</span>
                        <span>₹ <?= number_format($grand_total_amount, 2) ?></span>
                    </div>
                </div>
                
                <div style="border-top: 1px dashed #000; margin: 8px 0 6px 0;"></div>
                <div style="text-align: center; font-size: 10px;">
                    <div>Thank you for shopping with us!</div>
                    <div>Visit Again! www.prabhushriram.com</div>
                </div>
            </div>

            <!-- ==================== A4 STANDARD INVOICE LAYOUT ==================== -->
            <div class="layout-A4 size-layout" style="display: none; width: 780px; padding: 30px; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 12px; color: #333; line-height: 1.4;">
                <div style="display: flex; justify-content: space-between; border-bottom: 2px solid #37475a; padding-bottom: 15px; margin-bottom: 20px;">
                    <div>
                        <h2 style="margin: 0; color: #FF7F50; font-weight: bold; font-size: 26px;">RAMALAYA</h2>
                        <div style="color: #555; font-size: 11px; margin-top: 4px;">
                            Lal Bahadur Shastri International Airport, Varanasi UP 221006<br>
                            <strong>GSTIN:</strong> 09AANCM4929Q1ZG | <strong>Phone:</strong> +91 766 904 7613<br>
                            <strong>Email:</strong> info@prabhushriram.com | <strong>Web:</strong> www.prabhushriram.com
                        </div>
                    </div>
                    <div style="text-align: right;">
                        <h3 style="margin: 0; color: #37475a; font-size: 20px; font-weight: bold; text-transform: uppercase;">TAX INVOICE</h3>
                        <div style="margin-top: 8px; font-size: 12px;">
                            <div><strong>Invoice No:</strong> <span style="color: #000; font-weight:600;"><?= htmlspecialchars($bill_no) ?></span></div>
                            <div><strong>Date:</strong> <?= dateformat($purchage_date) ?></div>
                            <div><strong>Payment Mode:</strong> <?= htmlspecialchars($pay_mode_display) ?></div>
                        </div>
                    </div>
                </div>

                <div style="display: flex; justify-content: space-between; background: #f8f9fa; border: 1px solid #e9ecef; padding: 12px; border-radius: 4px; margin-bottom: 20px;">
                    <div>
                        <div style="font-weight: bold; color: #37475a; text-transform: uppercase; font-size: 11px; margin-bottom: 4px;">Billed To (Customer Details):</div>
                        <div style="font-size: 13px; font-weight: 600; color: #000;"><?= htmlspecialchars($account_name) ?></div>
                        <div><strong>Mobile:</strong> <?= htmlspecialchars($mobile_number) ?></div>
                        <?php if (!empty($gst_type)): ?><div><strong>GST Type:</strong> <?= htmlspecialchars($gst_type) ?></div><?php endif; ?>
                    </div>
                    <div style="text-align: right;">
                        <div style="font-weight: bold; color: #37475a; text-transform: uppercase; font-size: 11px; margin-bottom: 4px;">Invoice Summary:</div>
                        <div><strong>Total Items:</strong> <?= count($items) ?> (Qty: <?= $total_items_qty ?>)</div>
                        <div><strong>GST Region:</strong> <?= htmlspecialchars($under_gst) ?></div>
                    </div>
                </div>

                <table class="table table-bordered" style="width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 12px;">
                    <thead>
                        <tr style="background-color: #37475a; color: #ffffff;">
                            <th style="padding: 8px; text-align: center; width: 40px;">#</th>
                            <th style="padding: 8px; text-align: left;">Item Description</th>
                            <th style="padding: 8px; text-align: center;">Barcode / HSN</th>
                            <th style="padding: 8px; text-align: center;">Size / Color</th>
                            <th style="padding: 8px; text-align: center;">Qty</th>
                            <th style="padding: 8px; text-align: right;">Rate (₹)</th>
                            <th style="padding: 8px; text-align: right;">Taxable (₹)</th>
                            <th style="padding: 8px; text-align: right;">GST (₹)</th>
                            <th style="padding: 8px; text-align: right;">Total (₹)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $idx = 1; foreach ($items as $itm): ?>
                        <tr>
                            <td style="padding: 8px; text-align: center;"><?= $idx++ ?></td>
                            <td style="padding: 8px; font-weight: 600;"><?= htmlspecialchars($itm['item_name'] ?? '') ?></td>
                            <td style="padding: 8px; text-align: center; color: #555;"><?= htmlspecialchars($itm['barcode_number'] ?? '33074100') ?></td>
                            <td style="padding: 8px; text-align: center; color: #555;"><?= htmlspecialchars($itm['size'] ?? '-') ?> / <?= htmlspecialchars($itm['color'] ?? '-') ?></td>
                            <td style="padding: 8px; text-align: center;"><?= $itm['qty'] ?? 1 ?></td>
                            <td style="padding: 8px; text-align: right;"><?= number_format((float)($itm['rate'] ?? 0), 2) ?></td>
                            <td style="padding: 8px; text-align: right;"><?= number_format((float)($itm['taxable_amount'] ?? 0), 2) ?></td>
                            <td style="padding: 8px; text-align: right;"><?= number_format((float)($itm['gst_amount'] ?? 0), 2) ?></td>
                            <td style="padding: 8px; text-align: right; font-weight: 600;"><?= number_format((float)($itm['amount'] ?? 0), 2) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <div style="display: flex; justify-content: space-between; margin-top: 15px;">
                    <div style="width: 55%;">
                        <div style="border: 1px solid #e9ecef; padding: 10px; border-radius: 4px; background: #fafafa; font-size: 11px;">
                            <strong>Terms & Conditions:</strong><br>
                            1. Goods once sold will not be taken back without original bill.<br>
                            2. Subject to Varanasi Jurisdiction.<br>
                            3. This is a computer generated invoice.
                        </div>
                    </div>
                    <div style="width: 40%;">
                        <table style="width: 100%; font-size: 12px; border-collapse: collapse;">
                            <tr>
                                <td style="padding: 4px 0; text-align: left;">Subtotal Taxable Value:</td>
                                <td style="padding: 4px 0; text-align: right;">₹ <?= number_format($total_taxable_amount, 2) ?></td>
                            </tr>
                            <?php if ($under_gst == 'Inter'): ?>
                            <tr>
                                <td style="padding: 4px 0; text-align: left;">IGST Total:</td>
                                <td style="padding: 4px 0; text-align: right;">₹ <?= number_format($total_gst_amount, 2) ?></td>
                            </tr>
                            <?php else: ?>
                            <tr>
                                <td style="padding: 4px 0; text-align: left;">CGST Total:</td>
                                <td style="padding: 4px 0; text-align: right;">₹ <?= number_format($total_gst_amount / 2, 2) ?></td>
                            </tr>
                            <tr>
                                <td style="padding: 4px 0; text-align: left;">SGST Total:</td>
                                <td style="padding: 4px 0; text-align: right;">₹ <?= number_format($total_gst_amount / 2, 2) ?></td>
                            </tr>
                            <?php endif; ?>
                            <tr style="border-top: 2px solid #37475a; font-size: 14px; font-weight: bold;">
                                <td style="padding: 8px 0; text-align: left; color: #37475a;">Grand Total:</td>
                                <td style="padding: 8px 0; text-align: right; color: #37475a;">₹ <?= number_format($grand_total_amount, 2) ?></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div style="margin-top: 40px; display: flex; justify-content: space-between; align-items: flex-end;">
                    <div style="text-align: center; font-size: 11px; color: #777;">
                        Thank you for your business!
                    </div>
                    <div style="text-align: center; border-top: 1px solid #999; width: 180px; padding-top: 4px; font-size: 11px;">
                        Authorized Signatory
                    </div>
                </div>
            </div>

            <!-- ==================== A5 COMPACT INVOICE LAYOUT ==================== -->
            <div class="layout-A5 size-layout" style="display: none; width: 550px; padding: 20px; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 11px; color: #333; line-height: 1.3;">
                <div style="display: flex; justify-content: space-between; border-bottom: 2px solid #37475a; padding-bottom: 8px; margin-bottom: 12px;">
                    <div>
                        <h3 style="margin: 0; color: #FF7F50; font-weight: bold; font-size: 20px;">RAMALAYA</h3>
                        <div style="color: #555; font-size: 10px;">
                            Lal Bahadur Shastri Airport, Varanasi UP<br>
                            <strong>GSTIN:</strong> 09AANCM4929Q1ZG | Ph: +91 766 904 7613
                        </div>
                    </div>
                    <div style="text-align: right;">
                        <h4 style="margin: 0; color: #37475a; font-size: 16px; font-weight: bold;">TAX INVOICE (A5)</h4>
                        <div style="font-size: 11px; margin-top: 4px;">
                            <strong>Bill No:</strong> <?= htmlspecialchars($bill_no) ?><br>
                            <strong>Date:</strong> <?= dateformat($purchage_date) ?>
                        </div>
                    </div>
                </div>

                <div style="display: flex; justify-content: space-between; background: #f8f9fa; border: 1px solid #e9ecef; padding: 8px; border-radius: 4px; margin-bottom: 12px; font-size: 11px;">
                    <div>
                        <strong>Customer:</strong> <?= htmlspecialchars($account_name) ?><br>
                        <strong>Mobile:</strong> <?= htmlspecialchars($mobile_number) ?>
                    </div>
                    <div style="text-align: right;">
                        <strong>Pay Mode:</strong> <?= htmlspecialchars($pay_mode_display) ?><br>
                        <strong>GST:</strong> <?= htmlspecialchars($under_gst) ?>
                    </div>
                </div>

                <table class="table table-bordered" style="width: 100%; border-collapse: collapse; margin-bottom: 12px; font-size: 11px;">
                    <thead>
                        <tr style="background-color: #37475a; color: #ffffff;">
                            <th style="padding: 5px; text-align: center; width: 30px;">#</th>
                            <th style="padding: 5px; text-align: left;">Item Description</th>
                            <th style="padding: 5px; text-align: center;">Qty</th>
                            <th style="padding: 5px; text-align: right;">Rate (₹)</th>
                            <th style="padding: 5px; text-align: right;">Total (₹)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $idx2 = 1; foreach ($items as $itm): ?>
                        <tr>
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

<!-- PRINT OPTIONS POPUP MODAL -->
<div class="modal fade" id="printModalPopup" tabindex="-1" role="dialog" aria-labelledby="printModalPopupLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" style="max-width: 950px; width: 95%;">
        <div class="modal-content" style="border-radius: 8px; overflow: hidden; box-shadow: 0 5px 20px rgba(0,0,0,0.4);">
            <div class="modal-header" style="background: linear-gradient(135deg, #37475a, #232f3e); color: #fff; padding: 14px 20px;">
                <button type="button" class="close" data-dismiss="modal" onclick="closePrintModal()" aria-hidden="true" style="color: #fff; opacity: 0.8;">×</button>
                <h4 class="modal-title" id="printModalPopupLabel" style="margin:0; font-weight:600;">
                    <i class="fa fa-print"></i> Select Print Size Configuration & Preview (Bill No: <?= htmlspecialchars($bill_no) ?>)
                </h4>
            </div>
            <div class="modal-body" style="background-color: #f4f6f9; padding: 20px;">
                <div class="row" style="margin-bottom: 15px;">
                    <div class="col-sm-8 col-xs-12">
                        <span style="font-weight: 600; margin-right: 10px; font-size: 13px; color: #333;"><i class="fa fa-sliders"></i> Paper Size Options:</span>
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-sm btn-default modal-size-btn active" data-size="80mm" onclick="switchPrintSize('80mm')">
                                <i class="fa fa-print"></i> 80mm Thermal POS
                            </button>
                            <button type="button" class="btn btn-sm btn-default modal-size-btn" data-size="A4" onclick="switchPrintSize('A4')">
                                <i class="fa fa-file-text-o"></i> A4 Standard
                            </button>
                            <button type="button" class="btn btn-sm btn-default modal-size-btn" data-size="A5" onclick="switchPrintSize('A5')">
                                <i class="fa fa-file-o"></i> A5 Compact
                            </button>
                        </div>
                    </div>
                    <div class="col-sm-4 col-xs-12 text-right">
                        <button type="button" class="btn btn-sm btn-success" onclick="printInvoiceCurrent()">
                            <i class="fa fa-print"></i> Print Invoice Now
                        </button>
                    </div>
                </div>

                <!-- Modal Live Preview Container -->
                <div style="display: flex; justify-content: center; background: #eaeded; padding: 20px 10px; border-radius: 6px; min-height: 460px; overflow-x: auto;">
                    <div id="modal-invoice-preview-container" style="background: #ffffff; box-shadow: 0 2px 10px rgba(0,0,0,0.15); border-radius: 4px;">
                        <!-- LAYOUT CLONED HERE -->
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="background-color: #eef2f5;">
                <span style="float: left; font-size: 12px; color: #666; line-height: 30px;">
                    Selected Size: <strong id="modal-selected-size-label" style="color: #37475a;">80mm Thermal POS</strong>
                </span>
                <button type="button" class="btn btn-success" onclick="printInvoiceCurrent()"><i class="fa fa-print"></i> Print</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal" onclick="closePrintModal()">Close</button>
            </div>
        </div>
    </div>
</div>

<script src="assets/plugins/jQuery/jquery-1.12.4.min.js"></script>
<script src="assets/bootstrap/js/bootstrap.min.js"></script>
<script>
var currentPrintSize = '80mm';

function switchPrintSize(size) {
    currentPrintSize = size;
    
    var buttons = document.querySelectorAll('.print-size-btn, .modal-size-btn');
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

    var labelText = '80mm Thermal POS';
    if (size === 'A4') labelText = 'A4 Standard (Full Page)';
    if (size === 'A5') labelText = 'A5 Compact (Half Page)';

    var modalLabel = document.getElementById('modal-selected-size-label');
    if (modalLabel) modalLabel.textContent = labelText;
    
    var currentLabel = document.getElementById('current-selected-size-label');
    if (currentLabel) currentLabel.textContent = labelText;
}

function openPrintModal() {
    if (typeof $ !== 'undefined' && $.fn && $.fn.modal) {
        $('#printModalPopup').modal('show');
    } else {
        var modalEl = document.getElementById('printModalPopup');
        if (modalEl) {
            modalEl.classList.add('in');
            modalEl.style.display = 'block';
        }
    }
}

function closePrintModal() {
    if (typeof $ !== 'undefined' && $.fn && $.fn.modal) {
        $('#printModalPopup').modal('hide');
    } else {
        var modalEl = document.getElementById('printModalPopup');
        if (modalEl) {
            modalEl.classList.remove('in');
            modalEl.style.display = 'none';
        }
    }
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

document.addEventListener('DOMContentLoaded', function() {
    var mainBox = document.querySelector('.invoice-box');
    var modalContainer = document.getElementById('modal-invoice-preview-container');
    if (mainBox && modalContainer) {
        modalContainer.innerHTML = mainBox.outerHTML;
    }
    switchPrintSize('80mm');
    openPrintModal();
});
</script>
</body>
</html>
