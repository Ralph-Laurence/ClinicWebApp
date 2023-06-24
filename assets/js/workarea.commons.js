//
// Tick all row checkboxes when the column header's
// checkbox was checked
//
function checkAllRows(checkAll = true)
{
    var table = $(".dataset-table");
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
// Clear the datatable after searching.
// Also clear the searchfields
//
function clearSearch()
{
    // clear main searchbar
    $(".searchbar").val('').focus().blur();

    // clear hidden footer fields
    clearColSearchFields();

    // reset dropdown option
    $(function()
    {
        $(".combo-box").val('0');
        $(".combo-box").selectmenu('refresh');
    }); 

    // hide search term badges
    $(".capsule-badge-search").css("display", "none").hide();
    $(".btn-clear-search").hide();
}
//
// Clear hidden footer fields
//
function clearColSearchFields()
{
    $('tfoot .tfoot-col-search').each(function()
    {
        $(this).val('').keyup();
    });
}
//
// recreate the entries dropdown filter
//
function recreateEntriesFilter() 
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
// Sort specific column given by its index and order mode.
// colIndex     => the zero-based index of column.
// sortMode     => 1 = asc, -1 = desc
// highlightCol => sometimes, there is a hidden column that 
// needs to be sorted... we will instead, use this 'highlightCol' 
// to highlight the visible column while sorting the actual data in
// the hidden column. By default, 'colIndex' is used
function sortBy(colIndex, sortMode = 1, highlightCol = undefined)
{
    var sort = 'asc';
    var sortIcon = "assets/images/icons/sort-up.png";

    if (sortMode == -1)
    {
        sort = 'desc';
        sortIcon = "assets/images/icons/sort-down.png";
    }

    if (dataTable != null || dataTable != undefined)
    {
        dataTable.order( [colIndex, sort] ).draw();
    }
 
    // Highlight the column header of the sorted row
    $(".dataset-table").find('thead tr').each(function (i, el) 
    {
        var $th = $(this).find('th');

        if (highlightCol != undefined)
            colIndex = highlightCol;

        $th.each(function (index, header)
        {
            if (colIndex == index)
            {
                $th.eq(index).addClass("thead-th-active");
                $th.eq(index).find("div img").attr("src", sortIcon).show();
            }
            else
            {
                $th.eq(index).removeClass("thead-th-active");
                $th.eq(index).find("div img").attr("src", '').hide();
            }
        }); 
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
// Current scroll position of div container
//
function getScrollPosition()
{
    return $('.datatable-wrapper .simplebar-content-wrapper').scrollTop();
}
//
// highlight a specific row in specific page
//
function highlightRow(rowIndex, pageIndex)
{ 
    return;
    // row index and page index must be a valid value
    var row = System.TryParseInt(rowIndex, -1);
    var page = System.TryParseInt(pageIndex, -1);
 
    if (row < 0 || page < 0)
        return;

    // Scroll to target page
    scrollPage(page);

    // Emphasize the target row by adding a color to it
    var targetRow = $(".dataset-table .dataset-body").find("tr:eq(" + row + ")");
    
    // Go to the updated row
    $(targetRow)[0].scrollIntoView({ behavior: 'auto' /*or smooth*/, block: 'center' });

    // Add emphasize color
    targetRow.addClass("emphasize");
    
    // Remove emphasis after 4secs
    setTimeout(function()
    {
        $(targetRow).removeClass("emphasize");
    }, 4000); 
}

var optionMenuClosed = false;

function bindRowCheckOption()
{ 
    $(".dropdown-toggle").click(function()
    {
        if (!$(".actionbar-options > .dropdown-menu").is(":visible"))
            uncheckAllRows();
    });

    $(".li-close-options-menu").click(function()
    {
        optionMenuClosed = true; 
        uncheckAllRows();
    });
    
    $(".datatable-wrapper input[type=checkbox]").change(function()
    { 
        if (!$(this).is(":checked") && !$(".actionbar-options > .dropdown-menu").is(":visible"))
        {
            uncheckAllRows(); 
        } 
    });

    //var table = $(".dataset-table").DataTable();

    dataTable.on('page.dt', function () 
    {
        var pageInfo = dataTable.page.info();
        var page = pageInfo.page;
 
        if ($(".actionbar-options > .dropdown-menu").is(":visible"))
            //$(".row-check-parent").fadeIn('fast');
        {
            dataTable.page(page).draw('page');

            dataTable.rows({page: 'current'}).every(function () 
            {
                var node = this.node();
                $(".row-check-parent", node).show(); 
            });
        }
        else
        {
            dataTable.page(page).draw('page');

            dataTable.rows({page: 'current'}).every(function () 
            {
                var node = this.node();
                $("#row-check-box").prop("checked", false);
                $(".row-check-parent", node).hide(); 
            });
        }
    });

    // Show or hide the row checkbox when OPTIONS button was clicked
    $('.actionbar-options #options-dropdown-button')
    .on('shown.bs.dropdown', function() 
    {
        optionMenuClosed = false;

        $("#sort-dropdown-button, #summarize-dropdown-button").prop("disabled", true);
        $(".row-check-parent").fadeIn('fast');

    })
    .on('hidden.bs.dropdown', function()
    {
        $("#sort-dropdown-button, #summarize-dropdown-button").prop("disabled", false);

        if (countCheckedRows() > 0 && !optionMenuClosed)
            return false;
 
        uncheckAllRows();
    });
}

function countCheckedRows()
{  
    var table = $(".dataset-table").DataTable();
    var count = 0;

    table.rows().every(function () 
    {
        var rowNode = this.node();
        var $checkbox = $('#row-check-box', rowNode);

        if ($checkbox.length) 
        {
            var isChecked = $checkbox.prop('checked');
            console.log(isChecked);
            
            if (isChecked)
                count++;
        }
    });

    return count;
}

function uncheckAllRows()
{ 
    dataTable.rows().every(function () 
    {
        var node = this.node();
        $("#row-check-box", node).prop("checked", false);
        $(".row-check-parent", node).fadeOut('fast');  
    });

    $("#column-check-all, #row-check-box:checked").prop("checked", false);
    $(".row-check-parent").fadeOut('fast');  
}