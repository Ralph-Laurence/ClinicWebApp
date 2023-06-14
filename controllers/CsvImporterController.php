<?php
@session_start();

require_once("rootcwd.inc.php");

require_once($rootCwd . "database/configs.php");
require_once($rootCwd . "includes/system.php");
require_once($rootCwd . "includes/utils.php"); 
require_once($rootCwd . "database/dbhelper.php");
require_once($rootCwd . "includes/urls.php");

require_once($rootCwd . "MasterLayout.php");
$masterPage->includeStyle("office.css");
require_once($rootCwd . "layout-header.php");


// we must be logged in to view this page
if (!isset($_SESSION['isLoggedIn']) || $_SESSION['isLoggedIn'] !== true) 
{
    header("Location: " . Pages::LOGIN);
    exit;
}

// check if the user has enough permission to view this page.
// The required permission for this page is the 
// Checkup Permission
// $perm = UserPerms::getCheckupAccess();

// Show the "Access Denied" page
// if (!UserPerms::hasAccess($perm)) 
// {
//     throw_response_code(403);
//     exit();
// }

function drawInitialTableHeaders()
{
    $alphas = range('A', 'Z');

    echo "<th scope=\"col\" data-orderable=\"false\" class=\"text-center row-number-th\"></th>\n";

    foreach($alphas as $a)
    {
        echo "<th scope=\"col\" data-orderable=\"false\" class=\"text-center office-table-headers\">$a</th>\n";
    }
}

function drawInitialTableBody($rows)
{ 
    $alphas = range('A', 'Z');

    for ($i = 1; $i <= $rows; $i++) 
    {
        
        $firstRowCell = true;
        $noHover = $firstRowCell ? "td-no-hover" : "";

        echo "<tr>\n"; 
        echo "<td class=\"row-number-th text-center $noHover\">$i</td>\n";

        for ($x = 0; $x < count($alphas); $x++) 
        { 
            echo "<td class=\"row-number-th text-center\"></td>\n";
        }  
        $firstRowCell = false;
        echo "</tr>";
    }
}