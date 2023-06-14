var inputKeyword = undefined; 

var dialog = undefined;
var confirm = undefined;
var snackbar = undefined;

var dataTable = undefined; 
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
    snackbar = new SnackBar();
    confirm = new ConfirmDialog();

    inputKeyword = $("#input-keyword"); 

    setupDatatable();
    
    // show snackbar after a successful action
    notify();

    // recreate the entries dropdown filter
    recreateEntriesFilter();

    $(function () 
    {
        $(".combo-box").selectmenu({
            width: 200
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
    $(".btn-find").click(() => searchRecord()); 
    $(".btn-clear-search").click(() => clearSearch()); 

    // The checbox on column header which sets all
    // checkbox per rows as checked
    $("#column-check-all").on('change', function()
    {
        var checked = $(this).prop('checked');
        checkAllRows(checked);
    });

    // The dropdown option of 'Delete Selected' rows was clicked
    $(".option-delete-selected").click(() => deleteAllRows()); 

    bindRowCheckOption();
} 
//
//==========================================================
// REGION: RECORD PAGINATION AND DATASET / TABLE OPERATIONS
//==========================================================
// Create an instance of Datatable then
// Add search functionality to it.
// https://datatables.net/examples/api/multi_filter.html
//
function setupDatatable()
{ 
    jQuery.fn.dataTableExt.pager.numbers_length = 5;
    
    // Add search functionality foreach column
    $('.dataset-table tfoot th').each(function () 
    { 
        $(this).html(`<input class="tfoot-col-search" type="text" />`);
    });
    
    // Create the datatable
    dataTable = $(".dataset-table").DataTable(
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

    // Track click events on this table
    $(".dataset-table .dataset-body")
    .on("click", "tr .tr-action-edit", function()
    {
        var tr = $(this).closest("tr");
        var rowIndex = dataTable.rows({ page: 'current' }).row( tr.index() ).index();
        var patientKey = tr.find('.record-key').val().trim();
         
        editPatient(patientKey, rowIndex);
    })
    .on("click", "tr .tr-action-delete", function()
    {
        var tr = $(this).closest("tr");
        
        var patientKey = tr.find('.record-key').val().trim();
        var idNum = tr.find(".td-id-num").text().trim();
        
        deletePatient(patientKey, idNum);
    })
    .on("click", "tr .tr-action-details", function()
    {
        var tr = $(this).closest("tr");
    
        var patientKey = tr.find('.record-key').val().trim();     
        loadPatientDetails(patientKey);
    });

    // Find the original search bar of Datatable object then hide it
    $(".datatable-wrapper .dataTables_filter > label").hide();

    // Only show the sort button when there are rows present in the datatable
    if (dataTable.rows().count() > 0)
        $(".btn-sort").fadeIn();
}
//
// Filter / find records with specific term
//
function searchRecord()
{
    // Searchbox must not be empty to begin searching
    var keyword = $(".searchbar").val();

    if (System.isNullOrEmpty(keyword))
    {
        dialog.warn("Please enter a search keyword!"); 
        return;
    }

    // Everytime we search the table, we must clear recent searches first.
    clearColSearchFields();

    // Identify which filter to apply.
    var option = $(".combo-box").val();

    // Then Reflect the search terms in specific column filter inputboxes found in table's <tfoot>.
    // The original searchbar is hidden and it only triggers on keyup, so let's trigger it manually 
    // using keyup().

    switch (option)
    {
        // Patient Name
        case '0': 
             
            $("tfoot .search-col-patient-name").find(":input").val(keyword).keyup();
            break;

        // ID Number
        case '1': 
            
            $("tfoot .search-col-id-number").find(":input").val(keyword).keyup();
            break;
    }

    // Show the search keyword in a badge
    $(".capsule-badge-search-keyword").text(keyword);
    $(".capsule-badge-search").css("display", "flex").show();

    // Show the clear button whenever there is a search performed
    $(".btn-clear-search").show();
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
// function recreateEntriesFilter() 
// {
//     // hide the original entries paginator
//     $(".dataTables_length").hide();
//     $(".entries-paginator-container").empty();

//     // copy the original entries paginator's options
//     // to the virtual entries paginator
//     var cloned = $(".dataTables_length").find('select').clone(true, true)
//         .removeAttr("name")
//         .removeAttr("class")
//         .removeAttr("aria-controls")
//         .attr("id", "virtual-entries-filter")
//         .hide();

//     $(cloned[0]).appendTo(".entries-paginator-container");

//     $("#virtual-entries-filter").selectmenu({
//         width: 90,
//         change: function (event, ui) {
//             $($(".dataTables_length").find("select")).val(ui.item.value).change();
//         }
//     });
// }
//
// Tick all row checkboxes when the column header's
// checkbox was checked
//
// function checkAllRows(checkAll = true)
// {
//     var table = $(".dataset-table");
//     var rows = table.find("tbody tr");

//     rows.each(function(i, row)
//     {
//         var checkboxColumn = $(rows[i]).find("#row-check-box");

//         if (checkAll)
//             $(checkboxColumn).prop('checked', true);
//         else
//             $(checkboxColumn).prop('checked', false);
//     });
// }
//
// Sort specific column given by its index and order mode.
// colIndex = the zero-based index of column.
// sortMode => 1 = asc, -1 = desc
//
// function sortBy(colIndex, sortMode = 1)
// {
//     var sort = 'asc';
//     var sortIcon = "assets/images/icons/sort-up.png";

//     if (sortMode == -1)
//     {
//         sort = 'desc';
//         sortIcon = "assets/images/icons/sort-down.png";
//     }

//     if (dataTable != null || dataTable != undefined)
//     {
//         dataTable.order( [colIndex, sort] ).draw();
//     }
 
//     // Highlight the column header of the sorted row
//     $(".dataset-table").find('thead tr').each(function (i, el) 
//     {
//         var $th = $(this).find('th');

//         $th.each(function (index, header)
//         {
//             if (colIndex == index)
//             {
//                 $th.eq(index).addClass("thead-th-active");
//                 $th.eq(index).find("div img").attr("src", sortIcon).show();
//             }
//             else
//             {
//                 $th.eq(index).removeClass("thead-th-active");
//                 $th.eq(index).find("div img").attr("src", '').hide();
//             }
//         }); 
//     });
// }
//
//==========================================================
// REGION: C.R.U.D. (CREATE, READ, UPDATE, DELETE)
//==========================================================
//
// View details and information about the selected patient
//
function loadPatientDetails(recordKey)
{
    var inputKey = $(".frm-details #details-key").val(recordKey);

    if (System.isNullOrEmpty(inputKey))
    {
        dialog.danger("There was a problem while trying to read patient details. Please reload the page and try again.");
        return;
    }
    
    $(".frm-details").trigger("submit");
}
//
// Edit patient's information
//
function editPatient(patientKey, rowIndex)
{
    var inputKey = $(".frm-edit #edit-key").val(patientKey);
    $(".frm-edit #row-index").val(rowIndex);
    $(".frm-edit #page-index").val(getPaginationPage());
     
    if (System.isNullOrEmpty(inputKey))
    {
        dialog.danger("This action can't be completed. Please reload the page and try again.");
        return;
    } 

    $(".frm-edit").trigger("submit");
}
//
// Delete a patient
//
function deletePatient(recordKey, idNumber)
{
    var inputKey = $(".frm-delete #delete-key").val(recordKey);

    if (System.isNullOrEmpty(inputKey))
        return; 

    var confirmMessage = `The patient with ID Number "${idNumber}" will be removed permanently.\n\n` + 
    `\u25cf All records associated with this patient will be deleted too.\n\n Do you wish to continue?`;

    confirm.show(confirmMessage);
        
    confirm.actionOnOK = function()
    {
        $(".frm-delete").trigger("submit");
    };   
}
//
// Delete all checked rows
//
function deleteAllRows()
{ 
    // we count all checked rows then store them here
    var checkedRowsCount = 0;

    // store the record keys here
    var keys = [];

    // collect the keys of all checked rows 
    dataTable.rows().every(function (value, index) 
    {
        var tr = this.node();
        var isChecked = $(tr).find('#row-check-box').prop('checked');

        if (isChecked) {
            checkedRowsCount++;

            var key = $(tr).find('.record-key').val();
            if (key)
                keys.push(key);
        }
    });

    // Prompt user to select records
    if (checkedRowsCount == 0)
    {
        dialog.warn("Please select records to delete by checking each checkbox.");
        return;
    }

    // Exit if keys[] array is empty
    if (System.isObjectEmpty(keys))
    {
        dialog.danger("This action can't be completed. Please reload the page and try again.");
        return;
    }

    // encode form data as JSON  
    $(".frm-delete-records #record-keys").val(JSON.stringify(keys));

    confirm.actionOnOK = function()
    {  
        $(".frm-delete-records").trigger("submit");
    };  

    // Uncheck all checked rows on cancel
    confirm.actionOnCancel = function () 
    {  
        $(".form-check-input").prop("checked", false).change();
    };

    confirm.show("Do you really want to delete all selected patients?\n\n\u25CF This will also delete any related records for each patient.");

}
//
//========================================================
// REGION: VISUAL FEEDBACKS & USER INTERACTION 
//========================================================
//
function notify()
{
    var successMessage = $(".frm-session-var .success-message").val();

    if (!System.isNullOrEmpty(successMessage))
        snackbar.show(successMessage);
 
    // Highlight the row that was affected after edit
    var emphasisData = $(".edited-row-emphasis").val();
    
    $(".dataset-table").one('animationend', function()
    {
        //alert("done")
        if (!System.isNullOrEmpty(emphasisData)) {
            var jsonResult = JSON.parse(emphasisData);

            highlightRow(jsonResult.row, jsonResult.page);
        }

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