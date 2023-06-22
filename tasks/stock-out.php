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
require_once($rootCwd . "models/Stock.php");

use Models\Item;
use Models\Stock;
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

$payload = $_POST['payload'] ?? "";

if (empty($payload)) {
    onError();
}

$data = json_decode($payload, true);

// Check each values in the payload if there were empty.
// Those ones importante are the stock key, item key and amount
if (Utils::hasEmpty([ $data['stockKey'], $data['itemKey'], $data['amount'] ])) {
    onError();
}

// $stockQty   = $_POST['qty']       ?? "";
// $itemId     = $_POST['item-key']  ?? "";

// if (empty($itemId) || empty($stockQty))
// {
//     onError();
// }

try 
{
    // Decrypt the ids
    $itemId  = $security->Decrypt($data['itemKey'] );
    $stockId = $security->Decrypt($data['stockKey']);

    // Collect inputs 
    $amount = $data['amount'];

    $pulloutAll = $data['pulloutAll']; 
    $disposeReason = $data['disposalReason'];

    $reason = "";

    if (!empty($disposeReason)) 
    {
        $reasonId = $security->Decrypt($disposeReason);

        $reason = Item::StockoutReasons[$reasonId]['reason'];
    }

    // Rules:
    // if no dispose reason, do not move to waste 
    // if $pullout all stocks = 1 (true), ignore the amount input,
    // then just pullout immediately
    // Otherwise, subtract from the input amount

    $stoxTable = TableNames::stock;
    
    $s = Stock::getFields();

    // Data for holding the value that is needed to be saved into waste
    //$wasteDataset = [];
    $wasteDataset =
    [
        'itemId' => $itemId,
        'stockId' => $stockId,
        'amount' => $amount,
        'reason' => $reason
    ];

    switch($pulloutAll)
    {
        case 0: // FALSE
             
            // Stockout
            $stmt_stockout = $db->getInstance()->prepare
            (
                "UPDATE $stoxTable 
                    SET $s->quantity = ($s->quantity - ?) 
                WHERE $s->item_id = ? AND $s->id = ?"
            );

            // Waste
            if (!empty($reason)) {
                moveToWaste($wasteDataset);
            }

            $stmt_stockout->execute([$amount, $itemId, $stockId]);
  
            break;
        
        case 1: // TRUE
 
            $empty = 0;

            // Stock
            $db->update($stoxTable, [ $s->quantity => $empty ], 
            [
                $s->item_id => $itemId,
                $s->id => $stockId
            ]);

            // Waste
            if (!empty($reason)) {
                moveToWaste($wasteDataset);
            }
 
            // Delete stock from records
            $db->delete($db->getInstance(), $stoxTable, 
            [
                $s->id => $stockId
            ]);
 
            break;
    }  
    // Prepare the query to update the inventory table's quantities
    // which are the total quantities of each stocks with matching
    // item Id from the stocks table  

    $inventory = TableNames::inventory;
    $invFields = Item::getFields();

    $stmt_update_inventory = $db->getInstance()->prepare
    ( 
        "UPDATE $inventory AS i
        SET i.$invFields->remaining = 
        (
            SELECT SUM(s.$s->quantity)
            FROM $stoxTable AS s
            WHERE s.$s->item_id = i.$invFields->id
        )
        WHERE i.$invFields->id = ?;"
    );

    $stmt_update_inventory->execute([$itemId]);

    Response::Redirect
    (
        (ENV_SITE_ROOT . Pages::STOCK_OUT),
        Response::Code200,
        "A stock was successfully pulled out",
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

function onError()
{
    IError::Throw(500);
    exit;
}

function moveToWaste($datasource)
{
    global $db, $s;

    $itemId = $datasource['itemId'];
    $stockId = $datasource['stockId'];
    $amount = $datasource['amount']; 
    $reason = $datasource['reason'];

    $wasteTable = TableNames::waste;
    $w = Waste::getFields();

    $stmt_move_toWaste = $db->getInstance()->prepare
    (
        "INSERT INTO $wasteTable($w->itemId, $w->amount, $w->reason, $w->sku) VALUES(?,?,?,?)"
    ); 

    $sku = $db->getValue(TableNames::stock, $s->sku, [$s->id => $stockId, $s->item_id => $itemId]);

    $stmt_move_toWaste->execute([$itemId, $amount, $reason, $sku]);
}