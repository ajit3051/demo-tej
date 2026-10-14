$(function() {
    "use strict"; // Start of use strict
    //back to top
    $('body').append('<div id="toTop" class="btn back-top"><span class="ti-arrow-up"></span></div>');
    $(window).on("scroll", function () {
        if ($(this).scrollTop() !== 0) {
            $('#toTop').fadeIn();
        } else {
            $('#toTop').fadeOut();
        }
    });
    $('#toTop').on("click", function () {
        $("html, body").animate({scrollTop: 0}, 600);
        return false;
    });

    //lobipanel
    $('.lobidrag').lobiPanel({
        sortable: true,
        editTitle: {
            icon: 'ti-pencil'
        },
        unpin: {
            icon: 'ti-move'
        },
        reload: {
            icon: 'ti-reload'
        },
        minimize: {
            icon: 'ti-minus',
            icon2: 'ti-plus'
        },
        close: {
            icon: 'ti-close'
        },
        expand: {
            icon: 'ti-fullscreen',
            icon2: 'ti-fullscreen'
        }
    });

    $('.lobidisable').lobiPanel({
        reload: false,
        close: false,
        editTitle: false,
        sortable: true,
        unpin: {
            icon: 'ti-move'
        },
        minimize: {
            icon: 'ti-minus',
            icon2: 'ti-plus'
        },
        expand: {
            icon: 'ti-fullscreen',
            icon2: 'ti-fullscreen'
        }
    });
    //search
    $('a[href="#search"]').on('click', function(event) {
        event.preventDefault();
        $('#search').addClass('open');
        $('#search > form > input[type="search"]').focus();
    });
    $('#search, #search button.close').on('click keyup', function(event) {
        if (event.target == this || event.target.className == 'close' || event.keyCode == 27) {
            $(this).removeClass('open');
        }
    });
    //Datepicker
    function datepic() {
    $('.from-date, .to-date').datepicker({
        dateFormat: 'dd/mm/yy',
        changeMonth: true,
        changeYear: true
    });

    $(document).on('focus', '.from-date', function () {
        let $fromDate = $(this);
        $fromDate.datepicker('option', 'onSelect', function (dateText) {
            var selectedDate = $fromDate.datepicker('getDate');
            let $toDate = $fromDate.closest('.date-range-group').find('.to-date').first();
            if ($toDate.length) {
                $toDate.datepicker('option', 'minDate', selectedDate);
            }
        });
    });
}
datepic();
    //preloader
    // makes sure the whole site is loaded
         $( window ).on( "load", function() {
             // will first fade out the loading animation
             jQuery("#status").fadeOut();
             // will fade out the whole DIV that covers the website.
             jQuery("#preloader").delay(1000).fadeOut("slow");
         });  
    
});
 