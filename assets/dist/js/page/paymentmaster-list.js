function loadPaymentMasterData(page) {
    if (page === undefined || page === null) {
        page = $('#page').val() || 1;
    }
    $('#page').val(page);
    var search = $('#paymentMasterSearchForm #payment_search').val() || '';

    $.ajax({
        url: 'ajax/paymentmaster-results.php',
        type: 'POST',
        data: {
            page: page,
            search: search
        },
        dataType: 'json',
        success: function(response) {
            $('#paymentmaster-list-results').html(response.htmlData);
            $('#paymentmaster-pagination').html(response.pagination);
            $('#total-records-count').text(response.totalRecords);
        },
        error: function() {
            console.error('Failed to fetch payment master data');
        }
    });
}

$(document).ready(function() {
    // Auto-populate Party Code, Contact Name, and Mobile No on Party Name selection
    $(document).on('change', '.party-name-select', function() {
        var $tab = $(this).closest('.tab-pane');
        var $selected = $(this).find('option:selected');
        var code = $selected.attr('data-code') || $selected.data('code') || '';
        var contact = $selected.attr('data-contact') || $selected.data('contact') || '';
        var mobile = $selected.attr('data-mobile') || $selected.data('mobile') || '';

        $tab.find('input[name="party_code"]').val(code);
        $tab.find('input[name="regular_name"]').val(contact);
        $tab.find('input[name="mobile_no"]').val(mobile);
    });

    var searchTimer;
    $(document).on('keyup input', '#paymentMasterSearchForm #payment_search', function() {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(function() {
            loadPaymentMasterData(1);
        }, 300);
    });

    $(document).on('submit', '#paymentMasterSearchForm', function(e) {
        e.preventDefault();
        loadPaymentMasterData(1);
    });

    $(document).on('click', '#paymentMasterSearchBtn', function() {
        loadPaymentMasterData(1);
    });
});
