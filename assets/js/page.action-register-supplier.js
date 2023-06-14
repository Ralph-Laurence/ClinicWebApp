var dialog  = undefined;
var confirm = undefined;

let validation = 
[
    {   el: $("#fname"),            msg: "Please enter a valid firstname!" },
    {   el: $("#lname"),            msg: "Please enter a valid lastname!"  },
    {   el: $("#email"),            msg: "Please add a valid email!"       },
    {   el: $(".user-type"),        msg: "Please select a user type!"      },
    {   el: $("#username"),         msg: "Please add a valid username!"    },
    {   el: $("#password"),         msg: "Please add a valid password!"    },
    {   el: $("#retype-password"),  msg: "Passwords didn't matched!"       },
];

$(document).ready(function () {
    onAwake();
});

function onAwake() 
{  
    dialog  = new AlertDialog();
    confirm = new ConfirmDialog();

    Input.forceAlphaNums("alphanum", true);
    Input.forceNumeric("numeric");

    notify();
    onBind();
}

function onBind() 
{  
    $(".btn-submit").click(function () 
    {  
        if (System.isNullOrEmpty($(".input-supname").val()))
        {
            showError("Please enter a valid supplier name!");
            $(".input-supname").focus();
            return;
        }

        if (System.isNullOrEmpty($(".input-contact").val()))
        {
            showError("Please enter a valid contact number!");
            $(".input-contact").focus();
            return;
        }

        $("#register-form").trigger("submit");
    });

    $(".btn-cancel").click(function()
    {
        var msg = "Your changes won't be saved if you cancel.\n\nDo you wish to abort the operation?";

        confirm.actionOnOK = function()
        {
            var goBack = $(".goback").val();
            navHref(goBack);
        };

        confirm.show(msg, "Register Supplier", "Yes", "No");
    });
}

function notify() 
{  
    var err = $(".err-msg").val();

    if (!System.isNullOrEmpty(err))
        showError(err);
}

function showError(err) 
{  
    $(".error-message .error-label").text(err);
    $(".error-message").fadeIn();
}