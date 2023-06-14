<?php
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
require_once($rootCwd . "models/Supplier.php");
 
use Models\Supplier;

$security = new Security();
$security->requirePermission(Chmod::PK_SUPPLIERS, Chmod::FLAG_WRITE);
$security->checkAccess(Chmod::PK_SUPPLIERS, UserAuth::getId());                        // For encryption/decryption etc..
$security->BlockNonPostRequest();                   // make sure that this script file will only execute on POST

$db     = new DbHelper($pdo);                           // Db helpwer wraps CRUD operations as functions
$fields = Supplier::getFields();
 
$supplierKey = $_POST['edit-key'] ?? "";
 
if (empty($supplierKey))
{
    throwError();
}

// Required Data
// $data = 
// [
//     $fields->name    => $_POST['supname'] ?? "",
//     $fields->contact => $_POST['contact'] ?? "", 
// ];

// foreach (array_values($data) as $v)
// {
//     if (empty($v))
//     {
//         throwError();
//     }
// }
// // Optional Data
// $data[$fields->email]       = $_POST['email']       ?? "";
// $data[$fields->address]     = $_POST['address']     ?? "";
// $data[$fields->description] = $_POST['description'] ?? "";


// Required Data
$data = 
[
    $fields->name    => $_POST['supname'] ?? "",
    $fields->contact => $_POST['contact'] ?? "", 
];

$hasEmptyField = 0;

foreach (array_values($data) as $v)
{
    if ($v == "")
    {
        $hasEmptyField++;
        break;
    }
}

// Optional Data
$data[$fields->email]       = $_POST['email']       ?? "";
$data[$fields->address]     = $_POST['address']     ?? "";
$data[$fields->description] = $_POST['description'] ?? "";
 
if ($hasEmptyField > 0)
{
    writeError("Please fill out all required fields mark with asterisk (*)");
}

try
{  
    $supplierId = $security->Decrypt($supplierKey);

    // Unique contact and email
    // $unique = $db->select(TableNames::suppliers, ["$fields->contact AS ctx", "$fields->email AS eml"]);
    $select = implode(",", ["$fields->contact AS ctx", "$fields->email AS eml"]);
    $suppliers = TableNames::suppliers;

    $unique = $db->fetchAll("SELECT $select FROM $suppliers WHERE $fields->id <> $supplierId");

    foreach ($unique as $obj)
    {
        if (!empty($data[$fields->contact]) && $obj['ctx'] == $data[$fields->contact])
        {
            writeError("Contact number \"{$data[$fields->contact]}\" is already in use.");
            break;
        }

        if (!empty($data[$fields->email]) && $obj['eml'] == $data[$fields->email])
        {
            writeError("Email is already in use.");
            break;
        }
    }

    $db->update(TableNames::suppliers, $data, [$fields->id => $supplierId]);

    Response::Redirect( (ENV_SITE_ROOT . Pages::SUPPLIERS),
        Response::Code200, 
        "A supplier's information was successfully updated.",
        'supplier-actions-success-msg'
    );
    exit;
}
catch (\Exception $ex) 
{ 
    $error = $ex->getMessage();
 
    // $_SESSION['edit-supplier-last-key'] = $supplierKey;
    // $_SESSION['edit-supplier-last-inputs'] = $data;
 
    // if (IString::contains($error, "for key '$fields->email'"))
    // {
    //     Response::Redirect((ENV_SITE_ROOT . Pages::EDIT_SUPPLIER),
    //         Response::Code500,
    //         "Email is taken. Please try another",
    //         'supplier-action-error'
    //     );
    //     exit;
    // }
    //else 
    // if (IString::contains($error, "for key '$fields->contact'"))
    // { 
    //     Response::Redirect((ENV_SITE_ROOT . Pages::EDIT_SUPPLIER),
    //         Response::Code500,
    //         "Contact number is already in use",
    //         'supplier-action-error'
    //     ); 
    //     exit;
    // } 
    // else if (IString::contains($error, "for key '$fields->name'"))
    // {
    //     Response::Redirect((ENV_SITE_ROOT . Pages::EDIT_SUPPLIER),
    //         Response::Code500,
    //         "Supplier name already exists.",
    //         'supplier-action-error'
    //     ); 
    //     exit;
    // }

    
    if (IString::contains($error, "for key '$fields->name'"))
    {
        writeError("Supplier name already exists.");
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

function writeError($msg)
{
    global $data, $supplierKey;

    $_SESSION['edit-supplier-last-key'] = $supplierKey;
    $_SESSION['add-supplier-last-inputs'] = $data;

    Response::Redirect((ENV_SITE_ROOT . Pages::EDIT_SUPPLIER),
        Response::Code500,
        $msg,
        'supplier-action-error'
    ); 
    exit;
}