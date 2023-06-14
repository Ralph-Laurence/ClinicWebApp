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

require_once($rootCwd . "models/User.php");

use Models\User;

$security = new Security();
$security->requirePermission(Chmod::PK_USERS, Chmod::FLAG_WRITE);
$security->checkAccess(Chmod::PK_USERS, UserAuth::getId());

$goBack = (ENV_SITE_ROOT . Pages::USER_DETAILS);
$user_key = $_POST['user-key'] ?? "";

if (empty($user_key))
{
    onError();
}

$db = new DbHelper($pdo);
$usersTable = TableNames::users;

// These are required input data
$requiredFields = 
[
    'newPassword'    => $_POST['new-password'] ?? "",
    'retypePassword' => $_POST['retype-password'] ?? "",
    'yourPassword'   => $_POST['your-password'] ?? "",
    'username'       => $_POST['username'] ?? ""
];
 
// Check all required fields if there were empty values.
// Show error page if it has empty values
foreach(array_values($requiredFields) as $v)
{
    if ($v == "")
    {
        goBack(true, "The server encountered an incomplete value from one of your inputs and the server will not process it.");
    }
}
 
try 
{
    // decrypt the user key
    $userId = $security->Decrypt($user_key);

    $u = User::getFields();

    // Match the new and old password
    if ($requiredFields['newPassword'] != $requiredFields['retypePassword'])
    {
        goBack(true, "Passwords didn't matched. Please try again.");
    }
    // Password must be atleast 4 chars long
    if (strlen($requiredFields['newPassword']) < 4)
    {
        goBack(true, "Password is too short. Minimum length is atleast 4 characters long.");
    }
    // Confirm autheticated user's password and username
    $password = $db->getValue($usersTable, $u->password, 
    [
        $u->username => $requiredFields['username']
    ]);
    
    if (empty($password) || !password_verify($requiredFields['yourPassword'], $password))
    {
        goBack(true, "Your username and/or password is incorrect.");
    } 
    // New Password must not be the same as old
    $oldPassword = $db->getValue($usersTable, $u->password, 
    [
        $u->id => $userId
    ]);

    if (password_verify($requiredFields['newPassword'], $oldPassword))
    {
        goBack(true, "You cannot re-use an old password.");
    }

    // Update the user's new password
    $db->update($usersTable, [$u->password => password_hash($requiredFields['newPassword'], PASSWORD_DEFAULT)],
    [
        $u->id => $userId
    ]);

    goBack(false, "Password successfully changed");
   
} 
catch (\Exception $ex) { onError(); }
catch (\Throwable $ex) { onError(); }

function onError()
{
    IError::Throw(500);
    exit;
}

function goBack($isError = false, $message)
{
    global $user_key, $goBack;

    $_SESSION['user-details-last-key'] = $user_key;

    if ($isError)
    { 
        Response::Redirect($goBack, Response::Code301, 
            $message,
            "user-details-error"
        );
        exit;
    }
    else
    {
        Response::Redirect($goBack, Response::Code200, 
            $message,
            "user-details-success"
        );
        exit;
    }
}