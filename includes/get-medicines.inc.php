<?php

require_once("database/configs.php");
require_once("includes/system.php");
require_once("includes/utils.php"); 
require_once("database/dbhelper.php");
  
// global reference to PDO object
$pdo = constant('pdo');

// wraps basic sql functions like SELECT, INSERT etc..
$db = new DbHelper($pdo);

$items_table = TableNames::$items;
$category_table = TableNames::$categories;
$units_table = TableNames::$unit_measures;
  
$medicineDataSet = null;

$sql = "SELECT 
i.id AS item_id,
i.item_name,
c.name AS category,
u.measurement,
i.total_stock,
i.remaining,
i.critical_level
FROM $items_table i 
LEFT JOIN $category_table c ON c.id = i.item_category
LEFT JOIN $units_table u ON u.id = i.unit_measure
ORDER BY i.item_name ASC";

$sth = $pdo->prepare($sql); 
$sth->execute();
$medicineDataSet = $sth->fetchAll(PDO::FETCH_ASSOC);
 
$medicineCategories = [];
$criticalItemsCount = 0;
$soldOutItemsCount = 0;

if (count($medicineDataSet) > 0)
{
    foreach($medicineDataSet as $row)
    { 
        $category = $row['category'];
        
        if (!in_array($category, $medicineCategories))
            array_push($medicineCategories, $category);

        $remainingQty = $row['remaining'];
        $criticalLevel = $row['critical_level'];

        if ($remainingQty == 0)
        {
            $soldOutItemsCount++;
        }

        if ($remainingQty > 0 && $remainingQty <= $criticalLevel)
        {
            $criticalItemsCount++;
        }
    }
} 