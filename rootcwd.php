<?php 

$url = $_SERVER['REQUEST_URI']; //returns the current URL
$parts = explode('/',$url);
$dir = $_SERVER['SERVER_NAME'];
for ($i = 0; $i < count($parts) - 1; $i++) {
 $dir .= $parts[$i] . "/";
}

$rootUrl = (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . "://" . $dir;
 
$rootCwd = str_replace("\\", "/", dirname(__FILE__)) . "/";

define("cwd", $rootCwd);