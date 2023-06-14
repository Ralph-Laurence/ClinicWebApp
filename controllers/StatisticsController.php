<?php

use Models\Checkup;
use Models\Patient;

@session_start();

require_once("rootcwd.php");

require_once($rootCwd . "includes/urls.php"); 
require_once($rootCwd . "database/configs.php");
require_once($rootCwd . "includes/system.php");
require_once($rootCwd . "includes/utils.php");

require_once($rootCwd . "MasterLayout.php");
$masterPage->includeStyle("insights.css");
require_once($rootCwd . "layout-header.php");

require_once($rootCwd . "models/Patient.php");
require_once($rootCwd . "models/Checkup.php");

require_once($rootCwd . "includes/Security.php");
require_once($rootCwd . "includes/Auth.php");

$security = new Security();

$db = new DbHelper($pdo);

$patientsTable = TableNames::patients;
$patientFields = Patient::getFields();

$checkupsTable = TableNames::checkup_details;
$checkupFields = Checkup::getFields();

try 
{
    $sql = "SELECT $patientFields->patientType AS 'type', COUNT(*) AS 'total' FROM $patientsTable GROUP by $patientFields->patientType";
    $countPatients = $db->fetchAll($sql);
} 
catch (\Throwable $th) 
{
    IError::Throw(Response::Code500);
    exit;
}

function getTotalPatients($type = "all")
{
    global $countPatients;

    $countAll = 0;
 
    foreach($countPatients as $obj)
    {
        if ($type == "all")
        {
            $countAll += $obj['total'];
        }

        if ($type == PatientTypes::$STUDENT && $obj['type'] == PatientTypes::$STUDENT)
        {
            return $obj['total'];
        }

        if ($type == PatientTypes::$TEACHER && $obj['type'] == PatientTypes::$TEACHER)
        {
            return $obj['total'];
        }

        if ($type == PatientTypes::$STAFF && $obj['type'] == PatientTypes::$STAFF)
        {
            return $obj['total'];
        }
    }

    return $countAll;
}

function getWeeklyTotalRecords()
{
    global $db, $checkupsTable, $checkupFields;

    $sql = 
    "SELECT 
        WEEK($checkupFields->dateCreated) AS 'week', 
        COUNT(*) AS 'recordsCount'
    FROM $checkupsTable
    WHERE 
        YEAR($checkupFields->dateCreated) = YEAR(NOW()) AND 
        MONTH($checkupFields->dateCreated) = MONTH(NOW()) 
    GROUP BY WEEK($checkupFields->dateCreated)";

    $totalRecords = $db->fetchAll($sql);

    return json_encode($totalRecords);
}

function getDailyTotalRecords()
{
    global $db, $checkupsTable, $checkupFields;

    $sql = 
    "SELECT 
        DAYNAME($checkupFields->dateCreated) AS day, 
        COUNT(*) AS 'recordsCount' FROM $checkupsTable
    WHERE YEARWEEK($checkupFields->dateCreated) = YEARWEEK(CURRENT_DATE) 
    GROUP BY DAYOFWEEK($checkupFields->dateCreated) 
    ORDER BY DAYOFWEEK($checkupFields->dateCreated)";

    $totalRecords = $db->fetchAll($sql);

    return json_encode($totalRecords);
}

function getMonthlyTotalRecords()
{
    global $db, $checkupsTable, $checkupFields;

    $sql = 
    "SELECT 
        DATE_FORMAT($checkupFields->dateCreated, '%b') AS month, 
        COUNT(*) AS post_count 
    FROM $checkupsTable
    WHERE YEAR($checkupFields->dateCreated) = YEAR(CURRENT_DATE) 
    GROUP BY MONTH($checkupFields->dateCreated) 
    ORDER BY MONTH($checkupFields->dateCreated)";

    $totalRecords = $db->fetchAll($sql);

    return json_encode($totalRecords);
}