(function ($) {
    "use strict";

    // Handle keyup / search input change
    $("body").on("keyup change", ".search-input", function (e) {
        var $this = $(this);
        var formid = $this.closest("form").attr("id");
        if (!formid) {
            formid = "brandMasterSearchForm";
        }
        $("#" + formid).find("input[name=page]").val(1);
        getBrandAjaxResults(formid);
    });

    // Handle Pagination click
    $("body").on("click", ".page-link-v2", function (e) {
        e.preventDefault();
        var $this = $(this);
        var pageNo = $this.attr("href");

        if (pageNo && pageNo !== "#" && pageNo !== "javascript:void(0);") {
            var formid = $this.closest("section").find("form").attr("id");
            if (!formid) {
                formid = "brandMasterSearchForm";
            }
            $("#" + formid).find("input[name=page]").val(pageNo);
            getBrandAjaxResults(formid);
        }
    });

    // Initial load
    $(document).ready(function () {
        getBrandAjaxResults("brandMasterSearchForm");
    });

}(jQuery));

function getBrandAjaxResults(formId) {
    if (!formId) {
        formId = "brandMasterSearchForm";
    }

    var formData = $("#" + formId).serialize();

    $.ajax({
        type: "POST",
        url: "ajax/brandmaster-results.php",
        data: formData,
        success: function (response) {
            var res = typeof response === "string" ? JSON.parse(response) : response;
            if (res && res.htmlData !== undefined) {
                $("#brandmaster-list-results").html(res.htmlData);
                if (res.modalData !== undefined) {
                    $("#brand-modals-container").html(res.modalData);
                }
                $("#pagination-result").html(res.pagination);
            }
        },
        error: function (xhr, status, error) {
            console.error("AJAX Error loading brand list:", error);
        }
    });
}
