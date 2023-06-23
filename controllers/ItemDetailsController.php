<?php

use Models\Item;
use Models\Stock;

@session_start();

require_once("rootcwd.php");

require_once($rootCwd . "includes/urls.php");

require_once($rootCwd . "database/configs.php");
require_once($rootCwd . "database/dbhelper.php");
require_once($rootCwd . "includes/system.php");
require_once($rootCwd . "includes/utils.php");

require_once($rootCwd . "models/Item.php");
require_once($rootCwd . "models/Stock.php");

require_once($rootCwd . "includes/Auth.php");
require_once($rootCwd . "includes/Security.php");
require_once($rootCwd . "errors/IError.php");

require_once($rootCwd . "MasterLayout.php");
require_once($rootCwd . "layout-header.php");
 
$security   = new Security();
 
$security->requirePermission(Chmod::PK_USERS, Chmod::FLAG_READ);
$security->checkAccess(Chmod::PK_USERS, UserAuth::getId());
// $security->BlockNonPostRequest();

$itemFields = Item::getFields();
$db = new DbHelper($pdo);
$inventory = new Item($db);

$goBack = (ENV_SITE_ROOT . Pages::MEDICINE_INVENTORY);

$itemKey = $_POST['details-key'] ?? "";

if (isset($_SESSION['item-details-key']))
{
    $itemKey = $_SESSION['item-details-key'];
    unset($_SESSION['item-details-key']);
}
 
if (empty($itemKey))
{
    Response::Redirect($goBack, Response::Code301);
    exit;
}
 
try 
{
    $itemId  = $security->Decrypt($itemKey);
    $dataset = $inventory->find($itemId); //$db->findWhere(TableNames::inventory, [$itemFields->id => $itemId]);

    $stockFields = Stock::getFields();
    $stox = TableNames::stock;

    $filterShowExpired = "";

    if (isset($_POST['filter']) && $_POST['filter'] == 'x')
    {
        $filterShowExpired = " AND $stockFields->expiry_date <= CURRENT_DATE";
    }

    $stmt_load_stox = $db->getInstance()->prepare
    (
        "SELECT 
            $stockFields->id,
            $stockFields->sku, 
            $stockFields->quantity, 
            $stockFields->expiry_date,
            $stockFields->dateCreated
        FROM  $stox 
        WHERE $stockFields->item_id = ? $filterShowExpired
        ORDER BY $stockFields->dateCreated DESC" 
    );
    $stmt_load_stox->execute([$itemId]);
    $stocksDataset = $stmt_load_stox->fetchAll(PDO::FETCH_ASSOC);
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
  
function getItemKey()
{
    global $itemKey;
    return $itemKey;
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

function loadCondition()
{
    global $stocksDataset, $stockFields, $dataset; 

    $reserve = $dataset['reserve'];
    $remaining = $dataset['stock'];

    // 0 - ok, 1 - critical, no expired, 2 = expired
    $status = 0;

    foreach($stocksDataset as $row)
    {
        if (Dates::isPast($row[$stockFields->expiry_date]))
        {
            $status = 2;
            break;
        } 
    }

    if ($remaining <= $reserve && $status < 2)
        $status = 1;

    switch ($status)
    {
        case 0:
            echo <<<FAS
            <div class="d-flex align-items-center justify-content-center">
                <i class="fas fa-check-circle fs-6 text-success"></i>
                <div class="text-success fs-6 ms-2">Item is in good condition</div>
            </div>
            FAS;
            break;
        case 1:
            echo <<<FAS
            <div class="d-flex align-items-center justify-content-center bg-amber-300 py-1 px-3 rounded-7">
                <i class="fas fa-exclamation-triangle fs-6"></i>
                <div class="fs-6 ms-2">Item is low on stocks</div>
            </div>
            FAS;
            break;
        case 2:
            echo <<<FAS
            <div class="d-flex align-items-center justify-content-center expired-filter py-1 px-3 rounded-7">
                <i class="fas fa-exclamation-triangle fs-6"></i>
                <div class="fs-6 ms-2">Item has expired stocks</div>
            </div>
            <div class="ms-3 py-1 px-2 border border-2 rounded-7 expired-show-all">
                <i class="fas fa-undo me-1"></i>
                Show All
            </div>
            FAS;
            break;
    } 
}

function loadStocks()
{
    global $stocksDataset, $stockFields, $dataset, $security;

    foreach($stocksDataset as $row)
    {
        $sku = $row[$stockFields->sku];
        $qty = $row[$stockFields->quantity];
        $date = Dates::toString($row[$stockFields->dateCreated], "M. d, Y");
        $expiry = !empty($row[$stockFields->expiry_date]) ? Dates::toString($row[$stockFields->expiry_date], "M. d, Y") : "None";
        
        $action = <<<BTN
        <button type="button" class="btn btn-edit-expiry px-2 py-1 rounded-5 btn-secondary">Edit</button>
        BTN;

        $totalQty = $qty." ".$dataset['measurement'];

        if (Dates::isPast($row[$stockFields->expiry_date]))
        {
            $expiry = <<<SPAN
            <span class="bg-red text-white rounded-5 px-2 py-1">Expired</span>
            SPAN;

            $action = <<<BTN
            <button type="button" class="btn btn-discard px-2 py-1 rounded-5 bg-red-light font-red-dark">Discard</button>
            BTN;
        }

        $stockKey = $security->Encrypt($row[$stockFields->id]);

        echo <<<TR
        <tr>
            <td>$date</td>
            <td class="item-sku">$sku</td>
            <td>$qty</td>
            <td>$expiry</td>
            <td>$action</td>
            <td class="d-none">
                <input type="text" class="item-qty" value="$totalQty" />
                <input type="text" class="stock-key" value="$stockKey" />
            </td>
        </tr>
        TR;
    }
}

function getSuccessMessage()
{
    $message = "";

    if (isset($_SESSION['discard-action-success']))
    {
        $message = $_SESSION['discard-action-success'];
        unset($_SESSION['discard-action-success']);
    }

    echo $message;
}