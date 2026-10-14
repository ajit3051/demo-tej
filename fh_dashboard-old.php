<!DOCTYPE html>
<html lang="en">
   
<head>
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <title>Dashboard || Fashion Hub </title>
       <?php include('include/css.php');?> 

 
   </head>

   <body class="hold-transition sidebar-mini">
      
      <!--preloader-->
  <!--    <div id="preloader">
         <div id="status"></div>
      </div>  -->
      <!-- Site wrapper -->

      <div class="wrapper">
         <?php include('include/header-top-bar.php');?>
         <?php include('include/notification.php');?>
         <!-- =============================================== -->
         <!-- Left side column. contains the sidebar -->
         <?php include('include/sidebar-left.php');?>

         <!-- =============================================== -->
         <!-- Content Wrapper. Contains page content -->
         <div class="content-wrapper">
            <!-- Content Header (Page header) -->

            <section class="content-header">
               <?php include('include/fh_menuheader.php');?>
                  </section>

            <div class="container-fluid">
               <div class="row" style="margin-bottom: 5px;">
               
                </div>
            </div>
            <!-- Main content -->
            
            <section class="container-fluid">
               <?php include('include/home-card-box.php');?>

               <div class="container-fluid">
               <div class="row">
                  <label><b>Graph Reports For Sale</b></label>
               </div>
            </div>
            <?php include('include/barchart_dashboard.php');?>
               <?php include('include/dashboard_card_attendance_management.php');?>
               <!-- /.row -->
               <div class="row">
                 <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
                     <div class="panel panel-bd lobidisable">
                        <div class="panel-heading sidebar-toggle" data-toggle="offcanvas" role="button">
                           <div class="panel-title">
                              <h5>Pending Works <span style="font-size: 10px;" class="btn-rounded"><i class="pe-7s fa-spin"></i></span></h5>
                           </div>
                        </div>
                        <div class="panel-body">
                           <div class="Pendingwork">
                              <span class="label-warning label label-default pull-right">progressing</span>
                              <i class="fa fa-ban"></i>
                              <a href="#">Database tools</a>                          
                              <div class="upworkdate">
                                 <p>Jul 25, 2017 for Alimul Alrazy</p>
                              </div>
                           </div>
                           <div class="Pendingwork">
                              <span class="label-success label label-default pull-right">success</span>
                              <i class="fa fa-ban"></i>
                              <a href="#">Cabels</a>                          
                              <div class="upworkdate">
                                 <p>Jul 25, 2017 for Alimul</p>
                              </div>
                           </div>
                           <div class="Pendingwork">
                              <span class="label-danger label label-default pull-right">Failed</span>
                              <i class="fa fa-ban"></i>
                              <a href="#">Technologycal tools</a>                          
                              <div class="upworkdate">
                                 <p>Feb 25, 2017 for Alrazy</p>
                              </div>
                           </div>
                           <div class="Pendingwork">
                              <span class="label-warning label label-default pull-right">progressing</span>
                              <i class="fa fa-ban"></i>
                              <a href="#">Transaction</a>                          
                              <div class="upworkdate">
                                 <p>apr 25, 2017 for Mahfuz</p>
                              </div>
                           </div>
                           <div class="Pendingwork">
                              <span class="label-success label label-default pull-right">success</span>
                              <i class="fa fa-ban"></i>
                              <a href="#">Training tools</a>                          
                              <div class="upworkdate">
                                 <p>jun 25, 2017 for Alrazy</p>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>

                  <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
                     <div class="panel panel-bd lobidisable">
                        <div class="panel-heading sidebar-toggle" data-toggle="offcanvas" role="button">
                           <div class="panel-title">
                              <h5>Notice Board <span style="font-size: 10px;" class="btn-rounded"><i class="pe-7s fa-spin"></i></span></h5>
                           </div>
                        </div>
                        <div class="panel-body">
                           <div class="Workslist">
                              <div class="worklistdate">
                                 <table class="table table-hover">
                                    <thead>
                                       <tr>
                                          <th>Notice</th>
                                          <th>Published By</th>
                                          <th>Date Added</th>
                                       </tr>
                                    </thead>
                                    <tbody>
                                       <tr class="info">
                                          <td>new notice</td>
                                          <td>Mr. Alrazy</td>
                                          <td>20th April 2017</td>
                                       </tr>
                                       <tr>
                                          <td>Urgent notice</td>
                                          <td>Mr. Alrazy</td>
                                          <td>20th june 2017</td>
                                       </tr>
                                       <tr>
                                          <td>Urgent notice</td>
                                          <td>Mr. Jahir</td>
                                          <td>26th june 2017</td>
                                       </tr>
                                       
                                       
                                    </tbody>
                                 </table>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
                     <div class="panel panel-bd lobidisable">
                        <div class="panel-heading sidebar-toggle" data-toggle="offcanvas" role="button">
                           <div class="panel-title">
                              <h5>Upcoming Events <span style="font-size: 10px;" class="btn-rounded"><i class="pe-7s fa-spin"></i></span></h5>
                           </div>
                        </div>
                        <div class="panel-body">
                           <div class="work-touchpoint">
                              <div class="work-touchpoint-date">
                                 <span class="day">28</span>
                                 <span class="month">Apr</span>
                              </div>
                           </div>
                           <div class="detailswork">
                              <span class="label-custom label label-default pull-right">Email</span>
                              <a href="#" title="headings">Marketing policy</a> <br>
                              <p>Green Road - Dhaka,Bangladesh</p>
                           </div>
                           <div class="work-touchpoint">
                              <div class="work-touchpoint-date">
                                 <span class="day">2</span>
                                 <span class="month">Apr</span>
                              </div>
                           </div>
                           <div class="detailswork">
                              <span class="label-custom label label-default pull-right">skype</span>
                              <a href="#" title="headings">Accounting policy</a> <br>
                              <p>Kolkata, India</p>
                           </div>
                           <div class="work-touchpoint">
                              <div class="work-touchpoint-date2">
                                 <span class="day">17</span>
                                 <span class="month">Mrc</span>
                              </div>
                           </div>
                           <div class="detailswork">
                              <span class="label-custom label label-default pull-right">phone</span>
                              <a href="#" title="headings">Marketing policy</a> <br>
                              <p>Madrid  - spain</p>
                           </div>
                           <div class="work-touchpoint">
                              <div class="work-touchpoint-date2">
                                 <span class="day">3</span>
                                 <span class="month">jan</span>
                              </div>
                           </div>
                           <div class="detailswork">
                              <span class="label-custom label label-default pull-right">Mobile</span>
                              <a href="#" title="headings">Finance policy</a> <br>
                              <p>south Australia  - Australia</p>
                           </div>
                        </div>
                     </div>
                  </div>
                  
               </div>
               
               
               <div class="row">
                  <div class="col-xs-12 col-sm-8">
                     <div class="panel panel-bd lobidrag">
                        <div class="panel-heading sidebar-toggle" data-toggle="offcanvas" role="button">
                           <div class="panel-title">
                              <h5>Google Map <span style="font-size: 10px;" class="btn-rounded"><i class="pe-7s fa-spin"></i></span></h5>
                           </div>
                        </div>
                        <div class="panel-body">
                           <div class="google-maps">
                              <iframe src="https://maps.google.co.uk/maps?f=q&amp;source=s_q&amp;hl=en&amp;geocode=&amp;q=15+Springfield+Way,+Hythe,+CT21+5SH&amp;aq=t&amp;sll=52.8382,-2.327815&amp;sspn=8.047465,13.666992&amp;ie=UTF8&amp;hq=&amp;hnear=15+Springfield+Way,+Hythe+CT21+5SH,+United+Kingdom&amp;t=m&amp;z=14&amp;ll=51.077429,1.121722&amp;output=embed"></iframe>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="col-xs-12 col-sm-4">
                     <div class="panel panel-bd lobidrag">
                        <div class="panel-heading sidebar-toggle" data-toggle="offcanvas" role="button">
                           <div class="panel-title">
                              <h5>Calender <span style="font-size: 10px;" class="btn-rounded"><i class="pe-7s fa-spin"></i></span></h5>
                           </div>
                        </div>
                        <!-- Monthly calender widget -->
                        <div class="panel panel-bd">
                           <div class="panel-body">
                              <div class="monthly_calender">
                                 <div class="monthly" id="m_calendar"></div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
                
            </section>


            
            <!-- /.content -->
         </div>
         <!-- /.content-wrapper -->
         <footer class="main-footer" style="background-color:#009688;">
            <?php include('include/footer.php');?>  
         </footer>
         <?php include('include/Sidenavbuttons.php');?> 
      </div>
      <!-- /.wrapper -->
      <!-- Start Core Plugins
         =====================================================================-->
      <!-- jQuery -->
     <?php include('include/js.php');?> 


      
      

      
   </body>

</html>

