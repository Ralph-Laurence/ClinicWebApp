var fields = [];

var btn_submit = undefined;
var btn_dateTimeNow = undefined;
 
var checkbox_confirm = undefined;
 
//=============================================
//------------- PRE INITIALIZATION ------------
//=============================================
function onAwake()
{
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
        input_fathersName: $(".input-fathers-name"),
        input_mothersName: $(".input-mothers-name"),
        input_bday: $(".input-bday"),
        input_gender: $(".input-gender"),
        input_age: $(".input-age"),
        input_weight: $(".input-weight"),
        input_systolicBp: $(".input-systolic"),
        input_diastolicBp: $(".input-diastolic"),
        input_illness: $(".input-illness"),
        input_illness_id: $(".input-illness-id")
    };
 
    btn_submit = $(".btn-submit");
    btn_dateTimeNow = $(".btn-date-time-now");

    checkbox_confirm = $("#chk-confirm");

    // force numeric fields to accept only numbers

    Input.forceNumeric(System.getClass(fields.input_age));
    Input.forceNumeric(System.getClass(fields.input_systolicBp));
    Input.forceNumeric(System.getClass(fields.input_diastolicBp));
    Input.forceNumeric(System.getClass(fields.input_contact));
    Input.forceDecimals(System.getClass(fields.input_weight));


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
        btn_submit.prop('disabled', (!checked));
    }); 

    // get current date and time then bind it onto date time input fields
    btn_dateTimeNow.click(() => 
    {
        fields.input_checkupDate.val(moment().format("YYYY-MM-DD"));
        fields.input_checkupTime.val(moment().format("HH:mm"));
    });

    // apply validations to submit button before sending data to server.
    // disable the submit button on success to prevent resubmit
    btn_submit.click(() => 
    {
        var allFieldsValid = validateRequiredFields();
        
        //alert(allFieldsValid);

        if (allFieldsValid)
            sendDataToServer();

        // $(".checkup-form").reset();
    });
}
//=============================================
//-------------- BUSINESS LOGIC ---------------
//=============================================
function validateRequiredFields()
{ 
    for (var field in fields)
    {
        //console.log("key= " + field + " => val: " + fields[field].val());
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
        //data.push(obj);

        //console.log(obj);
    } 
 
    // var data = [];

    // for (var f in fields)
    // {
    //     var obj = {};
    //     obj[f] = fields[f].val();
    //     data.push(obj);

    //     console.log(obj);
    // } 

    $.ajax(
    {
        type: "POST",
        url: "ajax.save-checkup-info.php",
        data: { jsonData: JSON.stringify(obj) },
        // data: 
        // {
        //     checkupDate: fields.input_checkupDate.val(),
        //     checkupTime: fields.input_checkupTime.val(),
        //     formNumber: fields.input_formNumber.val(),
        //     firstname: fields.input_firstName.val(),
        //     middlename: fields.input_middleName.val(),
        //     lastname: fields.input_lastName.val(),
        //     address: fields.input_address.val(),
        //     contact: fields.input_contact.val(),
        //     fathersName: fields.input_fathersName.val(),
        //     mothersName: fields.input_mothersName.val(),
        //     bday: fields.input_bday.val(),
        //     gender: fields.input_gender.val(),
        //     age: fields.input_age.val(),
        //     weight: fields.input_weight.val(),
        //     systolicBp: fields.input_systolicBp.val(),
        //     diastolicBp: fields.input_diastolicBp.val(),
        //     illness: fields.input_illness_id.val()
        // },
        dataType: "json",
        success: function (s) 
        {
            if (s) 
            {
                //alert(s.statusCode);
                alert(s.message);
                // $.each(s, function(k, v)
                // {
                //     alert(k + " => " + v);
                // });
            }
        },
        error: function (jqXHR, exception)
        {
            alert("err: " + jqXHR.responseText);
        }
    });
}

// only execute this entire script after the page has fully loaded
$(document).ready(() => onAwake());
 