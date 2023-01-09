var dataTable = undefined;

var restockPlusBtn = undefined;
var restockMinusBtn = undefined;
var restockAmountInput = undefined;

var itemSelectModal = undefined;
var itemSelectModalClose = undefined;
var restockModal = undefined;
var restockModalElem = undefined;

var cardStockinButton = undefined;
var cardStockoutButton = undefined;

var checkboxAddToWaste = undefined; 

var progressLoader  = undefined;

var snackbar = undefined;
//
//==========================================================
// REGION: INITIALIZATION AND EVENT BINDINGS
//==========================================================
//
// Initialize objects after the DOM (Document Object Model)
// has fully loaded ... For readability purposes, the entire
// initialization logic / code is contained in onAwake()
//
$(document).ready(() => onAwake());
//
// All initialization code is contained here ..
// which is called by $(document).ready event above
//
function onAwake()
{ 
    dialog = new AlertDialog();

    restockPlusBtn = $(".btn-stockin-plus");
    restockMinusBtn = $(".btn-stockin-minus");
    restockAmountInput = $("#restock-input-amount");

    restockModal = new mdb.Modal($("#restockModal"));
    restockModalElem = document.getElementById("restockModal");

    itemSelectModal = new mdb.Modal($("#itemPickerModal"));
    itemSelectModalClose = $(".item-select-modal-close");
    progressLoader = $(".progress-loader-wrapper");

    cardStockinButton = $(".card-stockin-button");
    cardStockoutButton = $(".card-stockout-button");

    checkboxAddToWaste = $("#chk_addToWaste");

    snackbar = new SnackBar();

    // force the amount input field to accept only numerics
    Input.forceNumeric(System.getClass(restockAmountInput));
 
    dataTable = $(".items-table")
    .DataTable(
    {
        // searching: false,
        ordering:  false,
        autoWidth: false
    });

    // show snackbar after a successful edit/delete
    notify_OnRestockSuccess();

    // recreate the entries dropdown filter
    createVirtualEntriesPaginator();

    // recreate the pagination searchbar
    createVirtualPaginatorSearch();

    onBind();
}
//
// After initialization, we can now bind (attach) events
// onto elements .. Again, for readability, we put all 
// logic / code of event bindings in onBind() function
//
function onBind()
{ 
    // bind click events on the cards' button ... 

    // the stockin button in the 'Stock In' card
    cardStockinButton.click(() => 
    {
        // set form's action mode attribute to stock in (0);
        // $(".frm-restock").attr('action-mode', '0');
        $("#actionMode").val(0);
    });

    cardStockoutButton.click(() => 
    {
        // set form's action mode attribute to stock out (1);
        // $(".frm-restock").attr('action-mode', '1');
        $("#actionMode").val(1);
    });

    // increase the stock amount from inputfield 
    restockPlusBtn.click(() => 
    {
        // the input field's value (string)
        var inputAmount = restockAmountInput.val();

        // convert (parse) the input into number (int)
        var amount = parseInt(inputAmount);
        amount++;

        // then update the amount into the input field
        restockAmountInput.val(amount).change(); 
    });

    // decrease the stock amount from inputfield
    restockMinusBtn.click(() => 
    {
        // the input field's value (string)
        var inputAmount = restockAmountInput.val();

        // convert (parse) the input into number (int)
        var amount = parseInt(inputAmount);

        // amount should not go less than 1
        if (amount > 1)
            amount--;

        // then update the amount into the input field
        restockAmountInput.val(amount).change(); 
    });

    // track the input field when it's value was changed
    restockAmountInput.on("input", function()
    {
        // get the value present in this input field
        var amount = parseInt($(this).val());

        // force the value to not go below 1
        if (amount < 1)
            $(this).val(1);

        // reflect this input's values onto the hidden field
        $(".frm-restock > #amount").val($(this).val());
    });

    // check the input field if it's value is empty .. 
    // then set its value as 1
    restockAmountInput.on("change", function()
    {
        if ($(this).val() == '')
            $(this).val(1);

        // reflect this input's values onto the hidden field
        $(".frm-restock > #amount").val($(this).val());
    });

    // apply the restock amount when the OK button was clicked
    $(".btn-restock-submit").click(() => 
    {
        $(".frm-restock").trigger("submit");
    });

    // clear the restock form (hidden form) when
    // the restock window was closed or 
    // when the items selector modal was cancelled/closed
    restockModalElem.addEventListener('hidden.mdb.modal', (e) => clearRestockForm());
    itemSelectModalClose.click(() => clearRestockForm());

    // update flag 'add to waste' when checkbox is checked
    checkboxAddToWaste.on('change', function()
    { 
        var checked = $(this).prop('checked');
        
        if (checked) 
            $("#isAddToWaste").val(1);
        else
            $("#isAddToWaste").val(0);
    });
} 
//
//==========================================================
// REGION: RECORD PAGINATION AND DATASET / TABLE OPERATIONS
//==========================================================
//
// recreate the entries dropdown filter
//
function createVirtualEntriesPaginator() 
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
        .attr("id", "virtual-entries-paginator")
        .hide();

    $(cloned[0]).appendTo(".entries-paginator-container");

    $("#virtual-entries-paginator").selectmenu({
        width: 90,
        change: function (event, ui) {
            $($(".dataTables_length").find("select")).val(ui.item.value).change();
        }
    });
}
//
// recreate the pagination searchbar
//
function createVirtualPaginatorSearch()
{
    // the original searchbar element
    var searchBarParent = $(".dataTables_filter > label");
    var searchBar = $(searchBarParent).find(":input");

    // hide the original searchbat
    $(searchBarParent).hide();

    // reflect the virtual searchbar's value onto the original
    $("#pagination-search-bar").on("input", function()
    {
        // the original searchbar is hidden and it only 
        // triggers on keyup, so let's trigger it manually
        searchBar.val($(this).val()).keyup();
    });
}
//
// Restock modes:
// 0 -> Stockin (default)
// 1 -> Stockout
//
function showRestockModal(mode = 0, data)
{
    if (!data) 
        return;

    switch (mode)
    { 
        /// STOCK IN ///
        case 0: 

            // change the restock mode label's text and set color to green
            $(".lbl-restock-mode").removeClass("font-red")
            .addClass("font-teal").text("RESTOCK AMOUNT");

            // change the icon to blue down arrow
            $(".restock-mode-icon").attr("src", "assets/images/icn_stockin.png");
  
            // change modal title text
            $("#restockModalLabel").text("Stock In");
            
            // hide the checkbox for adding the item to waste
            $(".form-check-add-to-waste").hide();
            
            break;

        /// STOCK OUT ///
        case 1: 

            // change the restock mode label's text and set color to red
            $(".lbl-restock-mode").removeClass("font-teal")
            .addClass("font-red").text("PULL OUT AMOUNT");

            // change the icon to red up arrow
            $(".restock-mode-icon").attr("src", "assets/images/icn_stockout.png");
              
            // change modal title text
            $("#restockModalLabel").text("Stock Out");

            // show the checkbox for adding the item to waste
            $(".form-check-add-to-waste").show();

            // the 'add to waste' checkbox must be checked by default
            // and should be flagged with = 1
            checkboxAddToWaste.prop("checked", true);
            $("#isAddToWaste").val(1);

            break; 
    }

    // the data object stores the retrieved item data 
    // and binds it onto the restock modal's labels

    // item name
    $(".item-icon").empty().append(`<img src="assets/images/inventory/${data.icon}.png" width="32" height="32">`);
    $(".lbl-item-name").text(data.itemName);
    $(".lbl-item-code").text(data.itemCode);
    $(".lbl-category").text(data.category);
    $(".lbl-stock").text(`${data.remaining} ${data.measurement}(s)`);
    $(".frm-restock > #itemKey").val(data.itemId);

    restockModal.show();
}
//
// Fetch / Get / Load the selected item's information
//
function loadItemInfo(itemKey)
{  
    showLoader();

    $.ajax({
        url: 'ajax/ajax.get-item-stock.php',
        type: "POST",
        data: {
            itemKey: itemKey
        },
        success: function(res)
        {
            showLoader(false);

            if (res)
            { 
                itemSelectModal.hide();

                var obj = $.parseJSON(res);
                var data = 
                {
                    icon: obj.fas_icon,
                    itemId: obj.item_id,
                    itemName: obj.item_name,
                    itemCode: obj.item_code,
                    category: obj.category,
                    measurement: obj.measurement,
                    remaining: obj.remaining
                };
 
                // identify what action mode was the form set to
                var actionMode = $("#actionMode").val();
 
                // then show the specific modal for that action
                if (actionMode == '0')
                    showRestockModal(0, data);
                else if (actionMode == '1')
                    showRestockModal(1, data);
            } 
        },
        error: function(jqXhr, response)
        {
            showLoader(false);
        }
    });
}
//
// Show / hide progress loader when an AJAX action is triggered.
// This action happens when selecting an item and waiting for
// AJAX to load an item's properties.
// We will apply fading animations when hiding and/or showing
//
function showLoader(show = true)
{
    if (show)
    {
        progressLoader.fadeIn();
        return;
    }

    progressLoader.fadeOut(); 
} 

function clearRestockForm()
{
    $(".frm-restock").trigger("reset");
    restockAmountInput.val(1);
}

function notify_OnRestockSuccess()
{
    var restockStatus = $(".restock-status").val();

    if (!System.isNullOrEmpty(restockStatus))
    {
        snackbar.show(restockStatus);
    }
}