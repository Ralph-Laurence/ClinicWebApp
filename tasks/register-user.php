<?php
@session_start();

require_once("rootcwd.php");

require_once($rootCwd . "database/configs.php");
require_once($rootCwd . "database/dbhelper.php");

require_once($rootCwd . "library/semiorbit-guid.php");
require_once($rootCwd . "includes/system.php");
require_once($rootCwd . "includes/utils.php"); 
require_once($rootCwd . "includes/urls.php"); 

require_once($rootCwd . "includes/Auth.php");
require_once($rootCwd . "includes/Security.php");

require_once($rootCwd . "errors/IError.php");
  
require_once($rootCwd . "models/User.php");

use Models\User;
use SemiorbitGuid\Guid;

$security = new Security();                          
$security->BlockNonPostRequest();                    
$security->requirePermission(Chmod::PK_USERS, Chmod::FLAG_WRITE);
$security->checkAccess(Chmod::PK_USERS, UserAuth::getId()); 

$userFields = User::getFields();
$db = new DbHelper($pdo);       

$reservedNames = ["system", "default"];

$requiredFields = 
[
    $userFields->firstName  => $_POST['fname'] ?? "",
    $userFields->lastName   => $_POST['lname'] ?? "",
    $userFields->username   => $_POST['uname'] ?? "",
    $userFields->email      => $_POST['email'] ?? "",
    $userFields->password   => $_POST['password']   ?? "",
    $userFields->role       => $_POST['user-type']  ?? "",
    $userFields->guid       => Guid::NewGuid('-', false)
];

// Validate required fields
foreach (array_values($requiredFields) as $v)
{
    if (empty($v))
    { 
        onError();
        break;
    } 
}

// Validate chmod json
$chmod_json = $_POST['chmod-json'] ?? "";

if (empty($chmod_json)) {
    onError();
}

// Optional Field (Middlename)
$requiredFields[$userFields->middleName] = $_POST['mname'] ?? "";
$avatarData = $_POST['avatar-data'] ?? "";

try 
{
    // Validate permission data, then map those permissions to CHMOD equivalent
    $chmod = json_decode($chmod_json, true);    

    // Identify user role then decrypt it
    $role = $security->Decrypt($requiredFields[$userFields->role]);
    $requiredFields[$userFields->role] = $role;

    if (!empty($avatarData))
    {
        $avatar = $security->Decrypt($avatarData);
        $avatar = str_replace(".png", "", $avatar);
        
        $requiredFields[$userFields->avatar] = $avatar; 
    }

    // Encrypt password
    $password = $requiredFields[$userFields->password];
    $requiredFields[$userFields->password] = password_hash(trim($password), PASSWORD_DEFAULT);
    
    $permData = []; // Store permission data here for Db insertion
    
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
    // Fields should not contain 'system' or 'default' as its value.
    // This will be reserved for the system itself
    foreach (array_values($requiredFields) as $v)
    {  
        if (in_array(strtolower($v), $reservedNames))
        {
            // save last inputs because we will go back to the previous form
            saveSession($requiredFields, $userKey, $chmod);

            Response::Redirect((ENV_SITE_ROOT . Pages::CREATE_USER), Response::Code301,
                "$v is not a valid name, email, or username! Please try another.", 
                "user-action-error"
            );
            exit();
        }
    } 

    // Insert newly created user
    $db->save(TableNames::users, $requiredFields);
    
    // Find the newly created user's id using his email.
    // We will use this for permissions
    $userId = $db->selectValue(TableNames::users, $userFields->id, [
        $userFields->email => $requiredFields[$userFields->email]
    ]);
  
    // Add permissions
    $permData[Chmod::userFK] = $userId;

    $db->save(TableNames::chmod, $permData);

    // Return to create user page on success
    $fullName = $requiredFields[$userFields->firstName]." ".$requiredFields[$userFields->lastName];

    Response::Redirect((ENV_SITE_ROOT . Pages::CREATE_USER), 
        Response::Code200,
        "$fullName was successfuly registered as a user.", 
        "user-actions-success-msg"
    );
    exit;
} 
catch(Exception $ex)
{ 
    // save last inputs because we will go back to the previous form
    saveSession($requiredFields, $userKey, $chmod);
    
    if (Helpers::strContains($ex->getMessage(), "for key 'username'")) 
    { 
        Response::Redirect((ENV_SITE_ROOT . Pages::CREATE_USER), Response::Code301,
            "Username is taken! Please try another.", 
            "user-action-error"
        );
        exit();
    }
    else if (Helpers::strContains($ex->getMessage(), "for key 'email'")) 
    { 
        Response::Redirect((ENV_SITE_ROOT . Pages::CREATE_USER), Response::Code301,
            "Email is already in use! Please try another.",
            "user-action-error"
        ); 
        exit();
    }
    
    onError();
}

function saveSession($fields, $userKey, $chmod)
{
    // save last inputs because we will go back to the previous form
    $_SESSION['reg_user_last_inputs'] = $fields;
    $_SESSION['reg_last_user_key'] = $userKey;
    $_SESSION['last-chmod-data'] = $chmod;
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