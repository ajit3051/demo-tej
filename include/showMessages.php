<div id="toastsContainerTopRight" class="toasts-top-right fixed">
	<?php if (!empty($errorMessageArr) && (is_array($errorMessageArr) || is_object($errorMessageArr))) { ?>
		<?php foreach ($errorMessageArr as $errorMessage) { ?>
			<div class="toast bg-danger show" role="alert" aria-live="assertive" aria-atomic="true" style="cursor: pointer; margin-bottom: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); border-radius: 4px; overflow: hidden; transition: opacity 0.3s ease, transform 0.3s ease;">
				<div class="toast-body" style="position: relative; padding: 12px 35px 12px 15px;">
					<button data-dismiss="toast" type="button" class="close" aria-label="Close" style="position: absolute; top: 8px; right: 10px; color: #fff; opacity: 0.9; background: transparent; border: none; font-size: 20px; outline: none; cursor: pointer;">
						<span aria-hidden="true">&times;</span>
					</button>
					<div class="tst-msg" style="min-width: unset; max-width: 100%; font-size: 15px; word-break: break-word; color: #fff;"><?= xssSafe($errorMessage); ?></div>
				</div>
				<div class="progress" style="height: 4px; background: rgba(255,255,255,0.3);">
					<div class="progress-bar danger" role="progressbar" data-width="0" style="width: 0%; background-color: #fff; transition: width 0.08s linear;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
				</div>
			</div>
		<?php } ?>
	<?php } ?>
	
	<?php if (!empty($_SESSION['showMessages'])) { ?>
		<div class="toast bg-<?= empty($_SESSION['errorMessages']) ? 'success' : 'danger' ?> show" role="alert" aria-live="assertive" aria-atomic="true" style="cursor: pointer; margin-bottom: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); border-radius: 4px; overflow: hidden; transition: opacity 0.3s ease, transform 0.3s ease;">
			<div class="toast-body" style="position: relative; padding: 12px 35px 12px 15px;">
				<button data-dismiss="toast" type="button" class="close" aria-label="Close" style="position: absolute; top: 8px; right: 10px; color: #fff; opacity: 0.9; background: transparent; border: none; font-size: 20px; outline: none; cursor: pointer;">
					<span aria-hidden="true">&times;</span>
				</button>
				<div class="tst-msg" style="min-width: unset; max-width: 100%; font-size: 15px; word-break: break-word; color: #fff;"><?= xssSafe($_SESSION['showMessages']); ?></div>
			</div>
			<div class="progress" style="height: 4px; background: rgba(255,255,255,0.3);">
				<div class="progress-bar <?= empty($_SESSION['errorMessages']) ? 'success' : 'danger' ?>" role="progressbar" data-width="0" style="width: 0%; background-color: #fff; transition: width 0.08s linear;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
			</div>
		</div>
	<?php } ?>
	<?php 
	$_SESSION['showMessages'] = ''; 
	$_SESSION['errorMessages'] = ''; 
	?>
</div>
<script>
(function() {
    function removeToast(toast) {
        if (!toast || toast.dataset.removing) return;
        toast.dataset.removing = 'true';
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(-10px)';
        setTimeout(function() {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        }, 300);
    }

    function initToasts() {
        var container = document.getElementById('toastsContainerTopRight');
        if (!container) return;

        // Dismiss toast on click (either x button or toast body)
        container.addEventListener('click', function(e) {
            var toast = e.target.closest('.toast');
            if (toast) {
                removeToast(toast);
            }
        });

        // Progress bar timer (4 seconds total duration)
        var timer = setInterval(function() {
            var toasts = container.querySelectorAll('.toast');
            if (toasts.length === 0) {
                clearInterval(timer);
                return;
            }
            for (var i = 0; i < toasts.length; i++) {
                var toast = toasts[i];
                if (toast.dataset.removing) continue;
                var bar = toast.querySelector('.progress-bar');
                if (!bar) continue;
                var dw = parseInt(bar.getAttribute('data-width') || '0', 10);
                if (dw < 100) {
                    var count = dw + 2;
                    bar.setAttribute('data-width', count);
                    bar.style.width = count + '%';
                } else {
                    removeToast(toast);
                }
            }
        }, 80);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initToasts);
    } else {
        initToasts();
    }
})();
</script>