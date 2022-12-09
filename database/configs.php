<?php

// check wether we are running on production or development (local) server
function IsLocalhost($whitelist = ['127.0.0.1', '::1']) 
{
    return in_array($_SERVER['REMOTE_ADDR'], $whitelist);
}

$host = IsLocalhost() ? "localhost" : "";
$uid = IsLocalhost() ? "root" : "";
$password = IsLocalhost() ? "" : "";
$db = IsLocalhost() ? "patient_infosys" : "";
$pdo = null;

try 
{
    $dsn = "mysql:host=$host;dbname=$db;";
    $pdo = new PDO($dsn, $uid, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    return $pdo;
} 
catch (\Throwable $th) 
{
    die("Connection to server failed!");
}