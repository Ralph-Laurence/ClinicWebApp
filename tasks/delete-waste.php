<?php
@session_start();

require_once("rootcwd.php");

require_once($rootCwd . "database/configs.php");
require_once($rootCwd . "includes/system.php");
require_once($rootCwd . "includes/utils.php"); 
require_once($rootCwd . "includes/urls.php"); 
require_once($rootCwd . "database/dbhelper.php");

require_once($rootCwd . "models/Waste.php");  

require_once($rootCwd . "includes/Auth.php");
require_once($rootCwd . "includes/Security.php");
 
use Models\Waste;

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
    $id = $security->Decrypt($delete_key);
      
    // Delete the item from the waste
    $db->delete($pdo, TableNames::waste, [Waste::getFields()->id => $id]);

    Response::Redirect((ENV_SITE_ROOT . Pages::WASTE), Response::Code200, 
    "A record was successfully removed.",
    "waste-action-success");
    exit; 
}
catch (\Exception $ex) { onError(); }
catch (\Throwable $ex) { onError(); }

function onError()
{
    IError::Throw(500);
    exit;
}