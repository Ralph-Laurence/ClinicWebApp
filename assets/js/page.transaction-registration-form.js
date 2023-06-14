var btn_submit = undefined;
var btn_reset = undefined;

var errorMessage = undefined;
var errorLabel = undefined;
var input_birthday = undefined;

var confirm = undefined;
var dialog = undefined;
var toast = undefined; 

$(document).ready(function(){
    onAwake();
}); 

function onAwake()
{ 
    dialog = new AlertDialog();
    confirm = new ConfirmDialog();
    toast = new Toast();

    btn_submit = $(".btn-submit");
    btn_reset = $(".btn-reset");

    errorMessage = $(".error-message");
    errorLabel = $(".error-label");

    input_birthday = $(".input-birthday").datepicker({
        changeMonth: true,
        changeYear: true,
        yearRange: '1940:c'
    });  

    $(function () 
    {
        $("#select-patient-type").selectmenu({ width: 120 }); 

        $("#select-gender").selectmenu({ width: 120 }); 
    }); 

    notify();

    onBind();
}
 
function onBind()
{
    // Force Alphanumeric fields to accept only Letters, Numerics and dashes
    $(".alphanum").on("input", function(e)
    {
        var newValue = this.value.replace(/[^A-Za-z0-9\-]/gi, "");

        if (this.value != newValue)
            this.value = newValue;
    });

    // Address fields have limited special chars allowed
    $(".input-address").on("input", function(e)
    {
        var newValue = this.value.replace(/[^A-Za-z0-9\s\-\.\,]/gi, "");

        if (this.value != newValue)
            this.value = newValue;
    });

    // Force Alpha fields to accept only letters and dashes
    $(".alpha").on("input", function(e)
    {
        var newValue = this.value.replace(/[^A-Za-z\s\.\-]/gi, "");

        if (this.value != newValue)
            this.value = newValue;
    });

    $(".alpha, .alphanum, .input-address").on("blur", function(e)
    {
        this.value = this.value.trim();
    });

    // Allow letters, dots, dashes and spaces on parent input
    $(".input-parent-guardian").on("input", function(e)
    {
        var newValue = this.value.replace(/[^A-Za-z\-\s\.]/gi, "");

        if (this.value != newValue)
            this.value = newValue;
    });

    // Whole number fields
    Input.forceNumeric("numerics");

    // Decimal fields
    Input.forceDecimals("decimals"); 

    // Focus on the birthday field to prevent overlap
    $(".input-birthday").on("change", function() 
    {
        // calculate age on birthday select
        if ($(this).val())
        {
            // format the birthday from m-d-Y to Y-m-d
            var bday = mdy_to_ymd( $(this).val() );

            // calculate age then set the Age field to this value
            var age = calculateAge(bday);

            if (age < 0) age = 0;

            $(".input-age").val( age ).focus().blur();
        }

        $(this).focus().blur();
    });

    btn_submit.click(function()
    { 
        var validation = validateRequiredFields();

        if (validation == null)
        {
            $("#register-form").trigger("submit");
            return;
        }

        errorLabel.text(validation.errorMessage);
        errorMessage.show();
        $(validation.element).focus();
    });

    // Reset the form and remove prescriptions
    btn_reset.click(() => 
    {
        confirm.actionOnOK = function () 
        {
            $("#register-form").trigger("reset");
            toast.show("Form has been cleared.", "Reset Form", toast.toastTypes.SUCCESS);

            errorLabel.text('');
            errorMessage.hide();
        }; 

        confirm.show("Reset registration details?", "Reset Data", "OK", "Cancel");
    });

    //
    // During edit mode, track for the cancel button click
    //
    $(".btn-cancel").click(function() 
    {
        confirm.actionOnOK = function () 
        {
            var route = $(".cancel-route").val();
            navHref(route);
        };
        
        var msg = "Your changes won't be saved if you leave. Do you wish to cancel the operation?";
        
        confirm.show(msg, "Edit Patient", "Yes", "No");
    });


}

function validateRequiredFields()
{ 
    // ID number
    if (System.isNullOrEmpty($(".input-idnum").val()))
    {  
        return {
            errorMessage: "Please provide a valid ID number!",
            element: ".input-idnum"
        };
    }

    // Patient Type
    if ($("#select-patient-type").val() == null)
    { 
        return {
            errorMessage: "Please select a Patient Type!",
            element: "#select-patient-type"
        };
    }

    // Firstname
    if (System.isNullOrEmpty($(".input-fname").val()))
    {  
        return {
            errorMessage: "Please provide a valid Firstname!",
            element: ".input-fname"
        };
    } 

    // Middlename
    if (System.isNullOrEmpty($(".input-mname").val()))
    {  
        return {
            errorMessage: "Please provide a valid Middlename!",
            element: ".input-mname"
        };
    } 

    // Lastname
    if (System.isNullOrEmpty($(".input-lname").val()))
    {  
        return {
            errorMessage: "Please provide a valid Lastname!",
            element: ".input-lname"
        };
    } 

    // Birthday
    if (System.isNullOrEmpty(input_birthday.val()))
    {  
        return {
            errorMessage: "Please select a Birthdate!",
            element: ".input-birthday"
        };
    } 

    // Age
    if (System.isNullOrEmpty($(".input-age").val()))
    { 
        return {
            errorMessage: "Please provide a valid Age!",
            element: ".input-age"
        };
    } 

    // Gender
    if ($("#select-gender").val() == null) 
    {
        return {
            errorMessage: "Please select a Gender!",
            element: "#select-gender"
        };
    }

    return null; 
}

function mdy_to_ymd(dateString) 
{
    let dateParts = dateString.split("/");
    let formattedDate = `${dateParts[2]}-${dateParts[0]}-${dateParts[1]}`;
    return formattedDate;
}

function calculateAge(date) 
{
    let today = new Date();
    let birthDate = new Date(date);
    let age = today.getFullYear() - birthDate.getFullYear();
    let month = today.getMonth() - birthDate.getMonth();
    if (month < 0 || (month === 0 && today.getDate() < birthDate.getDate())) {
        age--;
    }
    return age;
}

//==================================================//
//:::::::::::::: SECTION: EDIT PATIENT ::::::::::::://
//==================================================//
  
function notify() 
{  
    var msg = $('.err-msg').val();

    if (!System.isNullOrEmpty(msg))
    {
        showError(msg);  
    } 
}

function showError(msg) 
{  
    $(".error-label").text(msg);
    $(".error-message").fadeIn();
}

function hideError() 
{  
    $(".error-message").hide();
    $(".error-label").text('');
}