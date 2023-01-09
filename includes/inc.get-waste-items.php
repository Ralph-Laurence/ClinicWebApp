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
$category_icons = TableNames::$category_icons;
$waste_table = TableNames::$waste;

$wasteDataSet = null;

try 
{
    $sql = "SELECT
    n.fas_icon,
    w.item_id,
    i.item_name,
    i.item_code,
    c.name as 'category',
    w.amount,
    u.measurement
    FROM $waste_table w 
    LEFT JOIN $items_table i on i.id = w.item_id
    LEFT JOIN $units_table u on u.id = i.unit_measure
    LEFT JOIN $category_table c on c.id = i.item_category
    LEFT JOIN $category_icons n on n.id = c.fas_icon_id
    ORDER BY w.date_created DESC";

    // get medicines dataset
    $sth = $pdo->prepare($sql);
    $sth->execute();
    $wasteDataSet = $sth->fetchAll(PDO::FETCH_ASSOC);
   
} 
catch (Exception $ex) 
{
    echo $ex->getMessage();
    // throw_response_code(500);
    exit();
}
