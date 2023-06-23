<?php

use Models\Item;
use Models\Stock;
use Models\Waste;

@session_start();

require_once("rootcwd.php");

require_once($rootCwd . "database/configs.php");
require_once($rootCwd . "includes/system.php");
require_once($rootCwd . "includes/utils.php"); 
require_once($rootCwd . "includes/urls.php"); 
require_once($rootCwd . "database/dbhelper.php");

require_once($rootCwd . "models/Waste.php");  
require_once($rootCwd . "models/Item.php");  
require_once($rootCwd . "models/Stock.php");  

require_once($rootCwd . "includes/Auth.php");
require_once($rootCwd . "includes/Security.php");
 
$security = new Security();
$security->requirePermission(Chmod::PK_INVENTORY, Chmod::FLAG_WRITE);
$security->checkAccess(Chmod::PK_INVENTORY, UserAuth::getId());

// make sure that this script will only execute with POST request.
$security->BlockNonPostRequest();

// wraps basic sql functions like SELECT, INSERT etc..
$db = new DbHelper($pdo);

$itemKey = $_POST['item-key'] ?? "";
$stockKey = $_POST['stock-key'] ?? "";
$referer = $_POST['referer'] ?? "";
 
try 
{
    // Decrypt the keys    
    $itemId = $security->Decrypt($itemKey);
    $stockId = $security->Decrypt($stockKey);

    $wasteTable = TableNames::waste;
    $w = Waste::getFields();

    $stoxTable = TableNames::stock;
    $s = Stock::getFields();

    $inventoryTable = TableNames::inventory;
    $i = Item::getFields();

    // Get the stock's quantity and sku
    $stmt_get_stockData = $db->getInstance()->prepare
    (
        "SELECT $s->quantity, $s->sku 
        FROM $stoxTable WHERE $s->id = ? AND $s->item_id = ?"
    );
    
    $stmt_get_stockData->execute([ $stockId, $itemId ]); 
    $stockData = $stmt_get_stockData->fetch(PDO::FETCH_ASSOC);
    
    // Data for waste table insert
    $wasteData = 
    [
        $w->amount => $stockData[$s->quantity],
        $w->reason => Item::StockoutReasons[3]['reason'],
        $w->itemId => $itemId,
        $w->sku    => $stockData[$s->sku]
    ];

    // Move the waste
    $db->insert($wasteTable, $wasteData);
 
    // Delete the stock
    $db->delete($db->getInstance(), $stoxTable, [
        $s->id => $stockId,
        $s->item_id => $itemId
    ]);

    // Update the inventory 
    $stmt_update_inventory = $db->getInstance()->prepare
    (
        "UPDATE $inventoryTable AS i
        SET i.$i->remaining = 
        (
            SELECT SUM(s.$s->quantity)
            FROM $stoxTable AS s
            WHERE s.$s->item_id = i.$i->id
        )
        WHERE i.$i->id = ?;"
    ); 

    $stmt_update_inventory->execute([$itemId]);
  
    $_SESSION['item-details-key'] = $itemKey; 
    
    Response::Redirect( (ENV_SITE_ROOT . Pages::ITEM_DETAILS), 200, 'Stock successfully discarded', 'discard-action-success');
} 
catch (\Exception $ex) 
{
    echo $ex->getMessage(); exit;

    IError::Throw(500);
    exit;
}
