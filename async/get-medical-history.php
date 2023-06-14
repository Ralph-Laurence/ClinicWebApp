<?php

use Models\Checkup;
use Models\Degrees;
use Models\Doctor;
use Models\Illness;
use Models\Item;

header("Access-Control-Allow-Origin: *");
header("Access-Control-Expose-Headers: Content-Length, X-JSON");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: *");

require_once("rootcwd.inc.php");

require_once($rootCwd . "database/configs.php");
require_once($rootCwd . "includes/system.php");

require_once($rootCwd . "models/Checkup.php");
require_once($rootCwd . "models/Illness.php");
require_once($rootCwd . "models/Doctor.php");
require_once($rootCwd . "models/Degrees.php");
require_once($rootCwd . "models/SettingsIni.php");

require_once($rootCwd . "includes/Security.php"); 
require_once($rootCwd . "database/dbhelper.php");

$db = new DbHelper($pdo);
$security = new Security();
$security->BlockNonAjaxRequest();
$security->BlockNonPostRequest();

$c = Checkup::getFields();
$i = Illness::getFields();
$d = Doctor::getFields();
$g = Degrees::getFields();

$patientKey = $_POST['patientKey'] ?? "";

if (empty($patientKey))
{
    header('HTTP/1.1 500 Internal Server Error');
    header('Content-Type: application/json; charset=utf-8');
    die(json_encode("Error reading patient key"));
}

try 
{
    $set = new SettingsIni();
    $recordYear = $set->GetValue($set->sect_General, $set->iniKey_RecordYear);
    
    $patientId = $security->Decrypt($patientKey);

    $sql = 
    "SELECT 
    c.$c->dateCreated AS 'date',
    i.$i->name AS 'illness',
    
    CASE 
        WHEN LOWER(g.$g->degree) = 'dr' THEN CONCAT(g.$g->degree, '. ', d.$d->firstName, ' ', d.$d->middleName, ' ', d.$d->lastName)
        ELSE CONCAT(d.$d->firstName, ' ', d.$d->middleName, ' ', d.$d->lastName, ', ', g.$g->degree)
    END AS 'docname'

    FROM checkup_details AS c
    LEFT JOIN illness AS i ON i.$i->id = c.$c->illnessId
    LEFT JOIN doctors AS d ON d.$d->id = c.$c->doctorId
    LEFT JOIN degrees AS g ON g.$g->id = d.$d->degree
    WHERE c.$c->patientFK = ? AND c.$c->checkupNumber LIKE '$recordYear%'
    ORDER BY c.$c->dateCreated DESC";

    $result = $db->fetchAllParam($sql, [ $patientId ]) ?? [];

    // OUTPUT JSON
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($result);
} 
catch (\Throwable $th) 
{
    //throw $th;
    header('HTTP/1.1 500 Internal Server Error');
    header('Content-Type: application/json; charset=utf-8');
    die(json_encode("Error loading medical history"));
}


