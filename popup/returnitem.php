<div class="modal-dialog">
	<div class="modal-content" id="pop-detail-content">
		<div class="modal-header modal-header-primary" style="background-color: #f39c12; color: #fff; border-top-left-radius: 4px; border-top-right-radius: 4px; padding: 12px 15px;">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="color: #fff; opacity: 0.9;">×</button>
			<h4 class="modal-title" style="color: #fff; font-size: 16px; font-weight: 600; margin: 0;"><i class="fa fa-undo" style="margin-right: 8px;"></i> Return Item Confirmation</h4>
		</div>
		<div class="modal-body text-center" style="padding: 25px 20px;">
			<input type="hidden" id="modal_return_item_id" value="<?= htmlspecialchars($_REQUEST['id'] ?? $_GET['id'] ?? '') ?>">
			<div style="margin-bottom: 15px;">
				<span style="display: inline-block; width: 60px; height: 60px; line-height: 60px; border-radius: 50%; background-color: #fef5e7; color: #f39c12;">
					<i class="fa fa-reply fa-2x" style="vertical-align: middle;"></i>
				</span>
			</div>
			<p class="h4" style="color: #2c3e50; font-weight: 600; margin-bottom: 0;">Are you sure you want to return this item to stock?</p>
		</div>
		<div class="modal-footer text-center" style="background-color: #f9fafb; border-top: 1px solid #eef2f5; text-align: center; padding: 12px 15px;">
			<button type="button" class="btn btn-default" data-dismiss="modal" style="min-width: 110px; font-weight: 500;">Cancel</button>
			<button type="button" class="btn btn-warning" id="confirmReturn" style="min-width: 110px; font-weight: 600; background-color: #f39c12; border-color: #e08e0b; color: #fff;">Confirm Return</button>
		</div>
	</div>
</div>
