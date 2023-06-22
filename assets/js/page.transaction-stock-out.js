var dialog    = undefined;
var dataTable = undefined;

$(document).ready(function () {
    onAwake();
});

function onAwake()
{
    dialog = new AlertDialog();

    // Force all numeric textboxes to accept only integers
    Input.forceNumeric("numeric");

    $(function () 
    {  
        $(".combo-box").selectmenu({width: 180});
    });
    setupDatatable();
    recreateEntriesFilter();
    bindTotalsCount();

    onBind();
}

function onBind()
{
    $(document).on("click", ".dataset-body tr .i-stockout-btn", function()
    {
        var tr = $(this).closest('tr');
        var itemKey = tr.find(".item-key").val();
 
        $(".frm-stockout #item-key").val(itemKey);
        
        if ( !$(".frm-stockout #item-key").val() )
        {
            dialog.danger("This action can't be completed. Please reload the page and try again.");
            return;
        }

        $(".frm-stockout").trigger("submit");
    });
 
    $(".btn-find").click(() => searchRecord()); 
    $(".btn-clear-search").click(() => clearSearch()); 
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

    $("tfoot .search-col-name").find(":input").val(keyword).keyup();

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

function selectCategory(key)
{ 
    $(".filter-form #filter").val('catg');
    $(".filter-form #query").val(key);
    $(".filter-form").trigger('submit');
}