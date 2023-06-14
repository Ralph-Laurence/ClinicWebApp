<?php

use Models\Item; 

@session_start();

require_once("rootcwd.inc.php");

require_once($rootCwd . "database/configs.php");
require_once($rootCwd . "includes/system.php");
require_once($rootCwd . "includes/utils.php"); 
require_once($rootCwd . "database/dbhelper.php");
require_once($rootCwd . "includes/urls.php");

require_once($rootCwd . "includes/Auth.php");
require_once($rootCwd . "includes/Security.php");
require_once($rootCwd . "models/Item.php");

require_once($rootCwd . "MasterLayout.php");
require_once($rootCwd . "layout-header.php");

$security = new Security();
$security->requirePermission(Chmod::PK_INVENTORY, Chmod::FLAG_WRITE);
$security->checkAccess(Chmod::PK_INVENTORY, UserAuth::getId());
// $security->BlockNonPostRequest();    

$itemFields = Item::getFields();
$db = new DbHelper($pdo);
$inventory = new Item($db);

$itemKey = $_POST['item-key'] ?? "";

if (isset($_SESSION['edit-item-last-key']))
{
    $itemKey = $_SESSION['edit-item-last-key'];
    unset($_SESSION['edit-item-last-key']);
}

if (empty($itemKey))
{
    Response::Redirect((ENV_SITE_ROOT . Pages::MEDICINE_INVENTORY), Response::Code301);
    exit;
}

// Store the last input values here from the session
$lastInputs = [];

if (isset($_SESSION['add-item-last-inputs']))
{
    $lastInputs = $_SESSION['add-item-last-inputs'];
    unset($_SESSION['add-item-last-inputs']);
} 
else
{
    try
    {
        $itemId     = $security->Decrypt($itemKey);
        $lastInputs = $inventory->find($itemId); //$db->findWhere(TableNames::inventory, [$itemFields->id => $itemId]);

        if (empty($lastInputs))
            onError();

    }
    catch (\Exception $th) { echo $th->getMessage(); exit; onError(); }
    catch (\Throwable $th) { echo $th->getMessage(); exit; onError(); }
}

/// Load last input value after submit 
function loadLastInput($inputKey, $defaultValue = "")
{
    global $lastInputs;

    if (empty($lastInputs))
        return $defaultValue;

    return $lastInputs[$inputKey];
}
 
function getErrorMessage()
{
    $errorMessage = "";

    if (isset($_SESSION['items-actions-error-msg'])) 
    {
        $errorMessage = $_SESSION['items-actions-error-msg'];
        unset($_SESSION['items-actions-error-msg']);
    }

    return $errorMessage;
}

function onError()
{
    IError::Throw(500);
    exit;
}

function loadItemId()       { return loadLastInput('itemId');     }
function loadItemName()     { return loadLastInput('item');       } 
function loadItemCode()     { return loadLastInput('sku');        }
  
function loadStock()        { return loadLastInput('stock');      }
function loadReserved()     { return loadLastInput('reserve');    }
function loadDescription()  { return loadLastInput('remarks');    }  
function loadExpiry()       
{ 
    $expiry = loadLastInput('expiryDate');

    return !empty($expiry) ? Dates::toString($expiry, "m/d/Y") : ""; 
}
function hasExpiry()       
{ 
    $expiry = loadLastInput('expiryDate');

    return !empty($expiry); 
}
function loadItemImage()    
{ 
    $image = ENV_SITE_ROOT . "storage/uploads/items/" . loadLastInput('image');        

    if (empty(loadLastInput('image')))
    {  
        if (!empty(loadLastInput('icon')))
            $image = "assets/images/inventory/". loadLastInput('icon') . ".png";
        else
            $image = "assets/images/icons/icn-icon.png";
            
        return $image;
    } 

    return $image;
}

function loadSpecialInputs() 
{
    global $security;

    $data = "";

    if (isset($_SESSION['add-item-last-special-data']))
    {
        $data = $_SESSION['add-item-last-special-data'];
        unset($_SESSION['add-item-last-special-data']);
    }
    else
    {
        $data = json_encode([
            'supplierKey'   => $security->Encrypt(loadLastInput('suppId')), 
            'supplierLabel' => loadLastInput('suppName'),
            'unitsKey'      => $security->Encrypt(loadLastInput('unitsId')),     
            'unitsLabel'    => loadLastInput('measurement'),
            'categoryKey'   => $security->Encrypt(loadLastInput('categoryId')), 
            'categoryLabel' => loadLastInput('category'), 
            'categoryIcon'  => loadLastInput('icon')
        ]);
    }
    
    return $data;
}

function showStockWarning()
{
    $stock   = loadLastInput('stock');
    $reserve = loadLastInput('reserve');
    $label = "This item is low on stock. Please restock soon.";
    $style = "d-none";

    if ($stock > 0 && $stock <= $reserve)
        $style = "bg-amber-light font-brown";

    if ($stock <= 0)
    {
        $style = "bg-red-light font-red-dark";
        $label = "This item is out of stock. Please restock now.";
    }

    echo <<<DIV
    <div class="stock-alert text-center $style p-2 rounded-2 fsz-14">
        $label
    </div>
    DIV;
}