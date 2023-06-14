<?php
@session_start();

require_once("rootcwd.php");
 
require_once($rootCwd . "database/configs.php");
require_once($rootCwd . "database/dbhelper.php");
 
require_once($rootCwd . "includes/system.php");
require_once($rootCwd . "includes/utils.php"); 
require_once($rootCwd . "includes/urls.php"); 
 
require_once($rootCwd . "includes/Auth.php");
require_once($rootCwd . "includes/Security.php");
 
require_once($rootCwd . "errors/IError.php");
   
require_once($rootCwd . "models/User.php");
 
use Models\User;
 
$security = new Security();                          
$security->BlockNonPostRequest();                    
$security->requirePermission(Chmod::PK_USERS, Chmod::FLAG_WRITE);
$security->checkAccess(Chmod::PK_USERS, UserAuth::getId()); 
 
$edit_user_key = $_POST['user-key'] ?? "";

if (empty($edit_user_key)) { onError(); }

$userFields = User::getFields();
$db = new DbHelper($pdo);       
 
$reservedNames = ["system", "default"];
$updateFields = 
[
    $userFields->firstName  => $_POST['fname'] ?? "",
    $userFields->lastName   => $_POST['lname'] ?? "",
    $userFields->username   => $_POST['uname'] ?? "",
    $userFields->email      => $_POST['email'] ?? "", 
    $userFields->role       => $_POST['user-type']  ?? ""
];
  
// Validate required fields
foreach (array_values($updateFields) as $v)
{
    if (empty($v))
    { 
        onError();
        break;
    } 
}

$chmod_json = $_POST['chmod-json'] ?? "";
 
if (empty($chmod_json)) { onError(); }

// Optional Fields
$updateFields[$userFields->middleName] = $_POST['mname'] ?? "";
$avatarData = $_POST['avatar-data'] ?? "";

// Admin users can't edit admin and super admins
$editNotAllowed = (UserAuth::getRole() == UserRoles::ADMIN &&
    (
        $updateFields[$userFields->role] == UserRoles::SUPER_ADMIN || 
        $updateFields[$userFields->role] == UserRoles::ADMIN 
    )
);

// Block the current user if he is not allowed to edit 
if ($editNotAllowed) 
{
    IError::Throw(Response::Code403);
    exit;
}

try
{
    // Validate permission data, then map those permissions to CHMOD equivalent
    $chmod = json_decode($chmod_json, true);    
 
    // Identify user role then decrypt it
    $role = $security->Decrypt($updateFields[$userFields->role]);
    $userId = $security->Decrypt($edit_user_key);

    if (!empty($avatarData))
    {
        $avatar = $security->Decrypt($avatarData);
        $avatar = str_replace(".png", "", $avatar);
        
        $updateFields[$userFields->avatar] = $avatar; 
    }

    $updateFields[$userFields->role] = $role;

    // Fields should not contain 'system' or 'default' as its value.
    // This will be reserved for the system itself
    foreach (array_values($updateFields) as $v) 
    {
        if (in_array(strtolower($v), $reservedNames)) 
        {
            // save last inputs because we will go back to the previous form
            saveSession($updateFields, $edit_user_key, $chmod);

            Response::Redirect((ENV_SITE_ROOT . Pages::EDIT_USER),
                Response::Code301,
                "$v is not a valid name, email, or username! Please try another.",
                'edit-user-error'
            );
            exit();
        }
    }

    //echo $updateFields[$userFields->role]; exit;
    // save the new user into the database
    $db->update(TableNames::users, $updateFields, [ $userFields->id => $userId ]);

    // Update permissions
    $permData = [];
    
    foreach ($chmod as $k => $v)
    {
        if (empty($v)) 
        {
            onError();
            break;
        } 

        $toPerm = chmod_toPermission($k, $v, $role);
        $permData[$toPerm['key']] = $toPerm['value'];
    }  
    
    $permData[Chmod::userFK] = $userId;

    $db->update(TableNames::chmod, $permData, [Chmod::userFK => $userId]);

    Response::Redirect((ENV_SITE_ROOT . Pages::USERS), Response::Code200, 
        "A user was updated successfully.", 'user-actions-success-msg');
    exit();
}
catch(Exception $ex)
{  
    // save last inputs because we will go back to the previous form
    saveSession($updateFields, $edit_user_key, $chmod);
    
    if (IString::contains($ex->getMessage(), "for key 'username'")) 
    { 
        Response::Redirect((ENV_SITE_ROOT . Pages::EDIT_USER), Response::Code200, 
            "Username is already taken! Please try another.", 'edit-user-error'
        );
        exit();
    }
    else if (IString::contains($ex->getMessage(), "for key 'email'")) 
    { 
        Response::Redirect((ENV_SITE_ROOT . Pages::EDIT_USER), Response::Code200, 
            "Email is already in use! Please try another.", 'edit-user-error'
        );
        exit();
    }
    
    onError();
}

function chmod_toPermission($featureTag, $chmod, $role)
{ 
    $perm = Chmod::chmodToPerm(trim($chmod));
 
    $featurePerms =
    [
        "feature-med-rec"   => array('key' => Chmod::PK_MEDICAL,     'value' => $perm),
        "feature-inven"     => array('key' => Chmod::PK_INVENTORY,   'value' => $perm),
        "feature-supp"      => array('key' => Chmod::PK_SUPPLIERS,   'value' => $perm),
        "feature-doct"      => array('key' => Chmod::PK_DOCTORS,     'value' => $perm),
        "feature-user"      => array('key' => Chmod::PK_USERS,       'value' => $perm),
        "feature-maint"     => array('key' => Chmod::PK_MAINTENANCE, 'value' => $perm)
    ];

    // Check the user role. Super admins should have full access.
    // Admins are limited to given perms and 
    // Staffs should only be able to create medical records.
    foreach ($featurePerms as $k => $obj)
    { 
        if ($role == UserRoles::SUPER_ADMIN) 
        {
            $featurePerms[$k] = array('key' => $obj['key'], 'value' => Chmod::FLAG_WRITE); 
        } 
        else if ($role == UserRoles::STAFF) 
        {
            if ($k != "feature-med-rec")
                $featurePerms[$k] = array('key' => $obj['key'], 'value' => Chmod::FLAG_DENY);
        }
    }

    return $featurePerms[trim($featureTag)];
} 

function onError()
{
    IError::Throw(500);
    exit;
}

function saveSession($fields, $userKey, $chmod)
{
    // save last inputs because we will go back to the previous form    
    $_SESSION['edit_user_last_inputs']  = $fields;
    $_SESSION['edit_last_user_key']     = $userKey;
    $_SESSION['last-chmod-data']        = $chmod; 
}