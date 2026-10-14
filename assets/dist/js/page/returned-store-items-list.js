(function ($) {
    "use strict";

    $("body").on("keyup input", ".search-input", function (e) {
        var $this = $(this);
        var formid = $this.closest('form').attr('id');
        $("#" + formid).find('input[name=page]').val(1);
        $("#" + formid).find('input[name=export]').val('');
        getAjaxResults(formid);
    });

    $("body").on("change", ".from-date, .to-date, .store-filter", function (e) {
        var $this = $(this);
        var formid = $this.closest('form').attr('id');
        $("#" + formid).find('input[name=page]').val(1);
        $("#" + formid).find('input[name=export]').val('');
        getAjaxResults(formid);
    });

    $("body").on("click", ".page-link-v2", function (e) {
        e.preventDefault();
        var $this = $(this);
        var pageNo = $this.attr("href");
        var formid = $this.closest('section').find('form').attr('id');
        $("#" + formid).find('input[name=page]').val(pageNo);
        $("#" + formid).find('input[name=export]').val('');
        getAjaxResults(formid);
    });

    // Checkbox Master & Row Selection
    $("body").on("change", "#checkAll", function () {
        var isChecked = $(this).prop("checked");
        $(".chk-item").prop("checked", isChecked);
        updateSelectedCount();
    });

    $("body").on("change", ".chk-item", function () {
        var total = $(".chk-item").length;
        var checked = $(".chk-item:checked").length;
        $("#checkAll").prop("checked", total > 0 && total === checked);
        updateSelectedCount();
    });

}(jQuery));

function showToast(message, type) {
    type = type || 'success';
    var bgClass = type === 'success' ? 'bg-success' : 'bg-danger';
    var barClass = type === 'success' ? 'success' : 'danger';
    
    var $container = $('#toastsContainerTopRight');
    if ($container.length === 0) {
        $container = $('<div id="toastsContainerTopRight" class="toasts-top-right fixed"></div>');
        $('body').append($container);
    }
    
    var toastHtml = '<div class="toast ' + bgClass + ' show" role="alert" aria-live="assertive" aria-atomic="true" style="cursor: pointer; margin-bottom: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); border-radius: 4px; overflow: hidden; transition: opacity 0.3s ease, transform 0.3s ease;">' +
        '<div class="toast-body" style="position: relative; padding: 12px 35px 12px 15px;">' +
            '<button data-dismiss="toast" type="button" class="close" aria-label="Close" style="position: absolute; top: 8px; right: 10px; color: #fff; opacity: 0.9; background: transparent; border: none; font-size: 20px; outline: none; cursor: pointer;">' +
                '<span aria-hidden="true">&times;</span>' +
            '</button>' +
            '<div class="tst-msg" style="min-width: unset; max-width: 100%; font-size: 15px; word-break: break-word; color: #fff;">' + message + '</div>' +
        '</div>' +
        '<div class="progress" style="height: 4px; background: rgba(255,255,255,0.3);">' +
            '<div class="progress-bar ' + barClass + '" role="progressbar" data-width="0" style="width: 0%; background-color: #fff; transition: width 0.08s linear;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>' +
        '</div>' +
    '</div>';
    
    var $toast = $(toastHtml);
    $container.append($toast);
    
    var toastEl = $toast[0];
    var progressInterval = setInterval(function () {
        if (!toastEl || toastEl.dataset.removing) {
            clearInterval(progressInterval);
            return;
        }
        var bar = toastEl.querySelector('.progress-bar');
        if (!bar) return;
        var dw = parseInt(bar.getAttribute('data-width') || '0', 10);
        if (dw < 100) {
            var count = dw + 2;
            bar.setAttribute('data-width', count);
            bar.style.width = count + '%';
        } else {
            clearInterval(progressInterval);
            removeToastEl(toastEl);
        }
    }, 80);
    
    $toast.find('.close, .toast-body').on('click', function () {
        clearInterval(progressInterval);
        removeToastEl(toastEl);
    });
}

function removeToastEl(toast) {
    if (!toast || toast.dataset.removing) return;
    toast.dataset.removing = 'true';
    toast.style.opacity = '0';
    toast.style.transform = 'translateY(-10px)';
    setTimeout(function () {
        if (toast.parentNode) {
            toast.parentNode.removeChild(toast);
        }
    }, 300);
}

function updateSelectedCount() {
    var count = $(".chk-item:checked").length;
    $("#selectedCount").text(count);
    if (count > 0) {
        $("#deleteSelectedBtn").show();
    } else {
        $("#deleteSelectedBtn").hide();
    }
}

function exportExcel(element, type) {
    $('#common-popup').load('popup/export?type=' + type, function () {
        $('#common-popup').modal('show');
        $('#common-popup').modal('show').one('click', '#confirm', function () {
            var formid = $(element).closest('.panel-body').find('form').attr('id');
            $("#" + formid).find('input[name=export]').val(type);
            getAjaxResults(formid);
            $('#common-popup').modal('hide');
        });
    });
}

$(document).ready(function () {
    getAjaxResults('searchForm');
});

function getAjaxResults(formId) {
    var formData = $("#"+formId).serialize();
    var exp = $("#"+formId).find('input[name=export]').val();

    if(exp){
        const url = 'ajax/returned-store-items-results.php?function=get_results&' + formData;
        window.location.href = url;
        return;
    }

    $("#overlay").show();
    $.ajax({
        type: 'POST',
        url: 'ajax/returned-store-items-results.php?function=get_results',
        data: formData,
        success: function (response) {
            var res = response;
            if (typeof response === 'string') {
                if (response.indexOf('!DOCTYPE html') !== -1) {
                    location.reload();
                    return;
                }
                try {
                    res = JSON.parse(response);
                } catch(e) {
                    console.error('Invalid JSON response', e);
                    $("#overlay").hide();
                    return;
                }
            }

            if (res && res.isLogged === 'false') {
                location.reload();
                return;
            }

            if (res) {
                $("#returned-store-items-results").html(res.htmlData);
                $("#pagination-result").html(res.pagination);
                $('#'+formId).find('.total-qty').text(res.total_qty || 0);
                $('#'+formId).find('.total-gst').text(res.total_gst || '0.00');
                $('#'+formId).find('.total-amt').text(res.total_amt || '0.00');
            }

            $("#checkAll").prop("checked", false);
            updateSelectedCount();

            $("#overlay").hide();
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', error);
            $("#overlay").hide();
        }
    });
}

function viewStoreItemDetails(id) {
    if (!id) return;
    $('#common-popup').load('popup/view-store-item-details.php?id=' + id, function () {
        $('#common-popup').modal('show');
    });
}

function deleterecord(id) {
    $('#common-popup').load('popup/deleterecord', function () {
        $('#common-popup').modal('show');
        $('#common-popup').modal('show').one('click', '#confirm', function () {
            $('#deleteaction').val('delete');
            $('#deleteid').val(id);
            $('#deleteform').submit();
        });
    });
}

function deleteSelectedRecords() {
    var selectedIds = [];
    $('.chk-item:checked').each(function () {
        selectedIds.push($(this).val());
    });
    if (selectedIds.length === 0) {
        showToast('Please select at least one record to delete.', 'error');
        return;
    }
    $('#common-popup').load('popup/deleterecord', function () {
        $('#common-popup').modal('show');
        $('#common-popup').modal('show').one('click', '#confirm', function () {
            $('#deleteaction').val('delete_bulk');
            $('#deleteids').val(selectedIds.join(','));
            $('#deleteform').submit();
        });
    });
}
