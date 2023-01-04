<?php
require_once("rootcwd.inc.php");

require_once($cwd . "database/configs.php");
require_once($cwd . "database/dbhelper.php");
require_once($cwd . "includes/system.php");
require_once($cwd . "includes/utils.php");

// Make sure that this file is only accessible by 
// the parent script that will include this file.  

// if (!defined("def_incCheckupRecord")) 
// {
//     http_response_code(404);
//     include($cwd . "errors/404.php");
//     die();
// }
  
$keyword = $_POST['input-keyword'] ?? "";
$findBy = $_POST['find-patient-option'] ?? "";
$monthOptions = $_POST['month-options'] ?? "";

$checkupDataSet = null;
$condition = "";
 
function generateCondition()
{
    global $findBy;
    global $keyword;
    global $monthOptions;
    global $condition;

    if (!empty($findBy)) 
    { 
        // check if there is a month filter applied ...
        // Only this year's Month will be selected
        if ($findBy == "filter-month" && !empty($monthOptions)) 
        {
            $formNumber = date("Y") . "-" . $monthOptions . "-";
    
            $condition = "WHERE c.form_number LIKE '$formNumber%'";

            return;
        }
    
        if (!empty($keyword)) 
        {
            switch ($findBy) 
            {
                case "filter-fname":
                    $condition = "WHERE c.patient_fname LIKE '$keyword%'";
                    break;
                case "filter-lname":
                    $condition = "WHERE c.patient_lname LIKE '$keyword%'";
                    break;
                case "filter-rec-num":
                    $condition = "WHERE c.form_number LIKE '%$keyword%'";
                    break;
            }
        }
    }
}

generateCondition();

$checkupTable = TableNames::$checkup;
$illnessTable = TableNames::$illness;
$patientTypesTable = TableNames::$patient_types;

// wraps basic sql functions like SELECT, INSERT etc..
$db = new DbHelper($pdo);

$sql = "SELECT 
c.id,
c.checkup_date,
c.checkup_time,
c.form_number,
CONCAT(c.patient_fname, ' ', c.patient_mname, ' ', c.patient_lname) AS patient_name,
t.description AS patient_type,
i.name AS 'illness'
FROM  $checkupTable c
LEFT JOIN $illnessTable i ON i.id = c.illness_id
LEFT JOIN $patientTypesTable t ON t.id = c.patient_type 
$condition 
ORDER BY c.form_number DESC";

$sth = $pdo->prepare($sql);
$sth->execute();
$checkupDataSet = $sth->fetchAll(PDO::FETCH_ASSOC);
