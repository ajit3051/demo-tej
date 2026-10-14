<?php include_once("include/config.php"); ?>
<?php require_once(_BASEPATH . 'include/generatepdf.php'); ?>
<?php
   $validationHelper = new validation();
   
   
   $db = connect();
   $bill_no = $_GET['bill_no'];
   
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
   
      redirectTo('purchase-master-billwise-list.php', $msg, $code);
   }else if ($_POST['action'] == 'send-mail' && $_POST['id'] != '') {
   
      $bill_no = $_GET['bill_no'];
   
      $sendid = '';
      if ($bill_no) {
         $sendid = '?bill_no=' . $bill_no;
      }
      $bill_number = $_POST['id'];
      $filename = generatePDF($bill_number);
   
      $mail_to = $_POST['account_email'];
      if($mail_to){
      $mail_subject = "bill invoice";
      $mail_body = "<p>Please find you bill.</p>";
   
      send_mail($mail_to, $mail_subject, $mail_body, '', '', $filename);
      redirectTo('purchase-master-billwise-list.php' . $sendid, 'Mail has been sent successfully');
      } else {
         redirectTo('purchase-master-billwise-list.php' . $sendid, 'Email id not found.', 1);
      }
   }
   ?>
<?php include('include/header.php'); ?>
<?php include('include/fh-form-scrolling-data-list.php');?>
<section class="">
   <div class="row">
      <div class="col-sm-12">
         <div class="panel panel-bd  lobidisable">
            <div class="panel-heading sidebar-toggle" data-toggle="offcanvas">
               <div class="btn-group" id="buttonexport">
                  <a href="#">
                     <h5>Recently Stock Entry</h5>
                  </a>
               </div>
            </div>
            <div class="panel-body form-scroll">
               <!-- Plugin content:powerpoint,txt,pdf,png,word,xl -->
               <div class="btn-group"style="margin-left:-13px;">
                  <style>
                     .ppbilllist{
                     border-radius: 50px;
                     border-color: #37475a;
                     margin-top: -15px;
                     }
                  </style>
                  <a href="tejaserp-stockreport-indetails.php"><button class="btn btn-exp btn-sm dropdown-toggle ppbilllist" data-toggle=""style="background-color:#37475a; border-top-right-radius: 0px; border-bottom-right-radius: 0px; margin-top: -18px;"><i class="fa fa-bars"></i><span style="margin-left:3px;">Stock Report (IN Details) !</span></button></a>
                  <div class="btn-group">
                     <button class="btn btn-exp btn-sm dropdown-toggle ppbilllist" data-toggle="dropdown" style="background-color:#37475a; color: #fff;"><i class="fa fa-bars"></i> Export Table Data</button>
                     <ul class="dropdown-menu exp-drop" role="menu">
                        <li>
                           <a role="button" onclick="exportExcel(this, 'excel');">
                           <img src="assets/dist/img/xls.png" width="24" alt="logo"> Export To Excel !</a>
                        </li>
                        <li>
                           <a role="button" onclick="exportExcel(this, 'pdf');">
                           <img src="assets/dist/img/pdf.png" width="24" alt="logo"><span style="margin-left:5px;">Export To PDF !</span></a>
                        </li>
                     </ul>
                  </div>
               </div>
               <form name="latestsearchForm" id="latestsearchForm" method="post" class="search-form" action="" autocomplete="off">
               <div class="row"style="margin-left:-28px; margin-right:-23px; margin-top:-8px;">
                  
                     <input type="hidden" name="export" value="" />
                     <input type="hidden" name="page" value="1" />
                     <input type="hidden" name="sortOrder" value="DESC" />
                     <input type="hidden" name="sortField" value="id" />
                     
                        <div class="col-md-2">
                           <small>Total Qty</small>
                           <p class="form-control bg-light total-qty"><?= countRecords('tbl_purchase', 'qty') ?></p>
                        </div>
                        <div class="col-md-2">
                           <small>Total Amount</small>
                           <p class="form-control bg-light total-qty"><?= countRecords('tbl_purchase', 'qty') ?></p>
                        </div>
                       
                        
                       
               </div>
               
               <div class="row"style="margin-left:-28px; margin-right:-23px;">
                    <div class="col-md-12" style="margin-top:-6px; ">
                           <div class="search-container">
                              <!--<small>All in One Search</small>-->
                              <input type="text" class="form-control-control search-input" placeholder="Search.." name="search">
                           </div>
                        </div>
                        
               </div>
               
               <div class="table-responsive "style="margin-left:-23px; margin-right:-23px;">
                  <table id="dataTableExample1" class="table table-bordered table-striped table-hover">
                     <thead>
                        <tr class="info">
                           <th>SrNo</th>
                           <th>Stock Date</th>
                           <th>Sale Date</th>
                           <th>Item Code</th>
                           <th>Barcode No</th>
                           <th>Article No</th>
                           <th>Picture</th>
                           <th>Item Name</th>
                           <th>Category</th>
                           <th>Stock Qty</th>
                           <th>Purchase Qty</th>
                           <th>Audit Qty</th>
                           <th>Sale Qty</th>
                           <th>Balance Qty</th>
                           <th>Store Code</th>
                           <th>Store Name</th>
                           <th>Store Location</th>
                        </tr>
                     </thead>
                     <tbody class="report-list-results">
                     </tbody>
                  </table>
               </div>
               <div class="pagination-result">
               </div>
            </form>
            </div>
         </div>
      </div>
   </div>
</section>
<section class="">
   <div class="row">
      <div class="col-sm-12">
         <div class="panel panel-bd  lobidisable">
            <div class="panel-heading sidebar-toggle" data-toggle="offcanvas">
               <div class="btn-group" id="buttonexport">
                  <a href="#">
                     <h5>Stock Report (IN ShortCut)</h5>
                  </a>
               </div>
            </div>
            <div class="panel-body form-scroll">
               <!-- Plugin content:powerpoint,txt,pdf,png,word,xl -->
               <div class="btn-group"style="margin-left:-13px;">
                  <style>
                     .ppbilllist{
                     border-radius: 50px;
                     border-color: #37475a;
                     margin-top: -15px;
                     }
                  </style>
                  <a href="tejaserp-stockreport-indetails.php"><button class="btn btn-exp btn-sm dropdown-toggle ppbilllist" data-toggle=""style="background-color:#37475a; border-top-right-radius: 0px; border-bottom-right-radius: 0px; margin-top: -18px;"><i class="fa fa-bars"></i><span style="margin-left:3px;">Stock Report (IN Details) !</span></button></a>
                  <div class="btn-group">
                     <button class="btn btn-exp btn-sm dropdown-toggle ppbilllist" data-toggle="dropdown" style="background-color:#37475a; color: #fff;"><i class="fa fa-bars"></i> Export Table Data</button>
                     <ul class="dropdown-menu exp-drop" role="menu">
                        <li>
                           <a role="button" onclick="exportExcel(this, 'excel');">
                           <img src="assets/dist/img/xls.png" width="24" alt="logo"> Export To Excel !</a>
                        </li>
                        <li>
                           <a role="button" onclick="exportExcel(this, 'pdf');">
                           <img src="assets/dist/img/pdf.png" width="24" alt="logo"><span style="margin-left:5px;">Export To PDF !</span></a>
                        </li>
                     </ul>
                  </div>
               </div>
               <form name="searchForm" id="searchForm" method="post" class="search-form" action="" autocomplete="off">
               <div class="row"style="margin-left:-28px; margin-right:-23px; margin-top:-8px;">
                  
                     <input type="hidden" name="export" value="" />
                     <input type="hidden" name="page" value="1" />
                     <input type="hidden" name="sortOrder" value="DESC" />
                     <input type="hidden" name="sortField" value="id" />
                     
                        <div class="col-md-2">
                           <small>Total Stock Qty</small>
                           <p class="form-control bg-light total-qty"><?= countRecords('tbl_purchase', 'qty') ?></p>
                        </div>
                        <div class="col-md-2">
                           <small>Total Sale Qty</small>
                           <p class="form-control bg-light total-used-qty"><?= countRecords('tbl_purchase', 'qty') ?></p>
                        </div>
                        <div class="col-md-2">
                           <small>Total Balance Qty</small>
                           <p class="form-control bg-light total-bal-qty"><?= countRecords('tbl_purchase', 'qty') ?></p>
                        </div>
                        <div class="col-sm-3">
                           <small>Date</small>
                           <input type="text" name="from_date" id="from_date" class="form-control from-date dtpicker" placeholder="From">
                        </div>
                        <div class="col-sm-3">
                           <small>Date</small>
                           <input type="text" name="to_date" id="to_date" class="form-control to-date dtpicker" placeholder="To">
                        </div>
                        
               </div>
               
               <div class="row"style="margin-left:-28px; margin-right:-23px;">
                    <div class="col-md-12" style="margin-top:-6px; ">
                           <div class="search-container">
                              <!--<small>All in One Search</small>-->
                              <input type="text" class="form-control-control search-input" placeholder="Search.." name="search">
                           </div>
                        </div>
                        
               </div>
               
               <div class="table-responsive "style="margin-left:-23px; margin-right:-23px;">
                  <table id="dataTableExample1" class="table table-bordered table-striped table-hover">
                     <thead>
                        <tr class="info">
                           <th>SrNo</th>
                           <th>Stock Date</th>
                           <th>Sale Date</th>
                           <th>Item Code</th>
                           <th>Barcode No</th>
                           <th>Article No</th>
                           <th>Picture</th>
                           <th>Item Name</th>
                           <th>Category</th>
                           <th>Stock Qty</th>
                           <th>Purchase Qty</th>
                           <th>Audit Qty</th>
                           <th>Sale Qty</th>
                           <th>Balance Qty</th>
                           <th>Store Code</th>
                           <th>Store Name</th>
                           <th>Store Location</th>
                        </tr>
                     </thead>
                     <tbody class="report-list-results">
                     </tbody>
                  </table>
               </div>
               <div class="pagination-result">
               </div>
               </form>
            </div>
         </div>
      </div>
   </div>
</section>
<section class="">
   <div class="row">
      <!-- Single Bar Chart -->
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
      <!-- Bar Chart -->
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
      <!-- Line Chart -->
      <div class="col-md-4">
         <div class="panel panel-bd lobidrag">
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
<?php include('include/footer-2.php'); ?>
</div>
<script src="assets/dist/js/page/tejaserp-stockreport-inshortcut.js"></script>