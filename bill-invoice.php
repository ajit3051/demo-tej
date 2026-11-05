<?php include_once("include/config.php"); ?>
<?php
$validationHelper = new validation();
$db = connect();

$bill_no = $_GET['bill_no'] ?? $_GET['bill_number'] ?? '';
$id = (int)($_GET['id'] ?? 0);
$type = $_GET['type'] ?? 'sale';

// If bill_no is empty but id is provided, fetch bill_number from database
if (empty($bill_no) && $id > 0) {
    if ($type == 'sale') {
        $id_stmt = $db->select("SELECT bill_number FROM tbl_bill WHERE id=? LIMIT 1", 'i', $id);
    } else {
        $id_stmt = $db->select("SELECT bill_number FROM tbl_purchase WHERE id=? LIMIT 1", 'i', $id);
    }
    if ($id_stmt) {
        $id_row = $id_stmt->fetch_assoc();
        if ($id_row) {
            $bill_no = $id_row['bill_number'];
        }
        $id_stmt->close();
    }
}

$items = [];
$account_name = 'Walk-in Customer';
$mobile_number = 'N/A';
$purchage_date = date('Y-m-d');
$created_date = date('Y-m-d H:i:s');
$pay_mode = 'CASH';
$under_gst = 'Local';
$gst_type = 'N/A';
$store_name = '';
$s_code = '';

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

$first = [];
if (!empty($items)) {
    $first = $items[0];
    foreach ($first as $key => $value) {
        $$key = $validationHelper->filterText($value);
    }
}

// Calculate financial totals
$total_taxable_amount = 0;
$total_gst_amount = 0;
$items_amount = 0;
$total_items_qty = 0;
$highest_gst_rate = 0;

foreach ($items as $item) {
    $total_taxable_amount += (float)($item['taxable_amount'] ?? 0);
    $total_gst_amount     += (float)($item['gst_amount'] ?? 0);
    $items_amount         += (float)($item['amount'] ?? 0);
    $total_items_qty      += (int)($item['qty'] ?? 1);
    $cur_gst = (float)($item['gst'] ?? 0);
    if ($cur_gst > $highest_gst_rate) {
        $highest_gst_rate = $cur_gst;
    }
}

$packing_amount        = (float)($first['packing_amount'] ?? 0);
$delivery_amount       = (float)($first['delivery_amount'] ?? 0);
$other_amount          = (float)($first['other_amount'] ?? 0);
$labour_amount         = (float)($first['labour_amount'] ?? 0);
$extra_charges         = $packing_amount + $delivery_amount + $other_amount + $labour_amount;
$discount_total_amount = (float)($first['discount_total_amount'] ?? 0);

$grand_total_amount = ($items_amount + $extra_charges) - $discount_total_amount;
if ($grand_total_amount <= 0 && !empty($first['amount'])) {
    $grand_total_amount = (float)$first['amount'];
}

$pay_mode_display = !empty($pay_mode) ? getPayModeByCode($pay_mode) : 'CASH';
$under_gst = $under_gst ?? 'Local';

// Normalize paper size
$raw_size = strtolower($_GET['size'] ?? ($_GET['print_option'] ?? '80mm'));
if ($raw_size === 'a4') {
    $current_size = 'A4';
} else if ($raw_size === 'a5') {
    $current_size = 'A5';
} else {
    $current_size = '80mm';
}

$auto_print = (!empty($_GET['auto_print']) && $_GET['auto_print'] == '1') ? 1 : 0;

// Resolve Store Information (from tbl_storeprofile_master if available)
$store_name_display = 'RAMALAYA';
$store_address_display = "Lal Bahadur Shastri Int'l Airport, Varanasi, Uttar Pradesh 221006";
$store_gstin_display = '09AANCM4929Q1ZG';
$store_phone_display = '+91 766 904 7613';
$store_email_display = 'info@prabhushriram.com';
$store_web_display = 'www.prabhushriram.com';

$store_row = null;
if (!empty($store_name)) {
    $st_stmt = $db->select("SELECT * FROM tbl_storeprofile_master WHERE store_name=? LIMIT 1", 's', $store_name);
    if ($st_stmt) { $store_row = $st_stmt->fetch_assoc(); $st_stmt->close(); }
}
if (!$store_row && !empty($s_code)) {
    $st_stmt = $db->select("SELECT * FROM tbl_storeprofile_master WHERE store_code=? OR code=? LIMIT 1", 'ss', $s_code, $s_code);
    if ($st_stmt) { $store_row = $st_stmt->fetch_assoc(); $st_stmt->close(); }
}
if ($store_row) {
    if (!empty($store_row['store_name'])) $store_name_display = $store_row['store_name'];
    $addr = trim(($store_row['address1'] ?? '') . (!empty($store_row['address2']) ? ', ' . $store_row['address2'] : ''));
    if (!empty($addr)) $store_address_display = $addr;
    if (!empty($store_row['gst_no'])) $store_gstin_display = $store_row['gst_no'];
    $ph = $store_row['phone_no'] ?: ($store_row['mobile_no1'] ?? '');
    if (!empty($ph)) $store_phone_display = $ph;
    if (!empty($store_row['email_id'])) $store_email_display = $store_row['email_id'];
    if (!empty($store_row['website_link'])) $store_web_display = $store_row['website_link'];
}

$bill_time_display = !empty($created_date) ? date('h:i A', strtotime($created_date)) : date('h:i A');
$bill_date_display = !empty($purchage_date) ? dateformat($purchage_date) : date('d-m-Y');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Invoice - <?= htmlspecialchars($bill_no) ?></title>
  <link href="assets/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
  <link href="assets/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
  
  <!-- Dynamic @page CSS rule updated per selected paper size -->
  <style id="dynamic-page-size-style">
    <?php if ($current_size === 'A4'): ?>
      @page { size: A4 portrait; margin: 10mm 12mm; }
    <?php elseif ($current_size === 'A5'): ?>
      @page { size: A5 portrait; margin: 8mm 10mm; }
    <?php else: ?>
      @page { size: 80mm auto; margin: 0; }
    <?php endif; ?>
  </style>

  <style>
    body {
      background-color: #eef2f5;
      font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
      margin: 0;
      padding: 0;
      color: #333;
    }

    .print-size-btn.active {
      background-color: #37475a !important;
      color: #fff !important;
      border-color: #232f3e !important;
    }

    /* On-Screen layout toggle */
    body.size-mode-80mm .layout-80mm { display: block !important; }
    body.size-mode-80mm .layout-A4   { display: none !important; }
    body.size-mode-80mm .layout-A5   { display: none !important; }

    body.size-mode-A4 .layout-80mm { display: none !important; }
    body.size-mode-A4 .layout-A4   { display: block !important; }
    body.size-mode-A4 .layout-A5   { display: none !important; }

    body.size-mode-A5 .layout-80mm { display: none !important; }
    body.size-mode-A5 .layout-A4   { display: none !important; }
    body.size-mode-A5 .layout-A5   { display: block !important; }

    /* ==================== PRINT MEDIA STYLES ==================== */
    @media print {
      .cs-hide_print,
      .control-bar,
      .modal,
      .modal-backdrop,
      .btn,
      button {
        display: none !important;
      }

      body {
        background-color: #ffffff !important;
        background: #ffffff !important;
        color: #000000 !important;
        margin: 0 !important;
        padding: 0 !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
      }

      .preview-container,
      .invoice-box,
      .container {
        width: 100% !important;
        max-width: 100% !important;
        padding: 0 !important;
        margin: 0 !important;
        background: transparent !important;
        box-shadow: none !important;
        border: none !important;
        display: block !important;
      }

      /* 80mm Print Mode */
      body.size-mode-80mm {
        width: 80mm !important;
      }
      body.size-mode-80mm .layout-80mm {
        display: block !important;
        width: 76mm !important;
        max-width: 76mm !important;
        margin: 0 auto !important;
        padding: 2mm 3mm !important;
        box-sizing: border-box !important;
        box-shadow: none !important;
        font-family: 'Courier New', Courier, monospace !important;
        color: #000 !important;
      }

      /* A4 Print Mode */
      body.size-mode-A4 {
        width: 100% !important;
      }
      body.size-mode-A4 .layout-A4 {
        display: block !important;
        width: 100% !important;
        max-width: 100% !important;
        margin: 0 auto !important;
        padding: 0 !important;
        box-sizing: border-box !important;
        box-shadow: none !important;
      }

      /* A5 Print Mode */
      body.size-mode-A5 {
        width: 100% !important;
      }
      body.size-mode-A5 .layout-A5 {
        display: block !important;
        width: 100% !important;
        max-width: 100% !important;
        margin: 0 auto !important;
        padding: 0 !important;
        box-sizing: border-box !important;
        box-shadow: none !important;
      }

      .table-bordered th, .table-bordered td {
        border: 1px solid #777 !important;
      }

      tr {
        page-break-inside: avoid !important;
        break-inside: avoid !important;
      }
    }
  </style>
</head>
<body class="size-mode-<?= $current_size ?>">

<!-- Top Control Toolbar (Hidden in Print) -->
<div class="cs-hide_print control-bar" style="background: linear-gradient(135deg, #37475a, #232f3e); padding: 10px 20px; color: #fff; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 10px rgba(0,0,0,0.3); position: sticky; top: 0; z-index: 1000;">
    <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
        <span style="font-weight: 600; font-size: 13px;"><i class="fa fa-print"></i> Paper Size:</span>
        <div class="btn-group" role="group">
            <button type="button" class="btn btn-sm btn-default print-size-btn <?= $current_size === '80mm' ? 'active btn-primary' : '' ?>" data-size="80mm" onclick="switchPrintSize('80mm')">
                <i class="fa fa-file-text-o"></i> Thermal 80mm
            </button>
            <button type="button" class="btn btn-sm btn-default print-size-btn <?= $current_size === 'A4' ? 'active btn-primary' : '' ?>" data-size="A4" onclick="switchPrintSize('A4')">
                <i class="fa fa-file-text"></i> A4 Standard
            </button>
            <button type="button" class="btn btn-sm btn-default print-size-btn <?= $current_size === 'A5' ? 'active btn-primary' : '' ?>" data-size="A5" onclick="switchPrintSize('A5')">
                <i class="fa fa-file-o"></i> A5 Compact
            </button>
        </div>
    </div>
    <div style="display: flex; gap: 10px;">
        <button type="button" class="btn btn-sm btn-success" onclick="window.print()" style="font-weight: 600; padding: 6px 18px;">
            <i class="fa fa-print"></i> Print Invoice
        </button>
        <button type="button" class="btn btn-sm btn-default" onclick="window.close()" style="font-weight: 600; padding: 6px 14px; color: #333;">
            <i class="fa fa-times"></i> Close
        </button>
    </div>
</div>

<!-- Main Invoice Preview Area -->
<div class="container preview-container" style="margin-top: 20px; margin-bottom: 40px;">
    <div style="display: flex; justify-content: center; background: #eaeded; padding: 25px 15px; border-radius: 8px; min-height: 500px; box-shadow: inset 0 0 8px rgba(0,0,0,0.1);">
        <div class="invoice-box" style="background: #ffffff; box-shadow: 0 4px 15px rgba(0,0,0,0.15); border-radius: 4px;">
            
            <!-- ==================== 80mm THERMAL RECEIPT LAYOUT ==================== -->
            <div class="layout-80mm size-layout" style="width: 290px; padding: 12px; font-family: 'Courier New', Courier, monospace; font-size: 11px; color: #000; line-height: 1.3;">
                <div style="text-align: center; margin-bottom: 8px;">
                    <div style="font-size: 17px; font-weight: bold; color: #000; text-transform: uppercase;"><?= htmlspecialchars($store_name_display) ?></div>
                    <div style="font-size: 10px; margin-top: 2px;"><?= htmlspecialchars($store_address_display) ?></div>
                    <div style="font-size: 10px;">GSTIN: <?= htmlspecialchars($store_gstin_display) ?></div>
                    <div style="font-size: 10px;">Ph: <?= htmlspecialchars($store_phone_display) ?></div>
                </div>
                
                <div style="border-top: 1px dashed #000; margin: 6px 0;"></div>
                <div style="font-size: 10px;">
                    <div><strong>Bill No:</strong> <?= htmlspecialchars($bill_no) ?></div>
                    <div><strong>Date:</strong> <?= $bill_date_display ?> <?= $bill_time_display ?></div>
                    <div><strong>Customer:</strong> <?= htmlspecialchars($account_name) ?></div>
                    <div><strong>Mobile:</strong> <?= htmlspecialchars($mobile_number) ?></div>
                    <div><strong>Pay Mode:</strong> <?= htmlspecialchars($pay_mode_display) ?></div>
                </div>
                <div style="border-top: 1px dashed #000; margin: 6px 0;"></div>
                
                <table style="width: 100%; border-collapse: collapse; font-size: 10px; margin: 4px 0;">
                    <thead>
                        <tr style="border-bottom: 1px dashed #000;">
                            <th style="text-align: left; padding-bottom: 4px;">Item</th>
                            <th style="text-align: center; padding-bottom: 4px; width: 32px;">Qty</th>
                            <th style="text-align: right; padding-bottom: 4px; width: 48px;">Rate</th>
                            <th style="text-align: right; padding-bottom: 4px; width: 55px;">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $itm): ?>
                        <tr>
                            <td style="padding: 3px 0; text-align: left; word-break: break-word;">
                                <?= htmlspecialchars($itm['item_name'] ?? '') ?>
                                <?php if (!empty($itm['size'])): ?><span style="font-size: 9px; color: #333;">(<?= htmlspecialchars($itm['size']) ?>)</span><?php endif; ?>
                            </td>
                            <td style="padding: 3px 0; text-align: center; vertical-align: top;"><?= $itm['qty'] ?? 1 ?></td>
                            <td style="padding: 3px 0; text-align: right; vertical-align: top;"><?= number_format((float)($itm['rate'] ?? 0), 2) ?></td>
                            <td style="padding: 3px 0; text-align: right; vertical-align: top;"><?= number_format((float)($itm['amount'] ?? 0), 2) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <div style="border-top: 1px dashed #000; margin: 6px 0;"></div>
                
                <div style="font-size: 10px;">
                    <div style="display: flex; justify-content: space-between;">
                        <span>Total Items / Qty:</span>
                        <span><?= count($items) ?> / <?= $total_items_qty ?></span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span>Taxable Value:</span>
                        <span>₹ <?= number_format($total_taxable_amount, 2) ?></span>
                    </div>
                    <?php if ($under_gst == 'Inter'): ?>
                        <?php if ($total_gst_amount > 0): ?>
                        <div style="display: flex; justify-content: space-between;">
                            <span>IGST<?= $highest_gst_rate > 0 ? ' (' . $highest_gst_rate . '%)' : '' ?>:</span>
                            <span>₹ <?= number_format($total_gst_amount, 2) ?></span>
                        </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <?php if ($total_gst_amount > 0): ?>
                        <div style="display: flex; justify-content: space-between;">
                            <span>CGST<?= $highest_gst_rate > 0 ? ' (' . ($highest_gst_rate / 2) . '%)' : '' ?>:</span>
                            <span>₹ <?= number_format($total_gst_amount / 2, 2) ?></span>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span>SGST<?= $highest_gst_rate > 0 ? ' (' . ($highest_gst_rate / 2) . '%)' : '' ?>:</span>
                            <span>₹ <?= number_format($total_gst_amount / 2, 2) ?></span>
                        </div>
                        <?php endif; ?>
                    <?php endif; ?>
                    <?php if ($extra_charges > 0): ?>
                    <div style="display: flex; justify-content: space-between;">
                        <span>Extra Charges:</span>
                        <span>₹ <?= number_format($extra_charges, 2) ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if ($discount_total_amount > 0): ?>
                    <div style="display: flex; justify-content: space-between;">
                        <span>Discount:</span>
                        <span>-₹ <?= number_format($discount_total_amount, 2) ?></span>
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
                    <div>Visit Again! <?= htmlspecialchars($store_web_display) ?></div>
                </div>
            </div>

            <!-- ==================== A4 STANDARD INVOICE LAYOUT ==================== -->
            <div class="layout-A4 size-layout" style="width: 780px; padding: 30px; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 12px; color: #333; line-height: 1.4;">
                <div style="display: flex; justify-content: space-between; border-bottom: 2px solid #37475a; padding-bottom: 15px; margin-bottom: 20px;">
                    <div>
                        <h2 style="margin: 0; color: #37475a; font-weight: bold; font-size: 24px; text-transform: uppercase;"><?= htmlspecialchars($store_name_display) ?></h2>
                        <div style="color: #555; font-size: 11px; margin-top: 4px;">
                            <?= htmlspecialchars($store_address_display) ?><br>
                            <strong>GSTIN:</strong> <?= htmlspecialchars($store_gstin_display) ?> | <strong>Phone:</strong> <?= htmlspecialchars($store_phone_display) ?><br>
                            <strong>Email:</strong> <?= htmlspecialchars($store_email_display) ?> | <strong>Web:</strong> <?= htmlspecialchars($store_web_display) ?>
                        </div>
                    </div>
                    <div style="text-align: right;">
                        <h3 style="margin: 0; color: #37475a; font-size: 20px; font-weight: bold; text-transform: uppercase;">TAX INVOICE</h3>
                        <div style="margin-top: 8px; font-size: 12px;">
                            <div><strong>Invoice No:</strong> <span style="color: #000; font-weight:600;"><?= htmlspecialchars($bill_no) ?></span></div>
                            <div><strong>Date:</strong> <?= $bill_date_display ?></div>
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
                            <td style="padding: 8px; text-align: center; color: #555;"><?= htmlspecialchars($itm['barcode_number'] ?? ($itm['article_no'] ?? '-')) ?></td>
                            <td style="padding: 8px; text-align: center; color: #555;"><?= htmlspecialchars($itm['size'] ?? '-') ?><?= !empty($itm['color']) ? ' / ' . htmlspecialchars($itm['color']) : '' ?></td>
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
                            <strong>Terms &amp; Conditions:</strong><br>
                            1. Goods once sold will not be taken back without original bill.<br>
                            2. Subject to jurisdiction of store city.<br>
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
                                <?php if ($total_gst_amount > 0): ?>
                                <tr>
                                    <td style="padding: 4px 0; text-align: left;">IGST Total:</td>
                                    <td style="padding: 4px 0; text-align: right;">₹ <?= number_format($total_gst_amount, 2) ?></td>
                                </tr>
                                <?php endif; ?>
                            <?php else: ?>
                                <?php if ($total_gst_amount > 0): ?>
                                <tr>
                                    <td style="padding: 4px 0; text-align: left;">CGST Total:</td>
                                    <td style="padding: 4px 0; text-align: right;">₹ <?= number_format($total_gst_amount / 2, 2) ?></td>
                                </tr>
                                <tr>
                                    <td style="padding: 4px 0; text-align: left;">SGST Total:</td>
                                    <td style="padding: 4px 0; text-align: right;">₹ <?= number_format($total_gst_amount / 2, 2) ?></td>
                                </tr>
                                <?php endif; ?>
                            <?php endif; ?>
                            <?php if ($extra_charges > 0): ?>
                            <tr>
                                <td style="padding: 4px 0; text-align: left;">Extra Charges:</td>
                                <td style="padding: 4px 0; text-align: right;">₹ <?= number_format($extra_charges, 2) ?></td>
                            </tr>
                            <?php endif; ?>
                            <?php if ($discount_total_amount > 0): ?>
                            <tr>
                                <td style="padding: 4px 0; text-align: left;">Discount / Adjustment:</td>
                                <td style="padding: 4px 0; text-align: right;">-₹ <?= number_format($discount_total_amount, 2) ?></td>
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
            <div class="layout-A5 size-layout" style="width: 550px; padding: 20px; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 11px; color: #333; line-height: 1.3;">
                <div style="display: flex; justify-content: space-between; border-bottom: 2px solid #37475a; padding-bottom: 8px; margin-bottom: 12px;">
                    <div>
                        <h3 style="margin: 0; color: #37475a; font-weight: bold; font-size: 18px; text-transform: uppercase;"><?= htmlspecialchars($store_name_display) ?></h3>
                        <div style="color: #555; font-size: 10px;">
                            <?= htmlspecialchars($store_address_display) ?><br>
                            <strong>GSTIN:</strong> <?= htmlspecialchars($store_gstin_display) ?> | Ph: <?= htmlspecialchars($store_phone_display) ?>
                        </div>
                    </div>
                    <div style="text-align: right;">
                        <h4 style="margin: 0; color: #37475a; font-size: 15px; font-weight: bold;">TAX INVOICE (A5)</h4>
                        <div style="font-size: 11px; margin-top: 4px;">
                            <strong>Bill No:</strong> <?= htmlspecialchars($bill_no) ?><br>
                            <strong>Date:</strong> <?= $bill_date_display ?>
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
                        Thank you for shopping with us!<br>
                        <?= htmlspecialchars($store_web_display) ?>
                    </div>
                    <div style="width: 45%;">
                        <div style="display: flex; justify-content: space-between;">
                            <span>Taxable:</span>
                            <span>₹ <?= number_format($total_taxable_amount, 2) ?></span>
                        </div>
                        <?php if ($total_gst_amount > 0): ?>
                        <div style="display: flex; justify-content: space-between;">
                            <span>GST Total:</span>
                            <span>₹ <?= number_format($total_gst_amount, 2) ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if ($extra_charges > 0): ?>
                        <div style="display: flex; justify-content: space-between;">
                            <span>Extra Charges:</span>
                            <span>₹ <?= number_format($extra_charges, 2) ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if ($discount_total_amount > 0): ?>
                        <div style="display: flex; justify-content: space-between;">
                            <span>Discount:</span>
                            <span>-₹ <?= number_format($discount_total_amount, 2) ?></span>
                        </div>
                        <?php endif; ?>
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

<script src="assets/plugins/jQuery/jquery-1.12.4.min.js"></script>
<script src="assets/bootstrap/js/bootstrap.min.js"></script>
<script>
var currentPrintSize = '<?= $current_size ?>';

var pageCssMap = {
    '80mm': '@page { size: 80mm auto; margin: 0; }',
    'A4':   '@page { size: A4 portrait; margin: 10mm 12mm; }',
    'A5':   '@page { size: A5 portrait; margin: 8mm 10mm; }'
};

function switchPrintSize(size) {
    currentPrintSize = size;
    
    // Update body class
    document.body.className = 'size-mode-' + size;
    
    // Update buttons
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

    // Update dynamic @page CSS
    var dynStyle = document.getElementById('dynamic-page-size-style');
    if (dynStyle && pageCssMap[size]) {
        dynStyle.innerHTML = pageCssMap[size];
    }

    // Update URL parameter without reload
    if (window.history && window.history.replaceState) {
        var url = new URL(window.location.href);
        url.searchParams.set('size', size);
        window.history.replaceState({}, document.title, url.toString());
    }
}

// Auto-print support when opened in a new window/tab with &auto_print=1
<?php if ($auto_print): ?>
window.addEventListener('load', function() {
    // Only auto-print if opened as the top window (not inside an iframe)
    if (window.self === window.top) {
        setTimeout(function() {
            window.print();
        }, 350);
    }
});
<?php endif; ?>
</script>
</body>
</html>