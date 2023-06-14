var dataTable = undefined;
var dialog    = undefined;
var modal     = undefined;
var snackbar  = undefined;
var confirm   = undefined;
var dropdown  = undefined;
var expiryDate = undefined;

$(document).ready(() => onAwake());

function onAwake()
{
    dialog = new AlertDialog();
    modal = new mdb.Modal($("#restockModal"));
    snackbar = new SnackBar();
    confirm = new ConfirmDialog();

    expiryDate = $(".stockin-expiry-date").datepicker({
        changeMonth: true,
        changeYear: true,
        yearRange: 'c:c+5'
    });  

    if ($("#moveToWaste").length > 0)
    {
        dropdown = new mdb.Dropdown($(".txt-reason.dropdown-toggle"));
    }

    $(function () 
    {  
        $(".combo-box").selectmenu({width: 180});
    });
    setupDatatable();
    recreateEntriesFilter();
    bindTotalsCount();
    notify();
    onBind();
}

function onBind()
{
    // Track click events on this table
    $(".dataset-table .dataset-body").on("click", "tr .action-btn", function()
    {
        var tr = $(this).closest("tr");
        var rowIndex = dataTable.rows({ page: 'current' }).row( tr.index() ).index();
        var itemKey  = tr.find( ".item-key"  ).val().trim();
        var itemName = tr.find( ".td-item-name" ).text().trim();
        var stock    = tr.find( ".td-current-stock" ).text().trim();
        var reserve  = tr.find( ".reserve-stock" ).val().trim();
        var icon     = tr.find( ".item-icon" ).attr("src");
        var max      = tr.find( ".td-max-qty" ).val() || 0;

        var expiry   = tr.find( ".td-expiry" ).val() || '';

        if (!itemKey || !itemName)
        {
            dialog.danger("This action can't be completed because of an error.\nPlease reload the page and try again.");
            return;
        }

        $(".frm-restock .item-key").val(itemKey);
        $('#input-stock-qty').attr("data-max-qty", max);

        bindRestockModal({
            itemName: itemName,
            stock: stock,
            reserve: reserve,
            icon: icon,
            expiry: expiry
        });
    })
    .on("click", "tr .discard-btn", function()
    {
        var tr = $(this).closest("tr");
        
        var itemKey  = tr.find( ".item-key"  ).val().trim();
        var itemName = tr.find( ".td-item-name" ).text().trim();
        var stock    = tr.find( ".expired-stock-units" ).text().trim();
        
        confirm.actionOnOK = function() 
        {    
            $(".frm-discard .item-key").val(itemKey);
            $(".frm-discard").trigger("submit");
        };

        var msg = `<div class="mb-2">Are you sure, you want to dispose 
        <span class="font-primary-dark fst-italic">${stock}</span> 
        of the item <span class="fw-bold font-primary-dark fst-italic">"${itemName}"</span> ?
        This item is no longer safe to use and must be moved to waste.</div>
        <div class="p-1 my-3 bg-document rounded-2 fsz-14"><i class="fas fa-info-circle me-1"></i>
        This will also reset the expiry date automatically.</div>
        Please confirm by clicking "Yes" or cancel by clicking "No".`;

        confirm.show(msg, "Discard Expired Stock", "Yes", "No", true);
    });

    // Minus button on modal
    $(".btn-qty-minus").click(function()
    {
        var input = $('#input-stock-qty');
        var qty = parseInt(input.val()) || 0;
        var min = parseInt(input.attr("data-min-qty")) || 0;

        if (qty > min)
        {
            qty--;
            $('#input-stock-qty').val(qty);
        } 
    });

    // Plus button on modal
    $(".btn-qty-plus").click(function()
    {
        var input = $('#input-stock-qty'); 
        var qty = parseInt(input.val()) || 0;
        var max = parseInt(input.attr("data-max-qty")) || 0;

        // Stock in mode, no limits, as long as max = 0
        if (max <= 0)
        {
            qty++;
            $('#input-stock-qty').val(qty);
            return;
        }

        // Stock out mode
        if (qty < max)
        {
            qty++;
            $('#input-stock-qty').val(qty);
        } 
    });

    // Track the input changes in quantity input field
    $("#input-stock-qty").on("input", function()
    {
        var qty = parseInt($(this).val()) || 0;
        var min = parseInt($(this).attr("data-min-qty")) || 0;
        var max = parseInt($(this).attr("data-max-qty")) || 0;

        if (qty < min)
            $(this).val(min);

        // Stock in mode, no limits
        if (max <= 0)
            return;

        // Stock out mode
        if (qty > max)
            $(this).val(max);
    });

    // Force all numeric textboxes to accept only integers
    Input.forceNumeric("numeric");

    // Validate before Submit
    $(".btn-ok").click(function()
    {
        handleSubmit();
    });

    $(".btn-find").click(() => searchRecord()); 
    $(".btn-clear-search").click(() => clearSearch()); 

    if ($("#moveToWaste").length > 0)
    { 
        $("#moveToWaste").change(function () 
        { 
            if ( $(this).is(":checked") )
            { 
                $("#h-moveToWaste").val('1');
                $(".txt-reason").prop("disabled", false);
                dropdown.show();

                return;   
            }

            $(".txt-reason").prop("disabled", true).blur();
            $("#h-moveToWaste").val('0');
        }); 
    }

    // Date picker for expiry date
    // $(".expiry-date").on("change", function() {
    //     $(this).focus().blur();
    // });

    // Date picker for expiry date
    $(".stockin-expiry-date").on("change", function () 
    {    
        $(".frm-restock #expiry-date").val( $(this).val() );
        $(this).focus().blur();
    });

    $(".btn-clear-expiry").click(function() 
    {  
        $(".frm-restock #expiry-date").val('');
        $(".stockin-expiry-date").val('').blur();
    });
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

function bindRestockModal(data)
{
    if (!data)
    {
        dialog.danger("This action can't be completed because of an error.");
        return;
    }

    $("#input-stock-qty").val(1);
    $(".restock-lbl-itemname").text(data.itemName);
    $(".restock-lbl-current-stock").text(data.stock);
    $(".restock-lbl-reserve-stock").text(data.reserve);
    $(".restock-item-icon").attr('src', data.icon);
     
    if ( $(".stockin-expiry-date").length > 0 )
    { 
        $(".stockin-expiry-date").val(data.expiry);
        $(".frm-restock #expiry-date").val(data.expiry);
    }

    if (System.isNullOrEmpty(data.expiry))
    {
        $(".expiry-date-wrapper").show();
    }
    else
    {
        $(".expiry-date-wrapper").hide();
    }

    modal.show();
    data = null;
}
 
function handleSubmit()
{
    var qty = $("#input-stock-qty").val();

    // Qty must not be 0 or empty
    if (qty == 0 || !qty)
    {
        dialog.danger("You've entered an invalid value. Please try again.");
        return;
    }

    // Restock amount
    $(".frm-restock .qty").val(qty);

    // Move to waste
    if ($("#moveToWaste").length > 0 && $("#moveToWaste").is(":checked"))
    {
        var reason = $(".txt-reason").val();

        if (System.isNullOrEmpty(reason))
        {
            $(".restock-validation-error").text("Please select a reason for disposal.").fadeIn();
            return;
        }  
    }

    $(".frm-restock").trigger("submit");
}

function notify()
{
    $msg = $(".server-response").val().trim();

    if (!System.isNullOrEmpty($msg))
        snackbar.show($msg);
}

function bindTotalsCount()
{
    var counter = $(".totals-counter").val();
    
    if (System.isNullOrEmpty(counter))
        return;
    
    var json = JSON.parse(counter);
    // console.log(json);

    $(".total-critical").text(json.totalCritical);
    $(".total-soldout").text(json.totalSoldout);
    $(".total-expired").text(json.totalExpired);
}
 
function setStockoutReason(sender, key) 
{  
    $(".txt-reason").val($(sender).find('.li-label').text().trim()).focus().blur();
    $("#h-wasteReason").val(key);
}

function selectCategory(key)
{ 
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