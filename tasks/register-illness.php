<?php

use Models\Illness;

@session_start();

require_once("rootcwd.php");

require_once($rootCwd . "database/configs.php");
require_once($rootCwd . "database/dbhelper.php");

require_once($rootCwd . "includes/system.php");
require_once($rootCwd . "includes/utils.php"); 
require_once($rootCwd . "includes/urls.php"); 

require_once($rootCwd . "includes/Auth.php");
require_once($rootCwd . "includes/Security.php");

require_once($rootCwd . "errors/IError.php");
require_once($rootCwd . "models/Illness.php");

$security = new Security();
$security->requirePermission(Chmod::PK_MAINTENANCE, Chmod::FLAG_WRITE);
$security->checkAccess(Chmod::PK_MAINTENANCE, UserAuth::getId());                        // For encryption/decryption etc..
$security->BlockNonPostRequest();                   // make sure that this script file will only execute on POST

$db     = new DbHelper($pdo);                           // Db helpwer wraps CRUD operations as functions
$fields = Illness::getFields();
 
// Required Data 
$illness = $_POST['illness-name'] ?? "";

if (empty($illness))
    throwError(); 

// Optional Data 
$desc = $_POST['illness-desc'] ?? "";
 
try
{
    $data = 
    [
        $fields->name => $illness,
        $fields->description => $desc
    ];   

    $db->insert(TableNames::illness, $data);

    Response::Redirect( (ENV_SITE_ROOT . Pages::ILLNESS),
       Response::Code200, 
       "A new illness was successfully added to the records.",
       'illness-actions-success-msg'
    );
    exit;
}
catch (\Exception $ex) 
{ 
    $error = $ex->getMessage();
  
    if (IString::contains($error, "for key '$fields->name'"))
    {
        Response::Redirect((ENV_SITE_ROOT . Pages::ILLNESS),
            Response::Code500,
            "The illness \"$illness\" will be ignored as it is already registered in the system.",
            'illness-actions-error-msg'
        );
        exit;
    }
     
    throwError(); 
} 

// Throw an Error then stop the script
function throwError()
{
    IError::Throw(500);
    exit;
} 