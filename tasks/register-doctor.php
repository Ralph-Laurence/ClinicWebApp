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

require_once($rootCwd . "models/Doctor.php");

use Models\Doctor;

$security = new Security();
$security->requirePermission(Chmod::PK_MEDICAL, Chmod::FLAG_WRITE);
$security->checkAccess(Chmod::PK_MEDICAL, UserAuth::getId());                        // For encryption/decryption etc..
$security->BlockNonPostRequest();                   // make sure that this script file will only execute on POST

$db = new DbHelper($pdo);                           // Db helpwer wraps CRUD operations as functions
$docFields  = Doctor::getFields();
 
// Required
$fname      = $_POST['fname']   ?? ""; 
$mname      = $_POST['mname']   ?? ""; 
$lname      = $_POST['lname']   ?? "";
$spec       = $_POST['spec']    ?? "";
$contact    = $_POST['contact'] ?? "";
$regnum     = $_POST['regnum']  ?? "";

// Optional
$address    = $_POST['address'] ?? "";
$degree     = $_POST['degree']  ?? "";

$specLabel    = $_POST['specLabel'] ?? 0;
$degreeLabel    = $_POST['degreeLabel'] ?? 0;

// Exit this script and throw an error if 
// atleast one of the required fields is empty
if (Utils::hasEmpty([$fname, $mname, $lname, $spec, $contact, $regnum]))
{
    throwError();
} 

try
{
    $specId = $security->Decrypt($spec);
 
    $data = [
        $docFields->firstName   => $fname,
        $docFields->middleName  => $mname,
        $docFields->lastName    => $lname,
        $docFields->spec        => $specId,
        $docFields->contact     => $contact,
        $docFields->regNum      => $regnum,
        $docFields->address     => $address,
    ];

    if (!empty($degree))
        $data[$docFields->degree] = $security->Decrypt($degree);

    $db->insert( TableNames::doctors, $data);

    Response::Redirect( (ENV_SITE_ROOT . Pages::REGISTER_DOCTOR),
        Response::Code200, 
        "A doctor was successfully added.",
        'doc-actions-success-msg'
    );
    exit;
}
catch (\Exception $ex) 
{ 
    $error = $ex->getMessage();

    $data['specLabel'] = $specLabel;
    $data['degreeLabel'] = $degreeLabel;

    $_SESSION['reg-doc-last-inputs'] = $data;
 
    if (IString::contains($error, "for key 'reg_num'"))
    {
        Response::Redirect((ENV_SITE_ROOT . Pages::REGISTER_DOCTOR),
            Response::Code500,
            "Registration number \"$regnum\" is taken.",
            'doc-action-error'
        );
        exit;
    }
    else if (IString::contains($error, "for key 'contact'"))
    {
        Response::Redirect((ENV_SITE_ROOT . Pages::REGISTER_DOCTOR),
            Response::Code500,
            "Contact number \"$contact\" is already in use.",
            'doc-action-error'
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