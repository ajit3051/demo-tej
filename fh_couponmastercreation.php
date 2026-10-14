<?php
   include('top.php');
   
   if(isset($_POST['submit']))
   {
       $coupon_code      = $_POST['coupon_code'];
       $company_name     = $_POST['company_name'];
       $store_name       = $_POST['store_name'];
       $description_1    = $_POST['description_1'];
       $description_2    = $_POST['description_2'];
       $remarks          = $_POST['remarks'];
       $discount_per     = $_POST['discount_per'];
       $discount_amt     = $_POST['discount_amt'];
       $minshopping_amt  = $_POST['minshopping_amt'];
       $tc_1  = $_POST['tc_1'];
       $tc_2  = $_POST['tc_2'];
       $tc_3  = $_POST['tc_3'];
       $tc_4  = $_POST['tc_4'];
       $tc_5  = $_POST['tc_5'];
       $tc_6  = $_POST['tc_6'];
       $tc_7  = $_POST['tc_7'];
       $tc_8  = $_POST['tc_8'];
       $tc_9  = $_POST['tc_9'];
       $tc_10 = $_POST['tc_10'];
       $creation_date = $_POST['creation_date'];
       $expiry_date   = $_POST['expiry_date'];
       $status        = $_POST['status'];
   
       // ✅ DUPLICATE CHECK USING COUPON CODE
       $dup = mysqli_query($conn,"SELECT * FROM tbl_coupon_master WHERE coupon_code='$coupon_code'");
   
       if(mysqli_num_rows($dup) > 0)
       {
           echo "<script>
           Swal.fire('Error!','Coupon Code Already Exists','error');
           </script>";
       }
       else
       {
           $path = "uploads/item-master/";
           if (!is_dir($path)) {
               mkdir($path, 0777, true);
           }
   
           // Picture Upload
           $picture_upload = "";
           if(!empty($_FILES['picture_upload']['name']))
           {
               $picture_upload = time().'_'.$_FILES['picture_upload']['name'];
               move_uploaded_file($_FILES['picture_upload']['tmp_name'], $path.$picture_upload);
           }
   
           // Logo Upload
           $logo_upload = "";
           if(!empty($_FILES['logo_upload']['name']))
           {
               $logo_upload = time().'_'.$_FILES['logo_upload']['name'];
               move_uploaded_file($_FILES['logo_upload']['tmp_name'], $path.$logo_upload);
           }
   
           $query = "INSERT INTO tbl_coupon_master(
           coupon_code,company_name,store_name,description_1,description_2,
           remarks,discount_per,discount_amt,minshopping_amt,
           tc_1,tc_2,tc_3,tc_4,tc_5,tc_6,tc_7,tc_8,tc_9,tc_10,
           creation_date,expiry_date,picture_upload,logo_upload,status
           ) VALUES(
           '$coupon_code','$company_name','$store_name','$description_1','$description_2',
           '$remarks','$discount_per','$discount_amt','$minshopping_amt',
           '$tc_1','$tc_2','$tc_3','$tc_4','$tc_5','$tc_6','$tc_7','$tc_8','$tc_9','$tc_10',
           '$creation_date','$expiry_date','$picture_upload','$logo_upload','$status'
           )";
   
           $run = mysqli_query($conn,$query);   // ✅ EXECUTE QUERY
   
           if($run)
   {
       echo "
       <script>
       document.addEventListener('DOMContentLoaded', function() {
           showPopup(
               'Success!',
               'Coupon Saved Successfully 🎉',
   
               
           );
       });
       </script>
       ";
   }
       }
   }
   ?>
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
      <link href="assets/dist/css/bootstrap-datepicker.php" rel="stylesheet" type="text/css"/>
      <?php include('include/fh_modal_dialog.php');?>  
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
      <?php include('include/fh-popup-message-successfully.php');?>
      <?php include('include/fh-form-scrolling-data-list.php');?>
      <section class="">
         <div class="row">
            <!-- Form controls -->
            <div class="col-sm-12">
               <div class="panel panel-bd lobidisable">
                  <div class="panel-heading">
                      <span class="fs-15 panel-header-text">
                         <i class="fa fa-pencil mr-5"></i>
                         <span>Coupon Master Creations !</span>
                      </span>
                   </div>
                  <div class="panel-body form-scroll">
                     <div class="btn-group">
                        <a href="fh_couponmastercreation_list.php"> <button class="button-btn-btn btn-exp btn-sm"><i class="fa fa-bars" style="margin-right: 5px;"></i>Coupon Master List</button></a>
                     </div>
                     <div class="btn-group">
                        <a href="fh_couponmastercreation_view.php"> <button class="button-btn-btn btn-exp btn-sm"><i class="fa fa-bars" style="margin-right: 5px;"></i>Coupon Master View</button></a>
                     </div>
                     <form method="POST" enctype="multipart/form-data">
                        <?php echo $msg; ?>
                        <div class="row">
                           <div class="col-sm-2">
                              <div class="form-group">
                                 <label>Coupon Code</label><label style="color:red;">*</label>
                                 <input type="text" name="coupon_code" class="form-control" id="coupon_code" readonly value="<?php @$no = mysqli_query($conn, "SELECT id FROM  tbl_coupon_master ORDER BY id DESC");
                                    $po_no = mysqli_fetch_assoc($no);
                                    echo 'CC000'.($po_no['id']+1);
                                    ?>">
                              </div>
                           </div>
                           <div class="col-sm-5">
                              <div class="form-group">
                                 <label>Company Name</label><label style="color:red;">*</label>
                                 <input type="text" class="form-control" name="company_name" id="company_name" placeholder="Enter Your Company Name" required>
                              </div>
                           </div>
                           <div class="col-sm-5">
                              <div class="form-group">
                                 <label>Store Name</label><label style="color:red;">*</label>
                                 <select name="store_name" class="form-control" required>
                                    <option value="null">Select Store Name </option>
                                    <?php
                                       $query = mysqli_query($conn, "SELECT * FROM tbl_store_master");
                                       while ($row = mysqli_fetch_array($query)) {
                                       ?>
                                    <option><?php echo $row['store_name']; ?></option>
                                    <?php } ?> 
                                 </select>
                              </div>
                              <a href="#" class="" data-toggle="modal" data-target="#addtrain">
                              <span style="font-size:11px;" class="pull-left"><i class="fa fa-plus"></i>Add New Store Name</span>
                              </a>
                           </div>
                        </div>
                        <div class="row">
                           <div class="col-sm-4">
                              <div class="form-group">
                                 <label>Description-1</label>
                                 <input type="text" class="form-control" name="description_1" id="description_1" placeholder="Enter Your Discription Name" >
                              </div>
                           </div>
                           <div class="col-lg-4">
                              <div class="form-group">
                                 <label>Description-2</label>
                                 <input type="text" class="form-control" name="description_2" id="description_2" placeholder="Enter Your Description Name">
                              </div>
                           </div>
                           <div class="col-lg-4">
                              <div class="form-group">
                                 <label>Remarks</label><label style="color:red;">*</label>
                                 <input type="text" class="form-control" name="remarks" id="remarks" placeholder="Enter Your Remarks Name" required>
                              </div>
                           </div>
                        </div>
                        <div class="row">
                           <div class="col-sm-4">
                              <div class="form-group">
                                 <label>Discount %</label><label style="color:red;">*</label>
                                 <input type="text" class="form-control" name="discount_per" id="discount_per" placeholder="Enter Your Discount %" required>
                              </div>
                           </div>
                           <div class="col-lg-4">
                              <div class="form-group">
                                 <label>Discount Amount</label><label style="color:red;">*</label>
                                 <input type="text" class="form-control" name="discount_amt" id="discount_amt" placeholder="Enter Your Discount Amount" required>
                              </div>
                           </div>
                           <div class="col-lg-4">
                              <div class="form-group">
                                 <label>Minimum Shopping Amount</label><label style="color:red;">*</label>
                                 <input type="text" class="form-control" name="minshopping_amt" id="minshopping_amt" placeholder="Enter Your Minimum Shopping Amount" required>
                              </div>
                           </div>
                        </div>
                        <label>Terms & Conditions ||</label>
                        <div class="row">
                           <div class="col-lg-6">
                              <div class="form-group">
                                 <label>TC-1</label>
                                 <input type="text" class="form-control" name="tc_1" id="tc_1" placeholder="Enter Your Terms & Conditions" >
                              </div>
                           </div>
                           <div class="col-lg-6">
                              <div class="form-group">
                                 <label>TC-2</label>
                                 <input type="text" class="form-control" name="tc_2" id="tc_2" placeholder="Enter Your Terms & Conditions" >
                              </div>
                           </div>
                        </div>
                        <div class="row">
                           <div class="col-lg-6">
                              <div class="form-group">
                                 <label>TC-3</label>
                                 <input type="text" class="form-control" name="tc_3" id="tc_3" placeholder="Enter Your Terms & Conditions" >
                              </div>
                           </div>
                           <div class="col-lg-6">
                              <div class="form-group">
                                 <label>TC-4</label>
                                 <input type="text" class="form-control" name="tc_4" id="tc_4" placeholder="Enter Your Terms & Conditions" >
                              </div>
                           </div>
                        </div>
                        <div class="row">
                           <div class="col-lg-6">
                              <div class="form-group">
                                 <label>TC-5</label>
                                 <input type="text" class="form-control" name="tc_5" id="tc_5" placeholder="Enter Your Terms & Conditions" >
                              </div>
                           </div>
                           <div class="col-lg-6">
                              <div class="form-group">
                                 <label>TC-6</label>
                                 <input type="text" class="form-control" name="tc_6" id="tc_6" placeholder="Enter Your Terms & Conditions" >
                              </div>
                           </div>
                        </div>
                        <div class="row">
                           <div class="col-lg-6">
                              <div class="form-group">
                                 <label>TC-7</label>
                                 <input type="text" class="form-control" name="tc_7" id="tc_7" placeholder="Enter Your Terms & Conditions" >
                              </div>
                           </div>
                           <div class="col-lg-6">
                              <div class="form-group">
                                 <label>TC-8</label>
                                 <input type="text" class="form-control" name="tc_8" id="tc_8" placeholder="Enter Your Terms & Conditions" >
                              </div>
                           </div>
                        </div>
                        <div class="row">
                           <div class="col-lg-6">
                              <div class="form-group">
                                 <label>TC-9</label>
                                 <input type="text" class="form-control" name="tc_9" id="tc_9" placeholder="Enter Your Terms & Conditions" >
                              </div>
                           </div>
                           <div class="col-lg-6">
                              <div class="form-group">
                                 <label>TC-10</label>
                                 <input type="text" class="form-control" name="tc_10" id="tc_10" placeholder="Enter Your Terms & Conditions" >
                              </div>
                           </div>
                        </div>
                        <div class="row">
                           <div class="col-sm-2">
                              <label>Creation Date</label><label style="color:red;">*</label>
                              <div class=" input-group date form_date">
                                 <input type="text" id='minMaxExample' name="creation_date" class="form-control years" value="<?php echo date('d/m/Y'); ?>"><span class="input-group-addon"><a href="#"><i class="fa fa-calendar"></i></a></span>
                              </div>
                           </div>
                           <div class="col-sm-2">
                              <label>Expiry Date</label><label style="color:red;">*</label>
                              <div class=" input-group date form_date">
                                 <input type="text" id='minMaxExample2' name="expiry_date" class="form-control years" value="<?php echo date('d/m/Y'); ?>"><span class="input-group-addon"><a href="#"><i class="fa fa-calendar"></i></a></span>
                              </div>
                           </div>
                           <div class="col-sm-3">
                              <div class="form-group">
                                 <label>Picture Upload</label>
                                 <input type="file" name="picture_upload" id="picture_upload">
                              </div>
                           </div>
                           <div class="col-sm-3">
                              <div class="form-group">
                                 <label>Logo Upload</label>
                                 <input type="file" name="logo_upload" id="logo_upload">
                              </div>
                           </div>
                           <div class="col-sm-2">
                              <div class="form-check">
                                 <label>Status</label><label style="color:red;">*</label><br>
                                 <label class="radio-inline">
                                 <input type="radio" name="status" value="true" checked="checked">Active</label>
                                 <label class="radio-inline"><input type="radio" name="status" value="false" >Inctive</label>
                              </div>
                           </div>
                        </div>
                        <br>
                        <div class="container-fluid">
                           <div class="row">
                              <button type="submit" name="submit" class="btn btn-warning" style="width:100px;">Reset</button>
                              <button type="submit" name="submit" class="btn btn-success" style="width:100px;background-color: #009688; color: white;">Save</button>
                           </div>
                        </div>
                     </form>
                  </div>
               </div>
            </div>
         </div>
      </section>
      <!-- Main content -->
      <section class="">
         <div class="row">
            <!-- Form controls -->
            <div class="col-sm-12">
               <div class="panel panel-bd lobidisable">
                  <div class="panel-heading">
                     <span class="fs-15 panel-header-text">
                        <i class="fa fa-pencil mr-5"></i>
                        <span>Coupon Master Creations !</span>
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
      <!-- /.content -->
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
      <section class="container-fluid">
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
         <div class="panel-body form-scroll">
            <div class="btn-group">
               <a href="fh_couponmastercreation.php"> <button class="button-btn-btn btn-exp btn-sm"><i class="fa fa-bars" style="margin-right: 5px;"></i>Add New Coupon Master Creation</button></a>
            </div>
            <div class="btn-group">
               <a href="fh_couponmastercreation_list.php"> <button class="button-btn-btn btn-exp btn-sm"><i class="fa fa-bars" style="margin-right: 5px;"></i>Coupon Master List</button></a>
            </div>
            <div class="btn-group">
               <a href="fh_couponmastercreation_view.php"> <button class="button-btn-btn btn-exp btn-sm"><i class="fa fa-bars" style="margin-right: 5px;"></i>Coupon Master List View</button></a>
            </div>
            <div class="container-fluid">
               <div class="row">
                  <input type="text" class="form-control-control" name="company_name" id="company_name" placeholder="Search" required>
               </div>
            </div>
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
                              <img src="uploads/item-master/<?php echo $user['logo_upload']; ?>" class="" alt="User Image" width="310" height="50">
                           </div>
                           <img src="uploads/item-master/<?php echo $user['picture_upload']; ?>" class="" alt="shirt" width="100%" height="300">
                           <div class="" style=" margin-left: 10px; background-color:white" >
                              <h2><b style="color:#009688"><?php echo substr($user['company_name'],0,50); ?></b></h2>
                              <p><b><?php echo substr($user['store_name'],0,50); ?></b></p3> 
                              <h3 style="color:#F5B027"><?php echo substr($user['description_1'],0,50); ?></h3>
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
                              <p style="margin-left: 10px;"><span style="color:red; font-size: 15px;">Expires : 
                                 <?php echo substr($user['expiry_date'],0,50); ?></span>
                              </p>
                           </div>
                        </div>
                     </div>
                     <div class="col-lg-4">
                        <div class="coupon">
                           <div class="promo">
                              <img src="uploads/item-master/<?php echo $user['logo_upload']; ?>" class="" alt="User Image" width="310" height="50">
                           </div>
                           <img src="uploads/item-master/<?php echo $user['picture_upload']; ?>" class="" alt="shirt" width="100%" height="300">
                           <div class="" style=" margin-left: 10px; background-color:white" >
                              <h2><b style="color:#009688"><?php echo substr($user['company_name'],0,50); ?></b></h2>
                              <p><b><?php echo substr($user['store_name'],0,50); ?></b></p3> 
                              <h3 style="color:#F5B027"><?php echo substr($user['description_1'],0,50); ?></h3>
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
                              <p style="margin-left: 10px;"><span style="color:red; font-size: 15px;">Expires : 
                                 <?php echo substr($user['expiry_date'],0,50); ?></span>
                              </p>
                           </div>
                        </div>
                     </div>
                     <div class="col-lg-4">
                        <div class="coupon">
                           <div class="promo">
                              <img src="uploads/item-master/<?php echo $user['logo_upload']; ?>" class="" alt="User Image" width="310" height="50">
                           </div>
                           <img src="uploads/item-master/<?php echo $user['picture_upload']; ?>" class="" alt="shirt" width="100%" height="300">
                           <div class="" style=" margin-left: 10px; background-color:white" >
                              <h2><b style="color:#009688"><?php echo substr($user['company_name'],0,50); ?></b></h2>
                              <p><b><?php echo substr($user['store_name'],0,50); ?></b></p3> 
                              <h3 style="color:#F5B027"><?php echo substr($user['description_1'],0,50); ?></h3>
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
                              <p style="margin-left: 10px;"><span style="color:red; font-size: 15px;">Expires : 
                                 <?php echo substr($user['expiry_date'],0,50); ?></span>
                              </p>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
      </section>
      <!-- /.content -->
      </div>
      <!-- /.content-wrapper -->
      <!-- Container for View Modals -->
      <div id="coupon-modals-container"></div>

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
      <!-- Coupon Master AJAX Search & Pagination JS -->
      <script src="assets/dist/js/page/couponmaster-list.js"></script>
      <!-- End Theme label Script
         =====================================================================-->
   </body>
   <!-- Mirrored from thememinister.com/crm/add-customer.html by HTTrack Website Copier/3.x [XR&CO'2014], Fri, 05 Aug 2022 06:10:42 GMT -->
</html>