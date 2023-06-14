<?php
@session_start();

require_once("rootcwd.inc.php");

require_once($rootCwd . "database/configs.php");
require_once($rootCwd . "database/dbhelper.php");
require_once($rootCwd . "includes/system.php");
require_once($rootCwd . "includes/utils.php"); 
require_once($rootCwd . "includes/urls.php");

require_once($rootCwd . "errors/IError.php");

require_once($rootCwd . "includes/Security.php");
require_once($rootCwd . "includes/Auth.php");

require_once($rootCwd . "models/Patient.php");
require_once($rootCwd . "models/Doctor.php");
require_once($rootCwd . "models/SettingsIni.php");
 
use Models\Doctor;
use Models\Patient;

require_once($rootCwd . "MasterLayout.php");
require_once($rootCwd . "layout-header.php");
 
$security = new Security();
$security->requirePermission(Chmod::PK_MEDICAL, Chmod::FLAG_WRITE);
$security->checkAccess(Chmod::PK_MEDICAL, UserAuth::getId());

// Database functions helper
$db = new DbHelper($pdo);

onGuard();

// Link to go back to registration page
$registrationGoBackRoute = ENV_SITE_ROOT . Pages::REGISTER_PATIENT;

// Error Message during checkup form process
$checkupErrorMsg = "";

if (isset($_SESSION['checkup-error-msg']))
{
    $checkupErrorMsg = $_SESSION['checkup-error-msg'];
    unset($_SESSION['checkup-error-msg']);
}

/// APPOINTMENT FORWARDED FROM PATIENT DETAILS PAGE ///
$forwardPatientKey = $_POST['appointment-patient-key'] ?? "";
$patientDetails = [];
 
// Find the the last created checkup ID on checkups database table.
// Then generate a unique form number with a combination of the
// Date today, and add +1 onto the last id, then pad with 
// 4 leading zeros. ex: Y-m-d-0000id
$checkupFormNumber = Helpers::generateFormNumber();

try 
{
    // Load default physician
    $set = new SettingsIni();
    $defaultDoctorId = $set->GetValue($set->sect_General, $set->iniKey_DefaultDoctor);

    $doc = new Doctor($db);

    $defaultPhysicianName   = '';
    $defaultPhysicianKey    = '';
 
    if (!empty($defaultDoctorId))
    {
        $defaultPhysicianName   = $doc->getTitle($defaultDoctorId, true);

        if (!empty($defaultPhysicianName))
            $defaultPhysicianKey    = $security->Encrypt($defaultDoctorId);
    }

    /// APPOINTMENT FORWARDED FROM PATIENT DETAILS PAGE ///
    if (!empty($forwardPatientKey))
    {
        $patientId = $security->Decrypt($forwardPatientKey);

        $p = Patient::getFields();
        $t = TableNames::patients;

        $sql = 
        "SELECT 
            $p->idNumber AS 'idNum', 
            $p->firstName AS 'fname', 
            $p->middleName AS 'mname', 
            $p->lastName AS 'lname'
        FROM $t
        WHERE $p->id = ?";
        
        $patientDetails = $db->fetchAllParam($sql, [$patientId], true);
        //dump($patientDetails);
    }    
} 
catch (\Throwable $th) 
{ 
    echo $th->getMessage(); exit;
    IError::Throw(500);
    exit;
}

function getSuccessMessage()
{
    $msg = "";

    if (isset($_SESSION['checkup-success-msg'])) {
        $msg = $_SESSION['checkup-success-msg'];
        unset($_SESSION['checkup-success-msg']);
    }

    return $msg;
}

function loadPatientDetails($key)
{
    global $patientDetails;

    if (empty($patientDetails))
        return "";

    $data = "";

    switch($key)
    {
        case 'idNum':
            $data = $patientDetails['idNum'];
            break;
        case 'name':
            $data = implode(" ", 
            [
                $patientDetails['fname'],
                $patientDetails['mname'],
                $patientDetails['lname']
            ]);
            break;
    }

    return $data;
}

function onGuard()
{
    global $db;
 
    // Tablenames
	$t = [TableNames::doctors, TableNames::inventory, TableNames::patients, TableNames::illness];
    
    $targetPages = [];

    $sql =
	"SELECT 
		(SELECT COUNT(*) FROM $t[0]) AS Doctors,
		(SELECT COUNT(*) FROM $t[1]) AS Medicines, 
		(SELECT COUNT(*) FROM $t[2]) AS Patients,
		(SELECT COUNT(*) FROM $t[3]) AS Illnesses";

		$counts = $db->fetchAll($sql, true);

    foreach ($counts as $k => $v) 
    {
        if (empty($v)) 
        { 
            $targetPages[] = $k;
        }
    }

    if (empty($targetPages))
    {
        return;
    }

    $_SESSION['checkup-guard-target'] = $targetPages;
    Response::Redirect(Pages::CHECKUP_GUARD, Response::Code200);
}

function get_onCreatePreviewKey()
{ 
    $key = "";

    if (isset($_SESSION['preview-new-checkup-record-key']))
    { 
        $key = $_SESSION['preview-new-checkup-record-key'];
        unset($_SESSION['preview-new-checkup-record-key']);
    }

    return $key;
}