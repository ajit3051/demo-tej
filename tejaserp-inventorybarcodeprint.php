<?php include_once("include/config.php"); ?>
<?php require_once(_BASEPATH . 'include/generatepdf.php'); ?>
<?php $exportType = "sale"; ?>
<?php include('include/header.php'); ?>

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
                                 <span>Recently Inventory Barcode Print !</span>
                              </span>
                           </div>
                        </div>
                        <div class="panel-body form-scroll">
                            <form name="latestsearchForm" id="latestsearchForm" method="post" class="search-form" action="" autocomplete="off">
                                 <input type="hidden" name="page" value="1" />
                                 <input type="hidden" name="export" value="" />
                                 <input type="hidden" name="sortOrder" value="DESC" />
                                 <input type="hidden" name="sortField" value="id" />
                               <div class="row">
                                  <div class="col-md-6">
                                     <div class="btn-group">
                                        <a href="fh_itemmastercreation.php"> <button class="button-btn-btn btn-exp btn-sm"><i class="fa fa-plus"></i>Add New Purchase Creation</button></a>
                                     </div>
                                     <?php include('include/button-export-to-data.php');?>
                                  </div>
                                  <div class="col-md-2">
                                  </div>
                                  <div class="col-md-2" style="margin-top:-20px;">
                                     <small>Barcode qty</small>
                                     <input type="text"
                                        class="form-control"
                                        placeholder="Barcode Qty">
                                  </div>
                                  <div class="col-md-2" style="margin-top:-20px;">
                                     <small>Purchase qty</small>
                                     <input type="text"
                                        class="form-control"
                                        placeholder="Purchase Qty">
                                  </div>
                               </div>
                               <div class="container-fluid">
                                  <div class="row">
                                     <input type="text" class="form-control-control search-input" name="search" placeholder="Data Search" required>
                                  </div>
                               </div>
                               <div class="table-responsive">
                                  <table id="dataTableExample1" class="table table-bordered table-striped table-hover">
                                     <thead>
                                        <tr class="info">
                                       <th width="50px;">SrNo</th>
                                       <th>Status</th>
                                       <th>Date & Time</th>
                                       <th>Bill No.</th>
                                       <th>Store Code</th>
                                       <th>Store Name</th>
                                       <th>Location</th>
                                       <th>
                                          <input name="export_data[]" value="barcode_number" type="checkbox">
                                          Barcode No
                                       </th>
                                       <th><input name="export_data[]" value="article_no" type="checkbox">
                                          Article No
                                       </th>
                                       <th><input name="export_data[]" value="item_name" type="checkbox">
                                          Item Name
                                       </th>
                                       <th><input name="export_data[]" value="qty" type="checkbox">
                                          Qty.
                                       </th>
                                       <th><input name="export_data[]" value="category" type="checkbox">
                                          Group
                                       </th>
                                       <th><input name="export_data[]" value="brand" type="checkbox">
                                          Brand
                                       </th>
                                       <th><input name="export_data[]" value="color" type="checkbox">Color</th>
                                       <th><input name="export_data[]" value="size" type="checkbox">Size</th>
                                       <th><input name="export_data[]" value="style" type="checkbox">Style</th>
                                       <th><input name="export_data[]" value="mrp" type="checkbox">MRP</th>
                                       <th><input name="export_data[]" value="percent_discount" type="checkbox">Dis.(%)</th>
                                       <th><input name="export_data[]" value="selling_price" type="checkbox">SP</th>
                                    </tr>
                                     </thead>
                                     <tbody class="inventory-results">
                                    </tbody>
                                  </table>
                               </div>
                               <div class="pagination-result">
                                </div>
                            </div>
                            <div class="container-fluid">
                               <div class="row">
                                
                                  <div class="col-md-6" style="margin-top:10px;">
                                     <button type="button" class="btn btn-warning resetPrint" style="width:100px;">Reset</button>
                                     <button type="button" class="btn btn-success barcodePrint" style="width:110px;background-color: #009688; color: white;">Print Barcode</button>
                                  </div>
                               </div>
                            </div>
                            </form>
                        <br>
                     </div>
                  </div>
               </div>
               <!-- customer Modal1 -->
               <!-- Add salary Modal1 -->
               <!-- details Modal1 -->
               <div class="modal fade" id="details" tabindex="-1" role="dialog" aria-hidden="true">
                  <!-- <div class="modal-dialog"> -->
                  <div class="container" style="margin-left:100px;">
                     <div class="modal-content">
                        <div class="modal-header modal-header-primary">
                           <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                           <h3><i class="fa fa-plus m-r-5"></i> Account Master Details</h3>
                        </div>
                        <div class="form-horizontal col-md-12">
                           <div class="panel" style="background-color:#009688; text-align:center; color:white;">
                              <div class="panel-heading">
                                 <div class="panel-title">
                                    <strong><?php echo $user['account_name']; ?></strong>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <div class="modal-body">
                           <div class="row">
                              <div class="col-md-3">
                                 <!-- User widget -->
                                 <div class="user-widget list-group">
                                    <div class="list-group-item heading">
                                       <img class="media-object img-circle" src="assets/dist/img/avatar5.png" alt="image">
                                       <div class="clearfix"></div>
                                    </div>
                                    <br>
                                    <div class="text-wrap">
                                       <a class="group-item">
                                          <p class="list-group-item-text"><strong><?php echo $user['account_name']; ?></strong></p>
                                       </a>
                                    </div>
                                 </div>
                              </div>
                              <div class="col-md-9">
                                 <!-- User widget -->
                                 <div class="row">
                                    <div class="col-sm-4">
                                       <p class="list-group-item-text"><strong>Account Code</strong></p>
                                       <p class="pull-left"><?php echo $user['account_code']; ?></p>
                                    </div>
                                    <div class="col-sm-4">
                                       <p class="list-group-item-text"><strong>Contact Name</strong></p>
                                       <p class="pull-left"><?php echo $user['contact_name']; ?></p>
                                    </div>
                                    <div class="col-sm-4">
                                       <p class="list-group-item-text"><strong>Group Name</strong></p>
                                       <p class="pull-left"><?php echo $user['group_name']; ?></p>
                                    </div>
                                 </div>
                                 <br>
                                 <div class="row">
                                    <div class="col-sm-4">
                                       <p class="list-group-item-text"><strong>Mobile No</strong></p>
                                       <p class="pull-left"><?php echo $user['account_code']; ?></p>
                                    </div>
                                    <div class="col-sm-4">
                                       <p class="list-group-item-text"><strong>Mobile No</strong></p>
                                       <p class="pull-left"><?php echo $user['contact_name']; ?></p>
                                    </div>
                                    <div class="col-sm-4">
                                       <p class="list-group-item-text"><strong>Email-ID</strong></p>
                                       <p class="pull-left"><?php echo $user['group_name']; ?></p>
                                    </div>
                                 </div>
                                 <br>
                                 <div class="row">
                                    <div class="col-sm-4">
                                       <p class="list-group-item-text"><strong>GST No</strong></p>
                                       <p class="pull-left"><?php echo $user['account_code']; ?></p>
                                    </div>
                                    <div class="col-sm-4">
                                       <p class="list-group-item-text"><strong>State Code</strong></p>
                                       <p class="pull-left"><?php echo $user['contact_name']; ?></p>
                                    </div>
                                    <div class="col-sm-4">
                                       <p class="list-group-item-text"><strong>Location</strong></p>
                                       <p class="pull-left"><?php echo $user['group_name']; ?></p>
                                    </div>
                                 </div>
                                 <br>
                                 <div class="row">
                                    <div class="col-sm-4">
                                       <p class="list-group-item-text"><strong>Address</strong></p>
                                       <p class="pull-left"><?php echo $user['account_code']; ?></p>
                                    </div>
                                    <div class="col-sm-4">
                                       <p class="list-group-item-text"><strong>Address</strong></p>
                                       <p class="pull-left"><?php echo $user['contact_name']; ?></p>
                                    </div>
                                    <div class="col-sm-4">
                                       <p class="list-group-item-text"><strong>Location</strong></p>
                                       <p class="pull-left"><?php echo $user['group_name']; ?></p>
                                    </div>
                                 </div>
                                 <br>
                                 <div class="row">
                                    <div class="col-sm-4">
                                       <p class="list-group-item-text"><strong>Mobile No</strong></p>
                                       <p class="pull-left"><?php echo $user['account_code']; ?></p>
                                    </div>
                                    <div class="col-sm-4">
                                       <p class="list-group-item-text"><strong>Email-ID</strong></p>
                                       <p class="pull-left"><?php echo $user['contact_name']; ?></p>
                                    </div>
                                    <div class="col-sm-4">
                                       <p class="list-group-item-text"><strong>Address</strong></p>
                                       <p class="pull-left"><?php echo $user['group_name']; ?></p>
                                    </div>
                                 </div>
                                 <br>
                                 <div class="row">
                                    <div class="col-sm-4">
                                       <p class="list-group-item-text"><strong>Address</strong></p>
                                       <p class="pull-left"><?php echo $user['account_code']; ?></p>
                                    </div>
                                    <div class="col-sm-4">
                                       <p class="list-group-item-text"><strong>Party Type</strong></p>
                                       <p class="pull-left"><?php echo $user['contact_name']; ?></p>
                                    </div>
                                    <div class="col-sm-4">
                                       <p class="list-group-item-text"><strong>Opening Balance</strong></p>
                                       <p class="pull-left"><?php echo $user['group_name']; ?></p>
                                    </div>
                                 </div>
                                 <br>
                                 <div class="row">
                                    <div class="col-sm-4">
                                       <p class="list-group-item-text"><strong>Credit Limit</strong></p>
                                       <p class="pull-left"><?php echo $user['account_code']; ?></p>
                                    </div>
                                    <div class="col-sm-4">
                                       <p class="list-group-item-text"><strong>Gender</strong></p>
                                       <p class="pull-left"><?php echo $user['contact_name']; ?></p>
                                    </div>
                                    <div class="col-sm-4">
                                       <p class="list-group-item-text"><strong>Creation Date</strong></p>
                                       <p class="pull-left"><?php echo $user['group_name']; ?></p>
                                    </div>
                                 </div>
                                 <br>
                                 <div class="row">
                                    <div class="col-sm-4">
                                       <p class="list-group-item-text"><strong>Status</strong></p>
                                       <p class="pull-left"><?php echo $user['account_code']; ?></p>
                                    </div>
                                 </div>
                              </div>
                              <div class="form-horizontal col-md-12">
                                 <div class="panel" style="background-color: #FFB61E; color: white;">
                                    <div class="panel-heading">
                                       <div class="panel-title">
                                          <strong>Total Balance Amount</strong>
                                          <p class="pull-right"><?php echo $user['contact_name']; ?></p>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <div class="modal-footer">
                           <button type="button" class="btn btn-danger pull-left" data-dismiss="modal">Close</button>
                        </div>
                     </div>
                     <!-- /.modal-content -->
                  </div>
                  <!-- /.modal-dialog -->
               </div>
               <!-- /.modal -->
               <!-- /.modal -->
               <!-- /.modal -->
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
                                 <span>Bill Wise Inventory Barcode Print !</span>
                              </span>
                           </div>
                        </div>
                        <div class="panel-body form-scroll">
                            <form name="billwisesearchForm" id="billwisesearchForm" method="post" class="search-form" action="" autocomplete="off">
                             <input type="hidden" name="page" value="1" />
                             <input type="hidden" name="export" value="" />
                             <input type="hidden" name="sortOrder" value="DESC" />
                             <input type="hidden" name="sortField" value="id" />
                           <div class="row date-range-group">
                              <div class="col-md-4">
                                 <div class="btn-group">
                                    <a href="fh_itemmastercreation.php"> <button class="button-btn-btn btn-exp btn-sm"><i class="fa fa-plus"></i>Add New Purchase Creation</button></a>
                                 </div>
                                 <?php include('include/button-export-to-data.php');?>
                              </div>
                                 <div class="col-sm-2" style="margin-top:-20px;">
                              <small>Date From</small>
                              <div class=" input-group date form_date">
                                 <input type="text" id='billFromDate' name="from-date" class="form-control years from-date" value=""><span class="input-group-addon"><a href="#"><i class="fa fa-calendar"></i></a></span>
                              </div>
                           </div>
                           <div class="col-sm-2" style="margin-top:-20px;">
                              <small>Date To</small>
                              <div class=" input-group date form_date">
                                 <input type="text" id='billToDate' name="to-date" class="form-control years to-date" value=""><span class="input-group-addon"><a href="#"><i class="fa fa-calendar"></i></a></span>
                              </div>
                           </div>
                              <div class="col-md-2" style="margin-top:-20px;">
                                 <small>Barcode qty</small>
                                 <input type="text"
                                    class="form-control"
                                    placeholder="Barcode Qty">
                              </div>
                              <div class="col-md-2" style="margin-top:-20px;">
                                 <small>Purchase qty</small>
                                 <input type="text"
                                    class="form-control"
                                    placeholder="Purchase Qty">
                              </div>
                           </div>
                           <div class="container-fluid">
                              <div class="row">
                                 <input type="text" class="form-control-control search-input" name="search" placeholder="Data Search" required>
                              </div>
                           </div>
                           <div class="table-responsive">
                              <table id="dataTableExample1" class="table table-bordered table-striped table-hover">
                                 <thead>
                                    <tr class="info">
                                       <th width="50px;">SrNo</th>
                                       <th>Status</th>
                                       <th>Date & Time</th>
                                       <th>Bill No.</th>
                                       <th>Store Code</th>
                                       <th>Store Name</th>
                                       <th>Location</th>
                                       <th>
                                          <input name="export_data[]" value="barcode_number" type="checkbox">
                                          Barcode No
                                       </th>
                                       <th><input name="export_data[]" value="article_no" type="checkbox">
                                          Article No
                                       </th>
                                       <th><input name="export_data[]" value="item_name" type="checkbox">
                                          Item Name
                                       </th>
                                       <th><input name="export_data[]" value="qty" type="checkbox">
                                          Qty.
                                       </th>
                                       <th><input name="export_data[]" value="category" type="checkbox">
                                          Group
                                       </th>
                                       <th><input name="export_data[]" value="brand" type="checkbox">
                                          Brand
                                       </th>
                                       <th><input name="export_data[]" value="color" type="checkbox">Color</th>
                                       <th><input name="export_data[]" value="size" type="checkbox">Size</th>
                                       <th><input name="export_data[]" value="style" type="checkbox">Style</th>
                                       <th><input name="export_data[]" value="mrp" type="checkbox">MRP</th>
                                       <th><input name="export_data[]" value="percent_discount" type="checkbox">Dis.(%)</th>
                                       <th><input name="export_data[]" value="selling_price" type="checkbox">SP</th>
                                    </tr>
                                 </thead>
                                  <tbody class="inventory-results">
                                </tbody>
                                    
                              </table>
                           </div>
                           <div class="pagination-result">
                            </div>
                        </div>
                        <div class="container-fluid">
                               <div class="row">
                                
                                  <div class="col-md-6" style="margin-top:10px;">
                                     <button type="button" class="btn btn-warning resetPrint" style="width:100px;">Reset</button>
                                     <button type="button" class="btn btn-success barcodePrint" style="width:110px;background-color: #009688; color: white;">Print Barcode</button>
                                  </div>
                               </div>
                            </div>
                       </form>
                     </div>
                  </div>
               </div>
               
               
            </section>
            <!-- /.content -->

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
                                 <span>Item Wise Inventory Barcode Print !</span>
                              </span>
                           </div>
                        </div>
                        <div class="panel-body form-scroll">
                            <form name="itemwisesearchForm" id="itemwisesearchForm" method="post" class="search-form" action="" autocomplete="off">
                             <input type="hidden" name="page" value="1" />
                             <input type="hidden" name="export" value="" />
                             <input type="hidden" name="sortOrder" value="DESC" />
                             <input type="hidden" name="sortField" value="id" />
                           <div class="row date-range-group">
                              <div class="col-md-4">
                                 <div class="btn-group">
                                    <a href="fh_itemmastercreation.php"> <button class="button-btn-btn btn-exp btn-sm"><i class="fa fa-plus"></i>Add New Purchase Creation</button></a>
                                 </div>
                                 <?php include('include/button-export-to-data.php');?>
                              </div>
                                 <div class="col-sm-2" style="margin-top:-20px;">
                              <small>Date From</small>
                              <div class=" input-group date form_date">
                                 <input type="text" id='itemFromDate' name="from-date" class="form-control years from-date" value=""><span class="input-group-addon"><a href="#"><i class="fa fa-calendar"></i></a></span>
                              </div>
                           </div>
                           <div class="col-sm-2" style="margin-top:-20px;">
                              <small>Date To</small>
                              <div class=" input-group date form_date">
                                 <input type="text" id='itemToDate' name="to-date" class="form-control years to-date" value=""><span class="input-group-addon"><a href="#"><i class="fa fa-calendar"></i></a></span>
                              </div>
                           </div>
                              <div class="col-md-2" style="margin-top:-20px;">
                                 <small>Barcode qty</small>
                                 <input type="text"
                                    class="form-control"
                                    placeholder="Barcode Qty">
                              </div>
                              <div class="col-md-2" style="margin-top:-20px;">
                                 <small>Purchase qty</small>
                                 <input type="text"
                                    class="form-control"
                                    placeholder="Purchase Qty">
                              </div>
                           </div>
                           <div class="container-fluid">
                              <div class="row">
                                 <input type="text" class="form-control-control search-input" name="search" placeholder="Data Search" required>
                              </div>
                           </div>
                           <div class="table-responsive">
                              <table id="dataTableExample1" class="table table-bordered table-striped table-hover">
                                 <thead>
                                    <tr class="info">
                                       <th width="50px;">SrNo</th>
                                       <th>Status</th>
                                       <th>Date & Time</th>
                                       <th>Bill No.</th>
                                       <th>Store Code</th>
                                       <th>Store Name</th>
                                       <th>Location</th>
                                       <th>
                                          <input name="export_data[]" value="barcode_number" type="checkbox">
                                          Barcode No
                                       </th>
                                       <th><input name="export_data[]" value="article_no" type="checkbox">
                                          Article No
                                       </th>
                                       <th><input name="export_data[]" value="item_name" type="checkbox">
                                          Item Name
                                       </th>
                                       <th><input name="export_data[]" value="qty" type="checkbox">
                                          Qty.
                                       </th>
                                       <th><input name="export_data[]" value="category" type="checkbox">
                                          Group
                                       </th>
                                       <th><input name="export_data[]" value="brand" type="checkbox">
                                          Brand
                                       </th>
                                       <th><input name="export_data[]" value="color" type="checkbox">Color</th>
                                       <th><input name="export_data[]" value="size" type="checkbox">Size</th>
                                       <th><input name="export_data[]" value="style" type="checkbox">Style</th>
                                       <th><input name="export_data[]" value="mrp" type="checkbox">MRP</th>
                                       <th><input name="export_data[]" value="percent_discount" type="checkbox">Dis.(%)</th>
                                       <th><input name="export_data[]" value="selling_price" type="checkbox">SP</th>
                                    </tr>
                                 </thead>
                                  <tbody class="inventory-results">
                                </tbody>
                                    
                                 </tbody>
                              </table>
                           </div>
                           <div class="pagination-result">
                            </div>
                            <div class="container-fluid">
                               <div class="row">
                                
                                  <div class="col-md-6" style="margin-top:10px;">
                                     <button type="button" class="btn btn-warning resetPrint" style="width:100px;">Reset</button>
                                     <button type="button" class="btn btn-success barcodePrint" style="width:110px;background-color: #009688; color: white;">Print Barcode</button>
                                  </div>
                               </div>
                            </div>
                            </form>
                        </div>
                       
                     </div>
                  </div>
               </div>
               
               
            </section>
         
</div>
<?php include('include/footer-2.php'); ?>
<script src="assets/dist/js/page/tejaserp-inventorybarcodeprint.js"></script>