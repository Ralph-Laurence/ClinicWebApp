var checkupForm = undefined;
var fields = [];

var patientDropButton = undefined;
var genderDropButton = undefined; 

var btn_submit = undefined;
var btn_reset = undefined;
var btn_dateTimeNow = undefined;
var btn_clearIllness = undefined;
var btn_clearPrescriptions = undefined;
 
var checkbox_confirm = undefined; 

var dialog = undefined;
var snackbar = undefined;

var medicinePickerCarousel = undefined;
var medicinePickerCarousel_BackBtn = undefined;
var medicinePickerCarousel_NextBtn = undefined;
var medicinePickerCarousel_OkBtn = undefined;
 
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
        fields.input_bday.datepicker({
            changeMonth: true,
            changeYear: true
        }); 
        fields.input_checkupDate.datepicker();
        fields.input_checkupDate.datepicker('setDate', new Date()); 

        $("#illness-starts-with") 
        .selectmenu
        ({
            change: function (event, ui) 
            {
                // filter illness record by selected leading char
                //getIllnessDataSet($(this).val());
            }
        });

        $("#filter-medicine-category") 
        .selectmenu
        ({
            change: function (event, ui) 
            {
                // filter illness record by selected categories
                filterMedicine($(this).val());
            }
        });
    });  
  
    medicinePickerCarousel = $("#medicinePickerCarousel");
    medicinePickerCarousel_BackBtn = $(".btn-carsl-back");
    medicinePickerCarousel_NextBtn = $(".btn-carsl-next");
    medicinePickerCarousel_OkBtn = $(".btn-carsl-ok");

    patientDropButton = $("#patient-dropdown-button");
    genderDropButton = $("#gender-dropdown-button");
  
    btn_submit = $(".btn-submit");
    btn_reset = $(".btn-reset");
    btn_dateTimeNow = $(".btn-date-time-now");
    btn_clearIllness = $(".btn-clear-illness");
    btn_clearPrescriptions = $(".btn-clear-prescriptions");

    checkbox_confirm = $("#chk-confirm"); 

    // force numeric fields to accept only numbers

    Input.forceNumeric(System.getClass(fields.input_age));
    Input.forceNumeric(System.getClass(fields.input_systolicBp));
    Input.forceNumeric(System.getClass(fields.input_diastolicBp));
    Input.forceNumeric(System.getClass(fields.input_contact));
    Input.forceDecimals(System.getClass(fields.input_weight));
 
    Input.forceNumericOnAppend("input-medicine-amount");
 
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

    // control the select button on medicine selector modal
    bindSelectMedicine();

    // control the plus and minus buttons on medicine selector modal
    bindPlusMinusOnSelectMedicine();
    
    // fire events after carousel has finished sliding
    medicinePickerCarousel.on("slid.mdb.carousel", () => 
    {
        // reference to each carousel pages
        var carousel_page1 = $(".carousel-page-1");
        var carousel_page2 = $(".carousel-page-2");

        // track each carousel page's visibility by checking if class 'active' is present
        // if page 2 is active, enable the back button and hide the next button. 
        // then show the OK button
        if (carousel_page2.hasClass("active"))
        {
            medicinePickerCarousel_NextBtn.hide();
            medicinePickerCarousel_BackBtn.show();
            medicinePickerCarousel_OkBtn.show();
        }
        // if page 1 is active, enable the next button and hide the back button. 
        // also hide the OK button.
        if (carousel_page1.hasClass("active"))
        {
            medicinePickerCarousel_BackBtn.hide();
            medicinePickerCarousel_NextBtn.show();
            medicinePickerCarousel_OkBtn.hide();
        }
    });

    // grab a copy of medicine picker modal's table
    $(".medicines-table").data('medicines-table-old-state', $(".medicines-table").html());

    btn_clearPrescriptions.click(() => 
    {
        $(".medicines-table").html($(".medicines-table").data('medicines-table-old-state'));
    });

    // force medicine selector input fields to set value as 1 when 
    // the user entered 0 or empty
    $(document).on("blur", ".input-medicine-amount", function()
    {
        if ($(this).val() == '' || $(this).val() < 1)
            $(this).val(1);
    });
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

//=============================================
//--------- MEDICINE PICKER CAROUSEL ----------
//=============================================

function unlockNextButton()
{
    var table = $(".selected-medicines-table");
    var rowLength = table.find("tbody tr").length;

    var noneSelected = (rowLength < 1);

    // enable the next button ONLY WHEN there are medicines selected
    medicinePickerCarousel_NextBtn.prop('disabled', noneSelected);
}

function bindSelectMedicine()
{
    $(".medicines-table").on("click", ".btn-select-medicine", function()
    {
        var currentRow = $(this).closest("tr");
        var flagText = currentRow.find("td:eq(4)").text(); 
 
        // get the flag text then check if true or false string
        var flag = (flagText == "true");

        // flip the flags .. if true then set to false vice-versa
        flag = !flag;

        // update the flag text in td
        currentRow.find("td:eq(4)").text(flag); 
        
        // if flag is true, button appearance should be Red background with text as "Unselect";
        // if flag is false, button appearance should be Teal background with text as "Select";
        if (flag)
        {
            currentRow.find('.btn-select-medicine').text("Unselect").removeClass('bg-teal').addClass('bg-red');
            
            var itemId = currentRow.find("td:eq(5)").text();  
            var itemName = currentRow.find("td:eq(0)").text();  
            var itemCategory = currentRow.find("td:eq(1)").text();  
            var remaining = currentRow.find("td:eq(6)").text();  
            var measurement = currentRow.find("td:eq(7)").text();  

            enqueueSelectedMedicines(itemId, itemName, itemCategory, remaining, measurement);
        }
        else 
        {
            currentRow.find('.btn-select-medicine').text("Select").removeClass('bg-red').addClass('bg-teal');

            var itemId = currentRow.find("td:eq(5)").text(); 
            dequeueSelectedMedicine(itemId)
        }
    });
}

function bindPlusMinusOnSelectMedicine()
{ 
    $(".selected-medicines-table")
    
    // the green plus button
    .on("click", ".btn-increase-amount", function()
    {
        var currentRow = $(this).closest("tr");
        var amountText = currentRow.find(".input-medicine-amount").val(); 
        var remainingText = currentRow.find("td:eq(5)").text(); 
        var amount = parseInt(amountText) || 0;
        var remaining = parseInt(remainingText) || 0;

        if (remaining == 0)
            return;

        // increase the amount but it should not go higher than remaining
        if (amount < remaining)
        {
            amount++;
            currentRow.find(".input-medicine-amount").val(amount);
        } 
    })
    
    // the red minus button
    .on("click", ".btn-decrease-amount", function()
    {
        var currentRow = $(this).closest("tr");
        var amountText = currentRow.find(".input-medicine-amount").val();  
        var amount = parseInt(amountText) || 0;

        // decrease the amount but it should not go lower than 1
        if (amount > 1)
        {
            amount--;
            currentRow.find(".input-medicine-amount").val(amount);
        } 
    });
}

function filterMedicine(category)
{ 
    var table = $(".medicines-table");
    var rows = table.find("tbody tr");

    // if there are no rows present, exit
    if (rows.length == 0)
        return;

    // show all medicines
    if (category == "all")
    {
        rows.each(function(i, row){
            $(rows[i]).show();
        });

        return;
    }

    // show only selected medicines
    if (category == "only-selected")
    {
        rows.each(function(i, row)
        {
            var flagCell = $(rows[i].cells[4]).text();

            if (flagCell == "true")
                $(rows[i]).show();
            else
                $(rows[i]).hide();
        });

        return;
    }

    // show specific medicine by category
    rows.each(function(i, row)
    { 
        var categoryCell = $(rows[i].cells[1]).text();

        if (categoryCell != category)
            $(rows[i]).hide();
        else
            $(rows[i]).show();
    }); 
}

function enqueueSelectedMedicines(itemId, itemName, itemCategory, remaining, measurement)
{ 
    var table = $(".selected-medicines-table");
    var rows = table.find("tbody tr");
    var tbody = $(".selected-medicine-dataset");

    var rowExists = false;

    // check the table's rows when the medicine item already exists
    // then do not add this row. We can do the checking by comparing the
    // item ids
    rows.each(function (i, row) 
    {
        var itemIdCell = $(rows[i].cells[4]).text();

        if (itemIdCell == itemId)
        {
            rowExists = true;
            return false;
        }
    });

    if (rowExists)
        return;
    
    // append the newly selected medicine
    tbody.append(`<tr class="align-middle">
        <td class=\"fw-bold\">${itemName}</td>
        <td>${itemCategory}</td>
        <td>${remaining} ${measurement}(s)</td>
        <td class="w-25">
            <div class="d-flex flex-row gap-2">
                <button class="btn btn-danger btn-decrease-amount bg-red text-white px-3">
                    <i class="fas fa-minus"></i>
                </button>
                <div class="form-outline">
                    <input type="text" class="form-control input-medicine-amount text-center" value="1" oninput=\"trackAmountInput(${remaining}, this.value, this)\" />
                </div>
                <button class="btn btn-primary btn-increase-amount bg-teal text-white px-3">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
        </td>
        <td class="d-none">${itemId}</td>
        <td class="d-none">${remaining}</td>
    </tr>`);

    unlockNextButton();
 
}
 
function dequeueSelectedMedicine(itemId)
{
    var table = $(".selected-medicines-table");
    var rows = table.find("tbody tr"); 

    var rowExists = false;
    var rowIndex = -1;

    // check the table's rows when the medicine item exists
    // then remove this row. We can do the checking by comparing the
    // item ids. We will remove the row by its 0-based RowIndex
    rows.each(function (i, row) 
    {
        var itemIdCell = $(rows[i].cells[4]).text();

        if (itemIdCell == itemId)
        {
            rowExists = true;
            rowIndex = i;
            
            return false;
        }
    });

    if (!rowExists || rowIndex < 0)
        return;

    table.find("tbody tr:eq(" + rowIndex + ")").remove();

    unlockNextButton();
}

function trackAmountInput(totalRemaining, amount, element)
{
    if (amount != '' && amount < 1)
    $(element).val(1);

    // amount entered should not go higher than remaining
    if (amount > totalRemaining)
        $(element).val(totalRemaining);
    // alert(amount);
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
 

// https://stackoverflow.com/questions/5737272/jquery-dynamically-show-table-rows
// reset divs
// https://stackoverflow.com/a/5557716