<?php

use LDAP\Result;
use Models\Waste;

@session_start();

require_once("rootcwd.php");

require_once($rootCwd . "database/configs.php");
require_once($rootCwd . "includes/system.php");
require_once($rootCwd . "includes/utils.php"); 
require_once($rootCwd . "includes/urls.php"); 
require_once($rootCwd . "database/dbhelper.php");

require_once($rootCwd . "models/Waste.php");  

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
$sender = $_POST['sender-key'] ?? "";

// These are referers which can use the DISCARD feature.
// These will also act as a go back route. If sendkey is not
// omitted, then by default, goes back to medicine inventory
$senders = 
[
    "1" => [ "route" => Pages::STOCK_IN, "response" => 'stockin-action-success'   ],
    "2" => [ "route" => Pages::STOCK_OUT, "response" => 'stockout-action-success' ],
    "3" => [ "route" => Pages::MEDICINE_INVENTORY, "response" => 'items-actions-success-msg' ]
];

if (empty($itemKey))
    onError();

try
{ 
    // Decrypt item key into plain string
    $itemId = $security->Decrypt($itemKey);
    
    $waste = new Waste($db);
    $waste->discardExpiredItem($itemId);
     
    // Go back to the sender page
    if (!empty($sender))
    {
        $senderId = $security->Decrypt($sender);

        $goback = $senders[$senderId];

        Response::Redirect((ENV_SITE_ROOT . $goback['route']), Response::Code200,
        "An expired item's stock was moved to waste.", 
        $goback['response']);
    }
    else
    {
        $goback = $senders['3'];

        Response::Redirect((ENV_SITE_ROOT . Pages::MEDICINE_INVENTORY), Response::Code200,
        "An expired item's stock was moved to waste.", 
        $goback['response']);
    }

    exit;
}
catch (\Exception $ex) { onError(); }
catch (\Throwable $ex) { onError(); }

function onError()
{
    IError::Throw(500);
    exit;
}