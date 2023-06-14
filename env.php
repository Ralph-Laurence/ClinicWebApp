<?php 

// This function / script gets the URL to
// where this file is located. I.E. when
// this script is placed at "includes" folder,
// then it will return something like:
// http://hostname/root/includes/
//
// To get / return the root URL, it is advisable
// to place this script at the root directory 
// which return:
// http://hostname/root/

function pathUrl($dir = __DIR__)
{

    $root = "";
    $dir = str_replace('\\', '/', realpath($dir));

    //HTTPS or HTTP
    $root .= !empty($_SERVER['HTTPS']) ? 'https' : 'http';

    //HOST
    $root .= '://' . $_SERVER['HTTP_HOST'];

    //ALIAS
    if(!empty($_SERVER['CONTEXT_PREFIX'])) {
        $root .= $_SERVER['CONTEXT_PREFIX'];
        $root .= substr($dir, strlen($_SERVER[ 'CONTEXT_DOCUMENT_ROOT' ]));
    } else {
        $root .= substr($dir, strlen($_SERVER[ 'DOCUMENT_ROOT' ]));
    }

    $root .= '/';

    return $root;
}

// Define super global variable which we
// can use later throughout the system
define('ENV_SITE_ROOT', pathUrl());

// Get the base path of root directory. This should include the 
// Drive name and root folder
function basePath()
{ 
    //$dir = realpath(dirname(__FILE__) . '/..');
    $dir = realpath(dirname(__FILE__));
    $path = str_replace('\\', '/', $dir) . '/';

    return $path;
}

define('BASE_DIR', basePath());

define('UPLOADS_DIR', BASE_DIR . 'storage/uploads');
define('UPLOADS_URL', ENV_SITE_ROOT . 'storage/uploads/');


//echo !empty($_SERVER['HTTPS']) ? 'https' : 'http' . "://" . $_SERVER["SERVER_NAME"] . $_SERVER['PHP_SELF'];