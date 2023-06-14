var btnSubmit = undefined;

var errorBox = undefined;
var errorLabel = undefined;

var confirm = undefined;
var dialog = undefined;
var toast = undefined; 
var snackbar = undefined;

$(document).ready(function(){
    onAwake();
}); 

function onAwake()
{ 
    dialog = new AlertDialog();
    confirm = new ConfirmDialog();
    toast = new Toast();
    snackbar = new SnackBar();

    btnSubmit = $(".btn-save");

    errorBox    = $(".error-message");
    errorLabel  = $(".error-label");

    $(function () 
    {
        $(".degree-option").selectmenu({
            change: function (event, ui) {
                $('.degreeLabel').val(ui.item.label)
            }
        });

        $(".spec-option").selectmenu({
            change: function (event, ui) {
                $('.specLabel').val(ui.item.label)
            }
        });

    });

    bindLastInputData();

    notify();
    onBind();
}
 
function onBind()
{ 
    // Force Alpha fields to accept only letters and dashes
    // Force Numeric fields to accept only numbers
    Input.forceNumeric("numeric");
    Input.forceAddress("address");
    Input.forceAlpha("alpha");
  
    btnSubmit.click(() => handleSubmit());
 
    $(".btn-reset").click(() => 
    {
        confirm.actionOnOK = function () 
        {
            $("#register-form").trigger("reset");
            toast.show("Form has been cleared.", "Reset Form", toast.toastTypes.SUCCESS);

            hideError();
        }; 

        confirm.show("Reset registration details?", "Reset Data", "OK", "Cancel");
    });

    //
    // During edit mode, track for the cancel button click
    //
    $(".btn-cancel").click(() => 
    {
        confirm.actionOnOK = function () 
        {
            redirectTo($(".on-cancel").val());
        }; 

        confirm.show("Your changes won't be saved when you leave. Do you wish to abort the operation?", 
        "Cancel Operation", "Yes", "No");
    });
}

function bindLastInputData()
{ 
    var spec   = $(".specLabel").val();
    var degree = $(".degreeLabel").val();
      
    $(function () 
    {
        // Load last spec value
        var specs = $(".spec-option")[0].options;

        $(specs).each(function (idx, option) 
        {
            if ($(option).text() == spec) {
                $(".spec-option").val($(option).val()).selectmenu('refresh');
                return;
            }
        });

        // Load last degree value 
        var degrees = $(".degree-option")[0].options;

        $(degrees).each(function (idx, option) 
        {
            if ($(option).text() == degree) {
                $(".degree-option").val($(option).val()).selectmenu('refresh');
                return;
            }
        });

    });
}

//
// Validate doctor registration
//
function handleSubmit()
{
    var validation = 
    [
        {   el: $("#fname"),        msg: "Please enter a valid firstname!"          },
        {   el: $("#mname"),        msg: "Please enter a valid middlename!"         },
        {   el: $("#lname"),        msg: "Please enter a valid lastname!"           },
        {   el: $("#contact"),      msg: "Please add a contact number!"             },
        {   el: $("#regnum"),       msg: "Please add a registration number!"        },
        {   el: $(".spec-option"),  msg: "Please select a doctor's specialization!" },
    ];

    for(let obj of validation)
    {   
        if (System.isNullOrEmpty(obj.el.val())) 
        {
            showError(obj.msg); 
            obj.el.focus();
            return;
        }
    }
    
    $("#register-form").trigger("submit");
}

function notify()
{
    // Show Success Message
    var success = $(".success-msg").val();

    if (!System.isNullOrEmpty(success)) {
        snackbar.show(success);
    }

    // Show Error
    var err = $(".error-msg").val();

    if (!System.isNullOrEmpty(err)) {
        showError(err);
    } 
}

function showError(message)
{
    errorLabel.text(message);
    errorBox.fadeIn('fast'); //show();
}

function hideError()
{
    errorLabel.text('');
    errorBox.hide();
}