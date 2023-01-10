<?php
@session_start();

require_once("rootcwd.php");

require_once($rootCwd . "database/configs.php");
require_once($rootCwd . "includes/system.php");
require_once($rootCwd . "includes/utils.php"); 
require_once($rootCwd . "includes/urls.php"); 
require_once($rootCwd . "database/dbhelper.php");

require_once($rootCwd . "library/defuse-crypto.phar");

use Defuse\Crypto\Crypto;
use Defuse\Crypto\Key;

// for encryption/decryption
$defuseKey_Ascii = Helpers::getDefuseKey($pdo);
$defuseKey = Key::loadFromAsciiSafeString($defuseKey_Ascii);

// make sure that this script file will only execute when accessed
// with POST amd AJAX request.
if ($_SERVER['REQUEST_METHOD'] != 'POST')
{
    http_response_code(404);
    die();
}

// wraps basic sql functions like SELECT, INSERT etc..
$db = new DbHelper($pdo);

$wasteTable = TableNames::$waste;
$itemsTable = TableNames::$items;
$unitsTable = TableNames::$unit_measures;

$itemKey = $_POST['itemKey'] ?? "";
$mode = $_POST['actionMode'] ?? "";

// valid modes
$modes = [0, 1, 2];
 
// if there were empty values or the mode supplied is invalid,
// stop the execution
if (empty($itemKey) || $mode == '' || (!in_array($mode, $modes)))
{
    throw_response_code(400);
    exit();
}

try
{ 
    // decrypt the item key (id) into plain text.
    // When mode == 2 (dispose all), we don't need to decrypt the key
    $itemId = $mode != '2' ? Crypto::decrypt($itemKey, $defuseKey) : '-1';
     
    //
    // Waste action modes: 
    // 0 -> restore
    // 1 -> dispose
    // 2 -> dispose all
    // 

    // create the appropriate query based on mode
    switch(intval($mode))
    {
        case 0: // RESTORE SELECTED WASTE
 
            restoreItem($pdo, $itemsTable, $itemId, $amount);
            break;

        case 1: // DISPOSE SELECTED WASTE

            disposeItem($pdo, $wasteTable, $itemId);
            break;
        
        case 2: // DISPOSE ALL

            disposeAll($pdo, $wasteTable);
            break;

    } 
 
    throw_response_code(200, Navigation::$URL_RESTOCK);
    exit();
}
catch (Exception $ex)   
{ 
    throw_response_code(500);
    exit();
} 

function restoreItem($pdo, $itemsTable, $itemId, $amount)
{  
    global $db;
    global $pdo;
    global $wasteTable;
    global $unitsTable;

    // get the item's amount from waste table matching the given item id
    $amount = $db->getValue($pdo, $wasteTable, "amount", $itemId);

    // return back the stock
    $sql = "UPDATE $itemsTable SET remaining = (remaining + ?) WHERE id = ?";
    $sth = $pdo->prepare($sql);  
    $sth->bindValue(1, $amount);
    $sth->bindValue(2, $itemId);
    $sth->execute();

    // then, remove the record from waste table
    $db->delete($pdo, $wasteTable, 
    [
        "item_id" => $itemId
    ]);

    // find just the item name and unit measure. 
    // We will use these later for session message 
 
    $sql = "SELECT i.item_name, u.measurement FROM $itemsTable i LEFT JOIN $unitsTable u ON u.id = i.unit_measure WHERE i.id = ?";
    $sth = $pdo->prepare($sql); 
    $sth->bindValue(1, $itemId);
    $sth->execute();

    $itemDetails = $sth->fetch(PDO::FETCH_ASSOC);
    $itemName = $itemDetails['item_name'];
    $unitMeasure = $itemDetails['measurement'];

    $_SESSION['restockMessage'] = "&plus;$amount $unitMeasure(s) was restored to $itemName.";
}

function disposeItem($pdo, $wasteTable, $itemId)
{  
    $sql = "DELETE FROM $wasteTable WHERE item_id = ?";
    $sth = $pdo->prepare($sql);  
    $sth->bindValue(1, $itemId);
    $sth->execute();

    $_SESSION['restockMessage'] = "A stock was successfully disposed.";
}

function disposeAll($pdo, $wasteTable)
{
    global $db;
    $db->truncate($pdo, $wasteTable);
    $_SESSION['restockMessage'] = "Inventory waste was cleared.";
}