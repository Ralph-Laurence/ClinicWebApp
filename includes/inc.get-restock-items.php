<?php
require_once("rootcwd.inc.php");

require_once($cwd . "database/configs.php");
require_once($cwd . "includes/system.php");
require_once($cwd . "includes/utils.php");
require_once($cwd . "database/dbhelper.php");

// wraps basic sql functions like SELECT, INSERT etc..
$db = new DbHelper($pdo);

$items_table = TableNames::$items;
$category_table = TableNames::$categories;
$units_table = TableNames::$unit_measures;
$suppliers_table = TableNames::$suppleirs;
$category_icons = TableNames::$category_icons;

$medicineDataSet = null;

try 
{
    $sql = "SELECT
i.id,
n.fas_icon,
i.item_name,
c.name AS category,
i.item_code,
i.remaining,
i.critical_level,
u.measurement
FROM $items_table i
LEFT JOIN $category_table c ON c.id = i.item_category
LEFT JOIN $category_icons n ON n.id = c.fas_icon_id
LEFT JOIN $units_table u ON u.id = i.unit_measure
ORDER BY i.item_name ASC";

    // get medicines dataset
    $sth = $pdo->prepare($sql);
    $sth->execute();
    $medicineDataSet = $sth->fetchAll(PDO::FETCH_ASSOC);

    $criticalItemsCount = 0;
    $soldOutItemsCount = 0;
    $medicineRecordsCount = count($medicineDataSet);

    if ($medicineRecordsCount > 0) 
    {
        foreach ($medicineDataSet as $row) 
        {
            $remainingQty = $row['remaining'];
            $criticalLevel = $row['critical_level'];

            if ($remainingQty == 0) 
                $soldOutItemsCount++;

            if ($remainingQty > 0 && $remainingQty <= $criticalLevel)
                $criticalItemsCount++;
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
        foreach ($categoriesDataSet as $row) 
        {
            $category_name = $row['name'];
            $category_id = $row['item_category'];

            if (!in_array($category_name, $medicineCategories))
                $medicineCategories[$category_name] = $category_id;
        }
    }

    return;
} 
catch (Exception $ex) 
{
    throw_response_code(500);
    exit();
}
