<?php
@session_start();

require_once("rootcwd.php");

require_once($rootCwd . "includes/urls.php");
require_once($rootCwd . "database/configs.php");
require_once($rootCwd . "database/dbhelper.php");
require_once($rootCwd . "includes/system.php");
require_once($rootCwd . "includes/utils.php");

require_once($rootCwd . "models/Patient.php");
require_once($rootCwd . "models/Checkup.php");

require_once($rootCwd . "includes/Auth.php");
require_once($rootCwd . "includes/Security.php");

require_once($cwd . "MasterLayout.php");
require_once($rootCwd . "layout-header.php");

use Models\Patient;
use TableFields\CheckupFields;
use TableFields\PatientFields;

$security = new Security();
$security->requirePermission(Chmod::PK_MEDICAL, Chmod::FLAG_READ);
$security->checkAccess(Chmod::PK_MEDICAL, UserAuth::getId());

$goBackLink = ENV_SITE_ROOT . Pages::PATIENTS;

$db = new DbHelper($pdo);

$detailsKey = $_POST['details-key'] ?? "";

if (empty($detailsKey))
{
    throw_response_code(301, Pages::PATIENTS);
    exit;
}

// Model represents the database table as an Object. 
// We will use this to hold the data that was retrieved from database
$model = new Patient();

// Count how many checkup history are there for this patient
$totalCheckupHistory = 0;

try 
{
    // Details key is an encrypted Record Id. We must decrypt it
    $recordId = $security->Decrypt($detailsKey);
    
    // Load the patient's details
    $patientDetails = $db->findWhere( TableNames::patients, [PatientFields::$id => $recordId]);

    // If there was no patient data found, exit the execution
    if (empty($patientDetails))
    {
        throw_response_code(500);
        exit;
    } 

    $model->id              = $patientDetails[PatientFields::$id];
    $model->idNumber        = $patientDetails[PatientFields::$idNumber];
    $model->patientType     = $patientDetails[PatientFields::$patientType];
    $model->firstName       = $patientDetails[PatientFields::$firstName];
    $model->middleName      = $patientDetails[PatientFields::$middleName];
    $model->lastName        = $patientDetails[PatientFields::$lastName];
    $model->birthDay        = $patientDetails[PatientFields::$birthDay];
    $model->gender          = $patientDetails[PatientFields::$gender];
    $model->age             = $patientDetails[PatientFields::$age];
    $model->address         = $patientDetails[PatientFields::$address];
    $model->weight          = floatval($patientDetails[PatientFields::$weight]) ?? 0;
    $model->height          = floatval($patientDetails[PatientFields::$height]) ?? 0;
    $model->contact         = $patientDetails[PatientFields::$contact];
    $model->parent          = $patientDetails[PatientFields::$parent];
    $model->dateCreated     = $patientDetails[PatientFields::$dateCreated];
    $model->dateUpdated     = $patientDetails[PatientFields::$dateUpdated];
} 
catch (\Throwable $th) 
{ 
    throw_response_code(500);
    exit;
}

function getGender()
{
    global $model;
    
    $color = "text-primary";
    $genderIcon = "fa-mars";
    $text = GenderTypes::toDescription($model->gender);

    if ($model->gender == GenderTypes::$FEMALE)
    {
        $color = "text-danger";
        $genderIcon = "fa-venus";
    }

    $layout = 
    "<span class=\"$color\">
        <i class=\"fas $genderIcon me-1\"></i>
        $text
    </span>";

    return $layout;
}

function bindDatasetToTable()
{
    global $patientsDataset;
    global $security;

    if (empty($patientsDataset))
        return;
 
    foreach($patientsDataset as $row)
    {  
        // Encrypt record ID 
        $recordId = $security->Encrypt($row->id);

        echo 
        "<tr class=\"align-middle\">
            <td>
                <div class=\"form-check\">
                    <input class=\"form-check-input\" type=\"checkbox\" value=\"\" id=\"row-check-box\" />
                </div>
            </td>
            <td class=\"text-primary fw-bold\">$row->idNumber</td>
            <td>$row->firstName</td>
            <td>{$row->describePatient()}</td> 
            <td>$row->dateCreated</td>
            <td class=\"text-center\">
                <div class=\"btn-group\">
                    <button type=\"button\" class=\"btn btn-primary btn-checkup-details px-2 py-1 text-center\" onclick=\"loadPatientDetails('{$recordId}')\">Details</button>
                    <button type=\"button\" class=\"btn btn-primary btn-split-arrow px-0 py-1 text-center dropdown-toggle dropdown-toggle-split\" data-mdb-toggle=\"dropdown\" aria-expanded=\"false\"></button>
                    <ul class=\"dropdown-menu dropdown-menu-custom-light-small\">
                        <li onclick=\"editPatient('$row->patientKey')\" class=\"d-flex align-items-center gap-3 px-3 py-1 dropdown-item-custom-light\">
                            <div class=\"dropdown-item-icon text-center\">
                                <i class=\"fas fa-pen fs-6 text-warning\"></i>
                            </div>
                            <div class=\"fs-6\">Edit</div>
                        </li>
                        <li onclick=\"deletePatient('$row->patientKey', '$row->idNumber' )\" class=\"d-flex align-items-center gap-3 px-3 py-1 dropdown-item-custom-light\">
                            <div class=\"dropdown-item-icon text-center\">
                                <i class=\"fas fa-trash fs-6 font-red\"></i>
                            </div>
                            <div class=\"fs-6\">Delete</div>
                        </li>
                    </ul>
                </div>
            </td>
            <td class=\"d-none\"></td>
        </tr>";
    }
}

function setBackground()
{
    // randomize background everytime we visit this page
    $backgrounds = ["bg-1", "bg-2", "bg-3"];
    shuffle($backgrounds);

    echo $backgrounds[0];
}

function calculateHeight($heightCm)
{
    // If height is not valid, leave it as 0
    if (empty($heightCm) || $heightCm < 1)
    {
        return "<span class=\"me-4\">0 Cm</span> (0 Ft.)";
    }

    // Convert CM to FT => n = n / 30.48
    $ft = $heightCm / 30.48;

    // Round to 2 decimals
    $heightFt = ceil($ft * 100) / 100;

    return "<span class=\"me-4\">$heightCm Cm</span> ($heightFt Ft.)";
}

function loadCheckupHistory()
{
    global $model;
    global $db;
    global $totalCheckupHistory;
  
    // Find all checkup data of this patient.
    // The order must be newest first (checkupdate DESCENDING)
    $checkupHistory = $db->select(
        TableNames::checkup_details, 
        [CheckupFields::$dateCreated, CheckupFields::$id], 
        [CheckupFields::$patientForeignKey => $model->id],
        $db->ORDER_MODE_DESC,
        CheckupFields::$dateCreated
    );
    
    $totalCheckupHistory = count($checkupHistory);

    return $checkupHistory;
}

function bindCheckupHistory()
{ 
    global $security;
    $checkupHistory = loadCheckupHistory();

    if (empty($checkupHistory))
    {
        echo "<div class=\"fs-5 text-secondary fw-bold\">No checkup history was found for this patient.</div>";
        return;
    }

    // Then extract only the checkup date, create a date card foreach of them
    foreach($checkupHistory as $row)
    {
        $date = $row[CheckupFields::$dateCreated];
        $recordId = $security->Encrypt($row[CheckupFields::$id]);

        createCheckupPreviewCard($date, $recordId); 
    }
}

function createCheckupPreviewCard($checkupDate, $recordId)
{
    $dayName = Dates::toString($checkupDate, "l");
    $dayNumber = Dates::toString($checkupDate, "d");
    $month = Dates::toString($checkupDate, "M"); 
    $year = Dates::toString($checkupDate, "Y");
 
    // 1- get the date yesterday
    $yesterday  = Dates::dateYesterday();

    // 2- get the date today
    $today = Dates::dateToday();

    // 3- format the checkup day as Y-m-d
    $checkupday = Dates::toString($checkupDate, "Y-m-d");

    // Check if date is today, yesterday or the past days.
    // The color code for today is TEAL with white text, 
    // yesterday is AMBER with dark text, and 
    // past is LIGHT BLUE background with blue text
    $bgColor = "bg-light-blue";
    $fontColor = "text-primary";
    $monthYearText = strtoupper("$month $year");

    switch ($checkupday)
    {
        case $today: 
            $bgColor = "bg-teal";
            $fontColor = "text-white";
            $monthYearText = "Today";
            break;
        
        case $yesterday: 
            $bgColor = "bg-amber";
            $fontColor = "text-dark";
            $monthYearText = "Yesterday";
            break;
    }

    echo 
    "<div class=\"checkup-card d-flex flex-column shadow-2-strong\">
        <div class=\"$bgColor $fontColor flex-grow-1 py-2 day-header text-center rounded-top position-relative\">
            <small class=\"day-name text-uppercase\">$dayName</small>
            <div class=\"fs-1 day-number\">$dayNumber</div>
            <div class=\"click-overlay w-100 h-100 rounded-top display-none\">
                <button class=\"btn btn-primary py-2 px-3\" onclick=\"viewCheckupDetails('$recordId')\">
                    <i class=\"fas fa-eye me-1\"></i>View
                </button>
            </div>
        </div>
        <div class=\"month-header d-flex justify-content-center align-items-center w-100 bg-white rounded-bottom\">
            $monthYearText
        </div>
    </div>";
}

// Load checkup history for this patient
loadCheckupHistory();