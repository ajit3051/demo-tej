<?php include('top.php');?>
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
   $msg = "";
   
   if (isset($_POST['submit'])) {
   
       // TEXT DATA
       $item_code      = $_POST['item_code'];
       $barcode_no     = $_POST['barcode_no'];
       $sku_no         = $_POST['sku_no'];
       $item_name      = $_POST['item_name'];
       $description    = $_POST['description'];
       $group_name     = $_POST['group_name'];
       $subgroup_name  = $_POST['subgroup_name'];
       $brand_name     = $_POST['brand_name'];
       $color_name     = $_POST['color_name'];
       $style_name     = $_POST['style_name'];
       $size_name      = $_POST['size_name'];
       $mou_name       = $_POST['mou_name'];
       $rac_no         = $_POST['rac_no'];
       $purchase_price = $_POST['purchase_price'];
       $dis_per        = $_POST['dis_per'];
       $dis_amt        = $_POST['dis_amt'];
       $mrp            = $_POST['mrp'];
       $sp             = $_POST['sp'];
       $profit_amt     = $_POST['profit_amt'];
       $min_qty        = $_POST['min_qty'];
       $reorder_qty    = $_POST['reorder_qty'];
       $status         = $_POST['status'];
       $date           = $_POST['date'];
       $about_1        = $_POST['about_1'];
       $about_2        = $_POST['about_2'];
       $about_3        = $_POST['about_3'];
       $about_4        = $_POST['about_4'];
       
   
   
       // DUPLICATE CHECK
       $check = mysqli_query($conn, "SELECT id FROM tbl_item_master WHERE item_name='$item_name'");
       if (mysqli_num_rows($check) > 0) {
           $msg = "<div class='alert alert-danger'>Item Name Already Exists!</div>";
       } else {
   
           // FILE UPLOAD PATH
           $path = "uploads/item-master/";
           if (!is_dir($path)) {
               mkdir($path, 0777, true);
           }
   
           // FILE UPLOAD FUNCTION
           function uploadFile($file, $path) {
               if (!empty($file['name'])) {
                   $filename = time() . "_" . basename($file['name']);
                   move_uploaded_file($file['tmp_name'], $path . $filename);
                   return $filename;
               }
               return "";
           }
   
           @$picture  = uploadFile($_FILES['picture'], $path);
           @$picture1 = uploadFile($_FILES['picture1'], $path);
           @$picture2 = uploadFile($_FILES['picture2'], $path);
           @$picture3 = uploadFile($_FILES['picture3'], $path);
   
           // INSERT QUERY (MATCHING TABLE EXACTLY)
           $sql = "INSERT INTO tbl_item_master (
                       item_code, barcode_no, sku_no, item_name, description,
                       group_name, subgroup_name, brand_name, color_name, style_name, size_name, mou_name,
                       rac_no, purchase_price, dis_per, dis_amt, mrp, sp, profit_amt,
                       min_qty, reorder_qty,
                       picture, picture1, picture2, picture3,
                       status, date, about_1, about_2, about_3, about_4
                   ) VALUES (
                       '$item_code', '$barcode_no', '$sku_no', '$item_name', '$description',
                       '$group_name', '$subgroup_name',
                       '$brand_name', '$color_name', '$style_name', '$size_name', '$mou_name',
                       '$rac_no', '$purchase_price', '$dis_per','$dis_amt','$mrp', '$sp', '$profit_amt',
                       '$min_qty', '$reorder_qty',
                       '$picture', '$picture1', '$picture2', '$picture3',
                       '$status', '$date', '$about_1', '$about_2',
                       '$about_3', '$about_4'
                   )";
   
           $run = mysqli_query($conn,$sql);   // ✅ EXECUTE QUERY

        if($run)
{
    echo "
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        showPopup(
            'Success!',
            'Item Master Saved Successfully 🎉',
            
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
   <head>
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <title>Item Master Creations || TEJASERP</title>
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
            <?php include('include/fh-popup-message-successfully.php');?>
            <?php include('include/fh-form-scrolling-data-list.php');?>
            <section class="">
               <div class="row">
                  <!-- Form controls -->
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
                                 <span>Item Master Creations !</span>
                              </span>
                           </div>
                        </div>
                        <div class="panel-body form-scroll">
                           <div class="btn-group">
                              <a href="fh_itemmastercreation_list.php"> <button class="button-btn-btn btn-exp btn-sm"><i class="fa fa-bars" style="margin-right: 5px;"></i>Item Master List</button></a>
                           </div>
                           <form method="post" enctype="multipart/form-data">
                              <?php echo $msg; ?>
                              <div class="row">
                                 <div class="col-sm-3">
                                    <div class="form-group">
                                       <label>Date</label><label style="color:red;">*</label>
                                        
                                       <input id='minMaxExample' type="text" name="date" id="date" class="form-control" required>
                                    </div>
                                 </div>
                                 <div class="col-sm-3">
                                    <div class="form-group">
                                       <label>ItemCode</label><label style="color:red;">*</label>
                                       <input type="password" name="item_code" class="form-control" id="item_code" readonly value="<?php @$no = mysqli_query($conn, "SELECT id FROM  tbl_item_master ORDER BY id DESC");
                                          $po_no = mysqli_fetch_assoc($no);
                                          echo 'IC-000'.($po_no['id']+1);
                                          ?>">
                                    </div>
                                 </div>
                                 <div class="col-sm-3">
                                    <div class="form-group">
                                       <label>Barcode No.</label><label style="color:red;">*</label>
                                       <input type="text" class="form-control" name="barcode_no" id="barcode_no" placeholder="Enter Your Barcode No" required>
                                    </div>
                                 </div>
                                 <div class="col-sm-3">
                                    <div class="form-group">
                                       <label>Article No. || SKU No.</label><label style="color:red;">*</label>
                                       <input type="text" class="form-control" name="sku_no" id="sku_no" placeholder="Enter Your Article || SKU No" required>
                                    </div>
                                 </div>
                              </div>
                              <div class="row">
                                 <div class="col-sm-6">
                                    <div class="form-group">
                                       <label>Item Name</label><label style="color:red;">*</label>
                                       <input type="text" class="form-control" name="item_name" id="item_name" placeholder="Enter Your Item Name" required>
                                    </div>
                                 </div>
                                 <div class="col-sm-6">
                                    <div class="form-group">
                                       <label>Description</label>
                                       <input type="text" class="form-control" name="description" id="description" placeholder="Enter Your Description" >
                                    </div>
                                 </div>
                              </div>
                             
                              <div class="row">
                                 <div class="col-lg-3">
                                    <div class="form-group">
                                       <label>Group || Category Name</label><label style="color:red;">*</label>
                                       <select name="group_name" class="form-control" required>
                                          <option value="null">Select Group || Category Nmae </option>
                                          <?php
                                             $query = mysqli_query($conn, "SELECT * FROM tbl_group_master");
                                             while ($row = mysqli_fetch_array($query)) {
                                             ?>
                                          <option><?php echo $row['group_name']; ?></option>
                                          <?php } ?> 
                                       </select>
                                    </div>
                                    <a href="#" class="" data-toggle="modal" data-target="#addtrain">
                                    <span style="font-size:11px;" class="pull-left"><i class="fa fa-plus"></i>Add New Group Master</span>
                                    </a>
                                 </div>
                                 <div class="col-lg-3">
                                    <div class="form-group">
                                       <label>SubGroup || SubCategory Name</label><label style="color:red;">*</label>
                                       <select name="subgroup_name" class="form-control" required>
                                          <option value="null">Select SubGroup || SubCategory Name</option>
                                          <?php
                                             $query = mysqli_query($conn, "SELECT * FROM tbl_subgroup_master");
                                             while ($row = mysqli_fetch_array($query)) {
                                             ?>
                                          <option><?php echo $row['subgroup_name']; ?></option>
                                          <?php } ?> 
                                       </select>
                                    </div>
                                    <a href="#" class="" data-toggle="modal" data-target="#addtrain">
                                    <span style="font-size:11px;" class="pull-left"><i class="fa fa-plus"></i>Add New SubGroup Master</span>
                                    </a>
                                 </div>
                                 <div class="col-lg-3">
                                    <div class="form-group">
                                       <label>Brand Name</label><label style="color:red;">*</label>
                                       <select name="brand_name" class="form-control" required>
                                          <option value="null">Select Brand Name</option>
                                          <?php
                                             $query = mysqli_query($conn, "SELECT * FROM tbl_brand_master");
                                             while ($row = mysqli_fetch_array($query)) {
                                             ?>
                                          <option><?php echo $row['brand_name']; ?></option>
                                          <?php } ?> 
                                       </select>
                                    </div>
                                    <a href="#" class="" data-toggle="modal" data-target="#addtrain">
                                    <span style="font-size:11px;" class="pull-left"><i class="fa fa-plus"></i>Add New Brand Master</span>
                                    </a>
                                 </div>
                                 <div class="col-lg-3">
                                    <div class="form-group">
                                       <label>Color Name</label><label style="color:red;">*</label>
                                       <select name="color_name" class="form-control" required>
                                          <option value="null">Select Color Name</option>
                                          <?php
                                             $query = mysqli_query($conn, "SELECT * FROM tbl_color_master");
                                             while ($row = mysqli_fetch_array($query)) {
                                             ?>
                                          <option><?php echo $row['color_name']; ?></option>
                                          <?php } ?> 
                                       </select>
                                    </div>
                                    <a href="#" class="" data-toggle="modal" data-target="#addtrain">
                                    <span style="font-size:11px;" class="pull-left"><i class="fa fa-plus"></i>Add New Color Master</span>
                                    </a>
                                 </div>
                              </div>
                              <div class="row">
                                 <div class="col-lg-3">
                                    <div class="form-group">
                                       <label>Style || Design Name</label><label style="color:red;">*</label>
                                       <select name="style_name" class="form-control" required>
                                          <option value="null">Select Style || Design Name</option>
                                          <?php
                                             $query = mysqli_query($conn, "SELECT * FROM tbl_styledesign_master");
                                             while ($row = mysqli_fetch_array($query)) {
                                             ?>
                                          <option><?php echo $row['style_name']; ?></option>
                                          <?php } ?> 
                                       </select>
                                    </div>
                                    <a href="#" class="" data-toggle="modal" data-target="#addtrain">
                                    <span style="font-size:11px;" class="pull-left"><i class="fa fa-plus"></i>Add New Desigh || Style Master</span>
                                    </a>
                                 </div>
                                 <div class="col-lg-3">
                                    <div class="form-group">
                                       <label>Size Name</label><label style="color:red;">*</label>
                                       <select name="size_name" class="form-control" required>
                                          <option value="null">Select Size Name</option>
                                          <?php
                                             $query = mysqli_query($conn, "SELECT * FROM tbl_size_master");
                                             while ($row = mysqli_fetch_array($query)) {
                                             ?>
                                          <option><?php echo $row['size_name']; ?></option>
                                          <?php } ?> 
                                       </select>
                                    </div>
                                    <a href="#" class="" data-toggle="modal" data-target="#addtrain">
                                    <span style="font-size:11px;" class="pull-left"><i class="fa fa-plus"></i>Add New Size Master</span>
                                    </a>
                                 </div>
                                 <div class="col-lg-3">
                                    <div class="form-group">
                                       <label>Measurement of Units</label><label style="color:red;">*</label>
                                       <select name="mou_name" class="form-control" required>
                                          <option value="null">Select Measurement of Units</option>
                                          <?php
                                             $query = mysqli_query($conn, "SELECT * FROM tbl_mou_master");
                                             while ($row = mysqli_fetch_array($query)) {
                                             ?>
                                          <option><?php echo $row['mou_name']; ?></option>
                                          <?php } ?> 
                                       </select>
                                    </div>
                                    <a href="#" class="" data-toggle="modal" data-target="#addtrain">
                                    <span style="font-size:11px;" class="pull-left"><i class="fa fa-plus"></i>Add New MOU Master</span>
                                    </a>
                                 </div>
                                  <div class="col-lg-3">
                                    <div class="form-group">
                                       <label>Rac No.</label>
                                       <input type="text" class="form-control" name="rac_no" id="rac_no" placeholder="Enter Your Rac No" >
                                    </div>
                                 </div>
                                
                              </div>
                              <div class="row">
                                 <div class="col-lg-3">
                                    <div class="form-group">
                                       <label>Purchase Price (CP)</label><label style="color:red;">*</label>
                                       <input type="text" class="form-control" name="purchase_price" id="purchase_price" placeholder="Enter Your Purchase Price" required>
                                    </div>
                                 </div>
                                 <div class="col-lg-3">
                                    <div class="form-group">
                                       <label>MRP</label><label style="color:red;">*</label>
                                       <input type="text" class="form-control" name="mrp" id="mrp" placeholder="Enter Your MRP" required>
                                    </div>
                                 </div>
                                  <div class="col-lg-3">
                                    <div class="form-group">
                                       <label>Discount (%)</label><label style="color:red;">*</label>
                                       <input type="text" class="form-control" name="dis_per" id="dis_per" placeholder="Enter Your Discount (%)" required>
                                    </div>
                                 </div>
                                  <div class="col-lg-3">
                                    <div class="form-group">
                                       <label>Discount Amt.</label><label style="color:red;">*</label>
                                       <input type="text" class="form-control" name="dis_amt" id="dis_amt" placeholder="Enter Your Discount Amt." required>
                                    </div>
                                 </div>
                                 
                              </div>
                              <div class="row">
                                  <div class="col-lg-3">
                                    <div class="form-group">
                                       <label>Selling Price (SP)</label><label style="color:red;">*</label>
                                       <input type="text" class="form-control" name="sp" id="sp" placeholder="Enter Your Selling Price" required>
                                    </div>
                                 </div>
                                 <div class="col-lg-3">
                                    <div class="form-group">
                                       <label>Profit Amt.</label><label style="color:red;">*</label>
                                       <input type="text" class="form-control" name="profit_amt" id="profit_amt" placeholder="Enter Your Profit Amt." required>
                                    </div>
                                 </div>
                                 <div class="col-lg-3">
                                    <div class="form-group">
                                       <label>Minimum Quantity</label><label style="color:red;">*</label>
                                       <input type="text" class="form-control" name="min_qty" id="min_qty" placeholder="Enter Your Minimum Qty" required>
                                    </div>
                                 </div>
                                 <div class="col-lg-3">
                                    <div class="form-group">
                                       <label>Reorder Quantity</label><label style="color:red;">*</label>
                                       <input type="text" class="form-control" name="reorder_qty" id="reorder_qty" placeholder="Enter Your Reorder Qty" required>
                                    </div>
                                 </div>
                                
                                
                              </div>
                              <br>
                              <label>About this Item :</label>
                              <div class="row">
                                 
                                 <div class="col-lg-6">
                                    <div class="form-group">
                                       <label>1st</label>
                                       <input type="text" class="form-control" name="about_1" id="about_1" placeholder="Enter Your About this Item" >
                                    </div>
                                 </div>
                                 <div class="col-lg-6">
                                    <div class="form-group">
                                       <label>2nd</label>
                                       <input type="text" class="form-control" name="about_2" id="about_2" placeholder="Enter Your About this Item" >
                                    </div>
                                 </div>
                              </div>
                              <div class="row">
                                 <div class="col-lg-6">
                                    <div class="form-group">
                                       <label>3rd</label>
                                       <input type="text" class="form-control" name="about_3" id="about_3" placeholder="Enter Your About this Item" >
                                    </div>
                                 </div>
                                 <div class="col-lg-6">
                                    <div class="form-group">
                                       <label>4th</label>
                                       <input type="text" class="form-control" name="about_4" id="about_4" placeholder="Enter Your About this Item" >
                                    </div>
                                 </div>
                              </div>
                              <br>
                              <div class="row">
                                 <div class="col-sm-3">
                                    <div class="form-group">
                                       <label>Front Picture upload</label><br>
                                      
                                       <input type="file" name="picture" id="picture">
                                    </div>
                                 </div>
                                 <div class="col-sm-3">
                                    <div class="form-group">
                                       <label>Back-1st Picture upload</label><br>
                                       
                                       <input type="file" name="picture1" id="picture1">
                                    </div>
                                 </div>
                                 <div class="col-sm-3">
                                    <div class="form-group">
                                       <label>Back-2nd Picture upload</label><br>
                                       
                                       <input type="file" name="picture2" id="picture2">
                                       <!-- <input type="hidden" name="old_picture"> -->
                                    </div>
                                 </div>
                                 <div class="col-sm-3">
                                    <div class="form-group">
                                       <label>Back-3rd Picture upload</label><br>
                                       
                                       <input type="file" name="picture3" id="picture3">
                                       <!-- <input type="hidden" name="old_picture"> -->
                                    </div>
                                 </div>
                               
                              </div>
                              <br>
                             
                                 <div class="row">
                                       <div class="col-sm-3">
                                    <div class="form-group">
                                       <label>Video Upload</label><br>
                                       
                                       <input type="file" name="picture" id="picture">
                                       <!-- <input type="hidden" name="old_picture"> -->
                                    </div>
                                 </div>
                                 <div class="col-sm-3">
                                    <div class="form-check">
                                       <label>Status</label><label style="color:red;">*</label><br>
                                       <label class="radio-inline">
                                       <input type="radio" name="status" value="true" checked="checked">Active</label>
                                       <label class="radio-inline"><input type="radio" name="status" value="false" >Inctive</label>
                                    </div>
                                 </div>
                                  <div class="col-sm-3">
                                   
                                 </div>
                                 <div class="col-sm-3">
                                     <br>
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
            <!-- model Modal1 -->
            <div class="modal fade" id="addtrain" tabindex="-1" role="dialog" aria-hidden="true">
               <div class="modal-dialog">
                  <div class="modal-content">
                     <div class="modal-header modal-header-primary">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h3><i class="fa fa-plus m-r-5"></i> Add New Group Master</h3>
                     </div>
                     <div class="modal-body">
                        <div class="row">
                           <div class="col-md-12">
                              <form method="post">
                                 <?php
                                    $msg='';
                                     if(isset($_POST['submit_btn']))
                                     {
                                      $group_code=$_POST['group_code'];
                                      $group_name=$_POST['group_name'];
                                      $hsn_code=$_POST['hsn_code'];
                                      $status=$_POST['status'];
                                      
                                          $dup=mysqli_query($conn,"select * from tbl_group_master where group_name='$group_name'");
                                            if(mysqli_num_rows($dup)>0)
                                            {
                                                $msg='<div class="alert alert-danger alert-dismissible fade1 show">
                                                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                                                    <strong>Size Master Already exist!</strong>
                                                    </div>';
                                            }
                                            else{
                                                $query="Insert into tbl_group_master(group_code,group_name,hsn_code,status)values('$group_code','$group_name','$hsn_code','$status')";
                                                if(mysqli_query($conn,$query)){
                                                    $msg='<div class="alert alert-success alert-dismissible fade1 show">
                                                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                                                    <strong>Group Creations Success..!</strong>
                                                  </div>';
                                                }
                                                else{
                                                    $msg='<div class="alert alert-danger alert-dismissible fade1 show">
                                                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                                                    <strong>Group Creations Failed!</strong>
                                                    </div>';
                                                }
                                            } 
                                        }
                                     ?>
                                 <div class="row">
                                    <div class="col-sm-2">
                                       <div class="form">
                                          <label>Code</label>
                                          <input type="password" name="group_code" class="form-control" id="group_code" readonly value="<?php @$no = mysqli_query($conn, "SELECT id FROM  tbl_group_master ORDER BY id DESC");
                                             $po_no = mysqli_fetch_assoc($no);
                                             echo 'GC-000'.($po_no['id']+1);
                                             ?>">
                                       </div>
                                    </div>
                                    <div class="col-sm-5">
                                       <div class="form-group">
                                          <label>Group Name</label>
                                          <input type="text" class="form-control" name="group_name" id="group_name" placeholder="Enter Your Group Name" required>
                                       </div>
                                    </div>
                                    <div class="col-sm-5">
                                       <div class="form-group">
                                          <label>HSN CODE</label>
                                          <input type="text" class="form-control" name="hsn_code" id="hsn_code" placeholder="Enter Your HSN Code" required>
                                       </div>
                                    </div>
                                 </div>
                                 <div class="container-fluid">
                                    <div class="row">
                                       <button type="submit" name="submit" class="btn btn-warning" style="width:100px;">Reset</button>
                                       <button type="submit" name="submit_btn" class="btn btn-success" style="width:100px;background-color: #009688; color: white;">Save</button>
                                    </div>
                                 </div>
                              </form>
                           </div>
                        </div>
                     </div>
                     <div class="modal-footer">
                        <button type="button" class="btn btn-danger pull-right" data-dismiss="modal">Close</button>
                     </div>
                  </div>
                  <!-- /.modal-content -->
               </div>
               <!-- /.modal-dialog -->
            </div>
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
                                 <span>Item Master List !</span>
                              </span>
                           </div>
                        </div>
                        <div class="panel-body form-scroll">
                           <div class="btn-group">
                              <a href="fh_itemmastercreation.php"> <button class="button-btn-btn btn-exp btn-sm"><i class="fa fa-plus"></i> Add New Item Master Creation</button></a>
                           </div>
                           <?php include('include/button-export-to-data.php');?>
                           <div class="container-fluid">
                              <div class="row">
                                 <input type="text" class="form-control-control" name="company_name" id="company_name" placeholder="Data Search" required>
                              </div>
                           </div>
                           <div class="table-responsive ">
                              <table id="dataTableExample1" class="table table-bordered table-striped table-hover">
                                 <thead>
                                    <tr class="info">
                                       <th width="50px;">SrNo</th>
                                       <th width="50px;">Action</th>
                                       <th>Status</th>
                                       <th>Item Code</th>
                                       <th>Barcode No</th>
                                       <th>SKU No</th>
                                       <th>Item Name</th>
                                       <th>Description</th>
                                       <th>Group</th>
                                       <th>SubGroup</th>
                                       <th>Brand</th>
                                       <th>Color</th>
                                       <th>Style</th>
                                       <th>Size</th>
                                       <th>MOU</th>
                                       <th>PP||CP</th>
                                       <th>MRP</th>
                                       <th>Discount(%)</th>
                                       <th>Discount Amt.</th>
                                       <th>SP</th>
                                       <th>Profit Amt</th>
                                       <th>Min Qty</th>
                                       <th>Reorder Qty</th>
                                       <th>RAC No</th>
                                       <th>About This Item-1</th>
                                       <th>About This Item-2</th>
                                       <th>About This Item-3</th>
                                       <th>About This Item-4</th>
                                       <th>Front Picture</th>
                                       <th>Back-1 Picture</th>
                                       <th>Back-2 Picture</th>
                                       <th>Back-3 Picture</th>
                                       <th>Video</th>
                                       <th>Status</th>
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
                                       <td style="background-color:#2A3F54" class="button-btn-btn-btn21">
                                          <a class=""onClick="return confirm('Are you sure you want to Update Item Master?')" href="fh_itemmastercreation_update.php?update_id=<?php echo $user['id']; ?>">
                                          <span
                                             data-toggle="tooltip" title="Are you sure you want to Update Item Name" type="button" class="" ><i style="color: white;" class="fa fa-pencil"></i><br><span style="color: white;">EDIT</span>
                                          </span></a><br><br>
                                          <a class="" onClick="return confirm('Are you sure you want to Delete Item Name List ?')" href="fh_itemmastercreation_list.php?delete_id=<?php echo $user['id']; ?>">
                                          <span data-toggle="tooltip" title="Are you sure you want to Delete Item Name List" type="button" class="" ><i style="color:white;" class="fa fa-trash-o"></i><span style="color:white;"class="">DELETE</span></span></a><br><br>

                                          <a class="" onClick="return confirm('Are you sure you want to Delete Item Name List ?')" href="fh_itemmastercreation_list.php?delete_id=<?php echo $user['id']; ?>">
                                          <span data-toggle="tooltip" title="Are you sure you want to Delete Brand Name List" type="button" class="" ><i style="color:white;" class="fa fa fa-list-alt"></i><span style="color:white;">DETAILS</span></span></a>
                                          <br><br>
                                          <label class="switch1">
                                          <input type="checkbox">
                                          <span class="slider1 round"></span>
                                          </label>
                                       </td>
                                       <td>
                                          <label class="switch1">
                                          <input type="checkbox">
                                          <span class="slider1 round"></span>
                                          </label>
                                       </td>
                                       <td>
                                          <?php echo $user['item_code']; ?>
                                       </td>
                                       <td><?php echo substr($user['barcode_no'],0,50); ?></td>
                                       <td><?php echo substr($user['sku_no'],0,50); ?></td>
                                       <td><?php echo substr($user['item_name'],0,50); ?></td>
                                       <td><?php echo substr($user['description'],0,50); ?></td>
                                       <td><?php echo substr($user['group_name'],0,50); ?></td>
                                       <td><?php echo substr($user['subgroup_name'],0,50); ?></td>
                                       <td><?php echo substr($user['brand_name'],0,50); ?></td>
                                       <td><?php echo substr($user['color_name'],0,50); ?></td>
                                       <td><?php echo substr($user['style_name'],0,50); ?></td>
                                       <td><?php echo substr($user['size_name'],0,50); ?></td>
                                       <td><?php echo substr($user['mou_name'],0,50); ?></td>
                                       <td><?php echo substr($user['purchase_price'],0,50); ?></td>
                                       <td><?php echo substr($user['mrp'],0,50); ?></td>
                                       <td><?php echo substr($user['dis_per'],0,50); ?></td>
                                       <td><?php echo substr($user['dis_amt'],0,50); ?></td>
                                       <td><?php echo substr($user['sp'],0,50); ?></td>
                                       <td><?php echo substr($user['profit_amt'],0,50); ?></td>
                                       <td><?php echo substr($user['min_qty'],0,50); ?></td>
                                       <td><?php echo substr($user['reorder_qty'],0,50); ?></td>
                                       <td><?php echo substr($user['rac_no'],0,50); ?></td>
                                       <td><?php echo substr($user['about_1'],0,50); ?></td>
                                       <td><?php echo substr($user['about_2'],0,50); ?></td>
                                       <td><?php echo substr($user['about_3'],0,50); ?></td>
                                       <td><?php echo substr($user['about_4'],0,50); ?></td>
                                       <td>
                                          <img src="uploads/item-master/<?php echo $user['picture']; ?>" class="img-circle" alt="User Image" width="50" height="50">
                                       </td>
                                       <td>
                                          <img src="uploads/item-master/<?php echo $user['picture1']; ?>" class="img-circle" alt="User Image" width="50" height="50">
                                       </td>
                                       <td>
                                          <img src="uploads/item-master/<?php echo $user['picture2']; ?>" class="img-circle" alt="User Image" width="50" height="50">
                                       </td>
                                       <td>
                                          <img src="uploads/item-master/<?php echo $user['picture3']; ?>" class="img-circle" alt="User Image" width="50" height="50">
                                       </td>
                                       <td>
                                          <img src="uploads/item-master/<?php echo $user['picture3']; ?>" class="img-circle" alt="User Image" width="50" height="50">
                                       </td>
                                       <td><?php echo substr($user['status'],0,50); ?></td>
                                    </tr>
                                    <?php } ?>
                                 </tbody>
                              </table>
                           </div>
                        </div>
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
            <!-- /.content -->
            <!-- /.content -->
         </div>
         <!-- /.content-wrapper -->
         <!-- /.footer -->
         <?php include('include/footer.php');?> 
         <!-- /.footer -->
         <?php include('include/Sidenavbuttons.php');?> 
      </div>
      <?php include('include/js.php');?>
   </body>
</html>