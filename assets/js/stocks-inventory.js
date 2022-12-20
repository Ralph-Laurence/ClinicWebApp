var columnCheckbox = undefined;
var inputKeyword = undefined;

var findButton = undefined;

var dialog = undefined;
var confirm = undefined;
var snackbar = undefined;

$(document).ready(() => onAwake());

function onAwake()
{
    dialog = new AlertDialog();
    confirm = new ConfirmDialog();
    snackbar = new SnackBar();
    
    findButton = $(".btn-find");

    columnCheckbox = $("#column-check-all");
    inputKeyword = $("#input-keyword");


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