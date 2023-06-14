<?php

use Models\Supplier;

@session_start();

require_once("rootcwd.inc.php");

require_once($rootCwd . "database/configs.php");
require_once($rootCwd . "includes/system.php");
require_once($rootCwd . "includes/utils.php"); 
require_once($rootCwd . "database/dbhelper.php");
require_once($rootCwd . "includes/urls.php");

require_once($rootCwd . "includes/Auth.php");
require_once($rootCwd . "includes/Security.php");
require_once($rootCwd . "models/Supplier.php");

require_once($rootCwd . "MasterLayout.php");
require_once($rootCwd . "layout-header.php");

$security = new Security();
$security->requirePermission(Chmod::PK_SUPPLIERS, Chmod::FLAG_WRITE);
$security->checkAccess(Chmod::PK_SUPPLIERS, UserAuth::getId());
    
$fields = Supplier::getFields();

// Store the last input values here from the session
$lastInputs = [];

if (isset($_SESSION['add-supplier-last-inputs']))
{
    $lastInputs = $_SESSION['add-supplier-last-inputs'];
    unset($_SESSION['add-supplier-last-inputs']);
}
 
/// Load last input value after submit 
function loadLastInput($inputKey, $defaultValue = "")
{
    global $lastInputs;

    if (empty($lastInputs))
        return $defaultValue;

    return $lastInputs[$inputKey];
}
 
function getErrorMessage()
{
    $errorMessage = "";

    if (isset($_SESSION['supplier-action-error'])) 
    {
        $errorMessage = $_SESSION['supplier-action-error'];
        unset($_SESSION['supplier-action-error']);
    }

    return $errorMessage;
}