<?php
@session_start();

require_once("rootcwd.inc.php");

require_once($rootCwd . "database/configs.php");
require_once($rootCwd . "includes/system.php");
require_once($rootCwd . "includes/utils.php"); 
require_once($rootCwd . "database/dbhelper.php");
require_once($rootCwd . "includes/urls.php");
  
require_once($rootCwd . "errors/IError.php");
require_once($rootCwd . "includes/Security.php");
require_once($rootCwd . "includes/Auth.php");

require_once($rootCwd . "MasterLayout.php");
require_once($rootCwd . "layout-header.php");

require_once($rootCwd . "models/User.php");
  
$security = new Security(); 

$security->requirePermission(Chmod::PK_USERS, Chmod::FLAG_WRITE);
$security->checkAccess(Chmod::PK_USERS, UserAuth::getId()); 
//$security->BlockNonPostRequest();

use Models\User;

$edit_user_key = $_POST['user-key'] ?? "";

// Although these doesn't affect the database values, these variables
// will be used to highlight the row which the record came from
$rowIndex = $_POST['row-index'] ?? "";
$pageIndex = $_POST['page-index'] ?? "";

// Check if there was an edit key saved from session
if (isset($_SESSION['edit_last_user_key']))
{
    $edit_user_key = $_SESSION['edit_last_user_key'];
    unset($_SESSION['edit_last_user_key']);
}

if (empty($edit_user_key))
{ 
    Response::Redirect(Pages::USERS, Response::Code301);
    exit;
}

$db = new DbHelper($pdo);
$userFields = User::getFields();

// Store the last input values here from the session
$lastInputs = [];

// Store the last chmod data here either from session / db
$chmod = [];

try 
{
    $userId = $security->Decrypt($edit_user_key);
     
    // Load data from the last inputs session
    if (isset($_SESSION['edit_user_last_inputs'])) 
    {
        $lastInputs = $_SESSION['edit_user_last_inputs'];
        unset($_SESSION['edit_user_last_inputs']);
    } 
    // Load from database
    else 
    {
        $lastInputs = $db->findWhere(TableNames::users, [$userFields->id => $userId]);
    }

    $targetRole = $lastInputs[$userFields->role];
 
    // Admin users can't edit admin and super admins
    $editNotAllowed = (UserAuth::getRole() == UserRoles::ADMIN &&
        (
            $targetRole == UserRoles::SUPER_ADMIN ||
            $targetRole == UserRoles::ADMIN
        )
    );
 
    // Block the current user if he is not allowed to edit 
    if ($editNotAllowed) 
    {
        Response::Redirect((ENV_SITE_ROOT . Pages::USERS),
            Response::Code200,
            "The requested operation could not be performed.\n\nYou are not permitted to modify Super Admin and Admin accounts.",
            'user-actions-error-msg'
        );
        exit;
    }

    // Load chmod data 
    if (isset($_SESSION['last-chmod-data']))
    { 
        $sess_chmod = $_SESSION['last-chmod-data'];
        
        //$chmod = [];
        $featureFlags = [];

        $featureFlags[Chmod::PK_MEDICAL] = explode(",", $sess_chmod["feature-med-rec"]);
        $featureFlags[Chmod::PK_INVENTORY] = explode(",", $sess_chmod["feature-inven"]);
        $featureFlags[Chmod::PK_SUPPLIERS] = explode(",", $sess_chmod["feature-supp"]);
        $featureFlags[Chmod::PK_DOCTORS] = explode(",", $sess_chmod["feature-doct"]);
        $featureFlags[Chmod::PK_USERS] = explode(",", $sess_chmod["feature-user"]);
        $featureFlags[Chmod::PK_MAINTENANCE] = explode(",", $sess_chmod["feature-maint"]);
 
        unset($_SESSION['last-chmod-data']);
    } 
    else 
    {  
        $chmod = $db->findWhere(TableNames::chmod, [Chmod::userFK => $userId]);

        $featureFlags = [];

        //Session flag data
        foreach ($chmod as $k => $v) 
        {
            if ($k == Chmod::id || $k == Chmod::userFK) 
            {
                continue;
            }
            $featureFlags[$k] = Chmod::permToChCode($v, true);
        } 

        if (empty($chmod)) {
            IError::ThrowSecErr(IError::ERR_CODE_EMPTY_PERM_DATA);
            exit;
        }
    }  
} 
catch (\Throwable $th) 
{
    throw_response_code(500);
    exit;
}

/// Load last input value after submit 
function loadLastInput($inputKey, $defaultValue = "")
{
    global $lastInputs;

    if (empty($lastInputs))
        return $defaultValue;

    return $lastInputs[$inputKey];
}

function getErrorMessage()
{
    $msg = "";

    if (isset($_SESSION['edit-user-error']))
    {
        $msg = $_SESSION['edit-user-error'];
        unset($_SESSION['edit-user-error']);
    }

    return $msg;
}

// function getSuccessMessage()
// {
//     $msg = "";

//     if (isset($_SESSION['edit-user-success']))
//     {
//         $msg = $_SESSION['edit-user-success'];
//         unset($_SESSION['edit-user-success']);
//     }

//     return $msg;
// }

function loadName($what)
{ 
    global $userFields;
    
    $nameField = '';
    
    switch ($what)
    {
        case 'f':
            $nameField = $userFields->firstName;
            break; 
        case 'm':
            $nameField = $userFields->middleName;
            break; 
        case 'l':
            $nameField = $userFields->lastName;
            break; 
    }

    return loadLastInput($nameField);
}

function loadEmail()
{
    global $userFields;
    return loadLastInput($userFields->email);
}

function loadUsername()
{
    global $userFields;
    return loadLastInput($userFields->username);
}

function bindUserTypes()
{
    global $security, $userFields;

    $isSelected = loadLastInput($userFields->role);

    $userTypes = 
    [
        UserRoles::ToDescName(UserRoles::SUPER_ADMIN)  => UserRoles::SUPER_ADMIN,
        UserRoles::ToDescName(UserRoles::ADMIN)        => UserRoles::ADMIN,
        UserRoles::ToDescName(UserRoles::STAFF)        => UserRoles::STAFF
    ];

    foreach ($userTypes as $k => $v)
    {
        $type = $security->Encrypt($v);
        $selected = "";

        if ($isSelected == $v)
            $selected = "selected";

        echo <<<OPTION
            <option value="$type" $selected>$k</option>
        OPTION;
    }
}

function bindPermissionFlags()
{
    global $featureFlags;

    // Original Flag data
    $flagData =
    [
            // TR Tag           Title Label         Icon                      Checkbox value
        [   "feature-med-rec", "Medical Records", "sidenav-medical.png",      [1,1,0] ],
        [   "feature-inven",   "Inventory",       "sidenav-inventory.png",    [1,1,0] ],
        [   "feature-supp",    "Suppliers",       "sidenav-supplier.png",     [1,1,0] ],
        [   "feature-doct",    "Doctors",         "sidenav-doctor.png",       [1,1,0] ],
        [   "feature-user",    "Users",           "sidenav-users.png",        [1,0,0] ],    
        [   "feature-maint",   "Maintenance",     "sidenav-maintenance.png",  [1,0,0] ],   
    ];
 
    if (!empty($featureFlags))
    {
        $flagData[0][3] = $featureFlags[Chmod::PK_MEDICAL];
        $flagData[1][3] = $featureFlags[Chmod::PK_INVENTORY];
        $flagData[2][3] = $featureFlags[Chmod::PK_SUPPLIERS];
        $flagData[3][3] = $featureFlags[Chmod::PK_DOCTORS];
        $flagData[4][3] = $featureFlags[Chmod::PK_USERS];
        $flagData[5][3] = $featureFlags[Chmod::PK_MAINTENANCE];
    } 

    foreach ($flagData as $data)
    {
        createFlagData($data[0], $data[1], $data[2], $data[3]);
    }
}

function createFlagData($tag, $title, $icon, $flagValues)
{ 
    $perm   = implode(",", $flagValues);
 
    echo <<<TR
    <tr class="align-middle" data-feature-tag="$tag">
        <td class="th-180">
            <div class="d-flex align-items-center">
                <img src="assets/images/icons/$icon" width="20" height="20">
                <span class="ms-2 perm-name">$title</span>
            </div>
        </td>
        <td class="th-65">
            <div class="d-flex justify-content-center">
                <input class="form-check-input perm-flag flag-read" type="radio" value="" data-flag-type="read"/>
            </div>
        </td> 
        <td class="th-65">
            <div class="d-flex justify-content-center">
                <input class="form-check-input perm-flag flag-write" type="radio" value="" data-flag-type="write"/>
            </div>
        </td> 
        <td class="th-65"> 
            <div class="d-flex justify-content-center">
                <input class="form-check-input perm-flag flag-deny" type="radio" value="" data-flag-type="deny"/>
            </div>
        </td> 
        <td class="th-100">
            <div id="" class="flag-label px-2 text-center rounded-2 fsz-14"></div>
            <input type="text" class="chmod d-none" value="$perm">
        </td>
    </tr>
    TR;
}

function loadAvatarImage($avatar)
{
    global $security;
    $src = $security->Decrypt($avatar);

    return (ENV_SITE_ROOT . "assets/images/avatars/$src.png");
}

function loadAvatar()
{
    global $lastInputs, $userFields, $security;

    if (empty($lastInputs))
        return $security->Encrypt("who");

    return $security->Encrypt($lastInputs[$userFields->avatar]);
}