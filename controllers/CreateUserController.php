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

use Models\User;

$userFields = User::getFields();

// Store the last input values here from the session
$lastInputs = [];

if (isset($_SESSION['reg_user_last_inputs']))
{
    $lastInputs = $_SESSION['reg_user_last_inputs'];
    unset($_SESSION['reg_user_last_inputs']);
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

    if (isset($_SESSION['user-action-error']))
    {
        $msg = $_SESSION['user-action-error'];
        unset($_SESSION['user-action-error']);
    }

    return $msg;
}

function getSuccessMessage()
{
    $msg = "";

    if (isset($_SESSION['user-actions-success-msg']))
    {
        $msg = $_SESSION['user-actions-success-msg'];
        unset($_SESSION['user-actions-success-msg']);
    }

    return $msg;
}

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
    global $security;

    $userTypes = 
    [
        UserRoles::ToDescName(UserRoles::SUPER_ADMIN)  => UserRoles::SUPER_ADMIN,
        UserRoles::ToDescName(UserRoles::ADMIN)        => UserRoles::ADMIN,
        UserRoles::ToDescName(UserRoles::STAFF)        => UserRoles::STAFF
    ];

    foreach ($userTypes as $k => $v)
    {
        // Admins cannot create another Admin and S. Admin
        if ( ($v == UserRoles::SUPER_ADMIN || $v == UserRoles::ADMIN ) && 
        UserAuth::getRole() != UserRoles::SUPER_ADMIN)
            continue;

        $type = $security->Encrypt($v);

        echo <<<OPTION
            <option value="$type">$k</option>
        OPTION;
    }
}

function bindPermissionFlags()
{
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
 
    // Session flag data
    if (isset($_SESSION['last-chmod-data']))
    {
        $chmod = $_SESSION['last-chmod-data'];

        $flagData[0][3] = explode(",", $chmod["feature-med-rec"]);
        $flagData[1][3] = explode(",", $chmod["feature-inven"]);
        $flagData[2][3] = explode(",", $chmod["feature-supp"]);
        $flagData[3][3] = explode(",", $chmod["feature-doct"]);
        $flagData[4][3] = explode(",", $chmod["feature-user"]);
        $flagData[5][3] = explode(",", $chmod["feature-maint"]);

        unset($_SESSION['last-chmod-data']);
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