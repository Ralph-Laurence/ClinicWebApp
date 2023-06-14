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

$userId = UserAuth::getId();

$userKey = $_POST['user-key'] ?? "";

if (empty($userId) || empty($userKey))
{
    Response::Redirect((ENV_SITE_ROOT . Pages::MY_PROFILE), Response::Code301);
    exit;
}

if ($security->Decrypt($userKey) != $userId)
{
    Response::Redirect((ENV_SITE_ROOT . Pages::MY_PROFILE), Response::Code301);
    exit;
}

$requiredFields = 
[
    $fields->firstName => $_POST['firstname'] ?? "",
    $fields->middleName => $_POST['middlename'] ?? "",
    $fields->lastName => $_POST['lastname'] ?? "",
    $fields->username => $_POST['username'] ?? "",
    $fields->email => $_POST['email'] ?? "",
    $fields->avatar => $_POST['avatar'] ?? "",
];

if ($security->isValidHash($requiredFields[$fields->avatar]))
    $requiredFields[$fields->avatar] = $security->Decrypt($requiredFields[$fields->avatar]);
    
$rename = str_replace(".png", "", $requiredFields[$fields->avatar]);
$requiredFields[$fields->avatar] = $rename;

foreach ($requiredFields as $k => $v)
{
    if (empty($v))
    {
        writeError("Please fill out all fields! $k");
        break;
    }

    switch ($k)
    {
        case $fields->firstName:
            if (!isOnlyLettersAndDashes($v))
                writeError("Firstname contains invalid symbols or characters");
            break;

        case $fields->middleName:
            if (!isOnlyLettersAndDashes($v))
                writeError("Middlename contains invalid symbols or characters");
            break;
        case $fields->lastName:
            if (!isOnlyLettersAndDashes($v))
                writeError("Lastname contains invalid symbols or characters");
            break;
    }
}

try
{    
    // Validate the password first
    $verify = $_POST['password'] ?? "";

    if (empty($verify))
    {
        writeError("Please enter your password to make changes to your profile.");
    }

    $password = $db->getValue(TableNames::users, $fields->password, [ $fields->id => $userId ]);
    
    if (!password_verify($verify, $password))
    {
        writeError("Incorrect password. You can't make changes to your profile. Please try again.");
    } 

    $db->update(TableNames::users, $requiredFields, [ $fields->id => $userId ]);

    $_SESSION['profile-edit-success'] = IString::random(8);

    Response::Redirect((ENV_SITE_ROOT . Pages::MY_PROFILE), Response::Code200, 
    "Profile successfully updated.", 
    'profile-action-success');
}
catch (\Exception $ex) 
{
    if (Helpers::strContains($ex->getMessage(), "for key 'username'")) 
    { 
        writeError("Username is taken! Please try another.");
    }
    else if (Helpers::strContains($ex->getMessage(), "for key 'email'")) 
    { 
        writeError("Email is already in use! Please try another.");
    }
    
    onError();
}
//catch (\Throwable $ex) { onError(); }
  
function onError()
{
    IError::Throw(500);
    exit;
}

function writeError($msg)
{ 
    global $requiredFields;

    $_SESSION['edit-profile-last-inputs'] = $requiredFields;

    Response::Redirect((ENV_SITE_ROOT . Pages::EDIT_MY_PROFILE),
        Response::Code301,
        $msg, 
        'edit-profile-error'
    );
    exit;
}
 
function isOnlyLettersAndDashes($string) {
    return preg_match('/^[a-zA-Z-]+$/', $string);
}