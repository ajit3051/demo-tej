function loadAccountMasterData(page) {
    if (page === undefined || page === null) {
        page = $('#page').val() || 1;
    }
    $('#page').val(page);
    var search = $('#accountMasterSearchForm #company_name').val() || '';

    $.ajax({
        url: 'ajax/accountmaster-results.php',
        type: 'POST',
        data: {
            page: page,
            search: search
        },
        dataType: 'json',
        success: function(response) {
            $('#accountmaster-list-results').html(response.htmlData);
            $('#accountmaster-pagination').html(response.pagination);
            $('#total-records-count').text(response.totalRecords);
        },
        error: function() {
            console.error('Failed to fetch account master data');
        }
    });
}

$(document).ready(function() {
    var searchTimer;
    $(document).on('keyup input', '#accountMasterSearchForm #company_name', function() {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(function() {
            loadAccountMasterData(1);
        }, 300);
    });

    $(document).on('submit', '#accountMasterSearchForm', function(e) {
        e.preventDefault();
        loadAccountMasterData(1);
    });
});
