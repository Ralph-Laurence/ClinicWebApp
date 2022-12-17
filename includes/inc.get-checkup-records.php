<?php
require_once("rootcwd.inc.php");

require_once($cwd . "database/configs.php");
require_once($cwd . "database/dbhelper.php");
require_once($cwd . "includes/system.php");
require_once($cwd . "includes/utils.php");
  
// wraps basic sql functions like SELECT, INSERT etc..
$db = new DbHelper($pdo);

$table = TableNames::$checkup;

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

    // check if there is a month filter applied ...
    // Only this year's Month will be selected

    if (!empty($findBy)) 
    { 
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

$sql = "SELECT 
c.checkup_date,
c.checkup_time,
c.form_number,
CONCAT(c.patient_fname, ' ', c.patient_mname, ' ', c.patient_lname) AS patient_name,
t.description AS patient_type,
i.name AS 'illness'
FROM  $table c
LEFT JOIN illness i ON i.id = c.illness_id
LEFT JOIN patient_types t ON t.id = c.patient_type 
$condition 
ORDER BY c.form_number DESC";

$sth = $pdo->prepare($sql);
$sth->execute();
$checkupDataSet = $sth->fetchAll(PDO::FETCH_ASSOC);
