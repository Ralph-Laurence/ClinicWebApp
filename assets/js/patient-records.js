var inputKeyword = undefined; 
var btnFind = undefined;

var dialog = undefined;
var confirm = undefined;
var snackbar = undefined;

var columnCheckbox = undefined;
var dataTable = undefined;

var sessionVar_RecordDeleted = undefined;
var sessionVar_RecordsDeleted = undefined;

var liOption_DeleteCheckedRows = undefined;
//
//==========================================================
// REGION: INITIALIZATION AND EVENT BINDINGS
//==========================================================
//
// Initialize objects after the DOM (Document Object Model)
// has fully loaded ... For readability purposes, the entire
// initialization logic / code is contained in onAwake()
//
$(document).ready(() => onAwake());
//
// All initialization code is contained here ..
// which is called by $(document).ready event above
//
function onAwake()
{ 
    dialog = new AlertDialog();
    snackbar = new SnackBar();
    confirm = new ConfirmDialog();

    inputKeyword = $("#input-keyword"); 
    btnFind = $(".btn-find");

    columnCheckbox = $("#column-check-all");

    sessionVar_RecordDeleted = $(".session-var-delete-record-status").val();
    sessionVar_RecordsDeleted = $(".session-var-delete-records-status").val();

    liOption_DeleteCheckedRows = $(".dropdown-option-delete-all-selected");

    dataTable = $(".patient-records-table")
    .DataTable(
    {
        searching: false,
        ordering:  false,
        autoWidth: false
    });

    // show snackbar after a successful edit/delete
    notify_OnEditDeleteSuccess();

    // recreate the entries dropdown filter
    createVirtualEntriesPaginator();

    $(function () 
    {
        $("#find-patient-option").selectmenu({
            width: 200,
            change: function(event, ui)
            {
                var selected = $(this).val();

                if (selected == "filter-month")
                {
                    $( "#month-options" ).selectmenu( "option", "disabled", false );
                    inputKeyword.prop("disabled", true); 
                }
                else 
                {
                    $( "#month-options" ).selectmenu( "option", "disabled", true );
                    inputKeyword.prop("disabled", false); 
                } 
            }
        });

        $("#month-options").selectmenu({
            width: 180
        });
    }); 

    onBind();
}
//
// After initialization, we can now bind (attach) events
// onto elements .. Again, for readability, we put all 
// logic / code of event bindings in onBind() function
//
function onBind()
{
    btnFind.click(() => searchRecord()); 

    // The checbox on column header which sets all
    // checkbox per rows as checked
    columnCheckbox.on('change', function()
    {
        var checked = $(this).prop('checked');
        checkAllRows(checked);
    });

    // The dropdown option of 'Delete Selected' rows was clicked
    liOption_DeleteCheckedRows.click(() => deleteAllRows());
} 
//
//==========================================================
// REGION: RECORD PAGINATION AND DATASET / TABLE OPERATIONS
//==========================================================
//
// Filter / find records with specific term
//
function searchRecord()
{
    var filter = $("#find-patient-option").val();

    if (filter != "filter-month" && inputKeyword.val() == "") 
    {
        dialog.warn("Please enter a search term.");
        return;
    }

    if (filter == "filter-month" && $("#month-options").val() == null)
    {
        dialog.warn("Please select a month.");
        return;
    }

    $(".filter-form").trigger("submit");
}
//
// recreate the entries dropdown filter
//
function createVirtualEntriesPaginator() 
{
    // hide the original entries paginator
    $(".dataTables_length").hide();
    $(".entries-paginator-container").empty();

    // copy the original entries paginator's options
    // to the virtual entries paginator
    var cloned = $(".dataTables_length").find('select').clone(true, true)
        .removeAttr("name")
        .removeAttr("class")
        .removeAttr("aria-controls")
        .attr("id", "virtual-entries-paginator")
        .hide();

    $(cloned[0]).appendTo(".entries-paginator-container");

    $("#virtual-entries-paginator").selectmenu({
        width: 90,
        change: function (event, ui) {
            $($(".dataTables_length").find("select")).val(ui.item.value).change();
        }
    });
}
//
// Tick all row checkboxes when the column header's
// checkbox was checked
//
function checkAllRows(checkAll = true)
{
    var table = $(".patient-records-table");
    var rows = table.find("tbody tr");

    rows.each(function(i, row)
    {
        var checkboxColumn = $(rows[i]).find("#row-check-box");

        if (checkAll)
            $(checkboxColumn).prop('checked', true);
        else
            $(checkboxColumn).prop('checked', false);
    });
}
//
//==========================================================
// REGION: C.R.U.D. (CREATE, READ, UPDATE, DELETE)
//==========================================================
//
// View details and information about the 
// selected checkup record
//
function loadCheckupDetails(checkupRecordKey, txn)
{
    if (System.isNullOrEmpty(checkupRecordKey))
    {
        dialog.danger("Can't preview checkup details. Please reload the page and try again." +
        " If this error persists, please contact the administrator.");
        return;
    }

    $("#details").val(checkupRecordKey);
    $("#txn").val(txn);
    $(".checkup_details_form").trigger("submit");
}
//
// Delete all checked rows
//
function deleteAllRows()
{
    var table = $(".patient-records-table");
    var rows = table.find("tbody tr");

    // we count all checked rows .. if no row has been checked, exit
    var checkedRowsCount = 0;

    // store the item keys here
    var formData = {};
  
    // find all checked rows
    rows.each(function(i, row)
    {
        var checkboxRow = $(rows[i]).find("#row-check-box");
        var isRowChecked = checkboxRow.prop("checked");

        if (isRowChecked)
        {
            checkedRowsCount++;
            
            // form data index syntax: recordN
            var recordKey = $(rows[i].cells[7]).text();

            formData[`record${i}`] = recordKey;
        } 
    });

    // encode form data as JSON
    var formData_Encoded = JSON.stringify(formData);

    $(".frm-delete-records #record-keys").val(formData_Encoded);

    if (checkedRowsCount == 0)
    {
        dialog.warn("Please select a record to delete by ticking each checkbox.");
        return;
    }

    confirm.show("Do you really want to delete all selected records?\n\nThis action cannot be undone. Please proceed with caution.");
        
    confirm.actionOnOK = function()
    {
        //alert($(".frm-delete-records #record-keys").val());
        $(".frm-delete-records").trigger("submit");
    };  
}
//
// Delete a single record
//
function deleteRecord(recordKey, formNumber)
{
    var inputRecordKey = $(".frm-delete-record #record-key").val(recordKey);

    if (inputRecordKey == undefined || inputRecordKey == "")
        return; 

    confirm.show(`The checkup record with number "${formNumber}" will be removed permanently. This action cannot be undone.\n\n Do you wish to continue?`);
        
    confirm.actionOnOK = function()
    {
        $(".frm-delete-record").trigger("submit");
    };   
}
//
//========================================================
// REGION: VISUAL FEEDBACKS & USER INTERACTION / ATTENTION
//========================================================
//
// Tell the user that the edit and/or delete operation has 
// completed successfully.
//
function notify_OnEditDeleteSuccess()
{
    // if (!System.isNullOrEmpty(sessionVar_ItemName))
    // {
    //     highlightUpdatedRow(sessionVar_ItemName, sessionVar_ItemPage);
    //     return;
    // }

    if (!System.isNullOrEmpty(sessionVar_RecordDeleted))
    { 
        snackbar.show("A record was successfully removed.");
        return;
    }
    else if (!System.isNullOrEmpty(sessionVar_RecordsDeleted))
    { 
        snackbar.show("Records were successfully removed.");
    }
}