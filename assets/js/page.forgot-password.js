$(document).ready(function () {
    onAwake();
});

function onAwake() 
{  
    notify();
    onBind();
}

function onBind() 
{  
    $(".btn-submit").click(function()
    {
        if (!validate())
            return;

        $(".main-form").trigger("submit");
    });

    $('#confirm-pass').on("change", function()
    {
        if ( $(this).val() != $('#new-pass').val())
        {
            onPasswordMismatch();
        }
        else
        {
            onPasswordMatch();
        }
    });
}

function validate()
{
    var validation = 
    {
        'username'     : 'Please enter your username!',
        'seckey'       : 'Please enter your security key!',
        'new-pass'     : 'Please enter your new passowrd!',
        'confirm-pass' : 'Please confirm your new password!'
    };

    var requiredFields = $('input').filter('[required]:visible');

    if (requiredFields)
    {
        for (var field of requiredFields)
        { 
            if (System.isNullOrEmpty($(field).val()))
            {
                var id = $(field).attr("id");
                $(`#${id}`).focus();
                showError(validation[id]);

                return false;
            }
        }
    }

    if ($('#new-pass').val().length < 8)
    {
        onPasswordMismatch("Password length must be atleast 8 characters.");
        $(this).focus();
        return false;
    }

    if ($('#new-pass').val() != $('#confirm-pass').val())
    {
        onPasswordMismatch();
        return false;
    }
    else
    {
        onPasswordMatch();
    }

    return true;
}

function showError(msg) 
{
    $(".error-label").text(msg).fadeIn('fast');
}

function hideError() 
{
    $(".error-label").text('').fadeOut('fast');
}

function onPasswordMismatch(customMessage = "Passwords did not match!")
{
    $(".confirm-passw-check .fas")
        .removeClass("fa-check text-success")
        .addClass("fa-times font-red")
        .parent().fadeIn('fast');
        
    showError(customMessage);
}

function onPasswordMatch()
{
    $(".confirm-passw-check .fas")
        .removeClass("fa-times font-red")
        .addClass("fa-check text-success")
        .parent().fadeIn('fast');

    hideError();
}

function notify()
{
    var msg = $(".err-message").val();

    if (!System.isNullOrEmpty(msg))
    {
        showError(msg);
    }
}