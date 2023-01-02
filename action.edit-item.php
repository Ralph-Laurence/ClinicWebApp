<?php
@session_start();

require_once("rootcwd.php");

require_once($rootCwd . "database/configs.php");
require_once($rootCwd . "includes/system.php");
require_once($rootCwd . "includes/utils.php");
require_once($rootCwd . "database/dbhelper.php");

require_once($rootCwd . "library/defuse-crypto.phar");

use Defuse\Crypto\Crypto;
use Defuse\Crypto\Key;

// for encryption/decryption
$defuseKey_Ascii = Helpers::getDefuseKey($pdo);
$defuseKey = Key::loadFromAsciiSafeString($defuseKey_Ascii);

$status = ""; 

if ($_SERVER['REQUEST_METHOD'] == 'POST')
    processEdit(); 

function processEdit()
{  
    global $status;
    global $pdo;  
    global $defuseKey;

    // we will use this to wrap return codes
    $returnCodes =
    [
        "badInput" => "0x400",
        "uniqueItem"  => "0x501",
        "uniqueCode"  => "0x502"
    ];
     
    // wraps basic sql functions like SELECT, INSERT etc..
    $db = new DbHelper($pdo);

    // reference to the database table
    $itemsTable = TableNames::$items;

    // input values
    $formData =
    [
        'itemKey' => $_POST["item-key"] ?? "",
        'itemName' => $_POST["input-item-name"] ?? "",
        'itemCode' => $_POST["input-item-code"] ?? "",
        'reserveStock' => $_POST["input-reserve-stock"] ?? "",

        'category' => $_POST["input-category"] ?? "",
        'units' => $_POST["input-units"] ?? "",

        'itemPage' => $_POST['item-page'] ?? ""
    ];

    // check for empty inputs. Every required inputs
    // should not be empty
    if (empty($formData))
    {
        $status = $returnCodes["badInput"]; 
        return; 
    }

    // optional input values
    $formData['supplier'] = $_POST["input-supplier"] ?? "";
    $formData['remarks'] = $_POST["input-remarks"] ?? "";
 
    $itemPage = $formData['itemPage'];
    $itemKey = $formData['itemKey'];
    $itemName = $formData['itemName'];
    $itemCode = $formData['itemCode'];
    $reserveStock = $formData['reserveStock'];
    $remarks = $formData['remarks'];
  
    // encrypted values
    $category = $formData['category'];
    $units = $formData['units'];
    $supplier = $formData['supplier'];

    // make sure that the category, unit measures and suppliers
    // are not empty. If empty, stop the execution
    if (empty($category) || empty($units) || empty($supplier)) 
    {   
        throw_response_code(400);
        return;
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

        $_SESSION['edit_item_success'] = true;
        $_SESSION['edit_updated_item_name'] = $itemName;
        $_SESSION['edit_item_page'] = $itemPage;
        
        $redirect = ENV_SITE_ROOT . Navigation::$URL_STOCKS_INVENTORY;
        throw_response_code(200, $redirect);

        exit();
    } 
    catch (Exception $th) 
    {
        if (Helpers::strContains($th->getMessage(), "for key 'item_name'")) 
        {
            $status = $returnCodes["uniqueItem"];
            $_SESSION['lastInput_ItemName'] = $itemName;
        }
        else if (Helpers::strContains($th->getMessage(), "for key 'item_code'")) 
        {
            $status = $returnCodes["uniqueCode"];
            $_SESSION['lastInput_ItemCode'] = $itemCode;
        }
        else
        {
            throw_response_code(500);
            exit();
        }

        return;
    }
}
