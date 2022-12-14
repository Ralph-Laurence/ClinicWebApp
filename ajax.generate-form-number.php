
<?php

@session_start();

require_once("database/configs.php");
require_once("database/dbhelper.php");
require_once("includes/system.php");
require_once("includes/utils.php");

$request = new Requests();

if ($_SERVER['REQUEST_METHOD'] != 'POST' || !$request->isAjax())
{
    http_response_code(404);
    die();
}

$pdo = constant('pdo'); 
$checkupFormNumber = Helpers::generateFormNumber($pdo);

echo $checkupFormNumber;