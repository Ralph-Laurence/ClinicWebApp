
<?php

@session_start();

require_once("rootcwd.inc.php");

require_once($cwd . "database/configs.php");
require_once($cwd . "database/dbhelper.php");
require_once($cwd . "includes/system.php");
require_once($cwd . "includes/utils.php");

$request = new Requests();

if ($_SERVER['REQUEST_METHOD'] != 'POST' || !$request->isAjax())
{
    http_response_code(404);
    die();
}
 
$checkupFormNumber = Helpers::generateFormNumber($pdo);

echo $checkupFormNumber;