<?php

use Models\User;
use SemiorbitGuid\Guid;

@session_start();

require_once("rootcwd.php");

require_once($rootCwd . "database/configs.php");
require_once($rootCwd . "includes/system.php");
require_once($rootCwd . "includes/utils.php"); 
require_once($rootCwd . "includes/urls.php"); 
require_once($rootCwd . "database/dbhelper.php");

require_once($rootCwd . "models/User.php");

//require_once($rootCwd . "includes/Auth.php");
require_once($rootCwd . "includes/Security.php");

require_once($rootCwd . "library/semiorbit-guid.php");
  
$security = new Security(false); 

// make sure that this script will only execute with POST request.
$security->BlockNonPostRequest();

// wraps basic sql functions like SELECT, INSERT etc..
$db = new DbHelper($pdo);

$username = $_POST['username'] ?? "";
$security_key = $_POST['seckey'] ?? "";
$newPass = $_POST['new-pass'] ?? "";
$confirmPass = $_POST['confirm-pass'] ?? "";
 
if (empty($security_key) || empty($username))
{
    writeError("Invalid username or security key. Please try again.");
}

try
{  
    $users = TableNames::users;
    $fields = User::getFields();
    
    // Verify the credential first
    $sql = "SELECT $fields->password AS 'old' FROM $users WHERE $fields->username = ? AND $fields->guid = ?";

    $verify = $db->fetchAllParam($sql, 
    [
        $username,
        $security_key
    ], true); 

    $oldPassword = $verify['old'];

    if (empty($oldPassword))
    {
        writeError("Incorrect username or security key.");
    }

    // validate password
    validatePassword($newPass, $confirmPass, $oldPassword);
    
    // set new password, then reset user key
    $newPassword = password_hash($newPass, PASSWORD_DEFAULT);
    $newSecKey = Guid::NewGuid('-', false);

    $db->update($users, 
    // New values
    [ $fields->password => $newPassword, $fields->guid => $newSecKey ], 
    // Condition
    [ $fields->username => $username, $fields->guid => $security_key ]);

    $_SESSION['username-from-reset'] = $username;
    $_SESSION['password-from-reset'] = $newPass;

    Response::Redirect((ENV_SITE_ROOT . Pages::LOGIN), Response::Code200);
}
catch (Exception $ex) { echo $ex->getMessage(); } //IError::Throw(Response::Code500); }

function writeError($msg)
{
    global $username, $security_key, $newPass;

    $_SESSION['forgot-passw-last-inputs'] = 
    [
        'username'  => $username,
        'seckey'    => $security_key,        
        'new-pass'  => $newPass
    ]; 

    Response::Redirect((ENV_SITE_ROOT . Pages::FORGOT_PASSWORD), Response::Code301,
        $msg,
        'forgot-passw-err-msg'
    );
    exit;
}

function validatePassword($newPassword, $confirmPassword, $oldPassword)
{
    // rule 1
    // password should not have spaces
    if (IString::contains($newPassword, " "))
    {
        writeError("Password should not contain spaces.");
    }

    // rule 2
    // password should be minimum of 8 chars long
    if (strlen($newPassword) < 8)
    {
        writeError("Password is too short. Must be atleast 8 characters long.");
    }

    // rule 3
    // New password must be equal to confirm password
    if ($newPassword != $confirmPassword)
    {
        writeError("Passwords didn't match!");
    }

    // rule 4
    // Old password must not be the same as new password
    if (password_verify($newPassword, $oldPassword))
    {
        writeError("You cannot reuse an old password.");
    }
}