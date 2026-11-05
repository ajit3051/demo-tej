(function ($) {
    "use strict";

    $("body").on("keyup", ".search-input", function (e) {
        var $this = $(this);
        var formid = $this.closest('form').attr('id');
        $("#" + formid).find('input[name=page]').val(1);
        $("#" + formid).find('input[name=export]').val('');
        getAjaxResults(formid);
    });

    $("body").on("change", ".from-date, .to-date", function (e) {
        var $this = $(this);
        var formid = $this.closest('form').attr('id');
        $("#" + formid).find('input[name=page]').val(1);
        $("#" + formid).find('input[name=export]').val('');
        getAjaxResults(formid);
    });

    // Pagination event
    $("body").on("click", ".page-link-v2", function (e) {
        e.preventDefault();
        var $this = $(this);
        var pageNo = $this.attr("href");

        var formid = $this.closest('section').find('form').attr('id');
        $("#" + formid).find('input[name=page]').val(pageNo);
        $("#" + formid).find('input[name=export]').val('');

        getAjaxResults(formid);
    });

    // Master checkbox toggle for bulk selection
    $("body").on("change", "#checkAllLatest", function () {
        var isChecked = $(this).prop("checked");
        $("#billwise-list-latest-results input[name='recent-chk[]']").prop("checked", isChecked);
    });

    $("body").on("change", "#checkAllMain", function () {
        var isChecked = $(this).prop("checked");
        $("#billwise-list-results input[name='recent-chk[]']").prop("checked", isChecked);
    });

}(jQuery));

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

// Initial fetch on page load
$(document).ready(function () {
    getAjaxResults('latestsearchForm');
    getAjaxResults('searchForm');
});

function getAjaxResults(formId) {
    var $form = $("#" + formId);
    if ($form.length === 0) return;

    var formData = $form.serialize();
    formData += '&formId=' + formId;
    var exp = $form.find('input[name=export]').val();

    if (exp) {
        var url = 'ajax/stock-transfer-results.php?function=get_results&' + formData;
        window.location.href = url;
        return;
    }

    $("#overlay").show();
    $.ajax({
        type: 'POST',
        url: 'ajax/stock-transfer-results.php?function=get_results',
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
                } catch (e) {
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
                if (formId === 'latestsearchForm') {
                    $("#billwise-list-latest-results").html(res.htmlData);
                    $("#pagination-latest-result").html(res.pagination);
                } else {
                    $("#billwise-list-results").html(res.htmlData);
                    $("#pagination-result").html(res.pagination);
                }

                $('#' + formId).find('.total-qty').text(res.total_qty);
                $('#' + formId).find('.total-gst').text(res.total_gst);
                $('#' + formId).find('.total-amt').text(res.total_amt);
            }

            $("#overlay").hide();
        },
        error: function (xhr, status, error) {
            console.error('AJAX Error:', error);
            $("#overlay").hide();
        }
    });
}

function view_details(id) {
    $('#common-popup').load('popup/view-purchase-details?id=' + id, function () {
        $('#common-popup').modal('show');
    });
}

function deleterecord(id) {
    $('#common-popup').load('popup/deleterecord', function () {
        $('#common-popup').modal('show');
        $('#common-popup').modal('show').one('click', '#confirm', function () {
            $('#deleteaction').val('delete');
            $('#deleteid').val(id);
            $('#deleteids').val('');
            $('#deleteform').submit();
        });
    });
}

function deleteRow(type) {
    var selector = (type === 'latestRecord')
        ? "#billwise-list-latest-results input[name='recent-chk[]']:checked"
        : "#billwise-list-results input[name='recent-chk[]']:checked";

    var checkedIds = [];
    $(selector).each(function () {
        var v = $(this).val();
        if (v) checkedIds.push(v);
    });

    if (checkedIds.length === 0) {
        $("input[name='recent-chk[]']:checked").each(function () {
            var v = $(this).val();
            if (v && checkedIds.indexOf(v) === -1) checkedIds.push(v);
        });
    }

    if (checkedIds.length === 0) {
        alert('Please select at least one record to delete.');
        return;
    }

    $('#common-popup').load('popup/deleterecord', function () {
        $('#common-popup').modal('show');
        $('#common-popup').modal('show').one('click', '#confirm', function () {
            $('#deleteaction').val('delete_bulk');
            $('#deleteid').val('');
            $('#deleteids').val(checkedIds.join(','));
            $('#deleteform').submit();
        });
    });
}

function sendMail(id) {
    $('#common-popup').load('popup/send-mail?id=' + encodeURIComponent(id), function () {
        $('#common-popup').modal('show');
        $('#common-popup').modal('show').one('click', '#confirm', function () {
            $('#mailid').val(id);
            $('#sendmailform').submit();
        });
    });
}

/* ── Stock Transfer Modal Functions ── */
window.openStockTransferModal = function (id) {
    $('#st_purchase_id').val(id || '');
    $('#st_alert_msg').hide().removeClass('alert-danger alert-success').text('');
    $('#st_stock_qty').val(1);
    $('#st_store_id').val('');
    $('#st_avail_qty_hint').text('');

    if (id) {
        $('#st_barcode_number').val('').prop('readonly', true);
        $('#st_item_name').val('').prop('readonly', true);
        $.ajax({
            url: 'get_ajaxdata?function=get_purchase_item_details',
            type: 'POST',
            dataType: 'json',
            data: { id: id },
            success: function (res) {
                if (res && res.status && res.data) {
                    $('#st_barcode_number').val(res.data.barcode_number || '');
                    $('#st_item_name').val(res.data.item_name || '');
                    var availQty = parseInt(res.data.qty || 1, 10);
                    $('#st_stock_qty').val(availQty).attr('max', availQty);
                    $('#st_avail_qty_hint').html('<i class="fa fa-info-circle text-primary"></i> Available Purchase Qty: <strong>' + availQty + '</strong>');
                }
            }
        });
    } else {
        // Quick Transfer / Scan Barcode
        $('#st_barcode_number').val('').prop('readonly', false).attr('placeholder', 'Enter or scan barcode...').focus();
        $('#st_item_name').val('').prop('readonly', true).attr('placeholder', 'Will auto-populate...');
        $('#st_stock_qty').val(1).removeAttr('max');
        $('#st_avail_qty_hint').html('<span class="text-muted"><i class="fa fa-barcode"></i> Enter Barcode above to fetch purchase item</span>');
    }

    $('#stockTransferModal').modal('show');
};

$(document).on('change blur', '#st_barcode_number', function () {
    var bc = $(this).val().trim();
    if (bc && !$('#st_purchase_id').val()) {
        $('#st_avail_qty_hint').html('<i class="fa fa-spinner fa-spin"></i> Checking item...');
        $.ajax({
            url: 'get_ajaxdata?function=get_purchase_item_details',
            type: 'POST',
            dataType: 'json',
            data: { barcode: bc },
            success: function (res) {
                if (res && res.status && res.data) {
                    $('#st_purchase_id').val(res.data.id || '');
                    $('#st_item_name').val(res.data.item_name || '');
                    var availQty = parseInt(res.data.qty || 1, 10);
                    $('#st_stock_qty').val(availQty).attr('max', availQty);
                    $('#st_avail_qty_hint').html('<i class="fa fa-check-circle text-success"></i> Available Purchase Qty: <strong>' + availQty + '</strong>');
                } else {
                    $('#st_purchase_id').val('');
                    $('#st_item_name').val('');
                    $('#st_avail_qty_hint').html('<span class="text-danger"><i class="fa fa-exclamation-circle"></i> Item not found for this barcode</span>');
                }
            },
            error: function () {
                $('#st_avail_qty_hint').html('<span class="text-danger"><i class="fa fa-exclamation-circle"></i> Error checking barcode</span>');
            }
        });
    }
});

$(document).on('submit', '#stockTransferForm', function (e) {
    e.preventDefault();
    var $btn = $('#st_btn_submit');
    var origHtml = $btn.html();
    $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Transferring...');

    var formData = $(this).serialize();
    $.ajax({
        url: 'get_ajaxdata?function=process_stock_transfer',
        type: 'POST',
        dataType: 'json',
        data: formData,
        success: function (res) {
            $btn.prop('disabled', false).html(origHtml);
            if (res && res.status) {
                $('#st_alert_msg').removeClass('alert-danger').addClass('alert alert-success').html('<i class="fa fa-check-circle"></i> ' + res.message).show();
                setTimeout(function () {
                    $('#stockTransferModal').modal('hide');
                    if (typeof getAjaxResults === 'function') {
                        getAjaxResults('latestsearchForm');
                        getAjaxResults('searchForm');
                    }
                }, 1200);
            } else {
                $('#st_alert_msg').removeClass('alert-success').addClass('alert alert-danger').html('<i class="fa fa-exclamation-triangle"></i> ' + (res.message || 'Transfer failed')).show();
            }
        },
        error: function () {
            $btn.prop('disabled', false).html(origHtml);
            $('#st_alert_msg').removeClass('alert-success').addClass('alert alert-danger').html('<i class="fa fa-exclamation-triangle"></i> Server error. Please try again.').show();
        }
    });
});

/* ── Charts ── */
function chartlist() {
    try {
        var ctx = document.getElementById("barChart");
        if (ctx && typeof Chart !== 'undefined') {
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ["January", "February", "March", "April", "May", "June", "July"],
                    datasets: [{
                        label: "Purchase Quantity",
                        data: [65, 59, 80, 81, 56, 55, 40],
                        borderColor: "rgba(0, 150, 136, 0.76)",
                        borderWidth: "0",
                        backgroundColor: "rgba(0, 150, 136, 0.76)"
                    },
                    {
                        label: "Stock Transferred",
                        data: [28, 48, 40, 19, 86, 27, 90],
                        borderColor: "rgba(240, 173, 78, 0.76)",
                        borderWidth: "0",
                        backgroundColor: "rgba(240, 173, 78, 0.76)"
                    }]
                },
                options: {
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true
                            }
                        }]
                    }
                }
            });
        }

        var ctxLine = document.getElementById("lineChart");
        if (ctxLine && typeof Chart !== 'undefined') {
            new Chart(ctxLine, {
                type: 'line',
                data: {
                    labels: ["January", "February", "March", "April", "May", "June", "July"],
                    datasets: [{
                        label: "Total Purchases",
                        borderColor: "rgba(0,0,0,.15)",
                        borderWidth: "1",
                        backgroundColor: "rgba(0,0,0,.05)",
                        data: [22, 44, 67, 43, 76, 45, 12]
                    },
                    {
                        label: "Stock Transferred",
                        borderColor: "rgba(0, 150, 136, 0.76)",
                        borderWidth: "1",
                        backgroundColor: "rgba(0, 150, 136, 0.15)",
                        pointHighlightStroke: "rgba(26,179,148,1)",
                        data: [16, 32, 18, 26, 42, 33, 44]
                    }]
                },
                options: {
                    responsive: true,
                    tooltips: {
                        mode: 'index',
                        intersect: false
                    },
                    hover: {
                        mode: 'nearest',
                        intersect: true
                    }
                }
            });
        }

        var ctxSingle = document.getElementById("singelBarChart");
        if (ctxSingle && typeof Chart !== 'undefined') {
            new Chart(ctxSingle, {
                type: 'bar',
                data: {
                    labels: ["Sun", "Mon", "Tu", "Wed", "Th", "Fri", "Sat"],
                    datasets: [{
                        label: "Daily Activity",
                        data: [40, 55, 75, 81, 56, 55, 40],
                        borderColor: "#009688",
                        borderWidth: "0",
                        backgroundColor: "rgba(0, 150, 136, 0.76)"
                    }]
                },
                options: {
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true
                            }
                        }]
                    }
                }
            });
        }
    } catch (err) {
        console.warn('Chart error caught:', err);
    }
}

try {
    chartlist();
} catch (e) { }
