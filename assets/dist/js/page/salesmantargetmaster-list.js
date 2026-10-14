function loadSalesmanTargetMasterData(page) {
    if (page === undefined || page === null) {
        page = $('#page').val() || 1;
    }
    $('#page').val(page);
    var search = $('#salesmanTargetMasterSearchForm #company_name').val() || '';

    $.ajax({
        url: 'ajax/salesmantargetmaster-results.php',
        type: 'POST',
        data: {
            page: page,
            search: search
        },
        dataType: 'json',
        success: function(response) {
            $('#salesmantargetmaster-list-results').html(response.htmlData);
            $('#salesmantargetmaster-pagination').html(response.pagination);
            $('#total-records-count').text(response.totalRecords);
        },
        error: function() {
            console.error('Failed to fetch salesman target master data');
        }
    });
}

$(document).ready(function() {
    var searchTimer;
    $(document).on('keyup input', '#salesmanTargetMasterSearchForm #company_name', function() {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(function() {
            loadSalesmanTargetMasterData(1);
        }, 300);
    });

    $(document).on('submit', '#salesmanTargetMasterSearchForm', function(e) {
        e.preventDefault();
        loadSalesmanTargetMasterData(1);
    });
});
