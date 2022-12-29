var noteActionClickable = undefined;
var noteAction = undefined;
var noteActionIcon = undefined;
var noteGuidelines = undefined;

var inputItemKey = undefined;
var inputItemName = undefined;
var inputItemCode = undefined;
var inputReserve = undefined;

var inputCategory = undefined;
var inputUnits = undefined;
var inputSupplier = undefined;
var inputRemarks = undefined;

var dropdownCategory = undefined;
var dropdownUnits = undefined;
var dropdownSupplier = undefined;

var dialog = undefined;
var confirm = undefined;
var snackbar = undefined;

var saveButton = undefined;
var editButton = undefined;
var cancelButton = undefined;
var btnCancelAll = undefined;

$(document).ready(() => onAwake());

function onAwake()
{  
    dialog = new AlertDialog();
    confirm = new ConfirmDialog();
    snackbar = new SnackBar();

    noteActionClickable = $(".note-warning .note-action-clickable");
    noteAction = $(".note-warning .note-action");
    noteActionIcon = $(".note-warning .note-action-icon");
    noteGuidelines = $(".note-guidelines");

    inputItemKey = $(".input-item-key");
    inputItemName = $(".input-item-name");
    inputItemCode = $(".input-item-code");
    inputReserve = $(".input-reserve-stock");

    inputCategory = $(".input-category");
    inputUnits = $(".input-units");
    inputSupplier = $(".input-supplier");
    inputRemarks = $(".input-remarks");

    dropdownCategory = $("#dropdownCategories");
    dropdownUnits = $("#dropdownUnits");
    dropdownSupplier = $("#dropdownSupplier");

    cancelButton = $(".btn-cancel");
    saveButton = $(".btn-save");
    editButton = $(".btn-edit");
    btnCancelAll = $(".btn-cancel-all");

    var expOnlySpecificChars = /[^A-Za-z0-9.\s\-\(\)\/]/gi;

    Input.forceNumeric(System.getClass(inputReserve));
    Input.whiteList(System.getClass(inputItemName), expOnlySpecificChars);
    Input.whiteList(System.getClass(inputItemCode), expOnlySpecificChars);
    Input.whiteList(System.getClass(inputRemarks), expOnlySpecificChars);

    onBind();
}

function onBind()
{    
    // the collapse/expand button for NOTE warning
    noteActionClickable.click(() => 
    {
        var action = noteAction.text();

        if (action == "collapse")
        {
            noteAction.text("expand");
            noteActionIcon.removeClass("fa-chevron-up").addClass("fa-chevron-down");
            noteGuidelines.hide();
            
        }
        else if (action == "expand")
        {
            noteAction.text("collapse");
            noteActionIcon.removeClass("fa-chevron-down").addClass("fa-chevron-up");
            noteGuidelines.show();
        }
        else
        {
            return;
        }
    });

    // prompt the user before cancelling the form
    cancelButton.click(() => cancelForm());

    // the save button has been clicked .. We must
    // validate the form before submitting
    saveButton.click(() => 
    {
        var valid = validateForm();

        if (valid) {
            updateItem();
        }
    });

    // show the save and cancel buttons back 
    // when the Edit button was clicked
    editButton.click(() => 
    {
        $(".btn-done, .btn-edit").hide();
        $(".btn-save, .btn-cancel").show();
    });
}

function setCategoryValue(value, displayText)
{
    dropdownCategory.text(displayText.trim());
    inputCategory.val(value.trim());
}

function setSupplierValue(value, displayText)
{
    dropdownSupplier.text(displayText.trim());
    inputSupplier.val(value.trim());
}

function setUnitsValue(value, displayText)
{
    dropdownUnits.text(displayText.trim());
    inputUnits.val(value.trim());
}

function validateForm()
{
    // check item name
    if (System.isNullOrEmpty(inputItemName.val()))
    {
        dialog.warn("Please enter a valid item name!");
        return false;
    }
    // check item code
    if (System.isNullOrEmpty(inputItemCode.val()))
    {
        dialog.warn("Please enter a valid item code!");
        return false;
    }
    // check category
    if (System.isNullOrEmpty(inputCategory.val()))
    {
        dialog.warn("Please select an item category!");
        return false;
    } 
    // check reserve stock
    if (System.isNullOrEmpty(inputReserve.val()))
    {
        dialog.warn("Reserve stock value is invalid! Value must be greater than 0.");
        return false;
    }
    // check unit measures
    if (System.isNullOrEmpty(inputUnits.val()))
    {
        dialog.warn("Please select an item's unit of measure!");
        return false;
    }
    // check supplier
    if (System.isNullOrEmpty(inputSupplier.val()))
    {
        dialog.warn("Please select a supplier. If an item does not have any supplier, select 'None' from the list.");
        return false;
    }

    return true;
}

function updateItem()
{
    var payload = 
    {
        itemKey:  inputItemKey.val(),
        itemName: inputItemName.val(),
        itemCode: inputItemCode.val(),
        category: inputCategory.val(), 
        reserveStock: inputReserve.val(),
        units: inputUnits.val(),
        supplier: inputSupplier.val(),
        remarks: inputRemarks.val()
    };

    $.ajax({
        url: "ajax/ajax.edit-item.php",
        type: "POST",
        data: {
            formData: JSON.stringify(payload)
        },
        success: function(responseCode)
        {
            switch(responseCode)
            {
                case "0x000":
                    snackbar.show("Item has been successfully updated");
                    onUpdateSuccess();
                    break;
                case "0x400":
                    dialog.danger("There were bad inputs supplied and as part of security measures, the server refused it. Please check your entries.");
                    break;
                case "0x500":
                    dialog.danger("There was a problem while the data is being processed by the server. If this error persists, please contact the administrator.");
                    break;
            } 
        },
        error: function(jqXHR, exception)
        { 
            switch(jqXHR.responseText)
            {
                case "0x501":
                    dialog.danger(`The item name "${payload.itemName}" already exists! Please change it to something unique.`, "Process Failed");
                    break;
                case "0x502":
                    dialog.danger(`The item code "${payload.itemCode}" already exists! Please change it to something unique.`, "Process Failed");
                    break;
                default:
                    dialog.danger("The item cannot be added because of an error.", "Process Failed");
                    break;
            } 
        }
    });
}
 
function cancelForm()
{
    confirm.actionOnOK = function()
    {
        btnCancelAll.click();
    };

    var message = "Your changes won't be saved if you cancel.\n\nDo you wish to abort the operation?";
    var title = "Attention";

    confirm.show(message, title, "Yes", "No");
}

function onUpdateSuccess()
{  
    $(".btn-done, .btn-edit").show();
    $(".btn-save, .btn-cancel").hide();
}