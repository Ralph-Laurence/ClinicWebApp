var dialog  = undefined;
var confirm = undefined;

var errorBox = undefined;
var errorLabel = undefined;

var lastCategoryLabel = "";
var isResetCategory = false;

var lastUnitsLabel = "";
var isResetUnits = false;

var lastSupplierLabel = "";
var isResetSupplier = false;

// the required fields
let validation = 
[
    {   el: $("#item-name"),            msg: "Please enter a valid item name!"          },
    {   el: $("#category-label"),       msg: "Please add or select a category!"         },
    {   el: $("#item-code"),            msg: "Please enter a valid item code!"          },
    {   el: $("#units-label"),          msg: "Please add or select a unit of measure!"  },
    {   el: $("#opening-stock"),        msg: "Please enter an opening stock amount!"    },
    {   el: $("#reserve-stock"),        msg: "Please enter a reserve stock amount!"     },
];

$(document).ready(function () {
    onAwake();
});

function onAwake() 
{  
    dialog  = new AlertDialog();
    confirm = new ConfirmDialog();

    Input.forceAlphaNums("alphanum", true);
    Input.forceNumeric("numeric");

    errorBox = $(".error-message");
    errorLabel = $(".error-label");

    $("#icn-category").val( $("#category-icon").attr('src') );

    bindLastInputs();
    notify();
    onBind();
}

function onBind() 
{  
    $(".btn-save").click(() => handleSubmit());

    $(".btn-cancel").click(function()
    {
        var msg = "Your changes won't be saved if you cancel.\n\nDo you wish to abort the operation?";

        confirm.actionOnOK = function()
        {
            var goBack = $(".goback").val();
            navHref(goBack);
        };

        confirm.show(msg, "Register Supplier", "Yes", "No");
    });
    //
    // Track category field input events
    //
    $("#category-label").on("input", function () 
    {  
        var isEmpty = System.isNullOrEmpty($(this).val());
        
        if (isEmpty || System.isNullOrEmpty(lastCategoryLabel))
            return;

        if (!isEmpty && $(this).val() != lastCategoryLabel && !isResetCategory)
            resetCategory();
    });
    //
    // Track units field input events
    //
    $("#units-label").on("input", function () 
    {  
        var isEmpty = System.isNullOrEmpty($(this).val());
        
        if (isEmpty || System.isNullOrEmpty(lastUnitsLabel))
            return;

        if (!isEmpty && $(this).val() != lastUnitsLabel && !isResetUnits)
            resetUnits();
    });
    //
    // Track suppliers field input events
    //
    $("#supplier-label").on("input", function () 
    {  
        var isEmpty = System.isNullOrEmpty($(this).val());
        
        if (isEmpty || System.isNullOrEmpty(lastSupplierLabel))
            return;

        if (!isEmpty && $(this).val() != lastSupplierLabel && !isResetSupplier)
            resetSupplier();
    });

    // Preview the uploaded image
    $("#item-image").on('change', () => getImgData());

    $("#findCategoryModal").on("show.mdb.modal", function()
    { 
        if ($(".categories-tbody tr").length < 1)
        {
            $(".warning-container").html(
                `<div class="note note-warning d-flex flex-row mb-3">
                    <div class="fw-bold me-2">Warning:</div> 
                    <div>No medicine categories were found. Please type / enter the category manually.</div>
                </div>`);
        }
    });

    $("#findSupplierModal").on("show.mdb.modal", function()
    { 
        if ($(".suppliers-tbody tr").length < 1)
        {
            $(".warning-container").html(
                `<div class="note note-warning d-flex flex-row mb-3">
                    <div class="fw-bold me-2">Warning:</div> 
                    <div>No suppliers were found. Please type / enter the supplier manually.</div>
                </div>`);
        }
    });
}

function notify() 
{  
    var err = $(".err-msg").val();

    if (!System.isNullOrEmpty(err))
        showError(err);
} 

//==================================================//
//---------------- FORM VALIDATIONS ----------------//
//==================================================//
function handleSubmit() 
{   
    for(let obj of validation)
    {   
        if (System.isNullOrEmpty(obj.el.val())) 
        {  
            focusOnError(obj.msg, obj.el);
            return;
        }
    }
 
    // Expiry Date
    if ($("#chk-expiry").length > 0 && $(".expiry-date").length > 0)
    {
        if (!($("#chk-expiry").is(":checked"))) {
            var expiry = $(".expiry-date");
    
            if (System.isNullOrEmpty(expiry.val())) {
                focusOnError("Please select an expiration date!", expiry);
                return;
            }
        }
    }

    $("#register-form").trigger("submit");
    $(".btn-save").prop('disabled', true);
}

function focusOnError(msg, el)
{
    showError(msg);  
    $("#error-box")[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
    el.focus();
}

function showError(message)
{
    errorLabel.text(message);
    errorBox.fadeIn('fast');
}

function hideError()
{
    errorLabel.text('');
    errorBox.hide();
}

function selectCategory(key, name, icon) 
{    
    isResetCategory = false;

    $("#category-icon").attr("src", icon);
    $("#category-label").val(name);
    $("#category-key").val(key);
    $("#icn-category").val(icon);

    lastCategoryLabel = name;
}

function resetCategory() 
{  
    isResetCategory = true;
    
    $("#category-icon").attr("src", `assets/images/icons/icn-icon.png`);
    $("#category-label").val('');
    $("#category-key").val('');
}

function selectUnits(key, unit) 
{   
    isResetUnits = false; 
    
    $("#units-label").val(unit);
    $("#units-key").val(key);

    lastUnitsLabel = unit;
}

function resetUnits()
{  
    isResetUnits = true;
     
    $("#units-label").val('');
    $("#units-key").val('');
}

function selectSupplier(key, name) 
{   
    isResetSupplier = false; 

    if (!key || !name)
        return;

    $("#supplier-label").val(name);
    $("#supplier-key").val(key);

    lastSupplierLabel = name;
}

function resetSupplier()
{  
    isResetSupplier = true;
     
    $("#supplier-label").val('');
    $("#supplier-key").val('');
}

function bindLastInputs() 
{  
    var json = $(".data-last-input").val();

    if (System.isNullOrEmpty(json))
        return;

    var lastData = $.parseJSON(json); 
    
    var iconPath = `assets/images/inventory/${lastData['categoryIcon']}.png`;

    if (!lastData['categoryIcon'])
        iconPath = `assets/images/icons/icn_no_image.png`;

    selectSupplier(lastData['supplierKey'], lastData['supplierLabel']);
    selectCategory(lastData['categoryKey'], lastData['categoryLabel'], iconPath);
    selectUnits(lastData['unitsKey'], lastData['unitsLabel']);

    $("#units-key").val(lastData['unitsKey']);
    $("#category-key").val(lastData['categoryKey']);
    $("#supplier-key").val(lastData['supplierKey']); 
}

function getImgData() 
{  
    const file = $("#item-image")[0].files[0];

    if (file)
    {
        const reader = new FileReader();

        reader.readAsDataURL(file);

        $(reader).on('load', function (e) 
        {  
            var src = this.result;
            
            $(".item-image-preview").attr('src', src);
        });
    }
}
