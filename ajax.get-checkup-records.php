<?php

require_once("database/configs.php");
require_once("includes/system.php");
require_once("includes/utils.php"); 
require_once("database/dbhelper.php");

$request = new Requests();

if ($_SERVER['REQUEST_METHOD'] != 'POST' || !$request->isAjax())
{
    http_response_code(404);
    die();
} 
 
// global reference to PDO object
$pdo = constant('pdo');

// wraps basic sql functions like SELECT, INSERT etc..
$db = new DbHelper($pdo);

$table = TableNames::$checkup;

$filter = $_POST['filter'] ?? "all";

$checkupDataSet = null;

if ($filter == 'all') 
{
    $sql = "SELECT 
    c.checkup_date,
    c.checkup_time,
    c.form_number,
    CONCAT(c.patient_fname, ' ', c.patient_mname, ' ', c.patient_lname) AS patient_name,
    t.description AS patient_type,
    i.name AS 'illness'
    FROM  checkup c
    LEFT JOIN illness i ON i.id = c.illness_id
    LEFT JOIN patient_types t ON t.id = c.patient_type
    ORDER BY checkup_date DESC";

    $sth = $pdo->prepare($sql);
    $sth->execute();
    $checkupDataSet = $sth->fetchAll(PDO::FETCH_ASSOC);
} 
// else 
// {
//     $sth = $pdo->prepare("SELECT * FROM $table WHERE name LIKE ?");
//     $sth->bindValue(1, "$filter%", PDO::PARAM_STR);
//     $sth->execute();
//     $checkupDataSet = $sth->fetchAll(PDO::FETCH_ASSOC);
// }
echo json_encode([
    "dataSet" => $checkupDataSet
]);