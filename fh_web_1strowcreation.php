<?php include('top.php');?>
<?php
   $msg='';
    if(isset($_POST['submit']))
    {
     $brand_code=$_POST['brand_code'];
     $brand_name=$_POST['brand_name'];
     $status=$_POST['status'];
     
     // $picture=$_POST['picture'];
     // $file1=rand(1111111,9999999).'_'.$_FILES['picture']['name'];
     // $file_tmp1=$_FILES['picture']['tmp_name'];
     // $data=[]; $data1=[]; $data2=[];$data3=[];
     // $data=[$file1];
     // $picture=implode(' ',$data);
   
     
   
         $dup=mysqli_query($conn,"select * from tbl_brand_master where brand_name='$brand_name'");
           if(mysqli_num_rows($dup)>0)
           {
               $msg='<div class="alert alert-danger alert-dismissible fade1 show">
                   <button type="button" class="close" data-dismiss="alert">&times;</button>
                   <strong>Brand Name Already exist!</strong>
                   </div>';
           }
           else{
               $query="Insert into tbl_brand_master(brand_code,brand_name,status)values('$brand_code','$brand_name','$status')";
               if(mysqli_query($conn,$query)){
                   $msg='<div class="alert alert-success alert-dismissible fade1 show">
                   <button type="button" class="close" data-dismiss="alert">&times;</button>
                   <strong>Brand Name Creations Success..!</strong>
                 </div>';
               }
               else{
                   $msg='<div class="alert alert-danger alert-dismissible fade1 show">
                   <button type="button" class="close" data-dismiss="alert">&times;</button>
                   <strong>Brand Name Creations Failed!</strong>
                   </div>';
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
      <title>1st Row Web Creations || TEJASERP</title>
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
            <section class="">
               <div class="row" >
                  <!-- Form controls -->
                  <div class="col-sm-12">
                     <div class="panel panel-bd lobidisable">
                        <div class="panel-heading">
                           <span class="fs-15 panel-header-text">
                              <i class="fa fa-pencil mr-5"></i>
                              <span>1st Row Web Creations !</span>
                           </span>
                        </div>
                        <div class="panel-body">
                            <div class="btn-group">
                              <a href="fh_web_1strowcreation_list.php"> <button class="button-btn-btn btn-exp btn-sm"><i class="fa fa-bars" style="margin-right: 5px;"></i>Row Web List</button></a>
                           </div>
                           <form method="post">
                              <div class="form-group">
                                 <?php echo $msg; ?>
                              </div>
                              <!-- start tab list   -->
                              <div class="panel-group" role="tablist" aria-multiselectable="true">
                                 <div class="panel panel-default">
                                    <div class="panel-heading" role="tab">
                                       <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                       <i class="more-less glyphicon glyphicon-plus"></i>
                                       Details
                                       </a>
                                    </div>
                                    <div id="collapseOne" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
                                       <div class="table-responsive">
                                          <table class="table table-bordered table-hover">
                                             <thead>
                                                <tr class="">
                                                   <th>Item Code</th>
                                                   <th>Barcode No</th>
                                                   <th>Article No || SKU No</th>
                                                   <th>Item Name</th>
                                                   <th>Description</th>
                                                </tr>
                                             </thead>
                                             <tbody>
                                                <tr>
                                                   <td>04321</td>
                                                   <td>6543234565</td>
                                                   <td>
                                                      434345
                                                   </td>
                                                   <td>shirts</td>
                                                   <td>shirts</td>
                                                </tr>
                                             </tbody>
                                             <thead>
                                                <tr class="">
                                                   <th>Head Name</th>
                                                   <th>SubHead Name</th>
                                                   <th>Category || Group Name</th>
                                                   <th>SubCategory || SubGroup Name</th>
                                                   <th>Brand</th>
                                                </tr>
                                             </thead>
                                             <tbody>
                                                <tr>
                                                   <td>04321</td>
                                                   <td>6543234565</td>
                                                   <td>434345</td>
                                                   <td>shirtsgfghdsafdytsadfgsahcvhgascxyfcyxtsactxy</td>
                                                   <td>gfhfhg</td>
                                                </tr>
                                             </tbody>
                                             <thead>
                                                <tr class="">
                                                   <th>Color</th>
                                                   <th>Style</th>
                                                   <th>Size</th>
                                                   <th>MOU</th>
                                                   <th>HSN Code</th>
                                                </tr>
                                             </thead>
                                             <tbody>
                                                <tr>
                                                   <td>04321</td>
                                                   <td>6543234565</td>
                                                   <td>434345</td>
                                                   <td>shirtsgfghdsafdytsadfgsahcvhgascxyfcyxtsactxy</td>
                                                   <td>gfhfhg</td>
                                                </tr>
                                             </tbody>
                                             <thead>
                                                <tr class="">
                                                   <th>Purchase Price (CP)</th>
                                                   <th>MRP</th>
                                                   <th>Selling Price (SP)</th>
                                                   <th>Profit Amt</th>
                                                   <th>Minimum Quantity</th>
                                                </tr>
                                             </thead>
                                             <tbody>
                                                <tr>
                                                   <td>04321</td>
                                                   <td>6543234565</td>
                                                   <td>434345</td>
                                                   <td>shirtsgfghdsafdytsadfgsahcvhgascxyfcyxtsactxy</td>
                                                   <td>gfhfhg</td>
                                                </tr>
                                             </tbody>
                                             <thead>
                                                <tr class="">
                                                   <th>Reorder Quantity</th>
                                                   <th>Rac No.</th>
                                                   <th>Self No.</th>
                                                   <th>Profit Amt</th>
                                                   <th>Minimum Quantity</th>
                                                </tr>
                                             </thead>
                                             <tbody>
                                                <tr>
                                                   <td>04321</td>
                                                   <td>6543234565</td>
                                                   <td>434345</td>
                                                   <td>shirtsgfghdsafdytsadfgsahcvhgascxyfcyxtsactxy</td>
                                                   <td>gfhfhg</td>
                                                </tr>
                                             </tbody>
                                          </table>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                              <!--- end tab list -->
                              <div class="row">
                                 <div class="col-lg-4">
                                    <div class="form-group">
                                       <label>Item Code</label>
                                       <input type="password" name="brand_code" class="form-control" id="brand_code" readonly value="<?php @$no = mysqli_query($conn, "SELECT id FROM  tbl_brand_master ORDER BY id DESC");
                                          $po_no = mysqli_fetch_assoc($no);
                                          echo 'BC-000'.($po_no['id']+1);
                                          ?>">
                                    </div>
                                 </div>
                                 <div class="col-lg-8">
                                    <div class="form-group">
                                       <label>Item Name</label>
                                       <input type="text" class="form-control" name="brand_name" id="brand_name" placeholder="Enter Your Brand Name" required>
                                    </div>
                                 </div>
                              </div>
                              <div class="row">
                                 <div class="col-lg-4">
                                    <div class="form-group">
                                       <label>Barcode No.</label>
                                       <input type="text" class="form-control" name="brand_name" id="brand_name" placeholder="Enter Your Brand Name" required>
                                    </div>
                                 </div>
                                 <div class="col-lg-8">
                                    <div class="form-group">
                                       <label>SKU No. || Article No.</label>
                                       <input type="text" class="form-control" name="brand_name" id="brand_name" placeholder="Enter Your Brand Name" required>
                                    </div>
                                 </div>
                              </div>
                              <br>
                              <div class="row">
                                 <div class="col-lg-4">
                                    <div class="form-group">
                                       <label>Front Picture upload</label><br>
                                       <input type="file" name="picture" id="picture">
                                    </div>
                                 </div>
                                 <div class="col-lg-4">
                                    <div class="form-group">
                                       <label>Video upload</label><br>
                                       <input type="file" name="picture" id="picture">
                                    </div>
                                 </div>
                                 <div class="col-lg-4">
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
                                    <button type="submit" name="submit" class="btn btn-success" style="width:100px;background-color: #009688; color: white;">Save</button>
                                 </div>
                              </div>
                              <br>
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
                              <span>1st Row Web List !</span>
                           </span>
                        </div>
                        <div class="panel-body">
                           <div class="btn-group">
                              <a href="fh_web_1strowcreation.php"> <button class="button-btn-btn btn-exp btn-sm"><i class="fa fa-plus" style="margin-right: 5px;"></i>Row Web Creation</button></a>
                           </div>
                           <?php include('include/button-export-to-data.php');?>
                           <div class="container-fluid">
                              <div class="row">
                                 <input type="text" class="form-control-control" name="user_name" id="user_name" placeholder="Data Search" required>
                              </div>
                           </div> 
                           <div class="table-responsive ">
                              <table id="dataTableExample1" class="table table-bordered table-striped table-hover">
                                 <thead>
                                    <tr class="info">
                                       <th>SrNo</th>
                                       <th>Action</th>
                                       <th>Status</th>
                                       <th>Item Code</th>
                                       <th>Barcode No</th>
                                       <th>SKU No</th>
                                       <th>Item Name</th>
                                       <th>Description</th>
                                       <th>Group</th>
                                       <th>Subgroup</th>
                                       <th>Brand</th>
                                       <th>Color</th>
                                       <th>Style</th>
                                       <th>Size</th>
                                       <th>MOU</th>
                                       <th>HSN Code</th>
                                       <th>PP||CP</th>
                                       <th>MRP</th>
                                       <th>SP</th>
                                       <th>Profit Amt</th>
                                       <th>Min Qty</th>
                                       <th>Reorder Qty</th>
                                       <th>RAC No</th>
                                       <th>Self No</th>
                                       <th>Front Picture</th>
                                       <th>Video</th>
                                       
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
                                          <a class=""onClick="return confirm('Are you sure you want to Update Row Web')" href="fh_web_1strowcreation_update.php?update_id=<?php echo $user['id']; ?>">
                                          <span
                                             data-toggle="tooltip" title="Are you sure you want to Update Row Web Master" type="button" class="" ><i style="color: white;" class="fa fa-pencil"></i><br><span style="margin-left:0px; color: white;">EDIT</span>
                                          </span></a><br><br>
                                          <a class="" onClick="return confirm('Are you sure you want to Delete Row Web Master List ?')" href="fh_web_1strowcreation_list.php?delete_id=<?php echo $user['id']; ?>">
                                          <span data-toggle="tooltip" title="Are you sure you want to Delete Row Web Master List" type="button" class="" ><i style="margin-left: 0px; color:white;" class="fa fa-trash-o"></i><span style="margin-left:0px; color:white;"class="">DELETE</span></span></a>
                                          <br><br>
                                          <a class="" onClick="return confirm('Are you sure you want to Details Item Name List ?')" href="fh_Detailsmastercreation_list.php?delete_id=<?php echo $user['id']; ?>">
                                          <span data-toggle="tooltip" title="Are you sure you want to Delete Row Web Name List" type="button" class="" ><i style="margin-left: 0px; color:white;" class="fa fa fa-list-alt"></i><span style="margin-left:0px; color:white;">DETAILS</span></span></a>
                                          <br><br>
                                          <label class="switch1">
                                          <input type="checkbox">
                                          <span class="slider1 round"></span>
                                          </label>
                                       </td>
                                        <td><?php echo substr($user['status'],0,50); ?></td>
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
                                       <td><?php echo substr($user['hsn_code'],0,50); ?></td>
                                       <td><?php echo substr($user['purchase_price'],0,50); ?></td>
                                       <td><?php echo substr($user['mrp'],0,50); ?></td>
                                       <td><?php echo substr($user['sp'],0,50); ?></td>
                                       <td><?php echo substr($user['profit_amt'],0,50); ?></td>
                                       <td><?php echo substr($user['min_qty'],0,50); ?></td>
                                       <td><?php echo substr($user['reorder_qty'],0,50); ?></td>
                                       <td><?php echo substr($user['rac_no'],0,50); ?></td>
                                       <td><?php echo substr($user['self_no'],0,50); ?></td>
                                       <td>
                                         <img src="uploads/item-master/<?php echo $user['picture']; ?>" class="img-circle" alt="User Image" width="50" height="50">
                                       </td>
                                       <td>
                                         <img src="uploads/item-master/<?php echo $user['picture1']; ?>" class="img-circle" alt="User Image" width="50" height="50">
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
         <footer class="main-footer">
            <strong>2024 <a href="#">Awesomesoft || SS Enterprises</a>!</strong> 
         </footer>
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