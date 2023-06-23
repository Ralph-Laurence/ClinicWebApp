var expiryDate = undefined;

$(document).ready(function () 
{
    // Initialize expiry date
    expiryDate = $(".expiry-date").datepicker({
        changeMonth: true,
        changeYear: true,
        yearRange: 'c:c+5'
    }); 

    // Bind events of Date picker for expiry date
    $(".expiry-date").on("change", function () {
        $(this).focus().blur();
    });

    $("#chk-expiry").change(function () {
        var isChecked = $(this).is(":checked");

        if (!isChecked)
            $(".expiry-date").prop('disabled', false).focus();
        else
            $(".expiry-date").val('').blur().prop('disabled', true);
    });

});