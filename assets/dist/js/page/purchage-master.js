(function ($) {

    total_hidden_posted();

    $("#purchage_form").validate({
        rules: {
            purchage_date: {
                required: true
            },
            bill_number: {
                required: true
            },
            gst_type: {
                required: true
            },
            under_gst: {
                required: true
            },
            bill_type: {
                required: true
            },
            /* mobile_number: {
                required: true
            },
            account_name: {
                required: true
            }, 
            invoice_number: {
                required: true
            },*/
        },
        messages: {
            purchage_date: {
                required: "Please enter invoice no."
            },
            bill_number: {
                required: "Please enter bill number."
            },
            gst_type: {
                required: "Please enter gst type."
            },
            under_gst: {
                required: "Please enter under gst."
            },
            bill_type: {
                required: "Please enter bill type."
            },
            mobile_number: {
                required: "Please enter mobile number."
            },
            account_name: {
                required: "Please enter account name."
            },
            invoice_number: {
                required: "Please enter invoice number."
            },
        }
    });

    $("#checkAll").change(function () {
        $("input:checkbox").prop('checked', $(this).prop("checked"));
    });

    // confirmbtn click is handled by onclick="openPopupsave()" in HTML
    // Final submit happens inside validateAndSubmitPopup() after popup Save is clicked

    /* ── Independent Input Listeners ── */
    $(document).on('input', 'input[name="account_name"]', function () {
        getAccountDetails($(this), 'account_name');
    });

    $(document).on('input', 'input[name="mobile_number"]', function () {
        getAccountDetails($(this), 'mobile');
    });

    $(document).on('input', 'input[name="ac_code"]', function () {
        getAccountDetails($(this), 'ac_code');
    });

    $('input[name="item_name"]').on('input', function () {
        let $this = $(this);
        getItemDetails($this, 'item_name');
    });
    $('input[name="article_no"]').on('input', function () {
        let $this = $(this);
        getItemDetails($this, 'article_no');
    });

    // Article No: pressing Enter triggers item lookup (same as barcode)
    document.getElementById('article_no').addEventListener('keydown', function (event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            var sku = this.value.trim();
            if (sku) {
                showItemByArticle(sku);
            }
        }
    });


    $(document).click(function (e) {
        if (!$(e.target).closest('#article_no, #item-name').length) {
            $('#suggestions').hide();
        }
    });

    document.getElementById('barcode_number').addEventListener('keydown', function (event) {
        if (event.key === 'Enter') {
            event.preventDefault(); // Prevent the default action if needed
            show_item_detail();
        }
    });

    $(document).on('click', '.emp-details', function () {
        var itemId = $(this).attr('item_id');
        $('.autocomplete-suggestions').hide();
        // If clicked from article_no suggestions, use sku lookup
        if ($(this).closest('#suggestions_article_no').length) {
            showItemByArticleId(itemId);
        } else {
            var rowObject = $(this).text();
            var barcode = rowObject.split(',')[2].trim();
            $('#barcode_number').val(barcode);
            show_item_detail();
        }
    });

    $('.extra-charge').on('input', function (e) {
        discountTotalPercent()
        total_hidden_posted();

    });

    // Pagination event
    $("body").on("click", ".page-link-v2", function (e) {
        e.preventDefault();
        var pageNo = $(this).attr("href");
        $("#searchForm").find('input[name=page]').val(pageNo);
        $("#searchForm").find('input[name=export]').val('');
        getAjaxResults();
    });
    
    /* ── DUAL mode: auto-fill the paired field with the remainder ── */
    function lockPair(lockIds, remainderTargetId, enteredVal, bill) {
        $(lockIds.join(', ')).prop('disabled', true).val('')
            .closest('.form-group-modern').css('opacity', '0.4');
        var remaining = bill - enteredVal;
        if (remaining < 0) remaining = 0;
        $(remainderTargetId).val(remaining.toFixed(2));
    }

    function unlockAll() {
        $('#pay_cash, #pay_upi, #pay_card, #pay_credit')
            .prop('disabled', false).closest('.form-group-modern').css('opacity', '1');
    }

    $(document).on('input', '#pay_cash', function () {
        if ($('#payment_mode_select').val() !== 'DUAL') return;
        var raw = $(this).val().trim();
        var cash = parseFloat(raw) || 0;
        if (cash < 0) { cash = 0; $(this).val(0); }
        var bill = parseFloat($('#popup_bill_amount').text().replace('₹', '').trim()) || 0;

        if (raw !== '' && cash > 0) {
            lockPair(['#pay_card', '#pay_credit'], '#pay_upi', cash, bill);
        } else {
            unlockAll();
            $('#pay_upi').val('');
        }
    });

    $(document).on('input', '#pay_upi', function () {
        if ($('#payment_mode_select').val() !== 'DUAL') return;
        var raw = $(this).val().trim();
        var upi = parseFloat(raw) || 0;
        if (upi < 0) { upi = 0; $(this).val(0); }
        var bill = parseFloat($('#popup_bill_amount').text().replace('₹', '').trim()) || 0;

        if (raw !== '' && upi > 0) {
            lockPair(['#pay_card', '#pay_credit'], '#pay_cash', upi, bill);
        } else {
            unlockAll();
            $('#pay_cash').val('');
        }
    });

    $(document).on('input', '#pay_card', function () {
        if ($('#payment_mode_select').val() !== 'DUAL') return;
        var raw = $(this).val().trim();
        var card = parseFloat(raw) || 0;
        if (card < 0) { card = 0; $(this).val(0); }
        var bill = parseFloat($('#popup_bill_amount').text().replace('₹', '').trim()) || 0;

        if (raw !== '' && card > 0) {
            lockPair(['#pay_cash', '#pay_upi'], '#pay_credit', card, bill);
        } else {
            unlockAll();
            $('#pay_credit').val('');
        }
    });

    $(document).on('input', '#pay_credit', function () {
        if ($('#payment_mode_select').val() !== 'DUAL') return;
        var raw = $(this).val().trim();
        var credit = parseFloat(raw) || 0;
        if (credit < 0) { credit = 0; $(this).val(0); }
        var bill = parseFloat($('#popup_bill_amount').text().replace('₹', '').trim()) || 0;

        if (raw !== '' && credit > 0) {
            lockPair(['#pay_cash', '#pay_upi'], '#pay_card', credit, bill);
        } else {
            unlockAll();
            $('#pay_card').val('');
        }
    });


}(jQuery));

function exportExcel(element, type) {
   
    $('#common-popup').load('popup/export?type='+type,
       function() {
          $('#common-popup').modal('show');
          $('#common-popup').modal('show').one('click', '#confirm', function() {
             $("#searchForm").find('input[name=export]').val(type);
      
            getAjaxResults();
             $('#common-popup').modal('hide');
          });
       });
 }

getAjaxResults();
function getAjaxResults() {

    var formData = $("#searchForm").serialize();

    var exp = $("#searchForm").find('input[name=export]').val();
  
    if(exp){
      
      const url = 'ajax/purchase-results?function=get_results&' + formData;
      window.location.href = url; // Redirect to the PHP script to trigger the download
    }

    //alert(formData);
    $("#overlay").show();
    $.ajax({
        type: 'POST',
        url: 'ajax/purchase-results?function=get_results',
        data: formData,
        success: function (response) {

            let result = response.match(/!DOCTYPE html/);
            if (result == '!DOCTYPE html') {
                location.reload();
            }

            //alert(response);
            //$("#dashboard-account-results").html(response);

            var res = JSON.parse(response);

            if (res.isLogged == 'false') {
                location.reload();
            }
            $("#latestRecord").html(res.htmlData);
            $("#pagination-result").html(res.pagination);

            $("#overlay").hide();

        }
    });
}

$(document).on('keyup input', '#searchForm input[name="search"], #searchForm #company_name', function() {
    getAjaxResults();
});
$(document).on('submit', '#searchForm', function(e) {
    e.preventDefault();
    getAjaxResults();
});

/* ── Optimized Suggestion Render Function ── */
function getAccountDetails($this, type = '') {
    let input = $this.val();
    
    // Target ONLY the drop-down elements nested directly adjacent to the selected input field
    let currentContainer = $this.siblings('.autocomplete-suggestions');
    
    // Collapse any separate visible suggestion trees instantly
    $('.autocomplete-suggestions').not(currentContainer).hide().html('');

    if (input.length > 0) {
        $.ajax({
            url: 'get_ajaxdata?function=get_account_details',
            method: 'POST',
            data: { term: input, type: type },
            success: function (res) {
                let result = JSON.parse(res);
                let suggestionsHTML = '';
                
                if (result.status && result.suggestions) {
                    $.each(result.suggestions, function (key, row) {
                        let safeCode   = $('<div>').text(row.account_code || '').html();
                        let safeName   = $('<div>').text(row.account_name || '').html();
                        let safeMobile = $('<div>').text(row.mobile_no || '').html();

                        suggestionsHTML += '<div class="autocomplete-suggestion" style="padding: 8px; cursor: pointer; border-bottom: 1px solid #f0f0f0;" ' +
                            'data-code="' + safeCode + '" ' +
                            'data-name="' + safeName + '" ' +
                            'data-mobile="' + safeMobile + '">' + 
                            row.display + '</div>';
                    });
                    currentContainer.html(suggestionsHTML).show();
                } else {
                    currentContainer.hide().html('');
                }
            }
        });
    } else {
        currentContainer.hide().html('');
    }
}

/* ── Independent Selection Handling ── */
   $(document).on('click', '.autocomplete-suggestions .autocomplete-suggestion', function () {
    // Collect dataset values natively embedded within target DOM
    var code   = $(this).data('code');
    var name   = $(this).data('name');
    var mobile = $(this).data('mobile');

    // Autofill all three input components 
    $('input[name="ac_code"]').val(code);
    $('input[name="account_name"]').val(name);
    $('input[name="mobile_number"]').val(mobile);

    // Hide and clear all visual autocomplete suggestions
    $('.autocomplete-suggestions').hide().html('');
});

    /* ── Close Suggestion Menus on Click Outside ── */
   $(document).click(function (e) {
    if (!$(e.target).closest('input[name="account_name"], input[name="mobile_number"], input[name="ac_code"], .autocomplete-suggestions').length) {
        $('.autocomplete-suggestions').hide().html('');
    }
});

$('#percent_total_discount, #discount_total_amount').on('focus', function() {
    if ($(this).val() == '0.00' || $(this).val() == '0') {
        $(this).val('');
    }
}).on('blur', function() {
    if ($(this).val() === '') {
        $(this).val('0.00');
    }
});

function getItemDetails($this, type = '') {
    let input = $this.val();
    if (input.length > 0) {
        $.ajax({
            url: 'get_ajaxdata?function=get_item_details',
            method: 'POST',
            data: { term: input, type: type },
            success: function (res) {
                let result = JSON.parse(res);

                let suggestionsHTML = '';
                $.each(result.suggestions, function (key, val) {
                    suggestionsHTML += '<div class="autocomplete-suggestion emp-details" data="' + val + '" item_id="' + key + '">' + val + '</div>';

                });

                $('#suggestions_' + type).html(suggestionsHTML).show();
            }
        });
    } else {
        $('#suggestions_' + type).hide();
    }
}

function show_item_detail() {

    var barcode = $("#barcode_number").val();
    var gst_type = $('#gst_type').find(":selected").val();
    var under_gst = $('#under_gst').find(":selected").val();
    var trcount = parseInt($('#dataTable tr').length);

    $.ajax({
        type: 'POST',
        dataType: "json",
        url: 'get_ajaxdata?function=get_item_info',
        data: {
            barcode: barcode,
            gst_type: gst_type,
            under_gst: under_gst,
            trcount: trcount,
            act: "get_item_info"
        },
        success: function (result) {

            if (result.status === true) {
                $("#barcode_number").val('');
                $("#item_name").val('');
                $("#article_no").val('');
                $('#barcode_number').focus();

                var existingRow = $('#row_' + barcode);
                //alert(existingRow.length);
                if (existingRow.length) {
                    let qtyInput = existingRow.find('.qtyval');
                    let newQty = parseInt(qtyInput.val()) + 1;
                    qtyInput.val(newQty);

                    // Update the total price when the quantity is increased               
                    updateTotalPrice(existingRow, gst_type, 1);
                } else {
                    $('#dataTable').append(result.html);
                    //total_price(result.gst_total);
                    total_hidden_posted();

                }

            }
        }
    });
}

function calculateFromPercentage(barcode) {
    var existingRow = $('#row_' + barcode);
    let newQty = parseFloat(existingRow.find('.qtyval').val()) || 0;
    let price  = parseFloat(existingRow.find('.pprice').val()) || 0;
    var bill_type = $('#gst_type').find(":selected").val();

    var discountPercentage = parseFloat(existingRow.find('.percent_discount').val()) || 0;
    if (discountPercentage < 0) {
        discountPercentage = 0;
        existingRow.find('.percent_discount').val(0);
    }

    const discountAmount = (discountPercentage / 100) * (price * newQty);
    existingRow.find('.discount_amt').val(discountAmount.toFixed(2));

    updateTotalPrice(existingRow, bill_type);
}

function calculateFromAmount(barcode) {
    var existingRow = $('#row_' + barcode);
    let newQty = parseFloat(existingRow.find('.qtyval').val()) || 0;
    let price  = parseFloat(existingRow.find('.pprice').val()) || 0;
    var bill_type = $('#gst_type').find(":selected").val();

    var discountAmount = parseFloat(existingRow.find('.discount_amt').val()) || 0;
    if (discountAmount < 0) {
        discountAmount = 0;
        existingRow.find('.discount_amt').val(0);
    }

    const discountPercentage = (price * newQty) > 0 ? (discountAmount / (price * newQty)) * 100 : 0;
    existingRow.find('.percent_discount').val(discountPercentage.toFixed(2));
    updateTotalPrice(existingRow, bill_type);
}

function discountTotalPercent() {
    var totalPrice = 0;
    $(".amount_array").each(function () {
        var value = $(this).val();
        totalPrice += Number(value);
    });

    //Extra Charges
    var extra_charges = extra_amount();
    totalPrice = totalPrice + extra_charges;

    var discountPercentage = parseFloat($('#percent_total_discount').val()) || 0;

    if (discountPercentage < 0) {
        discountPercentage = 0;
        $('#percent_total_discount').val(0);
    }

    if (totalPrice <= 0) {
        $('#discount_total_amount').val(0);
    } else {
        const discountAmount = (discountPercentage / 100) * totalPrice;
        $('#discount_total_amount').val(discountAmount.toFixed(2));
    }

    total_hidden_posted();
}

function discountTotalAmount() {
    var totalPrice = 0;
    $(".amount_array").each(function () {
        var value = $(this).val();
        totalPrice += Number(value);
    });

    //Extra Charges
    var extra_charges = extra_amount();
    totalPrice = totalPrice + extra_charges;

    var discountAmount = parseFloat($('#discount_total_amount').val()) || 0;

    if (discountAmount < 0) {
        discountAmount = 0;
        $('#discount_total_amount').val(0);
    }

    if (totalPrice <= 0) {
        $('#percent_total_discount').val(0);
    } else {
        const discountPercentage = (discountAmount / totalPrice) * 100;
        $('#percent_total_discount').val(discountPercentage.toFixed(2));
    }

    total_hidden_posted();
}

function updateBillType() {
    var bill_type = $('#gst_type').find(":selected").val();

    $("#dataTable tr").each(function () {
        var tr_id = $(this).attr('id');
        if (!tr_id) return; // skip rows without id
        var existingRow = $('#' + tr_id);
        updateTotalPrice(existingRow, bill_type, 1);
    });

}

function change_under_gst() {
    var under_gst = $('#under_gst').find(":selected").val();

    $("#dataTable tr").each(function () {

        var tr_id = $(this).attr('id');
        var existingRow = $('#' + tr_id);
        var gstvalue = existingRow.find('.gst_array').val();

        if (under_gst == 'Local') {
            var gstdevide = gstvalue / 2;
            var gstTitle = "CGST: " + gstdevide + "%, SGST: " + gstdevide + "%";
        } else {
            var gstTitle = "IGST: " + gstvalue + "%";
        }

        existingRow.find('.gst_array').attr('data-original-title', gstTitle);
    });

}
function updateTotalPrice(existingRow, bill_type, skipdiscount = 0) {

    discountTotalAmount();

    let price    = parseFloat(existingRow.find('.pprice').val())   || 0;
    let newQty   = parseFloat(existingRow.find('.qtyval').val())   || 0;
    let gst      = parseFloat(existingRow.find('.gst_array').val()) || 0;
    let discountPercentage = parseFloat(existingRow.find('.percent_discount').val()) || 0;
    let discount_amt       = parseFloat(existingRow.find('.discount_amt').val())     || 0;

    // Recalculate discount_amt from % when qty changes (skipdiscount=1)
    if (skipdiscount == 1 && discountPercentage > 0) {
        discount_amt = parseFloat(((discountPercentage / 100) * (price * newQty)).toFixed(2));
        existingRow.find('.discount_amt').val(discount_amt.toFixed(2));
    }

    // Net Amount = (qty × PP) − discount
    let net_price = (newQty * price) - discount_amt;
    if (net_price < 0) net_price = 0;

    // Keep after_pp in sync (per-unit after-discount price)
    existingRow.find('.after_pp').val(net_price.toFixed(2));

    // Net Amount in the table IS net_price (what was after_pp × qty, i.e. total after discount)
    let net_amount = net_price;

    var gst_amount, amount;
    if (bill_type == 'Inclusive') {
        gst_amount = parseFloat(((net_amount * gst) / (100 + gst)).toFixed(2));
        amount     = parseFloat(net_amount.toFixed(2));
    } else if (bill_type == 'Exclusive') {
        gst_amount = parseFloat(((net_amount * gst) / 100).toFixed(2));
        amount     = parseFloat((net_amount + gst_amount).toFixed(2));
    } else {
        gst_amount = 0;
        amount     = parseFloat(net_amount.toFixed(2));
    }

    var taxable_amount = parseFloat((amount - gst_amount).toFixed(2));

    existingRow.find('.net_array').val(net_amount.toFixed(2));
    existingRow.find('.gstval').val(gst_amount.toFixed(2));
    existingRow.find('.taxable_amount').val(taxable_amount.toFixed(2));
    existingRow.find('.amount_array').val(amount.toFixed(2));

    updatePopupBillAmount();
    total_hidden_posted();
}

function total_hidden_posted() {
    var qArray   = 0;
    var nArray   = 0;
    var gArray   = 0;
    var taxArray = 0;
    var amtArray = 0;

    $(".qty_array").each(function () {
        qArray += Number($(this).val()) || 0;
    });

    $(".net_array").each(function () {
        nArray += Number($(this).val()) || 0;
    });

    $(".gstval").each(function () {
        gArray += Number($(this).val()) || 0;
    });

    $(".taxable_amount").each(function () {
        taxArray += Number($(this).val()) || 0;
    });

    $(".amount_array").each(function () {
        amtArray += Number($(this).val()) || 0;
    });

    // Extra Charges
    var extra_charges = extra_amount();

    // Adjustment Charge
    var adjust_amount = $('#discount_total_amount').val();
    var adjust = (adjust_amount.trim() === '') ? 0 : parseFloat(adjust_amount) || 0;

    $('.total_quantity').val(JSON.stringify(qArray));
    $('.total_net_amount').val(JSON.stringify(nArray.toFixed(2)));
    $('.total_gst_amount').val(JSON.stringify(gArray.toFixed(2)));
    $('.total_amount').val(JSON.stringify(amtArray.toFixed(2)));

    $('#quantity_val').text(qArray);
    $('#total_net_amount2').text('₹' + nArray.toFixed(2));
    $('#total_gst_amount2').text('₹' + gArray.toFixed(2));
    $('#total_taxable_amount2').text('₹' + taxArray.toFixed(2));
    $('#total_amount2').text('₹' + ((amtArray + extra_charges) - adjust).toFixed(2));
}

function extra_amount() {

    // Extra Charges
    var packing_charge  = ($('#packing_amount').val() || '').trim();
    var delivery_charge = ($('#delivery_amount').val() || '').trim();
    var other_charge    = ($('#other_amount').val() || '').trim();
    var labour_charge   = ($('#labour_amount').val() || '').trim();

    var packing  = (packing_charge === '') ? 0 : parseFloat(packing_charge);
    var delivery = (delivery_charge === '') ? 0 : parseFloat(delivery_charge);
    var other    = (other_charge === '') ? 0 : parseFloat(other_charge);
    var labour   = (labour_charge === '') ? 0 : parseFloat(labour_charge);

    var extra_charges = packing + delivery + other + labour;

    return extra_charges;
}

function updatesrno(tableID) {
    var srno = 0;
    $('#' + tableID + " tr").each(function (i, v) {
        srno++;
        $('#' + v.id).find('.s_no').text(srno);
    });
}

function deleteRow(tableID) {

    var checkedvalue = [];
    $('input[type="checkbox"]:checked').each(function (k, v) {

        var rowCount = $('#' + tableID + " tr").length;
        if (rowCount > 1) {
            var chkedval = $(this).val();

            $(this).closest('tr').remove();
            updateBillType();

            if (chkedval != '' && chkedval != 'on') {
                checkedvalue.push(chkedval);
            }
        } else {
            alert("Cannot delete all the rows.");
            return false;
        }


    });

    if (checkedvalue != '' && checkedvalue != 'on' && checkedvalue.length != 0) {

        $.ajax({
            type: 'POST',
            dataType: "json",
            url: 'get_ajaxdata?function=deletePurchase',
            data: {
                id: checkedvalue
            },
            success: function (result) {
                if (result.status === false) {
                    alert('Something went wrong!');
                }
            }
        });
    }
    updatesrno(tableID);
}

/* Update "Your Bill Amount" in the payment popup whenever totals change */
function updatePopupBillAmount() {
    var amtArray = 0;
    $(".amount_array").each(function () {
        var v = parseFloat($(this).val()) || 0;
        amtArray += v;
    });
    var extra_charges = extra_amount();
    var adjust_amount = parseFloat($('#discount_total_amount').val()) || 0;
    var total = ((amtArray + extra_charges) - adjust_amount).toFixed(2);
    $('#popup_bill_amount').text('₹' + total);
}

/* ── Fix 1: Delete table row ─────────────────────────────────────────────── */
function deleteTableRow(btn) {
    var row = $(btn).closest('tr');
    if (confirm('Delete this row?')) {
        row.remove();
        // Renumber SN column
        $('#dataTable tr').each(function(i) {
            $(this).find('td:nth-child(2)').contents().filter(function(){
                return this.nodeType === 3; // text nodes
            }).first().replaceWith(i + 1);
        });
        total_hidden_posted();
        updatePopupBillAmount();
    }
}

/* ── Fix 3: Article No search ────────────────────────────────────────────── */
function showItemByArticle(sku) {
    var gst_type  = $('#gst_type').find(':selected').val();
    var under_gst = $('#under_gst').find(':selected').val();
    var trcount   = parseInt($('#dataTable tr').length);

    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: 'get_ajaxdata?function=get_item_info_by_sku',
        data: { sku: sku, gst_type: gst_type, under_gst: under_gst, trcount: trcount },
        success: function (result) {
            if (result.status === true) {
                $('#article_no').val('');
                $('#barcode_number').focus();
                var existingRow = $('#row_' + result.barcode_no);
                if (existingRow.length) {
                    var qtyInput = existingRow.find('.qtyval');
                    qtyInput.val(parseInt(qtyInput.val()) + 1);
                    updateTotalPrice(existingRow, $('#gst_type').find(':selected').val(), 1);
                } else {
                    $('#dataTable').append(result.html);
                    total_hidden_posted();
                }
            } else {
                alert('Item not found for Article No: ' + sku);
            }
        }
    });
}

function showItemByArticleId(itemId) {
    $.ajax({
        url: 'get_ajaxdata?function=get_item_details',
        method: 'POST',
        data: { term: itemId, type: 'id' },
        success: function(res) {
            var result = JSON.parse(res);
            if (result.status) {
                var barcode = result.suggestions[0]['Barcode No.'];
                $('#barcode_number').val(barcode);
                show_item_detail();
            }
        }
    });
}

/* ── Fix 4: Popup A/C Code / Mobile / Name autofill ─────────────────────── */
function getPopupAccountDetails(term, type) {
    if (!term || term.length < 1) {
        $('#popup_ac_suggestions').hide();
        return;
    }
    $.ajax({
        url: 'get_ajaxdata?function=get_account_details',
        method: 'POST',
        data: { term: term, type: type },
        success: function(res) {
            var result = JSON.parse(res);
            if (!result.status) { $('#popup_ac_suggestions').hide(); return; }
            var html = '';
            $.each(result.suggestions, function(id, row) {
                html += '<div class="popup-ac-suggestion autocomplete-suggestion" ' +
                        'data-code="' + $('<div>').text(row.account_code).html() + '" ' +
                        'data-name="' + $('<div>').text(row.account_name).html() + '" ' +
                        'data-mobile="' + $('<div>').text(row.mobile_no).html() + '">' +
                        row.display + '</div>';
            });
            $('#popup_ac_suggestions').html(html).show();
        }
    });
}

function fillPopupAccount(code, name, mobile) {
    document.querySelector('#modernPopup-save input[name="ac_code"]').value    = code;
    document.querySelector('#modernPopup-save input[name="account_name"]').value = name;
    document.querySelector('#modernPopup-save input[name="mobile_number"]').value = mobile;
    $('#popup_ac_suggestions').hide();
}

// ============================================================================
// GROUP DETAILS POPUP FUNCTIONS
// ============================================================================
var _activeGroupRow = null;

function openPopup(btn) {
    var errBanner = document.getElementById('grp_error_msg');
    errBanner.style.display = 'none';

    ['grp_sub_category','grp_brand','grp_color','grp_style'].forEach(function(id){
        var el = document.getElementById(id);
        el.value = '';
        el.style.borderColor = '';
    });

    _activeGroupRow = btn ? $(btn).closest('tr') : null;

    if (_activeGroupRow) {
        var sc = _activeGroupRow.find('input[name="sub_category[]"]').val();
        var br = _activeGroupRow.find('input[name="brand[]"]').val();
        var co = _activeGroupRow.find('input[name="color[]"]').val();
        var st = _activeGroupRow.find('input[name="style[]"]').val();
        if (sc) document.getElementById('grp_sub_category').value = sc;
        if (br) document.getElementById('grp_brand').value = br;
        if (co) document.getElementById('grp_color').value = co;
        if (st) document.getElementById('grp_style').value = st;
    }

    document.getElementById('modernPopup').classList.add('active');
    document.getElementById('grp_sub_category').focus();
}

function closePopup() {
    document.getElementById('modernPopup').classList.remove('active');
}

function saveGroupDetails() {
    var subCat = document.getElementById('grp_sub_category').value;
    var brand  = document.getElementById('grp_brand').value.trim();
    var color  = document.getElementById('grp_color').value.trim();
    var style  = document.getElementById('grp_style').value.trim();

    document.getElementById('grp_sub_category').style.borderColor = '';
    document.getElementById('grp_error_msg').style.display = 'none';

    if (!subCat) {
        document.getElementById('grp_error_text').textContent = 'SubGroup Name is required.';
        document.getElementById('grp_error_msg').style.display = 'flex';
        document.getElementById('grp_sub_category').style.borderColor = '#e53935';
        document.getElementById('grp_sub_category').focus();
        return;
    }

    if (!_activeGroupRow || _activeGroupRow.length === 0) {
        alert('Row not found. Please try again.');
        return;
    }

    _activeGroupRow.find('input[name="sub_category[]"]').val(subCat);
    _activeGroupRow.find('input[name="brand[]"]').val(brand);
    _activeGroupRow.find('input[name="color[]"]').val(color);
    _activeGroupRow.find('input[name="style[]"]').val(style);

    var catDisplay = _activeGroupRow.find('input.group-category-display');
    if (catDisplay.length) {
        var summary = subCat + (brand ? ' | ' + brand : '') + (color ? ' | ' + color : '') + (style ? ' | ' + style : '');
        catDisplay.attr('title', summary);
        catDisplay.css('border-color', '#009688');
        setTimeout(function(){ catDisplay.css('border-color', ''); }, 1500);
    }

    closePopup();
}

// ============================================================================
// PP DISCOUNT POPUP FUNCTIONS
// ============================================================================
var _activePPRow = null;

function openPopup1(btn) {
    _activePPRow = btn ? $(btn).closest('tr') : null;

    document.getElementById('pp_error_msg').style.display = 'none';
    ['pp_discount_pct','pp_discount_amt'].forEach(function(id){
        document.getElementById(id).style.borderColor = '';
    });

    if (_activePPRow) {
        var pp  = parseFloat(_activePPRow.find('.pprice').val()) || 0;
        var qty = parseFloat(_activePPRow.find('.qtyval').val()) || 1;
        var pct = parseFloat(_activePPRow.find('.percent_discount').val()) || 0;
        var amt = parseFloat(_activePPRow.find('.discount_amt').val()) || 0;
        var totalPP  = pp * qty;
        var afterPP  = amt > 0 ? (totalPP - amt).toFixed(2) : totalPP.toFixed(2);

        document.getElementById('pp_purchase_price').value = totalPP.toFixed(2);
        document.getElementById('pp_discount_pct').value   = pct > 0 ? pct : '';
        document.getElementById('pp_discount_amt').value   = amt > 0 ? amt : '';
        document.getElementById('pp_after_pp').value       = afterPP;
    }

    document.getElementById('modernPopup-pur').classList.add('active');
    document.getElementById('pp_discount_pct').focus();
}

function closePopup1() {
    document.getElementById('modernPopup-pur').classList.remove('active');
}

function syncPPFromPercent() {
    var pp  = parseFloat(document.getElementById('pp_purchase_price').value) || 0;
    var pct = parseFloat(document.getElementById('pp_discount_pct').value)   || 0;
    var amt = (pct / 100) * pp;
    document.getElementById('pp_discount_amt').value = amt.toFixed(2);
    document.getElementById('pp_after_pp').value     = (pp - amt).toFixed(2);
}

function syncPPFromAmount() {
    var pp  = parseFloat(document.getElementById('pp_purchase_price').value) || 0;
    var amt = parseFloat(document.getElementById('pp_discount_amt').value)   || 0;
    var pct = pp > 0 ? (amt / pp) * 100 : 0;
    document.getElementById('pp_discount_pct').value = pct.toFixed(2);
    document.getElementById('pp_after_pp').value     = (pp - amt).toFixed(2);
}

function savePPDetails() {
    var pct = document.getElementById('pp_discount_pct').value.trim();
    var amt = document.getElementById('pp_discount_amt').value.trim();

    document.getElementById('pp_error_msg').style.display = 'none';
    ['pp_discount_pct','pp_discount_amt'].forEach(function(id){
        document.getElementById(id).style.borderColor = '';
    });

    if (pct === '' && amt === '') {
        document.getElementById('pp_error_text').textContent = 'Please enter Discount % or Discount Amount.';
        document.getElementById('pp_error_msg').style.display = 'flex';
        document.getElementById('pp_discount_pct').style.borderColor = '#e53935';
        document.getElementById('pp_discount_amt').style.borderColor = '#e53935';
        return;
    }

    if (!_activePPRow || _activePPRow.length === 0) {
        alert('Row not found. Please try again.');
        return;
    }

    _activePPRow.find('.percent_discount').val(pct !== '' ? parseFloat(pct).toFixed(2) : '0');
    _activePPRow.find('.discount_amt').val(amt !== '' ? parseFloat(amt).toFixed(2) : '0');

    var afterPP = parseFloat(document.getElementById('pp_after_pp').value) || 0;
    _activePPRow.find('.after_pp').val(afterPP.toFixed(2));

    _activePPRow.find('.net_array').val(afterPP.toFixed(2));

    var bill_type = $('#gst_type').find(':selected').val();
    var gst = parseFloat(_activePPRow.find('.gst_array').val()) || 0;
    var gst_amount, amount;
    if (bill_type == 'Inclusive') {
        gst_amount = parseFloat(((afterPP * gst) / (100 + gst)).toFixed(2));
        amount     = parseFloat(afterPP.toFixed(2));
    } else if (bill_type == 'Exclusive') {
        gst_amount = parseFloat(((afterPP * gst) / 100).toFixed(2));
        amount     = parseFloat((afterPP + gst_amount).toFixed(2));
    } else {
        gst_amount = 0;
        amount     = parseFloat(afterPP.toFixed(2));
    }
    var taxable_amount = parseFloat((amount - gst_amount).toFixed(2));

    _activePPRow.find('.gstval').val(gst_amount.toFixed(2));
    _activePPRow.find('.taxable_amount').val(taxable_amount.toFixed(2));
    _activePPRow.find('.amount_array').val(amount.toFixed(2));

    updatePopupBillAmount();
    total_hidden_posted();

    _activePPRow.find('.net_array').css('border-color','#009688');
    setTimeout(function(){ _activePPRow.find('.net_array').css('border-color',''); }, 1500);

    closePopup1();
}

// ============================================================================
// MRP DISCOUNT POPUP FUNCTIONS
// ============================================================================
var _activeMRPRow = null;

function openPopupmrp(btn) {
    _activeMRPRow = btn ? $(btn).closest('tr') : null;
    document.getElementById('mrp_error_msg').style.display = 'none';
    ['mrp_discount_pct','mrp_discount_amt'].forEach(function(id){
        document.getElementById(id).value = '';
        document.getElementById(id).style.borderColor = '';
    });
    if (_activeMRPRow) {
        var mrp = parseFloat(_activeMRPRow.find('.mrp').val()) || 0;
        var dp  = parseFloat(_activeMRPRow.find('.discount_percent2').val()) || 0;
        var da  = parseFloat(_activeMRPRow.find('.discount_amount2').val()) || 0;
        var sp  = parseFloat(_activeMRPRow.find('.selling_price').val()) || mrp;
        document.getElementById('mrp_current').value = mrp.toFixed(2);
        if (dp > 0) document.getElementById('mrp_discount_pct').value = dp;
        if (da > 0) document.getElementById('mrp_discount_amt').value = da;
        document.getElementById('mrp_selling_price').value = sp.toFixed(2);
    }
    document.getElementById('modernPopup-mrp').classList.add('active');
    document.getElementById('mrp_discount_pct').focus();
}

function closePopupmrp() {
    document.getElementById('modernPopup-mrp').classList.remove('active');
}

function syncMRPFromPercent() {
    var mrp = parseFloat(document.getElementById('mrp_current').value) || 0;
    var pct = parseFloat(document.getElementById('mrp_discount_pct').value) || 0;
    var amt = (pct / 100) * mrp;
    document.getElementById('mrp_discount_amt').value = amt.toFixed(2);
    document.getElementById('mrp_selling_price').value = (mrp - amt).toFixed(2);
}

function syncMRPFromAmount() {
    var mrp = parseFloat(document.getElementById('mrp_current').value) || 0;
    var amt = parseFloat(document.getElementById('mrp_discount_amt').value) || 0;
    var pct = mrp > 0 ? (amt / mrp) * 100 : 0;
    document.getElementById('mrp_discount_pct').value = pct.toFixed(2);
    document.getElementById('mrp_selling_price').value = (mrp - amt).toFixed(2);
}

function saveMRPDetails() {
    var pct = document.getElementById('mrp_discount_pct').value.trim();
    var amt = document.getElementById('mrp_discount_amt').value.trim();
    document.getElementById('mrp_error_msg').style.display = 'none';
    ['mrp_discount_pct','mrp_discount_amt'].forEach(function(id){
        document.getElementById(id).style.borderColor = '';
    });

    if (pct === '' && amt === '') {
        document.getElementById('mrp_error_text').textContent = 'Please enter Discount % or Discount Amount.';
        document.getElementById('mrp_error_msg').style.display = 'flex';
        document.getElementById('mrp_discount_pct').style.borderColor = '#e53935';
        document.getElementById('mrp_discount_amt').style.borderColor = '#e53935';
        return;
    }
    if (!_activeMRPRow || _activeMRPRow.length === 0) { closePopupmrp(); return; }

    var sp = parseFloat(document.getElementById('mrp_selling_price').value) || 0;

    _activeMRPRow.find('.discount_percent2').val(pct !== '' ? parseFloat(pct).toFixed(2) : '0');
    _activeMRPRow.find('.discount_amount2').val(amt !== '' ? parseFloat(amt).toFixed(2) : '0');

    _activeMRPRow.find('.selling_price').val(sp.toFixed(2));

    _activeMRPRow.find('.selling_price').css('border-color','#009688');
    setTimeout(function(){ _activeMRPRow.find('.selling_price').css('border-color',''); }, 1500);

    closePopupmrp();
}

// ============================================================================
// POPUP SAVE FUNCTIONS
// ============================================================================
function openPopupsave(){
    updatePopupBillAmount();
    document.getElementById("modernPopup-save").classList.add("active");
}

function closePopupsave(){
    document.getElementById("modernPopup-save").classList.remove("active");
}

// ============================================================================
// PAYMENT MODE HANDLER
// ============================================================================
function handlePaymentMode(val) {
    ['pay_cash','pay_upi','pay_card','pay_credit'].forEach(function(id) {
        var el = document.getElementById(id);
        el.disabled = true;
        el.value = '';
        el.closest('.form-group-modern').style.opacity = '0.4';
    });
    var map = {
        'CASH':   ['pay_cash'],
        'UPI':    ['pay_upi'],
        'CARD':   ['pay_card'],
        'CREDIT': ['pay_credit'],
        'DUAL':   ['pay_cash','pay_upi','pay_card','pay_credit']
    };
    if (map[val]) {
        map[val].forEach(function(id) {
            var el = document.getElementById(id);
            el.disabled = false;
            el.closest('.form-group-modern').style.opacity = '1';
            if (map[val].length === 1) el.focus();
        });

        // Auto-fill bill amount for single payment modes only (skip DUAL)
        if (val !== 'DUAL' && map[val].length === 1) {
            var billAmount = parseFloat(
                $('#popup_bill_amount').text().replace('₹', '').trim()
            ) || 0;
            document.getElementById(map[val][0]).value = billAmount.toFixed(2);
        }
    }
}

// ============================================================================
// FORM VALIDATION AND SUBMISSION
// ============================================================================
function validateAndSubmitPopup() {
    var errors = [];
    var errorBanner = document.getElementById('popup_error_msg');
    var errorText   = document.getElementById('popup_error_text');

    ['payment_mode_select','pay_cash','pay_upi','pay_card','pay_credit'].forEach(function(id){
        var el = document.getElementById(id);
        if (el) el.style.borderColor = '';
    });
    ['gst_type','under_gst','bill_type','purchage_date','bill_number','invoice_number'].forEach(function(nm){
        var el = document.querySelector('[name="' + nm + '"]');
        if (el) el.style.borderColor = '';
    });

    var mainFields = [
        { name: 'purchage_date',    label: 'Purchase Date'   },
        { name: 'bill_number',      label: 'Bill No.'        }
    ];
    var hasMain = false;
    mainFields.forEach(function(f){
        var el = document.querySelector('[name="' + f.name + '"]');
        if (!el || el.value.trim() === '') {
            errors.push(f.label + ' is required');
            if (el) el.style.borderColor = '#e53935';
            hasMain = true;
        }
    });

    var sidebarFields = [
        { name: 'gst_type',  label: 'GST Type'  },
        { name: 'under_gst', label: 'Under GST' },
        { name: 'bill_type', label: 'Bill Type' }
    ];
    var hasSidebar = false;
    sidebarFields.forEach(function(f){
        var el = document.querySelector('[name="' + f.name + '"]');
        if (!el || el.value === '') {
            errors.push(f.label + ' is required (Additional Data)');
            if (el) el.style.borderColor = '#e53935';
            hasSidebar = true;
        }
    });

    var rowCount = $('#dataTable tr').length;
    if (rowCount < 1) {
        errors.push('Please add at least one item');
    }

    var payMode = document.getElementById('payment_mode_select');
    if (!payMode || payMode.value === '') {
        errors.push('Payment Mode is required');
        if (payMode) payMode.style.borderColor = '#e53935';
    } else {
        var modeMap = {
            'CASH':   ['pay_cash'],
            'UPI':    ['pay_upi'],
            'CARD':   ['pay_card'],
            'CREDIT': ['pay_credit'],
            'DUAL':   ['pay_cash','pay_upi','pay_card','pay_credit']
        };
        var activeFields = modeMap[payMode.value] || [];
        var anyFilled = false;
        activeFields.forEach(function(id){
            var el = document.getElementById(id);
            if (el && el.value.trim() !== '') anyFilled = true;
        });
        if (!anyFilled) {
            errors.push('Please enter at least one payment amount');
            activeFields.forEach(function(id){
                var el = document.getElementById(id);
                if (el && !el.disabled) el.style.borderColor = '#e53935';
            });
        }
    }

    if (errors.length > 0) {
        var msg = errors.map(function(e){ return '• ' + e; }).join('<br>');
        if (hasSidebar) {
            msg += '<br><span style="font-size:12px;color:#777;margin-top:4px;display:block;">👉 Click <b>Additional Data</b> button to fill missing fields.</span>';
        }
        if (hasMain) {
            msg += '<br><span style="font-size:12px;color:#777;margin-top:4px;display:block;">👉 Close this popup and fill the highlighted fields on the main form.</span>';
        }
        errorText.innerHTML = msg;
        errorBanner.style.display = 'flex';
        errorBanner.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        return;
    }

    errorBanner.style.display = 'none';
    document.getElementById('purchage_form').submit();
}

// ============================================================================
// RECORD MANAGEMENT FUNCTIONS
// ============================================================================
function view_details(id) {
    $('#common-popup').load('popup/view-purchase-details.php?id=' + id,
        function() {
            $('#common-popup').modal('show');
        });
}

function deleterecord(id) {
    $('#common-popup').load('popup/deleterecord.php',
        function() {
            $('#common-popup').modal('show');
            $('#common-popup').modal('show').one('click', '#confirm', function() {
                $('#deleteid').val(id);
                $('#deleteform').submit();
            });
        });
}

function sendMail(id) {
    $('#common-popup').load('popup/send-mail.php?id='+id,
        function() {
            $('#common-popup').modal('show');
            $('#common-popup').modal('show').one('click', '#confirm', function() {
                $('#mailid').val(id);
                $('#sendmailform').submit();
            });
        });
}

// ============================================================================
// SIDEBAR TOGGLE FUNCTIONS
// ============================================================================
document.addEventListener('DOMContentLoaded', function() {
    const filterBtn = document.getElementById("filterToggle");
    const closeBtn = document.getElementById("closePanel");
    const leftPanel = document.getElementById("leftPanel");
    const overlay = document.getElementById("filterOverlay");

    if (filterBtn) {
        filterBtn.onclick = function() {
            leftPanel.classList.add("active");
            overlay.classList.add("active");
        };
    }

    if (closeBtn) {
        closeBtn.onclick = function() {
            leftPanel.classList.remove("active");
            overlay.classList.remove("active");
        };
    }

    if (overlay) {
        overlay.onclick = function() {
            leftPanel.classList.remove("active");
            overlay.classList.remove("active");
        };
    }
});