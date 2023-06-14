<?php
@session_start();

require_once("rootcwd.php");

require_once($rootCwd . "includes/urls.php");

require_once($rootCwd . "database/configs.php");
require_once($rootCwd . "database/dbhelper.php");
require_once($rootCwd . "includes/system.php");
require_once($rootCwd . "includes/utils.php");

require_once($rootCwd . "includes/Auth.php");
require_once($rootCwd . "includes/Security.php");

require_once($rootCwd . "models/Patient.php");

require_once($rootCwd . "MasterLayout.php");
require_once($rootCwd . "layout-header.php");

use Models\Patient;
use TableFields\PatientFields;

$security = new Security();
$security->requirePermission(Chmod::PK_MEDICAL, Chmod::FLAG_READ);  
$security->checkAccess(Chmod::PK_MEDICAL, UserAuth::getId());

$db = new DbHelper($pdo);

$patientsTable = TableNames::patients;
$patientTypesTable = TableNames::patient_types;
$patientFields = Patient::getFields();

$patientsDataset = [];

// Counter Badges / Labels
$totalPatients = 0;
$totalStaffs = 0;
$totalStudents = 0;
$totalTeachers = 0;

try 
{
    $select_fields =
    [
        $patientFields->id,
        $patientFields->idNumber,
        $patientFields->patientKey,
        $patientFields->patientType,
        $patientFields->dateCreated,
        $patientFields->firstName,
        $patientFields->middleName,
        $patientFields->lastName, 
    ]; 

    $condition = !empty(getFilter()) ? [$select_fields[3] => getFilter()] : [];
    
    // check if sort mode is supplied
    $sortMode = $db->ORDER_MODE_ASC;
    $sortBy   = $select_fields[7];
    $hasNewPatient = false;

    if (isset($_SESSION['register-patient-extra']))
    {
        $sortMode = $db->ORDER_MODE_DESC;
        $sortBy = $select_fields[4];
        $hasNewPatient = true;

        unset($_SESSION['register-patient-extra']);
    }

    $patientsRecord = $db->select(
        $patientsTable, 
        $select_fields, 
        $condition, 
        $sortMode, 
        $sortBy
    );

    $totalPatients = count($patientsRecord);

    if (!empty($patientsRecord))
    { 
        foreach($patientsRecord as $row)
        {
            $model = new Patient();

            $model->id = $row[PatientFields::$id];
            $model->idNumber = $row[PatientFields::$idNumber];
            $model->firstName = $row[PatientFields::$firstName]; 
            $model->middleName = $row[PatientFields::$middleName]; 
            $model->lastName = $row[PatientFields::$lastName]; 
            $model->patientType = $row[PatientFields::$patientType];
            $model->patientKey = $security->Encrypt($row[PatientFields::$patientKey]);
            $model->dateCreated = Dates::toString($row[PatientFields::$dateCreated], "M. d, Y");

            switch ($model->patientType)
            {
                case PatientTypes::$STAFF:
                    $totalStaffs++;
                    break;
                
                case PatientTypes::$STUDENT: 
                    $totalStudents++; 
                    break;

                case PatientTypes::$TEACHER: 
                    $totalTeachers++;
                    break;
            }

            array_push($patientsDataset, $model);
        }
    }
} 
catch (Exception $th) 
{  
    throw_response_code(500);
    exit;
}

function bindDatasetToTable()
{
    global $patientsDataset, $security, $hasNewPatient;

    if (empty($patientsDataset))
        return;
 
    foreach($patientsDataset as $row)
    {  
        // Encrypt record ID 
        $recordId = $security->Encrypt($row->id);
        $bgNewPatient = "";

        if ($hasNewPatient)
        {
            $bgNewPatient = 'style="background-color: #FFDF87"';
            $hasNewPatient = false;
        }

        echo <<<TR
            <tr class="align-middle" $bgNewPatient>
            <td class="px-2 text-center mx-0 row-check-parent">
                <div class="d-inline">
                    <input class="form-check-input px-0 mx-0" type="checkbox" id="row-check-box" value="" />
                </div>
            </td>
            <td class="text-primary fw-bold text-truncate th-180 td-id-num">$row->idNumber</td>
            <td class="text-truncate th-230">{$row->getFullname(true)}</td>
            <td>{$row->describePatient()}</td> 
            <td class="text-truncate th-150">$row->dateCreated</td>
            <td class="text-center">
                <div class="btn-group">
                    <button type="button" class="btn btn-primary btn-checkup-details px-2 py-1 text-center tr-action-details">Details</button>
                    <button type="button" class="btn btn-primary btn-split-arrow px-0 py-1 text-center dropdown-toggle dropdown-toggle-split" data-mdb-toggle="dropdown" aria-expanded="false"></button>
                    <ul class="dropdown-menu shadow-3-strong dropdown-menu-custom-light-small">
                        <li class="d-flex align-items-center gap-3 px-3 py-1 dropdown-item-custom-light tr-action-edit">
                            <div class="dropdown-item-icon text-center">
                                <i class="fas fa-pen fs-6 text-warning"></i>
                            </div>
                            <div class="fs-6">Edit</div>
                        </li>
                        <li class="d-flex align-items-center gap-3 px-3 py-1 dropdown-item-custom-light tr-action-delete">
                            <div class="dropdown-item-icon text-center">
                                <i class="fas fa-trash fs-6 font-red"></i>
                            </div>
                            <div class="fs-6">Delete</div>
                        </li>
                    </ul>
                </div>
            </td>
            <td class="d-none">
                <input type="text" class="record-key d-none" value="$recordId" />
            </td>
            <td class="d-none">$row->patientKey</td>
        </tr>
        TR;
    }
}

function getEditRowEmphasizeData()
{
    $json = '';

    if (isset($_SESSION['patients-edit-row-emphasis']))
    {
        $json = $_SESSION['patients-edit-row-emphasis'];
        unset($_SESSION['patients-edit-row-emphasis']);
    }

    return $json;
}

function getActionSuccessMessage()
{ 
    $message = "";

    if (isset($_SESSION['patient-records-action-success']))
    {
        $message = $_SESSION['patient-records-action-success'];
        unset($_SESSION['patient-records-action-success']);
    }
    
    return $message;
}

// This will filter the record to show only specific patient types.
// If filter is 0 or none, it will show all
function getFilter()
{
    $patientFilters =
    [
        "All" => "",
        PatientTypes::toDescription(PatientTypes::$STAFF)   => PatientTypes::$STAFF,
        PatientTypes::toDescription(PatientTypes::$TEACHER) => PatientTypes::$TEACHER,
        PatientTypes::toDescription(PatientTypes::$STUDENT) => PatientTypes::$STUDENT
    ];

    $patientFilter = $_GET['patients'] ?? '';

    // If filter is not in the list, set to '0' (ALL)
    if (!in_array($patientFilter, array_keys($patientFilters)))
        return '';

    return $patientFilters[$patientFilter];
}

function createFilterItems()
{  
    // Filter Item Data
    $data = 
    [
        [
            "url"   => Pages::PATIENTS,
            "type"  => '',
            "label" => "All",
        ],
        [
            "url"   => Pages::PATIENTS . "?patients=" . PatientTypes::toDescription(PatientTypes::$STUDENT),
            "label" => PatientTypes::toDescription(PatientTypes::$STUDENT),
            "type" => PatientTypes::$STUDENT
        ],
        [
            "url"   => Pages::PATIENTS . "?patients=" . PatientTypes::toDescription(PatientTypes::$TEACHER),
            "label" => PatientTypes::toDescription(PatientTypes::$TEACHER),
            "type"  => PatientTypes::$TEACHER
        ],
        [
            "url"   => Pages::PATIENTS . "?patients=" . PatientTypes::toDescription(PatientTypes::$STAFF),
            "label" => PatientTypes::toDescription(PatientTypes::$STAFF),
            "type"  => PatientTypes::$STAFF
        ]
    ];
 
    foreach($data as $filterItem)
    { 
        $icon = (getFilter() == $filterItem["type"]) ? "selected" : "search";

        $url   = $filterItem["url"];
        $label = $filterItem["label"];
        
        echo <<<LI
        <li>
            <a role="button" class="d-flex align-items-center gap-3 px-3 py-1 dropdown-item-custom-light" href="$url">
                <div class="dropdown-item-icon text-center"></div>
                <div class="d-flex align-items-center gap-2">
                    <img src="assets/images/icons/filter-$icon.png" width="18" height="18">
                    <div class="fs-6">$label</div>
                </div>
            </a>
        </li>
        LI;
    }
}

function createFilterBadge()
{
    if (!empty(getFilter()))
    {
        $filter = PatientTypes::toDescription(getFilter());

        echo 
        "<div class=\"capsule-badge fsz-14 text-white display-none effect-reciever\" data-transition-index=\"5\" data-transition=\"fadein\">
            <div class=\"d-flex align-items-center\">
                <div class=\"capsule-badge-bg rounded-start px-2\">
                    <i class=\"fas fa-filter fsz-10 me-2\"></i>Filter
                </div>
                <div class=\"bg-mdb-purple rounded-end px-2 capsule-badge-indicator\">
                    $filter
                </div>
            </div> 
        </div>";
    }
}