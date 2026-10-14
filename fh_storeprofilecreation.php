<?php include('top.php');?>
<?php
   $msg='';
    if(isset($_POST['submit']))
    {
     $code=$_POST['code'];
     $store_code=$_POST['store_code'];
     $store_name=$_POST['store_name'];
     $company_name=$_POST['company_name'];
     $mobile_no1=$_POST['mobile_no1'];
     $mobile_no2=$_POST['mobile_no2'];
     $phone_no=$_POST['phone_no'];
     $gst_no=$_POST['gst_no'];
     $email_id=$_POST['email_id'];
     $website_link=$_POST['website_link'];
     $address1=$_POST['address1'];
     $address2=$_POST['address2'];
     $tc1=$_POST['tc1'];
     $tc2=$_POST['tc2'];
     $tc3=$_POST['tc3'];
     $tc4=$_POST['tc4'];
     $tc5=$_POST['tc5'];
     $tc6=$_POST['tc6'];
     $invoice_type=$_POST['invoice_type'];
     $print_type=$_POST['print_type'];
     $defaultprint_copy=$_POST['defaultprint_copy'];
     $additional_script=$_POST['additional_script'];
     $welcome_script=$_POST['welcome_script'];
     $status=$_POST['status'];
   
   
   
   
     // DUPLICATE CHECK
    $check = mysqli_query($conn, "SELECT id FROM tbl_storeprofile_master WHERE store_name='$store_name'");
    if (mysqli_num_rows($check) > 0) {
        $msg = "<div class='alert alert-danger'>Store Name Already Exists!</div>";
    } else {
   
        // FILE UPLOAD PATH
        $path = "uploads/item-master/";
        if (!is_dir($path)) {
            mkdir($path, 777, true);
        }
   
        function uploadFile($file, $path) {
    if (isset($file) && $file['error'] === 0 && is_uploaded_file($file['tmp_name'])) {
   
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','webp'];
   
        if (!in_array($ext, $allowed)) {
            return "";
        }
   
        $filename = time() . "_" . preg_replace("/[^a-zA-Z0-9\.]/", "", $file['name']);
        move_uploaded_file($file['tmp_name'], $path . $filename);
   
        return $filename;
    }
    return "";
   }
   
   
        @$picture1  = uploadFile($_FILES['picture1'], $path);
       
   
        // INSERT QUERY (MATCHING TABLE EXACTLY)
        $sql = "INSERT INTO tbl_storeprofile_master (
                    code,store_code,store_name,company_name,mobile_no1,mobile_no2,phone_no,gst_no,email_id,website_link,address1,address2,tc1,tc2,tc3,tc4,tc5,tc6,invoice_type,print_type,defaultprint_copy,additional_script,welcome_script,picture1,status
                ) VALUES (
                    '$code','$store_code','$store_name','$company_name','$mobile_no1','$mobile_no2','$phone_no','$gst_no','$email_id','$website_link','$address1','$address2','$tc1','$tc2','$tc3','$tc4','$tc5','$tc6','$invoice_type','$print_type','$defaultprint_copy','$additional_script','$welcome_script','$picture1','$status')";
   
        if (mysqli_query($conn, $sql)) {
            $msg = "<div class='alert alert-success'>Item Saved Successfully!</div>";
        } else {
            $msg = "<div class='alert alert-danger'>Insert Failed!</div>";
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
      <title>Store Profile Master Creations || TEJASERP</title>
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
            <?php include('include/fh-form-scrolling-data-list.php');?>
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
            <section class="">
               <div class="row">
                  <div class="col-sm-12">
                     <div class="panel panel-bd lobidisable">
                        <div class="panel-heading">
                           <span class="fs-15 panel-header-text">
                              <i class="fa fa-pencil mr-5"></i>
                              <span>Store Profile Creations !</span>
                           </span>
                        </div>
                        <div class="panel-body form-scroll">
                           <div class="btn-group">
                              <a href="fh_storeprofilecreation_list.php"> <button class="button-btn-btn btn-exp btn-sm"><i class="fa fa-bars" style="margin-right: 5px;"></i>Store Master List</button></a>
                           </div>
                           <form method="post" enctype="multipart/form-data">
                              <div class="form-group">
                                 <?php echo $msg; ?>
                              </div>
                              <div class="row">
                                 <div class="col-sm-2">
                                    <div class="form-group">
                                       <label>Code</label>
                                       <input type="text" name="code" class="form-control" id="code" readonly value="<?php @$no = mysqli_query($conn, "SELECT id FROM  tbl_storeprofile_master ORDER BY id DESC");
                                          $po_no = mysqli_fetch_assoc($no);
                                          echo 'SC-000'.($po_no['id']+1);
                                          ?>">
                                    </div>
                                 </div>
                                 <div class="col-sm-2">
                                    <div class="form-group">
                                       <label>Store Code</label><label style="color:red;">*</label>
                                       <input type="text" class="form-control" name="store_code" id="store_code" placeholder="Enter Your Store Code" required>
                                    </div>
                                 </div>
                                 <div class="col-sm-4">
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
                                    <a href="#" class="" data-toggle="modal" data-target="#storeprofilecreation">
                                    <span style="font-size:11px;" class="pull-left"><i class="fa fa-plus"></i>Add New Store Master Creation</span>
                                    </a>
                                 </div>
                                 <div class="col-sm-4">
                                    <div class="form-group">
                                       <label>Company Name</label><label style="color:red;">*</label>
                                       <input type="text" class="form-control" name="company_name" id="company_name" placeholder="Enter Your Company Name" required>
                                    </div>
                                 </div>
                              </div>
                              <div class="row">
                                 <div class="col-sm-4">
                                    <div class="form-group">
                                       <label>Mobile No-1</label><label style="color:red;">*</label>
                                       <input type="text" class="form-control" name="mobile_no1" id="mobile_no1" placeholder="Enter Your Mobile No" required>
                                    </div>
                                 </div>
                                 <div class="col-sm-4">
                                    <div class="form-group">
                                       <label>Mobile No-2</label>
                                       <input type="text" class="form-control" name="mobile_no2" id="mobile_no2" placeholder="Enter Your Mobile no" >
                                    </div>
                                 </div>
                                 <div class="col-sm-4">
                                    <div class="form-group">
                                       <label>Phone No.</label>
                                       <input type="text" name="phone_no" id="phone_no" class="form-control" placeholder="Enter Your Phone No">
                                    </div>
                                 </div>
                              </div>
                              <div class="row">
                                 <div class="col-sm-4">
                                    <div class="form-group">
                                       <label>GST No.</label><label style="color:red;">*</label>
                                       <input type="text" class="form-control" name="gst_no" id="gst_no" placeholder="Enter Your GST No" required>
                                    </div>
                                 </div>
                                 <div class="col-sm-4">
                                    <div class="form-group">
                                       <label>Email-ID</label><label style="color:red;">*</label>
                                       <input type="text" class="form-control" name="email_id" id="email_id" placeholder="Enter Your Email-ID" required>
                                    </div>
                                 </div>
                                 <div class="col-sm-4">
                                    <div class="form-group">
                                       <label>Website Link</label>
                                       <input type="text" name="website_link" id="website_link" class="form-control" placeholder="Enter Your Website Link">
                                    </div>
                                 </div>
                              </div>
                              <div class="row">
                                 <div class="col-sm-6">
                                    <div class="form-group">
                                       <label>Address-1</label><label style="color:red;">*</label>
                                       <input type="text" class="form-control" name="address1" id="address1" placeholder="Enter Your Address" required="">
                                    </div>
                                 </div>
                                 <div class="col-sm-6">
                                    <div class="form-group">
                                       <label>Address-2</label>
                                       <input type="text" class="form-control" name="address2" id="address2" placeholder="Enter Your Address" >
                                    </div>
                                 </div>
                              </div>
                              <label>Term & condition :</label>
                              <div class="row">
                                 <div class="col-sm-6">
                                    <div class="form-group">
                                       <label>TC-1.</label>
                                       <input type="text" class="form-control" name="tc1" id="tc1" placeholder="Enter Your T&C" >
                                    </div>
                                 </div>
                                 <div class="col-sm-6">
                                    <div class="form-group">
                                       <label>TC-2.</label>
                                       <input type="text" class="form-control" name="tc2" id="tc2" placeholder="Enter Your T&C" >
                                    </div>
                                 </div>
                              </div>
                              <div class="row">
                                 <div class="col-sm-6">
                                    <div class="form-group">
                                       <label>TC-3.</label>
                                       <input type="text" class="form-control" name="tc3" id="tc3" placeholder="Enter Your T&C" >
                                    </div>
                                 </div>
                                 <div class="col-sm-6">
                                    <div class="form-group">
                                       <label>TC-4.</label>
                                       <input type="text" class="form-control" name="tc4" id="tc4" placeholder="Enter Your T&C" >
                                    </div>
                                 </div>
                              </div>
                              <div class="row">
                                 <div class="col-sm-6">
                                    <div class="form-group">
                                       <label>TC-5.</label>
                                       <input type="text" class="form-control" name="tc5" id="tc5" placeholder="Enter Your T&C" >
                                    </div>
                                 </div>
                                 <div class="col-sm-6">
                                    <div class="form-group">
                                       <label>TC-6.</label>
                                       <input type="text" class="form-control" name="tc6" id="tc6" placeholder="Enter Your T&C" >
                                    </div>
                                 </div>
                              </div>
                              <div class="row">
                                 <div class="col-sm-2">
                                    <div class="form-group">
                                       <label>Invoice Type</label><label style="color:red;">*</label>
                                       <select name="invoice_type" class="form-control" required>
                                          <option value="null">Select Invoice Type </option>
                                          <option>Tax Invoice</option>
                                          <option>Retail Invoice</option>
                                          <option>Cash Memo</option>
                                       </select>
                                    </div>
                                 </div>
                                 <div class="col-sm-2">
                                    <div class="form-group">
                                       <label>Print Type</label><label style="color:red;">*</label>
                                       <select name="print_type" class="form-control" required>
                                          <option value="null">Select Print Type </option>
                                          <option>
                                             80 mm
                                          </option>
                                          <option>A4</option>
                                          <option>A5</option>
                                       </select>
                                    </div>
                                 </div>
                                 <div class="col-sm-2">
                                    <div class="form-group">
                                       <label>Default Print Copy</label><label style="color:red;">*</label>
                                       <select name="defaultprint_copy" class="form-control" required>
                                          <option value="null">Select Print Copy </option>
                                          <option>1</option>
                                          <option>2</option>
                                          <option>3</option>
                                       </select>
                                    </div>
                                 </div>
                                 <div class="col-sm-6">
                                    <div class="form-group">
                                       <label>Additional Script</label>
                                       <input type="text" class="form-control" name="additional_script" id="additional_script" placeholder="Enter Your Additional Script" >
                                    </div>
                                 </div>
                              </div>
                              <br>
                              <div class="row">
                                 <div class="col-sm-3">
                                    <div class="form-group">
                                       <label>Front</label><br>
                                       <label>Picture upload</label>
                                       <input type="file" name="picture1" id="picture1">
                                    </div>
                                 </div>
                                 <div class="col-sm-3">
                                    <div class="form-check">
                                       <label>Status</label><br>
                                       <label class="radio-inline">
                                       <input type="radio" name="status" value="true" checked="checked">Active</label>
                                       <label class="radio-inline"><input type="radio" name="status" value="false" >Inctive</label>
                                    </div>
                                 </div>
                                 <div class="col-sm-6">
                                    <div class="form-group">
                                       <label>Welcome Script</label>
                                       <input type="text" class="form-control" name="welcome_script" id="welcome_script" placeholder="Enter Your Welcome Script" >
                                    </div>
                                 </div>
                              </div>
                              <br>
                              <div class="container-fluid">
                                 <div class="row">
                                    <button type="submit" name="submit" class="btn btn-warning" style="width:100px;">Reset</button>
                                    <button type="submit" name="submit" class="btn btn-success" style="width:100px;background-color: #009688; color: white;">Save</button>
                                 </div>
                                 <br>
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
                  <div class="col-sm-12">
                     <div class="panel panel-bd lobidisable">
                        <div class="panel-heading">
                           <span class="fs-15 panel-header-text">
                              <i class="fa fa-th-list mr-5"></i>
                              <span>Store Profile List !</span>
                           </span>
                        </div>
                        <div class="panel-body form-scroll">
                           <!-- Toolbar & Search Box Row under Header Panel -->
                           <div class="row m-b-15">
                              <div class="col-sm-6 col-xs-12 m-b-5">
                                 <div class="btn-group">
                                    <a href="fh_storeprofilecreation.php" class="btn btn-exp btn-sm">
                                       <i class="fa fa-plus mr-5"></i>Add New Store Profile Creation
                                    </a>
                                 </div>
                                 <?php include('include/button-export-to-data.php');?>
                              </div>
                              <div class="col-sm-6 col-xs-12">
                                 <!-- Search Form Box under Header Panel -->
                                 <form id="storeProfileCreationSearchForm" class="search-form-wrap pull-right" onsubmit="return false;" style="width: 100%; max-width: 360px;">
                                    <input type="hidden" name="page" id="page" value="1">
                                    <div class="input-group search-box-group">
                                       <input type="text" class="form-control search-input search-input-field" name="search" id="company_name" placeholder="Search Store Profile..." autocomplete="off">
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
                                       <th>Profile Code</th>
                                       <th>Store Code</th>
                                       <th>Store Name</th>
                                       <th>Company Name</th>
                                       <th>Mobile No</th>
                                       <th>GST No</th>
                                       <th>Email ID</th>
                                       <th>Invoice Type</th>
                                    </tr>
                                 </thead>
                                 <tbody id="storeprofilecreation-list-results">
                                    <?php
                                       $init_page = 1;
                                       $init_perpage = isset($_SESSION["_RECORDPERPAGE_"]) && $_SESSION["_RECORDPERPAGE_"] ? (int)$_SESSION["_RECORDPERPAGE_"] : 20;
                                       $init_offset = ($init_page - 1) * $init_perpage;
                                       $query = mysqli_query($conn, "SELECT * FROM tbl_storeprofile_master ORDER BY id DESC LIMIT $init_offset, $init_perpage"); 
                                       $countQuery = mysqli_query($conn, "SELECT COUNT(*) as total FROM tbl_storeprofile_master");
                                       $countRow = mysqli_fetch_assoc($countQuery);
                                       $totalRecords = $countRow['total'] ?? 0;
                                       $profile_list = array();
                                       while ($sp = mysqli_fetch_array($query)) {
                                          $profile_list[] = $sp;
                                       }
                                       $i = $init_offset + 1;
                                       foreach ($profile_list as $user) {
                                          $statusBadge = ($user['status'] ?? 'Active') === 'Active' 
                                              ? '<span class="label label-success badge-status-sm">Active</span>' 
                                              : '<span class="label label-danger badge-status-sm">Inactive</span>';
                                      ?>
                                    <tr>
                                       <td class="td-center-middle"><?php echo $i++; ?></td>
                                       <td class="td-center-middle-nowrap">
                                          <a class="btn btn-info btn-xs" data-toggle="tooltip" title="Edit Store Profile" href="fh_storeprofilecreation.php?update_id=<?php echo $user['id']; ?>">
                                             <i class="fa fa-pencil"></i> Edit
                                          </a>
                                          <button type="button" class="btn btn-warning btn-xs" data-toggle="modal" data-target="#viewStoreProfileModal<?php echo $user['id']; ?>" title="View Details">
                                             <i class="fa fa-eye"></i> Details
                                          </button>
                                          <a class="btn btn-danger btn-xs" data-toggle="tooltip" title="Delete Store Profile" href="fh_storeprofilecreation_list.php?delete_id=<?php echo $user['id']; ?>" onclick="return confirm('Are you sure you want to delete this record?');">
                                             <i class="fa fa-trash-o"></i> Delete
                                          </a>
                                       </td>
                                       <td class="td-center-middle"><?php echo $statusBadge; ?></td>
                                       <td><?php echo htmlspecialchars($user['code'] ?? ''); ?></td>
                                       <td><?php echo htmlspecialchars($user['store_code'] ?? ''); ?></td>
                                       <td><?php echo htmlspecialchars($user['store_name'] ?? ''); ?></td>
                                       <td><?php echo htmlspecialchars($user['company_name'] ?? ''); ?></td>
                                       <td><?php echo htmlspecialchars($user['mobile_no1'] ?? ''); ?></td>
                                       <td><?php echo htmlspecialchars($user['gst_no'] ?? ''); ?></td>
                                       <td><?php echo htmlspecialchars($user['email_id'] ?? ''); ?></td>
                                       <td><?php echo htmlspecialchars($user['invoice_type'] ?? ''); ?></td>
                                    </tr>
                                    <?php } ?>
                                 </tbody>
                              </table>
                           </div>
                           <div class="row pagination-wrapper-row">
                              <div class="col-sm-6">
                                 <div class="pagination-info-text">
                                    Total Records: <strong id="total-records-count"><?php echo $totalRecords; ?></strong>
                                 </div>
                              </div>
                              <div class="col-sm-6">
                                 <div id="storeprofilecreation-pagination">
                                    <?php
                                       $totalPages = ceil($totalRecords / $init_perpage);
                                       if ($totalPages > 1) {
                                          echo '<ul class="pagination pagination-sm pagination-no-margin pull-right">';
                                          echo '<li class="disabled"><span>&laquo; Prev</span></li>';
                                          for ($p = 1; $p <= min(5, $totalPages); $p++) {
                                             if ($p == 1) {
                                                echo '<li class="active"><span>' . $p . '</span></li>';
                                             } else {
                                                echo '<li><a href="javascript:void(0)" onclick="loadStoreProfileCreationData(' . $p . ')">' . $p . '</a></li>';
                                             }
                                          }
                                          if ($totalPages > 5) {
                                             echo '<li class="disabled"><span>...</span></li>';
                                             echo '<li><a href="javascript:void(0)" onclick="loadStoreProfileCreationData(' . $totalPages . ')">' . $totalPages . '</a></li>';
                                          }
                                          echo '<li><a href="javascript:void(0)" onclick="loadStoreProfileCreationData(2)">Next &raquo;</a></li>';
                                          echo '</ul>';
                                       }
                                    ?>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               <!-- customer Modal1 -->
               <!-- model Modal1 -->
               <div class="modal fade" id="storeprofilecreation" tabindex="-1" role="dialog" aria-hidden="true">
                  <div class="modal-dialog">
                     <div class="modal-content">
                        <div class="modal-header modal-header-primary">
                           <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                           <h3><i class="fa fa-plus m-r-5"></i> Add New Store Master Creation</h3>
                        </div>
                        <div class="modal-body">
                           <div class="row">
                              <div class="col-md-12">
                                 <form method="post">
                                    <?php
                                       $msg='';
                                        if(isset($_POST['submit_btn']))
                                        {
                                         $store_code=$_POST['store_code'];
                                         $store_name=$_POST['store_name'];
                                         $location_name=$_POST['location_name'];
                                         $company_name=$_POST['company_name'];
                                         $status=$_POST['status'];
                                         
                                             $dup=mysqli_query($conn,"select * from tbl_store_master where store_name='$store_name'");
                                               if(mysqli_num_rows($dup)>0)
                                               {
                                                   $msg='<div class="alert alert-danger alert-dismissible fade1 show">
                                                       <button type="button" class="close" data-dismiss="alert">&times;</button>
                                                       <strong>Store Master Already exist!</strong>
                                                       </div>';
                                               }
                                               else{
                                                   $query="Insert into tbl_store_master(store_code,store_name,location_name,company_name,status)values('$store_code','$store_name','$location_name','$company_name','$status')";
                                                   if(mysqli_query($conn,$query)){
                                                       $msg='<div class="alert alert-success alert-dismissible fade1 show">
                                                       <button type="button" class="close" data-dismiss="alert">&times;</button>
                                                       <strong>Store Master Creations Success..!</strong>
                                                     </div>';
                                                   }
                                                   else{
                                                       $msg='<div class="alert alert-danger alert-dismissible fade1 show">
                                                       <button type="button" class="close" data-dismiss="alert">&times;</button>
                                                       <strong>Store Master Creations Failed!</strong>
                                                       </div>';
                                                   }
                                               } 
                                           }
                                        ?>
                                    <div class="row">
                                       <div class="col-sm-6">
                                          <div class="form">
                                             <label>Store Code</label>
                                             <input type="text" name="store_code" class="form-control" id="store_code" readonly value="<?php @$no = mysqli_query($conn, "SELECT id FROM  tbl_store_master ORDER BY id DESC");
                                                $po_no = mysqli_fetch_assoc($no);
                                                echo 'SC'.($po_no['id']+1);
                                                ?>">
                                          </div>
                                       </div>
                                       <div class="col-sm-6">
                                          <div class="form-group">
                                             <label>Store Name</label>
                                             <input type="text" class="form-control" name="store_name" id="store_name" placeholder="Enter Your Store Name" required>
                                          </div>
                                       </div>
                                    </div>
                                    <div class="row">
                                       <div class="col-sm-6">
                                          <div class="form-group">
                                             <label>Company Name</label>
                                             <input type="text" class="form-control" name="company_name" id="company_name" placeholder="Enter Your Company Name" required>
                                          </div>
                                       </div>
                                       <div class="col-sm-6">
                                          <div class="form-group">
                                             <label>Store Location</label>
                                             <input type="text" class="form-control" name="location_name" id="location_name" placeholder="Enter Your Location Name" required>
                                          </div>
                                       </div>
                                    </div>
                                    <br>
                                    <div class="row">
                                       <div class="col-sm-4">
                                          <div class="form-check">
                                             <label>Status</label><br>
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
                                          <button type="submit" name="submit_btn" class="btn btn-success" style="width:100px;background-color: #009688; color: white;">Save</button>
                                       </div>
                                    </div>
                                    <br>
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
               <!-- model Modal1-end -->
               <!-- /.modal -->
            </section>
            <!-- /.content -->
            <!-- /.content -->
         </div>
         <!-- /.content-wrapper -->
         <!-- Container for View Modals -->
         <div id="storeprofile-modals-container">
            <?php foreach ($profile_list as $user) { ?>
            <div class="modal fade" id="viewStoreProfileModal<?php echo $user['id']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
               <div class="modal-dialog modal-lg">
                  <div class="modal-content modal-content-rounded">
                     <div class="modal-header modal-header-teal">
                        <button type="button" class="close modal-close-white" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title modal-title-bold">
                           <i class="fa fa-folder-open mr-8"></i>Store Profile Details - <?php echo htmlspecialchars($user['company_name'] ?? $user['store_name'] ?? ''); ?>
                        </h4>
                     </div>
                     <div class="modal-body modal-body-padded">
                        <div class="row">
                           <div class="col-md-6">
                              <table class="table table-bordered table-striped mb-0">
                                 <tbody>
                                    <tr>
                                       <th class="th-w-35">Profile Code</th>
                                       <td><code><?php echo htmlspecialchars($user['code'] ?? ''); ?></code></td>
                                    </tr>
                                    <tr>
                                       <th>Store Code</th>
                                       <td><?php echo htmlspecialchars($user['store_code'] ?? ''); ?></td>
                                    </tr>
                                    <tr>
                                       <th>Store Name</th>
                                       <td><strong><?php echo htmlspecialchars($user['store_name'] ?? ''); ?></strong></td>
                                    </tr>
                                    <tr>
                                       <th>Company Name</th>
                                       <td><?php echo htmlspecialchars($user['company_name'] ?? ''); ?></td>
                                    </tr>
                                    <tr>
                                       <th>Mobile No 1</th>
                                       <td><?php echo htmlspecialchars($user['mobile_no1'] ?? ''); ?></td>
                                    </tr>
                                    <tr>
                                       <th>Mobile No 2</th>
                                       <td><?php echo htmlspecialchars($user['mobile_no2'] ?? ''); ?></td>
                                    </tr>
                                    <tr>
                                       <th>Email ID</th>
                                       <td><?php echo htmlspecialchars($user['email_id'] ?? ''); ?></td>
                                    </tr>
                                    <tr>
                                       <th>Website</th>
                                       <td><?php echo htmlspecialchars($user['website'] ?? ''); ?></td>
                                    </tr>
                                 </tbody>
                              </table>
                           </div>
                           <div class="col-md-6">
                              <table class="table table-bordered table-striped mb-0">
                                 <tbody>
                                    <tr>
                                       <th class="th-w-35">GST No</th>
                                       <td><?php echo htmlspecialchars($user['gst_no'] ?? ''); ?></td>
                                    </tr>
                                    <tr>
                                       <th>FSSAI License No</th>
                                       <td><?php echo htmlspecialchars($user['fssai_lic_no'] ?? ''); ?></td>
                                    </tr>
                                    <tr>
                                       <th>Invoice Type</th>
                                       <td><span class="label label-info"><?php echo htmlspecialchars($user['invoice_type'] ?? ''); ?></span></td>
                                    </tr>
                                    <tr>
                                       <th>Status</th>
                                       <td><?php echo (($user['status'] ?? 'Active') === 'Active') ? '<span class="label label-success">Active</span>' : '<span class="label label-danger">Inactive</span>'; ?></td>
                                    </tr>
                                    <tr>
                                       <th>Address 1</th>
                                       <td><?php echo htmlspecialchars($user['address1'] ?? ''); ?></td>
                                    </tr>
                                    <tr>
                                       <th>Address 2</th>
                                       <td><?php echo htmlspecialchars($user['address2'] ?? ''); ?></td>
                                    </tr>
                                    <tr>
                                       <th>City / State</th>
                                       <td><?php echo htmlspecialchars(($user['city_name'] ?? '') . ' / ' . ($user['state_name'] ?? '')); ?></td>
                                    </tr>
                                    <tr>
                                       <th>Pincode</th>
                                       <td><?php echo htmlspecialchars($user['pincode'] ?? ''); ?></td>
                                    </tr>
                                 </tbody>
                              </table>
                           </div>
                        </div>
                        <?php if (!empty($user['terms_and_condition'])) { ?>
                        <div class="row mt-15">
                           <div class="col-xs-12">
                              <div class="panel panel-default mb-0">
                                 <div class="panel-heading"><strong>Terms &amp; Conditions</strong></div>
                                 <div class="panel-body"><?php echo nl2br(htmlspecialchars($user['terms_and_condition'])); ?></div>
                              </div>
                           </div>
                        </div>
                        <?php } ?>
                     </div>
                     <div class="modal-footer">
                        <button type="button" class="btn btn-danger pull-right" data-dismiss="modal">Close</button>
                     </div>
                  </div>
               </div>
            </div>
            <?php } ?>
         </div>
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
       <!-- Store Profile Creation AJAX Pagination & Search JS -->
       <script src="assets/dist/js/page/storeprofilecreation-list.js"></script>
      <!-- End Theme label Script
         =====================================================================-->
   </body>
   <!-- Mirrored from thememinister.com/crm/add-customer.html by HTTrack Website Copier/3.x [XR&CO'2014], Fri, 05 Aug 2022 06:10:42 GMT -->
</html>