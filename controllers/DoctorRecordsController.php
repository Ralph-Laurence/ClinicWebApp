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
require_once($rootCwd . "errors/IError.php");
require_once($rootCwd . "models/Doctor.php");
require_once($rootCwd . "models/Degrees.php");

require_once($rootCwd . "MasterLayout.php");
require_once($rootCwd . "layout-header.php");

use Models\Degrees;
use Models\Doctor;
use Models\DoctorSpecialty;

$security   = new Security();
$security->requirePermission(Chmod::PK_DOCTORS, Chmod::FLAG_READ);
$security->checkAccess(Chmod::PK_DOCTORS, UserAuth::getId());

$db         = new DbHelper($pdo);
$doctor     = new Doctor($db);
$docFields  = $doctor->getFields();
$docSpecs   = new DoctorSpecialty($db);

$withPatients = 0;
$withoutPatients = 0;

$set       = new SettingsIni();
$defaultDoctorId = -1;

try 
{
    $dataset = $doctor->getAll("ASC", $docFields->firstName, true);
    $totalDoctors = 0;
 
    $defaultDoctorId = $set->GetValue($set->sect_General, $set->iniKey_DefaultDoctor);
} 
catch (\Exception $ex) { onError(); }
catch (\Throwable $ex) { onError(); }

function onError()
{
    IError::Throw(500);
    exit;
}

function bindDataset()
{
    global $dataset, $docFields, $security, $docSpecs, $defaultDoctorId;

    if (empty($dataset))
        return;

    $getFilter = getFilter();
    
    $filter = $getFilter['filter'];
    $query  = $getFilter['query'];
    //
    // Filter doctor specializations 
    //
    $hasSpecFilter = ($filter == "spec" && !empty($query));
    $specs = []; 
    $specId = 0;
     
    if ($hasSpecFilter && $security->isValidHash($query))
    {
        $specs  = $docSpecs->getAll();
        $specId = $security->Decrypt($query);

        if (!array_key_exists($specId, $specs))
            $hasSpecFilter = false;
    }
    else 
    { 
        $hasSpecFilter = false;
    } 
    
    $sortAA_highest = 0;
    $iteration = 0;

    $hasDefaultDoctor = 0;
    //
    // Bind rows to table
    //
    foreach ($dataset as $row)
    { 
        // Apply specializations filter
        if ($hasSpecFilter && ($specs[$specId] != $row[DoctorSpecialty::getFields()->spec]) )
            continue;

        // Apply with/out patient filter
        $totalPatients  = $row['total_patients'];

        // With
        if ($filter == "w1" && $totalPatients < 1)
            continue;

        // Without
        if ($filter == "w0" && $totalPatients > 0)
            continue;

        $name = implode(" ", 
        [
            $row[$docFields->firstName], 
            $row[$docFields->middleName],
            $row[$docFields->lastName]
        ]);

        $hilight        = $totalPatients > 0 ? "text-primary fw-bold" : "";
        $id             = $security->Encrypt($row[$docFields->id]);
        $degrees        = $row[Degrees::getFields()->degree];

        $docTitle   = !empty($degrees) 
                    ? (IString::startsWith(strtolower($degrees), "dr") ? "$degrees. $name" : "$name, $degrees") 
                    : $name;

        $isDefault = $defaultDoctorId == $row[$docFields->id];
        $defaultDocMarker = "";
        $defaultDocColor = "";
        $defaultDocAttr = "";
        $menuList_setDefault = <<<LI
        <li class="d-flex align-items-center gap-3 px-3 py-1 dropdown-item-custom-light tr-action-set-default">
            <div class="dropdown-item-icon text-center">
                <i class="fas fa-check fs-6 text-primary"></i>
            </div>
            <div class="fs-6">Default</div>
        </li>
        LI;

        if ($isDefault)
        {
            $hasDefaultDoctor++;
            $defaultDocMarker = <<<I
            <span class="doctor-bookmark" data-mdb-toggle="tooltip" data-mdb-placement="left"
            title="The default doctor will always appear at the top of the list when the page loads and is marked with a pin icon.">
                <img src="assets/images/icons/pin.png" width="26" height="26">
            </span>
            I; 
            $defaultDocColor = "font-base";
            $defaultDocAttr = "data-default-doctor='true'";

            $menuList_setDefault = "";
            $sortAA_highest = -1;//count($dataset) + 1;
        }
        else
        {
            $sortAA_highest = $iteration;
        }

        $iteration++;

        echo <<<TR
            <tr $defaultDocAttr>
                <td class="px-2 text-center mx-0 row-check-parent">
                    <div class="d-inline">
                        <input class="form-check-input px-0 mx-0" type="checkbox" id="row-check-box" value="" />
                    </div>
                </td>
                <td class="text-truncate th-230 td-doc-name $defaultDocColor">
                    $defaultDocMarker
                    $docTitle
                </td>
                <td class="text-truncate th-150">{$row[$docFields->regNum]}</td>
                <td class="text-truncate th-150">{$row[$docFields->spec]}</td>
                <td class="text-truncate th-100 td-total-patients $hilight">$totalPatients</td>
                <td class="th-150 text-center">
                    <div class="btn-group">
                        <button type="button" class="btn btn-primary btn-doctor-details px-2 py-1 text-center">Details</button>
                        <button type="button" class="btn btn-primary btn-split-arrow px-0 py-1 text-center dropdown-toggle dropdown-toggle-split" data-mdb-toggle="dropdown" aria-expanded="false"></button>
                        <ul class="dropdown-menu shadow-3-strong dropdown-menu-custom-light-small">
                            $menuList_setDefault
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
                    <input type="text" class="record-key" value="$id"/>
                </td>
                <td class="d-none">
                    $sortAA_highest
                </td>
            </tr>
        TR;
    }

    if (empty($hasDefaultDoctor))
    {
        $_SESSION['no-default-doctor'] = "A default doctor has not been selected. Please choose one from the list and set it as the default.";
    }
}

function createFilterItems()
{   
    // Filter Item Data
    $data = 
    [
        [ "label"  => "Show All",         "filter" => '',     "action" => "onclick=\"setfilteraction('*')\"" ],
        [ "label"  => 'Specialization',   "filter" => 'spec', "action" => "data-mdb-target='#findSpecsModal' data-mdb-toggle='modal'" ],
        [ "label"  => 'With Patients',    "filter" => 'w1'  , "action" => "onclick=\"setfilteraction('w1')\"" ],
        [ "label"  => 'Without Patients', "filter" => 'w0'  , "action" => "onclick=\"setfilteraction('w0')\"" ],
    ];
 
    foreach($data as $filterItem)
    { 
        $icon = (getFilter()['filter'] == $filterItem["filter"]) ? "selected" : "search";
        $label = $filterItem["label"];
        $liAction = $filterItem["action"]; 

        echo <<<LI
        <li class="d-flex align-items-center gap-3 px-3 py-1 dropdown-item-custom-light" $liAction> 
            <div class="dropdown-item-icon text-center"></div>
            <div class="d-flex align-items-center gap-2">
                <img src="assets/images/icons/filter-$icon.png" width="18" height="18">
                <div class="fs-6">$label</div>
            </div>
        </li>
        LI;
    }
}
 
function getFilter()
{  
    return [
        'filter' => $_GET['filter'] ?? '', // filter key
        'query'  => $_GET['query']  ?? ''  // filter value
    ];
}

function createFilterBadge()
{
    $filter = getFilter();

    $filters = 
    [
        'spec' => "Specializations",
        'w0'   => 'W/o Patients',
        'w1'   => 'With Patients'
    ];

    $filterKey = getFilter()['filter'];

    if (!empty($filter) && array_key_exists($filterKey, $filters))
    {
        $label = $filters[$filterKey];

        echo 
        "<div class=\"capsule-badge fsz-14 text-white display-none effect-reciever\" data-transition-index=\"5\" data-transition=\"fadein\">
            <div class=\"d-flex align-items-center\">
                <div class=\"capsule-badge-bg rounded-start px-2\">
                    <i class=\"fas fa-filter fsz-10 me-2\"></i>Filter
                </div>
                <div class=\"bg-mdb-purple rounded-end px-2 capsule-badge-indicator\">
                    $label
                </div>
            </div> 
        </div>";
    }
}

function getSuccessMessage()
{
    $msg = "";

    if (isset($_SESSION['doc-actions-success-msg']))
    {
        $msg = $_SESSION['doc-actions-success-msg'];
        unset($_SESSION['doc-actions-success-msg']);
    }

    return $msg;
}

function getDefaultDoctorWarning()
{
    $msg = "";

    if (isset($_SESSION['no-default-doctor']))
    {
        $msg = $_SESSION['no-default-doctor'];
        unset($_SESSION['no-default-doctor']);
    }

    return $msg;
}