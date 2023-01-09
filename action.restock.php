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

$itemsTable = TableNames::$items;
$wasteTable = TableNames::$waste;

$itemKey = $_POST['itemKey'] ?? "";
$amount = $_POST['amount'] ?? "";
$mode = $_POST['actionMode'] ?? "";
$addToWaste = $_POST['isAddToWaste'] ?? "";
 
if (empty($itemKey) || empty($amount) || $mode == '')
{
    throw_response_code(400);
    exit();
}
 
try
{ 
    // decrypt the item key (id) into plain text
    $itemId = Crypto::decrypt($itemKey, $defuseKey);

    // find just the item name and unit measure. 
    // We will use these later for session message 

    $sql = "SELECT i.item_name, u.measurement FROM $itemsTable i LEFT JOIN unit_measures u ON u.id = i.unit_measure WHERE i.id = ?";
    $sth = $pdo->prepare($sql); 
    $sth->bindValue(1, $itemId);
    $sth->execute();

    $itemDetails = $sth->fetch(PDO::FETCH_ASSOC);
    $itemName = $itemDetails['item_name'];
    $unitMeasure = $itemDetails['measurement'];

    // re-initialize (clear) the sql variable
    $sql = "";

    // create the appropriate query based on mode
    switch($mode)
    {
        case 0: // STOCK IN
            $sql = "UPDATE $itemsTable SET remaining = (remaining + ?) WHERE id = ?";
            break;
        case 1: // STOCK OUT
            $sql = "UPDATE $itemsTable SET remaining = (remaining - ?) WHERE id = ?";
            break;
    }

    $sth = $pdo->prepare($sql);
    $sth->bindValue(1, $amount);
    $sth->bindValue(2, $itemId);
    $sth->execute();

    // only add to waste on stock out mode.
    // if (flagged as add to waste, then add the item to waste)
    if ($mode == 1 && $addToWaste == '1')
    {
        $db->insert($pdo, $wasteTable, 
        [
            "item_id"       => $itemId,
            "amount"        => $amount
        ]);
    }

    // create the appropriate session message based on mode
    switch($mode)
    {
        // STOCK IN
        case 0: 
            $_SESSION['restockMessage'] = "$itemName was successfully restocked with &plus;$amount $unitMeasure(s).";
            break;
        
        // STOCK OUT
        case 1: 
            $strAddToWaste = ($addToWaste == '1') ? "and has been added to waste." : "";
            $_SESSION['restockMessage'] = "$amount $unitMeasure(s) was pulled out from $itemName $strAddToWaste";
            break;
    }
 
    throw_response_code(200, Navigation::$URL_RESTOCK);
    exit();
}
catch (Exception $ex)   
{
    //echo $ex->getMessage();
    throw_response_code(500);
    exit();
} 