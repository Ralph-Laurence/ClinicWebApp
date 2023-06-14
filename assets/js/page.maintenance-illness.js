var dialog = undefined;
var snackbar = undefined;
var confirm = undefined;

var infoModal = undefined;
var updateModal = undefined;

$(document).ready(function () {
    onAwake();
});

function onAwake() 
{  
    dialog = new AlertDialog();
    snackbar = new SnackBar();
    confirm = new ConfirmDialog();

    infoModal = new mdb.Modal($("#aboutIllnessModal"));
    updateModal = new mdb.Modal($("#updateIllnessModal"));
    
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
    .on("click", "tr .btn-illness-info", function () 
    {  
        var tr = $(this).closest("tr");

        var illnessName = tr.find(".td-ill-name").text();
        var illnessDate = tr.find(".td-ill-date").val();
        var illnessDesc = tr.find(".td-ill-desc").val();
        var totalRecord = tr.find(".td-total-records").text();

        $(".illness-name").text(illnessName);
        $(".illness-descript").val(illnessDesc);
        $(".illness-date").text(illnessDate);

        if (parseInt(totalRecord) > 0)
            $(".illness-total-records").text(`This illness has ${totalRecord} total checkup record(s)`);
        else 
            $(".illness-total-records").text(`No records were created for this illness.`);

        infoModal.show();
    })
    .on("click", "tr .btn-edit-illness", function () 
    {  
        var tr = $(this).closest("tr");

        var illnessName = tr.find(".td-ill-name").text().trim();
        var illnessDesc = tr.find(".td-ill-desc").val().trim();
        var key = tr.find(".record-key").val().trim();

        $(".input-update-illness-name").val(illnessName);
        $(".update-illness-desc").val(illnessDesc);
        $(".update-illness-key").val(key);

        updateModal.show();
    })
    .on("click", "tr .btn-delete-illness", function () 
    {  
        var tr = $(this).closest("tr");

        var illnessName = tr.find(".td-ill-name").text().trim();
        var key = tr.find(".record-key").val().trim();
        var totalRecords = tr.find(".td-total-records").text().trim();

        $(".input-update-illness-name").val(illnessName);
  
        var msg = 
        `<div class="mb-2">
            Are you sure, you want to delete "<strong>${illnessName}</strong>" from the illness records?
        </div>`;

        if (totalRecords > 0)
        {
            msg += 
            `<div class="bg-document p-1 rounded-2">
                &#x25cf; Removing this illness will affect ${totalRecords} checkup record(s).
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
        confirm.show(msg, "Delete Illness", "Yes", "No", true);
    });

    $(".btn-add-illness").click(function()
    {
        if ( !validateIllness( $(".input-add-illness-name") ))
            return;

        $(".register-illness-form").trigger("submit");
    });

    $(".btn-update-illness").click(function () 
    {  
        if (!validateIllness( $(".input-update-illness-name") ))
            return;

        $(".update-illness-form").trigger("submit");
    });
    
    // The dropdown option of 'Delete Selected' rows was clicked
    $(".option-delete-selected").click(() => deleteAllRows()); 

    // Reset illness registration form on cancel
    $(".btn-cancel-register").click(() => $(".register-illness-form").trigger("reset"));

    // Reset illness update form on cancel
    $(".btn-cancel-update").click(() => $(".update-illness-form").trigger("reset"));

    bindRowCheckOption();
} 

function validateIllness(input) 
{  
    if ( System.isNullOrEmpty(input.val()) )
    {
        showError(0, "Please enter a valid illness name!");
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
 
    $("tfoot .search-col-illness-name").find(":input").val(keyword).keyup();

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
//
// mode = 0 => ADD
// mode = 1 => UPDATE
//
function showError(mode, msg)
{
    switch (mode)
    {
        case 0: 
            $(".register-error-msg").text(msg).fadeIn();
            break;
        case 1:
            break;
    }
}

function hideError(mode) 
{  
    switch (mode)
    {
        case 0: 
            $(".register-error-msg").text('').fadeOut();
            break;
        case 1:
            break;
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
            var totalRecords = $(tr).find(".td-total-records").text().trim();

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
        Are you sure, you want to delete all selected illnesses from the records?
    </div>`;

    if (totalAffected > 0) 
    {
        msg +=
            `<div class="bg-document p-1 rounded-2">
                &#x25cf; Removing these illnesses will affect ${totalAffected} checkup record(s).
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

    confirm.show(msg, "Delete Illnesses", "Yes", "No", true);
}