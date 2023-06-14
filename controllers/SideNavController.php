<?php 

/**
 * If user level was not on the list, dont give access
 */
function hasFeatureAccess($allowOn = [UserRoles::STAFF])
{
    $hasAccess = 0;

    foreach ($allowOn as $userLevel)
    {
        if (UserAuth::getRole() == $userLevel)
            $hasAccess++;
    }

    return ($hasAccess > 0);
}