<?php
@session_start();

require_once("rootcwd.php");

require_once($rootCwd . "database/configs.php");
require_once($rootCwd . "database/dbhelper.php"); 

require_once($rootCwd . "includes/utils.php"); 
require_once($rootCwd . "includes/urls.php"); 

require_once($rootCwd . "includes/Security.php");
require_once($rootCwd . "includes/Auth.php");

require_once($rootCwd . "errors/IError.php");
require_once($rootCwd . "models/Item.php");
require_once($rootCwd . "models/UnitMeasure.php");
require_once($rootCwd . "models/Waste.php");

use Models\Item;
use Models\UnitMeasure;
use Models\Waste;

// for encryption/decryption
$security = new Security();

// make sure that this script will only execute with POST request.
$security->BlockNonPostRequest();
                        
$security->requirePermission(Chmod::PK_INVENTORY, Chmod::FLAG_WRITE);
$security->checkAccess(Chmod::PK_INVENTORY, UserAuth::getId()); 

global $pdo;

// wraps basic sql functions like SELECT, INSERT etc..
$db = new DbHelper($pdo);

$stockQty   = $_POST['qty']       ?? "";
$itemId     = $_POST['item-key']  ?? "";

if (empty($itemId) || empty($stockQty))
{
    onError();
}

function onError()
{
    IError::Throw(500);
    exit;
}

try 
{
    $id = $security->Decrypt($itemId);
 
    $inventory  = TableNames::inventory;
    $itemFields = Item::getFields();
    $unitFields = UnitMeasure::getFields();
 
    // MOVE THE STOCK ONTO THE WASTE. 
    // This also decrease the inventory stock
    if ( isset($_POST['moveToWaste']) && $_POST['moveToWaste'] == '1' )
    { 
        // STOCKOUT REASON
        $reasonKey  = $_POST['wasteReason'] ?? "";
        $reason = "Other";

        if (!empty($reasonKey)) 
        {
            $reasonId = $security->Decrypt($reasonKey);
            $reason   = Item::getStockoutReason($reasonId);
        } 

        $waste = new Waste($db);
        $waste->moveStockToWaste($id, $stockQty, $reason);
    }
    else
    { 
        // DECREASE THE INVENTORY STOCK
        $sql = "UPDATE $inventory SET $itemFields->remaining = ($itemFields->remaining - ?) WHERE $itemFields->id = ?";
        $sth = $pdo->prepare($sql)->execute([$stockQty, $id]);
    }

    // SUCCESS MESSAGE
    $sql = "SELECT i.$itemFields->itemName, u.$unitFields->measurement FROM $inventory AS i ".
    " LEFT JOIN ". TableNames::unit_measures ." AS u ON u.$unitFields->id = i.$itemFields->unitMeasure". 
    " WHERE i.$itemFields->id = $id";

    $itemInfo = $db->fetchAll($sql, true);

    $response = "&minus;$stockQty stock(s) pulled out from an item.";

    if (!empty($itemInfo))
    {
        $itemName = $itemInfo[$itemFields->itemName];
        $measures = $itemInfo[$unitFields->measurement];

        $grammar  = $stockQty == 1 ? "$measures was" : "$measures(s) were";
        $response = "&minus;$stockQty $grammar pulled out from $itemName.";
    }

    Response::Redirect
    (
        (ENV_SITE_ROOT . Pages::STOCK_OUT),
        Response::Code200,
        $response,
        'stockout-action-success'
    );
    exit;  
} 
catch (\Exception $ex) 
{ 
    echo $ex->getMessage(); exit;
    onError();
}
//catch (\Throwable $th) 
//{
    // onError();
//}
