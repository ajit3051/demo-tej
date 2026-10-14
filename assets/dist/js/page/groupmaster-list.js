(function ($) {
    "use strict";

    // Handle keyup / search input change
    $("body").on("keyup change", ".search-input", function (e) {
        var $this = $(this);
        var formid = $this.closest("form").attr("id");
        if (!formid) {
            formid = "groupMasterSearchForm";
        }
        $("#" + formid).find("input[name=page]").val(1);
        getGroupAjaxResults(formid);
    });

    // Handle Pagination click
    $("body").on("click", ".page-link-v2", function (e) {
        e.preventDefault();
        var $this = $(this);
        var pageNo = $this.attr("href");

        if (pageNo && pageNo !== "#" && pageNo !== "javascript:void(0);") {
            var formid = $this.closest("section").find("form").attr("id");
            if (!formid) {
                formid = "groupMasterSearchForm";
            }
            $("#" + formid).find("input[name=page]").val(pageNo);
            getGroupAjaxResults(formid);
        }
    });

    // Initial load
    $(document).ready(function () {
        getGroupAjaxResults("groupMasterSearchForm");
    });

}(jQuery));

function getGroupAjaxResults(formId) {
    if (!formId) {
        formId = "groupMasterSearchForm";
    }

    var formData = $("#" + formId).serialize();

    $.ajax({
        type: "POST",
        url: "ajax/groupmaster-results.php",
        data: formData,
        success: function (response) {
            var res = typeof response === "string" ? JSON.parse(response) : response;
            if (res && res.htmlData !== undefined) {
                $("#groupmaster-list-results").html(res.htmlData);
                if (res.modalData !== undefined) {
                    $("#group-modals-container").html(res.modalData);
                }
                $("#pagination-result").html(res.pagination);
            }
        },
        error: function (xhr, status, error) {
            console.error("AJAX Error loading group list:", error);
        }
    });
}
