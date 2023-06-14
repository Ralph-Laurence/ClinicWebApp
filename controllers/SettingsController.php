<?php

use Models\Checkup;

@session_start();

require_once("rootcwd.inc.php");

global $rootCwd;

require_once($rootCwd . "includes/system.php");
require_once($rootCwd . "includes/utils.php"); 
require_once($rootCwd . "includes/urls.php");

require_once($rootCwd . "includes/Auth.php");
require_once($rootCwd . "includes/Security.php");
require_once($rootCwd . "errors/IError.php");

require_once($rootCwd . "models/Checkup.php");
require_once($rootCwd . "MasterLayout.php");
require_once($rootCwd . "layout-header.php");

$security = new Security(); 

// Only Admin and Super Admin are allowed to change max days settings
if (UserAuth::getRole() == UserRoles::STAFF)
{
    IError::Throw(Response::Code403);
    exit;
}

$set = new SettingsIni();

try 
{
    $ini = $set->Read();
} 
catch (\Throwable $th) 
{
    die("Failed to read configuration data.");
}
 
function getSuccessMessage()
{
    $message = "";

    if (isset($_SESSION['settings-action-success']))
    {
        $message = $_SESSION['settings-action-success'];
        unset($_SESSION['settings-action-success']);
    }

    return $message;
}
 
function prefillYears()
{
    global $set;

    // Load year from ini file
    $loadYear = $set->GetValue($set->sect_General, $set->iniKey_RecordYear);
 
    $startYear = 2022; 
    $year = $startYear;

    for ($i = 0; $i < 10; $i++)
    {
        // select current year
        $selected = $year == $loadYear ? "selected" : "";

        echo <<<OPTION
        <option value="y$i" $selected>$year</option>
        OPTION;

        $year++;
    }

}

function prefillEditMaxDays()
{
    global $set;

    $loadMaxDays = $set->GetValue($set->sect_General, $set->iniKey_LockEditAfter);

    for ($i = 1; $i <= 3; $i++ )
    {
        $day = $i == 1 ? "Day" : "Days";
        $selected = $i == $loadMaxDays ? "selected" : "";

        echo <<<OPTION
        <option value="max$i" $selected>$i $day</option>
        OPTION;
    }
}

function prefillFormActions()
{
    global $set;
    $actionOnComplete = $set->GetValue($set->sect_General, $set->iniKey_CheckupComplete);
    $preview = Checkup::ON_COMPLETE_PREVIEW;
    $stay = Checkup::ON_COMPLETE_STAY;

    if ($actionOnComplete == $stay)
    {
        echo <<<OPTION
        <option value="$stay" selected>Stay</option>
        <option value="$preview">Preview</option>
        OPTION;

        return;
    }

    echo <<<OPTION
    <option value="$stay">Stay</option>
    <option value="$preview" selected>Preview</option>
    OPTION;
}

function getAppVersion()
{
    global $set;

    $appVersion = $set->GetValue($set->sect_SysInfo, $set->iniKey_AppVersion);
    return "v" .$appVersion;
}