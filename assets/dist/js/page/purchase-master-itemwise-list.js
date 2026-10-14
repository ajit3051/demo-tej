(function ($) {
    
   $("body").on("keyup", ".search-input", function (e) {
      var $this = $(this);
      var formid = $this.closest('form').attr('id');
      $("#"+formid).find('input[name=page]').val(1);
      $("#"+formid).find('input[name=export]').val('');
      getAjaxResults(formid);
   });

   $("body").on("change", ".from-date", function (e) {
      var $this = $(this);
      var formid = $this.closest('form').attr('id');
      $("#"+formid).find('input[name=page]').val(1);
      $("#"+formid).find('input[name=export]').val('');
      getAjaxResults(formid);
   });

   $("body").on("change", ".to-date", function (e) {
      var $this = $(this);
      var formid = $this.closest('form').attr('id');
      $("#"+formid).find('input[name=page]').val(1);
      $("#"+formid).find('input[name=export]').val('');
      getAjaxResults(formid);
   });

    // Pagination event
    $("body").on("click", ".page-link-v2", function (e) {
        e.preventDefault();
        var $this = $(this);
        var pageNo = $this.attr("href");

        var formid = $this.closest('section').find('form').attr('id');
        $("#"+formid).find('input[name=page]').val(pageNo);
        $("#"+formid).find('input[name=export]').val('');
    
        getAjaxResults(formid);
    });


}(jQuery));

function exportExcel(element, type) {
   
   $('#common-popup').load('popup/export?type='+type,
      function() {
         $('#common-popup').modal('show');
         $('#common-popup').modal('show').one('click', '#confirm', function() {
            var formid = $(element).closest('.panel-body').find('form').attr('id');
            $("#"+formid).find('input[name=export]').val(type);
            getAjaxResults(formid);
            $('#common-popup').modal('hide');
         });
      });
}

getAjaxResults('latestsearchForm');
getAjaxResults('searchForm');
function getAjaxResults(formId) {

    var formData = $("#"+formId).serialize();
    formData += '&formId=' + formId;
    var exp = $("#"+formId).find('input[name=export]').val();

    if(exp){
      
      const url = 'ajax/purchase-itemwise-results.php?function=get_results&' + formData;
      window.location.href = url; // Redirect to the PHP script to trigger the download
    }
    //alert(formData);
    $("#overlay").show();
    $.ajax({
        type: 'POST',
        url: 'ajax/purchase-itemwise-results.php?function=get_results',
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
                if(formId == 'latestsearchForm'){
                    $("#billwise-list-latest-results").html(res.htmlData);
                    $("#pagination-latest-result").html(res.pagination);
                } else {
                    $("#billwise-list-results").html(res.htmlData);
                    $("#pagination-result").html(res.pagination);
                }

                $('#'+formId).find('.total-qty').text(res.total_qty);
                $('#'+formId).find('.total-gst').text(res.total_gst);
                $('#'+formId).find('.total-amt').text(res.total_amt);
            }

            $("#overlay").hide();

        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', error);
            $("#overlay").hide();
        }
    });
}

function view_details(id) {
   $('#common-popup').load('popup/view-purchase-details?id=' + id,
      function() {

         $('#common-popup').modal('show');
      });
}

function deleterecord(id) {
   $('#common-popup').load('popup/deleterecord',
      function() {

         $('#common-popup').modal('show');
         $('#common-popup').modal('show').one('click', '#confirm', function() {

            $('#deleteid').val(id);
            $('#deleteform').submit();
         });
      });
}
function sendMail(id) {
   $('#common-popup').load('popup/send-mail?id='+id,
      function() {

         $('#common-popup').modal('show');
         $('#common-popup').modal('show').one('click', '#confirm', function() {

            $('#mailid').val(id);
            $('#sendmailform').submit();
         });
      });
}

function windowOpenStockTransferModal(id) {
    $('#st_purchase_id').val(id || '');
    $('#st_alert_msg').hide().removeClass('alert-danger alert-success').text('');
    $('#st_barcode_number').val('');
    $('#st_item_name').val('');
    $('#st_stock_qty').val(1);
    $('#st_store_id').val('');
    $('#st_avail_qty_hint').text('');
    
    // Set default toggle
    $('input[name="st_select_type"][value="barcode"]').prop('checked', true).trigger('change');

    if (id) {
        $.ajax({
            url: 'get_ajaxdata?function=get_purchase_item_details',
            type: 'POST',
            dataType: 'json',
            data: { id: id },
            success: function(res) {
                if (res && res.status && res.data) {
                    $('#st_barcode_number').val(res.data.barcode_number || '');
                    $('#st_item_name').val(res.data.item_name || '');
                    $('#st_stock_qty').val(res.data.qty || 1);
                    if (res.data.qty) {
                        $('#st_avail_qty_hint').text('Available Purchase Qty: ' + res.data.qty);
                    }
                }
            }
        });
    }

    $('#stockTransferModal').modal('show');
}
window.openStockTransferModal = windowOpenStockTransferModal;

function chartlist() {
    "use strict"; // Start of use strict

    try {
        //bar chart
        var ctx = document.getElementById("barChart");
        if (ctx && typeof Chart !== 'undefined') {
            var myChart = new Chart(ctx, {
               type: 'bar',
               data: {
                  labels: ["January", "February", "March", "April", "May", "June", "July"],
                  datasets: [{
                        label: "My First dataset",
                        data: [65, 59, 80, 81, 56, 55, 40],
                        borderColor: "rgba(0, 150, 136, 0.76)",
                        borderWidth: "0",
                        backgroundColor: "rgba(0, 150, 136, 0.76)"
                     },
                     {
                        label: "My Second dataset",
                        data: [28, 48, 40, 19, 86, 27, 90],
                        borderColor: "rgba(0, 150, 136, 0.76)",
                        borderWidth: "0",
                        backgroundColor: "rgba(0, 150, 136, 0.76)"
                     }
                  ]
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

        //line chart
        var ctxLine = document.getElementById("lineChart");
        if (ctxLine && typeof Chart !== 'undefined') {
            var myChart2 = new Chart(ctxLine, {
               type: 'line',
               data: {
                  labels: ["January", "February", "March", "April", "May", "June", "July"],
                  datasets: [{
                        label: "My First dataset",
                        borderColor: "rgba(0,0,0,.09)",
                        borderWidth: "1",
                        backgroundColor: "rgba(0,0,0,.07)",
                        data: [22, 44, 67, 43, 76, 45, 12]
                     },
                     {
                        label: "My Second dataset",
                        borderColor: "rgba(0, 150, 136, 0.76)",
                        borderWidth: "1",
                        backgroundColor: "rgba(0, 150, 136, 0.76)",
                        pointHighlightStroke: "rgba(26,179,148,1)",
                        data: [16, 32, 18, 26, 42, 33, 44]
                     }
                  ]
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

        // single bar chart
        var ctxSingle = document.getElementById("singelBarChart");
        if (ctxSingle && typeof Chart !== 'undefined') {
            var myChart3 = new Chart(ctxSingle, {
               type: 'bar',
               data: {
                  labels: ["Sun", "Mon", "Tu", "Wed", "Th", "Fri", "Sat"],
                  datasets: [{
                     label: "My First dataset",
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
    } catch(err) {
        console.warn('Chart error caught:', err);
    }
 }
 try { chartlist(); } catch(e) {}

/* ── Stock Transfer Modal JS ── */
function openStockTransferModal(id) {
    $('#st_purchase_id').val(id || '');
    $('#st_alert_msg').hide().removeClass('alert-danger alert-success').text('');
    $('#st_barcode_number').val('');
    $('#st_item_name').val('');
    $('#st_stock_qty').val(1);
    $('#st_store_id').val('');
    $('#st_avail_qty_hint').text('');

    if (id) {
        $.ajax({
            url: 'get_ajaxdata?function=get_purchase_item_details',
            type: 'POST',
            dataType: 'json',
            data: { id: id },
            success: function(res) {
                if (res && res.status && res.data) {
                    $('#st_barcode_number').val(res.data.barcode_number || '');
                    $('#st_item_name').val(res.data.item_name || '');
                    var availQty = parseInt(res.data.qty || 1, 10);
                    $('#st_stock_qty').val(availQty).attr('max', availQty);
                    if (res.data.qty) {
                        $('#st_avail_qty_hint').text('Available Purchase Qty: ' + availQty);
                    }
                }
            }
        });
    }

    $('#stockTransferModal').modal('show');
}

$(document).on('change blur', '#st_barcode_number', function() {
    var bc = $(this).val().trim();
    if (bc && !$('#st_purchase_id').val()) {
        $.ajax({
            url: 'get_ajaxdata?function=get_purchase_item_details',
            type: 'POST',
            dataType: 'json',
            data: { barcode: bc },
            success: function(res) {
                if (res && res.status && res.data) {
                    $('#st_purchase_id').val(res.data.id || '');
                    $('#st_item_name').val(res.data.item_name || '');
                    $('#st_stock_qty').val(res.data.qty || 1);
                    if (res.data.qty) {
                        $('#st_avail_qty_hint').text('Available Purchase Qty: ' + res.data.qty);
                    }
                }
            }
        });
    }
});

$(document).on('submit', '#stockTransferForm', function(e) {
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
        success: function(res) {
            $btn.prop('disabled', false).html(origHtml);
            if (res && res.status) {
                $('#st_alert_msg').removeClass('alert-danger').addClass('alert alert-success').html('<i class="fa fa-check-circle"></i> ' + res.message).show();
                setTimeout(function() {
                    $('#stockTransferModal').modal('hide');
                    if (typeof getAjaxResults === 'function') {
                        getAjaxResults('latestsearchForm');
                        getAjaxResults('searchForm');
                    }
                }, 1500);
            } else {
                $('#st_alert_msg').removeClass('alert-success').addClass('alert alert-danger').html('<i class="fa fa-exclamation-triangle"></i> ' + (res.message || 'Transfer failed')).show();
            }
        },
        error: function() {
            $btn.prop('disabled', false).html(origHtml);
            $('#st_alert_msg').removeClass('alert-success').addClass('alert alert-danger').html('<i class="fa fa-exclamation-triangle"></i> Server error. Please try again.').show();
        }
    });
});