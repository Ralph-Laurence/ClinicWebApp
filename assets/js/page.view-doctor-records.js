var snackbar    = undefined; 
var errorBox    = undefined;
var errorLabel  = undefined;
var regModal    = undefined;
var confirm     = undefined;
var dialog      = undefined;
var dataTable   = undefined;
var toast       = undefined;

$(document).ready(() => onAwake());

function onAwake()
{  
    confirm     = new ConfirmDialog();
    snackbar    = new SnackBar(); 
    dialog      = new AlertDialog();
    toast       = new Toast();

    $(function () 
    {
        $(".combo-box").selectmenu({
            width: 200
        });
    });
     
    setupDatatable();

    bindCapsuleBadge();

    // recreate the entries dropdown filter
    recreateEntriesFilter();

    notify();
    onBind();
}

function onBind()
{ 
    $(document)
    //
    // Details Button
    //
    .on("click", ".dataset-table .btn-doctor-details", function()
    {
        var tr = $(this).closest("tr");

        var key = tr.find(".record-key").val().trim();

        if(System.isNullOrEmpty(key))
        {
            dialog.danger("This action can't be completed. Please reload the page and try again.");
            return;
        }

        $(".frm-details #details-key").val(key);
        $(".frm-details").trigger("submit");
    })
    //
    // Single record delete button
    //
    .on("click", ".dataset-table .tr-action-delete", function()
    {
        var tr = $(this).closest("tr");

        if ($(tr).attr("data-default-doctor"))
        {
            dialog.danger("The requested action is not allowed.\nTo delete this doctor, please change the default doctor first.", "Access Denied");
            return;
        }

        var key = tr.find(".record-key").val().trim();

        if(System.isNullOrEmpty(key))
        {
            dialog.danger("This action can't be completed. Please reload the page and try again.");
            return;
        }

        var docName = tr.find(".td-doc-name").text().trim();
        var msg = `Do you wish to remove "<strong>${docName}</strong>" from the list of doctors?`;

        confirm.show(msg, "Remove Doctor", "Yes", "No", true);

        confirm.actionOnOK = function()
        {
            $(".frm-delete #record-key").val(key);
            $(".frm-delete").trigger("submit");
        };
    })
    //
    // SET DEFAULT DOCTOR
    //
    .on("click", ".dataset-table .tr-action-set-default", function()
    {
        var tr = $(this).closest("tr");
        var recordKey = tr.find(".record-key").val().trim();
        var name = tr.find(".td-doc-name").text().trim();
         
        confirm.show(`Do you want to set "<strong>${name}</strong>" as the default physician?`, 
        "Set Default Physician", "Yes", "No", true);

        confirm.actionOnOK = function () 
        {  
            $(".frm-change-doc #record-key").val(recordKey);

            if (System.isNullOrEmpty( $(".frm-change-doc #record-key").val() ))
            {
                dialog.danger("This action can't be completed. Please reload the page and try again.");
                return;
            }
    
            $(".frm-change-doc").trigger("submit");
        };
    })
    //
    // Edit
    //
    .on("click", ".dataset-table .tr-action-edit", function()
    {
        var tr = $(this).closest("tr");
        var recordKey = tr.find(".record-key").val().trim();
        
        if (System.isNullOrEmpty(recordKey))
        {
            dialog.danger("This action can't be completed. Please reload the page and try again.");
            return;
        }

        $(".frm-edit #record-key").val(recordKey);
        $(".frm-edit").trigger("submit");
    })
    //
    // Specializations Filter
    //
    .on("click", ".specs-filter-body .btn-select-specfilter", function () 
    {  
        var tr = $(this).closest("tr");
        var recordKey = tr.find(".specs-filter-key").val().trim();
        
        if (System.isNullOrEmpty(recordKey))
        {
            dialog.danger("This action can't be completed. Please reload the page and try again.");
            return;
        }

        $(".filter-form #filter").val('spec');
        $(".filter-form #query").val(recordKey);

        $(".filter-form").trigger("submit");
    });

    // The checbox on column header which sets all checkbox per rows as checked
    $("#column-check-all").on('change', function () {
        var checked = $(this).prop('checked');
        checkAllRows(checked);
    });

    // The dropdown option of 'Delete Selected' rows was clicked
    $(".option-delete-selected").click(() => deleteAllRows()); 

    $(".btn-find").click(() => searchRecord()); 
    $(".btn-clear-search").click(() => clearSearch()); 

    bindRowCheckOption();
}

function setfilteraction(action)
{ 
    switch (action)
    {
        case "w0":
            $(".filter-form #filter").val('w0');
            break;
        case "w1":
            $(".filter-form #filter").val('w1');
            break;
        case "*":
            $(".filter-form #filter").val('');
            break;
    }
 
    $(".filter-form").trigger("submit");
}

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
// Delete all checked rows
//
function deleteAllRows()
{  
    // we count all checked rows then store them here
    var checkedRowsCount = 0;

    // store the record keys here
    var keys = [];
  
    var hasDefaultDoctor = false;

    // collect the keys of all checked rows 
    dataTable.rows().every(function (value, index) 
    {
        var tr = this.node();
 
        var isChecked = $(tr).find('#row-check-box').prop('checked');

        if (isChecked) 
        {
            checkedRowsCount++;

            var key = $(tr).find('.record-key').val();

            if ($(tr).attr("data-default-doctor"))
            {
                hasDefaultDoctor = true;
                return false;
            }

            if (key)
                keys.push(key);
        }
    });

    if (hasDefaultDoctor)
    {
        dialog.danger("The requested action is not allowed.\nThe default doctor cannot be deleted. Please select another doctor then set it as the default.", "Access Denied");
        keys = [];
        uncheckAllRows();
        return;
    }

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

    var msg = 
    `<div class="mb-2">Do you really want to delete all of the doctors you've chosen?</div>
    <div class="d-flex flex-row mb-2 bg-document p-2 rounded-2">
        <div class="ms-0">
            <i class="fas fa-info-circle text-primary me-2"></i>
        </div>
        <div class="ms-auto flex-fill text-wrap d-flex">
            <div class="fsz-14">
                This will <strong>NOT</strong>, however, <strong>remove</strong> all checkup records associated with this doctor.
            </div>
        </div>
    </div>`; 

    // Uncheck all checked rows on cancel
    confirm.actionOnCancel = function () 
    {
        $(".form-check-input").prop("checked", false).change();
    };

    confirm.show(msg, "Remove Doctors", "Yes", "No", true);
        
    confirm.actionOnOK = function()
    {  
        // encode form data as JSON  then submit
        $(".frm-delete-records #record-keys").val(JSON.stringify(keys));
        $(".frm-delete-records").trigger("submit");
    };  
}

function notify()
{
    var success = $(".success-msg").val();

    if (!System.isNullOrEmpty(success)) {
        snackbar.show(success);
    }

    var defaultDoctorPrompt = $(".default-doctor-prompt").val();

    if (!System.isNullOrEmpty(defaultDoctorPrompt))
    {
        setTimeout(() => {
            toast.show(defaultDoctorPrompt, "Warning", toast.toastTypes.WARN, true, 8000);
        }, 1500);
    }
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
        order: [[7,'asc']],
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
    {
        $(".btn-sort").fadeIn();
        //prependDefaultDoctor();
    }
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
            $("tfoot .search-col-doc-name").find(":input").val(keyword).keyup();
            break;
        case '1': 
            $("tfoot .search-col-doc-regnum").find(":input").val(keyword).keyup();
            break; 
    }

    // Show the search keyword in a badge
    $(".capsule-badge-search-keyword").text(keyword);
    $(".capsule-badge-search").css("display", "flex").show();

    // Show the clear button whenever there is a search performed
    $(".btn-clear-search").show();
}

function bindCapsuleBadge() 
{  
    var totalDoctors = 0;
    var withPatients = 0;
    var withoutPatients = 0;

    dataTable.rows().every(function (value, index) 
    {
        var tr = this.node();
        var totalPatients = $(tr).find('.td-total-patients').text().trim();
        
        if (totalPatients > 0)
            withPatients++;
        else 
            withoutPatients++;

        totalDoctors++;
    });

    $(".badge-totalDoctors").text(totalDoctors);
    $(".badge-withPatients").text(withPatients);
    $(".badge-withoutPatients").text(withoutPatients);
}

function prependDefaultDoctor()
{  
    var defaultRow;
    
    // Iterate every rows in table,
    // Find the row that has the attribute "data-default-doctor"
    // Then cache that row in a variable called "defaultRow".
    // Remove that row from the table 
    dataTable.rows().every(function () 
    {
        var row = this.node();

        if ($(row).attr('data-default-doctor')) 
        { 
            defaultRow = $(row).clone(true); //this.data();
            dataTable.row(this).remove().draw();
        }
    });
    
    // Add the row back to the table then place it on top
    if (defaultRow) 
    {
        dataTable.row.add(defaultRow).draw();
        dataTable.row(0).nodes().to$().before(defaultRow);
    }

    // Add a tooltip onto the bookmark
    $('.doctor-bookmark').tooltip({
        title: "The default doctor will appear at the top of the list when the page loads and is marked with a pin icon.",
    });
}