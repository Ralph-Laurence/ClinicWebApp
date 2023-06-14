<?php
 
@session_start();

require_once("rootcwd.php");
require_once($rootCwd . "includes/urls.php");

require_once($rootCwd . "database/configs.php");
require_once($rootCwd . "database/dbhelper.php");
require_once($rootCwd . "includes/system.php");
require_once($rootCwd . "includes/utils.php");  
require_once($rootCwd . "library/semiorbit-guid.php");  

require_once($rootCwd . "models/User.php");

require_once($rootCwd . "includes/Auth.php");
require_once($rootCwd . "includes/Security.php");
require_once($rootCwd . "errors/IError.php"); 

require_once($rootCwd . "MasterLayout.php");
require_once($rootCwd . "layout-header.php");
 
use Models\User;
use SemiorbitGuid\Guid;

$security   = new Security();
$security->requirePermission(Chmod::PK_USERS, Chmod::FLAG_READ);
$security->checkAccess(Chmod::PK_USERS, UserAuth::getId());

$db         = new DbHelper($pdo);
$user       = new User($db);
$userFields = User::getFields();
 
$totalUsers     = 0;
$totalSAdmin    = 0;
$totalAdmin     = 0;
$totalStaff     = 0;

$filterRole = $_GET['usertype'] ?? "";

try 
{
    $dataset = $user->showAll("ASC", $userFields->firstName, $filterRole);

    $totalUsers = count($dataset);

    foreach($dataset as $obj)
    {
        $role = $obj[$userFields->role];

        switch ($role)
        {
            case UserRoles::SUPER_ADMIN:    $totalSAdmin++;     break;
            case UserRoles::ADMIN:          $totalAdmin++;      break;
            case UserRoles::STAFF:          $totalStaff++;      break;
        }
    }

    // The system requires atleast 2 super admin accounts.
    // It should check if there is less than 2, then it
    // must create an account for its own
    if ($totalSAdmin < 3)
    {
        $chmod = TableNames::chmod;
        $users  = TableNames::users;
        $sAdmin = UserRoles::SUPER_ADMIN;
        $passw  = password_hash("default", PASSWORD_DEFAULT);
        $guid = Guid::NewGuid('-', false);

        $commonFields = implode(",",[
            $userFields->firstName, 
            $userFields->lastName, 
            $userFields->username, 
            $userFields->email, 
            $userFields->role
        ]);
    
        $sql =
        "INSERT INTO $users ($commonFields, $userFields->password, $userFields->guid, $userFields->avatar)
        SELECT * FROM (SELECT 'system' as fname, 'default' as lname, 'system' as uname, 'default' as email, '$sAdmin', '$passw', '$guid', 'system' as avatar) AS tmp
        WHERE NOT EXISTS 
        (
            SELECT $commonFields FROM  $users 
            WHERE $userFields->firstName = 'system' AND
            $userFields->lastName  = 'default'      AND
            $userFields->username  = 'system'       AND 
            $userFields->email     = 'default'      AND
            $userFields->role      = '$sAdmin'
        ) LIMIT 1";
    
        $db->query($sql);
        
        // Create perms
        $sysFk = Chmod::userFK;
        $sysId = $db->getValue(TableNames::users, $userFields->id, [$userFields->username => 'system']);
        
        $commonFields = implode(",", [$sysFk, Chmod::PK_MEDICAL, Chmod::PK_INVENTORY, Chmod::PK_SUPPLIERS, Chmod::PK_DOCTORS, Chmod::PK_USERS, Chmod::PK_MAINTENANCE]);
        $w = Chmod::FLAG_WRITE;
        
        $sql = "INSERT INTO $chmod($commonFields) SELECT * FROM (SELECT '$sysId', '$w' as pk1, '$w' as pk2, '$w' as pk3, '$w' as pk4, '$w' as pk5, '$w' as pk6) AS chm 
        WHERE NOT EXISTS (SELECT $sysFk FROM $chmod WHERE $sysFk = $sysId) LIMIT 1";
        
        $db->query($sql);
    }
} 
catch (\Exception $ex) { onError(); }
catch (\Throwable $ex) { onError(); }

function onError()
{
    IError::Throw(500);
    exit;
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

function getErrorMessage()
{
    $msg = "";

    if (isset($_SESSION['user-actions-error-msg']))
    {
        $msg = $_SESSION['user-actions-error-msg'];
        unset($_SESSION['user-actions-error-msg']);
    }

    return $msg;
}

function bindDataset()
{
    global $dataset, $userFields, $security;

    if (empty($dataset))
        return;

    foreach ($dataset as $row)
    {
        // Do not show our own account and the system account
        if  ( ($row[$userFields->id] == UserAuth::getId()) || strtolower($row[$userFields->username]) == "system" )
            continue;

        $name       = "{$row[$userFields->firstName]} {$row[$userFields->middleName]} {$row[$userFields->lastName]}";
        $id         = $security->Encrypt($row[$userFields->id]);
        $role       = UserRoles::ToDescName($row[$userFields->role]);
        $email      = $row[$userFields->email];
        $username   = $row[$userFields->username];
        $avatar     = "assets/images/avatars/". $row[$userFields->avatar] .".png";
        $badge      = "";
 
        switch ($row[$userFields->role])
        {
            case UserRoles::SUPER_ADMIN:    $badge = "assets/images/icons/badge_s_admin.png";   break;
            case UserRoles::ADMIN:          $badge = "assets/images/icons/badge_admin.png";     break;
            case UserRoles::STAFF:          $badge = "assets/images/icons/badge_staff.png";     break;
        }

        $action = <<<BUTTON
        <div class="btn-group">
            <button type="button" class="btn btn-primary btn-details px-2 py-1 text-center">Details</button>
            <button type="button" class="btn btn-primary btn-split-arrow px-0 py-1 text-center dropdown-toggle dropdown-toggle-split" data-mdb-toggle="dropdown" aria-expanded="false"></button>
            <ul class="dropdown-menu shadow-3-strong dropdown-menu-custom-light-small">
                <li class="d-flex align-items-center gap-3 px-3 py-1 dropdown-item-custom-light tr-action-edit">
                    <div class="dropdown-item-icon text-center">
                        <i class="fas fa-pen fs-6 text-warning"></i>
                    </div>
                    <div class="fs-6">Edit</div>
                </li>
                <li class="d-flex align-items-center gap-3 px-3 py-1 dropdown-item-custom-light tr-action-delete">
                    <div class="dropdown-item-icon text-center">
                        <i class="fas fa-trash fs-6 font-red"></i>
                    </div>
                    <div class="fs-6">Delete</div>
                </li>
            </ul>
        </div>
        BUTTON;
 
        echo <<<TR
            <tr>
                <td class="px-2 text-center mx-0 row-check-parent">
                    <div class="d-inline">
                        <input class="form-check-input px-0 mx-0" type="checkbox" id="row-check-box" value="" />
                    </div>
                </td>
                <td class="th-75">
                    <img src="$avatar" width="26" height="26" />
                </td>
                <td class="text-truncate th-230 td-user-name fw-bold">$name</td>
                <td class="text-truncate th-150">
                    <div class="w-100 d-flex align-items-center gap-2">
                        <img src="$badge" width="16" height="16"/>
                        <span>$role</span>
                    </div>
                </td>
                <td class="text-truncate th-180">$email</td>
                <td class="text-truncate th-150">$username</td>
                <td class="th-150 text-center">
                    $action
                </td>
                <td class="d-none">
                    <input type="text" class="record-key" value="$id"/>
                </td>
            </tr>
        TR;
    } 
}


function createFilterItems()
{   
    // Filter Item Data
    $data = 
    [
        [ "label"  => "Show All",       "usertype" => ''                     ],
        [ "label"  => 'Staff',          "usertype" => UserRoles::STAFF       ],
        [ "label"  => 'Admin',          "usertype" => UserRoles::ADMIN       ],
        [ "label"  => 'Super Admin',    "usertype" => UserRoles::SUPER_ADMIN ],
    ];
 
    foreach($data as $filterItem)
    { 
        $icon   = (getFilter() == $filterItem["usertype"]) ? "selected" : "search";
        $filter = $filterItem["usertype"]; 

        echo <<<LI
        <li class="d-flex align-items-center gap-3 px-3 py-1 dropdown-item-custom-light li-user-filter" data-filter-type="$filter">
            <div class="dropdown-item-icon text-center"></div>
            <div class="d-flex align-items-center gap-2">
                <img src="assets/images/icons/filter-$icon.png" width="18" height="18">
                <div class="fs-6">{$filterItem["label"]}</div>
            </div>
        </li>
        LI;
    }
}
 
function getFilter()
{  
    return $_GET['usertype'] ?? '';
}

function createFilterBadge()
{
    $filter = getFilter();

    $filters = 
    [
        UserRoles::SUPER_ADMIN  => UserRoles::ToDescName(UserRoles::SUPER_ADMIN),
        UserRoles::STAFF        => UserRoles::ToDescName(UserRoles::STAFF),
        UserRoles::ADMIN        => UserRoles::ToDescName(UserRoles::ADMIN)
    ];
 
    if (!empty($filter) && array_key_exists($filter, $filters))
    {
        $label = $filters[$filter];

        echo <<<BADGE
        <div class="capsule-badge fsz-14 text-white display-none effect-reciever" data-transition-index="5" data-transition="fadein">
            <div class="d-flex align-items-center">
                <div class="capsule-badge-bg rounded-start px-2">
                    <i class="fas fa-filter fsz-10 me-2"></i>Filter
                </div>
                <div class="bg-mdb-vivid-purple rounded-end px-2 capsule-badge-indicator">
                    $label
                </div>
            </div> 
        </div>
        BADGE;
    }
}
