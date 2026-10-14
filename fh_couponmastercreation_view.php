<?php include('top.php');?>
<!DOCTYPE html>
<html lang="en">
   <!-- Mirrored from thememinister.com/crm/add-customer.html by HTTrack Website Copier/3.x [XR&CO'2014], Fri, 05 Aug 2022 06:10:42 GMT -->
   <head>
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <title>Coupon Master Creation || TEJASERP</title>
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
       
      <!-- =============================================== -->
      <!-- Left side column. contains the sidebar -->
      <aside class="main-sidebar">
         <!-- sidebar -->
         <?php include('include/sidebar-left.php');?>
         <?php include('include/toggle_switch_list.php');?> 
         <!-- /.sidebar -->
      </aside>
      <!-- =============================================== -->
      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      
      <div class="container-fluid">
         <div class="row">
            <?php include('include/menu-header.php');?>
            
         </div>
      </div>
      <!-- Main content -->
      <style>
         body {
         font-family: Arial;
         }
         .coupon {
         border: 5px dotted #bbb;
         width: 200%;
         border-radius: 15px;
         margin: 0 auto;
         max-width:325px;
         }
         .container {
         background: #f1f1f1;
         padding: 2px;
         }
         .promo {
         background: #f1f1f1;
         padding: 3px;
         }
         .expire {
         color: red;
         }
      </style>
      <!-- Main content -->
      <section class="">
         <div class="row">
         <!-- Form controls -->
         <div class="col-sm-12">
         <div class="panel panel-bd lobidisable">
          <div class="panel-heading">
             <span class="fs-15 panel-header-text">
                <i class="fa fa-th-list mr-5"></i>
                <span>Coupon List View !</span>
             </span>
          </div>
         <div class="panel-body">
            <div class="btn-group">
                              <a href="fh_couponmastercreation.php"> <button class="button-btn-btn btn-exp btn-sm"><i class="fa fa-bars" style="margin-right: 5px;"></i>Add New Coupon Master Creation</button></a>
                           </div>
                           <div class="btn-group">
                              <a href="fh_couponmastercreation_list.php"> <button class="button-btn-btn btn-exp btn-sm"><i class="fa fa-bars" style="margin-right: 5px;"></i>Coupon Master List</button></a>
                           </div>
                           <div class="btn-group">
                              <a href="fh_couponmastercreation_view.php"> <button class="button-btn-btn btn-exp btn-sm"><i class="fa fa-bars" style="margin-right: 5px;"></i>Coupon Master List View</button></a>
                           </div>
            <?php include('include/searchbox.php');?>
            <div class="table-responsive ">
               <?php
                  $query=mysqli_query($conn,"select * from tbl_coupon_master"); 
                  $rowcount=mysqli_num_rows($query);
                  for($i=1; $i<=$rowcount; $i++)
                  {
                  $user=mysqli_fetch_array($query);
                  ?>
               <?php } ?>
               <div class="panel-body">
                  <div class="row">
                     <div class="col-lg-4">
                        <div class="coupon">
                           <div class="promo">
                              <h3 style="margin-left: 10px;">Company Logo</h3>
                           </div>
                           <img src="images/shirts.jpg" alt="shirt" style="width:100%;">
                           <div class="" style=" margin-left: 10px; background-color:white" >
                              <h2><b><?php echo substr($user['company_name'],0,50); ?></b></h2>
                              <p><b><?php echo substr($user['store_name'],0,50); ?></b></p3> 
                              <p><?php echo substr($user['description_1'],0,50); ?></p>
                              <p><b>Details:</b></p>
                              <p><?php echo substr($user['tc_1'],0,50); ?></p>
                              <p><?php echo substr($user['tc_2'],0,50); ?></p>
                              <p><?php echo substr($user['tc_3'],0,50); ?></p>
                           </div>
                           <div class="promo">
                              <p style="margin-left: 10px;">Use Promo Code:
                                 <span class="promo">
                                 <b><?php echo substr($user['coupon_code'],0,50); ?></b>
                              </p>
                              </span>
                              </p>
                              <p class="expire" style="margin-left: 10px;">
                              <p style="margin-left: 10px;">Expiry Date: 
                                 <?php echo substr($user['expiry_date'],0,50); ?>
                              </p>
                           </div>
                        </div>
                     </div>
                     <div class="col-lg-4">
                        <div class="coupon">
                           <div class="promo">
                              <h3 style="margin-left: 10px;">Company Logo</h3>
                           </div>
                           <img src="images/shirts.jpg" alt="shirt" style="width:100%;">
                           <div class="" style=" margin-left: 10px; background-color:white" >
                              <h2><b><?php echo substr($user['discount_per'],0,50); ?>%</b></h2>
                              <p><?php echo substr($user['description_1'],0,50); ?></p>
                           </div>
                           <div class="promo">
                              <p style="margin-left: 10px;">Use Promo Code: 
                                 <span class="promo">BOH232</span>
                              </p>
                              <p class="expire" style="margin-left: 10px;">Expires: Jan 03, 2021</p>
                           </div>
                        </div>
                     </div>
                     <div class="col-lg-4">
                        <div class="coupon">
                           <div class="promo">
                              <h3 style="margin-left: 10px;">Company Logo</h3>
                           </div>
                           <img src="images/shirts.jpg" alt="shirt" style="width:100%;">
                           <div class="" style=" margin-left: 10px; background-color:white" >
                              <h2><b><?php echo substr($user['discount_per'],0,50); ?>%</b></h2>
                              <p><?php echo substr($user['description_1'],0,50); ?></p>
                           </div>
                           <div class="promo">
                              <p style="margin-left: 10px;">Use Promo Code: 
                                 <span class="promo">BOH232</span>
                              </p>
                              <p class="expire" style="margin-left: 10px;">Expires: Jan 03, 2021</p>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
      </section>
      <!-- /.content --><!-- /.content -->
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