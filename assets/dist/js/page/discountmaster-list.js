function loadDiscountMasterData(page) {
    if (page === undefined || page === null) {
        page = $('#page').val() || 1;
    }
    $('#page').val(page);
    var search = $('#discountMasterSearchForm #company_name').val() || '';

    $.ajax({
        url: 'ajax/discountmaster-results.php',
        type: 'POST',
        data: {
            page: page,
            search: search
        },
        dataType: 'json',
        success: function(response) {
            $('#discountmaster-list-results').html(response.htmlData);
            $('#discountmaster-pagination').html(response.pagination);
            $('#total-records-count').text(response.totalRecords);
        },
        error: function() {
            console.error('Failed to fetch discount master data');
        }
    });
}

$(document).ready(function() {
    loadDiscountMasterData(1);

    var searchTimer;
    $(document).on('keyup input', '#discountMasterSearchForm #company_name', function() {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(function() {
            loadDiscountMasterData(1);
        }, 300);
    });

    $(document).on('submit', '#discountMasterSearchForm', function(e) {
        e.preventDefault();
        loadDiscountMasterData(1);
    });

    $(document).on('click', '.view-discount-btn', function() {
        var btn = $(this);
        $('#modal-discount-id').text(btn.data('id') || '-');
        $('#modal-dis-code').text(btn.data('discode') || '-');
        $('#modal-distype-name').text(btn.data('type') || '-');
        $('#modal-group-name').text(btn.data('group') || '-');
        $('#modal-brand-name').text(btn.data('brand') || '-');
        $('#modal-barcode-no').text(btn.data('barcode') || '-');
        $('#modal-item-name').text(btn.data('item') || '-');
        $('#modal-store-name').text(btn.data('store') || '-');
        $('#modal-location-name').text(btn.data('location') || '-');
        $('#modal-discount-per').text(btn.data('per') ? btn.data('per') + '%' : '-');
        $('#modal-discount-amt').text(btn.data('amt') || '-');
        $('#modal-mrp').text(btn.data('mrp') || '-');
        $('#modal-sp').text(btn.data('sp') || '-');
        
        var status = btn.data('status') || 'Active';
        if (status === 'Active' || status === 'true') {
            $('#modal-discount-status').html('<span class="label label-success badge-status-sm">Active</span>');
        } else {
            $('#modal-discount-status').html('<span class="label label-danger badge-status-sm">Inactive</span>');
        }
    });
});
