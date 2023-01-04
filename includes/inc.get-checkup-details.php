<?php
require_once("rootcwd.inc.php");

require_once($cwd . "database/configs.php");
require_once($cwd . "database/dbhelper.php");
require_once($cwd . "includes/system.php");
require_once($cwd . "includes/utils.php");
 
require_once($cwd . "library/defuse-crypto.phar");

use Defuse\Crypto\Crypto;
use Defuse\Crypto\Key;

$defuseKey_Ascii = Helpers::getDefuseKey($pdo);
$defuseKey = Key::loadFromAsciiSafeString($defuseKey_Ascii);

// encrypted inputs
$checkupKey = $_POST['details'] ?? "";
$checkupTxn = $_POST['txn'] ?? "";

if (empty($checkupKey) || empty($checkupTxn))
{
    throw_response_code(301, Navigation::$URL_PATIENT_RECORDS);
    exit();
}

// decrypt the inputs
$checkupId = Crypto::decrypt($checkupKey, $defuseKey);
$formNumber = Crypto::decrypt($checkupTxn, $defuseKey);
 
// reference the table names
$checkupsTable = TableNames::$checkup;
$patientTypes = TableNames::$patient_types;
$illnessTable = TableNames::$illness;

// the checkup record / dataset will be  stored here
$checkupDataset = null;

// the prescription information will be stored here
// if ever it exists ...
$prescriptionDataset = null;

try
{  
    // find the patient's record by his Checkup Number
    // Also, we need to get the descriptive patient type and descriptive illness
    // which are defined on the other table ('patient_types' and 'illness')
    $sth = $pdo->prepare(
"SELECT c.*, t.description AS 'patientType', i.name AS 'illness' FROM $checkupsTable c 
LEFT JOIN $patientTypes t ON t.id = c.patient_type
LEFT JOIN $illnessTable i ON i.id = c.illness_id
WHERE form_number =?");

    $sth->bindValue(1, $formNumber, PDO::PARAM_STR);
    $sth->execute();

    $checkupDataset = $sth->fetch(PDO::FETCH_ASSOC);

    if (empty($checkupDataset))
    {
        throw_response_code(500);
        exit();
    }

    $sth = $pdo->prepare("SELECT p.amount, u.measurement, i.item_name 
    FROM `prescription` p 
    LEFT JOIN items i ON i.id = p.item_id
    LEFT JOIN unit_measures u ON u.id = p.unit_measure
    WHERE checkup_number =?");
    $sth->bindValue(1, $formNumber);
    $sth->execute();

    $prescriptionDataset = $sth->fetchAll(PDO::FETCH_ASSOC);
}
catch(Exception $ex)
{ 
    throw_response_code(500);
    exit();
}
