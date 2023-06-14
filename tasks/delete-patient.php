<?php
@session_start();

require_once("rootcwd.php");

require_once($rootCwd . "database/configs.php");
require_once($rootCwd . "includes/system.php");
require_once($rootCwd . "includes/utils.php"); 
require_once($rootCwd . "includes/urls.php"); 
require_once($rootCwd . "database/dbhelper.php");
require_once($rootCwd . "models/Patient.php"); 

require_once($rootCwd . "includes/Auth.php");
require_once($rootCwd . "includes/Security.php");

use Models\Patient;
use TableFields\PatientFields; 

$security = new Security();
$security->requirePermission(Chmod::PK_MEDICAL, Chmod::FLAG_WRITE);
$security->checkAccess(Chmod::PK_MEDICAL, UserAuth::getId());

// make sure that this script will only execute with POST request.
$security->BlockNonPostRequest();

// wraps basic sql functions like SELECT, INSERT etc..
$db = new DbHelper($pdo);

$delete_key = $_POST['delete-key'] ?? "";
 
if (empty($delete_key))
{
    throw_response_code(400);
    exit();
}

try
{ 
    // Decrypt patient key into plain string
    $patientKey = $security->Decrypt($delete_key);
    
    // Delete the patient with matching patient key
    // This will also delete any related records like checkup & prescriptions
    $db->delete($pdo, TableNames::patients, [Patient::getFields()->id => $patientKey]);

    $_SESSION['patient-records-action-success'] = "A patient and its related records were removed successfully.";

    throw_response_code(200, (ENV_SITE_ROOT . Pages::PATIENTS));
    exit;
}
catch (Exception $ex)   
{
    throw_response_code(500);
    exit;
}