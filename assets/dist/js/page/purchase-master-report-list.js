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
   
   $('#common-popup').load('popup/export.php?type='+type,
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

//getAjaxResults('latestsearchForm');
getAjaxResults('searchForm');
function getAjaxResults(formId) {

    var formData = $("#"+formId).serialize();
    formData += '&formId=' + formId;

    var exp = $("#"+formId).find('input[name=export]').val();

    if(exp){
      
      const url = 'ajax/purchase-report-results.php?function=get_results&' + formData;
      window.location.href = url; // Redirect to the PHP script to trigger the download
    }
    //alert(formData);

    $("#overlay").show();
    $.ajax({
        type: 'POST',
        url: 'ajax/purchase-report-results.php?function=get_results',
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

            if(formId == 'latestsearchForm'){

                $("#report-list-latest-results").html(res.htmlData);
                $("#pagination-latest-result").html(res.pagination);

            } else {

                $("#report-list-results").html(res.htmlData);
                $("#pagination-result").html(res.pagination);
            }
            $('#'+formId).find('.total-qty').text(res.total_qty);
            $('#'+formId).find('.total-used-qty').text(res.total_used_qty);
            $('#'+formId).find('.total-bal-qty').text(res.total_bal_qty);
            $('#'+formId).find('.total-gst').text(res.total_gst);
            $('#'+formId).find('.total-amt').text(res.total_amt);
            $("#overlay").hide();

        }
    });
}

function view_details(id) {
   $('#common-popup').load('popup/view-purchase-details.php?id=' + id,
      function() {

         $('#common-popup').modal('show');
      });
}

function deleterecord(id) {
   $('#common-popup').load('popup/deleterecord.php',
      function() {

         $('#common-popup').modal('show');
         $('#common-popup').modal('show').one('click', '#confirm', function() {

            $('#deleteid').val(id);
            $('#deleteform').submit();
         });
      });
}
function sendMail(id) {
   $('#common-popup').load('popup/send-mail.php?id='+id,
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