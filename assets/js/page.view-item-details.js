var dialog   = undefined;
var confirm = undefined;

$(document).ready(function(){
    onAwake();
});

function onAwake()
{
    dialog   = new AlertDialog();
    confirm = new ConfirmDialog();

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

    $(".btn-discard").click(() => 
    {
        if (System.isNullOrEmpty($(".frm-discard .item-key").val()))
        {
            dialog.danger("This action can't be completed. Please reload the page and try again.");
            return;
        }

        confirm.actionOnOK = function () 
        {  
            $(".frm-discard").trigger("submit");
        };

        var msg = `<div class="mb-2">Are you sure, you want to dispose 
        <span class="font-primary-dark fst-italic">${$(".stock-amount-text").text().trim()}</span> 
        of the item <span class="fw-bold font-primary-dark fst-italic">"${$(".label-item-name").text().trim()}"</span> ?
        This item is no longer safe to use and must be moved to waste.</div>
        <div class="p-1 my-3 bg-document rounded-2 fsz-14"><i class="fas fa-info-circle me-1"></i>
        This will also reset the expiry date automatically.</div>
        Please confirm by clicking "Yes" or cancel by clicking "No".`;

        confirm.show(msg, "Discard Expired Stock", "Yes", "No", true);
    });
}