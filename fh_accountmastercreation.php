<?php include('top.php');?>
<?php
    $msg=''; 
     if(isset($_POST['submit']))
     {
      $date=$_POST['date'];
      $account_code=$_POST['account_code'];
      $account_name=$_POST['account_name'];
      $contact_name=$_POST['contact_name'];
      $mobile_no1=$_POST['mobile_no1'];
      $mobile_no2=$_POST['mobile_no2'];
      $phone_no=$_POST['phone_no'];
      $email_id=$_POST['email_id'];
      $gst_no=$_POST['gst_no'];
      $partytype_name=$_POST['partytype_name'];
      $customertype_name=$_POST['customertype_name'];
      $location_name=$_POST['location_name'];
      $address_1=$_POST['address_1'];
      $address_2=$_POST['address_2'];
      $opn_bal=$_POST['opn_bal'];
      $picture=$_POST['picture'];
      $gender=$_POST['gender'];
      $status=$_POST['status'];

      
    
          $dup=mysqli_query($conn,"select * from tbl_account_master where account_name='$account_name'");
            if(mysqli_num_rows($dup)>0)
            {
                $msg='<div class="alert alert-danger alert-dismissible fade1 show">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <strong>Account Master Already exist!</strong>
                    </div>';
            }
            else{
                $query="Insert into tbl_account_master(date,account_code,account_name,contact_name,mobile_no1,mobile_no2,phone_no,email_id,gst_no,partytype_name,customertype_name,location_name,address_1,address_2,opn_bal,picture,gender,status)values('$date','$account_code','$account_name','$contact_name','$mobile_no1','$mobile_no2','$phone_no','$email_id','$gst_no','$partytype_name','$customertype_name','$location_name','$address_1','$address_2','$opn_bal','$picture', '$gender', '$status')";
                if(mysqli_query($conn,$query)){
                    $msg='<div class="alert alert-success alert-dismissible fade1 show">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <strong>Account Maste Creations Success..!</strong>
                  </div>';
                }
                else{
                    $msg='<div class="alert alert-danger alert-dismissible fade1 show">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <strong>Account Creations Failed!</strong>
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
      <title>Account Master Creations || TEJASERP</title>
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
            
            
           <div class="container-fluid">
               <div class="row">
                  <?php include('include/menu-header.php');?>
               </div>
            </div>
            
            

            <!-- Main content -->
            <section class="">
                
               <div class="row">
                   
                  <!-- Form controls -->
                  <div class="col-sm-12">
                      
                     <div class="panel panel-bd lobidisable">
                        <div class="panel-heading">
                           <span class="fs-15 panel-header-text">
                              <i class="fa fa-pencil mr-5"></i>
                              <span>Account Master Creations !</span>
                           </span>
                        </div>
                        <div class="panel-body">
                              <div class="btn-group">
                              <a href="fh_accountmastercreation_list.php"> <button class="button-btn-btn btn-exp btn-sm"><i class="fa fa-bars" style="margin-right: 5px;"></i>Account Master List</button></a>
                           </div>
                           <form method="post">
                              
                             

                              <div class="row">
                                 
                                 <div class="col-sm-2">
                                    <div class="form-group">
                                       <label>Code</label>
                                       <input type="text" name="account_code" class="form-control" id="account_code" readonly value="<?php @$no = mysqli_query($conn, "SELECT id FROM  tbl_account_master ORDER BY id DESC");
                                          $po_no = mysqli_fetch_assoc($no);
                                          echo 'AC-000'.($po_no['id']+1);
                                          ?>">
                                       </div>
                                    </div>
                                    <div class="col-sm-5">
                                       <div class="form-group">
                                          <label>Account Name</label>
                                          <input type="text" class="form-control" name="account_name" id="account_name" placeholder="Enter Your Account Name" required>
                                       </div>
                                    </div>
                                    <div class="col-sm-5">
                                       <div class="form-group">
                                         <label>Contact Name</label>
                                         <input type="text" class="form-control" name="contact_name" id="contact_name" placeholder="Enter Your Contact Name" required>
                                      </div>
                                   </div>
                                </div>
                                <div class="row">
                                 <div class="col-sm-2">
                                    <div class="form-group">
                                       <label>Date</label>
                                       <input id='minMaxExample' type="text" name="date" id="date" class="form-control" placeholder="Enter Date...">
                                    </div>
                                 </div>
                                 <div class="col-sm-5">
                                    <div class="form-group">
                                                   <label>Mobile No.1</label>
                                                   <input type="text" class="form-control" name="mobile_no1" id="mobile_no1" placeholder="Enter Your Mobile No" required>
                                                </div>
                                 </div>
                                 <div class="col-sm-5">
                                    <div class="form-group">
                                                   <label>Mobile No.2</label>
                                                   <input type="text" class="form-control" name="mobile_no2" id="mobile_no2" placeholder="Enter Your Mobile No" required>
                                                </div>
                                             </div>
                                          
                                          </div>
                                          
                                          <div class="row">
                                             
                                             <div class="col-sm-4">
                                                <div class="form-group">
                                                   <label>Phone No.</label>
                                                   <input type="text" class="form-control" name="phone_no" id="phone_no" placeholder="Enter Your Phone No" required>
                                                </div>
                                             </div>
                                             <div class="col-sm-4">
                                                <div class="form-group">
                                                   <label>Email-ID</label>
                                                   <input type="text" class="form-control" name="email_id" id="email_id" placeholder="Enter Your Email-ID" required>
                                                </div>
                                             </div>

                                             <div class="col-sm-4">
                                                <div class="form-group">
                                                   <label>GST No</label>
                                                   <input type="text" class="form-control" name="gst_no" id="gst_no" placeholder="Enter Your GST No" required>
                                                </div>
                                             </div>
                                             
                                             
                                             </div>

                                          

                                          <div class="row">
                                             
                                             <div class="col-lg-4">
                                                <div class="form-group">
                                                   <label>Party Type</label>
                                                    <select name="partytype_name" class="form-control" required>
                                                        <option value="null">Select Party Type</option>
                                                        <?php
                                                        $query = mysqli_query($conn, "SELECT * FROM tbl_partytype_master");
                                                        while ($row = mysqli_fetch_array($query)) {
                                                        ?>
                                                            <option><?php echo $row['partytype_name']; ?></option>
                                                        <?php } ?> 
                                                    </select>
                                                </div>
                                             </div>

                                             <div class="col-lg-4">
                                                <div class="form-group">
                                                   <label>Customer Type</label>
                                                    <select name="customertype_name" class="form-control" required>
                                                        <option value="null">Select Customer Type Name</option>
                                                        <?php
                                                        $query = mysqli_query($conn, "SELECT * FROM tbl_customertype_master");
                                                        while ($row = mysqli_fetch_array($query)) {
                                                        ?>
                                                            <option><?php echo $row['customertype_name']; ?></option>
                                                        <?php } ?> 
                                                    </select>
                                                </div>
                                             </div>

                                             <div class="col-lg-4">
                                                <div class="form-group">
                                                   <label>Location</label>
                                                    <select name="location_name" class="form-control" required>
                                                        <option value="null">Select Location Name</option>
                                                        <?php
                                                        $query = mysqli_query($conn, "SELECT * FROM tbl_location_master");
                                                        while ($row = mysqli_fetch_array($query)) {
                                                        ?>
                                                            <option><?php echo $row['location_name']; ?></option>
                                                        <?php } ?> 
                                                    </select>
                                                </div>
                                             </div>


                                          </div>

                                          <div class="row">
                                             
                                             <div class="col-lg-6">
                                                <div class="form-group">
                                                   <label>Address 1</label>
                                                   <input type="text" class="form-control" name="address_1" id="address_1" placeholder="Enter Your Address" required>
                                                </div>
                                             </div>
                                             <div class="col-lg-6">
                                                <div class="form-group">
                                                   <label>Address 2</label>
                                                   <input type="text" class="form-control" name="address_2" id="address_2" placeholder="Enter Your Address" required>
                                                </div>
                                             </div>
                                             
                                          </div>

                                          <div class="row">
                                             
                                             <div class="col-lg-6">
                                                <div class="form-group">
                                                   <label>Opening Balance</label>
                                                   <input type="text" class="form-control" name="opn_bal" id="opn_bal" placeholder="Enter Opening Bal" required>
                                                </div>
                                             </div>
                                             
                                          </div><br>
                                          
                                          
                                          
                                          
                                          
                                          <div class="row">
                                             <div class="col-sm-4">
                                                <div class="form-group">
                                                   <label>Picture upload</label>
                                                   <input type="file" name="picture" id="picture">
                                                   <!-- <input type="hidden" name="old_picture"> -->
                                                </div>
                                             </div>
                                             <div class="col-sm-4">
                                                <div class="form-group">
                                                   <label>Gender</label><br>
                                                   <label class="radio-inline"><input name="gender" value="1" checked="checked" type="radio">Male</label> 
                                                   <label class="radio-inline"><input name="gender" value="0" type="radio">Female</label>
                                                </div>
                                             </div>
                                             <div class="col-sm-4">
                                                <div class="form-check">
                                                   <label>Status</label><br>
                                                   <label class="radio-inline">
                                                      <input type="radio" name="status" value="true" checked="checked">Active</label>
                                                      <label class="radio-inline"><input type="radio" name="status" value="false" >Inctive</label>
                                                   </div>
                                                </div>
                                             </div><br>
                                             <div class="container-fluid">
                                                <div class="row">
                                                   <button type="submit" name="submit" class="btn btn-warning" style="width:100px;">Reset</button>
                                                <button type="submit" name="submit" class="btn btn-success" style="width:100px;background-color: #009688; color: white;">Save</button>
                                                </div><br>
                                                
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
                              <span>Account Master List !</span>
                           </span>
                        </div>
                        
                        <div class="panel-body">
                           <div class="btn-group">
                              <a href="fh_accountmastercreation.php"> <button class="button-btn-btn btn-exp btn-sm"><i class="fa fa-plus"></i> Add New Account Master Creation</button></a>
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

                                       <th>SrNo</th>
                                       <th>Action</th>
                                       <th>Status</th>
                                       <th>Date</th>
                                       <th>A/C Code</th>
                                       <th>A/C Name</th>
                                       <th>Contact Name</th>
                                       <th>Picture</th>
                                       <th>Mobile No:1</th>
                                       <th>Mobile No:2</th>
                                       <th>Phone No</th>
                                       <th>Email-ID</th>
                                       <th>GST No</th>
                                       <th>Party Type</th>
                                       <th>Customer Type</th>
                                       <th>Location</th>
                                       <th>Address-1</th>
                                       <th>Address-2</th>
                                       <th>Opening Bal.</th>
                                       <th>Gender</th>
                                       
                                    </tr>
                                 </thead>
                                 <tbody>
                                    <?php
                                                $query=mysqli_query($conn,"select * from tbl_account_master"); 
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
                                                         <a class=""onClick="return confirm('Are you sure you want to Update Item Master?')" href="fh_accountmastercreation_update.php?update_id=<?php echo $user['id']; ?>">
                                                            <span
                                                            data-toggle="tooltip" title="Are you sure you want to Update Account Name" type="button" class="" ><i style="color: white;" class="fa fa-pencil"></i><br><span style="margin-left:0px; color: white;">EDIT</span>
                                                         </span></a><br><br>

                                          

                                          <a class="" onClick="return confirm('Are you sure you want to Delete Account Name List ?')" href="fh_accountmastercreation_list.php?delete_id=<?php echo $user['id']; ?>">

                                             <span data-toggle="tooltip" title="Are you sure you want to Delete Account Name List" type="button" class="" ><i style="margin-left: 0px; color:white;" class="fa fa-trash-o"></i><br><span style="margin-left:0px; color:white;"class="">DELETE</span></span></a><br><br>

                                             <a class="" onClick="return confirm('Are you sure you want to Details Item Name List ?')" href="fh_Detailsmastercreation_list.php?delete_id=<?php echo $user['id']; ?>">

                                             <span data-toggle="tooltip" title="Are you sure you want to Delete Brand Name List" type="button" class="" ><i style="margin-left: 0px; color:white;" class="fa fa fa-list-alt"></i><br><span style="margin-left:0px; color:white;">DETAILS</span></span></a><br><br>

                                             <label class="switch1">
                                             <input type="checkbox">
                                             <span class="slider1 round"></span>
                                             </label>

                                             
                                          </td>
                                          <td><?php echo substr($user['status'],0,50); ?></td>

                                          <td>
                                             <?php echo $user['date']; ?>
                                                
                                             </td>

                                          <td>
                                             <?php echo $user['account_code']; ?>
                                                
                                             </td>
                                          <td>
                                             <?php echo $user['account_name']; ?>
                                                
                                             </td>
                                             <td><?php echo substr($user['contact_name'],0,50); ?></td>

                                                   <td><img src="assets/dist/img/w1.png" class="img-circle" alt="User Image" width="50" height="50"> </td>
                                                   
                                                   
                                                   <td><?php echo substr($user['mobile_no1'],0,50); ?></td>
                                                   <td><?php echo substr($user['mobile_no2'],0,50); ?></td>
                                                   <td><?php echo substr($user['phone_no'],0,50); ?></td>
                                                   <td><?php echo substr($user['email_id'],0,50); ?></td>
                                                   <td><?php echo substr($user['gst_no'],0,50); ?></td>
                                                   <td><?php echo substr($user['partytype_name'],0,50); ?></td>
                                                    <td><?php echo substr($user['customertype_name'],0,50); ?></td>
                                                    <td><?php echo substr($user['location_name'],0,50); ?></td>
                                                   <td><?php echo substr($user['address_1'],0,50); ?></td>
                                                    <td><?php echo substr($user['address_2'],0,50); ?></td>
                                                    <td><?php echo substr($user['opn_bal'],0,50); ?></td>
                                                   <td><?php echo substr($user['gender'],0,50); ?></td>
                                                     
                                                   
                                                   
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
                                    </div><br>
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
                                  
                                 </div><br>
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
                                  
                                 </div><br>
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
                                  
                                 </div><br>
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
                                  
                                 </div><br>
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
                                 
                                 </div><br>
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
                                 
                                 </div><br>
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
                                 
                                 </div><br>
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

