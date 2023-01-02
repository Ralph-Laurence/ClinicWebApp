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
   
$request = new Requests();

if (!$request->isAjax())
{
    http_response_code(404);
    die();
}
 
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
i.remaining,
i.critical_level,
i.unit_measure
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
        // $category = $row['category'];
        
        // if (!in_array($category, $medicineCategories))
        //     array_push($medicineCategories, $category);

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

echo json_encode([
    "medicines"         => $medicineDataSet,
    "soldOutCount"      => $soldOutItemsCount,
    "criticalCount"     => $criticalItemsCount
]);