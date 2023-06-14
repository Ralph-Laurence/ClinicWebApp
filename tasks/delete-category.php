<?php
@session_start();

require_once("rootcwd.php");

require_once($rootCwd . "database/configs.php");
require_once($rootCwd . "includes/system.php");
require_once($rootCwd . "includes/utils.php"); 
require_once($rootCwd . "includes/urls.php"); 
require_once($rootCwd . "database/dbhelper.php");
require_once($rootCwd . "models/Category.php"); 

require_once($rootCwd . "includes/Auth.php");
require_once($rootCwd . "includes/Security.php");
 
use Models\Category;

$security = new Security();
$security->requirePermission(Chmod::PK_MAINTENANCE, Chmod::FLAG_WRITE);
$security->checkAccess(Chmod::PK_MAINTENANCE, UserAuth::getId());

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
    // Decrypt category key into plain string
    $id = $security->Decrypt($delete_key);
    
    // Delete the category with matching category id
    $db->delete($pdo, TableNames::categories, [Category::getFields()->id => $id]);
 
    Response::Redirect((ENV_SITE_ROOT . Pages::CATEGORIES), Response::Code200,
        "An item category was successfully removed from the records.",
        'category-actions-success-msg'
    );
    exit;
}
catch (Exception $ex) { onError(); }

function onError()
{
    throw_response_code(500);
    exit;
}