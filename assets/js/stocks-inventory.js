var columnCheckbox = undefined;
var inputKeyword = undefined;

var findButton = undefined;

var dialog = undefined;
var confirm = undefined;
var snackbar = undefined;

var confirm_BtnOK = undefined;
var confirm_BtnCancel = undefined;

var liOption_DeleteCheckedRows = undefined;

var itemDetailsModal = undefined;

var dataTable = undefined;

var sessionVar_ItemName = undefined;
var sessionVar_ItemPage = undefined;
var sessionVar_ItemDeleted = undefined;
var sessionVar_ItemsDeleted = undefined;
//
//==========================================================
// REGION: INITIALIZATION AND EVENT BINDINGS
//==========================================================
//
// Initialize objects after the DOM (Document Object Model)
// has fully loaded ... For readability purposes, the entire
// initialization logic / code is contained in onAwake()
//
$(document).ready(function(){
    onAwake();
});
//
// All initialization code is contained here ..
// which is called by $(document).ready event above
//
function onAwake()
{
    dialog = new AlertDialog();
    confirm = new ConfirmDialog();
    snackbar = new SnackBar();
 
    itemDetailsModal = new mdb.Modal($("#stockDetailsModal"));
    
    findButton = $(".btn-find");
    confirm_BtnOK = $(".confirm-btn-ok");
    confirm_BtnCancel = $(".confirm-btn-cancel");

    columnCheckbox = $("#column-check-all");
    inputKeyword = $("#input-keyword");

    liOption_DeleteCheckedRows = $(".dropdown-option-delete-all-selected");
 
    $("#find-item-option") 
    .selectmenu
    ({
        width: 160,
        change: function (event, ui) 
        {
            // filter searchterms 
            findItemsOption(ui.item.value);
        }
    });

    $("#category-options").selectmenu(); 

    // for highlighting the recently updated item
    sessionVar_ItemName = $(".session-var-item-name").val();
    sessionVar_ItemPage = $(".session-var-item-page").val();
    sessionVar_ItemDeleted = $(".session-var-delete-item-status").val();
    sessionVar_ItemsDeleted = $(".session-var-delete-items-status").val();

    // render the table and bind event 
    // after databinding has completed
    dataTable = $('.stocks-table')  
    .DataTable(
    {
        searching: false,
        ordering:  false,
        autoWidth: false
    });

    // recreate the entries dropdown filter
    createVirtualEntriesPaginator();

    // show snackbar after a successful edit/delete
    notify_OnEditDeleteSuccess();

    // bind event handlers
    onBind();
}
//
// After initialization, we can now bind (attach) events
// onto elements .. Again, for readability, we put all 
// logic / code of event bindings in onBind() function
//
function onBind()
{
    columnCheckbox.on('change', function()
    {
        var checked = $(this).prop('checked');
        checkAllRows(checked);
    });

    findButton.click(() => searchRecord());

    liOption_DeleteCheckedRows.click(() => deleteAllRows());

    // show selected item's details in a modal window
    bindShowItemInfo();

    // submit the filter form when a category is selected
    bindCategoryOptionsOnChange();
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
    var filter = $("#find-item-option").val();

    var whiteList =
    [
        "filter-category",
        "filter-critical-item",
        "filter-soldout-item",
        "filter-newest-item"
    ];

    if (!whiteList.includes(filter) && inputKeyword.val() == "") 
    {
        dialog.warn("Please enter a search term.");
        return;
    }

    if (filter == "filter-category" && $("#category-options").val() == null)
    {
        dialog.warn("Please select a category.");
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
        .attr("id", "virtual-entries-filter")
        .hide();

    $(cloned[0]).appendTo(".entries-paginator-container");

    $("#virtual-entries-filter").selectmenu({
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
    var table = $(".stocks-table");
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
// change the pagination's page location given by its 
// page index
//
function scrollPage(pageIndex)
{
    var index = parseInt(pageIndex);

    if (index < 0)
        return;

    dataTable.page(index).draw(false);
}
//
// get the index of the currently displayed pagination 
// table page
//
function getPaginationPage()
{ 
    var info = dataTable.page.info();
    return info.page;
} 
//
// Immediately apply the category filter when
// a dropdown option was selected
// 
function bindCategoryOptionsOnChange()
{
    $("#category-options")
    .selectmenu({
        change: function()
        {
            findButton.click()
        }
    });
}
//
// Find an item by specific filter
//
function findItemsOption(selected)
{
    var disableInputKeyword = false;

    var blackList =
    [
        "filter-category",
        "filter-critical-item",
        "filter-soldout-item",
        "filter-newest-item"
    ];

    // category filter
    if (selected == "filter-category")
    {
        $("#category-options")
        .selectmenu({
            change: function()
            {
                findButton.click()
            }
        })
        .selectmenu( "option", "disabled", false );
        disableInputKeyword = true;
    }
    else
    {
        $("#category-options").selectmenu( "option", "disabled", true );
    }

    // other filter without the need of keywords
    if (blackList.includes(selected))
    {
        disableInputKeyword = true;

        // immediately find the item
        if (selected != "filter-category")
            findButton.click();
    }

    if (disableInputKeyword)
        inputKeyword.prop('disabled', true);
    else
        inputKeyword.prop('disabled', false);
}
//
//==========================================================
// REGION: C.R.U.D. (CREATE, READ, UPDATE, DELETE)
//==========================================================
//
// Launch the page for editing the item
//
function editItem(itemKey)
{
    var inputItemKey = $(".frm-edit-item #item-key").val(itemKey);
    
    $(".frm-edit-item #item-page").val(getPaginationPage());

    if (inputItemKey == undefined || inputItemKey == "")
        return;

    $(".frm-edit-item").trigger("submit");
}
//
// Delete all checked rows
//
function deleteAllRows()
{
    var table = $(".stocks-table");
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
            
            // form data index syntax: itemN
            var itemKey = $(rows[i].cells[10]).text().trim();

            formData[`item${i}`] = itemKey;
        } 
    });

    // encode form data as JSON
    var formData_Encoded = JSON.stringify(formData);

    $(".frm-delete-items #item-keys").val(formData_Encoded);

    if (checkedRowsCount == 0)
    {
        dialog.warn("Please select records to delete by checking each checkbox.");
        return;
    }

    confirm.show("Do you really want to delete all selected items?\n\nThis action cannot be undone.");
        
    confirm.actionOnOK = function()
    {
        $(".frm-delete-items").trigger("submit");
    };  
}
//
// Delete a single record
//
function deleteItem(itemKey, itemName)
{
    var inputItemKey = $(".frm-delete-item #item-key").val(itemKey);

    if (inputItemKey == undefined || inputItemKey == "")
        return; 

    confirm.show(`Do you really want to remove "${itemName}" from the records?\n\nThis action cannot be undone.`);
        
    confirm.actionOnOK = function()
    {
        $(".frm-delete-item").trigger("submit");
    };   
}
//
// View details and information about the 
// selected item record
//
function bindShowItemInfo()
{
    $(document).on("click", ".stocks-table .btn-item-details", function()
    { 
        // reference to the selected row
        var currentRow = $(this).closest("tr");

        // row's icon cell
        var itemIcon = currentRow.find("td:eq(1)").html(); 
        var itemName = currentRow.find("td:eq(2)").text(); 
        var itemCode = currentRow.find("td:eq(3)").text(); 
        var category = currentRow.find("td:eq(4)").text(); 
        var stock = currentRow.find("td:eq(5)").text(); 
        var reserved = currentRow.find("td:eq(6)").text(); 
        var supplier = currentRow.find("td:eq(8)").text(); 
        var dateAdded = currentRow.find("td:eq(9)").text(); 
        var unitMeasure = currentRow.find("td:eq(11)").text(); 
        var stockStatus = currentRow.find("td:eq(12)").text();
        var remarks = currentRow.find("td:eq(13)").text();
 
        var createdOn = moment(dateAdded).format("dddd, MMM. DD, YYYY");
 
        $(".lbl-item-name").text(itemName);
        $(".lbl-category").text(category);
        $(".lbl-category-icon").empty().html(itemIcon);
        $(".section-item-information .lbl-item-code").text(itemCode);
        $(".section-item-information .lbl-unit-measure").text(unitMeasure);
        $(".section-item-information .lbl-supplier").text(supplier);
        $(".section-item-information .lbl-total-stock").text(stock);
        $(".section-item-information .lbl-reserve").text(reserved);
        $(".section-item-information .lbl-date-added").text(createdOn);
        $(".section-item-information .item-description").val(remarks);
        
        if (stockStatus == "critical")
        {
            $(".item-status-warning").empty().
            html(`<div class="alert alert-warning text-center"><i class="fas fa-exclamation-triangle me-2"></i>This item is Low on Stock!</div>`);
        }
        else if (stockStatus == "soldout")
        {
            $(".item-status-warning").empty()
            .html(`<div class="alert alert-danger text-center"><i class="fas fa-exclamation-triangle me-2"></i>This item is Out of Stock!</div>`);
        }
        else 
        {
            $(".item-status-warning").empty()
            .html(`<div class="alert alert-success text-center"><i class="fas fa-info-circle me-2"></i>This item is available</div>`);
        }

        itemDetailsModal.show();
    });
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
    if (!System.isNullOrEmpty(sessionVar_ItemName))
    {
        highlightUpdatedRow(sessionVar_ItemName, sessionVar_ItemPage);
        return;
    }

    if (!System.isNullOrEmpty(sessionVar_ItemDeleted))
    { 
        snackbar.show("An item was successfully removed from the records.");
        return;
    }
    else if (!System.isNullOrEmpty(sessionVar_ItemsDeleted))
    { 
        snackbar.show("Items were successfully removed.");
        return;
    }
}
// 
// Highlight the recently updated row / record in green 
// background and move it onto the top of the table
//
function highlightUpdatedRow(updatedName, itemPage)
{   
    // Access / get all the rows found in the stocks table
    var rows = $(".stocks-table > tbody > tr");

    // make sure that the datatable has rows in it.
    // otherwise, stop the execution
    if (rows.length < 1 || System.isNullOrEmpty(updatedName))
        return;

    var isItemFoundInPage = false;
    var scrollPageIndex = itemPage;

    while(!isItemFoundInPage)
    { 
        // scroll to the paginated page's index where the item is displayed
        scrollPage(scrollPageIndex);

        // process the highlighting of rows
        dataTable.rows({ page: 'current' }).every(function(rowIdx, tableLoop, rowLoop)
        {
            // reference to the current row
            var tr = this.data();

            // match the updated name and the cell name ...
            if (tr[2] == updatedName)
            {
                // reference to the row in each iteration
                var currentRow = $(".stocks-table > tbody > tr")[rowLoop];

                // create a copy of the row
                var clone = $(currentRow).clone(true, true);

                // remove the old row
                $(currentRow).remove();

                // highlight the row and move it onto the top
                clone
                    .css("background-color", "#D6F0E0")
                    .insertBefore($(".stocks-table > tbody tr:first"));

                isItemFoundInPage = true;

                // break out of the iterator
                return false;
            }
        });  

        // we need to keep scrolling until we find the updated row.
        // we do this by increasing the page index ..
        scrollPageIndex++;
    }
 
    // Notify the user that the update process succeeded
    snackbar.show("Item has been successfully updated.");
}