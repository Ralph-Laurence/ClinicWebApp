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

$items_table = TableNames::$items;
  
$medicineDataSet = null;

$sql = "SELECT 
i.item_name,
c.name AS category,
u.measurement,
i.total_stock,
i.remaining,
i.critical_level
FROM $items_table i 
LEFT JOIN categories c ON c.id = i.item_category
LEFT JOIN unit_measures u ON u.id = i.unit_measure
ORDER BY i.item_name ASC";

$sth = $pdo->prepare($sql); 
$sth->execute();
$medicineDataSet = $sth->fetchAll(PDO::FETCH_ASSOC);
 
$medicineCategories = [];

if (count($medicineDataSet) > 0)
{
    foreach($medicineDataSet as $row)
    { 
        $category = $row['category'];
        
        if (!in_array($category, $medicineCategories))
            array_push($medicineCategories, $category);
    }
}

// foreach medicine name, get all first letters ... 
// we will use them later for dropdown filter
// $medicineLeadingChars = [];
// $leadingCharsDataSet = $db->get($pdo, $table, 'ASC', 'item_name'); 

// if (count($leadingCharsDataSet) > 0)
// {
//     foreach($leadingCharsDataSet as $row)
//     {
//         $medicine = $row['item_name'];
//         $lead = substr($medicine, 0, 1);

//         if (!(in_array($lead, $medicineLeadingChars)))
//             array_push($medicineLeadingChars, $lead);
//     } 
// }

// echo json_encode([
//     "dataSet" => $medicineDataSet,
//     "leadingChars" => $medicineLeadingChars
// ]);