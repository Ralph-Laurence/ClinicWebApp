<?php
@session_start();

require_once("rootcwd.php");

require_once($rootCwd . "database/configs.php");
require_once($rootCwd . "includes/system.php");
require_once($rootCwd . "includes/utils.php"); 
require_once($rootCwd . "includes/urls.php"); 
require_once($rootCwd . "database/dbhelper.php");
require_once($rootCwd . "models/Category.php"); 

require_once($rootCwd . "includes/Auth.php");
require_once($rootCwd . "includes/Security.php");

use Models\Category;

$security = new Security();
$security->requirePermission(Chmod::PK_MAINTENANCE, Chmod::FLAG_WRITE);
$security->checkAccess(Chmod::PK_MAINTENANCE, UserAuth::getId());

// make sure that this script will only execute with POST request.
$security->BlockNonPostRequest();

// wraps basic sql functions like SELECT, INSERT etc..
$db = new DbHelper($pdo);

// JSON - encoded values
$record_keys = $_POST['record-keys'] ?? "";
 
if (empty($record_keys)) { onError(); }

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
 
    // delete every category records with matching record id
    $db->deleteWhereIn( TableNames::categories, Category::getFields()->id, $recordIds);

    Response::Redirect( (ENV_SITE_ROOT . Pages::CATEGORIES),
        Response::Code200, 
        "The selected categories were successfully removed.",
        'category-actions-success-msg'
    );
    exit;
}
catch (Exception $ex) { onError(); }

function onError()
{
    IError::Throw(500);
    exit;
}

?>