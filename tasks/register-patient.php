<?php
@session_start();

require_once("rootcwd.php");

require_once($rootCwd . "database/configs.php");
require_once($rootCwd . "includes/system.php");
require_once($rootCwd . "includes/utils.php"); 
require_once($rootCwd . "includes/urls.php"); 
require_once($rootCwd . "database/dbhelper.php");

require_once($rootCwd . "includes/Auth.php");
require_once($rootCwd . "includes/Security.php");

require_once($rootCwd . "models/Patient.php");

use Models\Patient; 

$security = new Security();
$security->requirePermission(Chmod::PK_MEDICAL, Chmod::FLAG_WRITE);
$security->checkAccess(Chmod::PK_MEDICAL, UserAuth::getId());
$security->BlockNonPostRequest();
 
$db = new DbHelper($pdo);
 
$patientFields = Patient::getFields();
 
// These are required input data
$requiredFields = 
[
    $patientFields->idNumber    => $_POST['input-idnum'] ?? "",
    $patientFields->firstName   => $_POST['input-fname'] ?? "",
    $patientFields->middleName  => $_POST['input-mname'] ?? "",
    $patientFields->lastName    => $_POST['input-lname'] ?? "",
    $patientFields->patientType => $_POST['select-patient-type'] ?? "",
    $patientFields->gender      => $_POST['select-gender'] ?? "",
    $patientFields->age         => $_POST['input-age'] ?? "",
    $patientFields->birthDay    => $_POST['input-birthday'] ?? ""
];

// Check all required fields if there were empty values.
// Show error page if it has empty values
foreach(array_values($requiredFields) as $v)
{
    if ($v == "")
    {
        // save last inputs because we will go back to the previous form
        $_SESSION['register-last-inputs'] = $requiredFields; 

        Response::Redirect((ENV_SITE_ROOT . Pages::REGISTER_PATIENT), Response::Code301, 
            "Please fill out all fields with valid information!",
            'register-patient-error'
        ); 
        exit;
    }
}

// Format birthday as Year-Month-Day
if (!empty($requiredFields[$patientFields->birthDay]))
{ 
    $bday = date_format(date_create($requiredFields[$patientFields->birthDay]), "Y-m-d");
    $requiredFields[$patientFields->birthDay] = $bday;
}

//------------------------------------------// 
//----Collect inputs for optional fields----//
//------------------------------------------//
$requiredFields[$patientFields->weight]  = $_POST['input-weight'] ?? "";
$requiredFields[$patientFields->height]  = $_POST['input-height'] ?? "";
$requiredFields[$patientFields->contact] = $_POST['input-contact'] ?? "";
$requiredFields[$patientFields->parent]  = $_POST['input-parent-guardian'] ?? "";
$requiredFields[$patientFields->address] = $_POST['input-address'] ?? "";

//------------------------------------------// 
//----SAVE THE DATA ONTO PATIENTS TABLE-----//
//------------------------------------------//
try 
{   
    $db->insert( TableNames::patients, $requiredFields);
 
    $_SESSION['register-patient-idNum'] = $requiredFields[$patientFields->idNumber];

    $_SESSION['register-patient-extra'] = 1; // -> 1 = It means sort the record Descending, to display recent patient

    // Response::Redirect((ENV_SITE_ROOT . Pages::CHECKUP_FORM), Response::Code200, 
    Response::Redirect((ENV_SITE_ROOT . Pages::PATIENTS), Response::Code200, 
        "A patient was successfully registered",
        "patient-records-action-success"
    );
    exit;
} 
catch (\Exception $th) 
{   
    // save last inputs because we will go back to the previous form
    $_SESSION['register-last-inputs'] = $requiredFields; 

    if (IString::contains($th->getMessage(), "for key '$patientFields->idNumber'")) 
    { 
        Response::Redirect((ENV_SITE_ROOT . Pages::REGISTER_PATIENT), Response::Code200, 
            "A patient with the same ID Number has already been registered.",
            'register-patient-error'
        );
        exit;
    } 

    IError::Throw(500);
    exit();
}

