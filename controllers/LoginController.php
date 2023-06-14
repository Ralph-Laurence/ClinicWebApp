<?php
@session_start();

require_once("rootcwd.inc.php");

require_once($rootCwd . "database/configs.php");
require_once($rootCwd . "database/dbhelper.php");
require_once($rootCwd . "includes/system.php");
require_once($rootCwd . "includes/utils.php");
require_once($rootCwd . "includes/urls.php");

require_once($rootCwd . "models/User.php");
 
require_once($rootCwd . "errors/IError.php");

require_once($rootCwd . "database/configs.php");
require_once($rootCwd . "layout-header.php");

use Models\User;
  
$db = new DbHelper($pdo);

// If the user is already logged on, and if he visits 
// the login page, redirect him to HOME instead.
redirectOnLogin();

$userFields = User::getFields();

// Process the login authentication
if ($_SERVER['REQUEST_METHOD'] == 'POST') 
{ 
    // Primary input, from forms
    $username = $_POST['input-username'] ?? "";
    $password = $_POST['input-password'] ?? "";
 
    if (empty($username) || empty($password))
        onIncorrectCredential($username);

    Authenticate($username, $password, $pdo);
}

// If not input, check the session data
if (isset($_SESSION['username-from-reset'], $_SESSION['password-from-reset']))
{
    $username = $_SESSION['username-from-reset'] ?? "";
    $password = $_SESSION['password-from-reset'] ?? "";
    
    if (empty($username) || empty($password))
        onIncorrectCredential($username);
    
    unset($_SESSION['username-from-reset'], $_SESSION['password-from-reset']);
        
    Authenticate($username, $password, $pdo);
}

function Authenticate($username, $password, $pdo)
{
    global $userFields;

    $sql = "SELECT * FROM ". TableNames::users ." WHERE $userFields->username = :u OR $userFields->email = :e";
    $sth = $pdo->prepare($sql);
    $sth->bindParam(":u", $username);
    $sth->bindParam(":e", $username);
    $sth->execute();

    try 
    {
        // Find the user
        $result = $sth->fetch(PDO::FETCH_ASSOC);

        // Check if there was no user that was found with that username
        if (empty($result))
            onIncorrectCredential($username);
 
        // Match the password from database and the input 
        if (!password_verify($password, $result[$userFields->password]))
            onIncorrectCredential($username);
 
        // Must have a role
        if (empty($result[$userFields->role]))
        {
            IError::Throw(403);
            exit;
        }

        // We are now logged in! We will save references to these 
        // user details that we can use later throughout the system
        foreach ($result as $k => $v)
        { 
            $_SESSION[$k] = $v;
        } 

        $_SESSION['isLoggedIn'] = true;

        // Begin using the system
        redirectOnLogin();
    } 
    catch (\Exception $ex) { IError::Throw(500); exit; }
    catch (\Throwable $th) { IError::Throw(500); exit; }
}

function loadLastUsername()
{
    $uname = "";
    
    if (isset($_SESSION["login-last-uname"]))
    {
        $uname = $_SESSION["login-last-uname"];
        unset($_SESSION["login-last-uname"]);
    }

    return $uname;
}

function onIncorrectCredential($username)
{
    $_SESSION["login-last-uname"] = $username;

    Response::Redirect((ENV_SITE_ROOT . Pages::LOGIN), 
        Response::Code403,
        "Incorrect username, email or password. Please try again.",
        "login-err-msg"
    );
    exit;
}

function getErrorMessage()
{
    $msg = "";
    
    if (isset($_SESSION["login-err-msg"]))
    {
        $msg = $_SESSION["login-err-msg"];
        unset($_SESSION["login-err-msg"]);
    }

    return $msg;
}

function redirectOnLogin()
{
    global $userFields;

    if (isset($_SESSION['isLoggedIn'], $_SESSION['role']) && $_SESSION['isLoggedIn'] === true) 
    {
        // Redirect admin or super admin to home page
        $redirect = Pages::HOME;

        // Redirect staff to checkup form
        if ($_SESSION[$userFields->role] == UserRoles::STAFF)
            $redirect = Pages::CHECKUP_FORM;

        header("Location: $redirect");
        exit;
    }
}