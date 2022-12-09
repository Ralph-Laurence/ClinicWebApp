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

    //return $pdo;
} 
catch (\Throwable $th) 
{
    die("Connection to server failed!");
}

class TableNames
{
    public static $categories = "categories";
    public static $checkup = "checkup";
    public static $illness = "illness";
    public static $items = "items";
    public static $prescription = "prescription";
    public static $suppleirs = "suppliers";
    public static $unit_measures = "unit_measures";
}