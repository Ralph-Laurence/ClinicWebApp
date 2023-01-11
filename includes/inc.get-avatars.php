<?php
require_once("rootcwd.inc.php");

require_once($cwd . "database/configs.php");
require_once($cwd . "includes/system.php");
require_once($cwd . "includes/utils.php"); 
require_once($cwd . "database/dbhelper.php");
   
// wraps basic sql functions like SELECT, INSERT etc..
$db = new DbHelper($pdo);

$usersTable = TableNames::$users;
$permsTable = TableNames::$permissions;

$sql = "SELECT 
CONCAT(u.firstname, ' ', u.middlename, ' ', u.lastname) AS 'name',
u.guid,
u.username,
u.role,
u.email,
u.avatar
FROM $usersTable u";
// $condition
// $orderby";

// get users dataset
$sth = $pdo->prepare($sql); 
$sth->execute();
$usersDataSet = $sth->fetchAll(PDO::FETCH_ASSOC);

$superAdminCount = 0;
$adminCount = 0;
$staffCount = 0;
$userRecordsCount = count($usersDataSet);
// $criticalItemsCount = 0;
// $soldOutItemsCount = 0;
// $medicineRecordsCount = count($medicineDataSet);

if ($userRecordsCount > 0)
{
    foreach($usersDataSet as $row)
    {  
        $role = $row['role']; 

        switch($role)
        {
            case 3:
                $superAdminCount++;
                break;
            case 2:
                $adminCount++;
                break;
            case 1:
                $staffCount++;
                break;
        } 
    }
}
  