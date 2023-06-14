var dialog   = undefined;
var snackbar = undefined;

$(document).ready(function(){
    onAwake();
});

function onAwake()
{
    dialog   = new AlertDialog();
    snackbar = new SnackBar();
 
    onBind();
}

function onBind()
{ 
    $(".dataset-body").on("click", ".btn-details", function () 
    {  
        var tr = $(this).closest('tr');
        var key = tr.find('.record-key').val();

        $(".frm-details #details-key").val(key);

        if (System.isNullOrEmpty( $(".frm-details #details-key").val() ))
        {
            dialog.danger("This action can't be completed. Please reload this page and try again.");
            return;
        }

        $(".frm-details").trigger("submit");
    });
} 