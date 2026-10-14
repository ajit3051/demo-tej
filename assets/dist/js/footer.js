$(function () {
    "use strict"; // Start of use strict


    $('.datetimepicker').datepicker({
        language: 'en',
        dateFormat: 'dd-mm-yy',
        timeFormat: 'HH:mm:ss',
        changeMonth: true,
        changeYear: true,
        defaultDate: new Date()
        //minDate: new Date() // Now can select only dates, which goes after today
    }).datepicker("setDate", new Date());

    $('.dtpicker').datepicker({
        language: 'en',
        dateFormat: 'dd-mm-yy',
        timeFormat: 'HH:mm:ss',
        changeMonth: true,
        changeYear: true,
        defaultDate: new Date()
        //minDate: new Date() // Now can select only dates, which goes after today
    });



    $("body").delegate(".recordsPerPage", "change", function() {
        var selectedval = $(this).val();
        $.ajax({
            type: 'GET',
            url: 'get_ajaxdata.php?function=setPerpageValue&selectedval=' + selectedval,
            success: function(data) {
                location.reload();
            }
        });
    });

});
