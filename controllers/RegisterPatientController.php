<?php
@session_start();

require_once("rootcwd.inc.php");

require_once($rootCwd . "database/configs.php");
require_once($rootCwd . "includes/system.php");
require_once($rootCwd . "includes/utils.php"); 
require_once($rootCwd . "database/dbhelper.php");
require_once($rootCwd . "includes/urls.php");

require_once($rootCwd . "includes/Auth.php");
require_once($rootCwd . "includes/Security.php");

require_once($rootCwd . "MasterLayout.php");
require_once($rootCwd . "layout-header.php");

$security = new Security();
$security->requirePermission(Chmod::PK_MEDICAL, Chmod::FLAG_WRITE);
$security->checkAccess(Chmod::PK_MEDICAL, UserAuth::getId());
    
// Store the last input values here from the session
$lastInputs = [];

if (isset($_SESSION['register-last-inputs']))
    $lastInputs = $_SESSION['register-last-inputs'];
 
/// Load last input value after submit 
function loadLastInput($inputKey, $defaultValue = "")
{
    global $lastInputs;

    if (empty($lastInputs))
        return $defaultValue;

    return $lastInputs[$inputKey];
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

    if (isset($_SESSION['register-patient-error'])) 
    {
        $errorMessage = $_SESSION['register-patient-error'];
        unset($_SESSION['register-patient-error']);
    }

    return $errorMessage;
}