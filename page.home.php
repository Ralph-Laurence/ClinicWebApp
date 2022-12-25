<?php  
@session_start();

require_once("rootcwd.php");

require_once($rootCwd . "database/configs.php");
require_once($rootCwd . "includes/system.php");
require_once($rootCwd . "includes/urls.php");

// we must be logged in to view this page
if (!isset($_SESSION['isLoggedIn']) || $_SESSION['isLoggedIn'] !== true)
{
    header("Location: " . Navigation::$URL_LOGIN);
    exit;
}

header("Location: " . Navigation::$URL_CHECKUP_FORM);
exit;