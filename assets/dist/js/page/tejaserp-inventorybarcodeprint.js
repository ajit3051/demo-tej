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


    // ===== PRINT BARCODE BUTTON =====
    $('.barcodePrint').on('click', function () {

        var $this = $(this);
        var formid = $this.closest('section').find('form').attr('id');
        var $form = $('#' + formid);

        // 1. Get selected columns -> store {index, label, value}
        var selectedCols = [];
        $form.find('input[name="export_data[]"]:checked').each(function () {
            var $th = $(this).closest('th');
            var colIndex = $th.index();
            var label = $th.clone().children('input').remove().end().text().trim();

            selectedCols.push({
                index: colIndex,
                value: $(this).val(),
                label: label
            });
        });

        if (selectedCols.length === 0) {
            alert('Please select at least one column to print.');
            return;
        }

        // 1b. Always locate the Barcode No column index (needed even if not checked)
        var $barcodeCheckbox = $form.find('input[name="export_data[]"][value="barcode_number"]');
        var barcodeColIndex = $barcodeCheckbox.length ? $barcodeCheckbox.closest('th').index() : -1;

        // 1c. SrNo has no checkbox - it's always the first <th>, index 0
        var srNoColIndex = 0;

        // 2. Get selected rows via recent-chk[], scoped to this form only
        var $selectedRows = $form.find('input[name="recent-chk[]"]:checked').closest('tr');
        if ($selectedRows.length === 0) {
            $selectedRows = $form.find('.inventory-results tr');
        }

        if ($selectedRows.length === 0) {
            alert('No records to print.');
            return;
        }

        // 3. Build the print-only table
        var html = '<table border="1" cellspacing="0" cellpadding="6" ' +
                   'style="border-collapse:collapse;width:100%;font-family:Arial, sans-serif;font-size:13px;">';

        // Header
        html += '<thead><tr>';
        html += '<th style="background:#f0f0f0;text-align:left;">SrNo</th>'; // always first
        $.each(selectedCols, function (i, col) {
            html += '<th style="background:#f0f0f0;text-align:left;">' + col.label + '</th>';
        });
        html += '<th style="background:#f0f0f0;text-align:left;">Barcode</th>'; // extra column
        html += '</tr></thead><tbody>';

        // Rows
        $selectedRows.each(function () {
            var $tds = $(this).find('td');
            html += '<tr>';

            // SrNo cell (text inside the label, e.g. "1", "2")
            var srNoText = $tds.eq(srNoColIndex).find('.s_no').text().trim();
            if (!srNoText) {
                srNoText = $tds.eq(srNoColIndex).text().trim(); // fallback if no .s_no label
            }
            html += '<td>' + srNoText + '</td>';

            $.each(selectedCols, function (i, col) {
                var cellText = $tds.eq(col.index).text().trim();
                html += '<td>' + cellText + '</td>';
            });

            // Barcode image cell
            var barcodeValue = barcodeColIndex >= 0 ? $tds.eq(barcodeColIndex).text().trim() : '';
            html += '<td style="text-align:center;">';
            if (barcodeValue) {
                html += '<svg class="barcode" data-code="' + barcodeValue + '"></svg>';
            }
            html += '</td>';

            html += '</tr>';
        });

        html += '</tbody></table>';

        // 4. Open print window with JsBarcode loaded, render after load
        var printWin = window.open('', '_blank', 'width=1000,height=700');
        printWin.document.write(
            '<html><head><title>Print Barcode Records</title>' +
            '<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"><\/script>' +
            '</head><body>' +
            html +
            '<script>' +
            'window.onload = function() {' +
            '  document.querySelectorAll(".barcode").forEach(function(el) {' +
            '    JsBarcode(el, el.getAttribute("data-code"), { format: "CODE128", width: 1.5, height: 40, fontSize: 12, margin: 5 });' +
            '  });' +
            '  setTimeout(function() { window.print(); window.close(); }, 400);' +
            '};' +
            '<\/script>' +
            '</body></html>'
        );
        printWin.document.close();
        printWin.focus();
    });

    // ===== RESET BUTTON =====
    $('.resetPrint').on('click', function () {
        var $this = $(this);
        var formid = $this.closest('section').find('form').attr('id');
        var $form = $('#' + formid);

        $form.find('input[name="recent-chk[]"]').prop('checked', false);
        $form.find('input[name="export_data[]"]').prop('checked', false);
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
getAjaxResults('billwisesearchForm');
getAjaxResults('itemwisesearchForm');
function getAjaxResults(formId) {

    var formData = $("#"+formId).serialize();
    formData += '&formId=' + formId;

    var exp = $("#"+formId).find('input[name=export]').val();
   
    if(exp){
      
      const url = 'ajax/tejaserp-inventorybarcodeprint?function=get_results&' + formData;
      window.location.href = url; // Redirect to the PHP script to trigger the download
    }
    // alert(formData);
    $("#overlay").show();
    $.ajax({
        type: 'POST',
        url: 'ajax/tejaserp-inventorybarcodeprint?function=get_results',
        data: formData,
        success: function (response) {

            let result = response.match(/!DOCTYPE html/);
            if (result == '!DOCTYPE html') {
                location.reload();
            }

            //alert(response);
            var res = JSON.parse(response);

            if (res.isLogged == 'false') {
                location.reload();
            }

           
            $('#'+formId).find('.inventory-results').html(res.htmlData);
            $('#'+formId).find('.pagination-result').html(res.pagination);
            $('#'+formId).find('.total-qty').text(res.total_qty);
            $('#'+formId).find('.total-gst').text(res.total_gst);
            $('#'+formId).find('.total-amt').text(res.total_amt);
            $('#'+formId).closest('section').find('.cash-qty').text(res.cash_qty);
            $('#'+formId).closest('section').find('.cash-amt').text(res.cash_amt);
            $('#'+formId).closest('section').find('.upi-qty').text(res.upi_qty);
            $('#'+formId).closest('section').find('.upi-amt').text(res.upi_amt);
            $('#'+formId).closest('section').find('.card-qty').text(res.card_qty);
            $('#'+formId).closest('section').find('.card-amt').text(res.card_amt);
            $("#overlay").hide();

        }
    });
}

function view_details(id) {
   $('#common-popup').load('popup/view-sale-details?id=' + id,
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

function chartlist() {
    "use strict"; // Start of use strict

    //bar chart
    var ctx = document.getElementById("barChart");
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


    //line chart
    var ctx = document.getElementById("lineChart");
    var myChart = new Chart(ctx, {
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


    // single bar chart
    var ctx = document.getElementById("singelBarChart");
    var myChart = new Chart(ctx, {
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
 chartlist();
