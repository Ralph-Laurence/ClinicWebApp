<?php
@session_start();

require_once("rootcwd.php");

require_once($rootCwd . "database/configs.php");
require_once($rootCwd . "includes/system.php");
require_once($rootCwd . "includes/utils.php"); 
require_once($rootCwd . "includes/urls.php"); 
require_once($rootCwd . "database/dbhelper.php");
require_once($rootCwd . "models/Checkup.php"); 

require_once($rootCwd . "includes/Auth.php");
require_once($rootCwd . "includes/Security.php"); 

use TableFields\CheckupFields; 

$security = new Security();
$security->requirePermission(Chmod::PK_MEDICAL, Chmod::FLAG_WRITE);
$security->checkAccess(Chmod::PK_MEDICAL, UserAuth::getId());

// make sure that this script will only execute with POST request.
$security->BlockNonPostRequest();

// wraps basic sql functions like SELECT, INSERT etc..
$db = new DbHelper($pdo);

$delete_key = $_POST['record-key'] ?? "";
 
if (empty($delete_key))
{
    throw_response_code(400);
    exit();
}

try
{ 
    // Decrypt record key into plain string
    $recordId = $security->Decrypt($delete_key);
    
    // Delete the record with matching record id
    // This will also delete any related prescription record
    $db->delete($pdo, TableNames::checkup_details, [CheckupFields::$id => $recordId]);

    $_SESSION['checkup-records-action-success'] = "A record was removed successfully.";

    throw_response_code(200, (ENV_SITE_ROOT . Pages::CHECKUP_RECORDS));
    exit;
}
catch (Exception $ex)   
{
    throw_response_code(500);
    exit;
}