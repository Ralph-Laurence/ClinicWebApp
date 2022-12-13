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

$table = TableNames::$illness;

$filter = $_POST['filter'] ?? "all";

$illnessDataSet = null;

if ($filter == 'all') 
{
    $illnessDataSet = $db->get($pdo, $table); 
} 
else 
{
    $sth = $pdo->prepare("SELECT * FROM $table WHERE name LIKE ?");
    $sth->bindValue(1, "$filter%", PDO::PARAM_STR);
    $sth->execute();
    $illnessDataSet = $sth->fetchAll(PDO::FETCH_ASSOC);
}
 
// foreach illness name, get all first letters ... 
// we will use them later for dropdown filter
$illnessLeadingChars = [];
$leadingCharsDataSet = $db->get($pdo, $table); 

if (count($leadingCharsDataSet) > 0)
{
    foreach($leadingCharsDataSet as $row)
    {
        $illness = $row['name'];
        $lead = substr($illness, 0, 1);

        if (!(in_array($lead, $illnessLeadingChars)))
            array_push($illnessLeadingChars, $lead);
    }
}

echo json_encode([
    "dataSet" => $illnessDataSet,
    "leadingChars" => $illnessLeadingChars
]);