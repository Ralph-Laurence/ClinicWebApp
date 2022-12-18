<?php

date_default_timezone_set("Asia/Manila");

define('base_url', "http://localhost/projects/clinic/"); //$_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']);

// check wether we are running on production or development (local) server
function IsLocalhost($whitelist = ['127.0.0.1', '::1']) 
{
    return in_array($_SERVER['REMOTE_ADDR'], $whitelist);
}

$host = IsLocalhost() ? "localhost" : "sql310.epizy.com";
$uid = IsLocalhost() ? "root" : "epiz_33161880";
$password = IsLocalhost() ? "" : "0WaqnWunBVB0FM";
$db = IsLocalhost() ? "patient_infosys" : "epiz_33161880_patient_infosys";
$pdo = null;

try 
{
    $dsn = "mysql:host=$host;dbname=$db;";
    $pdo = new PDO($dsn, $uid, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
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
    public static $patient_types = "patient_types";
}