<?php include_once("include/config.php"); ?>
<?php require_once(_BASEPATH . 'include/generatepdf.php'); ?>
<?php
   $validationHelper = new validation();
   
   $db = connect();
   $bill_no = $_GET['bill_no'];
   
   if ($_POST['action'] == 'delete' && $_POST['id'] != '') {
   
      $id = $_POST['id'];
   
      if ($id) {
   
         $res = $db->delete("DELETE FROM tbl_bill WHERE id=?", 'i', $id);
   
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
   
      redirectTo('tejaserp-sale-billwise-list.php', $msg, $code);
   } else if ($_POST['action'] == 'send-mail' && $_POST['id'] != '') {
   
      $bill_no = $_GET['bill_no'];
   
      $sendid = '';
      if ($bill_no) {
         $sendid = '?bill_no=' . $bill_no;
      }
      $bill_number = $_POST['id'];
      $filename = generatePDF($bill_number);
   
      $mail_to = $_POST['account_email'];
      if ($mail_to) {
         $mail_subject = "bill invoice";
         $mail_body = "<p>Please find you bill.</p>";
   
         send_mail($mail_to, $mail_subject, $mail_body, '', '', $filename);
         redirectTo('tejaserp-sale-billwise-list.php' . $sendid, 'Mail has been sent successfully');
      } else {
         redirectTo('tejaserp-sale-billwise-list.php' . $sendid, 'Email id not found.', 1);
      }
   }
   ?>
<?php include('include/header.php'); ?>
<!--<div class="pull-right" style=" margin-right: 30px;margin-bottom: 10px; margin-top: 6px;">-->
<!--   <a href="purchase-master-itemwise-list.php"><button data-toggle="tooltip" title="Sale ItemWise List !" class="button-btn-btn-btn">Next !</button></a>-->
<!--</div>-->
<!--<div class="pull-right" style="margin-bottom: 10px; margin-top: 6px;">-->
<!--   <a href="purchase-master-itemwise-list.php"><button data-toggle="tooltip" title="Sale ItemWise List !" class="button-btn-btn">Previous !</button></a>-->
<!--</div>-->
<section class="">
   <div class="row">
      <div class="col-sm-12">
         <div class="panel panel-bd lobidisable">
            <div class="panel-heading">
               <div class="btn-group" id="buttonexport">
                  <span style="font-size: 15px;">
                     <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24">
                        <defs>
                           <clipPath id="lineMdWatchTwotoneLoop0">
                              <rect width="24" height="12"/>
                           </clipPath>
                           <symbol id="lineMdWatchTwotoneLoop1">
                              <path fill="#808080" fill-opacity="0" stroke="#808080" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M23 16.5C23 10.4249 18.0751 5.5 12 5.5C5.92487 5.5 1 10.4249 1 16.5z" clip-path="url(#lineMdWatchTwotoneLoop0)">
                                 <animate attributeName="d" dur="6s" keyTimes="0;0.07;0.93;1" repeatCount="indefinite" values="M23 16.5C23 11.5 18.0751 12 12 12C5.92487 12 1 11.5 1 16.5z;M23 16.5C23 10.4249 18.0751 5.5 12 5.5C5.92487 5.5 1 10.4249 1 16.5z;M23 16.5C23 10.4249 18.0751 5.5 12 5.5C5.92487 5.5 1 10.4249 1 16.5z;M23 16.5C23 11.5 18.0751 12 12 12C5.92487 12 1 11.5 1 16.5z"/>
                                 <animate fill="freeze" attributeName="fill-opacity" begin="0.6s" dur="0.15s" values="0;0.3"/>
                              </path>
                           </symbol>
                           <mask id="lineMdWatchTwotoneLoop2">
                              <use href="#lineMdWatchTwotoneLoop1"/>
                              <use href="#lineMdWatchTwotoneLoop1" transform="rotate(180 12 12)"/>
                              <circle cx="12" cy="12" r="0" fill="#fff">
                                 <animate attributeName="r" dur="6s" keyTimes="0;0.03;0.97;1" repeatCount="indefinite" values="0;3;3;0"/>
                              </circle>
                           </mask>
                        </defs>
                        <rect width="24" height="24" fill="currentColor" mask="url(#lineMdWatchTwotoneLoop2)"/>
                     </svg>
                     <span>Recently Sale BillWise List !</span>
                  </span>
               </div>
            </div>
            <div class="panel-body">
               <div class="btn-group">
                  <style>
                     .ppbilllist{
                     border-radius: 50px;
                     border-color: #37475a;
                     margin-top: -15px;
                     }
                  </style>
                   <a href="tejaserp-sale-itemwise-list.php"><button class="btn btn-exp btn-sm dropdown-toggle ppbilllist" data-toggle=""style="background-color:#37475a; border-top-right-radius: 0px; border-bottom-right-radius: 0px;"><i class="fa fa-bars"></i><span style="margin-left:3px;">Sale ItemWise List !</span></button></a>
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
               <?php include('include/search.php'); ?>
               <div class="row" style="background-color: #F5F5F5; margin-left:0px; margin-right:0px;">
                  <form name="latestsearchForm" id="latestsearchForm" method="post" class="search-form" action="" autocomplete="off">
                     <input type="hidden" name="export" value="" />
                     <input type="hidden" name="page" value="1" />
                     <input type="hidden" name="sortOrder" value="DESC" />
                     <input type="hidden" name="sortField" value="id" />
                     <div class="">
                        <div class="col-md-2">
                           <small>Total Qty</small>
                           <p class="form-control bg-light total-qty"><?= countLatestRecords('tbl_bill', 'qty') ?></p>
                        </div>
                        <div class="col-md-2">
                           <small>Total Rate</small>
                           <p class="form-control bg-light total-rate"><?= getTotalLatestValue('tbl_bill', 'rate') ?></p>
                        </div>
                        <div class="col-md-2">
                           <small>Total Gst</small>
                           <p class="form-control bg-light total-gst"><?= getTotalLatestValue('tbl_bill', 'gst_amount') ?></p>
                        </div>
                        <div class="col-md-2">
                           <small>Total Bill Amt</small>
                           <p class="form-control bg-light total-amt"><?= getTotalLatestValue('tbl_bill', 'amount') ?></p>
                        </div>
                        <div class="col-md-4">
                           <small>Search</small>
                           <input type="text" class="form-control search-input" name="search" placeholder="Data Search">
                        </div>
                     </div>
                  </form>
               </div>
               <div class="table-responsive ">
                  <table id="dataTableExample1" class="table table-bordered table-striped table-hover">
                     <thead>
                        <tr class="info">
                           <th>SrNo</th>
                           <th>Date</th>
                           <th>Bill No</th>
                           <th>Pay Mode</th>
                           <th>Name</th>
                           <th>Qty</th>
                           <th>GST Type</th>
                           <th>GST Amt</th>
                           <th>Bill Amt</th>
                           <th>Status</th>
                           <th>Dtails</th>
                           <th>Action</th>
                        </tr>
                     </thead>
                     <tbody id="billwise-list-latest-results">
                     </tbody>
                  </table>
               </div>
               <div id="pagination-latest-result">
               </div>
            </div>
         </div>
      </div>
   </div>
</section>
<section class="">
   <div class="row">
      <div class="col-sm-12">
         <div class="panel panel-bd lobidisable">
            <div class="panel-heading sidebar-toggle" data-toggle="offcanvas">
               <div class="btn-group" id="buttonexport">
                  <a href="#">
                     <h5>Sale Billwise List</h5>
                  </a>
               </div>
            </div>
            <div class="panel-body">
               <!-- Plugin content:powerpoint,txt,pdf,png,word,xl -->
              <div class="btn-group">
                  <!--<style>-->
                  <!--   .ppbilllist{-->
                  <!--   border-radius: 50px;-->
                  <!--   border-color: #37475a;-->
                  <!--   margin-top: -15px;-->
                  <!--   }-->
                  <!--</style>-->
                   <a href="tejaserp-sale-itemwise-list.php"><button class="btn btn-exp btn-sm dropdown-toggle ppbilllist" data-toggle=""style="background-color:#37475a; border-top-right-radius: 0px; border-bottom-right-radius: 0px;"><i class="fa fa-bars"></i><span style="margin-left:3px;">Sale ItemWise List !</span></button></a>
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
               <?php include('include/search.php'); ?>
               <style>
                  .button {
                  background-color: antiquewhite;
                  border: none;
                  color: black;
                  padding: 5px 10px;
                  text-align: center;
                  text-decoration: none;
                  display: inline-block;
                  font-size: 11px;
                  cursor: pointer;
                  border-radius: 16px;
                  }
                  .button:hover {
                  background-color: #f1f1f1;
                  }
               </style>
               <div class="row"style="margin-left: 0px;margin-right: 0px; background-color: #F5F5F5 ;">
                  <form name="searchForm" id="searchForm" method="post" class="search-form" action="" autocomplete="off">
                     <input type="hidden" name="export" value="" />
                     <input type="hidden" name="page" value="1" />
                     <input type="hidden" name="sortOrder" value="DESC" />
                     <input type="hidden" name="sortField" value="id" />
                     <div class="">
                        <div class="col-md-2">
                           <small>Total Qty</small>
                           <p class="form-control bg-light total-qty"><?= countRecords('tbl_bill', 'qty') ?></p>
                        </div>
                        <div class="col-sm-2">
                           <small>Total Rate</small>
                           <p class="form-control bg-light total-rate"><?= getTotalValue('tbl_bill', 'rate') ?></p>
                        </div>
                        <div class="col-sm-2">
                           <small>Total Gst</small>
                           <p class="form-control bg-light total-gst"><?= getTotalValue('tbl_bill', 'gst_amount') ?></p>
                        </div>
                        <div class="col-sm-2">
                           <small>Total Bill Amt</small>
                           <p class="form-control bg-light total-amt"><?= getTotalValue('tbl_bill', 'amount') ?></p>
                        </div>
                        <div class="col-sm-2">
                           <small>From</small>
                           <input type="text" name="from_date" id="from_date" class="form-control from-date dtpicker" placeholder="Enter Date...">
                        </div>
                        <div class="col-sm-2">
                           <small>To</small>
                           <input type="text" name="to_date" id="to_date" class="form-control to-date dtpicker" placeholder="Enter Date...">
                        </div>
                     </div>
                     <div class="row" style="margin-left: 0px; margin-right: 0px; padding: 10px 0;">
                        <div class="col-sm-12">
                           <input type="text" class="form-control search-input" name="search" placeholder="Data Search">
                        </div>
                     </div>
                  </form>
               </div>
               <div class="table-responsive ">
                  <table id="dataTableExample2" class="table table-bordered table-striped table-hover">
                     <thead>
                        <tr class="info">
                           <th>SrNo</th>
                           <th>Date</th>
                           <th>Bill No</th>
                           <th>Pay Mode</th>
                           <th>Name</th>
                           <th>Qty</th>
                           <th>GST Type</th>
                           <th>GST Amt</th>
                           <th>Bill Amt</th>
                           <th>Status</th>
                           <th>Dtails</th>
                           <th>Action</th>
                        </tr>
                     </thead>
                     <tbody id="billwise-list-results">
                     </tbody>
                  </table>
               </div>
               <div id="pagination-result">
               </div>
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
</div>
<?php include('include/footer-2.php'); ?>
<script src="assets/dist/js/page/sale-billwise-list.js"></script>