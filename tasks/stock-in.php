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
require_once($rootCwd . "models/Stock.php");
require_once($rootCwd . "models/UnitMeasure.php");

use Models\Item;
use Models\Stock;
use Models\UnitMeasure;

// for encryption/decryption
$security = new Security();

// make sure that this script will only execute with POST request.
$security->BlockNonPostRequest();
                        
$security->requirePermission(Chmod::PK_INVENTORY, Chmod::FLAG_WRITE);
$security->checkAccess(Chmod::PK_INVENTORY, UserAuth::getId()); 

global $pdo;

// wraps basic sql functions like SELECT, INSERT etc..
$db = new DbHelper($pdo);

$stocksTable = TableNames::stock;
$stockFields = Stock::getFields();

$inventory = TableNames::inventory;
$invFields = Item::getFields();

$stockQty   = $_POST['qty']       ?? "";
$itemId     = $_POST['item-key']  ?? "";
$sku        = $_POST['sku']       ?? "";
$expiryDate = $_POST['expiry']    ?? "";
  
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

    $exp = "";

    // Expiry date (Optional) 
    if (!empty($expiryDate))
        $exp = Dates::toString($exp);

    // Insert stock 
    $db->insert($stocksTable, 
    [
        $stockFields->quantity     => $stockQty,
        $stockFields->item_id      => $id,
        $stockFields->sku         => $sku,
        $stockFields->expiry_date  => $exp
    ]); 

    // Prepare the query to update the inventory table's quantities
    // which are the total quantities of each stocks with matching
    // item Id from the stocks table  
    $stmt_update_inventory = $db->getInstance()->prepare
    ( 
        "UPDATE $inventory AS i
        SET i.$invFields->remaining = 
        (
            SELECT SUM(s.$stockFields->quantity)
            FROM $stocksTable AS s
            WHERE s.$stockFields->item_id = i.$invFields->id
        )
        WHERE i.$invFields->id = ?;"
    );

    $stmt_update_inventory->execute([$id]);

    Response::Redirect
    (
        (ENV_SITE_ROOT . Pages::STOCK_IN),
        Response::Code200,
        "An item has been successfully restocked.",
        'stockin-action-success'
    );
    exit; 

    // $inventory  = TableNames::inventory;

    // $itemFields = Item::getFields();
    // $unitFields = UnitMeasure::getFields();
 
    // // Expiry date (Optional)
    // $exp = $_POST["expiry-date"] ?? "";
        
    // if (!empty($exp))
    //     $exp = Dates::toString($exp);
 
    // $expiry = ", $itemFields->expiryDate = '$exp'";

    // $sql = "UPDATE $inventory SET $itemFields->remaining = ($itemFields->remaining + ?) $expiry WHERE $itemFields->id = ?";
 
    // $sth = $pdo->prepare($sql)->execute([$stockQty, $id]);

    // $sql = "SELECT i.$itemFields->itemName, u.$unitFields->measurement FROM $inventory AS i ".
    // " LEFT JOIN ". TableNames::unit_measures ." AS u ON u.$unitFields->id = i.$itemFields->unitMeasure". 
    // " WHERE i.$itemFields->id = $id";

    // $itemInfo = $db->fetchAll($sql, true);

    // $response = "An item has been successfully restocked.";

    // if (!empty($itemInfo))
    // {
    //     $itemName = $itemInfo[$itemFields->itemName];
    //     $measures = $itemInfo[$unitFields->measurement];

    //     $response = "$itemName was restocked with &plus;$stockQty $measures(s)";
    // }
} 
catch (\Exception $ex) 
{ 
    echo $ex->getMessage() . " on ". $ex->getLine(); exit;
    onError();
}
catch (\Throwable $ex) 
{
    echo $ex->getMessage() . " on ". $ex->getLine(); exit;
    onError();
}
