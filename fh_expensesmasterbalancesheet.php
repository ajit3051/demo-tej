<?php include('top.php');?>
<?php

if(isset($_GET['delete_id']))
{
   $id=$_GET['delete_id'];
   $sqld="DELETE FROM tbl_expensescreation_master WHERE id='$id'";
   $res=mysqli_query($conn,$sqld);
   if($res)
   {
    header('refresh:.5; url=fh_expensesmastercreation_list.php');
   }else{
    echo "<script>alert('Expenses Creation Record Failed');</script>";
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
      <title>Expenses Balance Sheet || Fashion Hub</title>
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
            
              
             <!-- Main content -->
           
            <!-- /.content -->
            <section class="container-fluid">
               <div class="row">
                  <!-- Form controls -->
                  <div class="col-sm-12">
                     <div class="panel panel-bd lobidisable">
                  <div class="panel-heading">
                     <span class="fs-15 panel-header-text">
                        <i class="fa fa-calculator mr-5"></i>
                        <span>Expenses Balance Sheet !</span>
                     </span>
                  </div>

                        <div class="panel-body">
                            <div class="row">
                              <div class="col-sm-4">
                                 <?php include('include/button-export-to-data.php');?>
                              </div>
                              <div class="col-sm-4">
                                 
                              </div>
                              <div class="col-sm-2">
                                 <div class="form-group">
                                 
                                 <div class=" input-group date form_date redate">
                                    <input id='minMaxExample' type="text" name="year" class="form-control years"placeholder="Date From"><span class="input-group-addon"><a href="#"><i class="fa fa-calendar"></i></a></span>
                                 </div>
                              </div>
                              </div>
                              <div class="col-sm-2">
                                 <div class="form-group">
                                 
                                 <div class=" input-group date form_date redate">
                                    <input id='minMaxExample2' type="text" name="year" class="form-control years"placeholder="Date To"><span class="input-group-addon"><a href="#"><i class="fa fa-calendar"></i></a></span>
                                 </div>
                              </div>
                              </div>
                           </div>
                                                   

                         
                           <div class="table-responsive ">
                              <table id="dataTableExample1" class="table table-bordered table-striped table-hover">
                                 <thead>
                                    <tr class="info">
                                       <th>SrNo</th>
                                       
                                       <th>Date</th>
                                       
                                       <th>Party Name</th>
                                       <th>Regular Name</th>
                                       
                                       <th>ExpHead Name</th>
                                       <th>Remarks</th>
                                       <th>Payment Mode</th>
                                       <th>Amount</th>
                                       
                                    </tr>
                                 </thead>
                                 <tbody>
                                    <?php
                                                $query=mysqli_query($conn,"select * from tbl_expensescreation_master"); 
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
                                               
                                       
                                       
                                          
                                          <td><?php echo substr($user['date'],0,50); ?>
                                             
                                          </td>
                                          
                                          <td><?php echo substr($user['account_name'],0,50); ?>
                                             
                                          </td>
                                          <td><?php echo substr($user['regular_name'],0,50); ?>
                                             
                                          </td>
                                          
                                          <td><?php echo substr($user['exphead_name'],0,50); ?>
                                             
                                          </td>
                                          <td><?php echo substr($user['remarks'],0,50); ?>
                                             
                                          </td>
                                          <td><?php echo substr($user['paymode_name'],0,50); ?>
                                             
                                          </td>
                                          <td><?php echo substr($user['amount'],0,50); ?>
                                             
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

