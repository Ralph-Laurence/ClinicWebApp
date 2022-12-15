<?php

require_once("database/configs.php");
require_once("includes/system.php");
require_once("includes/utils.php"); 
require_once("database/dbhelper.php");

//$request = new Requests();

// if ($_SERVER['REQUEST_METHOD'] != 'POST' || !$request->isAjax())
// {
//     http_response_code(404);
//     die();
// } 
 
// global reference to PDO object
$pdo = constant('pdo');

// wraps basic sql functions like SELECT, INSERT etc..
$db = new DbHelper($pdo);

$table = TableNames::$items;
  
$medicineDataSet = null;

$sth = $pdo->prepare("SELECT * FROM $table ORDER BY ? ASC"); // WHERE item_name LIKE ?
//$sth->bindValue(1, "$filter%", PDO::PARAM_STR);
$sth->bindValue(1, "item_name", PDO::PARAM_STR);
$sth->execute();
$medicineDataSet = $sth->fetchAll(PDO::FETCH_ASSOC);
 
// foreach medicine name, get all first letters ... 
// we will use them later for dropdown filter
$medicineLeadingChars = [];
$leadingCharsDataSet = $db->get($pdo, $table, 'ASC', 'item_name'); 

if (count($leadingCharsDataSet) > 0)
{
    foreach($leadingCharsDataSet as $row)
    {
        $medicine = $row['item_name'];
        $lead = substr($medicine, 0, 1);

        if (!(in_array($lead, $medicineLeadingChars)))
            array_push($medicineLeadingChars, $lead);
    } 
}

// echo json_encode([
//     "dataSet" => $medicineDataSet,
//     "leadingChars" => $medicineLeadingChars
// ]);