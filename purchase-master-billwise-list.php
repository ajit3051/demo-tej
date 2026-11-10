<?php include_once("include/config.php"); ?>
<?php require_once(_BASEPATH . 'include/generatepdf.php'); ?>


<?php
$validationHelper = new validation();

$db = connect();
$bill_no = $_GET['bill_no'];

if (($_POST['action'] == 'delete' || $_POST['action'] == 'delete_bulk') && (!empty($_POST['id']) || !empty($_POST['ids']))) {

   if (!empty($_POST['ids'])) {
      $ids = array_filter(array_map('intval', explode(',', $_POST['ids'])));
      if (!empty($ids)) {
         $inClause = implode(',', $ids);
         $res = $db->query("DELETE FROM tbl_purchase WHERE id IN ($inClause)");
      }
   } else {
      $id = (int)$_POST['id'];
      $res = $db->delete("DELETE FROM tbl_purchase WHERE id=?", 'i', $id);
   }

   if ($res) {
      $msg = 'Record deleted successfully';
      $code = '';
   } else {
      $msg = 'Failed!';
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
   <div class="row"style="margin-left:-28px; margin-right:-23px;">
      <div class="col-sm-12">
         <div class="panel panel-bd lobidisable">
            <div class="panel-heading sidebar-toggle" data-toggle="offcanvas">
               <div class="btn-group" id="buttonexport">
                  <a href="#">
                     <h5><span style="margin-left:10px;">Recently Purchase Billwise List</span></h5>
                  </a>
               </div>
            </div>
            <div class="panel-body form-scroll">
               <div class="btn-group" style="margin-top:-15px;">
                  <style>
                     .ppbilllist{
                     border-radius: 50px;
                     border-color: #37475a;
                     
                     }
                  </style>
                   <a href="purchase-master-itemwise-list.php"><button class="btn btn-exp btn-sm dropdown-toggle ppbilllist" data-toggle=""style="background-color:#37475a; border-top-right-radius: 0px; border-bottom-right-radius: 0px;"><i class="fa fa-list"></i><span style="margin-left:5px;">Purchase ItemWise List</span></button></a>
                  <div class="btn-group">
                     <button class="btn btn-exp btn-sm dropdown-toggle ppbilllist" data-toggle="dropdown" style="background-color:#37475a; color: #fff;"><i class="fa fa-download"></i> Export Table Data</button>
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
              
               
                   <div class="row"style="margin-top:-12px;">
               <form name="latestsearchForm" id="latestsearchForm" method="post" class="search-form" action="" autocomplete="off">
                     <input type="hidden" name="page" value="1" />
                     <input type="hidden" name="export" value="" />
                     <input type="hidden" name="sortOrder" value="DESC" />
                     <input type="hidden" name="sortField" value="id" />
                  <div class="" style="margin-left: 0px;margin-bottom: 5px;">
                     <div class="col-md-4">
                        <small>Total Qty</small>
                        <p class="form-control bg-light total-qty"><?= countLatestRecords('tbl_purchase', 'qty') ?></p>
                     </div>
                     <div class="col-md-4">
                        <small>Total Gst</small>
                        <p class="form-control bg-light total-gst"><?= getTotalLatestValue('tbl_purchase', 'gst_amount') ?></p>
                     </div>
                     <div class="col-md-4">
                        <small>Total Bill Amt</small>
                        <p class="form-control bg-light total-amt"><?= getTotalLatestValue('tbl_purchase', 'amount') ?></p>
                     </div>
                     <!--<div class="col-md-6">-->
                     <!--      <small>All in One Search</small>-->
                     <!--         <input type="text" class="form-control search-input" placeholder="Search.." name="search">-->
                     <!--</div>-->
                  </div>
                  <div class="">
                       <div class="col-md-12" style="margin-top:-10px;">
                           <!--<small>All in One Search</small>-->
                              <input type="text" class="form-control-control search-input" placeholder="Search.." name="search">
                     </div>
                  </div>
                  </form>
                  </div>
                  
               <!--    <div class="container-fluid">-->
               <!--   <div class="row">-->
               <!--      <input type="text" class="form-control-control" name="company_name" id="company_name" placeholder="Data Search" required>-->
               <!--   </div>-->
               <!--</div>-->
              
               <!-- Plugin content:powerpoint,txt,pdf,png,word,xl -->
               <div class="table-responsive ">
                  <table id="dataTableExample1" class="table table-bordered table-striped table-hover">
                     <thead>
                        <tr class="info">
                           <th>SrNo</th>
                           <th>Date & Time</th>
                           <th>Bill No</th>
                           <th>Invoice No</th>
                           <th>Account Name</th>
                           <th>Qty</th>
                           <th>GST Type</th>
                           <th>GST Amt</th>
                           <th>Bill Amt</th>
                           <th>Status</th>
                           <th>Details</th>
                           <th>Action</th>
                        </tr>
                     </thead>
                     <tbody id="billwise-list-latest-results">

                     </tbody>
                  </table>
               </div>
               <style>
                  .dwc {
                     font-size: 13px;
                     height: 38px;
                     width: auto;
                     padding: 0 18px;
                     font-weight: 600;
                     border-radius: 50px;
                     display: inline-flex;
                     align-items: center;
                     justify-content: center;
                     gap: 6px;
                     box-shadow: 0 2px 6px rgba(229,57,53,0.2);
                     transition: all 0.2s ease;
                  }
                  .dwc:hover {
                     transform: translateY(-1px);
                     box-shadow: 0 4px 10px rgba(229,57,53,0.3);
                  }
               </style>
               <div class="row" style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; margin-top: 10px; margin-bottom: 10px;">
                   <div class="col-sm-8 col-xs-12">
                       <div id="pagination-latest-result"></div>
                   </div>
                   <div class="col-sm-4 col-xs-12" style="text-align:right;">
                       <button type="button" class="btn btn-danger btn-sm dwc" onclick="deleteRow('latest')"><i class="fa fa-trash"></i><span style="margin-left:4px;">Delete Selected</span></button>
                   </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</section>
<section class="">
   <div class="row" style="margin-left:-28px; margin-right:-23px;">
      <div class="col-sm-12">
         <div class="panel panel-bd lobidisable  ">
            <div class="panel-heading sidebar-toggle" data-toggle="offcanvas">
               <div class="btn-group" id="buttonexport">
                  <a href="#">
                     <h5><span style="margin-left:10px;">Purchase Billwise List</span></h5>
                  </a>
               </div>
            </div>
            <div class="panel-body form-scroll">
               <!-- Plugin content:powerpoint,txt,pdf,png,word,xl -->
               <!--<?php include('include/button-export-to-data.php'); ?>-->
                <div class="btn-group"style="margin-top:-16px;">
                  <style>
                     .ppbilllist{
                     border-radius: 50px;
                     border-color: #37475a;
                     
                     }
                  </style>
                   <a href="purchase-master-itemwise-list.php"><button class="btn btn-exp btn-sm dropdown-toggle ppbilllist" data-toggle=""style="background-color:#37475a; border-top-right-radius: 0px; border-bottom-right-radius: 0px;"><i class="fa fa-list"></i><span style="margin-left:5px;">Purchase ItemWise List</span></button></a>
                  <div class="btn-group">
                     <button class="btn btn-exp btn-sm dropdown-toggle ppbilllist" data-toggle="dropdown" style="background-color:#37475a; color: #fff;"><i class="fa fa-download"></i> Export Table Data</button>
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
               <div class="row"style="margin-top:-12px;">
                  <form name="searchForm" id="searchForm" method="post" class="search-form" action="" autocomplete="off">
                     <input type="hidden" name="page" value="1" />
                     <input type="hidden" name="export" value="" />
                     <input type="hidden" name="sortOrder" value="DESC" />
                     <input type="hidden" name="sortField" value="id" />

                     <div class="" style="margin-left: 0px;margin-bottom: 5px;">
                        
                        <div class="col-md-2">
                           <small>Total Qty</small>
                           <p class="form-control bg-light total-qty"><?= countRecords('tbl_purchase', 'qty') ?></p>
                        </div>
                        <div class="col-sm-2">
                           <small>Total Gst</small>
                           <p class="form-control bg-light total-gst"><?= getTotalValue('tbl_purchase', 'gst_amount') ?></p>
                        </div>
                        <div class="col-sm-2">
                           <small>Total Bill Amt</small>
                           <p class="form-control bg-light total-amt"><?= getTotalValue('tbl_purchase', 'amount') ?></p>
                        </div>
                        <div class="col-sm-3">
                           <small>From</small>
                           <input type="text" name="from_date" id="from_date" class="form-control from-date dtpicker" placeholder="Enter Date...">
                        </div>
                        <div class="col-sm-3">
                           <small>To</small>
                           <input type="text" name="to_date" id="to_date" class="form-control to-date dtpicker" placeholder="Enter Date...">
                        </div>
                       
                     </div>
                      <div class="">
                       <div class="col-md-12" style="margin-top:-10px;">
                           <!--<small>All in One Search</small>-->
                              <input type="text" class="form-control-control search-input" placeholder="Search.." name="search">
                     </div>
                  </div>
                  </form>
               </div>

               <div class="table-responsive ">
                  <table id="dataTableExample1" class="table table-bordered table-striped table-hover">
                     <thead>
                        <tr class="info">
                           <th>SrNo</th>
                           <th>Date & Time</th>
                           <th>Bill No</th>
                           <th>Invoice No</th>
                           <th>Account Name</th>
                           <th>Qty</th>
                           <th>GST Type</th>
                           <th>GST Amt</th>
                           <th>Bill Amt</th>
                           <th>Status</th>
                           <th>Details</th>
                           <th>Action</th>
                        </tr>
                     </thead>
                     <tbody id="billwise-list-results">

                     </tbody>
                  </table>
               </div>
               <div class="row" style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; margin-top: 10px; margin-bottom: 10px;">
                   <div class="col-sm-8 col-xs-12">
                       <div id="pagination-result"></div>
                   </div>
                   <div class="col-sm-4 col-xs-12" style="text-align:right;">
                       <button type="button" class="btn btn-danger btn-sm dwc" onclick="deleteRow('main')"><i class="fa fa-trash"></i><span style="margin-left:4px;">Delete Selected</span></button>
                   </div>
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
   <input type="hidden" name="action" id="deleteaction" value="delete">
   <input type="hidden" name="id" id="deleteid">
   <input type="hidden" name="ids" id="deleteids">
</form>
<?php include('include/footer-2.php'); ?>
</div>

<script src="assets/dist/js/page/purchase-master-billwise-list.js"></script>