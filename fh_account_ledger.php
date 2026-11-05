<?php 
include('top.php');

// Handle CSV Export
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    $exp_code = mysqli_real_escape_string($conn, trim($_GET['account_code'] ?? ''));
    $exp_id = (int)($_GET['account_id'] ?? 0);
    $acc_res = mysqli_query($conn, "SELECT * FROM tbl_account_master WHERE account_code = '$exp_code' OR id = '$exp_id' LIMIT 1");
    $acc = mysqli_fetch_assoc($acc_res);
    $acc_name = $acc['account_name'] ?? 'Account';
    $acc_code = $acc['account_code'] ?? '';
    $opening_bal = is_numeric($acc['opening_balance'] ?? 0) ? (float)$acc['opening_balance'] : 0.00;

    $where_sql = " WHERE (party_code = '$acc_code' OR (party_code = '' AND account_name = '" . mysqli_real_escape_string($conn, $acc_name) . "'))";
    if (!empty($_GET['from_date'])) {
        $from = mysqli_real_escape_string($conn, $_GET['from_date']);
        $where_sql .= " AND date >= '$from'";
    }
    if (!empty($_GET['to_date'])) {
        $to = mysqli_real_escape_string($conn, $_GET['to_date']);
        $where_sql .= " AND date <= '$to'";
    }
    if (!empty($_GET['payment_type'])) {
        $pt = mysqli_real_escape_string($conn, strtolower($_GET['payment_type']));
        $where_sql .= " AND LOWER(payment_type) = '$pt'";
    }

    $tx_res = mysqli_query($conn, "SELECT * FROM tbl_expensescreation_master $where_sql ORDER BY id ASC");

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=Ledger_' . preg_replace('/[^a-zA-Z0-9_-]/', '_', $acc_code) . '_' . date('Ymd_His') . '.csv');

    $output = fopen('php://output', 'w');
    fputcsv($output, ['Account Ledger Statement']);
    fputcsv($output, ['Account Name:', $acc_name, 'Account Code:', $acc_code]);
    fputcsv($output, ['Mobile:', $acc['mobile_no_1'] ?? '', 'Contact Person:', $acc['contact_name'] ?? '']);
    fputcsv($output, ['Generated Date:', date('d-m-Y H:i:s')]);
    fputcsv($output, []);
    fputcsv($output, ['#', 'Date', 'Voucher / Ref No', 'Type', 'Particulars / Head', 'Payment Mode', 'Remarks', 'Debit (Dr)', 'Credit (Cr)', 'Balance']);

    $running_bal = $opening_bal;
    fputcsv($output, ['-', $acc['date'] ?? '', '-', 'OPENING', 'Opening Balance b/f', '-', '-', '-', '-', number_format($running_bal, 2, '.', '')]);

    $i = 1;
    $tot_dr = 0;
    $tot_cr = 0;
    if ($tx_res) {
        while ($tx = mysqli_fetch_assoc($tx_res)) {
            $amt = (float)($tx['amount'] ?? 0);
            $type = strtolower(trim($tx['payment_type'] ?? 'debit'));
            if ($type === 'credit') {
                $dr = 0;
                $cr = $amt;
                $tot_cr += $amt;
                $running_bal += $amt;
            } else {
                $dr = $amt;
                $cr = 0;
                $tot_dr += $amt;
                $running_bal -= $amt;
            }
            fputcsv($output, [
                $i++,
                $tx['date'] ?? '',
                $tx['expcode'] ?? '',
                strtoupper($tx['payment_type'] ?? 'Debit'),
                $tx['exphead_name'] ?? '',
                $tx['paymode_name'] ?? '',
                $tx['remarks'] ?? '',
                $dr > 0 ? number_format($dr, 2, '.', '') : '0.00',
                $cr > 0 ? number_format($cr, 2, '.', '') : '0.00',
                number_format($running_bal, 2, '.', '')
            ]);
        }
    }
    fputcsv($output, []);
    fputcsv($output, ['Total', '', '', '', '', '', '', number_format($tot_dr, 2, '.', ''), number_format($tot_cr, 2, '.', ''), number_format($running_bal, 2, '.', '')]);
    exit;
}

// Get filter inputs
$account_code = mysqli_real_escape_string($conn, trim($_GET['account_code'] ?? ''));
$account_id = (int)($_GET['account_id'] ?? 0);
$from_date = mysqli_real_escape_string($conn, trim($_GET['from_date'] ?? ''));
$to_date = mysqli_real_escape_string($conn, trim($_GET['to_date'] ?? ''));
$payment_type = mysqli_real_escape_string($conn, trim($_GET['payment_type'] ?? ''));

// Fetch target account
$account_row = null;
if (!empty($account_code) || $account_id > 0) {
    $where_target = $account_id > 0 ? "id = '$account_id'" : "account_code = '$account_code'";
    $acc_q = mysqli_query($conn, "SELECT * FROM tbl_account_master WHERE $where_target LIMIT 1");
    if ($acc_q && mysqli_num_rows($acc_q) > 0) {
        $account_row = mysqli_fetch_assoc($acc_q);
    }
}

// Fallback to first available account if none found
if (!$account_row) {
    $first_acc_q = mysqli_query($conn, "SELECT * FROM tbl_account_master ORDER BY id DESC LIMIT 1");
    if ($first_acc_q && mysqli_num_rows($first_acc_q) > 0) {
        $account_row = mysqli_fetch_assoc($first_acc_q);
    }
}

$cur_acc_code = $account_row['account_code'] ?? '';
$cur_acc_name = $account_row['account_name'] ?? '';
$opening_bal = is_numeric($account_row['opening_balance'] ?? 0) ? (float)$account_row['opening_balance'] : 0.00;

// Fetch all accounts for dropdown switcher
$all_accounts_res = mysqli_query($conn, "SELECT id, account_code, account_name, contact_name, mobile_no_1 FROM tbl_account_master ORDER BY account_name ASC");

// Fetch transactions for current account
$tx_where = " WHERE (party_code = '$cur_acc_code' OR (party_code = '' AND account_name = '$cur_acc_name'))";
if (!empty($from_date)) {
    $tx_where .= " AND date >= '$from_date'";
}
if (!empty($to_date)) {
    $tx_where .= " AND date <= '$to_date'";
}
if (!empty($payment_type)) {
    $pt_clean = strtolower($payment_type);
    $tx_where .= " AND LOWER(payment_type) = '$pt_clean'";
}

$tx_query = mysqli_query($conn, "SELECT * FROM tbl_expensescreation_master $tx_where ORDER BY id ASC");
$transactions = [];
$total_debit = 0.00;
$total_credit = 0.00;
$debit_count = 0;
$credit_count = 0;

if ($tx_query) {
    while ($tx = mysqli_fetch_assoc($tx_query)) {
        $amt = (float)($tx['amount'] ?? 0);
        $type = strtolower(trim($tx['payment_type'] ?? 'debit'));
        if ($type === 'credit') {
            $total_credit += $amt;
            $credit_count++;
        } else {
            $total_debit += $amt;
            $debit_count++;
        }
        $transactions[] = $tx;
    }
}

// Calculate current net closing balance
$net_balance = $opening_bal + $total_credit - $total_debit;

// Also fetch sales bills for this party if available
$bills_res = mysqli_query($conn, "SELECT bill_number, MAX(purchage_date) as purchage_date, MAX(pay_mode) as pay_mode, MAX(status) as status, SUM(qty) as total_qty, SUM(amount) as bill_amount FROM tbl_bill WHERE (ac_code = '$cur_acc_code' OR (ac_code = '' AND account_name = '$cur_acc_name')) GROUP BY bill_number ORDER BY purchage_date DESC LIMIT 50");
$bills = [];
if ($bills_res) {
    while ($b = mysqli_fetch_assoc($bills_res)) {
        $bills[] = $b;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="utf-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <title>Party Account Ledger || <?php echo htmlspecialchars($cur_acc_name); ?> - TEJASERP</title>
   <?php include('include/css.php');?>  
   <style>
      .ledger-header-card {
         background: #ffffff;
         border: 1px solid #e1e6ef;
         border-radius: 4px;
         padding: 18px 20px;
         margin-bottom: 20px;
         box-shadow: 0 1px 3px rgba(0,0,0,0.06);
      }
      .ledger-title {
         font-size: 20px;
         font-weight: 700;
         color: #2c3e50;
         margin: 0 0 5px 0;
         display: flex;
         align-items: center;
         gap: 10px;
      }
      .ledger-subinfo {
         font-size: 13px;
         color: #6c757d;
         margin-bottom: 12px;
      }
      .account-meta-grid {
         display: grid;
         grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
         gap: 12px;
         background: #f8fafc;
         border: 1px solid #e9ecef;
         border-radius: 4px;
         padding: 12px 16px;
      }
      .meta-item-label {
         font-size: 11px;
         text-transform: uppercase;
         letter-spacing: 0.5px;
         color: #7f8c8d;
         margin-bottom: 2px;
         font-weight: 600;
      }
      .meta-item-val {
         font-size: 14px;
         font-weight: 600;
         color: #2c3e50;
      }
      .kpi-card {
         border-radius: 6px;
         padding: 15px 18px;
         color: #fff;
         margin-bottom: 20px;
         box-shadow: 0 2px 6px rgba(0,0,0,0.08);
         position: relative;
         overflow: hidden;
      }
      .kpi-card .kpi-icon {
         position: absolute;
         right: 15px;
         top: 15px;
         font-size: 40px;
         opacity: 0.25;
      }
      .kpi-card .kpi-title {
         font-size: 12px;
         text-transform: uppercase;
         font-weight: 600;
         letter-spacing: 0.5px;
         opacity: 0.9;
         margin-bottom: 6px;
      }
      .kpi-card .kpi-val {
         font-size: 24px;
         font-weight: 700;
         line-height: 1.2;
      }
      .kpi-card .kpi-sub {
         font-size: 11px;
         margin-top: 5px;
         opacity: 0.85;
      }
      .kpi-primary { background: linear-gradient(135deg, #1f3a52, #34495e); }
      .kpi-success { background: linear-gradient(135deg, #27ae60, #2ecc71); }
      .kpi-danger  { background: linear-gradient(135deg, #c0392b, #e74c3c); }
      .kpi-info    { background: linear-gradient(135deg, #0088cc, #2980b9); }
      .badge-debit {
         background-color: #fce8e6;
         color: #c5221f;
         border: 1px solid #fad2cf;
         padding: 3px 8px;
         border-radius: 3px;
         font-weight: 600;
         font-size: 11px;
      }
      .badge-credit {
         background-color: #e6f4ea;
         color: #137333;
         border: 1px solid #ceead6;
         padding: 3px 8px;
         border-radius: 3px;
         font-weight: 600;
         font-size: 11px;
      }
      .table-ledger th {
         background-color: #2A3F54 !important;
         color: #ffffff !important;
         font-weight: 600;
         font-size: 13px;
         vertical-align: middle;
      }
      .table-ledger tbody tr:hover {
         background-color: #f7f9fa;
      }
      .ledger-filter-box {
         background: #fdfdfe;
         border: 1px solid #e2e8f0;
         border-radius: 4px;
         padding: 12px 15px;
         margin-bottom: 15px;
      }
      .dr-text { color: #c0392b; font-weight: 600; }
      .cr-text { color: #27ae60; font-weight: 600; }
      .bal-text { font-weight: 700; }

      @media print {
         body { background: #fff !important; }
         .main-header, .main-sidebar, .sidebar-mini, .menu-header, .no-print, .btn, .search-box-group, .pagination, aside, header {
            display: none !important;
         }
         .content-wrapper { margin: 0 !important; padding: 0 !important; }
         .panel, .panel-bd { border: none !important; box-shadow: none !important; }
         .ledger-header-card { border: none !important; box-shadow: none !important; padding: 0 !important; }
         .table-ledger th { background-color: #eee !important; color: #000 !important; border: 1px solid #ccc !important; }
         .table-ledger td { border: 1px solid #ccc !important; }
         .print-only-header { display: block !important; margin-bottom: 20px; }
      }
      @media screen {
         .print-only-header { display: none; }
      }
   </style>
</head>
<body class="hold-transition sidebar-mini">
   <div class="wrapper">
      <?php include('include/header-top-bar.php');?>

      <!-- Left side column -->
      <aside class="main-sidebar">
         <?php include('include/sidebar-left.php');?>
         <?php include('include/toggle_switch_list.php');?> 
      </aside>
      
      <!-- Content Wrapper -->
      <div class="content-wrapper">
         <div class="container-fluid no-print">
            <div class="row">
               <?php include('include/menu-header.php');?>
            </div>
         </div> 

         <!-- Main content -->
         <section class="content" style="padding-top: 15px;">
            
            <!-- Print Header -->
            <div class="print-only-header">
               <h2 style="margin: 0; text-align: center; font-weight: 700;">TEJAS ERP - PARTY ACCOUNT STATEMENT</h2>
               <p style="text-align: center; color: #666; margin: 4px 0 15px 0;">Generated on <?php echo date('d-M-Y h:i A'); ?></p>
               <hr style="border-top: 2px solid #333;">
            </div>

            <!-- Account Header Card -->
            <div class="ledger-header-card">
               <div class="row">
                  <div class="col-sm-7">
                     <h3 class="ledger-title">
                        <i class="fa fa-book text-warning"></i>
                        <span><?php echo htmlspecialchars($cur_acc_name ?: 'Party Account Ledger'); ?></span>
                        <span class="label label-primary" style="font-size: 13px; font-weight: normal;"><?php echo htmlspecialchars($cur_acc_code); ?></span>
                     </h3>
                     <div class="ledger-subinfo">
                        <strong>Party Type:</strong> <?php echo htmlspecialchars($account_row['party_type'] ?? 'General'); ?> | 
                        <strong>Group:</strong> <?php echo htmlspecialchars($account_row['group_name'] ?? 'N/A'); ?> | 
                        <strong>Created Date:</strong> <?php echo htmlspecialchars($account_row['date'] ?? 'N/A'); ?>
                     </div>
                  </div>
                  <div class="col-sm-5 text-right no-print">
                     <div class="btn-group" style="margin-bottom: 8px;">
                        <a href="fh_accountmastercreation_list.php" class="btn btn-default btn-sm">
                           <i class="fa fa-arrow-left mr-5"></i> Back to Accounts
                        </a>
                        <a href="fh_paymentmastercreation.php?party_code=<?php echo urlencode($cur_acc_code); ?>" class="btn btn-primary btn-sm">
                           <i class="fa fa-plus mr-5"></i> New Payment
                        </a>
                        <button onclick="window.print()" class="btn btn-info btn-sm">
                           <i class="fa fa-print mr-5"></i> Print
                        </button>
                        <a href="fh_account_ledger.php?<?php echo http_build_query(array_merge($_GET, ['export' => 'csv'])); ?>" class="btn btn-success btn-sm">
                           <i class="fa fa-download mr-5"></i> Export CSV
                        </a>
                     </div>
                     <div>
                        <!-- Account Switcher Dropdown -->
                        <div class="input-group input-group-sm pull-right" style="max-width: 280px;">
                           <span class="input-group-addon"><i class="fa fa-user"></i> Switch:</span>
                           <select class="form-control" onchange="if(this.value) window.location.href='fh_account_ledger.php?account_code='+encodeURIComponent(this.value)">
                              <?php 
                              if ($all_accounts_res) {
                                  mysqli_data_seek($all_accounts_res, 0);
                                  while ($a_opt = mysqli_fetch_assoc($all_accounts_res)) {
                                      $sel = ($a_opt['account_code'] === $cur_acc_code) ? 'selected' : '';
                                      echo '<option value="' . htmlspecialchars($a_opt['account_code']) . '" ' . $sel . '>' . htmlspecialchars($a_opt['account_name']) . ' (' . htmlspecialchars($a_opt['account_code']) . ')</option>';
                                  }
                              }
                              ?>
                           </select>
                        </div>
                     </div>
                  </div>
               </div>

               <!-- Meta Info Grid -->
               <div class="account-meta-grid" style="margin-top: 10px;">
                  <div>
                     <div class="meta-item-label">Contact Person</div>
                     <div class="meta-item-val"><?php echo htmlspecialchars($account_row['contact_name'] ?? '-'); ?></div>
                  </div>
                  <div>
                     <div class="meta-item-label">Mobile Number</div>
                     <div class="meta-item-val"><?php echo htmlspecialchars($account_row['mobile_no_1'] ?? '-'); ?></div>
                  </div>
                  <div>
                     <div class="meta-item-label">GST Number</div>
                     <div class="meta-item-val"><?php echo htmlspecialchars($account_row['gst_no'] ?? '-'); ?></div>
                  </div>
                  <div>
                     <div class="meta-item-label">Location / City</div>
                     <div class="meta-item-val"><?php echo htmlspecialchars($account_row['location_1'] ?? '-'); ?></div>
                  </div>
                  <div>
                     <div class="meta-item-label">Current Account Status</div>
                     <div class="meta-item-val">
                        <?php if (($account_row['status'] ?? 'Active') === 'Active'): ?>
                           <span class="label label-success">Active</span>
                        <?php else: ?>
                           <span class="label label-danger">Inactive</span>
                        <?php endif; ?>
                     </div>
                  </div>
               </div>
            </div>

            <!-- Financial KPI Stats Row -->
            <div class="row">
               <div class="col-xs-12 col-sm-6 col-md-3">
                  <div class="kpi-card kpi-primary">
                     <i class="fa fa-university kpi-icon"></i>
                     <div class="kpi-title">Opening Balance</div>
                     <div class="kpi-val">&#8377; <?php echo number_format($opening_bal, 2); ?></div>
                     <div class="kpi-sub">Initial Balance from Master</div>
                  </div>
               </div>
               <div class="col-xs-12 col-sm-6 col-md-3">
                  <div class="kpi-card kpi-success">
                     <i class="fa fa-plus-circle kpi-icon"></i>
                     <div class="kpi-title">Total Credit (+)</div>
                     <div class="kpi-val">&#8377; <?php echo number_format($total_credit, 2); ?></div>
                     <div class="kpi-sub"><?php echo $credit_count; ?> Credit / Receipt Entry(s)</div>
                  </div>
               </div>
               <div class="col-xs-12 col-sm-6 col-md-3">
                  <div class="kpi-card kpi-danger">
                     <i class="fa fa-minus-circle kpi-icon"></i>
                     <div class="kpi-title">Total Debit (-)</div>
                     <div class="kpi-val">&#8377; <?php echo number_format($total_debit, 2); ?></div>
                     <div class="kpi-sub"><?php echo $debit_count; ?> Debit / Payment Entry(s)</div>
                  </div>
               </div>
               <div class="col-xs-12 col-sm-6 col-md-3">
                  <div class="kpi-card kpi-info">
                     <i class="fa fa-money kpi-icon"></i>
                     <div class="kpi-title">Current Net Balance</div>
                     <div class="kpi-val">&#8377; <?php echo number_format($net_balance, 2); ?></div>
                     <div class="kpi-sub">
                        <?php 
                        if ($net_balance >= 0) {
                            echo '<span style="color:#d4edda; font-weight:600;"><i class="fa fa-check-circle"></i> In Credit (Cr)</span>';
                        } else {
                            echo '<span style="color:#f8d7da; font-weight:600;"><i class="fa fa-exclamation-circle"></i> In Debit (Dr)</span>';
                        }
                        ?>
                     </div>
                  </div>
               </div>
            </div>

            <!-- Ledger Main Panel -->
            <div class="panel panel-bd lobidisable">
               <div class="panel-heading">
                  <div class="panel-title" style="display: flex; justify-content: space-between; align-items: center;">
                     <span class="fs-15">
                        <i class="fa fa-history mr-5 text-primary"></i>
                        <strong>Payment & Transaction History</strong>
                     </span>
                     <span class="badge" style="background:#2A3F54;"><?php echo count($transactions); ?> Transactions</span>
                  </div>
               </div>
               
               <div class="panel-body">
                  <!-- Filter Bar -->
                  <div class="ledger-filter-box no-print">
                     <form method="GET" action="fh_account_ledger.php" class="form-inline">
                        <input type="hidden" name="account_code" value="<?php echo htmlspecialchars($cur_acc_code); ?>">
                        <input type="hidden" name="account_id" value="<?php echo htmlspecialchars($account_row['id'] ?? 0); ?>">
                        
                        <div class="form-group" style="margin-right: 15px;">
                           <label style="margin-right: 5px; font-size: 12px; color: #555;">From Date:</label>
                           <input type="date" name="from_date" class="form-control input-sm" value="<?php echo htmlspecialchars($from_date); ?>">
                        </div>
                        
                        <div class="form-group" style="margin-right: 15px;">
                           <label style="margin-right: 5px; font-size: 12px; color: #555;">To Date:</label>
                           <input type="date" name="to_date" class="form-control input-sm" value="<?php echo htmlspecialchars($to_date); ?>">
                        </div>
                        
                        <div class="form-group" style="margin-right: 15px;">
                           <label style="margin-right: 5px; font-size: 12px; color: #555;">Type:</label>
                           <select name="payment_type" class="form-control input-sm">
                              <option value="">All Transactions</option>
                              <option value="Debit" <?php if(strtolower($payment_type) === 'debit') echo 'selected'; ?>>Debit Only (-)</option>
                              <option value="Credit" <?php if(strtolower($payment_type) === 'credit') echo 'selected'; ?>>Credit Only (+)</option>
                           </select>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-sm" style="margin-right: 6px;">
                           <i class="fa fa-filter mr-5"></i> Apply Filter
                        </button>
                        
                        <a href="fh_account_ledger.php?account_code=<?php echo urlencode($cur_acc_code); ?>" class="btn btn-default btn-sm">
                           <i class="fa fa-refresh mr-5"></i> Reset
                        </a>
                     </form>
                  </div>

                  <!-- Ledger Table -->
                  <div class="table-responsive">
                     <table class="table table-bordered table-striped table-hover table-ledger" id="ledgerTable">
                        <thead>
                           <tr>
                              <th style="width: 40px; text-align: center;">#</th>
                              <th style="width: 100px;">Date</th>
                              <th style="width: 120px;">Voucher No</th>
                              <th style="width: 90px; text-align: center;">Type</th>
                              <th>Particulars / Head</th>
                              <th style="width: 110px;">Payment Mode</th>
                              <th>Remarks / Narration</th>
                              <th style="width: 120px; text-align: right;">Debit (Dr)</th>
                              <th style="width: 120px; text-align: right;">Credit (Cr)</th>
                              <th style="width: 130px; text-align: right;">Running Balance</th>
                           </tr>
                        </thead>
                        <tbody>
                           <!-- Row 0: Opening Balance -->
                           <tr style="background-color: #fbfbfc; font-weight: 600;">
                              <td style="text-align: center; color: #888;">-</td>
                              <td><?php echo htmlspecialchars($account_row['date'] ?? '-'); ?></td>
                              <td><span class="text-muted">OPENING</span></td>
                              <td style="text-align: center;"><span class="label label-default" style="font-size: 10px;">OPENING</span></td>
                              <td colspan="3"><i class="fa fa-flag mr-5 text-warning"></i> <strong>Opening Balance Brought Forward (b/f)</strong></td>
                              <td style="text-align: right;">-</td>
                              <td style="text-align: right;">-</td>
                              <td style="text-align: right;" class="bal-text">&#8377; <?php echo number_format($opening_bal, 2); ?></td>
                           </tr>

                           <?php 
                           $running = $opening_bal;
                           $sr = 1;
                           if (!empty($transactions)):
                              foreach ($transactions as $tx):
                                 $amt = (float)($tx['amount'] ?? 0);
                                 $type = strtolower(trim($tx['payment_type'] ?? 'debit'));
                                 $is_credit = ($type === 'credit');

                                 if ($is_credit) {
                                    $dr_cell = '-';
                                    $cr_cell = '&#8377; ' . number_format($amt, 2);
                                    $running += $amt;
                                 } else {
                                    $dr_cell = '&#8377; ' . number_format($amt, 2);
                                    $cr_cell = '-';
                                    $running -= $amt;
                                 }
                           ?>
                           <tr>
                              <td style="text-align: center;"><?php echo $sr++; ?></td>
                              <td><?php echo htmlspecialchars($tx['date'] ?? ''); ?></td>
                              <td>
                                 <strong class="text-primary"><?php echo htmlspecialchars($tx['expcode'] ?? '-'); ?></strong>
                              </td>
                              <td style="text-align: center;">
                                 <?php if ($is_credit): ?>
                                    <span class="badge-credit"><i class="fa fa-arrow-down mr-2"></i> Credit</span>
                                 <?php else: ?>
                                    <span class="badge-debit"><i class="fa fa-arrow-up mr-2"></i> Debit</span>
                                 <?php endif; ?>
                              </td>
                              <td>
                                 <strong><?php echo htmlspecialchars($tx['exphead_name'] ?? 'General Expense'); ?></strong>
                                 <?php if(!empty($tx['regular_name']) && $tx['regular_name'] !== $tx['account_name']): ?>
                                    <small class="text-muted block">(<?php echo htmlspecialchars($tx['regular_name']); ?>)</small>
                                 <?php endif; ?>
                              </td>
                              <td>
                                 <span class="label label-info" style="font-size: 10px;"><?php echo htmlspecialchars($tx['paymode_name'] ?? 'Cash'); ?></span>
                              </td>
                              <td>
                                 <small><?php echo htmlspecialchars($tx['remarks'] ?? '-'); ?></small>
                              </td>
                              <td style="text-align: right;" class="<?php echo !$is_credit ? 'dr-text' : ''; ?>">
                                 <?php echo $dr_cell; ?>
                              </td>
                              <td style="text-align: right;" class="<?php echo $is_credit ? 'cr-text' : ''; ?>">
                                 <?php echo $cr_cell; ?>
                              </td>
                              <td style="text-align: right;" class="bal-text <?php echo $running >= 0 ? 'text-success' : 'text-danger'; ?>">
                                 &#8377; <?php echo number_format($running, 2); ?>
                              </td>
                           </tr>
                           <?php 
                              endforeach;
                           else:
                           ?>
                           <tr>
                              <td colspan="10" class="text-center text-muted" style="padding: 25px;">
                                 <i class="fa fa-info-circle fa-2x" style="display:block; margin-bottom:8px; opacity:0.4;"></i>
                                 No payment transactions found matching the selected filters.
                              </td>
                           </tr>
                           <?php endif; ?>
                        </tbody>
                        <tfoot>
                           <tr style="background-color: #f1f4f8; font-size: 14px; font-weight: 700;">
                              <td colspan="7" style="text-align: right;">Total Turnover & Final Balance:</td>
                              <td style="text-align: right;" class="dr-text">&#8377; <?php echo number_format($total_debit, 2); ?></td>
                              <td style="text-align: right;" class="cr-text">&#8377; <?php echo number_format($total_credit, 2); ?></td>
                              <td style="text-align: right;" class="bal-text <?php echo $net_balance >= 0 ? 'text-success' : 'text-danger'; ?>">
                                 &#8377; <?php echo number_format($net_balance, 2); ?>
                              </td>
                           </tr>
                        </tfoot>
                     </table>
                  </div>

               </div>
            </div>

            <!-- Optional: Associated Invoices & Bills Section -->
            <?php if (!empty($bills)): ?>
            <div class="panel panel-bd lobidisable no-print" style="margin-top: 20px;">
               <div class="panel-heading">
                  <div class="panel-title">
                     <i class="fa fa-file-text-o mr-5 text-success"></i>
                     <strong>Sales Bills & Invoices Associated with this Account</strong>
                     <span class="badge" style="background:#27ae60; margin-left: 8px;"><?php echo count($bills); ?> Invoices</span>
                  </div>
               </div>
               <div class="panel-body">
                  <div class="table-responsive">
                     <table class="table table-bordered table-striped table-hover">
                        <thead>
                           <tr class="info">
                              <th style="width: 40px; text-align: center;">#</th>
                              <th>Bill Number</th>
                              <th>Date</th>
                              <th>Payment Mode</th>
                              <th style="text-align: center;">Total Items</th>
                              <th style="text-align: right;">Total Amount</th>
                              <th style="text-align: center;">Status</th>
                           </tr>
                        </thead>
                        <tbody>
                           <?php 
                           $bi = 1;
                           $total_bill_amount = 0.00;
                           foreach ($bills as $b): 
                              $b_amt = (float)($b['bill_amount'] ?? 0);
                              $total_bill_amount += $b_amt;
                           ?>
                           <tr>
                              <td style="text-align: center;"><?php echo $bi++; ?></td>
                              <td><strong><?php echo htmlspecialchars($b['bill_number']); ?></strong></td>
                              <td><?php echo htmlspecialchars($b['purchage_date']); ?></td>
                              <td><span class="label label-primary"><?php echo htmlspecialchars($b['pay_mode'] ?: 'N/A'); ?></span></td>
                              <td style="text-align: center;"><?php echo (int)($b['total_qty'] ?? 0); ?></td>
                              <td style="text-align: right; font-weight: 600;">&#8377; <?php echo number_format($b_amt, 2); ?></td>
                              <td style="text-align: center;">
                                 <span class="label label-<?php echo ($b['status'] === 'success') ? 'success' : 'warning'; ?>">
                                    <?php echo htmlspecialchars(ucfirst($b['status'] ?: 'success')); ?>
                                 </span>
                              </td>
                           </tr>
                           <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                           <tr style="font-weight: 700; background: #fafafa;">
                              <td colspan="5" style="text-align: right;">Total Invoices Value:</td>
                              <td style="text-align: right;">&#8377; <?php echo number_format($total_bill_amount, 2); ?></td>
                              <td></td>
                           </tr>
                        </tfoot>
                     </table>
                  </div>
               </div>
            </div>
            <?php endif; ?>

         </section>
      </div>

      <!-- Footer -->
      <?php include('include/footer.php');?>
   </div>
   
   <?php include('include/js.php');?>
</body>
</html>
