function loadStoreProfileCreationData(page) {
    if (page === undefined || page === null) {
        page = $('#page').val() || 1;
    }
    $('#page').val(page);
    var search = $('#storeProfileCreationSearchForm #company_name').val() || '';

    $.ajax({
        url: 'ajax/storeprofilecreation-results.php',
        type: 'POST',
        data: {
            page: page,
            search: search
        },
        dataType: 'json',
        success: function(response) {
            $('#storeprofilecreation-list-results').html(response.htmlData);
            if (response.modalData !== undefined) {
                $('#storeprofile-modals-container').html(response.modalData);
            }
            $('#storeprofilecreation-pagination').html(response.pagination);
            $('#total-records-count').text(response.totalRecords);
        },
        error: function() {
            console.error('Failed to fetch store profile creation data');
        }
    });
}

$(document).ready(function() {
    loadStoreProfileCreationData(1);
    var searchTimer;
    $(document).on('keyup input', '#storeProfileCreationSearchForm #company_name', function() {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(function() {
            loadStoreProfileCreationData(1);
        }, 300);
    });

    $(document).on('submit', '#storeProfileCreationSearchForm', function(e) {
        e.preventDefault();
        loadStoreProfileCreationData(1);
    });
});
