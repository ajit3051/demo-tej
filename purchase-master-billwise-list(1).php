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



<section class="">
   <div class="row">
      <div class="col-sm-12">
         <div class="panel panel-bd lobidisable">
            <div class="panel-heading sidebar-toggle" data-toggle="offcanvas">
               <div class="btn-group" id="buttonexport">
                  <a href="#">
                     <h5>Recently Purchase Billwise List</h5>
                  </a>
               </div>
            </div>
            <div class="panel-body" style="margin-top:-13px;">
                <div class="btn-group">
                    <style>
                       .ppbilllist{
                           border-radius: 50px;
                       }
                   </style>
                  <a href="brand-master-creation.php"><button class="btn btn-exp btn-sm dropdown-toggle ppbilllist" data-toggle=""><i class="fa fa-bars"></i><span style="margin-left:3px;">Purchase Item Wise List !</span></button></a>
                  
                  <div class="btn-group">
   <button class="btn btn-exp btn-sm dropdown-toggle ppbilllist" data-toggle="dropdown" style="background-color:#009688;"><i class="fa fa-bars"></i> Export Table Data</button>
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
                     <div class="col-md-2">
                        <small>Total Qty</small>
                        <p class="form-control bg-light total-qty"><?= countLatestRecords('tbl_purchase', 'qty') ?></p>
                     </div>
                     <div class="col-md-2">
                        <small>Total Gst</small>
                        <p class="form-control bg-light total-gst"><?= getTotalLatestValue('tbl_purchase', 'gst_amount') ?></p>
                     </div>
                     <div class="col-md-2">
                        <small>Total Bill Amt</small>
                        <p class="form-control bg-light total-amt"><?= getTotalLatestValue('tbl_purchase', 'amount') ?></p>
                     </div>
                     <div class="col-md-6">
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
                           <th>Invoice No</th>
                           <th>Account Name</th>
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
         <div class="panel panel-bd lobidisable ">
            <div class="panel-heading sidebar-toggle" data-toggle="offcanvas">
               <div class="btn-group" id="buttonexport">
                  <a href="#">
                     <h5>Purchase Billwise List</h5>
                  </a>
               </div>
            </div>
            <div class="panel-body"style="margin-top:-13px;">
               <!-- Plugin content:powerpoint,txt,pdf,png,word,xl -->
               <!--<?php include('include/button-export-to-data.php'); ?>-->
                <div class="btn-group">
                    <style>
                       .ppbilllist{
                           border-radius: 50px;
                       }
                   </style>
                  <a href="brand-master-creation.php"><button class="btn btn-exp btn-sm dropdown-toggle ppbilllist" data-toggle=""><i class="fa fa-bars"></i><span style="margin-left:3px;">Purchase Item Wise List !</span></button></a>
                  
                  <div class="btn-group">
   <button class="btn btn-exp btn-sm dropdown-toggle ppbilllist" data-toggle="dropdown" style="background-color:#009688;"><i class="fa fa-bars"></i> Export Table Data</button>
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
                        <div class="col-sm-2">
                           <small>From</small>
                           <input type="text" name="from_date" id="from_date" class="form-control from-date dtpicker" placeholder="Enter Date...">
                        </div>
                        <div class="col-sm-2">
                           <small>To</small>
                           <input type="text" name="to_date" id="to_date" class="form-control to-date dtpicker" placeholder="Enter Date...">
                        </div>
                        <div class="col-sm-2">
                           <div class="search-container">
                              <small>All in One Search</small>
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
                           <th>Invoice No</th>
                           <th>Account Name</th>
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
<script src="assets/dist/js/page/purchase-master-billwise-list.js"></script>