<?php

use Models\User;

 @session_start();

require_once("rootcwd.inc.php");

global $rootCwd;

require_once($rootCwd . "includes/system.php");
require_once($rootCwd . "includes/utils.php"); 
require_once($rootCwd . "includes/urls.php");

require_once($rootCwd . "includes/Auth.php");
require_once($rootCwd . "includes/Security.php");
require_once($rootCwd . "errors/IError.php");

require_once($rootCwd . "models/User.php");

require_once($rootCwd . "MasterLayout.php");
require_once($rootCwd . "layout-header.php");

$security = new Security(); 

$db = new DbHelper($pdo);
$userId = UserAuth::getId();
 
if (empty($userId))
{
	throwError();
}
    
$userFields = User::getFields();

// Store the last input values here from the session
$lastInputs = [];

if (isset($_SESSION['edit-profile-last-inputs']))
{
    $lastInputs = $_SESSION['edit-profile-last-inputs'];
    unset($_SESSION['edit-profile-last-inputs']);
}
else 
{
    try 
    { 
        $lastInputs = $db->findWhere( TableNames::users, [$userFields->id => $userId]);
    } 
    catch (\Throwable $th) 
    {  
        IError::Throw(Response::Code500);
        exit;
    }
}

try
{
	//$table = TableNames::users;
	//$fields = User::getFields();
	// $userId = $security->Decrypt($userKey);
	//$sql = "SELECT * FROM $table WHERE $fields->id = $userId LIMIT 1";
	//$userData = $db->fetchAll($sql, true);
	
	//if (empty($userData))
	//{
		//throwError();
//	}
}
catch(\Exception $e) { echo $e->getMessage(); exit; throwError(); }
catch(\Throwable $e) { echo $e->getMessage(); exit; throwError(); }

function throwError()
{
	IError::Throw(Response::Code500);
	exit;
}

/// Load last input value after submit 
function loadLastInput($inputKey, $defaultValue = "")
{
    global $lastInputs;

    if (empty($lastInputs))
        return $defaultValue;

    return $lastInputs[$inputKey] ?? "";
}

function loadUserKey()
{
	global $security;

	return $security->Encrypt(UserAuth::getId());
}
 
function loadAvatarSrc()
{
	global $security;
	
	$avatar = loadLastInput("avatar");

	if ($security->isValidHash($avatar))
	{
		$avatar = $security->Decrypt($avatar);

		if (!IString::endsWith($avatar, ".png"))
			$avatar .= ".png";

		return "assets/images/avatars/$avatar";
	}

	if (!empty($avatar))
	{
		$avatar = "assets/images/avatars/$avatar.png";
		return $avatar;
	}

	return "assets/images/avatars/". UserAuth::getAvatar() .".png";
}

function loadAvatar()
{
	global $security;
	
	$avatar = loadLastInput("avatar");

	if (!$security->isValidHash($avatar))
	 	$avatar = $security->Encrypt($avatar);

	return $avatar;
}

function getErrorMessage()
{ 
	$msg = ""; 

	if (isset($_SESSION['edit-profile-error'])) 
	{
		$msg = $_SESSION['edit-profile-error'];
		unset($_SESSION['edit-profile-error']);
	}

	return $msg;
}

