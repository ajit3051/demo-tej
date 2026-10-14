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
               <div class="row" style="margin-bottom: 10px;">
               <?php include('include/menu-header.php');?> 
                </div>
            </div>
            <!-- Main content -->
            


            
            <!-- /.content -->
         </div>
         <!-- /.content-wrapper -->
         <footer class="main-footer" style="background-color:white;">
            <?php include('include/footer.php');?>  
         </footer>
         <?php include('include/Sidenavbuttons.php');?> 
      </div>
     <?php include('include/js.php');?> 
      
   </body>

</html>

