<?php include_once("include/config.php"); ?>
<?php require_once(_BASEPATH . 'include/generatepdf.php'); ?>
<?php
   $validationHelper = new validation();
   
   $db = connect();
   
   $stores_query = "SELECT id, store_name, location_name FROM tbl_store_master WHERE status = 'true' ORDER BY store_name ASC";
   $stores_res = $db->query($stores_query);
   $stores_list = array();
   if ($stores_res) {
       while ($s = $stores_res->fetch_assoc()) {
           $stores_list[] = $s;
       }
   }
   
   $action = $_POST['action'] ?? '';
   $post_id = $_POST['id'] ?? '';
   
   if ($action == 'delete' && $post_id != '') {
   
      $id = $post_id;
   
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
   
      redirectTo('purchase-master-itemwise-list.php', $msg, $code);
   
   } else if ($action == 'send-mail' && $post_id != '') {
      
      $bill_no = $_GET['bill_no'] ?? '';
      $sendid = '';
      if ($bill_no) {
         $sendid = '?bill_no=' . $bill_no;
      }
      $bill_number = $post_id;
      $filename = generatePDF($bill_number);
   
      $mail_to = $_POST['account_email'] ?? '';
      if($mail_to){
         $mail_subject = "bill invoice";
         $mail_body = "<p>Please find you bill.</p>";
      
         send_mail($mail_to, $mail_subject, $mail_body, '', '', $filename);
         redirectTo('purchase-master-itemwise-list.php' . $sendid, 'Mail has been sent successfully');
      } else {
         redirectTo('purchase-master-itemwise-list.php' . $sendid, 'Email id not found.', 1);
      }
   }
?>
<?php include('include/header.php'); ?>

<section class="content">
   <div class="row">
      <div class="col-sm-12">
         <div class="panel panel-bd lobidisable">
            <div class="panel-heading sidebar-toggle" data-toggle="offcanvas">
               <div class="btn-group" id="buttonexport">
                  <a href="#">
                     <h5><span style="margin-left:10px; color:#37475a;">Recently Purchase ItemWise List</span></h5>
                  </a>
               </div>
            </div>
            <div class="panel-body form-scroll">
               <div class="btn-group" style="margin-bottom: 15px;">
                  <a href="purchase-master-billwise-list.php" class="btn btn-exp btn-sm" style="background-color:#37475a; color:#fff; border-radius: 50px 0 0 50px;"><i class="fa fa-file-text-o"></i><span style="margin-left:5px;">Purchase BillWise List</span></a>
                  <div class="btn-group">
                     <button class="btn btn-exp btn-sm dropdown-toggle" data-toggle="dropdown" style="background-color:#37475a; color:#fff; border-radius: 0 50px 50px 0;"><i class="fa fa-download"></i> Export Table Data</button>
                     <ul class="dropdown-menu exp-drop" role="menu">
                        <li>
                           <a role="button" onclick="exportExcel(this, 'excel');">
                           <img src="assets/dist/img/xls.png" width="24" alt="logo"> Export To Excel</a>
                        </li>
                        <li>
                           <a role="button" onclick="exportExcel(this, 'pdf');">
                           <img src="assets/dist/img/pdf.png" width="24" alt="logo"><span style="margin-left:5px;">Export To PDF</span></a>
                        </li>
                     </ul>
                  </div>
               </div>

               <?php if (file_exists('include/search.php')) { include('include/search.php'); } ?>

               <form name="latestsearchForm" id="latestsearchForm" method="post" class="search-form" action="" autocomplete="off">
                  <input type="hidden" name="page" value="1" />
                  <input type="hidden" name="export" value="" />
                  <input type="hidden" name="sortOrder" value="DESC" />
                  <input type="hidden" name="sortField" value="id" />

                  <div class="row" style="margin-bottom: 15px;">
                     <div class="col-md-3 col-sm-6">
                        <div class="form-group">
                           <label><small>Total Qty</small></label>
                           <p class="form-control bg-light total-qty" style="font-weight:600; margin-bottom:0;"><?= countLatestRecords('tbl_purchase', 'qty') ?? 0 ?></p>
                        </div>
                     </div>
                     <div class="col-md-3 col-sm-6">
                        <div class="form-group">
                           <label><small>Total GST</small></label>
                           <p class="form-control bg-light total-gst" style="font-weight:600; margin-bottom:0;"><?= getTotalLatestValue('tbl_purchase', 'gst_amount') ?? '0.00' ?></p>
                        </div>
                     </div>
                     <div class="col-md-3 col-sm-6">
                        <div class="form-group">
                           <label><small>Total Bill Amt</small></label>
                           <p class="form-control bg-light total-amt" style="font-weight:600; margin-bottom:0;"><?= getTotalLatestValue('tbl_purchase', 'amount') ?? '0.00' ?></p>
                        </div>
                     </div>
                     <div class="col-md-3 col-sm-6">
                        <div class="form-group">
                           <label><small>All in One Search</small></label>
                           <input type="text" class="form-control search-input" placeholder="Search..." name="search">
                        </div>
                     </div>
                  </div>
               </form>

               <div class="table-responsive">
                  <table id="dataTableLatest" class="table table-bordered table-striped table-hover">
                     <thead>
                        <tr class="info">
                           <th>SrNo</th>
                           <th>Details</th>
                           <th>Send</th>
                           <th>Action</th>
                           <th>Bill Date</th>
                           <th>Bill No</th>
                           <th>Invoice No</th>
                           <th>Invoice Date</th>
                           <th>Party Name</th>
                           <th>Barcode No</th>
                           <th>Item Name</th>
                           <th>Category</th>
                           <th>Brand</th>
                           <th>Size</th>
                           <th>Qty</th>
                           <th>MOU</th>
                           <th>Rate</th>
                           <th>Purchase Price</th>
                           <th>Dis %</th>
                           <th>Dis Amt</th>
                           <th>Net Amt</th>
                           <th>GST %</th>
                           <th>GST Amt</th>
                           <th>Taxable Amt</th>
                           <th>Amount</th>
                        </tr>
                     </thead>
                     <tbody id="billwise-list-latest-results">
                     </tbody>
                  </table>
               </div>
               
               <div class="row" style="margin-top: 15px;">
                  <div class="col-sm-8">
                     <div id="pagination-latest-result"></div>
                  </div>
                  <div class="col-sm-4 text-right">
                     <button class="btn btn-danger btn-sm" style="border-radius: 50px; padding: 6px 18px; font-weight: 600;" onclick="deleteRow('latestRecord')"><i class="fa fa-trash"></i><span style="margin-left:5px;">Delete Selected</span></button>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</section>

<section class="content">
   <div class="row">
      <div class="col-sm-12">
         <div class="panel panel-bd lobidisable">
            <div class="panel-heading sidebar-toggle" data-toggle="offcanvas">
               <div class="btn-group" id="buttonexport2">
                  <a href="#">
                     <h5><span style="margin-left:10px; color:#37475a;">Purchase ItemWise List</span></h5>
                  </a>
               </div>
            </div>
            <div class="panel-body form-scroll">
               <div class="btn-group" style="margin-bottom: 15px;">
                  <a href="purchase-master-billwise-list.php" class="btn btn-exp btn-sm" style="background-color:#37475a; color:#fff; border-radius: 50px 0 0 50px;"><i class="fa fa-file-text-o"></i><span style="margin-left:5px;">Purchase BillWise List</span></a>
                  <div class="btn-group">
                     <button class="btn btn-exp btn-sm dropdown-toggle" data-toggle="dropdown" style="background-color:#37475a; color:#fff; border-radius: 0 50px 50px 0;"><i class="fa fa-download"></i> Export Table Data</button>
                     <ul class="dropdown-menu exp-drop" role="menu">
                        <li>
                           <a role="button" onclick="exportExcel(this, 'excel');">
                           <img src="assets/dist/img/xls.png" width="24" alt="logo"> Export To Excel</a>
                        </li>
                        <li>
                           <a role="button" onclick="exportExcel(this, 'pdf');">
                           <img src="assets/dist/img/pdf.png" width="24" alt="logo"><span style="margin-left:5px;">Export To PDF</span></a>
                        </li>
                     </ul>
                  </div>
               </div>

               <form name="searchForm" id="searchForm" method="post" class="search-form" action="" autocomplete="off">
                  <input type="hidden" name="page" value="1" />
                  <input type="hidden" name="export" value="" />
                  <input type="hidden" name="sortOrder" value="DESC" />
                  <input type="hidden" name="sortField" value="id" />

                  <div class="row" style="margin-bottom: 15px;">
                     <div class="col-md-2 col-sm-4">
                        <div class="form-group">
                           <label><small>Total Qty</small></label>
                           <p class="form-control bg-light total-qty" style="font-weight:600; margin-bottom:0;"><?= countRecords('tbl_purchase', 'qty') ?? 0 ?></p>
                        </div>
                     </div>
                     <div class="col-md-2 col-sm-4">
                        <div class="form-group">
                           <label><small>Total GST</small></label>
                           <p class="form-control bg-light total-gst" style="font-weight:600; margin-bottom:0;"><?= getTotalValue('tbl_purchase', 'gst_amount') ?? '0.00' ?></p>
                        </div>
                     </div>
                     <div class="col-md-2 col-sm-4">
                        <div class="form-group">
                           <label><small>Total Bill Amt</small></label>
                           <p class="form-control bg-light total-amt" style="font-weight:600; margin-bottom:0;"><?= getTotalValue('tbl_purchase', 'amount') ?? '0.00' ?></p>
                        </div>
                     </div>
                     <div class="col-md-2 col-sm-4">
                        <div class="form-group">
                           <label><small>From Date</small></label>
                           <input type="text" name="from_date" id="from_date" class="form-control from-date dtpicker" placeholder="Enter Date...">
                        </div>
                     </div>
                     <div class="col-md-2 col-sm-4">
                        <div class="form-group">
                           <label><small>To Date</small></label>
                           <input type="text" name="to_date" id="to_date" class="form-control to-date dtpicker" placeholder="Enter Date...">
                        </div>
                     </div>
                     <div class="col-md-2 col-sm-4">
                        <div class="form-group">
                           <label><small>Search</small></label>
                           <input type="text" class="form-control search-input" placeholder="Search..." name="search">
                        </div>
                     </div>
                  </div>
               </form>

               <div class="table-responsive">
                  <table id="dataTableMain" class="table table-bordered table-striped table-hover">
                     <thead>
                        <tr class="info">
                           <th>SrNo</th>
                           <th>Details</th>
                           <th>Send</th>
                           <th>Action</th>
                           <th>Bill Date</th>
                           <th>Bill No</th>
                           <th>Invoice No</th>
                           <th>Invoice Date</th>
                           <th>Party Name</th>
                           <th>Barcode No</th>
                           <th>Item Name</th>
                           <th>Category</th>
                           <th>Brand</th>
                           <th>Size</th>
                           <th>Qty</th>
                           <th>MOU</th>
                           <th>Rate</th>
                           <th>Purchase Price</th>
                           <th>Dis %</th>
                           <th>Dis Amt</th>
                           <th>Net Amt</th>
                           <th>GST %</th>
                           <th>GST Amt</th>
                           <th>Taxable Amt</th>
                           <th>Amount</th>
                        </tr>
                     </thead>
                     <tbody id="billwise-list-results">
                     </tbody>
                  </table>
               </div>
               
               <div class="row" style="margin-top: 15px;">
                  <div class="col-sm-8">
                     <div id="pagination-result"></div>
                  </div>
                  <div class="col-sm-4 text-right">
                     <button class="btn btn-danger btn-sm" style="border-radius: 50px; padding: 6px 18px; font-weight: 600;" onclick="deleteRow('latestRecord')"><i class="fa fa-trash"></i><span style="margin-left:5px;">Delete Selected</span></button>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</section>

<section class="content">
   <div class="row">
      <div class="col-md-4">
         <div class="panel panel-bd lobidisable">
            <div class="panel-heading">
               <div class="panel-title">
                  <h5>Weekly Chart</h5>
               </div>
            </div>
            <div class="panel-body">
               <canvas id="singelBarChart" height="250"></canvas>
            </div>
         </div>
      </div>
      <div class="col-md-4">
         <div class="panel panel-bd lobidisable">
            <div class="panel-heading">
               <div class="panel-title">
                  <h5>Monthly Chart</h5>
               </div>
            </div>
            <div class="panel-body">
               <canvas id="barChart" height="250"></canvas>
            </div>
         </div>
      </div>
      <div class="col-md-4">
         <div class="panel panel-bd lobidisable">
            <div class="panel-heading">
               <div class="panel-title">
                  <h5>Yearly Chart</h5>
               </div>
            </div>
            <div class="panel-body">
               <canvas id="lineChart" height="250"></canvas>
            </div>
         </div>
      </div>
   </div>
</section>

<form action="" id="deleteform" method="post" autocomplete="off">
   <input type="hidden" name="action" value="delete">
   <input type="hidden" name="id" id="deleteid">
</form>
<!-- Modal for Stock Transfer -->
<div class="modal fade" id="stockTransferModal" tabindex="-1" role="dialog" aria-labelledby="stockTransferModalLabel" aria-hidden="true">
   <div class="modal-dialog modal-md" role="document">
      <div class="modal-content" style="border-radius: 8px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.3);">
         <div class="modal-header" style="background-color: #37475a; color: #fff; padding: 12px 20px;">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: #fff; opacity: 0.9;">
               <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title" id="stockTransferModalLabel" style="font-weight: 600; font-size: 16px;">
               <i class="fa fa-exchange" style="margin-right: 8px; color: #f0ad4e;"></i> Stock Transfer to Store
            </h4>
         </div>
         <form id="stockTransferForm" autocomplete="off">
            <div class="modal-body" style="padding: 20px;">
               <input type="hidden" name="purchase_id" id="st_purchase_id" value="">

               <div class="row">
                  <div class="col-sm-6" id="st_barcode_container">
                     <div class="form-group">
                        <label style="font-weight: 600; font-size: 13px;">Barcode Number <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="barcode_number" id="st_barcode_number" placeholder="Barcode Number..." readonly required>
                     </div>
                  </div>
                  <div class="col-sm-6" id="st_item_container">
                     <div class="form-group">
                        <label style="font-weight: 600; font-size: 13px;">Item Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="item_name" id="st_item_name" placeholder="Item Name..." readonly required>
                     </div>
                  </div>
               </div>

               <div class="row">
                  <div class="col-sm-6">
                     <div class="form-group">
                        <label style="font-weight: 600; font-size: 13px;">Stock Quantity <span class="text-danger">*</span></label>
                        <input type="number" min="1" class="form-control" name="stock_qty" id="st_stock_qty" placeholder="Enter Stock Qty..." required>
                        <small class="text-muted" id="st_avail_qty_hint" style="margin-top: 4px; display: block; font-weight: 500;"></small>
                     </div>
                  </div>
                  <div class="col-sm-6">
                     <div class="form-group">
                        <label style="font-weight: 600; font-size: 13px;">Select Store <span class="text-danger">*</span></label>
                        <select class="form-control" name="store_id" id="st_store_id" required style="font-weight: 500;">
                           <option value="">-- Choose Target Store --</option>
                           <?php foreach ($stores_list as $st) { ?>
                              <option value="<?= $st['id'] ?>"><?= htmlspecialchars($st['store_name']) ?> (<?= htmlspecialchars($st['location_name']) ?>)</option>
                           <?php } ?>
                        </select>
                     </div>
                  </div>
               </div>

               <div id="st_alert_msg" style="margin-top: 15px; display: none;"></div>
            </div>
            <div class="modal-footer" style="background-color: #f5f5f5; border-top: 1px solid #e5e5e5; padding: 12px 20px;">
               <button type="button" class="btn btn-default" data-dismiss="modal" style="border-radius: 4px;">Cancel</button>
               <button type="submit" class="btn btn-warning" id="st_btn_submit" style="border-radius: 4px; font-weight: 600; background-color: #f0ad4e; border-color: #eea236; color: #fff;">
                  <i class="fa fa-paper-plane" style="margin-right: 5px;"></i> Submit Transfer
               </button>
            </div>
         </form>
      </div>
   </div>
</div>

<script>
window.openStockTransferModal = function(id) {
    if (typeof $ === 'undefined') return;
    $('#st_purchase_id').val(id || '');
    $('#st_alert_msg').hide().removeClass('alert-danger alert-success').text('');
    $('#st_barcode_number').val('');
    $('#st_item_name').val('');
    $('#st_stock_qty').val(1);
    $('#st_store_id').val('');
    $('#st_avail_qty_hint').text('');

    if (id) {
        $.ajax({
            url: 'get_ajaxdata?function=get_purchase_item_details',
            type: 'POST',
            dataType: 'json',
            data: { id: id },
            success: function(res) {
                if (res && res.status && res.data) {
                    $('#st_barcode_number').val(res.data.barcode_number || '');
                    $('#st_item_name').val(res.data.item_name || '');
                    var availQty = parseInt(res.data.qty || 1, 10);
                    $('#st_stock_qty').val(availQty).attr('max', availQty);
                    if (res.data.qty) {
                        $('#st_avail_qty_hint').text('Available Purchase Qty: ' + availQty);
                    }
                }
            }
        });
    }

    $('#stockTransferModal').modal('show');
};
</script>

<?php include('include/footer-2.php'); ?>
</div>

<script src="assets/dist/js/page/purchase-master-itemwise-list.js"></script>