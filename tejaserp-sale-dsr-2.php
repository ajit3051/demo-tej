<?php include_once("include/config.php"); ?>
<?php require_once(_BASEPATH . 'include/generatepdf.php'); ?>
<?php $exportType = "sale"; ?>
<?php
   $validationHelper = new validation();
   
   $db = connect();
   
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
   
      redirectTo('sale-itemwise-list.php', $msg, $code);
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
         redirectTo('sale-itemwise-list.php' . $sendid, 'Mail has been sent successfully');
      } else {
         redirectTo('sale-itemwise-list.php' . $sendid, 'Email id not found.', 1);
      }
   }

   function getPaymentSum($recent = false) {
       $db = connect();
       $dateCondition = "1 = 1";
       if ($recent) {
           $currentDate = date('Y-m-d');
           $dateCondition = "DATE(created_date) = '{$currentDate}'";
       }

       $sql = "SELECT 
           'CASH' AS payment_mode,
           COALESCE(SUM(CASE WHEN pay_mode = 'CASH' THEN qty ELSE 0 END), 0) AS total_qty,
           COALESCE(SUM(CASE WHEN pay_mode = 'CASH' THEN pay_cash ELSE 0 END), 0) AS total_amount
       FROM tbl_bill 
       WHERE {$dateCondition}

       UNION ALL

       SELECT 
           'UPI' AS payment_mode,
           COALESCE(SUM(CASE WHEN pay_mode = 'UPI' THEN qty ELSE 0 END), 0) AS total_qty,
           COALESCE(SUM(CASE WHEN pay_mode = 'UPI' THEN pay_upi ELSE 0 END), 0) AS total_amount
       FROM tbl_bill 
       WHERE {$dateCondition}

       UNION ALL

       SELECT 
           'CARD' AS payment_mode,
           COALESCE(SUM(CASE WHEN pay_mode = 'CARD' THEN qty ELSE 0 END), 0) AS total_qty,
           COALESCE(SUM(CASE WHEN pay_mode = 'CARD' THEN pay_card ELSE 0 END), 0) AS total_amount
       FROM tbl_bill 
       WHERE {$dateCondition}

       UNION ALL

       SELECT 
           'CREDIT' AS payment_mode,
           COALESCE(SUM(CASE WHEN pay_mode = 'CREDIT' THEN qty ELSE 0 END), 0) AS total_qty,
           COALESCE(SUM(CASE WHEN pay_mode = 'CREDIT' THEN pay_credit ELSE 0 END), 0) AS total_amount
       FROM tbl_bill
       WHERE {$dateCondition}";

       $query = $db->select($sql);

       return $query;
   }
   ?>
<?php include('include/header.php'); ?>
<!--<div class="pull-right" style=" margin-right: 30px;margin-bottom: 10px; margin-top: 6px;">-->
<!--   <a href="purchase-master-billwise-list.php"><button data-toggle="tooltip" title="Sale BillWise List !" class="button-btn-btn-btn">Next !</button></a>-->
<!--</div>-->
<!--<div class="pull-right" style="margin-bottom: 10px; margin-top: 6px;">-->
<!--   <a href="purchase-master-billwise-list.php"><button data-toggle="tooltip" title="Sale BillWise List !" class="button-btn-btn">Previous !</button></a>-->
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
                     <span>Recently DSR (Report) !</span>
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
                  <a href="tejaserp-sale-billwise-list.php"><button class="btn btn-exp btn-sm ppbilllist" data-toggle=""style="background-color:#37475a; margin-top: -18px;"><i class="fa fa-bars"></i><span style="margin-left:3px;">Sale BillWise List !</span></button></a>
                  <a href="tejaserp-sale-itemwise-list.php"><button class="btn btn-exp btn-sm ppbilllist" data-toggle=""style="background-color:#37475a; border-top-right-radius: 0px; border-bottom-right-radius: 0px; margin-top: -18px; margin-left: -5px;"><i class="fa fa-bars"></i><span style="margin-left:3px;">Sale ItemWise List !</span></button></a>
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
               
                <div class="row">
                     <?php 
                     $i = 1;
                     foreach(getPaymentSum(true) as $row){
                     ?>
                      <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
                         <div id="billwise-sale-cardbox<?= $i++; ?>">
                            <div class="statistic-box">
                               <i style="font-size:16px;" class="fa fa-user-plus fa-3x"></i>
                               <div class="counter-number pull-right">
                                  <span style="font-size:13px; color:#808080;" class="count-number" data-toggle="tooltip" title="Total Upcoming Booking"><?= $row['total_qty']; ?></span>
                                  <span class="slight">
                                  <i class="fa fa-play fa-rotate-270"></i>
                                  </span>
                               </div>
                               <br><br>
                               <div class="pull-right">
                                  <span style="font-size:20px; color:#808080;"><b><?= $row['payment_mode']; ?> !</b></span>
                               </div>
                               <div class="pull-left">
                                  <span data-toggle="tooltip" title="Current Date Total Amount"><span style="font-size:20px; color:#808080;"><b><?= $row['total_amount']; ?></b></span></span>
                               </div>
                            </div>
                         </div>
                      </div>
                     <?php
                     }
                     ?>
                  </div>
               <!-- Plugin content:powerpoint,txt,pdf,png,word,xl -->
               <?php include('include/search.php'); ?>
               
               
               <div class="row"style="background-color: #F5F5F5; margin-left:0px; margin-right:0px; margin-top:4px;">
                  <form name="latestsearchForm" id="latestsearchForm" method="post" class="search-form" action="" autocomplete="off">
                     <input type="hidden" name="page" value="1" />
                     <input type="hidden" name="export" value="" />
                     <input type="hidden" name="sortOrder" value="DESC" />
                     <input type="hidden" name="sortField" value="id" />
                     <div class="">
                        <!--  <div class="col-md-1">
                           <small>Total Qty</small>
                           <p class="form-control bg-light total-qty"></p>
                           </div> -->
                        <div class="col-sm-3">
                           <small>Sale Qty</small>
                           <p class="form-control bg-light t_qty"></p>
                        </div>
                        <!-- <div class="col-sm-1">
                           <small>Balance Qty</small>
                           <p class="form-control bg-light t_bal_qty"></p>
                           </div> -->
                        <div class="col-sm-3">
                           <small>Total Rate</small>
                           <p class="form-control bg-light t_rate"></p>
                        </div>
                        <div class="col-sm-3">
                           <small>Total GST</small>
                           <p class="form-control bg-light t_gst"></p>
                        </div>
                        <div class="col-sm-3">
                           <small>Total Amount</small>
                           <p class="form-control bg-light t_amt"></p>
                        </div>
                        <!--<div class="col-md-3">-->
                        <!--   <small>All in One Search</small>-->
                        <!--   <input type="text" class="form-control search-input" placeholder="Search.." name="search">-->
                        <!--</div>-->
                     </div>
                  </form>
               </div>
               
               <!-- Plugin content:powerpoint,txt,pdf,png,word,xl -->
               <div class="table-responsive ">
                  <table id="dataTableExample1" class="table table-bordered table-striped table-hover">
                     <thead>
                        <tr class="info">
                           <th>SrNo</th>
                           <th>DATE</th>
                           <th>QTY</th>
                           <th>CASH</th>
                           <th>UPI</th>
                           <th>CARD</th>
                           <th>CREDIT</th>
                        </tr>
                     </thead>
                     <tbody id="dsr1-list-latest-results">
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
                     <span>DSR (Report) !</span>
                  </span>
               </div>
            </div>
            <div class="panel-body">
               <div class="btn-group">
                  <!--<style>-->
                  <!--   .ppbilllist{-->
                  <!--   border-radius: 50px;-->
                  <!--   border-color: #37475a;-->
                  <!--   margin-top: -16px;-->
                  <!--   }-->
                  <!--</style>-->
                  <a href="tejaserp-sale-billwise-list.php"><button class="btn btn-exp btn-sm ppbilllist" data-toggle=""style="background-color:#37475a; margin-top: -18px;"><i class="fa fa-bars"></i><span style="margin-left:3px;">Sale BillWise List !</span></button></a>
                  <a href="tejaserp-sale-itemwise-list.php"><button class="btn btn-exp btn-sm dropdown-toggle ppbilllist" data-toggle=""style="background-color:#37475a; border-top-right-radius: 0px; border-bottom-right-radius: 0px; margin-top: -19px; margin-left: -5px;"><i class="fa fa-bars"></i><span style="margin-left:3px;">Sale ItemWise List !</span></button></a>
                  <div class="btn-group" style="margin-top: 0px;">
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
               <div class="row">
                     <?php 
                     $i = 1;
                     foreach(getPaymentSum() as $row){
                     ?>
                      <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
                         <div id="billwise-sale-cardbox<?= $i++; ?>">
                            <div class="statistic-box">
                               <i style="font-size:16px;" class="fa fa-user-plus fa-3x"></i>
                               <div class="counter-number pull-right">
                                  <span style="font-size:13px; color:#808080;" class="count-number" data-toggle="tooltip" title="Total Upcoming Booking"><?= $row['total_qty']; ?></span>
                                  <span class="slight">
                                  <i class="fa fa-play fa-rotate-270"></i>
                                  </span>
                               </div>
                               <br><br>
                               <div class="pull-right">
                                  <span style="font-size:20px; color:#808080;"><b><?= $row['payment_mode']; ?> !</b></span>
                               </div>
                               <div class="pull-left">
                                  <span data-toggle="tooltip" title="Current Date Total Amount"><span style="font-size:20px; color:#808080;"><b><?= $row['total_amount']; ?></b></span></span>
                               </div>
                            </div>
                         </div>
                      </div>
                     <?php
                     }
                     ?>
                  </div>
               <div class="row" style="background-color: #F5F5F5; margin-left:0px; margin-right:0px; margin-top:4px;">
                  <form name="searchForm" id="searchForm" method="post" class="search-form" action="" autocomplete="off">
                     <input type="hidden" name="export" value="" />
                     <input type="hidden" name="page" value="1" />
                     <input type="hidden" name="sortOrder" value="DESC" />
                     <input type="hidden" name="sortField" value="id" />
                     <div class="">
                        <!-- <div class="col-sm-2">
                           <small>Total Qty</small>
                           <p class="form-control bg-light total-qty"></p>
                           </div> -->
                        <div class="col-sm-2">
                           <small>Sale Qty</small>
                           <p class="form-control bg-light t_qty"></p>
                        </div>
                        <!-- <div class="col-sm-2">
                           <small>Balance Qty</small>
                           <p class="form-control bg-light t_bal_qty"></p>
                           </div> -->
                        <div class="col-sm-2">
                           <small>Total Rate</small>
                           <p class="form-control bg-light t_rate"></p>
                        </div>
                        <div class="col-sm-2">
                           <small>Total GST</small>
                           <p class="form-control bg-light t_gst"></p>
                        </div>
                        <div class="col-sm-2">
                           <small>Total Amount</small>
                           <p class="form-control bg-light t_amt"></p>
                        </div>
                        <div class="col-sm-2">
                           <small>From</small>
                           <input type="text" name="from_date" id="from_date" class="form-control from-date dtpicker" placeholder="Date From">
                        </div>
                        <div class="col-sm-2">
                           <small>To</small>
                           <input type="text" name="to_date" id="to_date" class="form-control to-date dtpicker" placeholder="Date To">
                        </div>
                     </div>
                  </form>
               </div>
               
               <!-- Plugin content:powerpoint,txt,pdf,png,word,xl -->
               <div class="table-responsive ">
                  <table id="dataTableExample1" class="table table-bordered table-striped table-hover">
                     <thead>
                        <tr class="info">
                           <th>SrNo</th>
                           <th>DATE</th>
                           <th>QTY</th>
                           <th>CASH</th>
                           <th>UPI</th>
                           <th>CARD</th>
                           <th>CREDIT</th>
                        </tr>
                     </thead>
                     <tbody id="dsr1-list-results">
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
      <div class="col-md-6">
         <div class="panel panel-bd lobidisable">
            <div class="panel-heading">
               <div class="panel-title">
                  <h5>Recently DSR (Report) Chart !</h5>
               </div>
            </div>
            <div class="panel-body">
               <canvas id="singelBarChart" height="250"></canvas>
            </div>
         </div>
      </div>
      <!-- Bar Chart -->
      <div class="col-md-6">
         <div class="panel panel-bd lobidisable">
            <div class="panel-heading">
               <div class="panel-title">
                  <h5>DSR (REPORT) Chart !</h5>
               </div>
            </div>
            <div class="panel-body">
               <canvas id="barChart" height="250"></canvas>
            </div>
         </div>
      </div>
      <!-- Line Chart -->
      
   </div>
</section>
<form action="" id="deleteform" method="post" autocomplete="off">
   <input type="hidden" name="action" value="delete">
   <input type="hidden" name="id" id="deleteid">
</form>
<?php include('include/footer-2.php'); ?>
</div>
<script src="assets/dist/js/page/sale-dsr1-list.js"></script>