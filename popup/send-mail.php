<?php include_once("../include/config.php"); ?>
<?php
$validationHelper = new validation();

$db = connect();
$bill_no = $_GET['id'] ?? '';
$mobile_number = '';
if ($bill_no) {
   $pur_stmt = $db->select("SELECT * FROM tbl_bill WHERE bill_number=?", 's', $bill_no);
   if ($pur_stmt) {
      $res = $pur_stmt->fetch_assoc();
      if (is_array($res)) {
         foreach ($res as $key => $value) {
            $$key = $validationHelper->filterText($value);
         }
      }
      $pur_stmt->close();
   }
}
?>
 <div class="modal-dialog">
 	<div class="modal-content" id="pop-detail-content">
 		<div class="modal-header  modal-header-primary">
 			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
 			<h3 class="text-danger"><i class="icon fa fa-exclamation-triangle"></i> Alert!</h3>

 		</div>
 		<div class="modal-body">

 			<form action="" id="sendmailform" method="post" autocomplete="off">
 				<div class="row">
 					<!-- Text input-->
 					<div class="col-md-3">
 						<label class="control-label">Mobile:</label>
 						<p><?= $mobile_number ?></p>
 					</div>
 					<div class="col-md-9">
 						<label class="control-label">Email:</label>
 						<input type="email" class="form-control" name="account_email" value="<?= getAccountEmailBymobile($mobile_number) ?>" required>
 					</div>
 				</div>
 				<input type="hidden" name="action" value="send-mail">
 				<input type="hidden" name="id" id="mailid">
 			</form>
			 <p class="h5 mt-2">Are you sure to send mail this record(s)?</p>
 		</div>
 		<div class="modal-footer justify-content-center">
 			<button type="button" class="btn btn-secondary cancel" data-dismiss="modal">Cancel</button>
 			<button type="button" class="btn btn-primary" id="confirm">Confirm</button>
 		</div>
 	</div>
 </div>