<?php

use Models\Item;
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
require_once($rootCwd . "models/Item.php");

require_once($rootCwd . "MasterLayout.php");
require_once($rootCwd . "layout-header.php");

$security = new Security();
$security->requirePermission(Chmod::PK_INVENTORY, Chmod::FLAG_WRITE);
$security->checkAccess(Chmod::PK_INVENTORY, UserAuth::getId());
    
$fields = Item::getFields();

// Store the last input values here from the session
$lastInputs = [];

if (isset($_SESSION['add-item-last-inputs']))
{
    $lastInputs = $_SESSION['add-item-last-inputs'];
    unset($_SESSION['add-item-last-inputs']);
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

    if (isset($_SESSION['items-actions-error-msg'])) 
    {
        $errorMessage = $_SESSION['items-actions-error-msg'];
        unset($_SESSION['items-actions-error-msg']);
    }

    return $errorMessage;
}

function loadItemName()
{
    global $fields;
    return loadLastInput($fields->itemName);
}

function loadItemCode()
{
    global $fields;
    return loadLastInput($fields->itemCode);
}
 
function loadStock()
{
    global $fields;
    return loadLastInput($fields->remaining);
}

function loadReserved()
{
    global $fields;
    return loadLastInput($fields->criticalLevel);
}

function loadDescription()
{
    global $fields;
    return loadLastInput($fields->remarks);
}
 
function loadSpecialInputs()
{
    $data = "";

    if (isset($_SESSION['add-item-last-special-data']))
    {
        $data = $_SESSION['add-item-last-special-data'];
        unset($_SESSION['add-item-last-special-data']);
    }
    
    return $data;
}