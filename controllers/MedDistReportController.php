<?php

use Models\Checkup;
use Models\Degrees;
use Models\Doctor;
use Models\DoctorSpecialty;
use Models\Item;
use Models\Patient;
use Models\Prescription;

@session_start();

require_once("rootcwd.inc.php");

global $rootCwd;

require_once($rootCwd . "includes/system.php");
require_once($rootCwd . "includes/utils.php"); 
require_once($rootCwd . "includes/urls.php");

require_once($rootCwd . "includes/Auth.php");
require_once($rootCwd . "includes/Security.php");
require_once($rootCwd . "errors/IError.php");

require_once($rootCwd . "models/Checkup.php");
require_once($rootCwd . "models/Doctor.php");
require_once($rootCwd . "models/Degrees.php");
require_once($rootCwd . "models/DoctorSpecialty.php");

require_once($rootCwd . "models/Prescription.php");
require_once($rootCwd . "models/Patient.php");
require_once($rootCwd . "models/Item.php");
require_once($rootCwd . "MasterLayout.php");
require_once($rootCwd . "layout-header.php");

$security = new Security(); 
 
$security->requirePermission(Chmod::PK_INVENTORY, Chmod::FLAG_READ);
$security->checkAccess(Chmod::PK_INVENTORY, UserAuth::getId());

$db     = new DbHelper($pdo);
$items  = TableNames::inventory;
$rxd    = TableNames::prescription_details;
$chd    = TableNames::checkup_details;
$pxt    = TableNames::patients;

$i      = Item::getFields(); 
$c      = Checkup::getFields();
$p      = Prescription::getFields();
$x      = Patient::getFields();

$set = new SettingsIni();

$totalStudents  = 0;
$totalStaffs    = 0;
$totalTeachers  = 0;

try 
{
    // $ini = $set->Read();

    // Find all medicine names and ids
    $temp_medicines = $db->select($items, [$i->id, $i->itemName], [], $db->ORDER_MODE_ASC, $i->itemName);
    $medicines = [];

    // Process the result set of medicines with Id as keys and Subarray as values
    foreach ($temp_medicines as $obj)
    {
        $medicines[$obj[$i->id]] = [
            'itemName' => $obj[$i->itemName]
        ];
    }

    // Find all list of given medicines
    $stmt_get_meds = $db->getInstance()->prepare
    (
        "SELECT 
            i.$i->id            AS 'itemId',
            p.$p->amount        AS 'totalMedicines',
            x.$x->patientType   AS 'patientType'
        FROM $items AS i
        LEFT JOIN $rxd AS p ON p.$p->itemId = i.$i->id
        LEFT JOIN $chd AS c ON c.$c->id = p.$p->checkupFK
        LEFT JOIN $pxt AS x ON x.$x->id = c.$c->patientFK
        WHERE YEAR (p.$p->dateCreated) = ?
        ORDER BY i.$i->itemName ASC "
    );

    $stmt_get_meds->execute([2023]);
    $medsList = $stmt_get_meds->fetchAll(PDO::FETCH_ASSOC);
    //
    // Update the subarray values of medicines result set.
    // 
    foreach ($medsList as $row)
    {
        $itemId = $row['itemId'];

        if (array_key_exists($itemId, $medicines))
        {
            // Get the total given medicines foreach patient type.
            switch ($row['patientType'])
            {
                case PatientTypes::$STUDENT:

                    // If array key doesnt exist yet, create one, then
                    // set the initial value
                    if (!array_key_exists('totalStudent', $medicines[$itemId]))
                        $medicines[$itemId]['totalStudent'] = $row['totalMedicines'];
                    // Otherwise, update the values
                    else 
                        $medicines[$itemId]['totalStudent'] += $row['totalMedicines'];

                    $totalStudents += $row['totalMedicines'];

                    break;

                case PatientTypes::$TEACHER:

                    if (!array_key_exists('totalTeacher', $medicines[$itemId]))
                        $medicines[$itemId]['totalTeacher'] = $row['totalMedicines'];
                    else 
                        $medicines[$itemId]['totalTeacher'] += $row['totalMedicines'];

                    $totalTeachers += $row['totalMedicines'];

                    break;

                case PatientTypes::$STAFF:
                    if (!array_key_exists('totalStaff', $medicines[$itemId]))
                        $medicines[$itemId]['totalStaff'] = $row['totalMedicines'];
                    else 
                        $medicines[$itemId]['totalStaff'] += $row['totalMedicines'];

                    $totalStaffs += $row['totalMedicines'];

                    break;
            } 
        }
    } 

   // dump($medicines);
} 
catch (\Throwable $th) 
{
    die("Failed to read configuration data.");
}
 
function getSuccessMessage()
{
    $message = "";

    if (isset($_SESSION['settings-action-success']))
    {
        $message = $_SESSION['settings-action-success'];
        unset($_SESSION['settings-action-success']);
    }

    return $message;
}

function bindDataset()
{
    global $medicines; 

    foreach ($medicines as $itemId => $obj)
    {
        $medicineName = $obj['itemName'];

        $zero = <<<SPAN
        <span style="color: #cecece;">0</span>
        SPAN;

        $totalStudent = $zero;
        $totalTeacher = $zero;
        $totalStaff   = $zero;

        if (array_key_exists('totalStudent', $obj))
            $totalStudent = <<<SPAN
            <span class="font-primary-dark">{$obj['totalStudent']}</span>
            SPAN;

        if (array_key_exists('totalTeacher', $obj)) 
            $totalTeacher = <<<SPAN
            <span class="font-primary-dark">{$obj['totalTeacher']}</span>
            SPAN;

        if (array_key_exists('totalStaff', $obj))
            $totalStaff = <<<SPAN
            <span class="font-primary-dark">{$obj['totalStaff']}</span>
            SPAN;

        echo <<<TR
        <tr>
            <th scope="row">$medicineName</th>
            <td class="border-start border-end">$totalStudent</td>
            <td>$totalTeacher</td>
            <td class="border-start">$totalStaff</td>
        </tr>
        TR;
    }
}

function getLogo()
{
    return ENV_SITE_ROOT . "assets/images/logo-s.png";
}

function getTotal($patientType)
{
    global $totalStudents, $totalStaffs, $totalTeachers;
    $total = 0;

    switch ($patientType)
    {
        case PatientTypes::$STUDENT:
            $total = $totalStudents;
            break;
        case PatientTypes::$STAFF:
            $total = $totalStaffs;
            break;
        case PatientTypes::$TEACHER:
            $total = $totalTeachers;
            break;
    }

    return $total;
}

function getOverall()
{
    global $totalStudents, $totalStaffs, $totalTeachers;
    
    return $totalStudents + $totalStaffs + $totalTeachers;
}

function getPreparedBy($what = 'role')
{
    if ($what == 'name')
        return implode(' ', [UserAuth::getFirstname(), UserAuth::getMiddlename(), UserAuth::getLastname()]);

    $roles = 
    [
        UserRoles::SUPER_ADMIN  => "System Admin",
        UserRoles::ADMIN        => "Administrator",
        UserRoles::STAFF        => "Medical Staff"
    ];
    return $roles[UserAuth::getRole()];
}

function getApprovedBy()
{
    global $db, $set;
 
    $defaultDoctorId = $set->GetValue($set->sect_General, $set->iniKey_DefaultDoctor);

    $d = Doctor::getFields();
    $g = Degrees::getFields();
    $s = DoctorSpecialty::getFields();

    $doctors = TableNames::doctors;
    $specs = TableNames::doctor_specialties;
    $deg = TableNames::doctor_degrees;

    $stmt = $db->getInstance()->prepare
    (
        "SELECT
            d.$d->id,
            d.$d->firstName,
            d.$d->middleName,
            d.$d->lastName,
            s.$s->spec,
            g.$g->degree
        FROM $doctors AS d
        LEFT JOIN $specs AS s ON s.$s->id = d.$d->spec
        LEFT JOIN $deg AS g ON g.$g->id = d.$d->degree
        WHERE d.$d->id = ?"
    );

    $stmt->execute([$defaultDoctorId]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
     
    if (!empty($data))
    {
        $docData = [ $data[$d->firstName], $data[$d->middleName], $data[$d->lastName] ];

        if (strtolower($data[$g->degree]) == 'dr')
            array_unshift($docData, ($data[$g->degree] . ".") );
        else 
            array_push($docData, ( ", " . $data[$g->degree]) );

        return implode(' ',  $docData);
    }

    return "Unknown";
}