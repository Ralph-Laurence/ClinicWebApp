$(document).ready(function () {
    onAwake();
});

function onAwake()
{
    onBind();
    notify();

    if ($(".avatar-preview-src").val())
    {
        $(".avatar-preview").attr("src", $(".avatar-preview-src").val());
    }
}

function onBind()
{
    $(".avatar-dataset").on("click", ".btn-select-avatar", function()
    {
        var tr = $(this).closest("tr");
        var avatar = $(tr).find(".td-avatar").val();
        var avatarSrc = $(tr).find(".avatar-img").attr("src");

        $(".avatar-preview").attr("src", avatarSrc);
        $(".avatar").val(avatar);
    });

    $(".btn-save").click(function()
    { 
        if (!validate())
            return;

        $(".frm-edit-profile").trigger("submit");
    });
}

function validate()
{
    var validation = 
    [
        { el: $("#password"),   msg: "Please enter your old password to make changes to your profile." },
        { el: $("#firstname"),  msg: "Please enter your firstname" },
        { el: $("#middlename"), msg: "Please enter your middlename." },
        { el: $("#lastname"),   msg: "Please enter your lastname." },
        { el: $("#username"),   msg: "Please enter your usernname." },
        { el: $("#email"),      msg: "Please enter your email." },
    ];
     
    for (var v of validation)
    {
        if (System.isNullOrEmpty(v.el.val()))
        { 
            showError(v.msg);
            v.el.focus();
            return false;
        }
    }

    return true;
}

function notify()
{
    var err = $(".action-error").val();

    if (!System.isNullOrEmpty(err))
    {
        showError(err);
        
        var container = $('.main-workarea .simplebar-content-wrapper')[0]; 
        container.scrollTo({ top: 1000, behavior: "smooth" }); 

        return;
    }
}

function showError(msg)
{
    $(".error-msg").text(msg).fadeIn('fast');
}