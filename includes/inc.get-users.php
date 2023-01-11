<?php
require_once("rootcwd.inc.php");

require_once($cwd . "database/configs.php");
require_once($cwd . "includes/system.php");
require_once($cwd . "includes/utils.php"); 
require_once($cwd . "database/dbhelper.php");
   
// $keyword = $_POST['input-keyword'] ?? "";
// $findBy = $_POST['find-item-option'] ?? "";
// $categoryOptions = $_POST['category-options'] ?? "";

// $medicineDataSet = null;
// $condition = "";
// $orderby = "ORDER BY i.item_name ASC";

/*
function generateCondition()
{
    global $findBy;
    global $keyword;
    global $categoryOptions;
    global $condition;
    global $orderby; 

    if (!empty($findBy))
    {
        // filter by items by category
        if (($findBy == "filter-category") && !empty($categoryOptions))
        { 
            $condition = "WHERE i.item_category = $categoryOptions";
            return;
        }
 
        switch ($findBy) 
        {
            case "filter-item-name":

                if (!empty($keyword))
                    $condition = "WHERE i.item_name LIKE '%$keyword%'";
                break;

            case "filter-item-code":

                if (!empty($keyword))
                    $condition = "WHERE i.item_code LIKE '%$keyword%'";
                break;

            case "filter-newest-item":
                $orderby = "ORDER BY i.date_added DESC";
                break;

            case "filter-soldout-item":
                $condition = "WHERE i.remaining = 0"; 
                break;

            case "filter-critical-item":
                $condition = "WHERE (i.remaining > 0 AND i.remaining <= i.critical_level)"; 
                break;
        }   
    }
}
*/

// generateCondition();

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
  