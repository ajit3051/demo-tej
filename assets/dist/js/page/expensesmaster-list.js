function loadExpensesMasterData(page) {
    if (page === undefined || page === null) {
        page = $('#page').val() || 1;
    }
    $('#page').val(page);
    var search = $('#expensesMasterSearchForm #company_name').val() || '';

    $.ajax({
        url: 'ajax/expensesmaster-results.php',
        type: 'POST',
        data: {
            page: page,
            search: search
        },
        dataType: 'json',
        success: function(response) {
            $('#expensesmaster-list-results').html(response.htmlData);
            $('#expensesmaster-pagination').html(response.pagination);
            $('#total-records-count').text(response.totalRecords);
        },
        error: function() {
            console.error('Failed to fetch expenses master data');
        }
    });
}

$(document).ready(function() {
    var searchTimer;
    $(document).on('keyup input', '#expensesMasterSearchForm #company_name', function() {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(function() {
            loadExpensesMasterData(1);
        }, 300);
    });

    $(document).on('submit', '#expensesMasterSearchForm', function(e) {
        e.preventDefault();
        loadExpensesMasterData(1);
    });
});
