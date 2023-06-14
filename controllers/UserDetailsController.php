<?php
@session_start();

require_once("rootcwd.php");

require_once($rootCwd . "includes/urls.php");

require_once($rootCwd . "database/configs.php");
require_once($rootCwd . "database/dbhelper.php");
require_once($rootCwd . "includes/system.php");
require_once($rootCwd . "includes/utils.php");

require_once($rootCwd . "models/User.php");

require_once($rootCwd . "includes/Auth.php");
require_once($rootCwd . "includes/Security.php");
require_once($rootCwd . "errors/IError.php");

require_once($rootCwd . "MasterLayout.php");
require_once($rootCwd . "layout-header.php");
 
use Models\User;

$security   = new Security();
 
$security->requirePermission(Chmod::PK_USERS, Chmod::FLAG_READ);
$security->checkAccess(Chmod::PK_USERS, UserAuth::getId());

$db   = new DbHelper($pdo); 
$user = new User($db);
$userFields  = User::getFields();

$detailsKey = $_POST['details-key'] ?? "";

if (isset($_SESSION['user-details-last-key']))
{
    $detailsKey = $_SESSION['user-details-last-key'];
    unset($_SESSION['user-details-last-key']);
}
else 
{
    $security->BlockNonPostRequest();
}

if (empty($detailsKey))
{
    Response::Redirect(Pages::USERS, 301);
}
 
try 
{
    // Details key is an encrypted Record Id. We must decrypt it first
    $recordId = $security->Decrypt($detailsKey);

    // Load the user from database
    $dataset = $user->find($recordId);
     
    // If there was no user data found, exit the execution
    if (empty($dataset))
    {
        onError();
    } 

    // Admin users can't edit admin and super admins' password
    $cantChangePassw = (UserAuth::getRole() == UserRoles::ADMIN &&
        (
            $dataset[$userFields->role] == UserRoles::SUPER_ADMIN ||
            $dataset[$userFields->role] == UserRoles::ADMIN
        )
    ); 
} 
catch (\Exception $ex) {  onError(); }
catch (\Throwable $th) {  onError(); }
 
function onError()
{
    IError::Throw(500);
    exit;
}

function bindChangePasswordBtn()
{
    global $cantChangePassw;

    if ($cantChangePassw)
        return;

    echo <<<BUTTON
    <button type="button" class="btn btn-secondary ms-auto px-2 py-1 mb-2" data-mdb-toggle="modal" data-mdb-target="#updatePasswordModal">
        <i class="fas fa-lock me-2"></i>
        Change Password
    </button>
    BUTTON;
}

function bindChangePasswModal()
{
    global $cantChangePassw, $rootCwd;

    if ($cantChangePassw)
        return;

    require_once($rootCwd . "includes/embed.change-passw-modal.php");
}

function getAvatar()
{
    global $dataset, $userFields;

    $avatar = $dataset[$userFields->avatar] ?? "avatar_0";

    return "assets/images/avatars/$avatar.png";
}

function getName()
{
    global $dataset, $userFields;

    $name = implode(" ",[
        $dataset[$userFields->firstName], $dataset[$userFields->middleName], $dataset[$userFields->lastName]]
    );

    return $name;
}

function getUsername()
{
    global $dataset, $userFields;
    return $dataset[$userFields->username];
}

function getEmail()
{
    global $dataset, $userFields;
    return $dataset[$userFields->email];
}
 

function getRole()
{
    global $dataset, $userFields;
    $role = UserRoles::ToDescName($dataset[$userFields->role]);
    $badge = "";

    switch ($dataset[$userFields->role])
    {
        case UserRoles::SUPER_ADMIN:
            $badge = "badge_s_admin.png";
            break;
        case UserRoles::ADMIN:
            $badge = "badge_admin.png";
            break;
        case UserRoles::STAFF:
            $badge = "badge_staff.png";
            break;
    }

    echo <<<ROLE
        <div class="w-100 d-flex flex-wrap align-items-center justify-content-center gap-2">
            <img src="assets/images/icons/$badge" width="20" height="20" />
            <span>$role</span>
        </div>
    ROLE;
}

function getJoinDate()
{
    global $dataset, $userFields;
    return "Joined, " . Dates::toString($dataset[$userFields->date_created], "M. d, Y");
}
 
function getDocKey()
{
    global $detailsKey;
    return $detailsKey;
}

function bindPermissionFlags()
{
    global $db, $recordId;

    $perms = $db->findWhere(TableNames::chmod, [Chmod::userFK => $recordId]);
     
    // Original Flag data
    $flagData =
    [
          // TR Tag               Title Label         Icon                      
        [ Chmod::PK_MEDICAL,      "Medical Records", "sidenav-medical.png",     ],
        [ Chmod::PK_INVENTORY,    "Inventory",       "sidenav-inventory.png",   ],
        [ Chmod::PK_SUPPLIERS,    "Suppliers",       "sidenav-supplier.png",    ],
        [ Chmod::PK_DOCTORS,      "Doctors",         "sidenav-doctor.png",      ],
        [ Chmod::PK_USERS,        "Users",           "sidenav-users.png",       ],    
        [ Chmod::PK_MAINTENANCE,  "Maintenance",     "sidenav-maintenance.png", ],   
    ];

    foreach ($flagData as $data)
    {
        createFlagData($data[1], $data[2], $perms[$data[0]]);
    }
}

function createFlagData($title, $icon, $flagValue)
{
    $mark = "<i class=\"fas fa-check\"></i>";
    $flagRead  = "<i class=\"fas fa-times font-red\"></i>";
    $flagWrite = "<i class=\"fas fa-times font-red\"></i>";
    $flagDeny  = "<i class=\"fas fa-times font-red\"></i>";
    $labelColor = ""; 
    $label = "";

    switch ($flagValue)
    {
        case Chmod::FLAG_WRITE:
            $flagWrite = $mark;
            $flagRead = $mark;
            $labelColor = "font-green";
            $label = "Full Access";
            break;
        case Chmod::FLAG_READ:
            $flagRead = $mark;
            $labelColor = "text-warning";
            $label = "View-Only";
            break;
        case Chmod::FLAG_DENY:
            $flagDeny = $mark;
            $labelColor = "font-red";
            $label = "No Access";
            break;
    }

    echo <<<TR
    <tr class="align-middle">
        <td class="th-180">
            <div class="d-flex align-items-center">
                <img src="assets/images/icons/$icon" width="20" height="20">
                <span class="ms-2 perm-name">$title</span>
            </div>
        </td>
        <td class="th-65 text-center">$flagRead</td> 
        <td class="th-65 text-center">$flagWrite</td> 
        <td class="th-65 text-center">$flagDeny</td> 
        <td class="th-100">
            <span class="$labelColor">$label</span>
        </td>
    </tr>
    TR;
}
function getErrorMessage()
{
    $msg = "";

    if (isset($_SESSION["user-details-error"]))
    {
        $msg = $_SESSION["user-details-error"];
        unset($_SESSION["user-details-error"]);
    }

    return $msg;
}

function getSuccessMessage()
{
    $msg = "";

    if (isset($_SESSION["user-details-success"]))
    {
        $msg = $_SESSION["user-details-success"];
        unset($_SESSION["user-details-success"]);
    }

    return $msg;
}