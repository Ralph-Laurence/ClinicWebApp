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

use TableFields\PatientFields; 

$security = new Security();
$security->requirePermission(Chmod::PK_MEDICAL, Chmod::FLAG_WRITE);
$security->checkAccess(Chmod::PK_MEDICAL, UserAuth::getId());

// make sure that this script will only execute with POST request.
$security->BlockNonPostRequest();

// wraps basic sql functions like SELECT, INSERT etc..
$db = new DbHelper($pdo);

// JSON - encoded values
$record_keys = $_POST['record-keys'] ?? "";
 
if (empty($record_keys))
{
    throw_response_code(500);
    exit();
}

try
{ 
    // decode the JSON data into assoc array
    $data = json_decode($record_keys, true); 

    // record id(s)
    $recordIds = array();

    // every record id is encrypted .. we should
    // decode these to plain string values 
    foreach($data as $k => $v)
    {
        $recordId = $security->Decrypt($v); 
        array_push($recordIds, $recordId);
    }
 
    // delete every patient records with matching record id
    $db->deleteWhereIn( TableNames::patients, PatientFields::$id, $recordIds);

    $_SESSION['patient-records-action-success'] = "Patients and their related records were removed successfully.";

    throw_response_code(200, (ENV_SITE_ROOT . Pages::PATIENTS));
    exit;
}
catch (Exception $ex)   
{
    throw_response_code(500);
    exit;
}

?>