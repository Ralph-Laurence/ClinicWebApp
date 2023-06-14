<?php

use Models\Doctor;

@session_start();

require_once("rootcwd.php");

require_once($rootCwd . "database/configs.php");
require_once($rootCwd . "includes/system.php");
require_once($rootCwd . "includes/utils.php"); 
require_once($rootCwd . "includes/urls.php"); 
require_once($rootCwd . "database/dbhelper.php");
require_once($rootCwd . "models/Doctor.php"); 

require_once($rootCwd . "includes/Security.php");
require_once($rootCwd . "includes/Auth.php");
  

// check if the user has enough permission to view this page.
// The required permission for this page is the 
// Patients Permission
//$perm = UserPerms::getPatientsAccess();

// Staff users are not allowed to delete records
// Show the "Access Denied" page
// if (!UserPerms::hasAccess($perm) || UserAuth::getRole() == 1)
// {
//     throw_response_code(403);
//     exit();
// }

// for encryption/decryption
$security = new Security();

// make sure that this script will only execute with POST request.
$security->BlockNonPostRequest();

// wraps basic sql functions like SELECT, INSERT etc..
$db = new DbHelper($pdo);

$record_key = $_POST['record-key'] ?? "";
 
if (empty($record_key))
    onError();

try
{ 
    // Decrypt patient key into plain string
    $docId = $security->Decrypt($record_key);
    
    $fields = Doctor::getFields();

    $db->delete($pdo, TableNames::doctors, [$fields->id => $docId]);
 
    Response::Redirect( (ENV_SITE_ROOT . Pages::DOCTORS),
        Response::Code200, 
        "A doctor was successfully removed.",
        'doc-actions-success-msg'
    );
    exit;
}
catch (\Exception $ex) { onError(); }
catch (\Throwable $th) { onError(); }

function onError()
{
    IError::Throw(500);
    exit;
}