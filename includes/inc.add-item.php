<?php

require_once("rootcwd.inc.php");
 
require_once($cwd . "database/configs.php");
require_once($cwd . "includes/system.php");
require_once($cwd . "includes/utils.php");
require_once($cwd . "database/dbhelper.php");

$categoriesTable = TableNames::$categories;
$categoriesDataSet = null;

$suppliersTable = TableNames::$suppleirs;
$suppliersDataSet = null;

$unitsTable = TableNames::$unit_measures;
$unitsDataSet = null;

try 
{
    // retrieve all categories
    $sql = "SELECT id, name FROM $categoriesTable"; 
    $categoriesDataSet = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

    // retrieve all suppliers
    $sql = "SELECT id, supplier_name FROM $suppliersTable"; 
    $suppliersDataSet = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

    // retrieve all unit measures
    $sql = "SELECT id, measurement FROM $unitsTable"; 
    $unitsDataSet = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
} 
catch (\Throwable $th) 
{
    http_response_code(500);
    die("Failed to retrieve item properties.");
}