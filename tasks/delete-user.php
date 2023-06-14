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
$security->requirePermission(Chmod::PK_USERS, Chmod::FLAG_WRITE);
$security->checkAccess(Chmod::PK_USERS, UserAuth::getId());

// make sure that this script will only execute with POST request.
$security->BlockNonPostRequest();

// wraps basic sql functions like SELECT, INSERT etc..
$db = new DbHelper($pdo);

$delete_key = $_POST['delete-key'] ?? "";
 
if (empty($delete_key))
{
    IError::Throw(Response::Code400);
    exit;
}

try
{ 
    // Decrypt user key into plain string
    $userId = $security->Decrypt($delete_key);

    // Find the role of the target user
    $u = User::getFields();
    
    $superAdmin = UserRoles::SUPER_ADMIN;
    $users      = TableNames::users;

    $sql = 
    "SELECT  u1.$u->role,
	    CASE u1.$u->role 
		    WHEN $superAdmin THEN (SELECT COUNT(u2.$u->id) FROM $users AS u2 WHERE u2.$u->role = $superAdmin)
	    END	AS 'count'
    FROM $users AS u1
    WHERE u1.$u->id = $userId";

    $data = $db->fetchAll($sql, true);

    // Admin users can't edit admin and super admins
    $notAllowed = (UserAuth::getRole() == UserRoles::ADMIN &&
        (
            $data['role'] == UserRoles::SUPER_ADMIN ||
            $data['role'] == UserRoles::ADMIN
        )
    );

    // Block the current user if he is not allowed to edit 
    if ($notAllowed) 
    {
        Response::Redirect((ENV_SITE_ROOT . Pages::USERS), Response::Code200,
            "The requested operation could not be performed.\n\nYou are not permitted to modify Super Admin and Admin accounts.",
        'user-actions-error-msg'
        ); 
        exit;
    }

    // Preserve atleast 2 super admin accounts
    if (intval($data['role']) == UserRoles::SUPER_ADMIN && intval($data['count']) <= 2)
    {
        Response::Redirect((ENV_SITE_ROOT . Pages::USERS), Response::Code200,
            "The requested operation could not be performed.\n\nAs a security measure, " .
            "the system prevents at least two (2) Super Admin accounts from being deleted.",
        'user-actions-error-msg'
        ); 
        exit;
    }

    // Delete the user with matching user key
    // This will also delete any related records like checkup & prescriptions
    $db->delete($pdo, TableNames::users, [User::getFields()->id => $userId]);
 
    Response::Redirect((ENV_SITE_ROOT . Pages::USERS), Response::Code200,
        "A user was successfully removed from the system.",
        'user-actions-success-msg'
    ); 
    exit;
}
catch (Exception $ex)   
{
    IError::Throw(Response::Code500);
    exit;
} 