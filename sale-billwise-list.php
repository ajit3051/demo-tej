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

   redirectTo('purchase-master-billwise-list.php', $msg, $code);
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
      redirectTo('purchase-master-billwise-list.php' . $sendid, 'Mail has been sent successfully');
   } else {
      redirectTo('purchase-master-billwise-list.php' . $sendid, 'Email id not found.', 1);
   }
}
?>
<?php include('include/header.php'); ?>

<section class="content">
   <div class="row">
      <div class="col-sm-12">
         <div class="panel panel-bd lobidrag ">
            <div class="panel-heading sidebar-toggle" data-toggle="offcanvas">
               <div class="btn-group" id="buttonexport">
                  <a href="#">
                     <h5>Recent Sale Billwise List</h5>
                  </a>
               </div>
            </div>
            <div class="panel-body">
               <?php include('include/button-export-to-data.php'); ?>
               <div class="btn-group">
                  <a href="sale-itemwise-list.php"><button class="btn btn-exp btn-sm dropdown-toggle" style="background-color:#37475a; color:#fff; border-radius: 25px;"><i class="fa fa-list"></i><span style="margin-left:5px;">Sale ItemWise List</span></button></a>
               </div>
               <?php include('include/search.php'); ?>
               <div class="row">

                  <div class="col-xs-12 col-sm-6 col-md-6 col-lg-4">
                     <div id="cardbox1" style="height: 100px;">
                        <div class="statistic-box">
                           <i style="font-size:16px;" class="fa fa-user-plus fa-3x"></i>
                           <div class="counter-number pull-right">
                              <span style="font-size:13px;" class="count-number cash-qty" data-toggle="tooltip" title="Total Upcoming Booking">11</span>
                              <span class="slight"><i class="fa fa-play fa-rotate-270"> </i>
                              </span>
                           </div>
                           <h4> <b style="font-size:15px;">CASH !</b></h4>
                           <div class="pull-left">
                              <span style="font-size:13px;" data-placement="bottom" data-toggle="tooltip" title="Current Date Total Amount"><button class="button label count-number cash-amt">40</button></span>
                           </div>
                        </div>
                     </div>
                  </div>

                  <div class="col-xs-12 col-sm-6 col-md-6 col-lg-4">
                     <div id="cardbox1" style="height: 100px;">
                        <div class="statistic-box">
                           <i style="font-size:16px;" class="fa fa-user-plus fa-3x"></i>
                           <div class="counter-number pull-right">
                              <span style="font-size:13px;" class="count-number upi-qty" data-toggle="tooltip" title="Total Upcoming Booking ">11</span>
                              <span class="slight"><i class="fa fa-play fa-rotate-270"> </i>
                              </span>
                           </div>
                           <h4> <b style="font-size:15px;">UPI !</b></h4>
                           <div class="pull-left">
                              <span style="font-size:13px;" data-placement="bottom" data-toggle="tooltip" title="Current Date Total Amount"><button class="button label count-number upi-amt">40</button></span>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="col-xs-12 col-sm-6 col-md-6 col-lg-4">
                     <div id="cardbox1" style="height: 100px;">
                        <div class="statistic-box">
                           <i style="font-size:16px;" class="fa fa-user-plus fa-3x"></i>
                           <div class="counter-number pull-right">
                              <span style="font-size:13px;" class="count-number card-qty" data-toggle="tooltip" title="Total Upcoming Booking ">11</span>
                              <span class="slight"><i class="fa fa-play fa-rotate-270"> </i>
                              </span>
                           </div>
                           <h4> <b style="font-size:15px;">CARD !</b></h4>
                           <div class="pull-left">
                              <span style="font-size:13px;" data-placement="bottom" data-toggle="tooltip" title="Current Date Total Amount"><button class="button label count-number card-amt">40</button></span>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="topnav">
                  <form name="latestsearchForm" id="latestsearchForm" method="post" class="search-form" action="" autocomplete="off">
                  <input type="hidden" name="export" value="" />
                     <input type="hidden" name="page" value="1" />
                     <input type="hidden" name="sortOrder" value="DESC" />
                     <input type="hidden" name="sortField" value="id" />
                     <div class="row" style="margin-left: 0px;margin-bottom: 0px;">
                        <div class="col-md-2">
                           <small>Total Qty</small>
                           <p class="form-control bg-light total-qty"><?= countLatestRecords('tbl_bill', 'qty') ?></p>
                        </div>
                        <div class="col-md-2">
                           <small>Total Rate</small>
                           <p class="form-control bg-light total-gst"><?= getTotalLatestValue('tbl_bill', 'gst_amount') ?></p>
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
                           <small>All in One Search</small>
                           <input type="text" class="form-control search-input" placeholder="Search.." name="search">
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
<section class="content">

   <div class="row">
      <div class="col-sm-12">
         <div class="panel panel-bd lobidrag ">
            <div class="panel-heading sidebar-toggle" data-toggle="offcanvas">
               <div class="btn-group" id="buttonexport">
                  <a href="#">
                     <h5>Sale Billwise List</h5>
                  </a>
               </div>
            </div>







            <div class="panel-body">
               <!-- Plugin content:powerpoint,txt,pdf,png,word,xl -->
               <?php include('include/button-export-to-data.php'); ?>
               <div class="btn-group">
                  <a href="brand-master-creation.php"><button class="btn btn-exp btn-sm dropdown-toggle" data-toggle=""><i class="fa fa-users"></i><span style="margin-left:3px;">Purchase Bill Wise List !</span></button></a>
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
               <div class="row">

                  <div class="col-xs-12 col-sm-6 col-md-6 col-lg-4">
                     <div id="cardbox1" style="height: 100px;">
                        <div class="statistic-box">
                           <i style="font-size:16px;" class="fa fa-user-plus fa-3x"></i>
                           <div class="counter-number pull-right">
                              <span style="font-size:13px;" class="count-number cash-qty" data-toggle="tooltip" title="Total Upcoming Booking ">11</span>
                              <span class="slight"><i class="fa fa-play fa-rotate-270"> </i>
                              </span>
                           </div>
                           <h4> <b style="font-size:15px;">CASH !</b></h4>
                           <div class="pull-left">
                              <span style="font-size:13px;" data-placement="bottom" data-toggle="tooltip" title="Current Date Total Amount"><button class="button label count-number cash-amt">40</button></span>
                           </div>
                        </div>
                     </div>
                  </div>

                  <div class="col-xs-12 col-sm-6 col-md-6 col-lg-4">
                     <div id="cardbox1" style="height: 100px;">
                        <div class="statistic-box">
                           <i style="font-size:16px;" class="fa fa-user-plus fa-3x"></i>
                           <div class="counter-number pull-right">
                              <span style="font-size:13px;" class="count-number upi-qty" data-toggle="tooltip" title="Total Upcoming Booking">11</span>
                              <span class="slight"><i class="fa fa-play fa-rotate-270"> </i>
                              </span>
                           </div>
                           <h4> <b style="font-size:15px;">UPI !</b></h4>
                           <div class="pull-left">
                              <span style="font-size:13px;" data-placement="bottom" data-toggle="tooltip" title="Current Date Total Amount"><button class="button label count-number upi-amt">40</button></span>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="col-xs-12 col-sm-6 col-md-6 col-lg-4">
                     <div id="cardbox1" style="height: 100px;">
                        <div class="statistic-box">
                           <i style="font-size:16px;" class="fa fa-user-plus fa-3x"></i>
                           <div class="counter-number pull-right">
                              <span style="font-size:13px;" class="count-number card-qty" data-toggle="tooltip" title="Total Upcoming Booking">11</span>
                              <span class="slight"><i class="fa fa-play fa-rotate-270"> </i>
                              </span>
                           </div>
                           <h4> <b style="font-size:15px;">CARD !</b></h4>
                           <div class="pull-left">
                              <span style="font-size:13px;" data-placement="bottom" data-toggle="tooltip" title="Current Date Total Amount"><button class="button label count-number card-amt">40</button></span>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="topnav">
                  <form name="searchForm" id="searchForm" method="post" class="search-form" action="" autocomplete="off">


                  <input type="hidden" name="export" value="" />
                     <input type="hidden" name="page" value="1" />
                     <input type="hidden" name="sortOrder" value="DESC" />
                     <input type="hidden" name="sortField" value="id" />


                     <div class="row" style="margin-left: 0px;margin-bottom: 0px;">

                        <div class="col-md-2">
                           <small>Total Qty</small>
                           <p class="form-control bg-light total-qty"><?= countRecords('tbl_bill', 'qty') ?></p>
                        </div>
                        <div class="col-sm-2">
                           <small>Total Rate</small>
                           <p class="form-control bg-light total-gst"><?= getTotalValue('tbl_bill', 'gst_amount') ?></p>
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
                     <div class="row" style="margin-left: 0px;margin-bottom: 10px;">
                        <div class="col-sm-12">
                           <div class="search-container">
                              <!--<small>All in One Search</small>-->
                              <input type="text" class="form-control search-input" placeholder="Search.." name="search">

                           </div>
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
<section class="content">
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
</div>/var/www/html/tej/sale-itemwise-list.php
<?php include('include/footer-2.php'); ?>
<script src="assets/dist/js/page/sale-billwise-list.js"></script>