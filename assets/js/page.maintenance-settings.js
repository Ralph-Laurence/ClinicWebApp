var dialog = undefined;
var snackbar = undefined;

$(document).ready(function () {
    onAwake();
});

function onAwake() 
{  
    dialog = new AlertDialog();
    snackbar = new SnackBar();

    notify();
    onBind();
}

function onBind() 
{   
    $("#record-year").selectmenu(
        {
            width: 120,
            change: function (event, ui) {
                $(".new-rec-year").val($(this).val());

                if (System.isNullOrEmpty($(".new-rec-year").val())) {
                    dialog.danger("This action can't be completed because of an error.");
                    return;
                }

                $(".frm-change-year").trigger("submit");
            }
        });

    $("#max-days").selectmenu(
        {
            width: 120,
            change: function (event, ui) {
                $(".new-max-days").val($(this).val());

                if (System.isNullOrEmpty($(".new-max-days").val())) {
                    dialog.danger("This action can't be completed because of an error.");
                    return;
                }

                $(".frm-change-max-days").trigger("submit");
            }
        });

    $("#checkup-form-action").selectmenu(
        {
            width: 120,
            change: function (event, ui) {
                $(".new-form-action").val($(this).val());

                if (System.isNullOrEmpty($(".new-form-action").val())) {
                    dialog.danger("This action can't be completed because of an error.");
                    return;
                }

                $(".frm-change-form-action").trigger("submit");
            }
        });
}

function notify() 
{  
    // Show toast when patient is successfully registered
    var success = $(".success-message").val();

    if (!System.isNullOrEmpty(success))
        snackbar.show(success);
}