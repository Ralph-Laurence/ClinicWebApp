var dataTable = undefined;
var dialog    = undefined; 
var snackbar  = undefined;
var confirm   = undefined; 

$(document).ready(() => onAwake());

function onAwake()
{
    dialog = new AlertDialog(); 
    snackbar = new SnackBar();
    confirm = new ConfirmDialog();
 
    $(function () 
    {  
        $(".combo-box").selectmenu({width: 180});
    });

    setupDatatable();
    recreateEntriesFilter();

    // The dropdown option of 'Delete Selected' rows was clicked
    $(".option-delete-selected").click(() => deleteAllRows()); 

    // The dropdown option of 'Delete Selected' rows was clicked
    $(".option-clear-waste").click(() => clearWaste()); 
    
    notify();
    onBind();
}

function onBind()
{
    // Track click events on this table
    $(".dataset-table .dataset-body").on("click", "tr .trash-btn", function()
    { 
        var tr = $(this).closest("tr");
        
        var itemKey  = tr.find(".item-key").val().trim();
        var itemName = tr.find(".td-item-name").text().trim();
        var icon     = tr.find(".item-icon").attr("src");
        var code     = tr.find(".td-item-code").text().trim();
        var category = tr.find(".td-category").text().trim();
        var reason   = tr.find(".td-reason").text().trim();
        var date     = tr.find(".td-date-disposed").text().trim();
        var amount   = tr.find(".td-dispose-amount").text().trim();

        var msg = 
        `<div class="mb-2 d-flex flex-row gap-3">
            <div class="preview p-2 rounded-2 border border-1 ms-0">
                <img src="${icon}" width="60" height="60" class="item-preview"/>
            </div>
            <div class="flex-fill">
                <h5 class="font-primary-dark text-wrap">${itemName}</h5>
                <div class="fsz-12 text-truncate text-muted text-uppercase">${code}</div>
                <div class="fsz-12 text-truncate fw-bold font-base text-uppercase">${category}</div>
            </div> 
        </div>
        <hr class="hr divider" />
        <div class="bg-document py-1 px-2 rounded-2 mb-3">
            <div class="d-flex align-items-center flex-row">
                <div>Reason for disposal:</div>
                <div class="flex-fill text-primary fsz-12 fw-bold text-uppercase text-end">${reason}</div>
            </div>
            <div class="d-flex align-items-center flex-row">
                <div>Date disposed:</div>
                <div class="flex-fill fsz-12 fw-bold text-uppercase text-end">${date}</div>
            </div>
            <div class="d-flex align-items-center flex-row">
                <div>Amount disposed:</div>
                <div class="flex-fill fsz-14 font-primary-dark fw-bold text-end">${amount}</div>
            </div>
        </div>
        <div class="">Are you sure you want to remove this record?</div>`;

        confirm.actionOnOK = function () 
        {  
            $(".frm-delete #delete-key").val(itemKey);

            if (System.isNullOrEmpty($(".frm-delete #delete-key").val()))
            {
                dialog.danger("This action can't be completed. Please reload the page and try again.");
                return;
            }

            $(".frm-delete").trigger("submit");
        };
        
        confirm.show(msg, "Delete Waste Record", "Yes", "No", true);
    });
  
    // The checbox on column header which sets all
    // checkbox per rows as checked
    $("#column-check-all").on('change', function()
    {
        var checked = $(this).prop('checked');
        checkAllRows(checked);
    });

    $(".btn-find").click(() => searchRecord()); 
    $(".btn-clear-search").click(() => clearSearch()); 

    bindRowCheckOption();
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
        case '0': 
            $("tfoot .search-col-name").find(":input").val(keyword).keyup();
            break;
        case '1':    
            $("tfoot .search-col-sku").find(":input").val(keyword).keyup();
            break;
    }

    // Show the search keyword in a badge
    $(".capsule-badge-search-keyword").text(keyword);
    $(".capsule-badge-search").css("display", "flex").show();

    // Show the clear button whenever there is a search performed
    $(".btn-clear-search").show();
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
        //"order": [[1, 'asc']], // default sort mode
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

function notify()
{
    $msg = $(".server-response").val().trim();

    if (!System.isNullOrEmpty($msg))
        snackbar.show($msg);

    $err = $(".server-err-response").val().trim();

    if (!System.isNullOrEmpty($err))
        dialog.danger($err);
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

            var key = $(tr).find('.item-key').val();
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
    confirm.actionOnCancel = function () {
        $(".form-check-input").prop("checked", false).change();
    };
   
    confirm.show("Are you sure you want to remove the selected records?", "Delete Waste Records", "Yes", "No");
}

function clearWaste() 
{  
    confirm.actionOnOK = function () 
    {   
        $(".frm-clear").trigger("submit");
    };
   
    var msg = 
    `<div class="mb-2">
    <strong>Warning:</strong> You are about to clear (delete) all waste records. This action cannot be undone.<br><br>Are you sure you want to proceed?
    </div>`;

    confirm.show(msg, "Clear Waste Records", "Yes", "No", true);
}

function selectCategory(key)
{ 
    $(".filter-form #filter").val('catg');
    $(".filter-form #query").val(key);
    $(".filter-form").trigger('submit');
}

function filterReason(key) 
{  
    $(".filter-form #filter").val('reas');
    $(".filter-form #query").val(key);
    $(".filter-form").trigger('submit');
} 