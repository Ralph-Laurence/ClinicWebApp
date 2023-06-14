<?php
// require_once("rootcwd.inc.php");
// require_once($rootCwd . "env.php");

$avatar = "avatar_0.png";

if (!empty(UserAuth::getAvatar()))
    $avatar = UserAuth::getAvatar() . ".png";

$name = implode(" ", [UserAuth::getFirstname(), UserAuth::getMiddlename(), UserAuth::getLastname()]);

if (Utils::hasEmpty([UserAuth::getFirstname(), UserAuth::getLastname()]))
    $name = "Anonymous";

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
?>