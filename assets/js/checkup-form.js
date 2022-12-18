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

var selectedMedicinesTable = undefined;

var clonable_medicineSelectorWrapper = undefined;
var clonable_selectedMedicinesWrapper = undefined;
var clonable_medicineSelectorFooter = undefined;

var carouselPage1 = undefined;
var carouselPage2 = undefined;
 
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
        input_illness_id: $(".input-illness-id"),
        input_remarks: $(".input-remarks")
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
                filterIllnessDataSet($(this).val());
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

    selectedMedicinesTable = $(".selected-medicines-table");

    carouselPage1 = $(".carousel-page-1");
    carouselPage2 = $(".carousel-page-2");

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
    Input.forceRemarks("input-remarks");
 
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
    
    // fire this event after the carousel slid event
    bindCarouselSlid();

    // create a copy of medicine picker modal's tables original state  
    clonable_medicineSelectorWrapper = $(".medicine-selector-table-wrapper").clone(true, true);
    clonable_selectedMedicinesWrapper = $(".selected-medicines-table-wrapper").clone(true, true);
    clonable_medicineSelectorFooter = $(".medicine-picker-footer").clone(true, true);

    btn_clearPrescriptions.click(() => clearPrescriptions());

    // force medicine selector input fields to set value as 1 when 
    // the user entered 0 or empty
    $(document).on("blur", ".input-medicine-amount", function()
    {
        if ($(this).val() == '' || $(this).val() < 1)
            $(this).val(1);
    });

    // copy the selected medicines from the modal into the prescription table
    $(document).on("click", ".btn-carsl-ok", () => createPrescription());
}
//=============================================
//-------------- BUSINESS LOGIC ---------------
//=============================================
function validateRequiredFields()
{ 
    var optionalFields = 
    [
        "input_remarks",
        "input_weight"
    ]; 

    var invalidFields = 0;

    Object.entries(fields).map(entry => 
    {
        let key = entry[0];
        let value = entry[1].val();
  
        if (!optionalFields.includes(key))
        {
            if (value == undefined || value == null || value == "")
            {
                invalidFields++;
            }
        }
    });
 
    return invalidFields == 0; 
}

function sendDataToServer()
{
    var obj = {};

    for (var f in fields)
    { 
        obj[f] = fields[f].val();  
    }   

    var prescription = prescriptionToOBJ();

    $.ajax(
    {
        type: "POST",
        url: "ajax.save-checkup-info.php",
        data: 
        { 
            jsonData: JSON.stringify(obj),
            prescription: JSON.stringify(prescription)
        },
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

function filterIllnessDataSet(leadingChar)
{
    var table = $(".illness-selector-table");
    var rows = table.find("tbody tr");

    // if there are no rows present, exit
    if (rows.length == 0)
        return;

    // show all medicines
    if (leadingChar == "all") 
    {
        rows.each(function (i, row) {
            $(rows[i]).show();
        });

        return;
    } 

    // show specific medicine by leading character
    rows.each(function (i, row) 
    {
        var illnessCell = $(rows[i].cells[0]).text();
        var lead = Array.from(illnessCell)[0];
 
        if (lead != leadingChar)
            $(rows[i]).hide();
        else
            $(rows[i]).show();
    });
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
    var rowLength = $(document.querySelector(".selected-medicines-table")).find("tbody tr").length;
    var nextBtn = $(document.querySelector(".btn-carsl-next"));
    var noneSelected = (rowLength < 1);
    // enable the next button ONLY WHEN there are medicines selected
    nextBtn.prop('disabled', noneSelected);
}

function bindSelectMedicine()
{
    $(document).on("click", ".medicines-table .btn-select-medicine", function()
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
            var units = currentRow.find("td:eq(8)").text();  
            
            enqueueSelectedMedicines(itemId, itemName, itemCategory, remaining, measurement, units);
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
    $(document)
    
    // the green plus button
    .on("click", ".selected-medicines-table .btn-increase-amount", function()
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

            // update hidden amount cell
            currentRow.find("td:eq(6)").text(amount);
        } 
    })
    
    // the red minus button
    .on("click", ".selected-medicines-table .btn-decrease-amount", function()
    {
        var currentRow = $(this).closest("tr");
        var amountText = currentRow.find(".input-medicine-amount").val();  
        var amount = parseInt(amountText) || 0;

        // decrease the amount but it should not go lower than 1
        if (amount > 1)
        {
            amount--;
            currentRow.find(".input-medicine-amount").val(amount);

            // update hidden amount cell
            currentRow.find("td:eq(6)").text(amount);
        } 
    });
}

function bindCarouselSlid()
{
    // fire events after carousel has finished sliding
    //medicinePickerCarousel.on("slid.mdb.carousel", () => 
    $(document).on("slid.mdb.carousel", "#medicinePickerCarousel", () => 
    {
        var nextBtn = $(document.querySelector(".btn-carsl-next"));
        var backBtn = $(document.querySelector(".btn-carsl-back")); 
        var okBtn = $(document.querySelector(".btn-carsl-ok")); 

        // track each carousel page's visibility by checking if class 'active' is present
        // if page 2 is active, enable the back button and hide the next button. 
        // then show the OK button
        //if (carousel_page2.hasClass("active"))
        if (carouselPage2.hasClass("active"))
        {
            nextBtn.hide();
            backBtn.show();
            okBtn.show();
        }
        // if page 1 is active, enable the next button and hide the back button. 
        // also hide the OK button.
        //if (carousel_page1.hasClass("active"))
        if (carouselPage1.hasClass("active"))
        {
            backBtn.hide();
            nextBtn.show();
            okBtn.hide();
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

function enqueueSelectedMedicines(itemId, itemName, itemCategory, remaining, measurement, units)
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
        <td class="d-none">1</td>
        <td class="d-none">${measurement}</td>
        <td class="d-none">${units}</td>
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
}

function resetForm()
{  
    checkupForm.reset();
    genderDropButton.text("Select Gender");
    fields.input_gender.val('');

    patientDropButton.text("Patient Type");
    fields.input_patientType.val(''); 

    clearPrescriptions();

    $(".checkup-form").getNiceScroll(0).doScrollTop(0, 300);
}

function createPrescription()
{
    var table = $(document.querySelector(".selected-medicines-table"));
    var rows = table.find("tbody tr");
    var tbody = $(".prescription-dataset");
     
    if (rows.length < 1)
        return;

    tbody.empty();

    rows.each(function(i, row)
    {
        var idCell = $(rows[i].cells[4]).text();
        var itemCell = $(rows[i].cells[0]).text();
        var categoryCell = $(rows[i].cells[1]).text();
        var amountCell = $(rows[i].cells[6]).text();
        var measuresCell = $(rows[i].cells[7]).text(); 
        var unitsCell = $(rows[i].cells[8]).text(); 
        
        tbody.append(`<tr class=\"align-middle\">
            <td class=\"d-none\">${idCell}</td>
            <td>${itemCell}</td>
            <td>${categoryCell}</td>
            <td>${amountCell} ${measuresCell}(s)</td>
            <td class=\"d-none\">${amountCell}</td>
            <td class=\"d-none\">${unitsCell}</td>
        </tr>`);
        
    });
}

function prescriptionToOBJ()
{
    var table = $(document.querySelector(".prescription-table"));
    var rows = table.find("tbody tr");
    var objArray = [];
     
    if (rows.length < 1)
        return objArray;

    rows.each(function(i, row)
    {
        var itemId = $(rows[i].cells[0]).text();
        var amount = $(rows[i].cells[4]).text();
        var units = $(rows[i].cells[5]).text();
        
        var obj = 
        {
            "itemId": itemId,
            "amount": amount,
            "units": units
        };

        objArray.push(obj);
    });

    return objArray;
}

function clearPrescriptions()
{
    // reset table wrappers to original state
    //var medicinesWrapper = document.querySelector(".medicine-selector-table-wrapper");
    var selectedWrapper = document.querySelector(".selected-medicines-table-wrapper");
    var footer = document.querySelector(".medicine-picker-footer");
    var prescriptionTable = $(".prescription-dataset");

    //$(medicinesWrapper).replaceWith(clonable_medicineSelectorWrapper.clone(true, true));
    loadMedicinesList();

    $(selectedWrapper).replaceWith(clonable_selectedMedicinesWrapper.clone(true, true));
    $(footer).replaceWith(clonable_medicineSelectorFooter.clone(true, true));

    prescriptionTable.empty();

    $("#filter-medicine-category").val('all').selectmenu('refresh');

    // reset carousel to slide1
    carouselPage1.addClass("active");
    carouselPage2.removeClass("active");
}

function loadMedicinesList()
{
    var tbody = $(".medicine-selector-dataset");

    $.ajax(
    {
        url: "ajax/ajax.get-medicines.php",
        dataType: "json",
        type: "GET",
        success: function(res)
        {
            // update the medicine selectors table
            if (res)
            { 
                tbody.empty();

                var totalCriticalItems = res.criticalCount;
                var totalSoldOutItems = res.soldOutCount;
                var medicinesDataset = res.medicines;

                // update counter indicators
                $(".lbl-critical-counter").text(totalCriticalItems);
                $(".lbl-soldout-counter").text(totalSoldOutItems);

                // recreate the medicines picker table
                medicinesDataset.forEach(data => 
                {
                    // header idx 5
                    var itemId = data.item_id;
                    var itemName = data.item_name;
                    var category = data.category;

                    // header idx 7
                    var measurement = data.measurement;

                    // header idx 6
                    var remaining = data.remaining;
                    var criticalLevel = data.critical_level;
                    var unitMeasure = data.unit_measure;

                    var badge_stock = `<span class="badge badge-success">Available</span>`;
                    var btn_disableOnSoldOut = (remaining == 0) ? "disabled" : "";

                    if (remaining <= criticalLevel)
                        badge_stock = `<span class="badge badge-warning">Low on stock</span>`;

                    if (remaining == 0)
                        badge_stock = `<span class="badge badge-danger">Sold out</span>`;

                    tbody.append(`<tr class=\"align-middle\"> 
                        <td>${itemName}</td> 
                        <td>${category}</td>
                        <td>${badge_stock}</td>
                        <td>
                            <button class=\"btn btn-primary btn-select-medicine bg-teal text-white py-1 px-0 text-center\" style=\"max-width: 92px; width: 92px;\" ${btn_disableOnSoldOut}>
                                Select
                            </button>
                        </td>
                        <td class=\"d-none\">false</td>
                        <td class=\"d-none\">${itemId}</td>
                        <td class=\"d-none\">${remaining}</td>
                        <td class=\"d-none\">${measurement}</td>
                        <td class=\"d-none\">${unitMeasure}</td>
                        </tr>`);
                });  
            }
        },
        error: function(jqXHR, exception)
        {
            dialog.danger("Failed to retrieve the list of medicines. Please reload the page or contact the administrator.");
        }
    });
}


// only execute this entire script after the page has fully loaded
$(document).ready(() => onAwake());