<?php
@session_start();

require_once("rootcwd.php");

require_once($rootCwd . "database/configs.php");
require_once($rootCwd . "includes/system.php");
require_once($rootCwd . "includes/utils.php"); 
require_once($rootCwd . "includes/urls.php"); 
require_once($rootCwd . "database/dbhelper.php");

require_once($rootCwd . "library/defuse-crypto.phar");

use Defuse\Crypto\Crypto;
use Defuse\Crypto\Key;

// for encryption/decryption
$defuseKey_Ascii = Helpers::getDefuseKey($pdo);
$defuseKey = Key::loadFromAsciiSafeString($defuseKey_Ascii);

// make sure that this script file will only execute when accessed
// with POST amd AJAX request.
if ($_SERVER['REQUEST_METHOD'] != 'POST')
{
    http_response_code(404);
    die();
}

// wraps basic sql functions like SELECT, INSERT etc..
$db = new DbHelper($pdo);

$checkupsTable = TableNames::$checkup;

// The record keys are stored as JSON string
$recordKeys =  $_POST['record-keys'] ?? "";

if (empty($recordKeys))
{
    throw_response_code(400);
    exit();
}

try
{
    // decode the JSON data into assoc array
    $data = json_decode($recordKeys, true); 

    // record id(s)
    $recordIds = array();

    // every record key is encrypted .. we should
    // decode these to plain string values 
    foreach($data as $k => $v)
    {
        $recordId = Crypto::decrypt($v, $defuseKey);
        array_push($recordIds, $recordId);
    }
 
    // delete every record with matching record id
    $db->deleteWhereIn($pdo, $checkupsTable, "id", $recordIds);
    
    $_SESSION['delete-records-success'] = true;
    $_SESSION['delete-records-status'] = "0x0";

    throw_response_code(200, Navigation::$URL_PATIENT_RECORDS);
    exit();
}
catch (Exception $ex)   
{
    throw_response_code(500);
    exit();
}