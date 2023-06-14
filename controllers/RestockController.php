<?php 
@session_start();

require_once("rootcwd.php");

require_once($rootCwd . "includes/system.php");
require_once($rootCwd . "includes/urls.php"); 
require_once($rootCwd . "includes/utils.php"); 

require_once($rootCwd . "includes/Auth.php");
require_once($rootCwd . "includes/Security.php");

require_once($rootCwd . "MasterLayout.php");
require_once($rootCwd . "layout-header.php");

$security = new Security();
$security->requirePermission(Chmod::PK_INVENTORY, Chmod::FLAG_WRITE);
$security->checkAccess(Chmod::PK_INVENTORY, UserAuth::getId());