var dialog   = undefined;
var snackbar = undefined;

let validation = 
[
    { el: $("#password"),         tag: ".error-tag-1", msg: "Please add a valid password!"  },
    { el: $("#retype-password"),  tag: ".error-tag-2", msg: "Please confirm the password!"  }, 
    { el: $("#username"),         tag: ".error-tag-3", msg: "Enter your username!"          }, 
    { el: $("#your-password"),    tag: ".error-tag-4", msg: "Enter your password!"          }, 
];

$(document).ready(function(){
    onAwake();
});

function onAwake()
{
    dialog   = new AlertDialog();
    snackbar = new SnackBar();

    notify();
    onBind();
}

function onBind()
{
    $(".btn-update").click(() => onValidate());

    $(".input-validation").on('input', function() 
    {
        if (!System.isNullOrEmpty($(this).val())) 
            $('.error-tag').fadeOut('fast');
    });

    $(".password-form").on("reset", function(e){
        $('.error-tag').hide();
    });
}

function onValidate()
{
    for(let obj of validation)
    {   
        if (System.isNullOrEmpty(obj.el.val())) 
        {
            $(obj.tag).text(obj.msg).fadeIn('fast'); 
            obj.el.focus();
            return;
        }
    }

    // Check password length
    if ($(validation[0].el).val().length < 4)
    {
        $(validation[0].tag).text("Password is too short!").fadeIn();
        return;
    }

    // Match the passwords
    if ($(validation[0].el).val() != $(validation[1].el).val())
    {
        $(validation[1].tag).text("Passwords didn't match!").fadeIn();
        return;
    } 

    $(".password-form").trigger("submit");
}

function notify()
{
    var success = $(".success-msg").val();

    if (!System.isNullOrEmpty(success))
        snackbar.show(success);

    var error = $(".error-msg").val();

    if (!System.isNullOrEmpty(error))
        dialog.danger(error);
}