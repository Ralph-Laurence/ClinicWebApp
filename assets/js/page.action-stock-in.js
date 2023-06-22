$(document).ready(function () {
    onAwake();
});

function onAwake() 
{  
    $(".input-expiry").datepicker({
        changeMonth: true,
        changeYear: true,
        yearRange: 'c:c+10',
        // beforeShow: function (i) 
        // {
        //     if ($(i).attr('readonly')) {
        //         return false;
        //     }
        // }
    });
    
    // Force all numeric textboxes to accept only integers
    Input.forceNumeric("numeric");

    notify();
    onBind();
}

function onBind()
{
    $(".btn-save").click(function()
    {
        var valid = validate();

        if (valid)
            $(".frm-stockin").trigger("submit");
    });

    $("#expiry-check").change(function()
    { 
        var hasExpiry = $(this).is(":checked");

        if (hasExpiry)
        {
            $(".input-expiry").val('').attr("disabled", true);
        }
        else 
        {
            $(".input-expiry").removeAttr("disabled");
        }
    });
}

function validate()
{
    // validate inputs
    var qty = $(".input-qty");
    var sku = $(".input-sku");
    var exp = $(".input-expiry");
    
    var noExpiry = $("#expiry-check").is(":checked");

    if (!sku.val())
    {
        showError("Please enter a valid SKU / item code!");
        sku.focus();
        return false;
    }

    if (!qty.val())
    {
        qty.focus();
        showError("Please enter stock quantity!")
        return false;
    }

    if (!noExpiry)
    {
        if (!exp.val())
        {
            exp.focus();
            showError("Please set an expiry date!")
            return false;
        }
        else
        {
            // Expiry date must be a newer date than today of past days
            var selectedDate = $(".input-expiry").datepicker("getDate");
            var isOlderThanToday = moment(selectedDate).isBefore(moment(), 'day');
            var isEqualToToday = moment(selectedDate).isSame(moment(), 'day');

            if(isOlderThanToday || isEqualToToday)
            { 
                showError("Expiry date must NOT be a past date or equal to today's date.");
                return false;
            }
        }
    }
 
    return true;
}

function notify()
{
    var msg = $(".server-response").val();

    if (msg)
        showError(msg);
}

function showError(msg)
{
    $(".error-box").text(msg).fadeIn('fast');
}

function hideError()
{
    $(".error-box").text('').fadeOut();
}