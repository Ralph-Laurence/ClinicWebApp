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

$(document).ready(() => onAwake());

function onAwake()
{
    dialog = new AlertDialog();
    confirm = new ConfirmDialog();
    snackbar = new SnackBar();

    dataTable = $('.stocks-table').DataTable({
        searching: false,
        ordering:  false
    });

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
            findItemsOption($(this).val());
        }
    });

    $("#category-options").selectmenu();
   
    // bind event handlers
    onBind();
}

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
}

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
        $("#category-options").selectmenu( "option", "disabled", false );
        disableInputKeyword = true;
    }
    else
    {
        $("#category-options").selectmenu( "option", "disabled", true );
    }

    // other filter without the need of keywords
    if (blackList.includes(selected))
        disableInputKeyword = true;

    if (disableInputKeyword)
        inputKeyword.prop('disabled', true);
    else
        inputKeyword.prop('disabled', false);
}

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

function deleteAllRows()
{
    var table = $(".stocks-table");
    var rows = table.find("tbody tr");

    // we count all checked rows .. if no row has been checked, exit
    var checkedRowsCount = 0;

    // find all checked rows
    rows.each(function(i, row)
    {
        var checkboxRow = $(rows[i]).find("#row-check-box");
        var isRowChecked = checkboxRow.prop("checked");
 
        if (isRowChecked)
            checkedRowsCount++;
        // if (checkAll)
        //     $(checkboxColumn).prop('checked', true);
        // else
        //     $(checkboxColumn).prop('checked', false);
    });

    if (checkedRowsCount == 0)
    {
        dialog.warn("Please select a record to delete by ticking each checkbox.");
        return;
    }

    confirm.show("Do you really want to delete all selected records?\n\nThis action cannot be undone. Please proceed with caution.");
        
    confirm.actionOnOK = function()
    {
        
    };  
}


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
// Launch the page for editing the item
//
function editItem(itemKey)
{
    var inputItemKey = $("#input-item-key").val(itemKey);

    if (inputItemKey == undefined || inputItemKey == "")
        return;

    $(".frm-edit-item").trigger("submit");
}
// 
// Highlight the recently updated row
//
function highlightUpdatedRow()
{
    var rows = $('.stocks-table tr');
 
    if (rows.length > 0)
    { 
        // Highlight the row
        //
        var tbody = $('.stocks-table').find("tbody tr");
        $(tbody[0]).css("background-color", "#D6F0E0");  
    }
} 