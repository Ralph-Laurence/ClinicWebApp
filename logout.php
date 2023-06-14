<?php 

require_once("rootcwd.php");

require_once($rootCwd . "includes/urls.php"); 

@session_start();

$_SESSION = array();

session_destroy();

header("Location: " . Pages::LOGIN);
exit;