$(document).ready(function () {
    onAwake();
});

function onAwake()
{
    var msg = $(".err-msg").val();

    if (!System.isNullOrEmpty(msg))
        showError(msg);

    onBind();
}

function onBind() 
{  
    $(".btn-login").click(() => 
    {
        var uname = $("#input-username").val();
        var passw = $("#input-password").val();

        if (System.isNullOrEmpty(uname))
        {
            showError("Please enter your username. Alternatively, you can use your email too.");
            return;
        }
        
        if (System.isNullOrEmpty(passw))
        {
            showError("Please enter your password!");
            return;
        }

        $(".main-form").trigger("submit");
    });
}

function showError(msg) 
{  
    $(".auth-msg").text(msg);
    $(".auth-warning").fadeIn();
}