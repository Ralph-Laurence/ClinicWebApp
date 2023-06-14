<?php
@session_start();

require_once("rootcwd.php");

require_once($rootCwd . "database/configs.php");
require_once($rootCwd . "database/dbhelper.php"); 

require_once($rootCwd . "includes/utils.php"); 
require_once($rootCwd . "includes/urls.php"); 

require_once($rootCwd . "includes/Security.php");
require_once($rootCwd . "includes/Auth.php");

require_once($rootCwd . "errors/IError.php");
 
// for encryption/decryption
$security = new Security();

// make sure that this script will only execute with POST request.
$security->BlockNonPostRequest();
 
// Only Admin and Super Admin are allowed to change max days settings
if (UserAuth::getRole() == UserRoles::STAFF)
{
    IError::Throw(Response::Code403);
    exit;
}

$new_days = $_POST['new-max-days'] ?? "";

if (empty($new_days))
{
    onError();
}
 
try 
{ 
    for ($i = 1; $i <= 3; $i++)
    {
        $days["max$i"] = $i;
    }
  
    // Make sure that the day value exists in array of days
    if (!array_key_exists($new_days, $days))
    {
        onError();
    }

    $set = new SettingsIni();
    $set->UpdateSection($set->sect_General, $set->iniKey_LockEditAfter, $days[$new_days]);
    
    Response::Redirect
    (
        (ENV_SITE_ROOT . Pages::SETTINGS),
        Response::Code200,
        "Max days successfully changed.",
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
 