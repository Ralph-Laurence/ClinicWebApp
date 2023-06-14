<?php
@session_start();

require_once("rootcwd.inc.php");

require_once($rootCwd . "database/configs.php");
require_once($rootCwd . "includes/system.php");
require_once($rootCwd . "includes/utils.php"); 
require_once($rootCwd . "database/dbhelper.php");
require_once($rootCwd . "includes/urls.php");

require_once($rootCwd . "models/Degrees.php");
require_once($rootCwd . "models/Doctor.php");
require_once($rootCwd . "models/DoctorSpecialty.php");

require_once($rootCwd . "includes/Auth.php");
require_once($rootCwd . "includes/Security.php");
require_once($rootCwd . "errors/IError.php");

require_once($rootCwd . "MasterLayout.php");
require_once($rootCwd . "layout-header.php");

use Models\DoctorSpecialty;
use Models\Degrees;
use Models\Doctor;


$security   = new Security();
$security->requirePermission(Chmod::PK_DOCTORS, Chmod::FLAG_WRITE);
$security->checkAccess(Chmod::PK_DOCTORS, UserAuth::getId());

$db         = new DbHelper($pdo);
$doctor     = new Doctor($db);
$docFields  = $doctor->getFields();
    
// Store the last input values here from the session
$lastInputs = [];

if (isset($_SESSION['reg-doc-last-inputs']))
{
    $lastInputs = $_SESSION['reg-doc-last-inputs'];
    unset($_SESSION['reg-doc-last-inputs']);
}
 
/// Load last input value after submit 
function loadLastInput($inputKey, $defaultValue = "")
{
    global $lastInputs;

    if (empty($lastInputs))
        return $defaultValue;

    return $lastInputs[$inputKey];
}

function bindDoctorSpecs()
{
    global $doctor, $security;

    $s = DoctorSpecialty::getFields();

    $specs = $doctor->getSpecializations();

    foreach($specs as $spec)
    {
        $id = $security->Encrypt($spec[$s->id]);

        echo <<<OPTION
            <option value="$id">{$spec[$s->spec]}</option>
        OPTION;
    }
}

function bindDoctorDegrees()
{
    global $doctor, $security;
    $d = Degrees::getFields();

    $degrees = $doctor->getDegrees();

    foreach($degrees as $deg)
    {
        $id = $security->Encrypt($deg[$d->id]);

        echo <<<OPTION
            <option value="$id">{$deg[$d->degree]}</option>
        OPTION;
    }
}

function getErrorMessage()
{
    $msg = "";

    if (isset($_SESSION['doc-action-error']))
    {
        $msg = $_SESSION['doc-action-error'];
        unset($_SESSION['doc-action-error']);
    }

    return $msg;
}

function getSuccessMessage()
{
    $msg = "";

    if (isset($_SESSION['doc-actions-success-msg']))
    {
        $msg = $_SESSION['doc-actions-success-msg'];
        unset($_SESSION['doc-actions-success-msg']);
    }

    return $msg;
}

function loadName($what)
{ 
    global $docFields;
    $nameField = '';
    
    switch ($what)
    {
        case 'f':
            $nameField = $docFields->firstName;
            break; 
        case 'm':
            $nameField = $docFields->middleName;
            break; 
        case 'l':
            $nameField = $docFields->lastName;
            break; 
    }

    return loadLastInput($nameField);
}

function loadAddress()
{
    global $docFields;
    return loadLastInput($docFields->address);
}

function loadContact()
{
    global $docFields;
    return loadLastInput($docFields->contact);
}

function loadRegNum()
{
    global $docFields;
    return loadLastInput($docFields->regNum);
}

function loadSpec()
{ 
    return loadLastInput('specLabel');
}

function loadDegree()
{ 
    return loadLastInput('degreeLabel');
}
 