<?php
require_once("rootcwd.inc.php");

require_once($cwd . "database/configs.php");
require_once($cwd . "includes/system.php");
require_once($cwd . "includes/utils.php"); 
require_once($cwd . "database/dbhelper.php");

require_once($cwd . "library/defuse-crypto.phar");

use Defuse\Crypto\Crypto;
use Defuse\Crypto\Key;

// for encryption/decryption
$defuseKey_Ascii = Helpers::getDefuseKey($pdo);
$defuseKey = Key::loadFromAsciiSafeString($defuseKey_Ascii);
   
$request = new Requests();

// make sure that this script file will only execute when accessed
// with POST amd AJAX request.
if ($_SERVER['REQUEST_METHOD'] != 'POST' || !$request->isAjax())
{
    http_response_code(404);
    die();
}

// we will use this to identify return codes
$returnCodes = 
[
    "badInput" => "0x400",
    "serverError" => "0x500",
    "success" => "0x000",
    "uniqueItem"  => "0x501",
    "uniqueCode"  => "0x502"
];
 
// wraps basic sql functions like SELECT, INSERT etc..
$db = new DbHelper($pdo);

$itemsTable = TableNames::$items;

// form data payload from AJAX is required.
// if not supplied, immediately stop the 
// execution
if (!isset($_POST['formData']))
{
    echo $returnCodes["badInput"];
    http_response_code(400);
    exit;
}

// the form data is an JSON encoded data
$formData = $_POST['formData'];

// we must decode it into an associative array
$payload = json_decode($formData, true);

if (empty($payload))
{
    echo $returnCodes["badInput"];
    http_response_code(400);
    exit;
}
  
// get the values from decoded json
$itemKey = $payload['itemKey'];
$itemName = $payload['itemName'];
$itemCode = $payload['itemCode'];
$reserveStock = $payload['reserveStock'];
$remarks = $payload['remarks'] ?? "";

// decrypted encrypted values
$category = $payload['category'];
$units = $payload['units'];
$supplier = $payload['supplier'];

// make sure that the category, unit measures and suppliers
// are not empty. If empty, stop the execution
if (empty($category) || empty($units) || empty($supplier))
{
    echo $returnCodes["badInput"];
    http_response_code(400);
    exit;
}

try 
{
    // decrypt the values to human readable / database compatible values
    $dec_category = Crypto::decrypt($category, $defuseKey);
    $dec_units = Crypto::decrypt($units, $defuseKey);
    $dec_supplier = Crypto::decrypt($supplier, $defuseKey);  
    
    $itemId = Crypto::decrypt($itemKey, $defuseKey);  
    
    // if supplier is 'none', convert it to 0
    if ($dec_supplier == 'none')
        $dec_supplier = 0;

    $data = 
    [
        "item_name"         => $itemName,
        "item_code"         => $itemCode,
        "item_category"     => $dec_category,
        "unit_measure"      => $dec_units,
        "supplier_id"       => $dec_supplier,
        "critical_level"    => $reserveStock,
        "remarks"           => $remarks
    ];

    $condition = ["id" => $itemId];
     
    $db->update($pdo, $itemsTable, $data, $condition);
    
    echo $returnCodes["success"];
    exit;
} 
catch (Exception $th) 
{ 
    if (Helpers::strContains($th->getMessage(), "for key 'item_name'"))
    {
        echo $returnCodes["uniqueItem"];
    }
    else if (Helpers::strContains($th->getMessage(), "for key 'item_code'"))
    {
        echo $returnCodes["uniqueCode"];
    }
    else 
    {
        echo $returnCodes["serverError"];
    }
    
    http_response_code(500);
    exit;
}

exit;