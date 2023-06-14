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

require_once($rootCwd . "models/Checkup.php");
require_once($rootCwd . "models/Prescription.php");
require_once($rootCwd . "models/Item.php");
require_once($rootCwd . "models/Category.php");
require_once($rootCwd . "models/UnitMeasure.php");
require_once($rootCwd . "models/Patient.php");
require_once($rootCwd . "models/Illness.php");
require_once($rootCwd . "models/User.php");
require_once($rootCwd . "models/Doctor.php");
require_once($rootCwd . "models/DoctorSpecialty.php");

require_once($rootCwd . "MasterLayout.php");
require_once($rootCwd . "layout-header.php");

use Models\Category;
use Models\Checkup;
use Models\Doctor;
use Models\DoctorSpecialty;
use Models\Illness;
use Models\Item;
use Models\Patient;
use Models\Prescription;
use Models\UnitMeasure;
use Models\User;

$security = new Security();
$security->requirePermission(Chmod::PK_MEDICAL, Chmod::FLAG_READ);
$security->checkAccess(Chmod::PK_MEDICAL, UserAuth::getId());

$recordKey = $_POST['record-key'] ?? "";

if (isset($_SESSION['preview-new-checkup-record-key'])) 
{
    $recordKey = $_SESSION['preview-new-checkup-record-key'];
    unset($_SESSION['preview-new-checkup-record-key']);
}
else
{
    // This page must only be accessed using POST 
    $security->BlockNonPostRequest();
}

if (empty($recordKey)) { ThrowErrorPage(); }

$db = new DbHelper($pdo);
 
$checkupFields = Checkup::getFields();
$rxFields = Prescription::getFields();

try 
{
    $recordId = $security->Decrypt($recordKey);
    
    // Load checkup details
    $dataset = findRecord($recordId);

    if (empty($dataset))
        ThrowErrorPage();

    // Then bind the data to its model
    $checkupDetails = checkupDatasetToModel($dataset);

    // Load prescription details (Less required) 
    $rx_dataset = findPrescription($recordId);

} 
catch (\Exception $ex) { echo $ex->getMessage(); exit; ThrowErrorPage(); }
catch (\Throwable $th) { echo $ex->getMessage(); exit; ThrowErrorPage(); }

function findRecord($recordId)
{
    global $db;
    
    $c = Checkup::getFields();
    $p = Patient::getFields();
    $i = Illness::getFields();
    $u = User::getFields();
    $d = Doctor::getFields();
    $s = DoctorSpecialty::getFields();

    $sql = 
    "SELECT 
    c.$c->checkupNumber,    c.$c->dateCreated,
    p.$p->idNumber,         p.$p->gender,       p.$p->age, 
    p.$p->weight,           p.$p->height,       p.$p->patientType, p.$p->birthDay,
    i.$i->name AS illness,
    u.$u->role,
    s.$s->spec as ward,

    CONCAT( c.$c->bpSystolic,   '/',    c.$c->bpDiastolic)  AS bp, 
    CONCAT( p.$p->firstName,    ' ',    p.$p->middleName,   ' ',   p.$p->lastName) AS patient_name,
    CONCAT( u.$u->firstName,    ' ',    u.$u->middleName,   ' ',   u.$u->lastName) AS encodedBy,
    CONCAT( d.$d->firstName,    ' ',    d.$d->middleName,   ' ',   d.$d->lastName) AS doctorName

    FROM " . TableNames::checkup_details . " AS c

    LEFT JOIN " . TableNames::illness  . " AS i ON i.$i->id    = c.$c->illnessId
    LEFT JOIN " . TableNames::patients . " AS p ON p.$p->id    = c.$c->patientFK
    LEFT JOIN " . TableNames::users    . " AS u ON u.$u->id    = c.$c->createdBy
    LEFT JOIN " . TableNames::doctors  . " AS d ON d.$d->id    = c.$c->doctorId
    LEFT JOIN " . TableNames::doctor_specialties . " AS s ON s.$s->id = d.$d->spec

    WHERE c.$c->id = $recordId";

    $result = $db->fetchAll($sql, true);
    return $result;
}

function findPrescription($recordId)
{
    global $db, $rxFields; 

    $i = Item::getFields();
    $c = Category::getFields();
    $u = UnitMeasure::getFields();

    $sql = "SELECT i.$i->itemName, i.$i->itemCode, c.$c->name, p.$rxFields->amount, u.$u->measurement
    FROM " . TableNames::prescription_details  . " p
    LEFT JOIN " . TableNames::inventory        . " i ON i.$i->id = p.$rxFields->itemId
    LEFT JOIN " . TableNames::categories       . " c ON c.$c->id = i.$i->category
    LEFT JOIN " . TableNames::unit_measures    . " u ON u.$u->id = i.$i->unitMeasure
    WHERE p.$rxFields->checkupFK = $recordId";

    $result = $db->fetchAll($sql);
    return $result;
}

function bindPrescriptionToTable()
{
    global $rx_dataset, $rxFields;

    if (empty($rx_dataset))
        return;

    $i = Item::getFields(); 
    $u = UnitMeasure::getFields();
    $c = Category::getFields();

    echo <<<TABLE
    <table class="table table-sm table-fixed checkup-details-table">
    <thead class="style-secondary fw-bold">
        <tr>
            <th>SKU</td>
            <th>Medicine</td>
            <th>Category</td>
            <th>Quantity</td>
        </tr>
    </thead>
    <tbody>
    TABLE;

    foreach($rx_dataset as $obj)
    {
        echo <<<TR
        <tr>
            <td>{$obj[$i->itemCode]}</td>
            <td>{$obj[$i->itemName]}</td>
            <td>{$obj[$c->name]}</td>
            <td>
                {$obj[$rxFields->amount]}
                <span class="fs-6">&times;</span>
                {$obj[$u->measurement]}
            </td>
        </tr>
        TR;
    }

    echo "
        </tbody>
    </table>";
}

function getTotalPrescriptions()
{
    global $rx_dataset, $rxFields;

    // Count the prescription
    $count = count($rx_dataset);

    // Count individual items
    $items = 0;

    foreach ($rx_dataset as $obj)
    {
        $amount = intval($obj[$rxFields->amount]);
        $items += $amount;
    }

    return "$count Medicines, $items Items Total";
}

function checkupDatasetToModel($dataset)
{ 
    global $checkupFields;

    $p = Patient::getFields();
 
    $data = [
        'checkupNumber'     => $dataset[$checkupFields->checkupNumber],
        'date'              => Dates::toString($dataset[$checkupFields->dateCreated], "M. d, Y"),
        'time'              => Dates::toString($dataset[$checkupFields->dateCreated], "g:i A"),
        'bp'                => $dataset['bp'],
        'illness'           => !empty($dataset['illness']) ? $dataset['illness'] : "Unknown",
        'patientName'       => $dataset['patient_name'],
        'idNumber'          => $dataset[$p->idNumber],
        'gender'            => GenderTypes::toDescription($dataset[$p->gender]),
        'age'               => $dataset[$p->age],
        'type'              => PatientTypes::toDescription($dataset[$p->patientType]),
        'birthday'          => Dates::toString($dataset[$p->birthDay], "M. d, Y"),
        'encodedBy'         => $dataset['encodedBy'],
        'doctor'            => $dataset['doctorName'] ?? "None",
        'ward'              => $dataset['ward']
    ];

    $data['role'] = ($dataset['role'] == UserRoles::SUPER_ADMIN || $dataset['role'] == UserRoles::ADMIN) ? 
    "Administrator" : "Clinic Staff";

    // Remove .00 from weight
    if (IString::endsWith($dataset[$p->weight], ".00"))
        $data['weight'] = str_replace(".00", "", $dataset[$p->weight]) . " Kgs";
    else 
        $data['weight'] = $dataset[$p->weight] . " Kgs";

    // Remove .00 from height
    if (IString::endsWith($dataset[$p->height], ".00"))
        $data['height'] = str_replace(".00", "", $dataset[$p->height]) . " Cm";
    else 
        $data['height'] = $dataset[$p->height] . " Cm";

    return (object) $data; 
}

function ThrowErrorPage()
{
    IError::Throw(500);
    exit;
}
 
function getSuccessMessage()
{
    $msg = "";

    if (isset($_SESSION['checkup-details-success']))
    {
        $msg = $_SESSION['checkup-details-success'];
        unset($_SESSION['checkup-details-success']);
    }

    return $msg;
}