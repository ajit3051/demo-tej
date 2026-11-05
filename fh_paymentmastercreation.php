<?php include('top.php');?>
<?php
$msg = '';
$is_edit = false;
$edit_id = 0;
$edit_row = array(
   'date' => date('Y-m-d'),
   'expcode' => '',
   'party_code' => '',
   'account_name' => '',
   'regular_name' => '',
   'mobile_no' => '',
   'exphead_name' => '',
   'remarks' => '',
   'paymode_name' => '',
   'amount' => '',
   'payment_type' => 'Debit'
);

if (isset($_GET['update_id'])) {
   $is_edit = true;
   $edit_id = (int)$_GET['update_id'];
   $get_record = mysqli_query($conn, "SELECT * FROM tbl_expensescreation_master WHERE id = '$edit_id'");
   if ($row = mysqli_fetch_assoc($get_record)) {
      $edit_row = $row;
   }
}

// Helper to update opening_balance in tbl_account_master based on account code
function updateAccountBalance($conn, $account_code, $amount, $type = 'debit') {
   $account_code = mysqli_real_escape_string($conn, trim($account_code));
   if (empty($account_code)) {
      return false;
   }
   $amount = (float)$amount;
   if ($amount == 0) {
      return true;
   }

   $acc_res = mysqli_query($conn, "SELECT id, opening_balance FROM tbl_account_master WHERE account_code = '$account_code' LIMIT 1");
   if ($acc_row = mysqli_fetch_assoc($acc_res)) {
      $cur_bal = is_numeric($acc_row['opening_balance']) ? (float)$acc_row['opening_balance'] : 0.00;
      if (strtolower($type) === 'debit') {
         // In case of debit: minus from balance
         $new_bal = $cur_bal - $amount;
      } else {
         // In case of credit: add to balance
         $new_bal = $cur_bal + $amount;
      }
      $formatted_bal = number_format($new_bal, 2, '.', '');
      return mysqli_query($conn, "UPDATE tbl_account_master SET opening_balance = '$formatted_bal' WHERE id = '{$acc_row['id']}'");
   }
   return false;
}

// Delete Record (if triggered on this page)
if (isset($_GET['delete_id'])) {
   $del_id = (int)$_GET['delete_id'];
   $old_del_res = mysqli_query($conn, "SELECT party_code, amount, payment_type FROM tbl_expensescreation_master WHERE id = '$del_id'");
   if ($old_del_row = mysqli_fetch_assoc($old_del_res)) {
      $del_amt = (float)($old_del_row['amount'] ?? 0);
      $del_type = strtolower($old_del_row['payment_type'] ?? 'debit');
      $del_code = $old_del_row['party_code'] ?? '';
      // Revert previous impact: debit reverted with credit, credit reverted with debit
      if ($del_type === 'debit') {
         updateAccountBalance($conn, $del_code, $del_amt, 'credit');
      } else {
         updateAccountBalance($conn, $del_code, $del_amt, 'debit');
      }
   }
   mysqli_query($conn, "DELETE FROM tbl_expensescreation_master WHERE id = '$del_id'");
   header('location:fh_paymentmastercreation.php');
   exit;
}

// Update Record
if (isset($_POST['update'])) {
   $date = mysqli_real_escape_string($conn, $_POST['date'] ?? date('Y-m-d'));
   $expcode = mysqli_real_escape_string($conn, $_POST['expcode'] ?? '');
   $party_code = mysqli_real_escape_string($conn, $_POST['party_code'] ?? '');
   $account_name = mysqli_real_escape_string($conn, $_POST['account_name'] ?? '');
   $regular_name = mysqli_real_escape_string($conn, $_POST['regular_name'] ?? '');
   $mobile_no = mysqli_real_escape_string($conn, $_POST['mobile_no'] ?? '');
   $exphead_name = mysqli_real_escape_string($conn, $_POST['exphead_name'] ?? '');
   $remarks = mysqli_real_escape_string($conn, $_POST['remarks'] ?? '');
   $paymode_name = mysqli_real_escape_string($conn, $_POST['paymode_name'] ?? '');
   $amount = mysqli_real_escape_string($conn, $_POST['amount'] ?? '');
   $payment_type = mysqli_real_escape_string($conn, $_POST['payment_type'] ?? 'Debit');

   // Fetch old transaction before update to revert previous balance impact
   $old_res = mysqli_query($conn, "SELECT party_code, amount, payment_type FROM tbl_expensescreation_master WHERE id = '$edit_id'");
   $old_record = mysqli_fetch_assoc($old_res);

   $update_query = "UPDATE tbl_expensescreation_master SET date='$date', expcode='$expcode', party_code='$party_code', account_name='$account_name', regular_name='$regular_name', mobile_no='$mobile_no', exphead_name='$exphead_name', remarks='$remarks', paymode_name='$paymode_name', amount='$amount', payment_type='$payment_type' WHERE id='$edit_id'";
   if (mysqli_query($conn, $update_query)) {
      // 1. Revert previous balance impact on old account
      if ($old_record) {
         $old_amount = (float)($old_record['amount'] ?? 0);
         $old_type = strtolower($old_record['payment_type'] ?? 'debit');
         $old_code = $old_record['party_code'] ?? '';

         if ($old_type === 'debit') {
            // Revert debit by adding back
            updateAccountBalance($conn, $old_code, $old_amount, 'credit');
         } else {
            // Revert credit by subtracting back
            updateAccountBalance($conn, $old_code, $old_amount, 'debit');
         }
      }

      // 2. Apply new balance impact to account based on account code
      if (strtolower($payment_type) === 'debit') {
         // Debit: minus from balance
         updateAccountBalance($conn, $party_code, (float)$amount, 'debit');
      } else {
         // Credit: add to balance
         updateAccountBalance($conn, $party_code, (float)$amount, 'credit');
      }

      $msg = '<div class="alert alert-success alert-dismissible fade1 show">
         <button type="button" class="close" data-dismiss="alert">&times;</button>
         <strong>Payment Record Updated Successfully..!</strong>
      </div>';
      $get_record = mysqli_query($conn, "SELECT * FROM tbl_expensescreation_master WHERE id = '$edit_id'");
      if ($row = mysqli_fetch_assoc($get_record)) {
         $edit_row = $row;
      }
   } else {
      $msg = '<div class="alert alert-danger alert-dismissible fade1 show">
         <button type="button" class="close" data-dismiss="alert">&times;</button>
         <strong>Payment Record Update Failed!</strong>
      </div>';
   }
}

// Create Record
if (isset($_POST['submit'])) {
   $date = mysqli_real_escape_string($conn, $_POST['date'] ?? date('Y-m-d'));
   $expcode = mysqli_real_escape_string($conn, $_POST['expcode'] ?? '');
   $party_code = mysqli_real_escape_string($conn, $_POST['party_code'] ?? '');
   $account_name = mysqli_real_escape_string($conn, $_POST['account_name'] ?? '');
   $regular_name = mysqli_real_escape_string($conn, $_POST['regular_name'] ?? '');
   $mobile_no = mysqli_real_escape_string($conn, $_POST['mobile_no'] ?? '');
   $exphead_name = mysqli_real_escape_string($conn, $_POST['exphead_name'] ?? '');
   $remarks = mysqli_real_escape_string($conn, $_POST['remarks'] ?? '');
   $paymode_name = mysqli_real_escape_string($conn, $_POST['paymode_name'] ?? '');
   $amount = mysqli_real_escape_string($conn, $_POST['amount'] ?? '');
   $payment_type = mysqli_real_escape_string($conn, $_POST['payment_type'] ?? 'Debit');

   $dup = mysqli_query($conn, "SELECT id FROM tbl_expensescreation_master WHERE expcode='$expcode' AND expcode != ''");
   if (mysqli_num_rows($dup) > 0) {
      $msg = '<div class="alert alert-danger alert-dismissible fade1 show">
         <button type="button" class="close" data-dismiss="alert">&times;</button>
         <strong>Payment Code Already Exists!</strong>
      </div>';
   } else {
      $insert_query = "INSERT INTO tbl_expensescreation_master(date,expcode,party_code,account_name,regular_name,mobile_no,exphead_name,remarks,paymode_name,amount,payment_type) VALUES('$date','$expcode','$party_code','$account_name','$regular_name','$mobile_no','$exphead_name','$remarks','$paymode_name','$amount','$payment_type')";
      if (mysqli_query($conn, $insert_query)) {
         // Update balance in tbl_account_master based on account code
         if (strtolower($payment_type) === 'debit') {
            // Debit: minus from balance
            updateAccountBalance($conn, $party_code, (float)$amount, 'debit');
         } else {
            // Credit: add to balance
            updateAccountBalance($conn, $party_code, (float)$amount, 'credit');
         }

         $msg = '<div class="alert alert-success alert-dismissible fade1 show">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <strong>Payment Creation Success..!</strong>
         </div>';
      } else {
         $msg = '<div class="alert alert-danger alert-dismissible fade1 show">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <strong>Payment Creation Failed!</strong>
         </div>';
      }
   }
}

@$last_no = mysqli_query($conn, "SELECT id FROM tbl_expensescreation_master ORDER BY id DESC LIMIT 1");
$po_no = mysqli_fetch_assoc($last_no);
$next_debit_code = 'EC-000' . (($po_no['id'] ?? 0) + 1);
$next_credit_code = 'ECC-000' . (($po_no['id'] ?? 0) + 1);

// Pre-load dropdown data
$accounts = array();
$acc_res = mysqli_query($conn, "SELECT * FROM tbl_account_master ORDER BY account_name ASC");
while ($r = mysqli_fetch_assoc($acc_res)) {
   $accounts[] = $r;
}

$expheads = array();
$head_res = mysqli_query($conn, "SELECT * FROM tbl_expenseshead_master ORDER BY exphead_name ASC");
while ($r = mysqli_fetch_assoc($head_res)) {
   $expheads[] = $r;
}

$paymodes = array();
$pm_res = mysqli_query($conn, "SELECT * FROM tbl_paymode_master ORDER BY paymode_name ASC");
while ($r = mysqli_fetch_assoc($pm_res)) {
   $paymodes[] = $r;
}

$is_credit_active = (isset($edit_row['payment_type']) && strtolower($edit_row['payment_type']) === 'credit');
?>

<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <title>Payment Creation || TEJASERP</title>
      <?php include('include/css.php');?> 
   </head>
   <body class="hold-transition sidebar-mini">
      <div class="wrapper">
         <?php include('include/header-top-bar.php');?>

         <!-- Left side column -->
         <aside class="main-sidebar">
            <?php include('include/sidebar-left.php');?>
            <?php include('include/toggle_switch_list.php');?> 
            <?php include('include/fh-form-scrolling-data-list.php');?>
            <?php include('include/fh-popup-message-successfully.php');?>
         </aside>

         <!-- Content Wrapper -->
         <div class="content-wrapper">
            <div class="container-fluid">
               <div class="row">
                  <?php include('include/menu-header.php');?>
               </div>
            </div> 

            <!-- Form Section -->
            <section class="">
               <div class="row">
                  <div class="col-sm-12">
                     <div class="panel panel-bd lobidisable">
                        <div class="panel-heading">
                           <span class="fs-15 panel-header-text">
                              <i class="fa fa-pencil mr-5"></i>
                              <span><?php echo $is_edit ? 'Payment Master Edit !' : 'Payment Creations !'; ?></span>
                           </span>
                        </div>

                        <div class="panel-body form-scroll">
                           <div class="btn-group m-b-15">
                              <a href="fh_paymentmastercreation_list.php" class="btn btn-exp btn-sm">
                                 <i class="fa fa-list-alt mr-5"></i>Payment Master List !
                              </a>
                              <?php if ($is_edit): ?>
                                 <a href="fh_paymentmastercreation.php" class="btn btn-info btn-sm ml-5">
                                    <i class="fa fa-plus mr-5"></i>Add New Payment
                                 </a>
                              <?php endif; ?>
                           </div>

                           <div class="form-group">
                              <?php echo $msg; ?>
                           </div>

                           <!-- Nav tabs -->
                           <ul class="nav nav-tabs m-b-15">
                              <li class="<?php echo !$is_credit_active ? 'active' : ''; ?>">
                                 <a href="#tab1" data-toggle="tab"><i class="fa fa-minus-circle text-danger mr-5"></i>Debit || Payment</a>
                              </li>
                              <li class="<?php echo $is_credit_active ? 'active' : ''; ?>">
                                 <a href="#tab2" data-toggle="tab"><i class="fa fa-plus-circle text-success mr-5"></i>Credit || Payment</a>
                              </li>
                           </ul>

                           <!-- Tab panels -->
                           <div class="tab-content">
                              <!-- TAB 1: Debit Payment -->
                              <div class="tab-pane fade in <?php echo !$is_credit_active ? 'active' : ''; ?>" id="tab1">
                                 <form method="post">
                                    <input type="hidden" name="payment_type" value="Debit">

                                    <div class="row">
                                       <div class="col-sm-3">
                                          <div class="form-group">
                                             <label>Code</label><label class="text-danger">*</label>
                                             <input type="text" name="expcode" class="form-control" id="expcode_debit" readonly value="<?php echo htmlspecialchars($is_edit && !$is_credit_active ? $edit_row['expcode'] : $next_debit_code); ?>">
                                          </div>
                                       </div>

                                       <div class="col-sm-4">
                                          <div class="form-group">
                                             <label>Date</label><label class="text-danger">*</label>
                                             <input type="date" name="date" class="form-control" required value="<?php echo htmlspecialchars($is_edit && !$is_credit_active && !empty($edit_row['date']) ? $edit_row['date'] : date('Y-m-d')); ?>">
                                          </div>
                                       </div>
                                    </div>

                                    <div class="row">
                                       <div class="col-sm-3">
                                          <div class="form-group">
                                             <label>Party Code</label><label class="text-danger">*</label>
                                             <input type="text" class="form-control" name="party_code" id="party_code_debit" placeholder="Enter Party Code" required value="<?php echo htmlspecialchars(!$is_credit_active ? $edit_row['party_code'] : ''); ?>">
                                          </div>
                                       </div>
                                       <div class="col-sm-4">
                                          <div class="form-group">
                                             <label>Party Name</label><label class="text-danger">*</label>
                                             <select name="account_name" id="account_name_debit" class="form-control party-name-select" required>
                                                <option value="">Select Party Name</option>
                                                <?php foreach ($accounts as $acc): ?>
                                                   <option value="<?php echo htmlspecialchars($acc['account_name']); ?>"
                                                           data-code="<?php echo htmlspecialchars($acc['account_code'] ?? ''); ?>"
                                                           data-contact="<?php echo htmlspecialchars($acc['contact_name'] ?? ''); ?>"
                                                           data-mobile="<?php echo htmlspecialchars($acc['mobile_no_1'] ?? ''); ?>"
                                                           <?php echo (!$is_credit_active && $edit_row['account_name'] == $acc['account_name']) ? 'selected' : ''; ?>>
                                                      <?php echo htmlspecialchars($acc['account_name']); ?>
                                                   </option>
                                                <?php endforeach; ?>
                                             </select>
                                          </div>
                                       </div>
                                       <div class="col-sm-5">
                                          <div class="form-group">
                                             <label>Contact Name</label><label class="text-danger">*</label>
                                             <input type="text" class="form-control" name="regular_name" id="regular_name_debit" placeholder="Enter Contact / Regular Name" required value="<?php echo htmlspecialchars(!$is_credit_active ? $edit_row['regular_name'] : ''); ?>">
                                          </div>
                                       </div>
                                    </div>

                                    <div class="row">
                                       <div class="col-sm-3">
                                          <div class="form-group">
                                             <label>Mobile No.</label><label class="text-danger">*</label>
                                             <input type="text" class="form-control" name="mobile_no" id="mobile_no_debit" placeholder="Enter Mobile No" maxlength="10" required value="<?php echo htmlspecialchars(!$is_credit_active ? $edit_row['mobile_no'] : ''); ?>">
                                          </div>
                                       </div>
                                       <div class="col-sm-4">
                                          <div class="form-group">
                                             <label>Head Name</label><label class="text-danger">*</label>
                                             <select name="exphead_name" class="form-control" required>
                                                <option value="">Select Expenses Head Name</option>
                                                <?php foreach ($expheads as $h): ?>
                                                   <option value="<?php echo htmlspecialchars($h['exphead_name']); ?>" <?php echo (!$is_credit_active && $edit_row['exphead_name'] == $h['exphead_name']) ? 'selected' : ''; ?>>
                                                      <?php echo htmlspecialchars($h['exphead_name']); ?>
                                                   </option>
                                                <?php endforeach; ?>
                                             </select>
                                          </div>
                                       </div>
                                       <div class="col-sm-5">
                                          <div class="form-group">
                                             <label>Remarks</label>
                                             <input type="text" class="form-control" name="remarks" placeholder="Enter Remarks" value="<?php echo htmlspecialchars(!$is_credit_active ? $edit_row['remarks'] : ''); ?>">
                                          </div>
                                       </div>
                                    </div>

                                    <div class="row">
                                       <div class="col-sm-3">
                                          <div class="form-group">
                                             <label>Payment Mode</label><label class="text-danger">*</label>
                                             <select name="paymode_name" class="form-control" required>
                                                <option value="">Select Payment Mode</option>
                                                <?php foreach ($paymodes as $pm): ?>
                                                   <option value="<?php echo htmlspecialchars($pm['paymode_name']); ?>" <?php echo (!$is_credit_active && $edit_row['paymode_name'] == $pm['paymode_name']) ? 'selected' : ''; ?>>
                                                      <?php echo htmlspecialchars($pm['paymode_name']); ?>
                                                   </option>
                                                <?php endforeach; ?>
                                             </select>
                                          </div>
                                       </div>
                                       <div class="col-sm-4">
                                          <div class="form-group">
                                             <label>Amount</label><label class="text-danger">*</label>
                                             <input type="text" class="form-control" name="amount" placeholder="Enter Amount" required value="<?php echo htmlspecialchars(!$is_credit_active ? $edit_row['amount'] : ''); ?>">
                                          </div>
                                       </div>
                                    </div>

                                    <h4 class="m-t-20 m-b-10 text-primary"><i class="fa fa-info-circle mr-5"></i>Payment Details :</h4>
                                    <div class="row">
                                       <div class="col-sm-3">
                                          <div class="form-group">
                                             <label>Transfer Amt. Date</label>
                                             <input type="date" name="transfer_date" class="form-control" value="<?php echo date('Y-m-d'); ?>">
                                          </div>
                                       </div>
                                       <div class="col-sm-4">
                                          <div class="form-group">
                                             <label>Bank Name From</label>
                                             <select name="bank_from" class="form-control">
                                                <option value="">Select Bank From</option>
                                                <?php foreach ($paymodes as $pm): ?>
                                                   <option value="<?php echo htmlspecialchars($pm['paymode_name']); ?>"><?php echo htmlspecialchars($pm['paymode_name']); ?></option>
                                                <?php endforeach; ?>
                                             </select>
                                          </div>
                                       </div>
                                       <div class="col-sm-5">
                                          <div class="form-group">
                                             <label>Account No. From</label>
                                             <input type="text" class="form-control" name="account_from" placeholder="Enter Account No. From">
                                          </div>
                                       </div>
                                    </div>

                                    <div class="row">
                                       <div class="col-sm-3">
                                          <div class="form-group">
                                             <label>Bank Name To</label>
                                             <select name="bank_to" class="form-control">
                                                <option value="">Select Bank To</option>
                                                <?php foreach ($paymodes as $pm): ?>
                                                   <option value="<?php echo htmlspecialchars($pm['paymode_name']); ?>"><?php echo htmlspecialchars($pm['paymode_name']); ?></option>
                                                <?php endforeach; ?>
                                             </select>
                                          </div>
                                       </div>
                                       <div class="col-sm-4">
                                          <div class="form-group">
                                             <label>Account No. To</label>
                                             <input type="text" class="form-control" name="account_to" placeholder="Enter Account No. To">
                                          </div>
                                       </div>
                                    </div>

                                    <div class="row">
                                       <div class="col-sm-3">
                                          <div class="form-group">
                                             <label>Cheque Issue Date</label>
                                             <input type="date" name="cheque_issue_date" class="form-control">
                                          </div>
                                       </div>
                                       <div class="col-sm-4">
                                          <div class="form-group">
                                             <label>Cheque Clearance Date</label>
                                             <input type="date" name="cheque_clear_date" class="form-control">
                                          </div>
                                       </div>
                                       <div class="col-sm-5">
                                          <div class="form-group">
                                             <label>Cheque No.</label>
                                             <input type="text" class="form-control" name="cheque_no" placeholder="Enter Cheque No.">
                                          </div>
                                       </div>
                                    </div>

                                    <div class="row m-t-15 m-b-15">
                                       <div class="col-sm-12">
                                          <?php if ($is_edit && !$is_credit_active): ?>
                                             <a href="fh_paymentmastercreation.php" class="btn btn-default btn-w-100">Cancel</a>
                                             <button type="submit" name="update" class="btn btn-teal-100">Update</button>
                                          <?php else: ?>
                                             <button type="reset" class="btn btn-warning btn-w-100">Reset</button>
                                             <button type="submit" name="submit" class="btn btn-teal-100">Save</button>
                                          <?php endif; ?>
                                       </div>
                                    </div>
                                 </form>
                              </div>

                              <!-- TAB 2: Credit Payment -->
                              <div class="tab-pane fade in <?php echo $is_credit_active ? 'active' : ''; ?>" id="tab2">
                                 <form method="post">
                                    <input type="hidden" name="payment_type" value="Credit">

                                    <div class="row">
                                       <div class="col-sm-3">
                                          <div class="form-group">
                                             <label>Code</label><label class="text-danger">*</label>
                                             <input type="text" name="expcode" class="form-control" id="expcode_credit" readonly value="<?php echo htmlspecialchars($is_edit && $is_credit_active ? $edit_row['expcode'] : $next_credit_code); ?>">
                                          </div>
                                       </div>

                                       <div class="col-sm-4">
                                          <div class="form-group">
                                             <label>Date</label><label class="text-danger">*</label>
                                             <input type="date" name="date" class="form-control" required value="<?php echo htmlspecialchars($is_edit && $is_credit_active && !empty($edit_row['date']) ? $edit_row['date'] : date('Y-m-d')); ?>">
                                          </div>
                                       </div>
                                    </div>

                                    <div class="row">
                                       <div class="col-sm-3">
                                          <div class="form-group">
                                             <label>Party Code</label><label class="text-danger">*</label>
                                             <input type="text" class="form-control" name="party_code" id="party_code_credit" placeholder="Enter Party Code" required value="<?php echo htmlspecialchars($is_credit_active ? $edit_row['party_code'] : ''); ?>">
                                          </div>
                                       </div>
                                       <div class="col-sm-4">
                                          <div class="form-group">
                                             <label>Party Name</label><label class="text-danger">*</label>
                                             <select name="account_name" id="account_name_credit" class="form-control party-name-select" required>
                                                <option value="">Select Party Name</option>
                                                <?php foreach ($accounts as $acc): ?>
                                                   <option value="<?php echo htmlspecialchars($acc['account_name']); ?>"
                                                           data-code="<?php echo htmlspecialchars($acc['account_code'] ?? ''); ?>"
                                                           data-contact="<?php echo htmlspecialchars($acc['contact_name'] ?? ''); ?>"
                                                           data-mobile="<?php echo htmlspecialchars($acc['mobile_no_1'] ?? ''); ?>"
                                                           <?php echo ($is_credit_active && $edit_row['account_name'] == $acc['account_name']) ? 'selected' : ''; ?>>
                                                      <?php echo htmlspecialchars($acc['account_name']); ?>
                                                   </option>
                                                <?php endforeach; ?>
                                             </select>
                                          </div>
                                       </div>
                                       <div class="col-sm-5">
                                          <div class="form-group">
                                             <label>Contact Name</label><label class="text-danger">*</label>
                                             <input type="text" class="form-control" name="regular_name" id="regular_name_credit" placeholder="Enter Contact / Regular Name" required value="<?php echo htmlspecialchars($is_credit_active ? $edit_row['regular_name'] : ''); ?>">
                                          </div>
                                       </div>
                                    </div>

                                    <div class="row">
                                       <div class="col-sm-3">
                                          <div class="form-group">
                                             <label>Mobile No.</label><label class="text-danger">*</label>
                                             <input type="text" class="form-control" name="mobile_no" id="mobile_no_credit" placeholder="Enter Mobile No" maxlength="10" required value="<?php echo htmlspecialchars($is_credit_active ? $edit_row['mobile_no'] : ''); ?>">
                                          </div>
                                       </div>
                                       <div class="col-sm-4">
                                          <div class="form-group">
                                             <label>Head Name</label><label class="text-danger">*</label>
                                             <select name="exphead_name" class="form-control" required>
                                                <option value="">Select Expenses Head Name</option>
                                                <?php foreach ($expheads as $h): ?>
                                                   <option value="<?php echo htmlspecialchars($h['exphead_name']); ?>" <?php echo ($is_credit_active && $edit_row['exphead_name'] == $h['exphead_name']) ? 'selected' : ''; ?>>
                                                      <?php echo htmlspecialchars($h['exphead_name']); ?>
                                                   </option>
                                                <?php endforeach; ?>
                                             </select>
                                          </div>
                                       </div>
                                       <div class="col-sm-5">
                                          <div class="form-group">
                                             <label>Remarks</label>
                                             <input type="text" class="form-control" name="remarks" placeholder="Enter Remarks" value="<?php echo htmlspecialchars($is_credit_active ? $edit_row['remarks'] : ''); ?>">
                                          </div>
                                       </div>
                                    </div>

                                    <div class="row">
                                       <div class="col-sm-3">
                                          <div class="form-group">
                                             <label>Payment Mode</label><label class="text-danger">*</label>
                                             <select name="paymode_name" class="form-control" required>
                                                <option value="">Select Payment Mode</option>
                                                <?php foreach ($paymodes as $pm): ?>
                                                   <option value="<?php echo htmlspecialchars($pm['paymode_name']); ?>" <?php echo ($is_credit_active && $edit_row['paymode_name'] == $pm['paymode_name']) ? 'selected' : ''; ?>>
                                                      <?php echo htmlspecialchars($pm['paymode_name']); ?>
                                                   </option>
                                                <?php endforeach; ?>
                                             </select>
                                          </div>
                                       </div>
                                       <div class="col-sm-4">
                                          <div class="form-group">
                                             <label>Amount</label><label class="text-danger">*</label>
                                             <input type="text" class="form-control" name="amount" placeholder="Enter Amount" required value="<?php echo htmlspecialchars($is_credit_active ? $edit_row['amount'] : ''); ?>">
                                          </div>
                                       </div>
                                    </div>

                                    <h4 class="m-t-20 m-b-10 text-primary"><i class="fa fa-info-circle mr-5"></i>Payment Details :</h4>
                                    <div class="row">
                                       <div class="col-sm-3">
                                          <div class="form-group">
                                             <label>Transfer Amt. Date</label>
                                             <input type="date" name="transfer_date" class="form-control" value="<?php echo date('Y-m-d'); ?>">
                                          </div>
                                       </div>
                                       <div class="col-sm-4">
                                          <div class="form-group">
                                             <label>Bank Name To</label>
                                             <select name="bank_to" class="form-control">
                                                <option value="">Select Bank From</option>
                                                <?php foreach ($paymodes as $pm): ?>
                                                   <option value="<?php echo htmlspecialchars($pm['paymode_name']); ?>"><?php echo htmlspecialchars($pm['paymode_name']); ?></option>
                                                <?php endforeach; ?>
                                             </select>
                                          </div>
                                       </div>
                                       <div class="col-sm-5">
                                          <div class="form-group">
                                             <label>Account No. To</label>
                                             <input type="text" class="form-control" name="account_to" placeholder="Enter Account No. To">
                                          </div>
                                       </div>
                                    </div>

                                    <div class="row">
                                       <div class="col-sm-3">
                                          <div class="form-group">
                                             <label>Cheque Issue Date</label>
                                             <input type="date" name="cheque_issue_date" class="form-control">
                                          </div>
                                       </div>
                                       <div class="col-sm-4">
                                          <div class="form-group">
                                             <label>Cheque Clearance Date</label>
                                             <input type="date" name="cheque_clear_date" class="form-control">
                                          </div>
                                       </div>
                                       <div class="col-sm-5">
                                          <div class="form-group">
                                             <label>Cheque No.</label>
                                             <input type="text" class="form-control" name="cheque_no" placeholder="Enter Cheque No.">
                                          </div>
                                       </div>
                                    </div>

                                    <div class="row m-t-15 m-b-15">
                                       <div class="col-sm-12">
                                          <?php if ($is_edit && $is_credit_active): ?>
                                             <a href="fh_paymentmastercreation.php" class="btn btn-default btn-w-100">Cancel</a>
                                             <button type="submit" name="update" class="btn btn-teal-100">Update</button>
                                          <?php else: ?>
                                             <button type="reset" class="btn btn-warning btn-w-100">Reset</button>
                                             <button type="submit" name="submit" class="btn btn-teal-100">Save</button>
                                          <?php endif; ?>
                                       </div>
                                    </div>
                                 </form>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </section>

            <!-- List Section -->
            <section class="">
               <div class="row">
                  <div class="col-sm-12">
                     <div class="panel panel-bd lobidisable">
                        <div class="panel-heading">
                           <span class="fs-15 panel-header-text">
                              <i class="fa fa-th-list mr-5"></i>
                              <span>Payment Creation List !</span>
                           </span>
                        </div>
                        <div class="panel-body form-scroll">
                           <!-- Toolbar & Search Box Row under Header Panel -->
                           <div class="row m-b-15">
                              <div class="col-sm-6 col-xs-12 m-b-5">
                                 <div class="btn-group">
                                    <a href="fh_paymentmastercreation.php" class="btn btn-exp btn-sm"><i class="fa fa-plus mr-5"></i>Add New Payment Creation</a>
                                 </div>
                                 <?php include('include/button-export-to-data.php');?>
                              </div>
                              <div class="col-sm-6 col-xs-12">
                                 <form id="paymentMasterSearchForm" class="search-form-wrap pull-right" onsubmit="return false;" style="width: 100%; max-width: 360px;">
                                    <input type="hidden" name="page" id="page" value="1">
                                    <div class="input-group search-box-group">
                                       <input type="text" class="form-control search-input search-input-field" name="search" id="payment_search" placeholder="Search Payments..." autocomplete="off">
                                       <span class="input-group-addon search-addon-btn" id="paymentMasterSearchBtn" style="cursor: pointer;" title="Click to search">
                                          <i class="fa fa-search search-icon-sm"></i>
                                       </span>
                                    </div>
                                 </form>
                                 <div class="clearfix"></div>
                              </div>
                           </div>
                           <div class="table-responsive">
                              <table id="dataTableExample1" class="table table-bordered table-striped table-hover">
                                 <thead>
                                    <tr class="info">
                                       <th class="th-width-40-center">#</th>
                                       <th class="th-minwidth-100-center">Action</th>
                                       <th class="text-center-align">Status</th>
                                       <th>ExpCode No</th>
                                       <th>Date</th>
                                       <th>Party Code</th>
                                       <th>Party Name</th>
                                       <th>Regular Name</th>
                                       <th>Mobile No</th>
                                       <th>Head Name</th>
                                       <th>Remarks</th>
                                       <th>Payment Mode</th>
                                       <th>Amount</th>
                                    </tr>
                                 </thead>
                                 <tbody id="paymentmaster-list-results">
                                    <?php
                                       $init_page = 1;
                                       $init_perpage = isset($_SESSION["_RECORDPERPAGE_"]) && $_SESSION["_RECORDPERPAGE_"] ? (int)$_SESSION["_RECORDPERPAGE_"] : 20;
                                       $init_offset = ($init_page - 1) * $init_perpage;
                                       $query = mysqli_query($conn, "SELECT * FROM tbl_expensescreation_master ORDER BY id DESC LIMIT $init_offset, $init_perpage"); 
                                       $countQuery = mysqli_query($conn, "SELECT COUNT(*) as total FROM tbl_expensescreation_master");
                                       $countRow = mysqli_fetch_assoc($countQuery);
                                       $totalRecords = $countRow['total'] ?? 0;
                                       $expenses_list = array();
                                       while ($e = mysqli_fetch_array($query)) {
                                          $expenses_list[] = $e;
                                       }
                                       $i = $init_offset + 1;
                                       foreach ($expenses_list as $user) {
                                      ?>
                                    <tr>
                                       <td class="td-center-middle"><?php echo $i++; ?></td>
                                       <td class="td-center-middle-nowrap">
                                          <a class="btn btn-info btn-xs" data-toggle="tooltip" title="Edit Payment" href="fh_paymentmastercreation.php?update_id=<?php echo $user['id']; ?>">
                                             <i class="fa fa-pencil"></i> Edit
                                          </a>
                                          <a class="btn btn-danger btn-xs" data-toggle="tooltip" title="Delete Payment" href="fh_paymentmastercreation_list.php?delete_id=<?php echo $user['id']; ?>" onclick="return confirm('Are you sure you want to delete this record?');">
                                             <i class="fa fa-trash-o"></i> Delete
                                          </a>
                                       </td>
                                       <td class="td-center-middle"><span class="label label-success badge-status-sm">Active</span></td>
                                       <td><?php echo htmlspecialchars($user['expcode'] ?? ''); ?></td>
                                       <td><?php echo htmlspecialchars($user['date'] ?? ''); ?></td>
                                       <td><?php echo htmlspecialchars($user['party_code'] ?? ''); ?></td>
                                       <td><?php echo htmlspecialchars($user['account_name'] ?? ''); ?></td>
                                       <td><?php echo htmlspecialchars($user['regular_name'] ?? ''); ?></td>
                                       <td><?php echo htmlspecialchars($user['mobile_no'] ?? ''); ?></td>
                                       <td><?php echo htmlspecialchars($user['exphead_name'] ?? ''); ?></td>
                                       <td><?php echo htmlspecialchars($user['remarks'] ?? ''); ?></td>
                                       <td><?php echo htmlspecialchars($user['paymode_name'] ?? ''); ?></td>
                                       <td><?php echo htmlspecialchars($user['amount'] ?? ''); ?></td>
                                    </tr>
                                    <?php } ?>
                                 </tbody>
                              </table>
                           </div>
                           <div class="row pagination-wrapper-row">
                              <div class="col-sm-6">
                                 <div class="pagination-info-text">
                                    Total Records: <strong id="total-records-count"><?php echo $totalRecords; ?></strong>
                                 </div>
                              </div>
                              <div class="col-sm-6">
                                 <div id="paymentmaster-pagination">
                                    <?php
                                       $totalPages = ceil($totalRecords / $init_perpage);
                                       if ($totalPages > 1) {
                                          echo '<ul class="pagination pagination-sm pagination-no-margin pull-right">';
                                          echo '<li class="disabled"><span>&laquo; Prev</span></li>';
                                          for ($p = 1; $p <= min(5, $totalPages); $p++) {
                                             if ($p == 1) {
                                                echo '<li class="active"><span>' . $p . '</span></li>';
                                             } else {
                                                echo '<li><a href="javascript:void(0)" onclick="loadPaymentMasterData(' . $p . ')">' . $p . '</a></li>';
                                             }
                                          }
                                          if ($totalPages > 5) {
                                             echo '<li class="disabled"><span>...</span></li>';
                                             echo '<li><a href="javascript:void(0)" onclick="loadPaymentMasterData(' . $totalPages . ')">' . $totalPages . '</a></li>';
                                          }
                                          echo '<li><a href="javascript:void(0)" onclick="loadPaymentMasterData(2)">Next &raquo;</a></li>';
                                          echo '</ul>';
                                       }
                                    ?>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </section>
         </div>
         <!-- /.content-wrapper -->
         <?php include('include/footer.php');?> 
         <?php include('include/Sidenavbuttons.php');?>
      </div>
      <!-- ./wrapper -->
      <?php include('include/js.php');?>
      <script src="assets/dist/js/page/paymentmaster-list.js?v=<?= time(); ?>"></script>
   </body>
</html>
