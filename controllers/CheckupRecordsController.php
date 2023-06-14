<?php
@session_start();
date_default_timezone_set("Asia/Manila");
require_once("rootcwd.php");

require_once($rootCwd . "includes/urls.php");
require_once($rootCwd . "database/configs.php");
require_once($rootCwd . "database/dbhelper.php");
require_once($rootCwd . "includes/system.php");
require_once($rootCwd . "includes/utils.php"); 
require_once($rootCwd . "models/Checkup.php");
require_once($rootCwd . "models/Illness.php");
require_once($rootCwd . "models/Patient.php");
require_once($rootCwd . "models/SettingsIni.php");

require_once($rootCwd . "includes/Auth.php");
require_once($rootCwd . "includes/Security.php");

use TableFields\CheckupFields;
use TableFields\IllnessFields;
use TableFields\PatientFields;

require_once($rootCwd . "MasterLayout.php");
require_once($rootCwd . "layout-header.php");

$security = new Security();
$security->requirePermission(Chmod::PK_MEDICAL, Chmod::FLAG_READ);
$security->checkAccess(Chmod::PK_MEDICAL, UserAuth::getId());

$db = new DbHelper($pdo);
//
// Load Settings from ini file
// 
$set = new SettingsIni();

$RECORD_YEAR = 0;

$checkupsTable = TableNames::checkup_details;
$illnessTable = TableNames::illness;
$patientsTable = TableNames::patients;

$checkupsDataset = [];

// Counter Badges / Labels
$totalRecords = 0; 
$totalToday = 0;
$totalYesterday = 0;

try 
{ 
    // Load the RECORD YEAR from settings
    $RECORD_YEAR = $set->GetValue($set->sect_General, $set->iniKey_RecordYear);
      
    // The column names we want to find
    $selectFields = 
    [
        "c." . CheckupFields::$id,
        "c." . CheckupFields::$checkupNumber,
        "c." . CheckupFields::$dateCreated,
        "i." . IllnessFields::$name,
        "p." . PatientFields::$firstName,
        "p." . PatientFields::$middleName,
        "p." . PatientFields::$lastName
    ];

    // Create a filter condition where we can only 
    // limit the record to a specific patient type
    $filterCondition = !empty(getFilter()) 
        ? "AND " . PatientFields::$patientType ."=". getFilter() 
        : "";
 
    // Fieldnames to concatenate
    $c_illnessId    = CheckupFields::$illnessId;
    $c_patientFK    = CheckupFields::$patientForeignKey;
    $c_dateCreated  = $selectFields[2];
    $i_illnessId    = IllnessFields::$id;
    $p_id           = PatientFields::$id;

    // Join the field names as single string separated by comma
    $fields = implode(",", $selectFields);
 
    $sql = 
    "SELECT $fields FROM $checkupsTable c
    LEFT JOIN $illnessTable i ON i.$i_illnessId = c.$c_illnessId
    LEFT JOIN $patientsTable p ON p.$p_id = c.$c_patientFK
    WHERE $c_dateCreated LIKE '$RECORD_YEAR%' $filterCondition
    ORDER BY $c_dateCreated DESC";

    $checkupsRecord = $db->queryFetchAll($pdo, $sql); 

    $totalRecords = count($checkupsRecord); 

    if (!empty($checkupsRecord))
    {
        foreach($checkupsRecord as $row)
        {
            $fname = $row[PatientFields::$firstName];
            $mname = $row[PatientFields::$middleName];
            $lname = $row[PatientFields::$lastName]; 
            $date  = $row[CheckupFields::$dateCreated];

            // Check if record date is today or yesterday
            if (Dates::toString($date) == Dates::dateToday())
            {
                $totalToday++;
            }
            else if (Dates::toString($date) == Dates::dateYesterday())
            {
                $totalYesterday++;
            }

            // Cast array as object
            $obj = (object) array(
                "recordKey"         => $security->Encrypt($row[CheckupFields::$id]),
                "checkupNumber"     => $row[CheckupFields::$checkupNumber],
                "patientName"       => "$lname, $fname $mname",
                "illness"           => $row[IllnessFields::$name],
                "checkupDate"       => Dates::toString($date, "M. d, Y"),
                "checkupDateRaw"    => $date
            );  
            
            // Shorthand for array push
            $checkupsDataset[] = $obj;
        }
    }
} 
catch (\Exception $th) 
{   
    
    throw_response_code(500);
    exit;
}

function bindDatasetToTable()
{ 
    global $checkupsDataset;  

    if (empty($checkupsDataset))
        return;

    // Get the date today. We will use it to track the records
    // which are created today. We will do that by highlighting
    // the transaction number with Teal (bluegreen) color.
    // Otherwise, it should be marked with dark blue (indigo)
    $dateToday = date("M. d, Y") ;//Dates::dateToday("M. d, Y"); 
    $dateYesterday = Dates::dateYesterday("M. d, Y");

    // Create rows for each data/records that we can append into the table
    foreach($checkupsDataset as $obj)
    {   
        $dateLabel = $obj->checkupDate;
        $txnColor = "font-indigo";

        $checkDate = compareDate($obj->checkupDateRaw);

        if ($checkDate == 1) //($obj->checkupDate == $dateToday)
        {
            $txnColor = "font-teal";

            $dateLabel = <<<LABEL
            <span class="bg-teal text-truncate text-white rounded-6 w-100 px-2 py-1">Today</span>
            LABEL;
        }
        else if($checkDate == -1) //($obj->checkupDate == $dateYesterday)
        {
            $txnColor = "text-warning";

            $dateLabel = <<<LABEL
            <span class="bg-warning text-truncate text-white rounded-6 w-100 px-2 py-1">Yesterday</span>
            LABEL;
        }

        $illness = $obj->illness ?? "<span class=\"fst-italic\"><i class=\"fas fa-exclamation-circle me-1 text-warning\"></i>Unknown</span>";

        echo <<<TR
        <tr class="align-middle">
            <td class="px-2 text-center mx-0 row-check-parent">
                <div class="d-inline">
                    <input class="form-check-input px-0 mx-0" type="checkbox" id="row-check-box" value="" />
                </div>
            </td>
            <td class="$txnColor fw-bold th-180 text-truncate">{$obj->checkupNumber}</td>
            <td class="th-230 text-truncate">{$obj->patientName}</td>
            <td class="th-180 text-truncate">$illness</td>
            <td class="th-150">$dateLabel</td>
            <td class="th-150 text-center">
                <div class="btn-group">
                    <button type="button" class="btn btn-primary tr-action-details px-2 py-1 text-center">Details</button>
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
                <input type="text" class="record-key" value="$obj->recordKey"> 
            </td>
        </tr>
        TR;
    }
}

function getActionSuccessMessage()
{ 
    $message = "";

    if (isset($_SESSION['checkup-records-action-success']))
    {
        $message = $_SESSION['checkup-records-action-success'];
        unset($_SESSION['checkup-records-action-success']);
    }
    
    return $message;
}

function getActionErrorMessage()
{ 
    $message = "";

    if (isset($_SESSION['checkup-records-action-error']))
    {
        $message = $_SESSION['checkup-records-action-error'];
        unset($_SESSION['checkup-records-action-error']);
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
            "url"   => Pages::CHECKUP_RECORDS,
            "type"  => '',
            "label" => "All",
        ],
        [
            "url"   => Pages::CHECKUP_RECORDS . "?patients=" . PatientTypes::toDescription(PatientTypes::$STUDENT),
            "label" => PatientTypes::toDescription(PatientTypes::$STUDENT),
            "type" => PatientTypes::$STUDENT
        ],
        [
            "url"   => Pages::CHECKUP_RECORDS . "?patients=" . PatientTypes::toDescription(PatientTypes::$TEACHER),
            "label" => PatientTypes::toDescription(PatientTypes::$TEACHER),
            "type"  => PatientTypes::$TEACHER
        ],
        [
            "url"   => Pages::CHECKUP_RECORDS . "?patients=" . PatientTypes::toDescription(PatientTypes::$STAFF),
            "label" => PatientTypes::toDescription(PatientTypes::$STAFF),
            "type"  => PatientTypes::$STAFF
        ]
    ];
 
    foreach($data as $filterItem)
    { 
        $icon = (getFilter() == $filterItem["type"]) ? "selected" : "search";

        $url = $filterItem["url"];
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

        echo <<<DIV
        <div class="capsule-badge fsz-14 text-white d-flex align-items-center">
            <div class="capsule-badge-bg rounded-start px-2">
                <i class="fas fa-filter fsz-10 me-2"></i>Filter
            </div>
            <div class="bg-mdb-purple rounded-end px-2 capsule-badge-indicator">
                $filter
            </div>
        </div>
        DIV;
    }
}

function compareDate($date)
{
    /*
    $date = date('Y-m-d', $raw);
    $today = date('Y-m-d');
    $yesterday = date('Y-m-d', strtotime("-1 day"));

    if ($date == $today)
       return 1;
    else if ($date == $yesterday)
       return 0;
    */
    //$date = "2023-06-07 17:52:48";
    $timestamp = strtotime($date);

    if (date('Y-m-d', $timestamp) == date('Y-m-d')) {
        return 1;
    } elseif (date('Y-m-d', $timestamp) == date('Y-m-d', strtotime('-1 day'))) {
        return -1;
    } else {
        return 0;
    }
}