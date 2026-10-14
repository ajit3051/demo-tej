<?php include('top.php');?>
<?php
   if(isset($_GET['delete_id']))
   {
      $id=$_GET['delete_id'];
      $sqld="DELETE FROM tbl_coupon_master WHERE id='$id'";
      $res=mysqli_query($conn,$sqld);
      if($res)
      {
       header('refresh:.5; url=fh_couponmastercreation_list.php');
      }else{
       echo "<script>alert('Coupon Master Record Failed');</script>";
      }
   }
?>
<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <title>Coupon Master List || TEJASERP</title>
      <!-- Favicon and touch icons -->
      <?php include('include/css.php');?>  
   </head>
   <body class="hold-transition sidebar-mini">
      <!-- Site wrapper -->
      <div class="wrapper">
         <?php include('include/header-top-bar.php');?>
       
         <!-- Left side column. contains the sidebar -->
         <aside class="main-sidebar">
            <?php include('include/sidebar-left.php');?>
            <?php include('include/toggle_switch_list.php');?> 
            <?php include('include/fh-form-scrolling-data-list.php');?>
         </aside>
         
         <!-- Content Wrapper. Contains page content -->
         <div class="content-wrapper">
            <div class="container-fluid">
               <div class="row">
                  <?php include('include/menu-header.php');?>
               </div>
            </div>
            
            <!-- Main content -->
            <section class="">
               <div class="row">
                  <div class="col-sm-12">
                     <div class="panel panel-bd lobidisable">
                         <div class="panel-heading">
                            <span class="fs-15 panel-header-text">
                               <i class="fa fa-th-list mr-5"></i>
                               <span>Coupon Master List !</span>
                            </span>
                         </div>
                        <div class="panel-body form-scroll">
                           <!-- Toolbar & Search Box Row under Header Panel -->
                           <div class="row m-b-15">
                              <div class="col-sm-6 col-xs-12 m-b-5">
                                 <div class="btn-group">
                                    <a href="fh_couponmastercreation.php" class="btn btn-exp btn-sm">
                                       <i class="fa fa-plus mr-5"></i>Add New Coupon Master Creation !
                                    </a>
                                 </div>
                                 <?php include('include/button-export-to-data.php');?>
                              </div>
                              <div class="col-sm-6 col-xs-12">
                                 <!-- Search Form Box under Header Panel -->
                                 <form id="couponMasterSearchForm" class="search-form-wrap pull-right" onsubmit="return false;" style="width: 100%; max-width: 360px;">
                                    <input type="hidden" name="page" id="page" value="1">
                                    <div class="input-group search-box-group">
                                       <input type="text" class="form-control search-input search-input-field" name="search" placeholder="Search Coupons..." autocomplete="off">
                                       <span class="input-group-addon search-addon-btn">
                                          <i class="fa fa-search search-icon-sm"></i>
                                       </span>
                                    </div>
                                 </form>
                                 <div class="clearfix"></div>
                              </div>
                           </div>
                           <div class="table-responsive">
                              <table class="table table-bordered table-striped table-hover">
                                 <thead>
                                    <tr class="info">
                                       <th class="th-width-40-center">#</th>
                                       <th class="th-minwidth-100-center">Action</th>
                                       <th class="text-center-align">Status</th>
                                       <th>Coupon Code</th>
                                       <th>Company Name</th>
                                       <th>Store Name</th>
                                       <th>Discount (%)</th>
                                       <th>Discount Amt</th>
                                       <th>Expiry Date</th>
                                    </tr>
                                 </thead>
                                 <tbody id="couponmaster-list-results">
                                    <!-- AJAX rows -->
                                 </tbody>
                              </table>
                           </div>

                           <div id="pagination-result"></div>
                        </div>
                     </div>
                  </div>
               </div>
            </section>
         </div>

         <div id="coupon-modals-container"></div>

         <?php include('include/footer.php');?> 
         <?php include('include/Sidenavbuttons.php');?>
      </div>
      <?php include('include/js.php');?>
      <script src="assets/dist/js/page/couponmaster-list.js"></script>
   </body>
</html>