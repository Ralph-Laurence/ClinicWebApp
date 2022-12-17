<?php
require_once("rootcwd.inc.php");

require_once($cwd . "database/configs.php");
require_once($cwd . "includes/system.php");
require_once($cwd . "includes/utils.php"); 
require_once($cwd . "database/dbhelper.php");
    
// wraps basic sql functions like SELECT, INSERT etc..
$db = new DbHelper($pdo);

$table = TableNames::$illness;
 
$illnessDataSet = $db->get($pdo, $table); 
  
// foreach illness name, get all first letters ... 
// we will use them later for dropdown filter
$illnessLeadingChars = []; 

if (count($illnessDataSet) > 0)
{
    foreach($illnessDataSet as $row)
    {
        $illness = $row['name'];
        $lead = substr($illness, 0, 1);

        if (!(in_array($lead, $illnessLeadingChars)))
            array_push($illnessLeadingChars, $lead);
    }
} 