<?php
@session_start();

require_once("rootcwd.inc.php");

require_once($rootCwd . "database/configs.php");
require_once($rootCwd . "includes/system.php");
require_once($rootCwd . "includes/utils.php"); 
require_once($rootCwd . "database/dbhelper.php");
require_once($rootCwd . "includes/urls.php");
 
require_once($rootCwd . "includes/Auth.php");
require_once($rootCwd . "includes/Security.php");
require_once($rootCwd . "errors/IError.php");

require_once($rootCwd . "models/Checkup.php");
require_once($rootCwd . "models/Illness.php");
require_once($rootCwd . "models/Item.php");
require_once($rootCwd . "models/Patient.php");
require_once($rootCwd . "models/Prescription.php");
require_once($rootCwd . "models/UnitMeasure.php");
require_once($rootCwd . "models/Category.php");
require_once($rootCwd . "models/Doctor.php");

use Models\Category;
use Models\Checkup;
use Models\Degrees;
use Models\Doctor; 
use Models\Illness;
use Models\Item;
use Models\Patient;
use Models\Prescription;
use Models\UnitMeasure; 

require_once($rootCwd . "MasterLayout.php");
require_once($rootCwd . "layout-header.php");

$security = new Security();
$security->requirePermission(Chmod::PK_MEDICAL, Chmod::FLAG_WRITE);
$security->checkAccess(Chmod::PK_MEDICAL, UserAuth::getId());
$security->BlockNonPostRequest();

// Record key is the encrypted record id
$recordKey = $_POST['record-key'] ?? "";

// Make sure that the record key is present. Exit if none
if (empty($recordKey))
{
    IError::Throw(500);
    exit;
}
 
// Database functions helper
$db = new DbHelper($pdo);
 
// Tablenames
$checkupsTable = TableNames::checkup_details;
$patientsTable = TableNames::patients;
$illnessTable = TableNames::illness;
$itemsTable = TableNames::inventory;
$unitsTable = TableNames::unit_measures;
$categoryTable = TableNames::categories;
$rxTable = TableNames::prescription_details;
 
// Load all prescriptions from database then
// bind the data onto the Prescription table
$rxDataset = []; 
  
// Reference to the Models' field names
$t = Item::getFields();
$c = Checkup::getFields();
$p = Patient::getFields();
$i = Illness::getFields();
$r = Prescription::getFields();
$u = UnitMeasure::getFields();
$g = Category::getFields();
$d = Doctor::getFields(); 

$doctor = new Doctor($db);

$checkupData = [];
$illnessData = [];
$patientData = [];
$doctorsData = [];

try 
{
    $recordId = $security->Decrypt($recordKey);
     
    //-----------------------------------------//
    // TASK1: Load checkup record information  //
    //-----------------------------------------// 
 
    // Retrieve the result as ASSOCIATIVE array
    $checkupRecord = getCheckupRecord($recordId);

    if (empty($checkupRecord))
    {
        IError::Throw(500);
        exit;
    }
  
    // Bind the fetched checkup record into the Model
    $checkupData['checkupNumber']   = $checkupRecord[$c->checkupNumber];
    $checkupData['dateCreated']     = Dates::toString($checkupRecord[$c->dateCreated], "F d, Y  / h:i a");
    $checkupData['bpSystolic']      = $checkupRecord[$c->bpSystolic];
    $checkupData['bpDiastolic']     = $checkupRecord[$c->bpDiastolic];
    
    $illnessData['id']              = !empty($checkupRecord[$i->id]) ? $security->Encrypt($checkupRecord[$i->id]) : '';
    $illnessData['name']            = $checkupRecord[$i->name];

    $patientData['fullName']        = $checkupRecord[$p->lastName].", ".$checkupRecord[$p->firstName]." ".$checkupRecord[$p->middleName];
    $patientData['idNumber']        = $checkupRecord[$p->idNumber];
    $patientData['patientType']     = PatientTypes::toDescription($checkupRecord[$p->patientType]);
    
    $doctorsData['docId']           = $checkupRecord['docId'];

    // Lock record after max days
    $set = new SettingsIni();
    $daysAgo = $set->GetValue($set->sect_General, $set->iniKey_LockEditAfter);
 
    $record_date = strtotime( Dates::toString($checkupRecord[$c->dateCreated]) );

    // strtotime(date("Y-m-d")) is = today

    // Get the difference and divide into
    // total no. seconds 60/60/24 to get
    // number of days 
    $diff = (strtotime(date("Y-m-d")) - $record_date) / 60 / 60 / 24;

    if ($diff > $daysAgo)
    {
        $days = $daysAgo == 1 ? "day" : "days";

        Response::Redirect(Pages::CHECKUP_RECORDS, Response::Code301, 
        "The record was locked for editing.\r\n\r\nAs a part of the system's security feature," 
        ." all records that were made more than $daysAgo $days ago will be locked for editing to ensure data integrity.",
        'checkup-records-action-error'
        );
        exit;
    }

    //-----------------------------------------//
    // TASK2: Load prescriptions from database //
    //-----------------------------------------//

    $rxDataset = getPrescriptions($recordId);
  
} 
catch (\Exception $th) 
{    
    IError::Throw(500);
    exit;
}
  
function getCheckupRecord($recordId)
{ 
    global $c, $d, $i, $p, $db, 
    $checkupsTable, $illnessTable, $patientsTable;
 
    $doctorsTable = TableNames::doctors;
    $degreesTable = TableNames::doctor_degrees;
 
    $g = Degrees::getFields();
 
    $select_checkup = Utils::prefixJoin("c.", [$c->checkupNumber, $c->dateCreated, $c->bpSystolic, $c->bpDiastolic]);
    $select_illness = Utils::prefixJoin("i.", [$i->id, $i->name]);
    $select_patient = Utils::prefixJoin("p.", [$p->idNumber, $p->patientType, $p->firstName, $p->middleName, $p->lastName]);
    $select_doctors = Utils::prefixJoin("d.", 
    [
        "{$d->id} AS docId", 
        "{$d->firstName}  AS docFname",
        "{$d->middleName} AS docMname", 
        "{$d->lastName}   AS docLname"
    ]);

    $sql =
    "SELECT $select_checkup, $select_illness, $select_patient, $select_doctors, g.$g->degree AS docDegree

    FROM $checkupsTable AS c
    LEFT JOIN $illnessTable  AS i ON i.$i->id = c.$c->illnessId
    LEFT JOIN $patientsTable AS p ON p.$p->id = c.$c->patientFK
    LEFT JOIN $doctorsTable  AS d ON d.$d->id = c.$c->doctorId
    LEFT JOIN $degreesTable  AS g ON g.$g->id = d.$d->degree

    WHERE c.$c->id = $recordId";

    $result = $db->fetchAll($sql, true);
    return $result;
}

function getPrescriptions($recordId)
{
    global $db, $c, $t, $g, $u, $r;
    global $rxTable, $checkupsTable, $itemsTable, $unitsTable, $categoryTable;

    $select_items = Utils::prefixJoin("t.", [$t->id, $t->itemName, $t->itemCode, $t->remaining]);

    $sql = 
    "SELECT $select_items, g.$g->name, r.$r->amount, u.$u->measurement FROM $rxTable AS r
    LEFT JOIN $checkupsTable AS c ON c.$c->id = r.$r->checkupFK
    LEFT JOIN $itemsTable    AS t ON t.$t->id = r.$r->itemId
    LEFT JOIN $categoryTable AS g ON g.$g->id = t.$t->category
    LEFT JOIN $unitsTable    AS u ON u.$u->id = t.$t->unitMeasure
    WHERE r.$r->checkupFK = $recordId";

    $result = $db->fetchAll($sql);
    return $result;
}   
  
// Error Message during edit process
function getErrorMessage()
{ 
    $msg = "";

    if (isset($_SESSION['edit-checkup-error-msg'])) {
        $msg = $_SESSION['edit-checkup-error-msg'];
        unset($_SESSION['edit-checkup-error-msg']);
    }

    return $msg;
}

function getSuccessMessage()
{
    $msg = "";

    if (isset($_SESSION['edit-checkup-success-msg'])) {
        $msg = $_SESSION['edit-checkup-success-msg'];
        unset($_SESSION['edit-checkup-success-msg']);
    }

    return $msg;
}

// 0 -> Systolic
// 1 -> Diastolic
function getBp($type = 0)
{
    global $checkupData;
    $bp = "";

    if (!empty($checkupData['bpSystolic']) && !empty($checkupData['bpDiastolic']))
    {
        switch ($type)
        {
            case 0: 
                $bp = $checkupData['bpSystolic'];
                break;
            case 1: 
                $bp = $checkupData['bpDiastolic'];
                break;
        }
    }

    return $bp;
}
//
// Info: 0 = Id
// Info: 1 = Fullname/Title
//
function getPhysician($info)
{
    global $doctorsData, $doctor, $security;

    $data = "";

    switch ($info)
    {
        case 'id':
            $data = $security->Encrypt($doctorsData['docId']);
            break;
        case 'name':
            $data = $doctor->getTitle($doctorsData['docId']);
            break;
    } 

    return $data;
}

function bindPrescriptions()
{ 
    global $rxDataset, $security;
    global $t, $g, $u, $r;
 
    if (!empty($rxDataset))
    {
        foreach ($rxDataset as $row)
        {
            if (empty($row[$t->id]))
            {
                return;
                break;
            }

            $itemName       = trim($row[$t->itemName]);
            $itemCode       = trim($row[$t->itemCode]);
            $itemId         = $security->Encrypt($row[$t->id]);
            $category       = $row[$g->name];
            $remaining      = $row[$t->remaining];
            $unit           = $row[$u->measurement];
            $stock          = $remaining ." ". $unit;
            $amount         = $row[$r->amount];

            // When an item is out of stock, it cannot be edited. 
            // Instead of showing the controls for amount, we will
            // show the actual AMOUNT instead.
            $amountControls = 
            "<div class=\"d-flex flex-row align-items-center justify-content-center gap-2\">
                <button type=\"button\" class=\"btn btn-warning bg-accent py-1 px-2 btn-qty-minus\">
                    <i class=\"fas fa-minus\"></i>
                </button>
                <div>
                    <input type=\"text\" class=\"text-center prescription-qty numeric\" data-min-qty=\"1\" value=\"$amount\" data-max-qty=\"$remaining\"/>
                </div>
                <button type=\"button\" class=\"btn btn-primary bg-teal py-1 px-2 btn-qty-plus\">
                    <i class=\"fas fa-plus\"></i>
                </button>
            </div>";

            // Check if an item's stock is still available
            if ($row[$t->remaining] == 0)
            {
                $stock = "<div class=\"stock-label-soldout d-inline-block px-2\">Out of Stock</div>";
                $amountControls = 
                "<div>
                    <input type=\"text\" class=\"text-center prescription-qty\" value=\"$amount\" readonly/>
                </div>";
            }

            echo <<<TR
            <tr class="align-middle" data-tag="$itemCode" data-trait="original">
                <td class="item-name">$itemName</td>
                <td>$category</td>
                <td class="stock-label">$stock</td>
                <td class="text-center">
                    $amountControls
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-secondary p-1 btn-remove" data-action="dequeue" data-dequeue="$itemCode">
                        <i class="fas fa-times me-1"></i>
                        <span>Remove</span>
                    </button>
                </td>
                <td class="d-none">
                    <input type="text" class="item-key" value="$itemId" />
                </td>
                <td class="d-none">
                    <input type="text" class="orig-qty" value="$amount" />
                    <input type="text" class="flag-return" value="0" />
                    <input type="text" class="flag-remove" value="0" />
                    <input type="text" class="units-label" value="$unit" />
                </td>
            </tr>
            TR;
        }
    }
}

