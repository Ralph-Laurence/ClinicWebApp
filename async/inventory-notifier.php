<?php

use Models\Item;

header("Access-Control-Allow-Origin: *");
header("Access-Control-Expose-Headers: Content-Length, X-JSON");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: *");

require_once("rootcwd.inc.php");

require_once($rootCwd . "database/configs.php");
require_once($rootCwd . "includes/system.php");
require_once($rootCwd . "models/Item.php");

require_once($rootCwd . "includes/Security.php"); 
require_once($rootCwd . "database/dbhelper.php");

$db = new DbHelper($pdo);
$security = new Security();
$security->BlockNonAjaxRequest();
$security->BlockNonPostRequest();

$item = Item::getFields();

$result = $db->select(TableNames::inventory, 
[
    $item->remaining, 
    $item->criticalLevel, 
    $item->expiryDate
]);

$totalSoldout  = 0;
$totalLowStock = 0;
$totalExpired  = 0;

foreach ($result as $obj) 
{
    $stock   = $obj[$item->remaining]; 
    $expiry  = $obj[$item->expiryDate];

    // Expired
    if (!empty($expiry) && Dates::isPast($expiry)) 
    {
        if ($stock > 0)  
            $totalExpired++;

        continue;
    } 

    // Sold out 
    if ($stock <= 0)
    { 
        $totalSoldout++;
        continue;
    }

    // Critical
    if ($stock <= $obj[$item->criticalLevel] && $stock > 0)
    { 
        $totalLowStock++;
        continue;
    }
} 

// OUTPUT JSON
header('Content-Type: application/json; charset=utf-8');
echo json_encode([
    'totalExpired' => $totalExpired,
    'totalCritical' => $totalLowStock,
    'totalSoldout' => $totalSoldout
]);