var btnSave     = undefined;
var errorLabel  = undefined;
var errorBox    = undefined;

var confirm     = undefined;
var toast       = undefined;
var snackbar    = undefined;
var dialog      = undefined;

var carousel    = undefined;

var currentFrame = 0;
var totalFrames = 0; 

let validation = 
[
    {   el: $("#fname"),            msg: "Please enter a valid firstname!", frame: 0 },
    {   el: $("#lname"),            msg: "Please enter a valid lastname!" , frame: 0 },
    {   el: $("#email"),            msg: "Please add a valid email!"      , frame: 0 },
    {   el: $(".user-type"),        msg: "Please select a user type!"     , frame: 1 },
    {   el: $("#username"),         msg: "Please add a valid username!"   , frame: 1 },
];

$(document).ready(function () {
    onAwake();
});

function onAwake()
{
    snackbar     = new SnackBar();
    dialog       = new AlertDialog();
    confirm      = new ConfirmDialog();
    toast        = new Toast();
    carousel     = new mdb.Carousel($("#carousel-main"));

    currentFrame = carousel._triggerSlideEvent().from;
    totalFrames = carousel._items.length;

    btnSave     = $(".btn-save");
    errorBox    = $(".error-message");
    errorLabel  = $(".error-label");

    $(function() 
    {
        $(".user-type").selectmenu({width: 200, 
            create: function(event, ui)
            { 
                onSelectMenuUI($(".user-type option:selected").text());
            },
            change: function(event, ui)
            { 
                onSelectMenuUI(ui.item.label.trim());
                hideError();
            } 
        });
    });

    tickPermissionOnLoad();

    notify();
    onBind();
}

function onBind()
{
    Input.forceAlphaNums("alphanum", false);
    Input.forceAlpha("alpha");

    $(".btn-save").click(() => handleSubmit());
 
    $(".btn-cancel").click(() => 
    {
        confirm.actionOnOK = function () {
            redirectTo($(".on-cancel").val());
        };

        confirm.show("Your changes won't be saved when you leave. Do you wish to abort the operation?",
            "Cancel Operation", "Yes", "No");
    });
    //
    // Carosuel Scroller Buttons
    //
    $(".btn-user-bio").click(() => carousel.to(0) );
    $(".btn-account").click( () => carousel.to(1) );
    $(".btn-security").click(() => carousel.to(2) );

    $(".btn-next").click(() => cycleNext() );
    $(".btn-back").click(() => cyclePrev() );
    //
    // The main carousel
    //
    // SLIDE => SLIDE EVENT -> CAROUSEL IS CURRENTLY SLIDING
    //
    $("#carousel-main").on('slide.mdb.carousel', (e) => 
    { 
        // Only show the Submit buttons when the 3rd Frame (index #2 ) has been reached
        if (e.to == 2) 
        {
            $(".notes").fadeOut('fast');
            $(".perm-table-wrapper, .btn-save").fadeIn();
            $(".btn-next").hide();
        }
        else
        {
            $(".notes, .btn-next").fadeIn();
            $(".perm-table-wrapper, .btn-save").hide();
        } 

        if (e.to == 0)
            $(".btn-back").hide();
        else 
            $(".btn-back").show();
    })
    //
    // SLID => SLID EVENT -> AFTER CAROUSEL HAS COMPLETED SLIDING
    //
    .on("slid.mdb.carousel", (e) =>
    {
        currentFrame = e.to;
        var sliderButtons = $(".carousel-buttons button");
        
        sliderButtons.removeClass("active");
        $(sliderButtons[e.to]).addClass("active");
    });
    //
    // Permission flag checkboxes
    //
    $(document).on("click", ".dataset-body .flag-read", function (e) 
    {  
        flagRead( $(this), $(this).closest("tr") );
    })
    .on("click", ".dataset-body .flag-write", function (e) 
    {  
        flagWrite( $(this), $(this).closest("tr") );
    }).
    on("click", ".dataset-body .flag-deny", function (e) 
    {  
        flagDeny( $(this), $(this).closest("tr") );

        if ($(".user-type option:selected").text() == "Super Admin")
        toast.show("This action won't take effect. Super Admin accounts " +
        "always have full access to features.", "Deny Access", 
        toast.toastTypes.DANGER, true, 5500);
    })
    .on("click", ".avatar-dataset .btn-select-avatar", function(e)
    {
        var tr = $(this).closest("tr");
        var avatar = tr.find(".td-avatar").val().trim();
        var img = tr.find(".avatar-img").attr("src");

        $(".avatar-data").val(avatar);
        $(".avatar-preview").attr("src", img);
    }); 
    //
    // Hide the error message when fields with class "apply-validation" are interacted
    //
    $(".apply-validation").on("input", () => hideError());
}
//==================================================//
//---------------CAROUSEL OPERATIONS ---------------//
//==================================================//
function cycleNext()
{
    if (currentFrame < totalFrames)
        carousel.next();
}

function cyclePrev()
{
    if (currentFrame > 0)
        carousel.prev();
}
//==================================================//
//------------- PERMISSION OPERATIONS --------------//
//==================================================//
function flagRead(sender, tr) 
{  
    // Uncheck all other checkboxes except the Read flag
    tr.find(".perm-flag:not(.flag-read)").prop("checked", false);

    // Check for self's checkbox state then
    // Update chmod values
    tr.find(".chmod").val("1,0,0");

    showFlagLabel(tr, sender, 0);
}

function flagWrite(sender, tr) 
{  
    // Uncheck the Deny checkbox
    tr.find(".flag-deny").prop("checked", false);

    tr.find(".flag-read").prop("checked", true);
    tr.find(".chmod").val("1,1,0"); 

    showFlagLabel(tr, sender, 1);
}

function flagDeny(sender, tr) 
{  
    // Uncheck all other checkboxes except the Deny flag
    tr.find(".perm-flag:not(.flag-deny)").prop("checked", false);

    // Check for self's checkbox state then Update chmod values
    tr.find(".chmod").val("0,0,0");

    showFlagLabel(tr, sender, -1);
}

function showFlagLabel(tr, sender, flag) 
{   
    switch(flag)
    {
        case -1: 
            tr.find(".flag-label").attr("id", "flag-desc-red").text('No Access');
            break;
        case 0: 
            tr.find(".flag-label").attr("id", "flag-desc-amber").text('View-Only');
            break;
        case 1: 
            tr.find(".flag-label").attr("id", "flag-desc-green").text('Full Control');
            break;
    } 

    if (sender.prop("checked"))
        tr.find(".flag-label").show();
    else 
        tr.find(".flag-label").hide();
}

function collectPermissions() 
{  
    // Collect all permission CHMOD data and store that into a JSON object
    var row = $(".dataset-table .dataset-body tr");
    
    var permData = {};

    $(row).each((index, tr) => 
    {
        var chmod = $(tr).find('.chmod').val().trim();
        var featureTag = $(tr).attr("data-feature-tag");
        var permName = $(tr).find(".perm-name").text().trim();

        if (empty(chmod))
        {
            dialog.danger(`Please set a permission for ${permName}!`);
            return;
        }
        
        permData[featureTag] = chmod;
        //console.log(`${permName}: ${featureTag} => ${chmod}`);
    });

    if (System.isObjectEmpty(permData))
    {
        dialog.danger("This action can't be completed. Please reload the page and try again.");
        return;
    }

    $(".chmod-json").val(JSON.stringify(permData));
}

function onSelectMenuUI(label) 
{    
    // Show permission note based on selected role
    switch (label) 
    {
        case "Super Admin": 
            $(".perm-note-content").text("Super Admin accounts always have full access to features. To restrict a user from a specific feature, change the user type.");
            break;

        case "Admin": 
            $(".perm-note-content").text("Admin accounts may still have some restrictions even on full permission.");
            break;

        case "Staff": 
            $(".perm-note-content").text("Staff accounts are only limited to Medical Records.");
            break;

    }

    if (!System.isNullOrEmpty($(".perm-note-content").text()))
        $(".perm-notes").show();

    // Staff accounts will only have Medical Record perms enabled
    if (label == "Staff") 
    {
        var row = $("tr:not([data-feature-tag=feature-med-rec])");
        row.find(".flag-label").attr("id", "flag-desc-unavailable").text("Unavailable");
        row.find(".perm-flag").prop("disabled", true);
    }
    else 
    { 
        $(".perm-flag").prop("disabled", false);
        tickPermissionOnLoad();
    }
}
//==================================================//
//---------------- FORM VALIDATIONS ----------------//
//==================================================//
function handleSubmit() 
{   
    for(let obj of validation)
    {   
        if (System.isNullOrEmpty(obj.el.val())) 
        {
            showError(obj.msg); 
            carousel.to(obj.frame);
            obj.el.focus();
            return;
        }
    }

    // These fields must be of 4chars min length
    if (!checkLength($("#username"), 'Username', 4, 1))
        return;

    collectPermissions();
    
    $("#register-form").trigger("submit");
    $(".btn-save").prop('disabled', true);
}

function showError(message)
{
    errorLabel.text(message);
    errorBox.fadeIn('fast');
}

function hideError()
{
    errorLabel.text('');
    errorBox.hide();
}

function checkLength(el, elName, len, frame) 
{ 
    if ($(el).val().length < len)
    {
        showError(`${elName} is too short! ${elName} requires atleast ${len} characters.`);
        carousel.to(frame);
        $(el).focus();
        return false;
    }

    return true;
}

function empty(data) {  
    return System.isNullOrEmpty(data);
}

function notify() 
{  
    var success = $(".success-msg").val();

    if (!System.isNullOrEmpty(success))
        snackbar.show(success);

    var err = $(".error-msg").val();

    if (!System.isNullOrEmpty(err))
        showError(err);
}

function tickPermissionOnLoad()
{
    var row = $(".dataset-table .dataset-body tr");
     
    $(row).each((index, tr) => 
    {
        var chmod = $(tr).find('.chmod').val().trim(); 

        if (empty(chmod)) 
            return; 
        // perm write = 1,1,0
        // perm read  = 1,0,0
        // perm deny = 0,0,0
        switch (chmod.trim().replaceAll(",", ""))
        {
            case "110":
                $(tr).find('.flag-read').prop('checked', true);
                $(tr).find('.flag-write').prop('checked', true);
                $(tr).find('.flag-deny').prop('checked', false);
                $(tr).find(".flag-label").attr("id", "flag-desc-green").text('Full Control');
                break;
            case "100":
                $(tr).find('.flag-read').prop('checked', true);
                $(tr).find('.flag-write').prop('checked', false);
                $(tr).find('.flag-deny').prop('checked', false);
                $(tr).find(".flag-label").attr("id", "flag-desc-amber").text('View-Only');
                break;
            case "000":
                $(tr).find('.flag-read').prop('checked', false);
                $(tr).find('.flag-write').prop('checked', false);
                $(tr).find('.flag-deny').prop('checked', true);
                $(tr).find(".flag-label").attr("id", "flag-desc-red").text('No Access');
                break;
        }
    });
} 