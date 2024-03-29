<?php
@session_start();

require_once("rootcwd.php");

require_once($rootCwd . "database/configs.php");
require_once($rootCwd . "database/dbhelper.php");

require_once($rootCwd . "includes/system.php");
require_once($rootCwd . "includes/utils.php"); 
require_once($rootCwd . "includes/urls.php"); 

require_once($rootCwd . "models/Checkup.php");
require_once($rootCwd . "models/Prescription.php");
require_once($rootCwd . "models/PrescriptionHistory.php");
require_once($rootCwd . "models/Item.php");
require_once($rootCwd . "models/Stock.php");

require_once($rootCwd . "includes/Auth.php");
require_once($rootCwd . "includes/Security.php");

require_once($rootCwd . "errors/IError.php");

use Models\Checkup;
use Models\Item;
use Models\Prescription;
use Models\PrescriptionHistory;
use Models\Stock;

$security = new Security();
$security->requirePermission(Chmod::PK_MEDICAL, Chmod::FLAG_WRITE);
$security->checkAccess(Chmod::PK_MEDICAL, UserAuth::getId());                        
$security->BlockNonPostRequest(); 

$db = new DbHelper($pdo);                           // Db helper wraps CRUD operations as functions
$dbInstance     = $db->getInstance();               // Get the PDO instance from the db helper
$checkupFields  = Checkup::getFields();             // Get the field names of Checkup Table
$itemFields     = Item::getFields();                // Get the field names of Items Table
$rxFields       = Prescription::getFields();        // Get the field names of Prescriptions Table
$stocksTable    = TableNames::stock;
$stocks         = new Stock($db);
$stockFields    = $stocks->getFields();
$inventoryTable = TableNames::inventory;
 
$illness_id_raw = $_POST['illness-id']  ?? "";      // Encrypted illness ID coming from hidden input
$patient_id_raw = $_POST['patient-key'] ?? "";      // Encrypted patient ID coming from hidden input
$doctor_id_raw  = $_POST['doctor-key']  ?? "";      // Encrypted doctor ID coming from hidden input

$bp_systolic    = $_POST['bp-systolic'] ?? 0;       // Blood pressure data (optional)
$bp_diastolic   = $_POST['bp-diastolic'] ?? 0;

$rx_raw         = $_POST['prescriptions'] ?? "";    // JSON data of prescriptions

// Generate a unique form number 
$checkupFormNumber = Helpers::generateFormNumber();

// Exit this script and throw an error if 
// atleast one of the required IDs is empty
if (Utils::hasEmpty([$patient_id_raw, $illness_id_raw, $doctor_id_raw]))
{
    throwError();
} 

try
{ 
    // Decrypt patient id and doctor ID
    $patient_id = $security->Decrypt($patient_id_raw);
    $doctor_id  = $security->Decrypt($doctor_id_raw);

    //------------------------------------------------//
    //-----------TASK 1 :: SAVE CHECKUP DATA----------//
    //------------------------------------------------//
 
    $checkupInsertData =
    [
        $checkupFields->checkupNumber   => $checkupFormNumber,
        $checkupFields->createdBy       => UserAuth::getId(),
        $checkupFields->illnessId       => $security->Decrypt($illness_id_raw),
        $checkupFields->patientFK       => $patient_id,
        $checkupFields->doctorId        => $doctor_id,
        $checkupFields->bpSystolic      => $bp_systolic,
        $checkupFields->bpDiastolic     => $bp_diastolic,
        $checkupFields->dateCreated     => date("Y-m-d H:i:s")
    ];

    $db->insert( TableNames::checkup_details, $checkupInsertData);

    //----------------------------------------------//
    //-------TASK 2 :: SAVE PRESCRIPTION DATA-------//
    //----------------------------------------------//

    // Decode the prescriptions json data
    $prescriptions = json_decode($rx_raw, true);

    // If no prescription data is available, just exit
    if (empty($prescriptions)) {
        onComplete();
    }
 
    // Find the id of the newly created checkup record based on its form number.
    // We'll use this for adding prescriptions
    $checkup_id = $db->getValue(TableNames::checkup_details, $checkupFields->id, 
    [
        $checkupFields->checkupNumber => $checkupFormNumber
    ]);

    // Fieldnames of prescription table
    $fields = 
    [   
        $rxFields->checkupFK,
        $rxFields->itemId,
        $rxFields->amount,
        $rxFields->stockFK
    ]; 

    // Query for updating the stocks
    $stmt_update_stocks = $db->getInstance()->prepare
    (
        "UPDATE $stocksTable 
            SET $stockFields->quantity = ($stockFields->quantity - ?)
        WHERE $stockFields->id = ? AND $stockFields->item_id = ?"
    );

    // Query to update the inventory
    $stmt_update_inventory = $dbInstance->prepare
    ( 
        "UPDATE $inventoryTable AS i
        SET i.$itemFields->remaining = 
        (
            SELECT SUM(s.$stockFields->quantity)
            FROM $stocksTable AS s
            WHERE s.$stockFields->item_id = i.$itemFields->id
        )
        WHERE i.$itemFields->id = ?;"
    );

    $history = new PrescriptionHistory($db);
    $rxHistory = [];
    $rxvalues = [];
    
    // These are Prescription values
    // We will use these to insert into prescriptions table
    foreach($prescriptions as $row)
    { 
        $itemId = $security->Decrypt($row['itemId']);
        
        $rxvalues[] = 
        [
            $checkup_id,
            $itemId,
            $row['quantity'],
            $row['stockId']
        ];

        // Update the stocks table
        $stmt_update_stocks->execute([$row['quantity'], $row['stockId'], $itemId]);

        // Update the inventory table
        $stmt_update_inventory->execute([$itemId]);

        $rxHistory[] = ['stockId' => $row['stockId'], 'checkupId' => $checkup_id, 'used' => $row['quantity']];
    }
    
    $history->push($rxHistory);

    // Insert / save multiple prescription data to database
    $db->insertRange(TableNames::prescription_details, $fields, $rxvalues);
 

    onComplete();
}
catch (\Exception $ex) { throwError($ex->getMessage() . " at " . $ex->getLine()); }
catch (\Throwable $th) { throwError($th->getMessage() . " at " . $th->getLine()); }

// Throw an Error then stop the script
function throwError($ex = "")
{
    if (!empty($ex))
    {
        echo $ex;
        exit; 
    }

    IError::Throw(500);
    exit;
}

// Go back to Checkup Form with success message 
function onComplete()
{
    global $db, $security, $checkupFields;

    // Go to history page
    $set = new SettingsIni();
    $actionOnComplete = $set->GetValue($set->sect_General, $set->iniKey_CheckupComplete);
    $id = $checkupFields->id;

    $sql = "SELECT $id FROM " .TableNames::checkup_details. " ORDER BY $id DESC LIMIT 1";
    $onInsert = $db->fetchAll($sql, true);

    $_SESSION['preview-new-checkup-record-key'] = $security->Encrypt($onInsert[$id]);

    if ($actionOnComplete == Checkup::ON_COMPLETE_PREVIEW)
    { 
        Response::Redirect( (ENV_SITE_ROOT . Pages::CHECKUP_DETAILS), Response::Code200,
        "Checkup record successfully saved.",
        'checkup-details-success');
        exit;
    }

    // Return to checkup form after it has been completed
    Response::Redirect( (ENV_SITE_ROOT . Pages::CHECKUP_FORM),
        Response::Code200, 
        "Checkup record successfully saved.",
        'checkup-success-msg'
    );
    exit;
} 