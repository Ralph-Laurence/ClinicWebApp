
<?php

@session_start();

require_once("database/configs.php");
require_once("database/dbhelper.php");
require_once("includes/system.php");
require_once("includes/utils.php");

$request = new Requests();

if ($_SERVER['REQUEST_METHOD'] != 'POST' || !$request->isAjax())
{
    http_response_code(404);
    die();
}

$pdo = constant('pdo');
// $db = new DbHelper($pdo);

// // generate new form number
// $lastCheckupFormId = Helpers::getLastId($pdo, TableNames::$checkup) + 1; 
// $checkupFormNumber = Dates::dateToday() . "-" . str_pad($lastCheckupFormId, 5, "0", STR_PAD_LEFT);

$checkupFormNumber = Helpers::generateFormNumber($pdo);

echo $checkupFormNumber;