var dialog = undefined;
var snackbar = undefined;
var confirm = undefined;
var dataTable = undefined;

$(document).ready(function () {
    onAwake();    
});

function onAwake() 
{
    dialog = new AlertDialog();  
    snackbar = new SnackBar();
    confirm = new ConfirmDialog();

    $(function () 
    {
        $(".combo-box").selectmenu({
            width: 200
        });
    });
    
    setupDatatable();

    // recreate the entries dropdown filter
    recreateEntriesFilter();

    bindTotalsCount();
    
    notify();
    onBind();
}

function onBind() 
{  
    $(document).on('click', '.dataset-body .btn-details',function (e) 
    { 
        var tr = $(this).closest('tr');
        showDetails(tr);
    })
    .on('click', '.dataset-body .tr-action-edit',function (e) 
    { 
        var tr = $(this).closest('tr');
        editRecord(tr);
    })
    .on('click', '.dataset-body .tr-action-delete',function (e) 
    { 
        var tr = $(this).closest('tr');
        deleteRecord(tr);
    });

    // The checbox on column header which sets all
    // checkbox per rows as checked
    $("#column-check-all").on('change', function()
    {
        var checked = $(this).prop('checked');
        checkAllRows(checked);
    });

    // The dropdown option of 'Delete Selected' rows was clicked
    $(".option-delete-selected").click(() => deleteAllRows()); 

    $(".btn-find").click(() => searchRecord()); 
    $(".btn-clear-search").click(() => clearSearch()); 

    bindRowCheckOption();
}

function showDetails(tr) 
{  
    var recordKey = tr.find('.record-key').val();

    if (System.isNullOrEmpty(recordKey))
    {
        dialog.danger("This action can't be completed. Please reload the page and try again.");
        return;
    }
    
    $(".frm-details #details-key").val(recordKey);
    $(".frm-details").trigger("submit");
}

function editRecord(tr) 
{  
    var recordKey = tr.find('.record-key').val();

    if (System.isNullOrEmpty(recordKey))
    {
        dialog.danger("This action can't be completed. Please reload the page and try again.");
        return;
    }
    
    $(".frm-edit #item-key").val(recordKey);
    $(".frm-edit").trigger("submit");
}

function deleteRecord(tr) 
{   
    var recordKey = tr.find('.record-key').val();
    
    if (System.isNullOrEmpty(recordKey))
    {
        dialog.danger("This action can't be completed. Please reload the page and try again.");
        return;
    }
    
    confirm.actionOnOK = function()
    {   
        $(".frm-delete #delete-key").val(recordKey);
        $(".frm-delete").trigger("submit");
    };

    var name = tr.find(".td-item-name").text().trim();

    var msg = 
    `<div class="text-wrap d-flex flex-column">
        <div>
            Are you sure, you want to remove "<span class="fw-bold fst-italic">${name}</span>" from the inventory?
        </div>
        <div class="d-flex align-items-center mt-2 p-2 rounded-2 fsz-14 bg-amber-light" style="color: #73510D;">
            <i class="fas fa-info-circle me-2"></i>
            <span>This item will no longer show up on prescription records.</span>
        </div>
        <div class="mt-2">Do you wish to proceed?</div>
    </div>
    `;

    confirm.show(msg, "Delete Inventory Item", "Yes", "No", true);
}

function notify() 
{  
    var success = $(".success-message").val().trim();

    if (!System.isNullOrEmpty(success))
    {
        snackbar.show(success);
    }

    var error = $(".error-message").val().trim();

    if (!System.isNullOrEmpty(error))
    {
        dialog.danger(error);
    }
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

        if (isChecked) 
        {
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
  
    confirm.actionOnOK = function () 
    {  
        // encode form data as JSON  
        $(".frm-delete-records #record-keys").val(JSON.stringify(keys));
        $(".frm-delete-records").trigger("submit");
    };

    // Uncheck all checked rows on cancel
    confirm.actionOnCancel = function () 
    {  
        $(".form-check-input").prop("checked", false).change();
    };
 
    var msg = 
    `<div class="text-wrap d-flex flex-column">
        <div>
            Are you sure you want to remove the selected items?
        </div>
        <div class="d-flex align-items-center mt-2 p-2 rounded-2 fsz-14 bg-amber-light" style="color: #73510D;">
            <i class="fas fa-info-circle me-2"></i>
            <span>These items will no longer show up on prescription records.</span>
        </div>
        <div class="mt-2">Do you wish to proceed?</div>
    </div>
    `;

    confirm.show(msg, "Delete Inventory Items", "Yes", "No", true);
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
        case '1':    
            $("tfoot .search-col-sku").find(":input").val(keyword).keyup();
            break;
        case '0': 
            $("tfoot .search-col-name").find(":input").val(keyword).keyup();
            break;
    }

    // Show the search keyword in a badge
    $(".capsule-badge-search-keyword").text(keyword);
    $(".capsule-badge-search").css("display", "flex").show();

    // Show the clear button whenever there is a search performed
    $(".btn-clear-search").show();
}
  
function selectCategory(key)
{
    // alert(key);
    $(".filter-form #filter").val('catg');
    $(".filter-form #query").val(key);
    $(".filter-form").trigger('submit');
}

function setfilteraction(action)
{ 
    switch (action)
    {
        case "s0":
            $(".filter-form #filter").val('s0');
            break;
        case "s1":
            $(".filter-form #filter").val('s1');
            break;
        case "x":
            $(".filter-form #filter").val('x');
            break;
        case "*":
            $(".filter-form #filter").val('');
            break;
    }
 
    $(".filter-form").trigger("submit");
}

function bindTotalsCount()
{
    var counter = $(".totals-counter").val();
    
    if (System.isNullOrEmpty(counter))
        return;
    
    var json = JSON.parse(counter);
    console.log(json);

    $(".total-critical").text(json.totalCritical);
    $(".total-soldout").text(json.totalSoldout);
    $(".total-expired").text(json.totalExpired);
}