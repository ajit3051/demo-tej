function loadSubheadMasterData(page) {
    if (page === undefined || page === null) {
        page = $('#page').val() || 1;
    }
    $('#page').val(page);
    var search = $('#subheadMasterSearchForm #company_name').val() || '';

    $.ajax({
        url: 'ajax/subheadmaster-results.php',
        type: 'POST',
        data: {
            page: page,
            search: search
        },
        dataType: 'json',
        success: function(response) {
            $('#subheadmaster-list-results').html(response.htmlData);
            $('#subheadmaster-pagination').html(response.pagination);
            $('#total-records-count').text(response.totalRecords);
        },
        error: function() {
            console.error('Failed to fetch subhead master data');
        }
    });
}

$(document).ready(function() {
    var searchTimer;
    $(document).on('keyup input', '#subheadMasterSearchForm #company_name', function() {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(function() {
            loadSubheadMasterData(1);
        }, 300);
    });

    $(document).on('submit', '#subheadMasterSearchForm', function(e) {
        e.preventDefault();
        loadSubheadMasterData(1);
    });
});
