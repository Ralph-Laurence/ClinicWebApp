<?php
require_once("rootcwd.inc.php");

require_once($cwd . "database/configs.php");
require_once($cwd . "includes/system.php");
require_once($cwd . "includes/utils.php"); 
require_once($cwd . "database/dbhelper.php");
   
$keyword = $_POST['input-keyword'] ?? "";
$findBy = $_POST['find-item-option'] ?? "";
$categoryOptions = $_POST['category-options'] ?? "";

$medicineDataSet = null;
$condition = "";
$orderby = "ORDER BY i.item_name ASC";

function generateCondition()
{
    global $findBy;
    global $keyword;
    global $categoryOptions;
    global $condition;
    global $orderby; 

    if (!empty($findBy))
    {
        // filter by items by category
        if (($findBy == "filter-category") && !empty($categoryOptions))
        { 
            $condition = "WHERE i.item_category = $categoryOptions";
            return;
        }
 
        switch ($findBy) 
        {
            case "filter-item-name":

                if (!empty($keyword))
                    $condition = "WHERE i.item_name LIKE '%$keyword%'";
                break;

            case "filter-item-code":

                if (!empty($keyword))
                    $condition = "WHERE i.item_code LIKE '%$keyword%'";
                break;

            case "filter-newest-item":
                $orderby = "ORDER BY i.date_added DESC";
                break;

            case "filter-soldout-item":
                $condition = "WHERE i.remaining = 0"; 
                break;

            case "filter-critical-item":
                $condition = "WHERE (i.remaining > 0 AND i.remaining <= i.critical_level)"; 
                break;
        }   
    }
}

generateCondition();

// wraps basic sql functions like SELECT, INSERT etc..
$db = new DbHelper($pdo);

$items_table = TableNames::$items;
$category_table = TableNames::$categories; 
$units_table = TableNames::$unit_measures;
$suppliers_table = TableNames::$suppleirs;
$category_icons = TableNames::$category_icons;

$sql = "SELECT
i.id,
n.fas_icon,
i.item_name,
c.name AS category,
i.item_code,
i.remaining,
i.critical_level,
u.measurement,
s.supplier_name,
i.date_added,
i.remarks
FROM $items_table i
LEFT JOIN $category_table c ON c.id = i.item_category
LEFT JOIN $category_icons n ON n.id = c.fas_icon_id
LEFT JOIN $units_table u ON u.id = i.unit_measure
LEFT JOIN $suppliers_table s ON s.id = i.supplier_id
$condition
$orderby";

// get medicines dataset
$sth = $pdo->prepare($sql); 
$sth->execute();
$medicineDataSet = $sth->fetchAll(PDO::FETCH_ASSOC);

$criticalItemsCount = 0;
$soldOutItemsCount = 0;
$medicineRecordsCount = count($medicineDataSet);

if ($medicineRecordsCount > 0)
{
    foreach($medicineDataSet as $row)
    {  
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
 
// get all medicine categories
$sql = "SELECT i.item_category, c.name
FROM $items_table i 
LEFT JOIN $category_table c ON i.item_category = c.id
ORDER BY c.name";
$sth = $pdo->prepare($sql);
$sth->execute();

$categoriesDataSet = $sth->fetchAll(PDO::FETCH_ASSOC);
$categoriesRecordsCount = count($categoriesDataSet);
$medicineCategories = [];

if ($categoriesRecordsCount > 0)
{
    foreach($categoriesDataSet as $row)
    {
        $category_name = $row['name'];
        $category_id = $row['item_category'];
        
        if (!in_array($category_name, $medicineCategories))
            $medicineCategories[$category_name] = $category_id;
    } 
}
