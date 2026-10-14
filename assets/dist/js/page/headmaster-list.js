function loadHeadMasterData(page) {
    if (page === undefined || page === null) {
        page = $('#page').val() || 1;
    }
    $('#page').val(page);
    var search = $('#headMasterSearchForm #company_name').val() || '';

    $.ajax({
        url: 'ajax/headmaster-results.php',
        type: 'POST',
        data: {
            page: page,
            search: search
        },
        dataType: 'json',
        success: function(response) {
            $('#headmaster-list-results').html(response.htmlData);
            $('#headmaster-pagination').html(response.pagination);
            $('#total-records-count').text(response.totalRecords);
        },
        error: function() {
            console.error('Failed to fetch head master data');
        }
    });
}

$(document).ready(function() {
    var searchTimer;
    $(document).on('keyup input', '#headMasterSearchForm #company_name', function() {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(function() {
            loadHeadMasterData(1);
        }, 300);
    });

    $(document).on('submit', '#headMasterSearchForm', function(e) {
        e.preventDefault();
        loadHeadMasterData(1);
    });
});
