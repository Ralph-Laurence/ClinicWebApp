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

$new_year = $_POST['new-record-year'] ?? "";

if (empty($new_year))
{
    onError();
}
 
try 
{ 

    $startingYear = 2022;
    $year = $startingYear;

    for ($i = 0; $i < 10; $i++)
    {
        $years["y$i"] = $year;
        $year++;
    }
 
    // Make sure that the year value exists in array of years
    if (!array_key_exists($new_year, $years))
    {
        onError();
    }

    $set = new SettingsIni();
    $ini = $set->Read();

    $set->UpdateSection($set->sect_General, $set->iniKey_RecordYear, $years[$new_year]);
     
    $initiator = $_POST['setting-initiator'] ?? "";
    $goBack = (ENV_SITE_ROOT . Pages::SETTINGS);

    if (!empty($initiator) && $security->Decrypt($initiator) == "history")
    {
        Response::Redirect((ENV_SITE_ROOT . Pages::CHECKUP_RECORDS), Response::Code200);
        exit;
    }

    Response::Redirect($goBack, Response::Code200, "Record year successfully changed.", 'settings-action-success');
    exit; 
} 
catch (\Exception $ex) { onError(); }
catch (\Throwable $th) { onError(); }

function onError()
{
    IError::Throw(500);
    exit;
}
 