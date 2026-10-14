<!-- store-profile-creation-start -->

<!-- store-profile-details-start -->
<!-- model Modal1 -->
<div class="modal fade" id="addtrain2" tabindex="-1" role="dialog" aria-hidden="true">
   <div class="modal-body">
      <div class="modal-content">
         <div class="modal-header modal-header-primary">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h3><i class="fa fa-plus m-r-5"></i> Store Profile Name Details</h3>
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
                              <input type="password" name="store_code" class="form-control" id="store_code" readonly value="<?php @$no = mysqli_query($conn, "SELECT id FROM  tbl_store_master ORDER BY id DESC");
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
<!-- store-profile-details-end -->
<!-- itemmaster to groupmaster start-->
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
                     <br><br>
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
<!-- itemmaster to groupmaster end-->
<!-- discount-master-creation- festival name-end -->
<!-- model Modal1 -->
<div class="modal fade" id="festival" tabindex="-1" role="dialog" aria-hidden="true">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header modal-header-primary">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h3><i class="fa fa-plus m-r-5"></i> Add New Festival Name Creation</h3>
         </div>
         <div class="modal-body">
            <div class="row">
               <div class="col-md-12">
                  <form method="post">
                     <?php
                        $msg='';
                         if(isset($_POST['submit_btn']))
                         {
                          $fest_code=$_POST['fest_code'];
                          $fest_name=$_POST['fest_name'];
                          $status=$_POST['status'];
                          
                              $dup=mysqli_query($conn,"select * from tbl_festival_master where fest_name='$fest_name'");
                                if(mysqli_num_rows($dup)>0)
                                {
                                    $msg='<div class="alert alert-danger alert-dismissible fade1 show">
                                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                                        <strong>Festival Master Already exist!</strong>
                                        </div>';
                                }
                                else{
                                    $query="Insert into tbl_store_master(fest_code,fest_name,location_name,status)values('$fest_code','$fest_name','$status')";
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
                              <label>Festival Code</label>
                              <input type="password" name="fest_code" class="form-control" id="fest_code" readonly value="<?php @$no = mysqli_query($conn, "SELECT id FROM  tbl_festival_master ORDER BY id DESC");
                                 $po_no = mysqli_fetch_assoc($no);
                                 echo 'FC'.($po_no['id']+1);
                                 ?>">
                           </div>
                        </div>
                        <div class="col-sm-6">
                           <div class="form-group">
                              <label>Festival Name</label>
                              <input type="text" class="form-control" name="fest_name" id="fest_name" placeholder="Enter Your Festival Name" required>
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
<!-- discount-master-creation- festival name-end -->