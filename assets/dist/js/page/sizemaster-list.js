(function ($) {
    "use strict";

    // Handle keyup / search input change
    $("body").on("keyup change", ".search-input", function (e) {
        var $this = $(this);
        var formid = $this.closest("form").attr("id");
        if (!formid) {
            formid = "sizeMasterSearchForm";
        }
        $("#" + formid).find("input[name=page]").val(1);
        getSizeAjaxResults(formid);
    });

    // Handle Pagination click
    $("body").on("click", ".page-link-v2", function (e) {
        e.preventDefault();
        var $this = $(this);
        var pageNo = $this.attr("href");

        if (pageNo && pageNo !== "#" && pageNo !== "javascript:void(0);") {
            var formid = $this.closest("section").find("form").attr("id");
            if (!formid) {
                formid = "sizeMasterSearchForm";
            }
            $("#" + formid).find("input[name=page]").val(pageNo);
            getSizeAjaxResults(formid);
        }
    });

    // Initial load
    $(document).ready(function () {
        getSizeAjaxResults("sizeMasterSearchForm");
    });

}(jQuery));

function getSizeAjaxResults(formId) {
    if (!formId) {
        formId = "sizeMasterSearchForm";
    }

    var formData = $("#" + formId).serialize();

    $.ajax({
        type: "POST",
        url: "ajax/sizemaster-results.php",
        data: formData,
        success: function (response) {
            var res = typeof response === "string" ? JSON.parse(response) : response;
            if (res && res.htmlData !== undefined) {
                $("#sizemaster-list-results").html(res.htmlData);
                if (res.modalData !== undefined) {
                    $("#size-modals-container").html(res.modalData);
                }
                $("#pagination-result").html(res.pagination);
            }
        },
        error: function (xhr, status, error) {
            console.error("AJAX Error loading size list:", error);
        }
    });
}
