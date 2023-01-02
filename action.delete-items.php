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

// The item keys are stored as JSON string
$itemKeys = $_POST['item-keys'] ?? "";
 
if (empty($itemKeys))
{
    throw_response_code(400);
    exit();
}

try
{
    // decode the JSON data into assoc array
    $data = json_decode($itemKeys, true); 

    // item id(s)
    $itemIds = array();

    // every item key is encrypted .. we should
    // decode these to plain string values 
    foreach($data as $k => $v)
    {
        $itemId = Crypto::decrypt($v, $defuseKey);
        array_push($itemIds, $itemId);
    }

    // decrypt the item key (id) into plain text
    //
    //$condition = ["id" => $itemId];

    $db->deleteWhereIn($pdo, $itemsTable, "id", $itemIds);
    
    $_SESSION['delete-items-success'] = true;
    $_SESSION['delete-items-status'] = "0x0";

    throw_response_code(200, Navigation::$URL_STOCKS_INVENTORY);
    exit();
}
catch (Exception $ex)   
{
    throw_response_code(500);
    exit();
}