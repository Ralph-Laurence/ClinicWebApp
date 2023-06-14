<?php
@session_start();

require_once("rootcwd.php");

require_once($rootCwd . "database/configs.php");
require_once($rootCwd . "includes/system.php");
require_once($rootCwd . "includes/utils.php"); 
require_once($rootCwd . "includes/urls.php"); 
require_once($rootCwd . "database/dbhelper.php");

require_once($rootCwd . "models/Item.php"); 
require_once($rootCwd . "models/Prescription.php"); 

require_once($rootCwd . "includes/Auth.php");
require_once($rootCwd . "includes/Security.php");

use Models\Item;
use Models\Prescription;

$security = new Security();
$security->requirePermission(Chmod::PK_INVENTORY, Chmod::FLAG_WRITE);
$security->checkAccess(Chmod::PK_INVENTORY, UserAuth::getId());

// make sure that this script will only execute with POST request.
$security->BlockNonPostRequest();

// wraps basic sql functions like SELECT, INSERT etc..
$db = new DbHelper($pdo);

$delete_key = $_POST['delete-key'] ?? "";
 
if (empty($delete_key))
    onError();

try
{ 
    // Decrypt item key into plain string
    $itemId = $security->Decrypt($delete_key);
    
    // Delete the item from the inventory
    $db->delete($pdo, TableNames::inventory, [Item::getFields()->id => $itemId]);

    // Delete the item from the prescriptions
    $db->delete($pdo, TableNames::prescription_details, [Prescription::getFields()->itemId => $itemId]);

    $_SESSION['items-actions-success-msg'] = "An item was successfully removed from the inventory.";

    throw_response_code(200, (ENV_SITE_ROOT . Pages::MEDICINE_INVENTORY));
    exit;
}
catch (\Exception $ex) { onError(); }
catch (\Throwable $ex) { onError(); }

function onError()
{
    IError::Throw(500);
    exit;
}