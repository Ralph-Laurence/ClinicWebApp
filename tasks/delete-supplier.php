<?php
@session_start();

require_once("rootcwd.php");

require_once($rootCwd . "database/configs.php");
require_once($rootCwd . "includes/system.php");
require_once($rootCwd . "includes/utils.php"); 
require_once($rootCwd . "includes/urls.php"); 
require_once($rootCwd . "database/dbhelper.php");
require_once($rootCwd . "models/Supplier.php"); 

require_once($rootCwd . "includes/Auth.php");
require_once($rootCwd . "includes/Security.php");
 
use Models\Supplier;

$security = new Security();
$security->requirePermission(Chmod::PK_SUPPLIERS, Chmod::FLAG_WRITE);
$security->checkAccess(Chmod::PK_SUPPLIERS, UserAuth::getId());

// make sure that this script will only execute with POST request.
$security->BlockNonPostRequest();

// wraps basic sql functions like SELECT, INSERT etc..
$db = new DbHelper($pdo);

$delete_key = $_POST['delete-key'] ?? "";
 
if (empty($delete_key))
{
    throw_response_code(400);
    exit();
}

try
{ 
    // Decrypt supplier key into plain string
    $id = $security->Decrypt($delete_key);
    
    // Delete the supplier with matching supplier id
    $db->delete($pdo, TableNames::suppliers, [Supplier::getFields()->id => $id]);
 
    Response::Redirect((ENV_SITE_ROOT . Pages::SUPPLIERS), Response::Code200,
        "A supplier was removed successfully.",
        'supplier-actions-success-msg'
    );
    exit;
}
catch (Exception $ex) { onError(); }

function onError()
{
    throw_response_code(500);
    exit;
}