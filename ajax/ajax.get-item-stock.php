<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Expose-Headers: Content-Length, X-JSON");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: *");

require_once("rootcwd.inc.php");

require_once($cwd . "database/configs.php");
require_once($cwd . "includes/system.php");
require_once($cwd . "includes/utils.php"); 
require_once($cwd . "database/dbhelper.php");
   
require_once($cwd . "library/defuse-crypto.phar");

use Defuse\Crypto\Crypto;
use Defuse\Crypto\Key;

// for encryption/decryption
$defuseKey_Ascii = Helpers::getDefuseKey($pdo);
$defuseKey = Key::loadFromAsciiSafeString($defuseKey_Ascii);

$request = new Requests();

if ($_SERVER['REQUEST_METHOD'] != 'POST' && !$request->isAjax())
{
    http_response_code(404);
    exit();
}

// item key is encrypted
$itemKey = $_POST['itemKey'] ?? "";

if (empty($itemKey))
{
    http_response_code(400);
    exit();
}
 
// wraps basic sql functions like SELECT, INSERT etc..
$db = new DbHelper($pdo);

$items_table = TableNames::$items;
$category_table = TableNames::$categories;
$units_table = TableNames::$unit_measures;
$iconsTable = TableNames::$category_icons;
  
$itemDataSet = null;

try
{
    // decrypt the item key into readable id
    $itemId = Crypto::decrypt($itemKey, $defuseKey);

    $sql = "SELECT 
    n.fas_icon,
    i.id AS item_id, 
    i.item_code, 
    i.item_name, 
    c.name AS category, 
    u.measurement, 
    i.remaining 
    FROM $items_table i 
    LEFT JOIN $category_table c ON c.id = i.item_category 
    LEFT JOIN $iconsTable n ON n.id = c.fas_icon_id
    LEFT JOIN $units_table u ON u.id = i.unit_measure 
    WHERE i.id = ?;";
    
    $sth = $pdo->prepare($sql);
    $sth->bindValue(1, $itemId);
    $sth->execute();
    $itemDataSet = $sth->fetch(PDO::FETCH_ASSOC);

    foreach($itemDataSet as $k => $v)
    {
        if ($k == 'item_id')
        {
            $encryptedId = Crypto::encrypt(strval($v), $defuseKey);
            $itemDataSet['item_id'] = $encryptedId;
            break;
        } 
    } 

}
catch (Exception $ex)
{
    http_response_code(500);
    exit();
}

echo json_encode($itemDataSet);