<?php
@session_start();

require_once("rootcwd.inc.php");

require_once($rootCwd . "database/configs.php");
require_once($rootCwd . "includes/system.php");
require_once($rootCwd . "includes/utils.php"); 
require_once($rootCwd . "database/dbhelper.php");
require_once($rootCwd . "includes/urls.php");
require_once($rootCwd . "models/Patient.php");

require_once($rootCwd . "includes/Auth.php");
require_once($rootCwd . "includes/Security.php");

use Models\Patient;
use TableFields\PatientFields;

require_once($rootCwd . "MasterLayout.php");
require_once($rootCwd . "layout-header.php");

$security = new Security();
$security->requirePermission(Chmod::PK_MEDICAL, Chmod::FLAG_WRITE);
$security->checkAccess(Chmod::PK_MEDICAL, UserAuth::getId());

// Load Patient Key from POST. If none, go back to patients page
$edit_patient_key = $_POST['edit-key'] ?? "";

// Although these doesn't affect the database values, these variables
// will be used to highlight the row which the record came from
$rowIndex = $_POST['row-index'] ?? "";
$pageIndex = $_POST['page-index'] ?? "";

// Check if there was an edit key saved from session
if (isset($_SESSION['edit-patient-key']))
{
    $edit_patient_key = $_SESSION['edit-patient-key'];
}

if (empty($edit_patient_key))
{
    //$security->BlockNonPostRequest();
    Response::Redirect((ENV_SITE_ROOT . Pages::PATIENTS), Response::Code301);
    //throw_response_code(301, Pages::PATIENTS);
    exit;
}

$db = new DbHelper($pdo);
$patientFields = Patient::getFields();

// Display error message or hide if no errors
// $errorLabelDisplay = "display-none"; 
// $errorMessage = "";

// if (isset($_SESSION['edit-patient-error']))
// {
//     $errorLabelDisplay = "";
//     $errorMessage = $_SESSION['edit-patient-error'];
// }
    
// Store the last input values here from the session
$lastInputs = [];

if (isset($_SESSION['edit-patient-last-inputs']))
{
    $lastInputs = $_SESSION['edit-patient-last-inputs'];
    unset($_SESSION['edit-patient-last-inputs']);
}
else 
{
    try 
    {
        $patientId = $security->Decrypt($edit_patient_key);
    
        $lastInputs = $db->findWhere( TableNames::patients, [$patientFields->id => $patientId]);
    } 
    catch (\Throwable $th) 
    {  
        throw_response_code(500);
        exit;
    }
}
 
/// Load last input value after submit 
function loadLastInput($inputKey, $defaultValue = "")
{
    global $lastInputs;

    if (empty($lastInputs))
        return $defaultValue;

    return $lastInputs[$inputKey] ?? "";
}

///--------------------------------------------///
/// DROPDOWN OPTIONS | VALUES FOR PATIENT TYPE ///
///--------------------------------------------///
function create_patientTypeOption(int $type, int $selectedValue = 0)
{
    $value = ""; 

    switch($type)
    {
        case PatientTypes::$STUDENT:
            $value = PatientTypes::$STUDENT;
            break;

        case PatientTypes::$STAFF: 
            $value = PatientTypes::$STAFF;
            break;
        
        case PatientTypes::$TEACHER: 
            $value = PatientTypes::$TEACHER;
            break;
    }
  
    $selected = $value == $selectedValue ? "selected" : "";
    $label = PatientTypes::toDescription($value);

    echo "<option value=\"$value\" $selected>$label</option>";
}

///--------------------------------------------///
/// DROPDOWN OPTIONS | VALUES FOR GENDER TYPE  ///
///--------------------------------------------///
function create_genderOption(int $type, int $selectedValue = 0)
{
    $value = ""; 

    switch($type)
    {
        case GenderTypes::$MALE:
            $value = GenderTypes::$MALE;
            break;

        case GenderTypes::$FEMALE: 
            $value = GenderTypes::$FEMALE;
            break; 
    }

    $selected = $value == $selectedValue ? "selected" : "";
    $label = GenderTypes::toDescription($value);

    echo "<option value=\"$value\" $selected>$label</option>";
}

function getErrorMessage()
{
    $errorMessage = "";

    if (isset($_SESSION['edit-patient-error'])) 
    {
        $errorMessage = $_SESSION['edit-patient-error'];
        unset($_SESSION['edit-patient-error']);
    }

    return $errorMessage;
}

unset(
    $_SESSION['edit-patient-last-inputs'],
    //$_SESSION['edit-patient-error'],
    $_SESSION['edit-patient-key']
);