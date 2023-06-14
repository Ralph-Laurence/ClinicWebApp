var dialog = undefined;
var confirm = undefined;
var toast = undefined;

var medicineDatatable = undefined;

var errorMessageWrapper = undefined;
var errorMessageLabel = undefined;

var mainForm = undefined;

var updateValues = {};

$(document).ready(function(){
    onAwake();
});

function onAwake()
{
    dialog = new AlertDialog();
    confirm = new ConfirmDialog();
    toast = new Toast();
  
    errorMessageWrapper = $(".error-message");
    errorMessageLabel = $(".error-label");

    mainForm = $("#checkup-form");

    hideEnqueuedMedicines();
 
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
        // The parent TR of this 'remove' button
        var trSelf = $(this).closest("tr");

        dequeuePrescription(trSelf, $(this));
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
        var unitsKey = tr.find(".units-key").text().trim();
        var itemCode = tr.attr("id").trim();

        var medicineData = 
        {
            name: medicineName,
            category: category,
            stock: stock,
            maxQty: maxQty,
            unitsKey: unitsKey,
            itemKey: itemKey,
            itemCode, itemCode
        };
 
        selectMedicine(medicineData);
    })
    //
    // Medicine picker Modal -> Restore Medicine button
    //
    .on("click", ".medicine-picker-body .btn-restore-medicine", function()
    {
        var tr = $(this).closest("tr");
 
        var itemCode = tr.attr("id").trim();
        
        var medicineData = 
        {
            itemCode, itemCode
        };
 
        restoreMedicine(medicineData);
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
  
    // Clear all prescriptions on 'Clear' button click
    $(".btn-clear-prescription").click(() => 
    {
         
        return;
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
    $(".btn-update").click(function()
    {  
        handleFormSubmit(); 
    });

    // Reset the form and remove prescriptions
    $(".btn-cancel").click(() => 
    {
        confirm.actionOnOK = function () 
        {
            navHref($(".go-back").val());
        };

        var message = "Do you wish to abort the operation?\n\n\u25CF Your changes won't be saved when you leave.";

        confirm.show(message, "Edit Checkup Record", "Yes", "No");
    });

    // Force all numeric fields to accept only numbers
    Input.forceNumeric("numeric");

    mapOriginalMedx();
}
// Stock Amount is the actual amount
// Stock is the descriptive text
function selectMedicine(data)
{   
    // Build the row
    var row = 
    `<tr class=\"align-middle\">
        <td>${data.name}</td>
        <td>${data.category}</td>
        <td>${data.stock}</td>
        <td class="text-center">
            <div class="d-flex flex-row align-items-center justify-content-center gap-2">
                <button type="button" class="btn btn-warning bg-accent py-1 px-2 btn-qty-minus">
                    <i class="fas fa-minus"></i>
                </button>
                <div>
                    <input type="text" class="text-center prescription-qty" data-min-qty="1" value="1" data-max-qty="${data.maxQty}"/>
                </div>
                <button type="button" class="btn btn-primary bg-teal py-1 px-2 btn-qty-plus">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-secondary p-1 btn-remove" data-action="dequeue" data-dequeue="${data.itemCode}">
                <i class="fas fa-times me-1"></i>
                <span>Remove</span>
            </button>
        </td> 
        <td class=\"d-none\">${data.unitsKey}</td>
        <td class=\"d-none\">
            <input type=\"text\" class=\"item-key\" value=\"${data.itemKey}\" />
        </td>
    </tr>`;

    // Insert (append) the medicine into the prescription table
    $(".prescription-body").append(row);

    // Hide the selected Row from picker
    $(".medicine-picker-body").find(`tr#${data.itemCode}`).hide();

    // Scroll to the bottom after we append a new medicine
    // https://github.com/Grsmto/simplebar/issues/184#issuecomment-531666659
    // Thanks kgkg !
    // var container = document.querySelector('#workarea .simplebar-content-wrapper'); 
    var container = document.querySelector('#main-workarea .simplebar-content-wrapper'); 
    container.scrollTo({ top: 500, behavior: "smooth" }); 
}

function restoreMedicine(data)
{ 
    // The target prescription item (tr).
    var target = $(`.prescription-table [data-tag="${data.itemCode}"]`);

    // The original amount
    var restoreAmount = target.find(".orig-qty").val();

    if (System.isNullOrEmpty(restoreAmount))
    {
        dialog.danger("This action can't be completed because of an error. Please reload the page and try again.");
        return;
    }

    // Apply the restore amount then show the restored row again
    target.find(".prescription-qty").val(restoreAmount);

    // Disable the return-to-inventory flag
    target.find(".flag-return").val('0');

    // Disable the remove-prescription-item flag
    target.find(".flag-remove").val('0'); 

    target.show();

    // Hide the selected Row from the medicine picker
    $(".medicine-picker-body").find(`tr#${data.itemCode}`).hide(); 
}
//
// Each of the medicines which are loaded from prescriptions
// will be called 'original'. We need to hide those original 
// medicines from medicine picker.
//
function mapOriginalMedx()
{
    var originals = $(".prescription-table").find("[data-trait='original']");

    // For all original medicines, show RESTORE button instead of SELECT
    if (originals.length > 0)
    {
        var html = 
        `<div class="bg-light-indigo p-2 fsz-14 rounded-2 mb-3">
            As you remove a prescription item, you can see them again here. 
            To restore an item, click on the <span class="bg-base text-white p-1 rounded-2 user-select-none">Restore</span> button.
        </div>`;

        $("#selectMedicineModal .modal-body .table-wrapper").css("max-height", "400px");
        $("#selectMedicineModal .modal-body").prepend(html);
    }

    $(originals).each(function()
    {
        // Tag is the ITEM code, which is a class of <tr> in medicine picker's rows
        var tag = $(this).attr("data-tag");
        
        $(".medicine-picker-body").find(`tr#${tag}`).attr("data-trait", "original").hide();
    });
}
//
// Dequeue (Remove) medicine from prescription table
//
function dequeuePrescription(trSelf, sender)
{
    // Distinguish between original medicine and new medicine.
    // Every <tr> in prescription table is an original medicine.
    var isOriginal = trSelf.attr("data-trait") == "original";

    // The target row in Medicine picker is given by this tr's data-tag attribute.
    // By default, all original prescription rows have dequeue target.
    // We will use this to identify rows for removal. Only non-original rows
    // are removed from prescription table. Original rows are preserved (on hide)
    var dequeueTarget = trSelf.attr("data-tag");

    if (isOriginal) 
    { 
        // Replace the medicine picker's SELECT button with RESTORE button
        var restoreBtn =
            `<button type=\"button\" class=\"btn btn-secondary bg-base text-white py-1 px-2 btn-restore-medicine\" data-mdb-dismiss=\"modal\">
                Restore
            </button>`;

        var medxActionTd = $(`.medicine-picker-body tr#${dequeueTarget}`).find(`td:eq(5)`);

        medxActionTd.empty().html(restoreBtn);
  
        var medicineName = trSelf.find(".item-name").text().trim();
        var units = trSelf.find(".units-label").val();
        var qty = trSelf.find(".orig-qty").val();
        var stockLabel = `${qty} ${units}`;

        // Tell the server that this prescription will be removed
        trSelf.find(".flag-remove").val('1'); 

        // Confirm / Prompt user if he wants to return the item back
        msg = 
        `<div class="text-start">
            <div>
                You removed <span class="fon-fam-special font-teal">${medicineName}</span> with a quantity of 
                <span class="mx-1 font-base fon-fam-special"><span class="fs-5 me-1">&times;</span>${stockLabel}</span> 
                from the prescriptions list.
            </div>
            <div class="font-base d-block fw-bold mt-3 mb-4">
                Would you like to return the quantity back to inventory?
            </div>
            <div class="py-1 px-2 rounded-2 d-flex flex-row bg-document fsz-14 fst-italic">
                <div class="me-2">
                    <div class="d-flex gap-1 align-items-center">
                        <i class="fas fa-info-circle text-primary"></i> 
                        <strong>Note:</strong>
                    </div>
                </div>
                <div class="flex-fill px-2">
                    If you choose "Yes", make it sure to really return the item as this will affect the tracking of item count.
                </div>
            </div>
        </div>`;

        confirm.show(msg, "Return Quantity", "Yes", "No", true);
        confirm.actionOnOK = function()
        {
            trSelf.find(".flag-return").val('1'); 
        };

        $(`.medicine-picker-body tr#${dequeueTarget}`).show();

        trSelf.hide();
        return;
    }

    // Set the dequeue target to the non-original rows
    // data-dequeue-target 
    dequeueTarget = sender.attr("data-dequeue");
    $(`.medicine-picker-body tr#${dequeueTarget}`).show();
    trSelf.remove();
}

function handleFormSubmit()
{
    // Validate fields before submitting
    var allFieldsValid = validateFields();

    if (!allFieldsValid)
    {
        toast.show("Please fillout all fields with valid information!", "Submit Failed", toast.toastTypes.DANGER);
        return;
    }
    
    collectPrescriptions();

    // If no prescriptions have been used, just ignore
    var prescriptions = collectPrescriptions();
 
    if (!System.isNullOrEmpty(prescriptions))
        $(".prescriptions").val(prescriptions);

    mainForm.trigger("submit");
}


function notify()
{ 
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
    // Collect Prescriptions
    var prescriptionRows = $('.prescription-table .prescription-body tr');
  
    if (prescriptionRows.length <= 0)
        return;
 
    // We will store all prescription data here as objects
    var prescriptionObj = [];
 
    $(prescriptionRows).each(function () 
    {
        var itemKey = $(this).find('.item-key').val();
        var qty = $(this).find('.prescription-qty').val();
        var flagReturn = $(this).find('.flag-return').val();
        var flagRemove = $(this).find('.flag-remove').val();

        prescriptionObj.push({
            itemKey: itemKey,
            quantity: qty,
            flagReturn: flagReturn,
            flagRemove: flagRemove
        });
    }); 

    if (prescriptionObj.length > 0)
        return JSON.stringify(prescriptionObj);

    return '';
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

function validateFields()
{ 
    var recordKey = $(".record-key").val();
    var patientName = $(".patient-name").val();
    var patientId = $(".patient-idnum").val();

    if (System.isNullOrEmpty(recordKey) || System.isNullOrEmpty(patientName) || System.isNullOrEmpty(patientId))
    {
        showError("A problem has occurred during the validation of patient's information. Please reload the page and try again.");    
        return false;
    }

    // Illness Field
    var illnessId = $(".illness-id").val();

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

//
// Hide all medicines from the picker window
// when they were initially enqueued in prescriptions
//
function hideEnqueuedMedicines()
{
    // Get all rows from prescription table
    var rows = $('.prescription-body > tr');

    if (rows.length < 1)
        return '';

    // Iterate rows in prescription table
    $(rows).each(function(index, tr) 
    {
        // every <tr> in prescription table has an attribute
        // of [data-enqueued]. We must grab references to these
        // attributes. These attributes will be used to identify
        // in picker modal which medicine to hide.
        var el_toHide = $(this).attr("data-enqueued");
        
        // Hide <tr> rows with matching class which was given by
        // the attribute above
        $(`tr.${el_toHide}`).hide();
    });
}