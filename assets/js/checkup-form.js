var input_checkupDate = undefined;
var input_checkupTime = undefined;

var input_firstName = undefined;
var input_middleName = undefined;
var input_lastName = undefined;
var input_address = undefined;
var input_contact = undefined;
var input_fathersName = undefined;
var input_mothersName = undefined;

var input_bday = undefined;
var input_gender = undefined;
var input_age = undefined;
var input_weight = undefined;
var input_systolicBp = undefined;
var input_diastolicBp = undefined;

var input_illness = undefined;
var input_illness_id = undefined;

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
 
    var fields =
    {
        input_checkupDate: $(".input-checkup-date"),
        input_checkupTime: $(".input-checkup-time"),
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

}

function sendDataToServer()
{

}

// only execute this entire script after the page has fully loaded
$(document).ready(() => onAwake());

/*
$(document).ready(() => 
        {
            $.ajax(
            {
                type: "POST",
                url: "ajax.save-checkup-info.php",
                data: 
                {
                    // msg: "Here it comes!"
                },
                dataType: "json",
                success: function(s) 
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
                error: function(e) 
                {
                    alert("err: " + e);
                }
            });
        });
*/