<?php
$type = $_GET['type'];
?>
<div class="modal-dialog">
 	<div class="modal-content" id="pop-detail-content">
 		<div class="modal-header  modal-header-primary">
		 <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
 			<h3 class="text-danger"><i class="icon fa fa-exclamation-triangle"></i> Alert!</h3>
 			
 		</div>
 		<div class="modal-body">
 			<p class="h5">Are you sure to download <?= $type=='excel' ? 'excel' : 'pdf' ?>?</p>
 		</div>
 		<div class="modal-footer justify-content-center">
 			<button type="button" class="btn btn-secondary cancel" data-dismiss="modal">Cancel</button>
 			<button type="button" class="btn btn-primary" id="confirm">Confirm</button>
 		</div>
 	</div>
 </div>