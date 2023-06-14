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

$delete_keys = $_POST['record-keys'] ?? "";
 
if (empty($delete_keys))
{
    IError::Throw(Response::Code400);
    exit;
}

$u = User::getFields();
$superAdmin = UserRoles::SUPER_ADMIN;
$admin = UserRoles::ADMIN;
$users = TableNames::users;

try
{ 
    // Deserialize json into assoc array
    $userIds = json_decode(trim($delete_keys), true);

    // Decrypt Ids
    for ($i = 0; $i < count($userIds); $i++)
    {
        $userIds[$i] = $security->Decrypt($userIds[$i]); 
    }
       
    
    // *1: Find the ids and roles of all Admin and Super Admin Accounts in database
    $sql = "SELECT u.$u->id AS 'ID', u.$u->role AS 'ROLE' FROM $users u WHERE u.$u->role IN ($superAdmin, $admin)";
    
    $dataset = $db->fetchAll($sql);

    // *2: Classify the ids (from db) by role
    foreach ($dataset as $obj)
    {
        switch( $obj['ROLE'] )
        {
            case UserRoles::SUPER_ADMIN:
                
                $id = $obj['ID'];

                $SUP_ADMIN_IDS[] = $id;
                $S_ADMIN_MAP["s_$id"] = $id;

                break;

            case UserRoles::ADMIN:
                $ADMIN_IDS[] = $obj['ID'];
                break;
        }
    }

    // *3: Get the role of authenticated user
    $myRole = UserAuth::getRole();
  
    // *4: For every userIds (in POST), check if authenticated user's role 
    //     is allowed to delete. 
    foreach ($userIds as $id)
    {
        $notAllowed = ($myRole == UserRoles::ADMIN &&
            (
                in_array($id, $ADMIN_IDS) ||
                in_array($id, $SUP_ADMIN_IDS)
            )
        );

        // Block the current user if he is not allowed to edit 
        if ($notAllowed) 
        {
            Response::Redirect((ENV_SITE_ROOT . Pages::USERS),
                Response::Code200,
                "The requested operation could not be performed.\n\nYou are not permitted to modify Super Admin and Admin accounts.",
                'user-actions-error-msg'
            );
            exit;
        }

        // *5: Begin enqueue of user Ids

        // *6: If user type is super admin, dequeue the id from the super admins array.
        //     This simulates tracking of remaining super admin accounts.
        //     We must preserve atleast 2 super admins
        if (in_array($id, $SUP_ADMIN_IDS))
        {
            if (count($S_ADMIN_MAP) > 2)
            { 
                $ID_QUEUE[] = $id;
                unset($S_ADMIN_MAP["s_$id"]);
            }
            else continue;
        } 
        else
            $ID_QUEUE[] = $id;
    }
   
    if (empty($ID_QUEUE))
    {
        // If no users were deleted, just go back
        Response::Redirect((ENV_SITE_ROOT . Pages::USERS), Response::Code200); 
        exit;
    }

    // Delete the users with matching ID
    $db->deleteWhereIn(TableNames::users, $u->id, $ID_QUEUE); 

    // Go back to users page with success message
    Response::Redirect((ENV_SITE_ROOT . Pages::USERS), Response::Code200,
        "The selected users were successfully removed from the system.",
        'user-actions-success-msg'
    ); 
    exit;
}
catch (Exception $ex)   
{
    IError::Throw(Response::Code500);
    exit;
} 