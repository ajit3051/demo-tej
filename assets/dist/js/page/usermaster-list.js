(function ($) {
    "use strict";

    // Handle keyup / search input change
    $("body").on("keyup change", ".search-input", function (e) {
        var $this = $(this);
        var formid = $this.closest("form").attr("id");
        if (!formid) {
            formid = "userMasterSearchForm";
        }
        $("#" + formid).find("input[name=page]").val(1);
        getAjaxResults(formid);
    });

    // Handle Pagination click
    $("body").on("click", ".page-link-v2", function (e) {
        e.preventDefault();
        var $this = $(this);
        var pageNo = $this.attr("href");

        if (pageNo && pageNo !== "#" && pageNo !== "javascript:void(0);") {
            var formid = $this.closest("section").find("form").attr("id");
            if (!formid) {
                formid = "userMasterSearchForm";
            }
            $("#" + formid).find("input[name=page]").val(pageNo);
            getAjaxResults(formid);
        }
    });

    // Initial load
    $(document).ready(function () {
        getAjaxResults("userMasterSearchForm");
    });

}(jQuery));

function getAjaxResults(formId) {
    if (!formId) {
        formId = "userMasterSearchForm";
    }

    var formData = $("#" + formId).serialize();

    $.ajax({
        type: "POST",
        url: "ajax/usermaster-results.php",
        data: formData,
        success: function (response) {
            var res = typeof response === "string" ? JSON.parse(response) : response;
            if (res && res.htmlData !== undefined) {
                $("#usermaster-list-results").html(res.htmlData);
                if (res.modalData !== undefined) {
                    $("#user-modals-container").html(res.modalData);
                }
                $("#pagination-result").html(res.pagination);
            }
        },
        error: function (xhr, status, error) {
            console.error("AJAX Error loading user list:", error);
        }
    });
}
