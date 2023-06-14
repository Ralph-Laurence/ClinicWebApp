<?php
@session_start();

require_once("rootcwd.php");

require_once($rootCwd . "includes/urls.php"); 
require_once($rootCwd . "database/configs.php");
require_once($rootCwd . "includes/system.php");
require_once($rootCwd . "includes/utils.php");
require_once($rootCwd . "layout-header.php");

require_once($rootCwd . "includes/Security.php");
require_once($rootCwd . "includes/Auth.php");

$security = new Security();

$avatar = UserAuth::getAvatar() .".png";
 
if (empty(UserAuth::getAvatar()))
    $avatar = "avatar_0.png"; 

$name = implode(" ", [UserAuth::getFirstname(), UserAuth::getMiddlename(), UserAuth::getLastname()]);

if (Utils::hasEmpty([UserAuth::getFirstname(), UserAuth::getLastname()]))
    $name = "Anonymous"; 

$role = UserRoles::ToDescName(UserAuth::getRole());

$roleBadgeIcon = "assets/images/icons/shield-windows.png";
$roleBadge = "badge-success";

switch (UserAuth::getRole())
{
    case UserRoles::SUPER_ADMIN:    
        $roleBadge = "badge-success";   
        $roleBadgeIcon = "assets/images/icons/badge_s_admin.png";
        break;
    case UserRoles::ADMIN:          
        $roleBadge = "badge-primary";   
        $roleBadgeIcon = "assets/images/icons/badge_admin.png";
        break;
    case UserRoles::STAFF:          
        $roleBadge = "badge-warning";   
        $roleBadgeIcon = "assets/images/icons/badge_staff.png";
        break;
}