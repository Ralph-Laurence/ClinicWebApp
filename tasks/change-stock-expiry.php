<?php

use LDAP\Result;
use Models\Item;
use Models\Stock;
use Models\Waste;

@session_start();

require_once("rootcwd.php");

require_once($rootCwd . "database/configs.php");
require_once($rootCwd . "includes/system.php");
require_once($rootCwd . "includes/utils.php"); 
require_once($rootCwd . "includes/urls.php"); 
require_once($rootCwd . "database/dbhelper.php");

require_once($rootCwd . "models/Waste.php");  
require_once($rootCwd . "models/Item.php");  
require_once($rootCwd . "models/Stock.php");  

require_once($rootCwd . "includes/Auth.php");
require_once($rootCwd . "includes/Security.php");
 
$security = new Security();
$security->requirePermission(Chmod::PK_INVENTORY, Chmod::FLAG_WRITE);
$security->checkAccess(Chmod::PK_INVENTORY, UserAuth::getId());

// make sure that this script will only execute with POST request.
$security->BlockNonPostRequest();

// wraps basic sql functions like SELECT, INSERT etc..
$db = new DbHelper($pdo);

$itemKey = $_POST['item-key'] ?? "";
$stockKey = $_POST['stock-key'] ?? "";
$newExpiry = $_POST['new-expiry'] ?? "";
//$referer = $_POST['referer'] ?? "";
 
$goBack = ENV_SITE_ROOT . Pages::ITEM_DETAILS;

try 
{
    // Decrypt the keys    
    $itemId = $security->Decrypt($itemKey);
    $stockId = $security->Decrypt($stockKey);
 
    // Expiration date must not be empty or 
    // must not be the same day as today and must be a future date
    if (empty($newExpiry))
    {
        //IError::Throw(500);
        Response::Redirect($goBack, Response::Code301, "Expiry date is invalid. Please try again", 'view-item-action-error');
        exit;
    }

    if (Dates::isPast($newExpiry) || (Dates::toString($newExpiry) == Dates::dateToday()))
    {
        Response::Redirect($goBack, 
        Response::Code301, "Expiration date should NOT be a past date or equal to the current date.", 
        'view-item-action-error');

        return;
    }
 
    $s = Stock::getFields();

    $db->update(TableNames::stock, [ $s->expiry_date => date("Y-m-d", strtotime($newExpiry)) ], 
    [
        $s->item_id => $itemId,
        $s->id      => $stockId,
    ]);

    $_SESSION['item-details-key'] = $itemKey; 
    
    Response::Redirect( (ENV_SITE_ROOT . Pages::ITEM_DETAILS), 200, 'Expiry date successfully changed', 'view-item-action-success');
} 
catch (\Exception $ex) 
{ 
    IError::Throw(500);
    exit;
}
