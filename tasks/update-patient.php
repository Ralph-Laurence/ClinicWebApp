<?php
@session_start();

require_once("rootcwd.php");

require_once($rootCwd . "database/configs.php");
require_once($rootCwd . "includes/system.php");
require_once($rootCwd . "includes/utils.php"); 
require_once($rootCwd . "includes/urls.php"); 
require_once($rootCwd . "database/dbhelper.php"); 

require_once($rootCwd . "includes/Security.php");
require_once($rootCwd . "includes/Auth.php");

require_once($rootCwd . "models/Patient.php");

use Models\Patient;
use TableFields\PatientFields as Fields;

$security = new Security();
$security->requirePermission(Chmod::PK_MEDICAL, Chmod::FLAG_WRITE);
$security->checkAccess(Chmod::PK_MEDICAL, UserAuth::getId());

$route_onFail = (ENV_SITE_ROOT . Pages::EDIT_PATIENT);
$edit_key = $_POST['edit-key'];

// Although these doesn't affect the database values, these variables
// will be used to highlight the row which the record came from.
// These variables are also present at the Edit Patient page.
$rowIndex = $_POST['row-index'] ?? "";
$pageIndex = $_POST['page-index'] ?? "";

// echo "row: " . $rowIndex . "<br>page: " . $pageIndex; exit;

if (empty($edit_key))
{
    throw_response_code(500);
    exit;
}

$db = new DbHelper($pdo);

// The patient model holds the data as objects
$model = new Patient();

// Required
$model->idNumber    = $_POST['input-idnum'] ?? "";
$model->firstName   = $_POST['input-fname'] ?? "";
$model->middleName  = $_POST['input-mname'] ?? "";
$model->lastName    = $_POST['input-lname'] ?? "";
$model->patientType = $_POST['select-patient-type'] ?? "";
$model->gender      = $_POST['select-gender'] ?? "";
$model->age         = $_POST['input-age'] ?? "";
$model->dateUpdated = Dates::createTimestamp();

// Optional
$model->birthDay    = $_POST['input-birthday'] ?? "";
$model->weight      = $_POST['input-weight'] ?? "";
$model->height      = $_POST['input-height'] ?? "";
$model->contact     = $_POST['input-contact'] ?? "";
$model->parent      = $_POST['input-parent-guardian'] ?? "";
$model->address     = $_POST['input-address'] ?? "";

// These are required input data
$requiredFields = 
[
    Fields::$idNumber       => $model->idNumber,
    Fields::$firstName      => $model->firstName,
    Fields::$middleName     => $model->middleName,
    Fields::$lastName       => $model->lastName,
    Fields::$patientType    => $model->patientType,
    Fields::$gender         => $model->gender,
    Fields::$age            => $model->age,
    Fields::$dateUpdated    => $model->dateUpdated
];

// Format birthday as Year-Month-Day
if (!empty($model->birthDay))
{ 
    $requiredFields[Fields::$birthDay] = date_format(date_create($model->birthDay), "Y-m-d");
}

// Check all required fields if there were empty values.
// Show error page if it has empty values
foreach(array_values($requiredFields) as $v)
{
    if ($v == "")
    {
        // save last inputs because we will go back to the previous form
        $_SESSION['edit-patient-last-inputs'] = $requiredFields; 
        $_SESSION['edit-patient-error'] = "Please fill out all required* fields with valid information!";
        $_SESSION['edit-patient-key'] = $edit_key;

        throw_response_code(301, $route_onFail);
        exit;
    }
}

//------------------------------------------// 
//----Collect inputs for optional fields----//
//------------------------------------------//
$requiredFields[Fields::$weight] = $model->weight;
$requiredFields[Fields::$height] = $model->height;
$requiredFields[Fields::$contact] = $model->contact;
$requiredFields[Fields::$parent] = $model->parent;
$requiredFields[Fields::$address] = $model->address;

//------------------------------------------// 
//----SAVE THE DATA ONTO PATIENTS TABLE-----//
//------------------------------------------//
try 
{
    // decrypt the patient key
    $patient_key = $security->Decrypt($edit_key);

    // save the changes
    $db->update( TableNames::patients, $requiredFields, [Patient::getFields()->id => $patient_key]);

    $_SESSION['patient-records-action-success'] = "A patient's profile information was successfully updated";

    /// OPTIONAL ///
    // These will be used to highlight the row after a succesful edit
    $emphasis = 
    [
        "row" => $rowIndex, 
        "page" => $pageIndex
    ];
    
    $_SESSION['patients-edit-row-emphasis'] = json_encode($emphasis);
     
    throw_response_code(200, (ENV_SITE_ROOT . Pages::PATIENTS));
    exit;
} 
catch (\Throwable $th) 
{   
    // save last inputs because we will go back to the previous form
    $_SESSION['edit-patient-last-inputs'] = $requiredFields; 
    $_SESSION['edit-patient-key'] = $edit_key;

    if (Helpers::strContains($th->getMessage(), "for key 'id_number'")) 
    { 
        Response::Redirect((ENV_SITE_ROOT . Pages::EDIT_PATIENT), Response::Code301,
        "A patient with the same ID Number has already been registered.",
        'edit-patient-error');
        //throw_response_code(301, $route_onFail);
        exit;
    } 
    else if (Helpers::strContains($th->getMessage(), "for key 'patient_key'")) 
    {
        //$_SESSION['edit-patient-error'] = "There was a problem during the registration process. Please re-submit the form.";
        
        Response::Redirect((ENV_SITE_ROOT . Pages::EDIT_PATIENT), Response::Code301,
        "There was a problem during the registration process. Please re-submit the form.",
        'edit-patient-error');
        //throw_response_code(301, $route_onFail);
        exit;
    }
 
    throw_response_code(500);
    exit();
}

