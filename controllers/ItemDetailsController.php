<?php

use Models\Item;

@session_start();

require_once("rootcwd.php");

require_once($rootCwd . "includes/urls.php");

require_once($rootCwd . "database/configs.php");
require_once($rootCwd . "database/dbhelper.php");
require_once($rootCwd . "includes/system.php");
require_once($rootCwd . "includes/utils.php");

require_once($rootCwd . "models/Item.php");

require_once($rootCwd . "includes/Auth.php");
require_once($rootCwd . "includes/Security.php");
require_once($rootCwd . "errors/IError.php");

require_once($rootCwd . "MasterLayout.php");
require_once($rootCwd . "layout-header.php");
 
$security   = new Security();
 
$security->requirePermission(Chmod::PK_USERS, Chmod::FLAG_READ);
$security->checkAccess(Chmod::PK_USERS, UserAuth::getId());
$security->BlockNonPostRequest();

$itemFields = Item::getFields();
$db = new DbHelper($pdo);
$inventory = new Item($db);

$goBack = (ENV_SITE_ROOT . Pages::MEDICINE_INVENTORY);

$itemKey = $_POST['details-key'] ?? "";
 
if (empty($itemKey))
{
    Response::Redirect($goBack, Response::Code301);
    exit;
}
 
try 
{
    $itemId  = $security->Decrypt($itemKey);
    $dataset = $inventory->find($itemId); //$db->findWhere(TableNames::inventory, [$itemFields->id => $itemId]);

    // dump($dataset);

    if (empty($dataset))
        onError();

} catch (\Exception $th) {
    echo $th->getMessage();
    exit;
    onError();
} catch (\Throwable $th) {
    echo $th->getMessage();
    exit;
    onError();
}
  
function onError()
{
    IError::Throw(500);
    exit;
}

// function loadItemId()       { return loadLastInput('itemId');       }
function loadItemName()     
{ 
    global $dataset;
    return $dataset['item'];         
} 

function loadItemCode()     
{ 
    global $dataset;
    return $dataset['sku'];          
}

function loadCategory()     
{ 
    global $dataset;
    return $dataset['category'];          
}

function loadStock()        
{ 
    global $dataset;
    return "{$dataset['stock']} {$dataset['measurement']}";        
}
function loadReserve()     
{ 
    global $dataset;
    return "{$dataset['reserve']} {$dataset['measurement']}";      
}

function loadSupplier()     
{ 
    global $dataset;
    return $dataset['suppName'];      
}

function loadDescription()  
{ 
    global $dataset;
    return !empty($dataset['remarks']) ? $dataset['remarks'] : "No description available";      
}  

function loadItemImage()    
{ 
    global $dataset;
 
    if (empty($dataset['image']))
    { 
        if (!empty($dataset['icon']))
            $image = "assets/images/inventory/". $dataset['icon'] . ".png";
        else
            $image = "assets/images/icons/icn-icon.png";
            
        return $image;
    } 

    return ENV_SITE_ROOT . "storage/uploads/items/" . $dataset['image'];
}


function loadStatus()
{
    global $dataset;

    $stock = $dataset['stock'];
    $reserve = $dataset['reserve'];
    $expiry = $dataset['expiryDate'];

    $stockLabel = 
    "<span class=\"input-group-text fsz-14 text-white bg-success\">
        <i class=\"fas fa-check me-2\"></i>
        In Stock
    </span>";

    $soldOut = 
    "<span class=\"input-group-text fsz-14 bg-red-light font-red-dark\">
        <i class=\"fas fa-times me-2\"></i>
        Out of Stock
    </span>";

    // Sold out 
    if ($stock <= 0)
    {   
        $stockLabel = $soldOut;
    }

    // Critical
    if ($stock <= $reserve && $stock > 0)
    {  
        $stockLabel = 
        "<span class=\"input-group-text fsz-14 bg-amber text-dark\">
            <i class=\"fas fa-exclamation-triangle me-2\"></i>
            Critical
        </span>";
        
    }

    // Expired
    if (Dates::isPast($expiry))
    { 
        if ($stock > 0)
        {
            $stockLabel =     
            "<span class=\"input-group-text fsz-14 bg-red text-white\">
                <i class=\"fas fa-exclamation-triangle me-2\"></i>
                Expired
            </span>";
        } 
        else if ($stock == 0)
        {
            $stockLabel = $soldOut;
        }
    }

    return $stockLabel;
}

function loadExpiry()
{
    global $dataset; 

    return !empty($dataset['expiryDate']) ?
        Dates::toString($dataset['expiryDate'], "M. d, Y") : "None";
}

function loadExpireMessage()
{
    global $dataset; 
 
    // Expired
    if (Dates::isPast($dataset['expiryDate']))
    { 
        if ($dataset['stock'] > 0)
        {
            echo <<< DIV
            <div class="expire-message text-muted fsz-14 fst-italic">
                This item is no longer safe to use and must be discarded.
            </div>
            <button class="btn btn-discard btn-secondary bg-red text-white py-1 px-2">Discard</button>
            DIV;
        }  
    } 
}

function setSenderKey()
{
    global $security;
    return $security->Encrypt("3");
}