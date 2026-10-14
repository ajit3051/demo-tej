<?php include('top.php');?>
<?php
   ?>
<!DOCTYPE html>
<html lang="en">
   <!-- Mirrored from thememinister.com/crm/add-customer.html by HTTrack Website Copier/3.x [XR&CO'2014], Fri, 05 Aug 2022 06:10:42 GMT -->
   <head>
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <title>Item Master List || TEJASERP</title>
      <!-- Favicon and touch icons -->
      <?php include('include/css.php');?>  
      <!-- End Theme Layout Style
         =====================================================================-->
   </head>
   <body class="hold-transition sidebar-mini">
      <!--preloader-->
      <!-- <div id="preloader">
         <div id="status"></div>
         </div> -->
      <!-- Site wrapper -->
      <div class="wrapper">
         <?php include('include/header-top-bar.php');?>
         <
         <!-- =============================================== -->
         <!-- Left side column. contains the sidebar -->
         <aside class="main-sidebar">
            <!-- sidebar -->
            <?php include('include/sidebar-left.php');?>
            <?php include('include/toggle_switch_list.php');?>
            <?php include('include/fh-form-scrolling-data-list.php');?> 
            <!-- /.sidebar -->
         </aside>
         <!-- =============================================== -->
         <!-- Content Wrapper. Contains page content -->
         <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            
            <!-- Main content -->
            <!-- Main content -->
            

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
                                 <span>Bill Wise Inventory Barcode Re-Print !</span>
                              </span>
                           </div>
                        </div>
                        <div class="panel-body form-scroll">
                           <div class="row">
                              <div class="col-md-4">
                                 <div class="btn-group">
                                    <a href="fh_itemmastercreation.php"> <button class="button-btn-btn btn-exp btn-sm"><i class="fa fa-plus"></i>Add New Purchase Creation</button></a>
                                 </div>
                                 <?php include('include/button-export-to-data.php');?>
                              </div>
                                 <div class="col-sm-2" style="margin-top:-20px;">
                              <small>Date From</small>
                              <div class=" input-group date form_date">
                                 <input type="text" id='minMaxExample' name="creation_date" class="form-control years" value="<?php echo date('d/m/Y'); ?>"><span class="input-group-addon"><a href="#"><i class="fa fa-calendar"></i></a></span>
                              </div>
                           </div>
                           <div class="col-sm-2" style="margin-top:-20px;">
                              <small>Date To</small>
                              <div class=" input-group date form_date">
                                 <input type="text" id='minMaxExample2' name="expiry_date" class="form-control years" value="<?php echo date('d/m/Y'); ?>"><span class="input-group-addon"><a href="#"><i class="fa fa-calendar"></i></a></span>
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
                                 <input type="text" class="form-control-control" name="company_name" id="company_name" placeholder="Data Search" required>
                              </div>
                           </div>
                           <div class="table-responsive">
                              <table id="dataTableExample1" class="table table-bordered table-striped table-hover">
                                 <thead>
                                    <tr class="info">
                                       <th>SN</th>
                                       <th>Status</th>
                                       <th>Date & Time</th>
                                       <th>Bill No.</th>
                                       
                                       <th>Store Code</th>
                                       <th>Store Name</th>
                                       <th>Location</th>
                                       <th>
                                          
                                          Qty.
                                       </th>
                                       <th>
                                          Re-Print
                                       </th>
                                       
                                    </tr>
                                 </thead>
                                 <tbody>
                                    <?php
                                       $query=mysqli_query($conn,"select * from tbl_item_master"); 
                                       $rowcount=mysqli_num_rows($query);
                                       for($i=1; $i<=$rowcount; $i++)
                                       {
                                         $user=mysqli_fetch_array($query);
                                       ?>
                                    <tr>
                                       <td>
                                          
                                             
                                             <label><?php echo $i; ?></label>
                                          
                                       </td>
                                       <td>
                                          <label class="">
                                          Pending
                                          </label>
                                       </td>
                                       <td>
                                          <?php echo $user['item_code']; ?>
                                       </td>
                                       <td>
                                          <?php echo $user['item_code']; ?>
                                       </td>
                                       <td><?php echo substr($user['barcode_no'],0,50); ?></td>
                                       <td><?php echo substr($user['barcode_no'],0,50); ?></td>
                                       <td><?php echo substr($user['sku_no'],0,50); ?></td>
                                       <td><?php echo substr($user['item_name'],0,50); ?></td>
                                       <td>
                                          <a class=""onClick="return confirm('Are you sure you want to Update Store profile Master?')" href="fh_storeprofilecreation_update.php?update_id=<?php echo $user['id']; ?>">
                                          <span
                                             data-toggle="tooltip" title="Are you sure you want to Update Store Profile" type="button" class="" ><span style="">Re-Print</span>
                                          </span></a>
                                          
                                       </td>
                                       
                                    </tr>
                                    <?php } ?>
                                 </tbody>
                              </table>
                           </div>
                        </div>
                       
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
                                 <span>Item Wise Inventory Barcode Re-Print !</span>
                              </span>
                           </div>
                        </div>
                        <div class="panel-body form-scroll">
                           <div class="row">
                              <div class="col-md-4">
                                 <div class="btn-group">
                                    <a href="fh_itemmastercreation.php"> <button class="button-btn-btn btn-exp btn-sm"><i class="fa fa-plus"></i>Add New Purchase Creation</button></a>
                                 </div>
                                 <?php include('include/button-export-to-data.php');?>
                              </div>
                                 <div class="col-sm-2" style="margin-top:-20px;">
                              <small>Date From</small>
                              <div class=" input-group date form_date">
                                 <input type="text" id='minMaxExample' name="creation_date" class="form-control years" value="<?php echo date('d/m/Y'); ?>"><span class="input-group-addon"><a href="#"><i class="fa fa-calendar"></i></a></span>
                              </div>
                           </div>
                           <div class="col-sm-2" style="margin-top:-20px;">
                              <small>Date To</small>
                              <div class=" input-group date form_date">
                                 <input type="text" id='minMaxExample2' name="expiry_date" class="form-control years" value="<?php echo date('d/m/Y'); ?>"><span class="input-group-addon"><a href="#"><i class="fa fa-calendar"></i></a></span>
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
                                 <input type="text" class="form-control-control" name="company_name" id="company_name" placeholder="Data Search" required>
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
                                       <th>Preview</th>
                                       <th>Store Code</th>
                                       <th>Store Name</th>
                                       <th>Location</th>
                                       <th>
                                          <input id="checkbox1" type="checkbox">
                                          Barcode No
                                       </th>
                                       <th><input id="checkbox1" type="checkbox">
                                          Article No
                                       </th>
                                       <th><input id="checkbox1" type="checkbox">
                                          Item Name
                                       </th>
                                       <th><input id="checkbox1" type="checkbox">
                                          Qty.
                                       </th>
                                       <th><input id="checkbox1" type="checkbox">
                                          Group
                                       </th>
                                       <th><input id="checkbox1" type="checkbox">
                                          Brand
                                       </th>
                                       <th><input id="checkbox1" type="checkbox">Color</th>
                                       <th><input id="checkbox1" type="checkbox">Size</th>
                                       <th><input id="checkbox1" type="checkbox">Style</th>
                                       <th><input id="checkbox1" type="checkbox">MRP</th>
                                       <th><input id="checkbox1" type="checkbox">Dis.(%)</th>
                                       <th><input id="checkbox1" type="checkbox">SP</th>
                                       <th>Re-Print</th>
                                    </tr>
                                 </thead>
                                 <tbody>
                                    <?php
                                       $query=mysqli_query($conn,"select * from tbl_item_master"); 
                                       $rowcount=mysqli_num_rows($query);
                                       for($i=1; $i<=$rowcount; $i++)
                                       {
                                         $user=mysqli_fetch_array($query);
                                       ?>
                                    <tr>
                                       <td>
                                          <div class="checkbox checkbox-info">
                                             <input id="checkbox1" type="checkbox">
                                             <label for="checkbox1"><?php echo $i; ?></label>
                                          </div>
                                       </td>
                                       <td>
                                          <label class="">
                                          Pending
                                          </label>
                                       </td>
                                       <td>
                                          <?php echo $user['item_code']; ?>
                                       </td>
                                       <td>
                                          <?php echo $user['item_code']; ?>
                                       </td>
                                       <td><?php echo substr($user['barcode_no'],0,50); ?></td>
                                       <td><?php echo substr($user['barcode_no'],0,50); ?></td>
                                       <td><?php echo substr($user['sku_no'],0,50); ?></td>
                                       <td><?php echo substr($user['item_name'],0,50); ?></td>
                                       <td>
                                          <div class="checkbox checkbox-info">
                                             <input id="checkbox1" type="checkbox">
                                             <label for="checkbox1"><?php echo substr($user['description'],0,50); ?></label>
                                          </div>
                                       </td>
                                       <td>
                                          <div class="checkbox checkbox-info">
                                             <input id="checkbox1" type="checkbox">
                                             <label for="checkbox1"><?php echo substr($user['description'],0,50); ?></label>
                                          </div>
                                       </td>
                                       <td>
                                          <div class="checkbox checkbox-info">
                                             <input id="checkbox1" type="checkbox">
                                             <label for="checkbox1"><?php echo substr($user['description'],0,50); ?></label>
                                          </div>
                                       </td>
                                       <td>
                                          <div class="checkbox checkbox-info">
                                             <input id="checkbox1" type="checkbox">
                                             <label for="checkbox1"><?php echo substr($user['description'],0,50); ?></label>
                                          </div>
                                       </td>
                                       <td>
                                          <div class="checkbox checkbox-info">
                                             <input id="checkbox1" type="checkbox">
                                             <label for="checkbox1"><?php echo substr($user['description'],0,50); ?></label>
                                          </div>
                                       </td>
                                       <td>
                                          <div class="checkbox checkbox-info">
                                             <input id="checkbox1" type="checkbox">
                                             <label for="checkbox1"><?php echo substr($user['description'],0,50); ?></label>
                                          </div>
                                       </td>
                                       <td>
                                          <div class="checkbox checkbox-info">
                                             <input id="checkbox1" type="checkbox">
                                             <label for="checkbox1"><?php echo substr($user['description'],0,50); ?></label>
                                          </div>
                                       </td>
                                       <td>
                                          <div class="checkbox checkbox-info">
                                             <input id="checkbox1" type="checkbox">
                                             <label for="checkbox1"><?php echo substr($user['description'],0,50); ?></label>
                                          </div>
                                       </td>
                                       <td>
                                          <div class="checkbox checkbox-info">
                                             <input id="checkbox1" type="checkbox">
                                             <label for="checkbox1"><?php echo substr($user['description'],0,50); ?></label>
                                          </div>
                                       </td>
                                       <td>
                                          <div class="checkbox checkbox-info">
                                             <input id="checkbox1" type="checkbox">
                                             <label for="checkbox1"><?php echo substr($user['description'],0,50); ?></label>
                                          </div>
                                       </td>
                                       <td>
                                          <div class="checkbox checkbox-info">
                                             <input id="checkbox1" type="checkbox">
                                             <label for="checkbox1"><?php echo substr($user['description'],0,50); ?></label>
                                          </div>
                                       </td>
                                       <td>
                                          <div class="checkbox checkbox-info">
                                             <input id="checkbox1" type="checkbox">
                                             <label for="checkbox1"><?php echo substr($user['description'],0,50); ?></label>
                                          </div>
                                       </td>
                                        <td>
                                          <a class=""onClick="return confirm('Are you sure you want to Update Store profile Master?')" href="fh_storeprofilecreation_update.php?update_id=<?php echo $user['id']; ?>">
                                          <span
                                             data-toggle="tooltip" title="Are you sure you want to Update Store Profile" type="button" class="" ><span style="">Re-Print</span>
                                          </span></a>
                                          
                                       </td>
                                    </tr>
                                    <?php } ?>
                                 </tbody>
                              </table>
                           </div>
                        </div>
                       
                     </div>
                  </div>
               </div>
               
               
            </section>
            <!-- /.content -->
         </div>
         <!-- /.content-wrapper -->
         <!-- /.footer -->
         <?php include('include/footer.php');?> 
         <!-- /.footer -->
         <?php include('include/Sidenavbuttons.php');?> 
      </div>
      <!-- ./wrapper -->
      <!-- Start Core Plugins
         =====================================================================-->
      <!-- jQuery -->
      <?php include('include/js.php');?>
      <!-- End Theme label Script
         =====================================================================-->
   </body>
   <!-- Mirrored from thememinister.com/crm/add-customer.html by HTTrack Website Copier/3.x [XR&CO'2014], Fri, 05 Aug 2022 06:10:42 GMT -->
</html>