var dialog = undefined;
var snackbar = undefined;
var toast = undefined;

$(document).ready(function () 
{
    onAwake();
});

function onAwake()
{
    dialog = new AlertDialog();
    snackbar = new SnackBar();
    toast = new Toast();

    notify();
    onBind();

    onProfileEditComplete();
}

function onBind() 
{  
    $(".btn-ok").click(function () 
    {   
        $("#see-guid-password").val( $("#input-password").val() );

        if (System.isNullOrEmpty( $("#see-guid-password").val() ))
        {
            $(".error-box").fadeIn();
            $("#input-password").focus();
            return;
        }

        $(".frm-see-guid").trigger("submit");
    });

    $("#editProfileModal .btn-ok").click(function () 
    {  
        $("#edit-password").val( $("#editProfileModal #input-password").val() );

        if (System.isNullOrEmpty( $("#edit-password").val() ))
        {
            $("#editProfileModal .error-box").fadeIn();
            $("#editProfileModal #input-password").focus();
            return;
        }

        $(".frm-edit-profile").trigger("submit");
    });

    $(".btn-cancel").click(function () 
    {   
        $(".error-box").hide();
        $("#input-password").val('').focus().blur();
        $(".frm-see-guid").trigger("reset");
    });

    $(".btn-copy-sec-key").click(function()
    {
        var el = $("#sec-key-field");
        copyToClipboard(el);

        toast.show("Copied to clipboard!", "Security Key");
    });

    $(".btn-change-password").click(function()
    {
        $(this).hide();
        $(".password-form-wrapper").fadeIn();
        $(".password-header").text("Change Password");

        var container = $('.main-workarea .simplebar-content-wrapper')[0]; 
        container.scrollTo({ top: 1000, behavior: "smooth" }); 
    });

    $(".frm-change-pass .btn-cancel").click(function()
    {
        $(".frm-change-pass").trigger("reset");
        $(".password-form-wrapper").fadeOut("fast");
        $(".btn-change-password").fadeIn("fast");
        $(".password-header").text("Password");

        $(".password-match-icon").removeClass("fa-times font-red fa-check text-success");
        $(".frm-change-pass .error-msg").text('').hide();
    });

    $(".frm-change-pass .btn-save").click(function()
    {
        if (!validateChangePassword())
            return;

        $(".frm-change-pass").trigger("submit");
    });

    $("#input-old-password, #input-new-password").on("input", function()
    {
        $(".frm-change-pass .error-msg").text('').fadeOut('fast');
    });

    $(".frm-change-pass #input-confirm-password").on("input", function()
    {
        if (!System.isNullOrEmpty($("#input-new-password").val()) && $(this).val() != $("#input-new-password").val())
        {
            $(".frm-change-pass .error-msg").text("Password didn't matched!").fadeIn('fast');
            onPasswordInvalid();
        }
        else
        {
            $(".frm-change-pass .error-msg").text('').fadeOut('fast');
            $(".password-match-icon").removeClass("fa-times font-red").addClass("fa-check text-success");
        }
    });

    // Passwords should not have spaces
    $(".password-fields").on('keypress', function(e) {
        if (e.which == 32) {
            e.preventDefault();
        }
    });
}

function validateChangePassword()
{
    // Validate passwords if empty
    var validation = 
    [
        { el: $("#input-old-password"),     msg: "Please enter your old password." },
        { el: $("#input-new-password"),     msg: "Please enter your new password" },
        { el: $("#input-confirm-password"), msg: "Please confirm your new password" }
    ];
     
    for (var v of validation)
    {
        if (System.isNullOrEmpty(v.el.val()))
        {
            $(".frm-change-pass .error-msg").text(v.msg).fadeIn('fast');
            $(".frm-change-pass").find(v.el).focus();
            
            return false;
        }
    }

    // Check if new password is similar to old
    if ($("#input-old-password").val() == $("#input-new-password").val())
    {
        $(".frm-change-pass .error-msg").text("You cannot reuse and old password.").fadeIn('fast');
        validation[1].el.focus();
        onPasswordInvalid();
        return false;
    }

    // Confirm the new password
    if ($("#input-new-password").val() != $("#input-confirm-password").val())
    {
        $(".frm-change-pass .error-msg").text("Password mismatched! Please retype your new password.").fadeIn('fast');
        onPasswordInvalid();
        validation[2].el.focus();
        return false;
    }

    return true;
}

function onPasswordInvalid()
{
    $(".password-match-icon").removeClass("fa-check text-success").addClass("fa-times font-red");
}

function notify() 
{  
    var err = $(".action-error").val();

    if (!System.isNullOrEmpty(err))
    {
        dialog.danger(err);
        return;
    }

    var success = $(".action-success").val();

    if (!System.isNullOrEmpty(success))
    {
        snackbar.show(success);
    }
}

function copyToClipboard(element) 
{
    var $temp = $("<input>");
    $("body").append($temp);
    $temp.val($(element).val()).select();
    document.execCommand("copy");
    $temp.remove();
}

function onProfileEditComplete()
{
    var successKey = $(".edit-success-key").val();

    if (System.isNullOrEmpty(successKey))
        return;

    var re_loginModal = new mdb.Modal($("#re_loginModal"),
    {
        'backdrop': 'static'
    });

    re_loginModal.show();
}