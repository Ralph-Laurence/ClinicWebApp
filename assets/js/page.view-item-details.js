var dialog   = undefined;
var confirm = undefined;
var snackbar = undefined;

$(document).ready(function(){
    onAwake();
});

function onAwake()
{
    dialog   = new AlertDialog();
    confirm = new ConfirmDialog();
    snackbar = new SnackBar();
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
        // var msg = `<div class="mb-2">Are you sure, you want to dispose 
        // <span class="font-primary-dark fst-italic">${$(".stock-amount-text").text().trim()}</span> 
        // of the item <span class="fw-bold font-primary-dark fst-italic">"${$(".label-item-name").text().trim()}"</span> ?
        // This item is no longer safe to use and must be moved to waste.</div>
        // <div class="p-1 my-3 bg-document rounded-2 fsz-14"><i class="fas fa-info-circle me-1"></i>
        // This will also reset the expiry date automatically.</div>
        // Please confirm by clicking "Yes" or cancel by clicking "No".`;
 // 
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
}

function notify()
{
    var success = $(".success-message").val();

    if (!System.isNullOrEmpty(success))
    {
        snackbar.show(success.trim());
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