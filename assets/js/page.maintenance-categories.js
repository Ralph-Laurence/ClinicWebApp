var dialog = undefined;
var snackbar = undefined;
var confirm = undefined;

var infoModal = undefined;
var updateModal = undefined;
var dataTable = undefined;

$(document).ready(function () {
    onAwake();
});

function onAwake() 
{  
    dialog = new AlertDialog();
    snackbar = new SnackBar();
    confirm = new ConfirmDialog();

    infoModal = new mdb.Modal($("#aboutCategoryModal"));
    
    setupDatatable();

    // recreate the entries dropdown filter
    recreateEntriesFilter();

    notify();
    onBind();
}

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

    $(".dataset-table tbody")
    .on("click", "tr .btn-category-info", function () 
    {  
        var tr = $(this).closest("tr"); 

        var category = tr.find(".td-catg-name").text().trim();
        var totalItems = tr.find(".td-total-items").text().trim();
        var entryDate = tr.find(".td-date-entry").val();

        $(".about-category-name").text(category);
        $(".about-category-date").text(`Date Created: ${entryDate}`);

        var descript = "This category is not in use by any inventory items";

        if (parseInt(totalItems) > 0)
            descript = `This category is in use by ${totalItems} inventory item(s).`;

        $(".category-descript").val(descript);

        infoModal.show();
    })
    .on("click", "tr .btn-edit-category", function () 
    {  
        var tr = $(this).closest("tr");

        var category = tr.find(".td-catg-name").text().trim();
        var key = tr.find(".record-key").val().trim();
        var iconKey = tr.find(".td-icon-key").val().trim();
        var icon = tr.find(".td-category-icon").attr('src');
        
        $(".input-category-name").val(category);
        $(".category-icon").val(iconKey);

        // 0 -> ADD
        // 1 -> UPDATE
        $(".category-action-mode").val(1);
        $(".categories-form").attr("action", $(".action-update-category").val());
        $(".update-key").val(key);
 
        $(".icon-preview").attr("src", icon); 
    })
    .on("click", "tr .btn-delete-category", function () 
    {  
        var tr = $(this).closest("tr");

        var category = tr.find(".td-catg-name").text().trim();
        var key = tr.find(".record-key").val().trim();
        var totalRecords = tr.find(".td-total-items").text().trim();
 
        var msg = 
        `<div class="mb-2">
            Are you sure, you want to delete "<strong>${category}</strong>" from the categories?
        </div>`;

        if (totalRecords > 0)
        {
            msg += 
            `<div class="bg-document p-1 rounded-2">
                &#x25cf; Removing this category will affect ${totalRecords} inventory item(s).
            </div>`;
        }
        
        confirm.actionOnOK = function () 
        {  
            $(".frm-delete #delete-key").val(key);

            if ( System.isNullOrEmpty($(".frm-delete #delete-key").val()) )
            {
                dialog.danger("This action can't be completed. Please reload the page and try again.");
                return;
            }

            $(".frm-delete").trigger("submit");
        };
        confirm.show(msg, "Delete Category", "Yes", "No", true);
    });

    /// ACTION: ADD CATEGORY
    $(".li-action-add-category").click(() => 
    {
        // 0 -> ADD
        // 1 -> UPDATE
        $(".category-action-mode").val(0);
        $(".categories-form").attr("action", $(".action-register-category").val() );
    });


    /// ACTION: SAVE CHANGES TO CATEGORY
    $(".btn-save-category").click(function()
    {
        var mode = $(".category-action-mode").val();

        if ( System.isNullOrEmpty(mode) )
        {
            showError("This action can't be completed because of an error. Please contact the administrator.");
            return;
        }

        if ( !validateCategory( $(".input-category-name") ))
            return;

        $(".categories-form").trigger("submit");
    });

    /// ACTION: Reset registration form on cancel
    $(".btn-cancel-category").click(function() 
    {
        $(".categories-form").trigger("reset");
        $(".icon-preview").attr("src", $(".default-icon").val());
        collapseCategoryIconPicker();
        $(".categories-form").removeAttr("action");
    });
    
    // The dropdown option of 'Delete Selected' rows was clicked
    $(".option-delete-selected").click(() => deleteAllRows()); 

    // Reset illness update form on cancel
    //$(".btn-cancel-update").click(() => $(".update-illness-form").trigger("reset"));

    // When an icon was selected
    $(".icon-picker-item").click(function () 
    {  
        var iconKey = $(this).find(".icon-key").val();
        $(".category-icon").val(iconKey);

        var icon = $(this).find(".icon-image").attr('src');
        $(".icon-preview").attr("src", icon);

        $(".categories-form .selected-icon-name").text($(this).find(".icon-name").text().trim());
    });

    // Expand the icon picker
    $(".categories-form .btn-choose-icon").click(() => 
    {
        if ($(".icon-picker-horizontal").is(":visible"))
        {
            collapseCategoryIconPicker();
            return;
        }

        expandCategoryIconPicker();
    });

    bindRowCheckOption();
} 

function expandCategoryIconPicker() 
{  
    $(".icon-picker-horizontal").fadeIn('fast');
    $(".caret-icon").removeClass("fa-caret-down").addClass("fa-caret-up");
}

function collapseCategoryIconPicker() 
{  
    $(".icon-picker-horizontal").fadeOut('fast');
    $(".caret-icon").removeClass("fa-caret-up").addClass("fa-caret-down");
}

function validateCategory(input) 
{  
    if ( System.isNullOrEmpty(input.val()) )
    {
        showError(0, "Please enter a valid category name!");
        input.focus();
        return false;
    }

    return true;
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
        "order": [[1, 'asc']], // default sort mode
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
 
    $("tfoot .search-col-category-name").find(":input").val(keyword).keyup();

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
//Clear hidden footer fields
//
function clearColSearchFields()
{
    $('tfoot .tfoot-col-search').each(function()
    {
        $(this).val('').keyup();
    });
}
 
function notify() 
{  
    // Show toast when patient is successfully registered
    var success = $(".success-message").val();

    if (!System.isNullOrEmpty(success))
        snackbar.show(success);

    var error = $(".error-message").val();

    if (!System.isNullOrEmpty(error))
        dialog.danger(error);
}
 
function showError(mode, msg)
{ 
    $(".form-error-msg").text(msg).fadeIn();
}

function hideError(mode) 
{  
    $(".form-error-msg").text('').fadeOut();
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
    
    // count affected records
    var totalAffected = 0;

    // collect the keys of all checked rows 
    dataTable.rows().every(function (value, index) 
    {
        var tr = this.node();
        var isChecked = $(tr).find('#row-check-box').prop('checked');

        if (isChecked) 
        {
            checkedRowsCount++;

            var key = $(tr).find('.record-key').val();
            var totalRecords = $(tr).find(".td-total-items").text().trim();

            if (key)
                keys.push(key);

            if( !System.isNullOrEmpty(totalRecords) )
            {
                var total = parseInt(totalRecords);
                totalAffected += total;
            }
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
        dialog.danger("This action can't be completed.");
        return;
    }
 
    var msg = 
    `<div class="mb-2">
        Are you sure, you want to delete all selected categories from the records?
    </div>`;

    if (totalAffected > 0) 
    {
        msg +=
            `<div class="bg-document p-1 rounded-2">
                &#x25cf; Removing these categories will affect ${totalAffected} inventory item(s).
            </div>`;
    }

    // Uncheck all checked rows on cancel
    confirm.actionOnCancel = function () 
    {  
        $(".form-check-input").prop("checked", false).change();
    };

    confirm.actionOnOK = function()
    {
        // encode form data as JSON  
        $(".frm-delete-records #record-keys").val(JSON.stringify(keys));

        $(".frm-delete-records").trigger("submit");
    };  

    confirm.show(msg, "Delete Categories", "Yes", "No", true);
}
 