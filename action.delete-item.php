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

$itemKey = $_POST['item-key'] ?? "";
 
if (empty($itemKey))
{
    throw_response_code(400);
    exit();
}

try
{
    // decrypt the item key (id) into plain text
    $itemId = Crypto::decrypt($itemKey, $defuseKey);
    $condition = ["id" => $itemId];

    $db->delete($pdo, $itemsTable, $condition);
    
    $_SESSION['delete-item-success'] = true;
    $_SESSION['delete-item-status'] = "0x0";

    throw_response_code(200, Navigation::$URL_STOCKS_INVENTORY);
    exit();
}
catch (Exception $ex)   
{
    echo $ex->getMessage();
    throw_response_code(500);
    exit();
}