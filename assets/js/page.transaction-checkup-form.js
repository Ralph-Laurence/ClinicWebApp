var confirm = undefined;
var snackbar = undefined;
var toast = undefined;

var btn_newPatient = undefined;
var btn_clearPrescription = undefined;
var btn_submit = undefined;
var btn_reset = undefined;

var expandHistory = false;

//var medicineDatatable = undefined;

var errorMessageWrapper = undefined;
var errorMessageLabel = undefined;

var mainForm = undefined;

$(document).ready(function(){
    onAwake();
});

function onAwake()
{
    confirm = new ConfirmDialog();
    snackbar = new SnackBar();
    toast = new Toast();

    btn_newPatient = $(".btn-new-patient");
    btn_clearPrescription = $(".btn-clear-prescription");
    btn_submit = $(".btn-submit");
    btn_reset = $(".btn-reset");

    errorMessageWrapper = $(".error-message");
    errorMessageLabel = $(".error-label");

    mainForm = $("#checkup-form");

    $("#patient-search-filter").selectmenu({
        width: 150,
        change: function (event, ui) 
        {  
            clearSearchOnPicker('patient-picker-toolbar', 'patient-picker-table-wrapper');
        }
    });
    
    $(`.patient-picker-toolbar .btn-clear-search`).click(function()
    {
        clearSearchOnPicker('patient-picker-toolbar', 'patient-picker-table-wrapper');
        $("#patient-search-filter").val('0').selectmenu('refresh');
    });

    $(`.illness-picker-toolbar .btn-clear-search`).click(function()
    {
        clearSearchOnPicker('illness-picker-toolbar', 'illness-table-wrapper', 'illness-searchbar'); 
    });

    $('.medicine-picker-toolbar .btn-clear-search').click(function()
    {
        clearSearchOnPicker('medicine-picker-toolbar ', 'medicine-table-wrapper', 'medicines-searchbar'); 
    });

    setupPatientPickerTable();
    setupIllnessPickerTable();
    setupMedicinesPickerTable();

    // Load history of forwarded patient
    var forwardAppointKey = $(".forward-appointment-key").val();

    if (!System.isNullOrEmpty(forwardAppointKey))
    {
        loadMedicalHistory(forwardAppointKey);
    }

    notify();
    onBind();
}

function onBind()
{ 
    $(document)
    //
    // Increase quantity from prescription table
    //
    .on("click", ".prescription-table .btn-qty-plus", function()
    {
        var input = $(this).parent().find('.prescription-qty');
        var qty = parseInt(input.val()) || 0;
        var max = parseInt(input.attr("data-max-qty")) || 0;

        if (qty < max)
        {
            qty++;
            $(this).parent().find('.prescription-qty').val(qty);
        } 
    })
    //
    // Decrease quantity from prescription table
    //
    .on("click", ".prescription-table .btn-qty-minus", function()
    { 
        var input = $(this).parent().find('.prescription-qty');
        var qty = parseInt(input.val()) || 0;
        var min = parseInt(input.attr("data-min-qty")) || 0;

        if (qty > min)
        {
            qty--;
            $(this).parent().find('.prescription-qty').val(qty);
        } 
    })
    //
    // When typing / entering specific values,
    // Prevent quantity values from going above or less than min and max
    //
    .on("input", ".prescription-table .prescription-qty",function()
    {
        var qty = parseInt($(this).val()) || 0;
        var min = parseInt($(this).attr("data-min-qty")) || 0;
        var max = parseInt($(this).attr("data-max-qty")) || 0;

        if (qty < min)
            $(this).val(min);

        if (qty > max)
            $(this).val(max);
    })
    //
    // Dequeue (Remove) medicine from prescription table
    //
    .on("click", ".prescription-table [data-action='dequeue']",function()
    {
        //
        var dequeueTarget = $(this).attr("data-dequeue-target");
        $(".medicine-picker-body").find(`tr#${dequeueTarget}`).show();

        $(this).closest('tr').remove();
    })
    //
    // Medicine picker Modal -> Select Medicine button
    //
    .on("click", ".medicine-picker-body .btn-select-medicine", function()
    {
        var tr = $(this).closest("tr");
 
        var itemKey = tr.find(".item-key").text().trim();
        var medicineName = tr.find(".item-name").text().trim();
        var category = tr.find(".item-category").text().trim();
        var stock = tr.find(".stock-label").text().trim();
        var maxQty = tr.find(".max-qty").text().trim();
        var itemCode = tr.attr("id").trim();
        var stockData = tr.find(".stock-data").text().trim();
        var unitsLabel = tr.find(".units-label").text().trim();

        var medicineData = 
        {
            name: medicineName,
            category: category,
            stock: stock,
            maxQty: maxQty,
            itemKey: itemKey,
            itemCode: itemCode,
            stockData: stockData,
            unitsLabel: unitsLabel
        };
 
        selectMedicine(medicineData);
    })
    //
    // Doctor picker Modal -> Select Doctor button
    //
    .on("click", ".doctor-picker-body .btn-select-doctor", function()
    {
        var tr = $(this).closest("tr");

        var doctorKey = tr.find(".td-doctor-key").text().trim();
        var doctorName = tr.find(".td-doctor-name").text().trim();

        selectDoctor(doctorKey, doctorName);
    });

    // Show warning message when the Register button was clicked
    btn_newPatient.click(() => 
    { 
        confirm.actionOnOK = function () {
            var goBack = $(".registration-route").val();
            navHref(goBack);
        };

        var message = "You are about to leave this page. All unsaved changes will be lost.\n\nDo you wish to continue?";
        var title = "Register Patient";

        confirm.show(message, title, "Yes", "No");
    });

    // Clear all prescriptions on 'Clear' button click
    btn_clearPrescription.click(() => 
    {
        // Get all rows from prescription table
        var rows = $('.prescription-body > tr');

        if (rows.length < 1)
            return;
            
        confirm.actionOnOK = function () {
            clearPrescriptions(rows);
        };

        var message = "Do you wish to remove all prescriptions?";

        confirm.show(message, "Clear Prescriptions", "OK", "Cancel");
    });

    // Submit the checkup form
    btn_submit.click(() => 
    {
        // validate fields before submitting
        var allFieldsValid = validateFields();

        if (!allFieldsValid)
        {
            toast.show("Please fillout all fields!", "Submit Failed", toast.toastTypes.DANGER);
            return;
        }

        var prescriptions = collectPrescriptions();

        if (!System.isNullOrEmpty(prescriptions))
            $(".prescriptions").val(prescriptions);

        mainForm.trigger("submit");
    });

    // Reset the form and remove prescriptions
    btn_reset.click(() => 
    {
        confirm.actionOnOK = function () 
        {
            resetForm();
            toast.show("Form has been cleared.", "Reset Form", toast.toastTypes.SUCCESS);
        };

        var message = "Do you wish to reset all data? This will:\n\n\u25CF Clear all input fields\n\u25CF Remove all prescriptions";

        confirm.show(message, "Reset Form Data", "OK", "Cancel");
    });

    $(".btn-add-medicine").click(function () 
    {  
        $(".prescription-wrapper").fadeIn();
        $(".btn-clear-prescription").fadeIn();
    });

    // Force all numeric fields to accept only numbers
    Input.forceNumeric("numeric");

    $(".patient-picker-toolbar .searchbar").on("keyup", function()
    {
        searchPatients();
    });

    $(".illness-picker-toolbar .illness-searchbar").on("keyup", function()
    {
        searchIllness();
    });

    $(".medicine-picker-toolbar .medicines-searchbar").on("keyup", function()
    {
        searchMedicines();
    }); 

    $(".past-medical-history .btn-expand").click(function()
    {
        expandHistory = !expandHistory;

        //if ($(".tr-history-expandable:hidden").length > 0)
        if (expandHistory)
        {
            $(".tr-history-expandable").fadeIn('fast');
            $(".btn-expand .expand-arrow") 
                .removeClass("fa-chevron-down").addClass("fa-chevron-up");

            $(".btn-expand span").text("Show Less");
            return;
        }
        
        $(".tr-history-expandable").fadeOut('fast');
        $(".btn-expand .expand-arrow") 
            .removeClass("fa-chevron-up").addClass("fa-chevron-down");

        $(".btn-expand span").text("Show More");
    });

    $(".btn-preview").click(function()
    {
        $(".frm-preview-checkup").trigger("submit");
    });
}

function setupPatientPickerTable()
{
    jQuery.fn.dataTableExt.pager.numbers_length = 5;

    // Add search functionality foreach column
    $('.patient-picker-table tfoot th').each(function (i) 
    { 
        $(this).html(`<input id="tfoot-col-search-${i}" class="tfoot-col-search" type="text" />`);
    });
    
    // Create the datatable
    $(".patient-picker-table").DataTable(
    { 
        pagingType: "full_numbers",
        autoWidth: false,
        initComplete: function () 
        {
            // Apply the search
            this.api().columns().every(function () 
            {
                var that = this;

                $('.tfoot-col-search', this.footer()).on('keyup change clear', function () {
                    if (that.search() !== this.value)
                        that.search(this.value).draw();
                });
            });
        }
    });

    // Find the original search bar of Datatable object then hide it
    $(".patient-picker-table-wrapper .dataTables_filter > label").hide();

    recreatePagination();
}

function setupIllnessPickerTable()
{
    jQuery.fn.dataTableExt.pager.numbers_length = 5;

    $('.illness-picker-table tfoot th').each(function (i) 
    { 
        $(this).html(`<input id="tfoot-col-search-${i}" class="tfoot-col-search" type="text" />`);
    });

    $(".illness-table-wrapper .table").DataTable({
        pagingType: "full_numbers",
        autoWidth: false,
        initComplete: function () 
        {
            // Apply the search
            this.api().columns().every(function () 
            {
                var that = this;

                $('.tfoot-col-search', this.footer()).on('keyup change clear', function () {
                    if (that.search() !== this.value)
                        that.search(this.value).draw();
                });
            });
        }
    });

    $(".illness-table-wrapper").find("#DataTables_Table_1_filter").hide();
    var dtLength = $(".illness-table-wrapper").find(".dataTables_length");
    
    // hide the original entries paginator
    dtLength.hide();

    $(".illness-entries-filter-container").empty();

    // copy the original entries paginator's options
    // to the virtual entries paginator
    var cloned = dtLength.find('select').clone(true, true)
        .removeAttr("name")
        .removeAttr("class")
        .removeAttr("aria-controls")
        .attr("id", "illness-entries-filter")
        .hide();

    $(cloned[0]).appendTo(".illness-entries-filter-container");

    $("#illness-entries-filter").selectmenu({
        width: 90,
        change: function (event, ui) 
        {
            $(dtLength.find("select")).val(ui.item.value).change();
        }
    });
}

function setupMedicinesPickerTable()
{
    jQuery.fn.dataTableExt.pager.numbers_length = 5;

    $('.medicine-picker-table tfoot th').each(function (i) 
    { 
        $(this).html(`<input id="tfoot-col-search-${i}" class="tfoot-col-search" type="text" />`);
    });

    $(".medicine-picker-table").DataTable({
        pagingType: "full_numbers",
        autoWidth: false,
        order: [[1, 'asc']],
        initComplete: function () 
        {
            // Apply the search
            this.api().columns().every(function () 
            {
                var that = this;

                $('.tfoot-col-search', this.footer()).on('keyup change clear', function () {
                    if (that.search() !== this.value)
                        that.search(this.value).draw();
                });
            });
        }
    });

    $(".medicine-table-wrapper").find("#DataTables_Table_2_filter").hide();
    var dtLength = $(".medicine-table-wrapper").find(".dataTables_length");
    
    // hide the original entries paginator
    dtLength.hide();

    $(".medicine-entries-filter-container").empty();

    // copy the original entries paginator's options
    // to the virtual entries paginator
    var cloned = dtLength.find('select').clone(true, true)
        .removeAttr("name")
        .removeAttr("class")
        .removeAttr("aria-controls")
        .attr("id", "medicine-entries-filter")
        .hide();

    $(cloned[0]).appendTo(".medicine-entries-filter-container");

    $("#medicine-entries-filter").selectmenu({
        width: 90,
        change: function (event, ui) 
        {
            $(dtLength.find("select")).val(ui.item.value).change();
        }
    });
}
//
// Recreate the entries dropdown filter
//
function recreatePagination() 
{
    var dtLength = $(".patient-picker-table-wrapper").find(".dataTables_length");
    
    // hide the original entries paginator
    dtLength.hide();

    $(".entries-filter-container").empty();

    // copy the original entries paginator's options
    // to the virtual entries paginator
    var cloned = dtLength.find('select').clone(true, true)
        .removeAttr("name")
        .removeAttr("class")
        .removeAttr("aria-controls")
        .attr("id", "patient-entries-filter")
        .hide();

    $(cloned[0]).appendTo(".entries-filter-container");

    $("#patient-entries-filter").selectmenu({
        width: 90,
        change: function (event, ui) 
        {
            $(dtLength.find("select")).val(ui.item.value).change();
        }
    });
}

function searchPatients()
{
    // Searchbox must not be empty to begin searching
    var keyword = $(".searchbar").val();
 
    // Identify which filter to apply.
    var option = $("#patient-search-filter").val();

    // Then Reflect the search terms in specific column filter inputboxes found in table's <tfoot>.
    // The original searchbar is hidden and it only triggers on keyup, so let's trigger it manually 
    // using keyup().
    switch (option)
    { 
        case '1':    
            $("tfoot .search-col-patient-id").find(":input").val(keyword).keyup();
            break;
        case '0': 
            $("tfoot .search-col-patient-name").find(":input").val(keyword).keyup();
            break; 
    }
  
    // Show the clear button whenever there is a search performed
    $(".patient-picker-toolbar .btn-clear-search").show();
}

function clearSearchOnPicker(toolbar, tableWrapper, searchbar = "searchbar")
{
    $(`.${tableWrapper} tfoot .tfoot-col-search`).each(function()
    {
        $(this).val('').keyup();
    });

    $(`.${toolbar} .${searchbar}`).val('');
    $(`.${toolbar} .btn-clear-search`).hide();
}


function searchIllness()
{
    // Searchbox must not be empty to begin searching
    var keyword = $(".illness-searchbar").val();
  
    $("tfoot .search-col-illness").find(":input").val(keyword).keyup();

    $(".illness-picker-toolbar .btn-clear-search").show();
}

function searchMedicines()
{
    // Searchbox must not be empty to begin searching
    var keyword = $(".medicines-searchbar").val();
  
    $("tfoot .search-col-medicines").find(":input").val(keyword).keyup();

    $(".medicine-picker-toolbar .btn-clear-search").show();
}
  
// Stock Amount is the actual amount
// Stock is the descriptive text
function selectMedicine(data)
{     
    // data-max-qty="${data.maxQty}"
    // ${data.stock}
    // <option value="" selected disabled>Select Stock</option>

    // Build the row
    var row = 
    `<tr class="align-middle rx-tr">
        <td class="text-truncate" style="max-width: 200px; width: 200px; py-0">
            <div class="d-flex justify-content-start flex-column">
                <div class="fw-bold">${data.name}</div>
                <div class="text-primary">${data.category}</div>
            </div>
        </td>
        <td class="text-truncate" style="max-width: 200px; width: 200px;">
            <select class="stocks-selector"></select>
        </td>
        <td class="text-truncate available-stock-label" style="max-width: 200px; width: 200px;"></td>
        <td class="text-center">
            <div class="d-flex flex-row align-items-center justify-content-center gap-2">
                <button type="button" class="btn btn-warning bg-accent py-1 px-2 btn-qty-minus">
                    <i class="fas fa-minus"></i>
                </button>
                <div>
                    <input type="text" class="text-center prescription-qty" data-min-qty="1" value="1" data-max-qty=""/>
                </div>
                <button type="button" class="btn btn-primary bg-teal py-1 px-2 btn-qty-plus">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-secondary p-1 btn-remove" data-action="dequeue" data-dequeue-target="${data.itemCode}">
                <i class="fas fa-times me-1"></i>
                <span>Remove</span>
            </button>
        </td>  
        <td class="d-none rx-item-key">${data.itemKey}</td>
        <td class="d-none stock-data">${data.stockData}</td>
        <td class="d-none">
            <input type="text" class="rx-stock-id" />
        </td>
    </tr>`;
  
    // Insert (append) the medicine into the prescription table
    $(".prescription-body").append(row);

    var newlyAppendedRow = $(".prescription-body .rx-tr:last");

    if (data.stockData)
    {
        var json = jQuery.parseJSON(data.stockData);

        $.each(json, function(index, item)
        {
            if (item.qty > 0)
                $(newlyAppendedRow).find(".stocks-selector").append
                (
                    `<option value="${item.stockId}" data-stock="${item.stockId}" data-qty="${item.qty}" data-units="${data.unitsLabel}">
                        Stock #${item.stockId} - ${item.sku}
                    </option>`
                ); 
        }); 
    }

    $('.stocks-selector').selectmenu({
        width: "100%",
        change: function(event, ui)
        {
            var tr = $(this).closest('tr');
            var option = tr.find(":selected");
             
            tr.find(".available-stock-label")
            .text(`${option.attr('data-qty')} ${option.attr('data-units')}`);

            tr.find('.prescription-qty').val('1').attr('data-max-qty', option.attr('data-qty'));
            tr.find('.rx-stock-id').val(option.attr("data-stock"));
        }
    });

    // Default values of newly added prescription
    var defaultOption = $(newlyAppendedRow).find('.stocks-selector option:selected'); 
    $(newlyAppendedRow).find('.available-stock-label').text(`${defaultOption.attr('data-qty')} ${defaultOption.attr('data-units')}`);
    $(newlyAppendedRow).find('.prescription-qty').attr('data-max-qty', defaultOption.attr('data-qty'));
    $(newlyAppendedRow).find('.rx-stock-id').val(defaultOption.attr('data-stock'));

    // Hide the selected Row from picker
    $(".medicine-picker-body").find(`tr#${data.itemCode}`).hide();
    // $(".medicine-picker-body").find(`tr:eq(${rowIndex})`).hide();

    // Scroll to the bottom after we append a new medicine
    // https://github.com/Grsmto/simplebar/issues/184#issuecomment-531666659
    // Thanks kgkg !
    // var container = document.querySelector('#workarea .simplebar-content-wrapper'); 
    var container = document.querySelector('#main-workarea .simplebar-content-wrapper'); 
    container.scrollTo({ top: 500, behavior: "smooth" }); 
}

function notify()
{
    // Show toast when patient is successfully registered
    var success = $(".register-success-message").val();

    if (!System.isNullOrEmpty(success))
        toast.show(success, "Success", toast.toastTypes.SUCCESS);

    // Show toast when checkup data is successfully created
    var checkup_success = $(".checkup-success-message").val();

    if (!System.isNullOrEmpty(checkup_success))
        snackbar.show(checkup_success);

    // Show error message on checkup form
    var checkupErrorMsg = $(".checkup-error").val();

    if (!System.isNullOrEmpty(checkupErrorMsg))
        showError(checkupErrorMsg);
}

function showError(message)
{
    errorMessageLabel.text(message);
    errorMessageWrapper.show();

    var container = document.querySelector('#main-workarea .simplebar-content-wrapper'); 
    container.scrollTo({ top: 500, behavior: "smooth" }); 
}

function hideError()
{
    errorMessageLabel.text('');
    errorMessageWrapper.hide();
}

function selectPatient(key, id, name, type)
{ 
    $(".selected-patient-key").val(key);
    $(".patient-name").val(name).focus().blur();
    $(".patient-idnum").val(id).focus().blur();
    $(".patient-type-lbl").text(type);
    $(".patient-type-lbl-wrapper").show();

    loadMedicalHistory(key)
}   

function loadMedicalHistory(key)
{
    // Disable FIND button while fetching history
    $(".btn-find-patient").prop("disabled", true);

    // Show progress bar on history fetch

    // Fetch AJAX data
    $.ajax({
        type: "POST",
        url: $(".medical-history-url").val(),
        data: {
            patientKey: key
        },
        success: function (response) 
        {   
            $(".past-medical-history .history-body").empty();
            $(".past-medical-history .btn-expand").fadeOut('fast');

            if(response.length > 0)
            { 
                $.each(response, function(index, value) 
                {
                    var date = "Unknown";
                    var time = "Unknown";

                    if (value.date)
                    {
                        // convert date to Month, Day, Year format
                        date = moment(value.date).format("MMMM DD, YYYY");

                        // extract time
                        time = moment(value.date).format("hh:mm a");

                        // Tell if date is today, yesterday or past day
                        var today = moment().startOf('day');
                        var yesterday = moment().subtract(1, 'days').startOf('day');

                        if (moment(value.date).isSame(today, 'd')) {
                            date = `<span class="px-2 py-1 bg-document text-primary rounded-5">Today</span>`;
                        }
                        else if (moment(value.date).isSame(yesterday, 'd')) {
                            date = "Yesterday";
                        }
                    }

                    var collapsedTr = "";

                    if (index > 0)
                    {
                        collapsedTr = "display-none tr-history-expandable";

                        if ($(".past-medical-history .btn-expand").is(":hidden"))
                            $(".past-medical-history .btn-expand").fadeIn('fast');
                    }
                      
                    $(".past-medical-history .history-body")
                    .append(`
                        <tr class="${collapsedTr}">
                            <td class="th-230 text-truncate">${value.illness}</td>
                            <td class="th-230 text-truncate">${value.docname}</td>
                            <td class="th-100 text-truncate">${date}</td>
                            <td class="th-100 text-truncate">${time}</td>
                        </tr>
                    `); 
                });

                $(".past-medical-history").fadeIn('fast');
            }
            else
            {
                $(".past-medical-history").fadeOut('fast');
            }
        },
        error: function(jqXHR, textStatus, errorThrown) 
        {
            // handle error response
            console.log('Error: ' + errorThrown);
        },
        complete: function(jqXHR, textStatus)
        {
            $(".btn-find-patient").prop("disabled", false);
        }
    });
}

function selectDoctor(key, name)
{
    $(".doctor-key").val(key);
    $(".physician").val(name).focus().blur();
}

function selectIllness(id, name)
{ 
    $(".illness-id").val(id);
    $(".input-illness").val(name).focus().blur();
}
 
function collectPrescriptions()
{
    // Get all rows from prescription table
    var rows = $('.prescription-body > tr');

    if (rows.length < 1)
        return '';

    // We will store all prescription objects here
    // for json encode
    var prescriptions = [];

    $(rows).each(function(index, tr) 
    {
        var itemKey     = $(tr).find(".rx-item-key").text().trim();
        var quantity    = $(tr).find(".prescription-qty").val();
        var stockId     = $(tr).find(".rx-stock-id").val();

        var prescriptionObj = 
        {
            itemId:     itemKey,
            quantity:   quantity,
            stockId:    stockId
        };
         
        prescriptions.push(prescriptionObj);
    });

    return JSON.stringify(prescriptions);
}

function clearPrescriptions(rows)
{ 
    // Find every cell that contains the "Remove" button
    // Then trigger the "click" event.
    $(rows).each(function(index, tr) 
    { 
        var removeButton = $(tr).find(".btn-remove"); 
        removeButton.click();
    }); 
}
//
// Resetting the form clears input fields and 
// re-initializes them back to original state.
// This will also remove all prescription list
// and hide any validation errors.
//
function resetForm()
{
    mainForm.trigger("reset");

    // Get all rows from prescription table
    var rows = $('.prescription-body > tr');

    if (rows.length > 0)
        clearPrescriptions();

    hideError(); 

    $(".past-medical-history .btn-expand").fadeOut('fast');
    $(".past-medical-history").fadeOut('fast');
    $(".patient-type-lbl-wrapper").fadeOut('fast');
    $(".physician").focus().blur();

    $(".prescription-wrapper").fadeOut('fast');
    $(".btn-clear-prescription").fadeOut('fast');
}

function validateFields()
{ 
    var patientKey = $(".selected-patient-key").val();
    var patientName = $(".patient-name").val();
    var patientId = $(".patient-idnum").val();
    var illnessId = $(".illness-id").val(); 
    var doctorKey = $(".doctor-key").val();

    // Patient Field
    if (System.isNullOrEmpty(patientKey) || System.isNullOrEmpty(patientName) || System.isNullOrEmpty(patientId))
    {
        showError("Please select a patient! Click on the \"FIND\" button to load patient information.");    
        return false;
    }

    // Doctor
    if (System.isNullOrEmpty(doctorKey) || System.isNullOrEmpty($(".physician").val()))
    {
        showError("Please select a physician! Click on the \"SELECT\" button to choose.");    
        return false;
    }

    // Illness Field
    if (System.isNullOrEmpty(illnessId))
    {
        showError("Please select an illness! Click on the \"Illness / Disease\" textbox to open the list of illnesses.");
        $(".input-illness").focus();
        return false;
    }

    // If Blood Pressure Field is incomplete, 
    // force the user to complete it with valid value
    var bpSystolic = $(".input-systolic").val();
    var bpDiastolic = $(".input-diastolic").val();

    if (!System.isNullOrEmpty(bpDiastolic) && System.isNullOrEmpty(bpSystolic))
    {
        showError("Please enter a value for Systolic blood pressure!");
        $(".input-systolic").focus();
        return false;
    }

    if (bpSystolic == '0')
    {
        showError("Systolic blood pressure is invalid!");
        $(".input-systolic").focus();
        return false;
    }

    if (!System.isNullOrEmpty(bpSystolic) && System.isNullOrEmpty(bpDiastolic))
    {
        showError("Please enter a value for Diastolic blood pressure!");
        $(".input-diastolic").focus();
        return false;
    }

    if (bpDiastolic == '0')
    {
        showError("Diastolic blood pressure is invalid!");
        $(".input-diastolic").focus();
        return false;
    }

    return true;
}
 