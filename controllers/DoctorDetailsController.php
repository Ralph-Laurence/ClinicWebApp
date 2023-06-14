<?php
@session_start();

require_once("rootcwd.php");

require_once($rootCwd . "includes/urls.php");

require_once($rootCwd . "database/configs.php");
require_once($rootCwd . "database/dbhelper.php");
require_once($rootCwd . "includes/system.php");
require_once($rootCwd . "includes/utils.php");

require_once($rootCwd . "models/Doctor.php");
require_once($rootCwd . "models/Degrees.php");

require_once($rootCwd . "includes/Auth.php");
require_once($rootCwd . "includes/Security.php");
require_once($rootCwd . "errors/IError.php");

require_once($rootCwd . "MasterLayout.php");
require_once($rootCwd . "layout-header.php");

use Models\Degrees;
use Models\Doctor;

$security   = new Security();
$security->BlockNonPostRequest();
$security->requirePermission(Chmod::PK_DOCTORS, Chmod::FLAG_READ);
$security->checkAccess(Chmod::PK_DOCTORS, UserAuth::getId());

$db         = new DbHelper($pdo);
$doctor     = new Doctor($db);
$docFields  = $doctor->getFields();

$detailsKey = $_POST['details-key'] ?? "";

if (empty($detailsKey))
{
    Response::Redirect(Pages::DOCTORS, 301);
}

$totalReferredPatients = 0;
// Model represents the database table as an Object. 
// We will use this to hold the data that was retrieved from database
//$model = new Patient();

// Count how many checkup history are there for this patient
//$totalCheckupHistory = 0;

try 
{
    // Details key is an encrypted Record Id. We must decrypt it first
    $recordId = $security->Decrypt($detailsKey);

    // Load the doctor from database
    $dataset = $doctor->find($recordId, "ASC", $docFields->firstName, true);
     
    // If there was no doctor data found, exit the execution
    if (empty($dataset))
    {
        onError();
    } 

    // Select patients referred to this doctor
    $getPatients = $doctor->getPatients($recordId);
    $patients = array();
    $latestPatient = [];
    
    foreach ($getPatients as $obj)
    {
        $patientId = $obj['patientId'];

        if (!in_array($patientId, $latestPatient))
        {
            $patients[] = $obj;
            $latestPatient[] = $patientId;
            $totalReferredPatients++;
        }

        $latestPatient[] = $patientId;
    }
 
} 
catch (\Exception $ex) {  onError(); }
catch (\Throwable $th) {  onError(); }
 
function onError()
{
    IError::Throw(500);
    exit;
}

function getDocName()
{
    global $dataset, $docFields;

    $name           = "{$dataset[$docFields->firstName]} {$dataset[$docFields->middleName]} {$dataset[$docFields->lastName]}";
    $degrees        = $dataset[Degrees::getFields()->degree];

    $docTitle   = !empty($degrees) 
                ? (IString::startsWith(strtolower($degrees), "dr") ? "$degrees. $name" : "$name, $degrees") 
                : $name;

    return $docTitle;
}

function getRegNum()
{
    global $dataset, $docFields;
    return $dataset[$docFields->regNum];
}

function getSpec()
{
    global $dataset, $docFields;
    return $dataset[$docFields->spec];
}

function getContact()
{
    global $dataset, $docFields;
    return $dataset[$docFields->contact];
}

function getAddress()
{
    global $dataset, $docFields;

    $address = $dataset[$docFields->address];

    return  empty($address) ? "N/A" : $address;
}

function getJoinDate()
{
    global $dataset, $docFields;
    return "Joined, " . Dates::toString($dataset[$docFields->dateCreated], "M. d, Y");
}

function bindDatasetToTable()
{
    // global $patientsDataset;
    // global $security;

    // if (empty($patientsDataset))
    //     return;
 
    // foreach($patientsDataset as $row)
    // {  
    //     // Encrypt record ID 
    //     $recordId = $security->Encrypt($row->id);

    //     echo 
    //     "<tr class=\"align-middle\">
    //         <td>
    //             <div class=\"form-check\">
    //                 <input class=\"form-check-input\" type=\"checkbox\" value=\"\" id=\"row-check-box\" />
    //             </div>
    //         </td>
    //         <td class=\"text-primary fw-bold\">$row->idNumber</td>
    //         <td>$row->firstName</td>
    //         <td>{$row->describePatient()}</td> 
    //         <td>$row->dateCreated</td>
    //         <td class=\"text-center\">
    //             <div class=\"btn-group\">
    //                 <button type=\"button\" class=\"btn btn-primary btn-checkup-details px-2 py-1 text-center\" onclick=\"loadPatientDetails('{$recordId}')\">Details</button>
    //                 <button type=\"button\" class=\"btn btn-primary btn-split-arrow px-0 py-1 text-center dropdown-toggle dropdown-toggle-split\" data-mdb-toggle=\"dropdown\" aria-expanded=\"false\"></button>
    //                 <ul class=\"dropdown-menu dropdown-menu-custom-light-small\">
    //                     <li onclick=\"editPatient('$row->patientKey')\" class=\"d-flex align-items-center gap-3 px-3 py-1 dropdown-item-custom-light\">
    //                         <div class=\"dropdown-item-icon text-center\">
    //                             <i class=\"fas fa-pen fs-6 text-warning\"></i>
    //                         </div>
    //                         <div class=\"fs-6\">Edit</div>
    //                     </li>
    //                     <li onclick=\"deletePatient('$row->patientKey', '$row->idNumber' )\" class=\"d-flex align-items-center gap-3 px-3 py-1 dropdown-item-custom-light\">
    //                         <div class=\"dropdown-item-icon text-center\">
    //                             <i class=\"fas fa-trash fs-6 font-red\"></i>
    //                         </div>
    //                         <div class=\"fs-6\">Delete</div>
    //                     </li>
    //                 </ul>
    //             </div>
    //         </td>
    //         <td class=\"d-none\"></td>
    //     </tr>";
    // }
}
 
function getDocKey()
{
    global $detailsKey;
    return $detailsKey;
}

function loadCheckupHistory()
{
 
}

function bindCheckupHistory()
{ 
 
} 

function bindReferredPatients()
{
    global $patients, $security;
  
    foreach ($patients as $obj)
    {
        $patientType = PatientTypes::toDescription($obj['patientType']);
        $key = $security->Encrypt($obj['patientId']);

        echo <<<TR
        <tr>
            <td>
                <div class="row fsz-14"> 
                    <div class="col fw-bold text-truncate">{$obj['patientName']}</div>
                    <div class="col-4 text-end text-truncate">{$obj['illness']}</div> 
                </div>
                <div class="row fsz-12">
                    <div class="col font-teal">$patientType</div>
                    <div class="col text-muted text-end">{$obj['idNum']}</div>
                </div>
            </td>
            <td class="align-middle text-center th-150">
                <button type="button" class="btn btn-primary btn-doctor-details px-2 py-1 text-center"
                data-mdb-toggle="tooltip" title="See details about patient" data-mdb-placement="left">
                    About
                </button>
            </td>
            <td class="d-none">
                <input type="text" class="patientKey" value="$key" />
            </td>
        </tr>
        TR; 
    } 
} 