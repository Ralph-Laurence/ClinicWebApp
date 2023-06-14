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

$password_seeGuid = $_POST['see-guid-password'] ?? ""; // isset($_POST['see-guid-password']) ? $_POST['see-guid-password'] : "";
$revealGuid = false;

try
{
	$table = TableNames::users;
	$fields = User::getFields();
	// $userId = $security->Decrypt($userKey);
	$sql = "SELECT * FROM $table WHERE $fields->id = $userId LIMIT 1";
	$userData = $db->fetchAll($sql, true);
	
	if (empty($userData))
	{
		throwError();
	}
}
catch(\Exception $e) { echo $e->getMessage(); exit; throwError(); }
catch(\Throwable $e) { echo $e->getMessage(); exit; throwError(); }

function throwError()
{
	IError::Throw(Response::Code500);
	exit;
}

function getUsername()
{
	global $userData, $fields;
	return $userData[$fields->username];
}

function getFullname()
{
	global $userData, $fields;

	$name = 
	[
		$userData[$fields->firstName],
		$userData[$fields->middleName],
		$userData[$fields->lastName]
	];
	
	return implode(" ", $name);
} 

function getEmail()
{
	global $userData, $fields;
	return $userData[$fields->email];
}

function getAvatar()
{
	global $userData, $fields;
	return $userData[$fields->avatar];
}

function getGuid()
{
	global $userData, $fields, $revealGuid;

	$reveal = $_SESSION['flag-view-guid'] ?? "";
	$guid = $_SESSION['guid-readable'] ?? "";

	if (!empty($reveal) && $reveal === true)
	{
		unset($_SESSION['guid-readable'], $_SESSION['flag-view-guid']);
		$revealGuid = true;
		return $guid;
	}

	// Mask the GUID
	$guid = $userData[$fields->guid];
	$astk = implode("", array_fill(0, strlen($guid), "*"));
	return $astk;
}

function getPassword()
{
	global $userData, $fields;
	return $userData[$fields->password];
}

function setProfile()
{
	global $userData, $fields;

	return "assets/images/avatars/". $userData[$fields->avatar];
	/*
	$profile = getProfile();
	
	// if empty, use avatar...
	if (empty($profile))
	{
		return "assets/images/avatars/". $profile;
	}
	// if not empty, use pic
	else
	{
		
	}
	*/
}

function getErrorMessage()
{ 
	$msg = ""; 

	if (isset($_SESSION['profile-action-error'])) 
	{
		$msg = $_SESSION['profile-action-error'];
		unset($_SESSION['profile-action-error']);
	}

	return $msg;
}

function getSuccessMessage()
{ 
	$msg = ""; 

	if (isset($_SESSION['profile-action-success'])) 
	{
		$msg = $_SESSION['profile-action-success'];
		unset($_SESSION['profile-action-success']);
	}

	return $msg;
} 

function getProfileKey()
{
	global $security, $userId;
	return $security->Encrypt($userId);
}

function getDateJoined()
{
	global $userData, $fields;
	$date = $userData[$fields->date_created];

	if (!empty($date))
	{
		return "Joined " . Dates::toString($date, "M. d, Y");
	}
	return "Unknown entry date";
}

function getEditSuccessKey()
{
	$key = "";

	if (isset($_SESSION['profile-edit-success']))
	{
		$key = $_SESSION['profile-edit-success'];
		unset($_SESSION['profile-edit-success']);
	}

	return $key;
}