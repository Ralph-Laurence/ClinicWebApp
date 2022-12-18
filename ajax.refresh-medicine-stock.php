<?php

require_once("rootcwd.php");

require_once($rootCwd . "database/configs.php");
require_once($rootCwd . "database/dbhelper.php"); 
require_once($rootCwd . "includes/system.php");

$request = new Requests();

if ($_SERVER['REQUEST_METHOD'] != 'POST' || !$request->isAjax())
{
    http_response_code(404);
    die();
}
 
// after saving checkup record with prescription,
// refresh the table with new updated stocks

$itemsTable = TableNames::$items; 

$sql = "SELECT id, remaining FROM $itemsTable";
$sth = $pdo->query($sql);
$stocksDataset = $sth->fetchAll(PDO::FETCH_ASSOC);
  
echo json_encode(["dataset" => $stocksDataset]);
exit;