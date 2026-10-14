<?php include('top.php');?>
<?php
    $msg='';
     if(isset($_POST['submit']))
     {
      $salesman_code=$_POST['salesman_code'];
      $salesman_name=$_POST['salesman_name'];
      $mobile_no=$_POST['mobile_no'];
      $store_name=$_POST['store_name'];
      $location_name=$_POST['location_name'];
      $target_amt=$_POST['target_amt'];
      $start_date=$_POST['start_date'];
      $end_date=$_POST['end_date'];
      $amount_per=$_POST['amount_per'];
      $amount=$_POST['amount'];
      $product_name=$_POST['product_name'];
      $description=$_POST['description'];
      $status=$_POST['status'];
      
      // $picture=$_POST['picture'];
      // $file1=rand(1111111,9999999).'_'.$_FILES['picture']['name'];
      // $file_tmp1=$_FILES['picture']['tmp_name'];
      // $data=[]; $data1=[]; $data2=[];$data3=[];
      // $data=[$file1];
      // $picture=implode(' ',$data);

      
    
          $dup=mysqli_query($conn,"select * from tbl_salesman_target_master where salesman_name='$salesman_name'");
            if(mysqli_num_rows($dup)>0)
            {
                $msg='<div class="alert alert-danger alert-dismissible fade1 show">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <strong>SalesMan Name Already exist!</strong>
                    </div>';
            }
            else{
                $query="Insert into tbl_salesman_target_master(salesman_code,salesman_name,mobile_no,store_name,location_name,target_amt,start_date,end_date,amount_per,amount,product_name,description,status)values('$salesman_code','$salesman_name','$mobile_no','$store_name','$location_name','$target_amt','$start_date','$end_date','$amount_per','$amount','$product_name','$description','$status')";
                if(mysqli_query($conn,$query)){
                    $msg='<div class="alert alert-success alert-dismissible fade1 show">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <strong>SalesMan Name Creations Success..!</strong>
                  </div>';
                }
                else{
                    $msg='<div class="alert alert-danger alert-dismissible fade1 show">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <strong>SalesMan Name Creations Failed!</strong>
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
      <title>SalesMan Target Master Creation || Fashion Hub</title>
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

         <?php include('include/notification.php');?> 
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
            <section class="content-header">
                <?php include('include/fh_menuheader.php');?>
                     
                  </section>
           <div class="container-fluid">
               <div class="row">
               <?php include('include/menu-header.php');?>
               <div class="pull-right"style=" margin-right: 15px;margin-bottom: 5px; margin-top: 6px;">
                        <a href="account-master-list.php"><button data-toggle="tooltip" title="Account Master List !" class="button-btn-btn-btn">Next !</button></a>
                        </div>
                        <div class="pull-left" style="margin-bottom: 5px;margin-left: 15px; margin-top: 6px;">
                        <a href="account-master-list.php"><button data-toggle="tooltip" title="Account Master List !" class="button-btn-btn">Previous !</button></a>
                        </div>
                </div>
            </div> 
            
            

            <!-- Main content -->
            <section class="container-fluid">
               <div class="row">
                  <!-- Form controls -->
                  <div class="col-sm-12">
                     <div class="panel panel-bd lobidisable">
                  <div class="panel-heading">
                     <span class="fs-15 panel-header-text">
                        <i class="fa fa-pencil mr-5"></i>
                        <span>SalesMan Target Master Creations !</span>
                     </span>
                  </div>

                        <div class="panel-body">
                           
                           <form method="post">
                              <div class="form-group">
                                 <?php echo $msg; ?>
                             </div>
                             
                              <div class="row">
                                 <div class="col-sm-2">
                                    <div class="form-group">
                                                        <label>SalesMan Code</label>
                                                        <input type="text" name="salesman_code" class="form-control" id="salesman_code" readonly value="<?php @$no = mysqli_query($conn, "SELECT id FROM  tbl_salesman_target_master ORDER BY id DESC");
                                                        $po_no = mysqli_fetch_assoc($no);
                                                        echo 'SMC-000'.($po_no['id']+1);
                                                    ?>">
                                                </div>
                                             </div>
                                             <div class="col-sm-5">
                                                <div class="form-group">
                                                   <label>SalesMan Name</label>
                                                   <select name="salesman_name" class="form-control" required>
                                                      <option value="null">Select SalesMan Name </option>
                                                      <?php
                                                      $query = mysqli_query($conn, "SELECT * FROM tbl_salesman_master");
                                                      while ($row = mysqli_fetch_array($query)) {
                                                         ?>
                                                         <option><?php echo $row['salesman_name']; ?></option>
                                                      <?php } ?>
                                                   </select>
                                                   
                                                </div>
                                             </div>
                                             <div class="col-sm-5">
                                                <div class="form-group">
                                                   <label>Mobile No.</label>
                                                   <input type="text" class="form-control" name="mobile_no" id="mobile_no" placeholder="Enter Your Mobile No" required>
                                                </div>
                                             </div>

                                             
                                             
                                          </div>

                                          <div class="row">
                                             <div class="col-lg-4">
                                                <div class="form-group">
                                                   <label>Store Name</label>
                                                   <input type="text" class="form-control" name="store_name" id="store_name" placeholder="Enter Your Store Name" required>
                                                   
                                                </div>
                                                <a href="#" class="" data-toggle="modal" data-target="#addtrain">
                                                   <span style="font-size:11px;" class="pull-left">
                                                      <i class="fa fa-plus"></i>Add New Store Master</span>
                                                   </a>
                                                </div>
                                 
                                             
                                             <div class="col-sm-4">
                                                <div class="form-group">
                                                   <label>Location Name</label>
                                                   <input type="text" class="form-control" name="location_name" id="location_name" placeholder="Enter Your Location Name" required>
                                                </div>
                                             </div>

                                             <div class="col-sm-4">
                                                <div class="form-group">
                                                   <label>Target Amt</label>
                                                   <input type="text" class="form-control" name="target_amt" id="target_amt" placeholder="Enter Your Target Amt" required>
                                                </div>
                                             </div>

                                             


                                          </div>
                                          <div class="row">
                                             <div class="col-sm-4">
                                                <div class="form-group">
                                                   <label>Start Date</label>
                                                   <input id='minMaxExample' type="text" name="start_date" id="start_date" class="form-control" placeholder="Enter Date...">
                                                </div>
                                             </div>

                                             <div class="col-sm-4">
                                                <div class="form-group">
                                                   <label>End Date</label>
                                                   <input id='minMaxExample' type="text" name="end_date" id="end_date" class="form-control" placeholder="Enter Date...">
                                                </div>
                                             </div>
                                             

                                          </div>

                                          <div class="row">
                                             <div class="col-sm-4">
                                                
                                                   <label>Incentive Details :</label>
                                                </div>
                                             </div><br>

                                             <div class="row">

                                                <div class="col-sm-2">
                                                <div class="form-group">
                                                   <label>Amount %</label>
                                                   <input type="text" class="form-control" name="amount_per" id="amount_per" placeholder="Enter Your Amt %" required>
                                                </div>
                                             </div>
                                 
                                             
                                             <div class="col-sm-2">
                                                <div class="form-group">
                                                   <label>Amount</label>
                                                   <input type="text" class="form-control" name="amount" id="amount" placeholder="Enter Your Amount" required>
                                                </div>
                                             </div>

                                             <div class="col-sm-3">
                                                <div class="form-group">
                                                   <label>Product Name</label>
                                                   <input type="text" class="form-control" name="product_name" id="product_name" placeholder="Enter Your Product Name" required>
                                                </div>
                                             </div>

                                             <div class="col-sm-5">
                                                <div class="form-group">
                                                   <label>Description</label>
                                                   <input type="text" class="form-control" name="description" id="description" placeholder="Enter Your Description" required>
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
                                                   <label>Coupon upload</label>
                                                   <input type="file" name="picture" id="picture">
                                                   <!-- <input type="hidden" name="old_picture"> -->
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
                                                </div>
                                                
                                             </div>
                                          </form>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </section>
              
             <!-- Main content -->
           <section class="container-fluid">
               <div class="row">
                  <!-- Form controls -->
                  <div class="col-sm-12">
                     <div class="panel panel-bd lobidisable">
                  <div class="panel-heading">
                     <span class="fs-15 panel-header-text">
                        <i class="fa fa-th-list mr-5"></i>
                        <span>SalesMan Target Master List !</span>
                     </span>
                  </div>

                        <div class="panel-body">
                           <?php include('include/button-export-to-data.php');?>
                         <?php include('include/searchbox.php');?>
                           <div class="table-responsive ">
                              <table id="dataTableExample1" class="table table-bordered table-striped table-hover">
                                 <thead>
                                    <tr class="info">
                                       <th>SrNo</th>
                                       <th>Action</th>
                                       <th>Status</th>
                                       <th>SalesMan Code</th>
                                       <th>SalesMan</th>
                                       <th>Mobile No</th>
                                       <th>Store Name</th>
                                       <th>Location</th>
                                       <th>Target Amt</th>
                                       <th>Start Date</th>
                                       <th>End Date</th>
                                       <th>Amount %</th>
                                       <th>Amount</th>
                                       <th>Product Name</th>
                                       <th>Description</th>
                                       <th>Picture</th>
                                       <th>Coupon</th>
                                    </tr>
                                 </thead>
                                 <tbody>
                                    <?php
                                                $query=mysqli_query($conn,"select * from tbl_salesman_target_master"); 
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
                                               <td style="background-color:#2A3F54">
                                                         <a class=""onClick="return confirm('Are you sure you want to Update SalesMan Target Master?')" href="fh_salesman_target_mastercreation_update.php?update_id=<?php echo $user['id']; ?>">
                                                            <span
                                                            data-toggle="tooltip" title="Are you sure you want to Update SalesMan Target Master" type="button" class="" ><i style="color: white;" class="fa fa-pencil"></i><span style="margin-left:5px; color: white;">Edit</span>
                                                         </span></a>

                                          

                                          <a class="" onClick="return confirm('Are you sure you want to Delete SalesMan Target Master List ?')" href="fh_salesman_target_mastercreation_list.php?delete_id=<?php echo $user['id']; ?>">

                                             <span data-toggle="tooltip" title="Are you sure you want to Delete SalesMan Target Master List" type="button" class="" ><i style="margin-left: 15px; color:white;" class="fa fa-trash-o"></i><span style="margin-left:5px; color:white;"class="">Delete</span></span></a>

                                             
                                          </td>
                                       </td>
                                       <td>
                                          <label class="switch1">
                                             <input type="checkbox">
                                             <span class="slider1 round"></span>
                                             </label>
                                          </td>
                                          <td><?php echo $user['salesman_code']; ?>
                                             
                                          </td>
                                          <td><?php echo substr($user['salesman_name'],0,50); ?>
                                             
                                          </td>
                                          <td><?php echo $user['mobile_no']; ?>
                                             
                                          </td>
                                          <td><?php echo substr($user['store_name'],0,50); ?>
                                             
                                          </td>
                                          <td><?php echo $user['location_name']; ?>
                                             
                                          </td>
                                          <td><?php echo substr($user['target_amt'],0,50); ?>
                                             
                                          </td>
                                          <td><?php echo $user['start_date']; ?>
                                             
                                          </td>
                                          <td><?php echo substr($user['end_date'],0,50); ?>
                                             
                                          </td>
                                          <td><?php echo $user['amount_per']; ?>
                                             
                                          </td>
                                          <td><?php echo substr($user['amount'],0,50); ?>
                                             
                                          </td>
                                          <td><?php echo $user['product_name']; ?>
                                             
                                          </td>
                                          <td><?php echo substr($user['description'],0,50); ?>
                                             
                                          </td>
                                          <td><img src="assets/dist/img/w1.png" class="img-circle" alt="User Image" width="50" height="50"> </td>
                                          <td><img src="assets/dist/img/w1.png" class="img-circle" alt="User Image" width="50" height="50"> </td>

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

