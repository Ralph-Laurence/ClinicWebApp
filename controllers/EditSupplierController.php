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
$db = new DbHelper($pdo);

$supplierKey = $_POST['record-key'] ?? "";
 
if (isset($_SESSION['edit-supplier-last-key']))
{
    $supplierKey = $_SESSION['edit-supplier-last-key'];
    unset($_SESSION['edit-supplier-last-key']);
}

if (empty($supplierKey))
{
    Response::Redirect((ENV_SITE_ROOT . Pages::SUPPLIERS), Response::Code301);
    exit;
}

// Store the last input values here from the session
$lastInputs = [];

if (isset($_SESSION['edit-supplier-last-inputs']))
{
    $lastInputs = $_SESSION['edit-supplier-last-inputs'];
    unset($_SESSION['edit-supplier-last-inputs']);
}
else
{
    try 
    {
        $supplierId = $security->Decrypt($supplierKey);
        $lastInputs = $db->findWhere(TableNames::suppliers, [$fields->id => $supplierId]);
 
        if (empty($lastInputs))
            onError(); 
    } 
    catch (\Exception $th) { onError(); }
    catch (\Throwable $th) { onError(); }
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

function onError()
{
    IError::Throw(500);
    exit;
}