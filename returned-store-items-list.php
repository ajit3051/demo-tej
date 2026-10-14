<?php include_once("include/config.php"); ?>
<?php
   $validationHelper = new validation();
   $db = connect();
   
   $action = $_POST['action'] ?? '';
   $post_id = $_POST['id'] ?? '';
   $post_ids = $_POST['ids'] ?? '';
   
   if ($action == 'delete' && $post_id != '') {
      $id = (int)$post_id;
      if ($id) {
         $res = $db->delete("DELETE FROM tbl_store_items WHERE id=?", 'i', $id);
         if ($res) {
            $msg = 'Record deleted successfully';
            $code = '';
         } else {
            $msg = 'Failed!';
            $code = '1';
         }
      } else {
         $msg = "Something went wrong, Try again!";
         $code = '1';
      }
      redirectTo('returned-store-items-list.php', $msg, $code);
   } else if ($action == 'delete_bulk' && $post_ids != '') {
      $ids_arr = array_map('intval', explode(',', $post_ids));
      $ids_clean = array_filter($ids_arr, function($v){ return $v > 0; });
      if (!empty($ids_clean)) {
         $in_sql = implode(',', $ids_clean);
         $res = $db->query("DELETE FROM tbl_store_items WHERE id IN ($in_sql)");
         if ($res) {
            $msg = count($ids_clean) . ' Record(s) deleted successfully';
            $code = '';
         } else {
            $msg = 'Failed to delete selected records!';
            $code = '1';
         }
      } else {
         $msg = "Invalid records selected!";
         $code = '1';
      }
      redirectTo('returned-store-items-list.php', $msg, $code);
   }

   $stores_query = "SELECT id, store_name, location_name FROM tbl_store_master WHERE status = 'true' ORDER BY store_name ASC";
   $stores_res = $db->query($stores_query);
   $stores_list = array();
   if ($stores_res) {
       while ($s = $stores_res->fetch_assoc()) {
           $stores_list[] = $s;
       }
   }
?>
<?php include('include/header.php'); ?>

<section class="content">
   <div class="row">
      <div class="col-sm-12">
         <div class="panel panel-bd lobidisable">
            <div class="panel-heading sidebar-toggle" data-toggle="offcanvas">
               <div class="btn-group" id="buttonexport">
                  <a href="#">
                     <h5><span style="margin-left:10px; color:#37475a;">Returned Store Items (moved in stock)</span></h5>
                  </a>
               </div>
            </div>
            <div class="panel-body form-scroll">
               <div class="btn-group" style="margin-bottom: 15px;">
                  <a href="store-items-list.php" class="btn btn-exp btn-sm" style="background-color:#37475a; color:#fff; border-radius: 50px 0 0 50px;"><i class="fa fa-list"></i><span style="margin-left:5px;">Store Transfer List</span></a>
                  <div class="btn-group">
                     <button class="btn btn-exp btn-sm dropdown-toggle" data-toggle="dropdown" style="background-color:#37475a; color:#fff; border-radius: 0 50px 50px 0;"><i class="fa fa-download"></i> Export Table Data</button>
                     <ul class="dropdown-menu exp-drop" role="menu">
                        <li>
                           <a role="button" onclick="exportExcel(this, 'excel');">
                           <img src="assets/dist/img/xls.png" width="24" alt="logo"> Export To Excel</a>
                        </li>
                        <li>
                           <a role="button" onclick="exportExcel(this, 'pdf');">
                           <img src="assets/dist/img/pdf.png" width="24" alt="logo"><span style="margin-left:5px;">Export To PDF</span></a>
                        </li>
                     </ul>
                  </div>
                  <button type="button" id="deleteSelectedBtn" onclick="deleteSelectedRecords();" class="btn btn-danger btn-sm" style="border-radius: 50px; margin-left: 10px; display: none;">
                     <i class="fa fa-trash"></i> Delete Selected (<span id="selectedCount">0</span>)
                  </button>
               </div>

               <form name="searchForm" id="searchForm" method="post" class="search-form" action="" autocomplete="off">
                  <input type="hidden" name="page" value="1" />
                  <input type="hidden" name="export" value="" />
                  <input type="hidden" name="sortOrder" value="DESC" />
                  <input type="hidden" name="sortField" value="id" />

                  <div class="row" style="margin-bottom: 15px;">
                     <div class="col-md-2 col-sm-4">
                        <div class="form-group">
                           <label><small>Total Qty</small></label>
                           <p class="form-control bg-light total-qty" style="font-weight:600; margin-bottom:0;">0</p>
                        </div>
                     </div>
                     <div class="col-md-2 col-sm-4">
                        <div class="form-group">
                           <label><small>Total GST</small></label>
                           <p class="form-control bg-light total-gst" style="font-weight:600; margin-bottom:0;">0.00</p>
                        </div>
                     </div>
                     <div class="col-md-2 col-sm-4">
                        <div class="form-group">
                           <label><small>Total Amt</small></label>
                           <p class="form-control bg-light total-amt" style="font-weight:600; margin-bottom:0;">0.00</p>
                        </div>
                     </div>
                     <div class="col-md-2 col-sm-4">
                        <div class="form-group">
                           <label><small>Filter Store</small></label>
                           <select name="store_id" class="form-control store-filter" style="font-size:12px;">
                              <option value="">All Stores</option>
                              <?php foreach ($stores_list as $st) { ?>
                                 <option value="<?= $st['id'] ?>"><?= htmlspecialchars($st['store_name']) ?></option>
                              <?php } ?>
                           </select>
                        </div>
                     </div>
                     <div class="col-md-2 col-sm-4">
                        <div class="form-group">
                           <label><small>From Date</small></label>
                           <input type="text" name="from_date" id="from_date" class="form-control from-date dtpicker" placeholder="Enter Date...">
                        </div>
                     </div>
                     <div class="col-md-2 col-sm-4">
                        <div class="form-group">
                           <label><small>Search</small></label>
                           <input type="text" class="form-control search-input" placeholder="Search..." name="search">
                        </div>
                     </div>
                  </div>
               </form>

               <div class="table-responsive">
                  <table id="dataTableMain" class="table table-bordered table-striped table-hover">
                     <thead>
                        <tr class="info">
                           <th style="white-space:nowrap;"><input type="checkbox" id="checkAll" style="cursor:pointer; vertical-align:middle; margin-right:5px;"> SrNo</th>
                           <th>Action</th>
                           <th>Status</th>
                           <th>Store Name</th>
                           <th>Return Date</th>
                           <th>Bill No</th>
                           <th>Invoice No</th>
                           <th>Party Name</th>
                           <th>Barcode No</th>
                           <th>Item Name</th>
                           <th>Category</th>
                           <th>Brand</th>
                           <th>Size</th>
                           <th>Returned Qty</th>
                           <th>MOU</th>
                           <th>Purchase Price</th>
                           <th>GST %</th>
                           <th>GST Amt</th>
                           <th>Taxable Amt</th>
                           <th>Total Amount</th>
                        </tr>
                     </thead>
                     <tbody id="returned-store-items-results">
                     </tbody>
                  </table>
               </div>
               
               <div class="row" style="margin-top: 15px;">
                  <div class="col-sm-8">
                     <div id="pagination-result"></div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</section>

<form action="" id="deleteform" method="post" autocomplete="off">
   <input type="hidden" name="action" id="deleteaction" value="delete">
   <input type="hidden" name="id" id="deleteid">
   <input type="hidden" name="ids" id="deleteids">
</form>

<?php include('include/footer-2.php'); ?>
</div>

<script src="assets/dist/js/page/returned-store-items-list.js?v=<?= time() ?>"></script>
