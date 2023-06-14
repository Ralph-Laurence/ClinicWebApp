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
 
// Only Admin and Super Admin are allowed to change year settings
if (UserAuth::getRole() == UserRoles::STAFF)
{
    IError::Throw(Response::Code403);
    exit;
}
  
$doctor_key = $_POST['record-key'] ?? "";
 
try 
{ 
    $doctor_id = $security->Decrypt($doctor_key);
     
    $set = new SettingsIni();

    $set->UpdateSection($set->sect_General, $set->iniKey_DefaultDoctor, $doctor_id);
    
    Response::Redirect
    (
        (ENV_SITE_ROOT . Pages::DOCTORS), Response::Code200,
        "Default doctor successfully changed.",
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
 