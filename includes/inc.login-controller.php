<?php

@session_start();

require_once("rootcwd.inc.php");

require_once($cwd . "database/configs.php");
require_once($cwd . "includes/system.php");
require_once($cwd . "includes/utils.php");
require_once($cwd . "database/dbhelper.php");
require_once($cwd . "includes/urls.php");

$username = "";
$password = "";
$errorCount = 0;
$usersTable = TableNames::$users;
$permsTable = TableNames::$permissions;

// If we are already logged in and we visited the login page,
// we skip this page and redirect to workarea instead
redirectOnLogin();

// Process the login authentication
if ($_SERVER['REQUEST_METHOD'] == 'POST') 
{
    global $username;
    global $password;

    $username = $_POST['input-username'] ?? "";
    $password = $_POST['input-password'] ?? "";

    authenticate($username, $password);
}

function authenticate($uname, $passw)
{
    global $errorCount;
    global $pdo;
    global $usersTable;
    global $permsTable;

    // the username and password must not be empty
    if (empty($uname) || empty($passw)) 
    {
        $errorCount++;
        return;
    }

    try 
    {
        // Find the username from database
        $sql = "SELECT * FROM $usersTable WHERE username = :uname OR email = :email";
        $sth = $pdo->prepare($sql);
        $sth->bindParam(":uname", $uname);
        $sth->bindParam(":email", $uname);
        $sth->execute();
        $result = $sth->fetch(PDO::FETCH_ASSOC);

        // stop if no username has matched
        if (empty($result)) 
        {
            $errorCount++;
            return;
        }

        // match the password from database and the input
        $hash_password = $result['password'];
        $match_password = password_verify($passw, $hash_password);

        // stop if the password is incorrect
        if (!$match_password) 
        {
            $errorCount++;
            return;
        }

        // we are now logged in!
        // we can now save references to some user details
        // that we can use later on throughout the system
        $_SESSION['username'] = $result['username'];
        $_SESSION['email'] = $result['email'];
        $_SESSION['role'] = $result['role'];
        $_SESSION['guid'] = $result['guid'];
        $_SESSION['firstname'] = $result['firstname'];
        $_SESSION['middlename'] = $result['middlename'];
        $_SESSION['lastname'] = $result['lastname'];
        $_SESSION['avatar'] = $result['avatar'];

        // retrieve the access permissions of logged-on user
        // we can later use this for blocking the users on
        // restricted areas
        $sql = "SELECT * FROM $permsTable WHERE user_guid = ?";
        $sth = $pdo->prepare($sql);
        $sth->bindValue(1, $result['guid']);
        $sth->execute();
        $result = $sth->fetch(PDO::FETCH_ASSOC);

        // if no permission data has been found, launch error page
        if (empty($result)) {
            http_response_code(500);
            die();
        }

        // save references to permissions
        $_SESSION['perm_checkup'] = $result['perm_checkup_form'];
        $_SESSION['perm_restock'] = $result['perm_restock'];
        $_SESSION['perm_inventory'] = $result['perm_inventory'];
        $_SESSION['perm_suppliers'] = $result['perm_suppliers'];
        $_SESSION['perm_patient_records'] = $result['perm_patient_records'];
        $_SESSION['perm_illness'] = $result['perm_illness'];
        $_SESSION['perm_categories'] = $result['perm_categories'];

        // flag that we are already logged on.
        $_SESSION['isLoggedIn'] = true;

        // redirect to workarea
        redirectOnLogin();
    } 
    catch (Exception $ex) 
    {
        echo $ex->getMessage();
        http_response_code(500);
        die();
    }
}

function redirectOnLogin()
{
    if (isset($_SESSION['isLoggedIn']) && $_SESSION['isLoggedIn'] === true) 
    {
        $redirect = Navigation::$URL_HOME;
        header("Location: $redirect");
        exit;
    }
}
