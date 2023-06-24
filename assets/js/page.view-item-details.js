var dialog   = undefined;
var confirm = undefined;
var snackbar = undefined; 

const dropdownElementList = [].slice.call(document.querySelectorAll('.btn-edit-expiry.dropdown-toggle'));
const editExpiryDropups = dropdownElementList.map((dropdownToggleEl) => 
{ 
    // return {
    //     dropDown: new mdb.Dropdown(dropdownToggleEl),
    //     datePicker: $(dropdownToggleEl).closest('.dropdup').find('.i-date-picker').datepicker('instance')
    // };

    return new mdb.Dropdown(dropdownToggleEl)
});


$(document).ready(function(){
    onAwake();
});

function onAwake()
{
    dialog   = new AlertDialog();
    confirm = new ConfirmDialog();
    snackbar = new SnackBar();

    $(".i-date-picker").datepicker({
        changeMonth: true,
        changeYear: true,
        yearRange: "c:c+10"
    });

    notify();
    onBind();
}  

function onBind() 
{  
    $(".btn-edit").click(() => 
    {
        var recordKey = $('.item-key').val();

        if (System.isNullOrEmpty(recordKey))
        {
            dialog.danger("This action can't be completed. Please reload the page and try again.");
            return;
        }
        
        $(".frm-edit #item-key").val(recordKey);
        $(".frm-edit").trigger("submit");
    });

    $(document).on("click", ".stocks-dataset-body tr .btn-discard", function()
    {
        if (System.isNullOrEmpty($(".frm-discard .item-key").val()))
        {
            dialog.danger("This action can't be completed. Please reload the page and try again.");
            return;
        }
 
        var tr = $(this).closest('tr');
        var sku = tr.find('.item-sku').text().trim();
        var qty = tr.find('.item-qty').val();
        
        var msg = `<div class="mb-2">
        The expired stock of <span class="fw-bold font-primary-dark fst-italic">"${sku}"</span> 
        with a quantity of '<span class="font-primary-dark fst-italic">${qty}(s)</span> ' are no longer safe to use and must be 
        discarded immediately.</div>
        <div class="mb-2">You can view the disposed stock in the Waste records later.</div>
        <div class="p-1 my-3 bg-document rounded-2 fsz-14"><i class="fas fa-info-circle me-1"></i>
        Make sure to throw away expired items to ensure safety</div>
        Please confirm by clicking "Yes" or cancel by clicking "No".`;

        confirm.actionOnOK = function () 
        {  
            var stockKey = tr.find('.stock-key').val();

            $(".frm-discard .stock-key").val(stockKey);

            $(".frm-discard").trigger("submit");
        };

        confirm.show(msg, "Discard Expired Stock", "Yes", "No", true);
    });

    $(".expired-filter").click(() => showExpired());
    $(".expired-show-all").click(() => showAll());
  
    $(".dropup").on("show.bs.dropdown", function()
    { 
        closeExpiryDropup();
    });
    
    $(".btn-close-expiry, .i-close-expiry-menu").click(() => closeExpiryDropup());

    $(".btn-save-expiry").click(function () 
    {  
        var date = $(this).closest('tr').find('.i-date-picker').val();
        var stockKey = $(this).attr("data-target-stock");

        $(".frm-edit-expiry .stock-key").val(stockKey);
        $(".frm-edit-expiry .new-expiry").val(date);
        
        if ( !$(".frm-edit-expiry .stock-key").val() || !$(".frm-edit-expiry .new-expiry").val())
        {
            dialog.danger("This action can't be completed because of an error. Please reload the page and try again");
            return;
        }

        $(".frm-edit-expiry").trigger("submit");
    });

    $(".i-date-picker").change(function()
    {
        if (!$(this).val())
        {
            $(".btn-save-expiry").prop('disabled', true);
            return;
        }

        $(".btn-save-expiry").prop('disabled', false);

        $(this).focus().blur();
        
        // Check if the date is valid
        // Expiry date must be a newer date than today of past days
        var selectedDate = $(".i-date-picker").datepicker("getDate");
        var isOlderThanToday = moment(selectedDate).isBefore(moment(), 'day');
        var isEqualToToday = moment(selectedDate).isSame(moment(), 'day');
 
        if (isOlderThanToday || isEqualToToday)
        { 
            $(".edit-expiry-error").fadeIn('fast');
            $(".edit-expiry-warning").fadeOut('fast');
        }
        else
        {
            $(".edit-expiry-error").fadeOut('fast');
            $(".edit-expiry-warning").fadeIn('fast');
        }
        // 
    });
}

function closeExpiryDropup()
{
    $(".i-date-picker").val('');
    $(".edit-expiry-error, .edit-expiry-warning").fadeOut('fast');
    $(".edit-expiry-warning").hide();
    editExpiryDropups.forEach((instance) => instance.hide());
}

function notify()
{
    var success = $(".success-message").val();

    if (!System.isNullOrEmpty(success))
    {
        snackbar.show(success.trim());
    }

    var error = $(".error-message").val();

    if (!System.isNullOrEmpty(error))
    {
        dialog.danger(error);
    }
}

function showExpired()
{
    $(".frm-details #filter").val('x');

    if ($(".frm-details #filter").val())
        $(".frm-details").trigger("submit");
}

function showAll()
{
    $(".frm-details #filter").val('');
    $(".frm-details").trigger("submit");
}