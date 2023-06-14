<?php

use Models\Category;

@session_start();

require_once("rootcwd.php");

require_once($rootCwd . "database/configs.php");
require_once($rootCwd . "database/dbhelper.php");

require_once($rootCwd . "includes/system.php");
require_once($rootCwd . "includes/utils.php"); 
require_once($rootCwd . "includes/urls.php"); 

require_once($rootCwd . "includes/Auth.php");
require_once($rootCwd . "includes/Security.php");

require_once($rootCwd . "errors/IError.php");
require_once($rootCwd . "models/Category.php");

$security = new Security();
$security->requirePermission(Chmod::PK_MAINTENANCE, Chmod::FLAG_WRITE);
$security->checkAccess(Chmod::PK_MAINTENANCE, UserAuth::getId());                        // For encryption/decryption etc..
$security->BlockNonPostRequest();                   // make sure that this script file will only execute on POST

$db     = new DbHelper($pdo);                           // Db helpwer wraps CRUD operations as functions
$fields = Category::getFields();
 
// Required Data 
$category  = $_POST['category-name'] ?? "";
$recordKey = $_POST['update-key'] ?? "";

if (Utils::hasEmpty([$category, $recordKey]))
    throwError(); 

// Optional Data 
$iconKey = $_POST['category-icon'] ?? "";
  
try
{
    $data = [ $fields->name => $category ];   

    if (!empty($iconKey))
    {
        $icon = $security->Decrypt($iconKey);
        $data[$fields->icon] = $icon;
    }

    $recordId = $security->Decrypt($recordKey);
 
    $db->update(TableNames::categories, $data, [$fields->id => $recordId]);

    Response::Redirect( (ENV_SITE_ROOT . Pages::CATEGORIES),
       Response::Code200, 
       "An item category was successfully updated.",
       'category-actions-success-msg'
    );
    exit; 
}
catch (\Exception $ex) 
{ 
    $error = $ex->getMessage();
   
    if (IString::contains($error, "for key '$fields->name'"))
    {
        Response::Redirect((ENV_SITE_ROOT . Pages::CATEGORIES),
            Response::Code500,
            "The category \"$category\" will be ignored as it is already registered in the system.",
            'category-actions-error-msg'
        );
        exit;
    }
     
    throwError(); 
} 

// Throw an Error then stop the script
function throwError()
{
    IError::Throw(500);
    exit;
} 