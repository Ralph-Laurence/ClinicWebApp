var checkupForm = undefined;
var fields = [];

var patientDropButton = undefined;
var genderDropButton = undefined; 

var btn_submit = undefined;
var btn_reset = undefined;
var btn_dateTimeNow = undefined;
var btn_clearIllness = undefined;
 
var checkbox_confirm = undefined; 

var dialog = undefined;
var snackbar = undefined;
 
//=============================================
//------------- PRE INITIALIZATION ------------
//=============================================
function onAwake()
{
    checkupForm = $("#checkup-form")[0];

    // cache input field references
 
    fields =
    {
        input_checkupDate: $(".input-checkup-date"),
        input_checkupTime: $(".input-checkup-time"),
        input_formNumber: $(".input-form-number"),
        input_firstName: $(".input-fname"),
        input_middleName: $(".input-mname"),
        input_lastName: $(".input-lname"),
        input_address: $(".input-address"),
        input_contact: $(".input-contact"),
        input_patientType: $(".input-patient-type"),
        input_parentsGuardian: $(".input-parent-guardian"),
        input_bday: $(".input-birthday"),
        input_gender: $(".input-gender"),
        input_age: $(".input-age"),
        input_weight: $(".input-weight"),
        input_systolicBp: $(".input-systolic"),
        input_diastolicBp: $(".input-diastolic"),
        input_illness: $(".input-illness"),
        input_illness_id: $(".input-illness-id")
    };
  
    requestNewFormNumber();

    $(function() 
    { 
        fields.input_bday.datepicker(); 
        fields.input_checkupDate.datepicker();
        fields.input_checkupDate.datepicker('setDate', new Date()); 

        $("#illness-starts-with") 
        .selectmenu
        ({
            change: function (event, ui) 
            {
                // filter illness record by selected leading char
                getIllnessDataSet($(this).val());
            }
        });
    });  

    // load all illness record from table, then
    // - bind illness leading names into combobox
    // - bind illness record to <table>
    getIllnessDataSet();

    patientDropButton = $("#patient-dropdown-button");
    genderDropButton = $("#gender-dropdown-button");
  
    btn_submit = $(".btn-submit");
    btn_reset = $(".btn-reset");
    btn_dateTimeNow = $(".btn-date-time-now");
    btn_clearIllness = $(".btn-clear-illness");

    checkbox_confirm = $("#chk-confirm"); 

    // force numeric fields to accept only numbers

    Input.forceNumeric(System.getClass(fields.input_age));
    Input.forceNumeric(System.getClass(fields.input_systolicBp));
    Input.forceNumeric(System.getClass(fields.input_diastolicBp));
    Input.forceNumeric(System.getClass(fields.input_contact));
    Input.forceDecimals(System.getClass(fields.input_weight));

    dialog = new AlertDialog();
    snackbar = new SnackBar();

    // bind events 
    onBind();
}
//=============================================
//-------------- EVENT BINDINGS ---------------
//=============================================
function onBind()
{
    // enable submit button when the certify checkbox is checked
    checkbox_confirm.on("change", function() 
    {
        var checked = $(this).prop('checked');
        
        if (checked) {
            btn_submit.prop('disabled', false);
        } else {
            btn_submit.prop('disabled', true);
        }
    }); 

    // get current date and time then bind it onto date time input fields
    btn_dateTimeNow.click(function()
    {
        fields.input_checkupDate.datepicker('setDate', new Date());
        fields.input_checkupTime.val(moment().format("HH:mm"));
    });

    // apply validations to submit button before sending data to server.
    // disable the submit button on success to prevent resubmit
    btn_submit.click(function()
    {
        var allFieldsValid = validateRequiredFields();
        
        if (allFieldsValid)
        {
            btn_submit.prop('disabled', true);
            sendDataToServer();
        }
        else 
        {
            dialog.warn("Please fill out all fields!\n\nFor fields that do not apply to you, please enter \"N/A\" (or '0' for number field) instead.");
        } 
    });

    btn_reset.click(() => resetForm());

    btn_clearIllness.click(function()
    {
        fields.input_illness.val("");
        fields.input_illness_id.val("");
    });

    // focus on birthday picker after binding value
    fields.input_bday.on("change", () => fields.input_bday.focus().blur());
    fields.input_checkupDate.on("change", () => fields.input_checkupDate.focus().blur());
}
//=============================================
//-------------- BUSINESS LOGIC ---------------
//=============================================
function validateRequiredFields()
{ 
    for (var field in fields)
    { 
        var val = fields[field].val();

        if (val == undefined || val == null || val == "")
        { 
            return false;
        }
    }
 
    return true; 
}

function sendDataToServer()
{
    var obj = {};

    for (var f in fields)
    { 
        obj[f] = fields[f].val(); 
    }  

    $.ajax(
    {
        type: "POST",
        url: "ajax.save-checkup-info.php",
        data: { jsonData: JSON.stringify(obj) },
        dataType: "json",
        success: function (s) 
        {
            if (s) 
            {
                resetForm();  
                snackbar.show(s.message);  
                requestNewFormNumber();
            }
        },
        error: function (jqXHR, exception)
        {
            dialog.danger("Process failed because of an error. Reload the page and try again.\n\nIf this error persists, please contact the Administrator.");
            btn_submit.prop('disabled', false);
        }
    });
}

function setPatientType(value)
{
    fields.input_patientType.val(value); 
    
    switch (value)
    {
        case 1: 
            patientDropButton.text("Student");
            break;
        case 2: 
            patientDropButton.text("Faculty");
            break;
        case 3: 
            patientDropButton.text("Staff");
            break;
    }
}

function setGender(value)
{
    fields.input_gender.val(value); 
    genderDropButton.text(value);
}
 
function appendIllnessSelectOptions(bindingSource = undefined)
{
    var selected = $("#illness-starts-with").val();
    
    $("#illness-starts-with")
    .empty()
    .append(`<option selected value='all'>Show All</option>`)
    .append(`<option disabled value=''>Begins With :</option>`);

    if (bindingSource != undefined)
    {
        bindingSource.forEach(item => 
        {
            var attr = "";

            if (item == selected)
                attr = "selected";

            $("#illness-starts-with")
            .append(`<option ${attr} value='${item}'>${item}</option>`);
        });
    }

    $("#illness-starts-with").selectmenu("refresh"); 
}

// load the records found in Illness table
function getIllnessDataSet(filter = 'all')
{  
    $.ajax(
    {
        url: "ajax.get-illness.php",
        type: 'POST',
        dataType: 'json',
        data: {
            filter: filter
        },
        success: function(res)
        {   
            if (res)
            { 
                if (!res.leadingChars || !res.dataSet)
                    return;
                
                appendIllnessSelectOptions(res.leadingChars); 
                
                $(".illness-selector-dataset").empty()

                res.dataSet.forEach(i => 
                {
                    $(".illness-selector-dataset") 
                    .append(`<tr class="align-middle">
                                 <td>${i.name}</td>
                                 <td>
                                    <button class="btn btn-primary" onclick="selectIllness(${i.id}, '${i.name}')" data-mdb-dismiss="modal">
                                    Select
                                    </button>
                                 </td>
                             </tr>`);
                });
                
            }
        },
        error: function(jqXHR, exception)
        {
            dialog.danger("Failed to retrieve illness records because of an error.");
        }
    });
}

function selectIllness(id, name)
{ 
    $(".input-illness").val(name);
    $(".input-illness-id").val(id);
}

function requestNewFormNumber()
{
    $.ajax(
    {
        type: "POST",
        url: "ajax.generate-form-number.php",
        success: function (i) 
        {
            if (i) {
                fields.input_formNumber.val(i);
            }
        },
        error: function (jqXHR, exception)
        {
            dialog.warn("Failed to request a new form number. Please reload the page.");
        }
    });
}

function resetForm()
{  
    checkupForm.reset();
    genderDropButton.text("Select Gender");
    fields.input_gender.val('');

    patientDropButton.text("Patient Type");
    fields.input_patientType.val(''); 
}

// only execute this entire script after the page has fully loaded
$(document).ready(() => onAwake());
 