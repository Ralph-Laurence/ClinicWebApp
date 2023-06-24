var dialog = undefined;

$(document).ready(function () {
    onAwake();
});

function onAwake()
{
    $("#waste-reason").selectmenu({
        width: 180,
        change: function(event, ui)
        {
            hideError();
        }
    });

    dialog = new AlertDialog();
    onBind();
}

function onBind()
{
    $(".stocks-tbody tr").click(function()
    { 
        // Highlight the selected row
        $(".stocks-tbody tr.active").removeClass("active");
        $(this).addClass("active");

        // Get its stock key and other properties
        var isExpired = $(this).find(".best-before").attr("data-expired");
        var sku = $(this).find(".sku").text().trim();
        
        // If selected row is expired, exit
        if (isExpired != 0)
        {
            dialog.danger(`Cannot restock an expired stock as it is no longer safe to use. Please dispose of this stock immediately.\n\nStock / SKU:\n${sku}`);
            return;
        }

        // Get total qty. We will use this value whenever the "All" checkbox was ticked
        var qty = $(this).closest('tr').find(".stock-total-qty").val();

        // Do not allow stock out when qty is 0
        if (qty < 1)
        {
            dialog.danger(`Cannot pullout from an empty stock.\n\nStock / SKU:\n${sku}`);
            return;
        }

        // Get the reference to the stock by caching its key
        var stockKey = $(this).find(".stock-key").val();

        // Bind the values onto the fields
        $(".input-sku").val(sku).change(); 
        $(".input-qty").attr('data-total-qty', qty).focus();
        $(".input-stock-key").val(stockKey);
    });

    // Checkbox for setting disposal reasons
    $("#check-disposal").change(function()
    {
        var state = $(this).is(":checked");

        // Enable or disable the selectmenu depending on the checked state
        $("#waste-reason").prop("disabled", !state).selectmenu("refresh");
    });

    // Checkbox for pulling out all stock amounts at once
    $("#check-all-qty").change(function()
    { 
        if ($(this).is(":checked"))
        { 
            $(".input-qty").val($('.input-qty').attr('data-total-qty')).prop("readonly", true);
        }
        else
        {
            // CLear the value then focus on this field
            $(".input-qty").val('').prop("readonly", false).focus();
        }
    });

    // Unlock all disabled inputs when a stock was selected on the list
    $(".input-sku").change(function()
    {
        if ($(this).val())
        { 
            $(".initial-lock").prop("disabled", false);
            hideError();
        }
    });

    // Validate the inputs before saving
    $(".btn-save").click(function () 
    {  
        var payloadsValidation = validate();

        if (typeof payloadsValidation !== undefined && payloadsValidation)
        {
            $(".payload").val(payloadsValidation);
            $(".frm-stockout").trigger("submit");
        }
    }); 

    // Amount input should not go more than the maximum
    $(".input-qty").on('input', function()
    {  
        var maxValue = parseInt($(this).attr('data-total-qty'), 10);
        var minValue = 1;

        if ($(this).val() > maxValue) {
            $(this).val(maxValue);
        }
        else if ($(this).val() < minValue) {
            $(this).val(minValue);
        }
    });
}

function validate()
{ 
    var sku             = $(".input-sku").val();
    var stockKey        = $(".input-stock-key").val();
    var itemKey         = $(".input-item-key").val(); 
    var amount          = $(".input-qty").val();
    var pulloutAll      = $("#check-all-qty").is(":checked") ? 1 : 0;
    var disposalReason  = $("#waste-reason").val();
    var isDispose       = $("#check-disposal").is(":checked") ? 1 : 0;
    
    if ( (typeof stockKey === "undefined" && !stockKey) && (typeof itemKey === "undefined" && !itemKey) )
    {
        dialog.danger("This action can't be completed because of an error. Please reload the page and try again.");
        return undefined;
    }

    if (typeof sku === 'undefined' || !sku)
    {
        showError("Please select a stock from the list");
        return undefined;
    }

    if (!amount)
    {
        showError("Please enter an amount to pull out from the stock.");
        $(".input-qty").focus();
        return undefined;
    }

    // If dispose checkbox is ON, the user must select a reason
    if (isDispose != 0 && (!disposalReason || disposalReason == null))
    {
        showError("Please select a reason for disposal");
        return undefined;
    }

    // Build the payload values
    var payload = {
        stockKey: stockKey,
        itemKey: itemKey,
        amount: amount,
        pulloutAll : pulloutAll, 
        disposalReason: disposalReason
    };

    return JSON.stringify(payload);
}

function showError(msg)
{
    $(".error-box").text(msg).fadeIn('fast');
}

function hideError()
{
    $(".error-box").text('').fadeOut();
}