<?php

use Models\User;

@session_start();

require_once("rootcwd.php");

require_once($rootCwd . "database/configs.php");
require_once($rootCwd . "includes/system.php");
require_once($rootCwd . "includes/utils.php"); 
require_once($rootCwd . "includes/urls.php"); 
require_once($rootCwd . "database/dbhelper.php");
 
require_once($rootCwd . "models/User.php");

require_once($rootCwd . "includes/Auth.php");
require_once($rootCwd . "includes/Security.php");
 
$security = new Security(); 

// make sure that this script will only execute with POST request.
$security->BlockNonPostRequest();

// wraps basic sql functions like SELECT, INSERT etc..
$db = new DbHelper($pdo);
$fields = User::getFields();

$profile_key = $_POST['profile-key'] ?? "";
$input_passw = $_POST['see-guid-password'] ?? "";

$passwErr = "You've entered an incorrect password!";
$failure = "This action cant be completed because of an error.";

if (empty($profile_key)) { writeError($failure); }

if (empty($input_passw)) { writeError($passwErr); }

try
{  
    $profileId = $security->Decrypt($profile_key);
  
    $data = $db->select(TableNames::users, [ $fields->password, $fields->guid ], [ $fields->id => $profileId ], '', '', true);

    $password = $data[$fields->password];
    
    if (empty($password) || (!empty($password) && !password_verify($input_passw, $password)))
    {
        writeError($passwErr);
    }
  
    if (empty($data[$fields->guid])) { writeError($failure); }

    $_SESSION['flag-view-guid'] = true;
    $_SESSION['guid-readable'] = $data[$fields->guid];
    
    Response::Redirect((ENV_SITE_ROOT . Pages::MY_PROFILE), Response::Code200);
    exit;
}
catch (\Exception $ex) { onError(); }
catch (\Throwable $ex) { onError(); }

function onError()
{
    IError::Throw(500);
    exit;
}

function writeError($msg)
{ 
    Response::Redirect((ENV_SITE_ROOT . Pages::MY_PROFILE),
        Response::Code301,
        $msg, 
        'profile-action-error'
    );
    exit;
}