<?php 

// This script will be used in many parts of our application
// to get the ROOT DIRECTORY / url of our website.

// returns the current URL
$url = $_SERVER['REQUEST_URI']; 
$parts = explode('/',$url);
$dir = $_SERVER['SERVER_NAME'];
for ($i = 0; $i < count($parts) - 1; $i++) {
 $dir .= $parts[$i] . "/";
}

// We will use this in some parts of our system to get the 
// Root URL of our HOST
$rootUrl = (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . "://" . $dir;

// This is what we exactly need for getting the 
// Current Working Directory of our HOST. 
$rootCwd = str_replace("\\", "/", dirname(__FILE__)) . "/";

// Root volume directory
$rootDir = getcwd();
$rootDir = str_replace("\\", "/", $rootDir);

// super global defines
define('ROOT_URL', $rootUrl);