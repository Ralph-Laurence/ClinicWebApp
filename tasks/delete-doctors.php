<?php
@session_start();

require_once("rootcwd.php");

require_once($rootCwd . "database/configs.php");
require_once($rootCwd . "includes/system.php");
require_once($rootCwd . "includes/utils.php"); 
require_once($rootCwd . "includes/urls.php"); 
require_once($rootCwd . "database/dbhelper.php");
require_once($rootCwd . "models/Doctor.php"); 

require_once($rootCwd . "includes/Security.php");
require_once($rootCwd . "includes/Auth.php");

use Models\Doctor;

// check if the user has enough permission to view this page.
// The required permission for this page is the 
// Checkups Permission
//$perm = UserPerms::getCheckupAccess();

// Staff users are not allowed to delete records
// Show the "Access Denied" page
// if (!UserPerms::hasAccess($perm) || UserAuth::getRole() == 1)
// {
//     throw_response_code(403);
//     exit();
// }
 
// for encryption/decryption
$security = new Security();
 
// make sure that this script will only execute with POST request.
$security->BlockNonPostRequest();

// wraps basic sql functions like SELECT, INSERT etc..
$db = new DbHelper($pdo);

// JSON - encoded values
$record_keys = $_POST['record-keys'] ?? "";
 
if (empty($record_keys))
    onError();

try
{ 
    // decode the JSON data into assoc array
    $data = json_decode($record_keys, true); 
 
    // record id(s)
    $recordIds = array();

    $fields = Doctor::getFields();

    // every record id is encrypted .. we should
    // decode these to plain string values 
    foreach($data as $k => $v)
    { 
        $recordId = $security->Decrypt($v);  
        array_push($recordIds, $recordId);
    } 
    // delete every records with matching id
    $db->deleteWhereIn( TableNames::doctors, $fields->id, $recordIds);
 
    Response::Redirect( (ENV_SITE_ROOT . Pages::DOCTORS),
        Response::Code200, 
        (
            count($recordIds) == 1
            ? "A doctor was removed from the system."
            : count($recordIds) . "&plus; Doctors were removed from the system."
        ),
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
?>

