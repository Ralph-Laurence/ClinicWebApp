<?php

use Models\Checkup;

@session_start();

require_once("rootcwd.php");

require_once($rootCwd . "database/configs.php");
require_once($rootCwd . "database/dbhelper.php"); 

require_once($rootCwd . "includes/utils.php"); 
require_once($rootCwd . "includes/urls.php"); 

require_once($rootCwd . "models/Checkup.php");
require_once($rootCwd . "includes/Security.php");
require_once($rootCwd . "includes/Auth.php");

require_once($rootCwd . "errors/IError.php");
 
// for encryption/decryption
$security = new Security();

// make sure that this script will only execute with POST request.
$security->BlockNonPostRequest();
 
// Only Admin and Super Admin are allowed to change these settings
if (UserAuth::getRole() == UserRoles::STAFF)
{
    IError::Throw(Response::Code403);
    exit;
}

$new_action = $_POST['new-form-action'] ?? "";

if (empty($new_action))
{
    onError();
}
  
try 
{  
    $actions = 
    [
        Checkup::ON_COMPLETE_PREVIEW,
        Checkup::ON_COMPLETE_STAY
    ];

    // Make sure that the action value exists in array of actions
    if (!in_array($new_action, $actions))
    {
        onError();
    }

    $set = new SettingsIni();
    $set->UpdateSection($set->sect_General, $set->iniKey_CheckupComplete, $new_action);
    
    Response::Redirect
    (
        (ENV_SITE_ROOT . Pages::SETTINGS),
        Response::Code200,
        "Checkup form action successfully changed.",
        'settings-action-success'
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
 