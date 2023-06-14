<?php
@session_start();

require_once("rootcwd.php");

require_once($rootCwd . "database/configs.php");
require_once($rootCwd . "includes/system.php");
require_once($rootCwd . "includes/utils.php"); 
require_once($rootCwd . "includes/urls.php"); 
require_once($rootCwd . "database/dbhelper.php");
require_once($rootCwd . "models/Checkup.php");
require_once($rootCwd . "models/Prescription.php");
require_once($rootCwd . "models/Item.php");

require_once($rootCwd . "includes/Auth.php");
require_once($rootCwd . "includes/Security.php");
 
use Models\Checkup;
use Models\Item;
use Models\Prescription; 

$security = new Security();
$security->requirePermission(Chmod::PK_MEDICAL, Chmod::FLAG_WRITE);
$security->checkAccess(Chmod::PK_MEDICAL, UserAuth::getId());
$security->BlockNonPostRequest();               // Do not run this script when not accessed with POST

$db = new DbHelper($pdo);                       // Db helpwer wraps CRUD operations as functions
 
$checkupModel = new Checkup();                  // Model will hold values as object
$checkupFields = $checkupModel->getFields();

$rxModel    = new Prescription();
$rxFields   = $rxModel->getFields();
$rxTable    = TableNames::prescription_details; 

$inventoryModel = new Item($db); 

// Post the record ID and Illness ID
$illness_id_raw = $_POST['illness-id'] ?? "";
$record_id_raw  = $_POST['record-key'] ?? "";
$doctor_id_raw  = $_POST['doctor-key'] ?? ""; 

// Exit this script and throw an error if 
// atleast one of the required IDs is empty
if (Utils::hasEmpty([$record_id_raw, $illness_id_raw, $doctor_id_raw]))
{
    throwError();
} 

// Blood pressure data
$bp_systolic  = $_POST['bp-systolic']  ?? '0';
$bp_diastolic = $_POST['bp-diastolic'] ?? '0';
 
$prescriptions_raw = $_POST['prescriptions'] ?? "";     // JSON data of prescriptions
$prescriptions = [];                                    // Store prescription data here as an array

// Decode json data if available.
// After decode, the result will be an array of objects
if (!empty($prescriptions_raw))
    $prescriptions = json_decode($prescriptions_raw, true);

try
{  
    $record_id  = $security->Decrypt($record_id_raw);
    $illness_id = $security->Decrypt($illness_id_raw);
    $doctor_id  = $security->Decrypt($doctor_id_raw);

    //==========================================//
    //      TASK 1: UPDATE CHECKUP DATA         //
    //==========================================//

    $db->update( TableNames::checkup_details, 
    [
        $checkupFields->illnessId   => $illness_id,
        $checkupFields->bpSystolic  => $bp_systolic,
        $checkupFields->bpDiastolic => $bp_diastolic,
        $checkupFields->doctorId    => $doctor_id
    ],
    [ $checkupFields->id => $record_id ]);
 
    //==========================================//
    //      TASK 2: UPDATE PRESCRIPTION         //
    //==========================================//

    // If no prescription data is available, just exit after update
    if (empty($prescriptions))
        onComplete();

    $updateItems = [];              // Items to update the qty from, which is coming from POST
    $removeItems = [];              // ID of each of the Item that we want to remove

    $incrementStock  = [];          // Returned medicines including  qty that were decreased during edit  
    $decrementStock  = [];          // Newly added medicines including qty that were increased during edit
    $update_prescriptionQty = [];   // Edited medicine quantities for prescription

    // Field names of Prescription table
    $fields = [ $rxFields->checkupFK, $rxFields->itemId, $rxFields->amount ];

    // To tell if an item is original, It should have the flagReturn and flagRemove key.
    // NEW ITEMS will be inserted
    // DEFAULT ITEMS will be updated or removed, depending on the flag applied
    foreach ($prescriptions as $obj)
    { 
        // Decrypt item id
        $itemKey = $security->Decrypt($obj['itemKey']);
 
        // ORIG ITEMS
        if (array_key_exists("flagReturn", $obj) && array_key_exists("flagRemove", $obj))
        { 
            // Get all items keys that will be removed
            if ($obj['flagRemove'] == 1)
                array_push($removeItems, $itemKey);

            // Get all items that will be returned to inventory
            if ($obj['flagReturn'] == 1)
            { 
                // INCREMENT STOCK | RETURN BACK
                array_push($incrementStock, 
                [
                    'id'     => $itemKey,
                    'amount' => $obj['quantity']
                ]);
            }

            // We will grab a reference to every original item's 
            // ID and Quantity. We will use this later to check
            // If an item's qty was updated.
            $updateItems["item_" . $itemKey] = $obj['quantity'];
 
            // continue with Next iteration and skip adding new items below
            continue;
        }

        // Newly added prescriptions will be stored here as Objects
        $newItems[] = array ( $record_id, $itemKey, $obj['quantity'] );

        // NEW ITEMS will be decremented in inventory
        array_push($decrementStock, [ 'id' => $itemKey, 'amount' => $obj['quantity'] ]);
    }

    // INSERT NEW PRESCRIPTIONS
    if (!empty($newItems))
    { 
        $db->insertRange($rxTable, $fields, $newItems);
    }

    // REMOVE AN ITEM FROM PRESCRIPTION RECORD
    if (!empty($removeItems))
    {
        $sql = "DELETE FROM $rxTable WHERE $fields[0] = $record_id AND $fields[1] IN (" . implode(",", $removeItems) . ");";
        $db->query($sql); 
    }
     
    //==========================================//
    //      TASK 4: UPDATE INVENTORY            //
    //==========================================//

    // TRACK FOR UPDATED VALUES OF PRESCRIPTION ITEM'S QTY
    if (!empty($updateItems))
    {  
        // Load the actual prescription data from database.
        // We can use this to tell if there were newly added medicines
        $actualValues = getActualPrescriptions($fields[0], $fields[1], $fields[2]);

        if (!empty($actualValues))
        {
            foreach($updateItems as $k => $v)
            {
                if (array_key_exists($k, $actualValues))
                { 
                    // Remove prefix from item id
                    $item_id = str_replace("item_", "", $k);
    
                    // DECREASE USED AMOUNT OF NEW ITEMS INTO INVENTORY
                    if ($actualValues[$k] < $v)
                    {  
                        array_push($decrementStock,
                        [
                            'id'     => $item_id,
                            'amount' => ($v - $actualValues[$k])
                        ]);
                    }
                    // INCREMENT STOCK | RETURN BACK TO INVENTORY
                    else if ($actualValues[$k] > $v)
                    {  
                        array_push($incrementStock, 
                        [
                            'id'     => $item_id,
                            'amount' => ($actualValues[$k] - $v)
                        ]); 
                    }
    
                    array_push($update_prescriptionQty,
                    [
                        'checkupFK' => $record_id, 
                        'itemId'    => $item_id, 
                        'amount'    => $v
                    ]);
                }
            }
        }
    }

    // INCREMENT STOCK | RETURN AN ITEM BACK TO INVENTORY
    if (!empty($incrementStock))
        updateStocks($incrementStock, $pdo, $inventoryModel->getFields(), 1);
    
    // DECREMENT STOCK | PULL OUT AN ITEM's STOCK IN INVENTORY
    if (!empty($decrementStock))
        updateStocks($decrementStock, $pdo, $inventoryModel->getFields(), 0);

    // UPDATE THE AMOUNT/QTY USED IN EACH PRESCRIPTION
    if (!empty($update_prescriptionQty))
        updatePrescriptionQty($pdo, $update_prescriptionQty, $rxFields);

    // Exit and return a success message
    onComplete();
}
//catch (\Throwable $ex) {  onError(); }
catch (\Exception $ex) {  onError(); }

function onComplete()
{
    $_SESSION['checkup-records-action-success'] = "A record has been successfully updated.";
    throw_response_code(200, (ENV_SITE_ROOT . Pages::CHECKUP_RECORDS));
    exit;
}

function onError()
{
    IError::Throw(500);
    exit;
}

/**
 * Load the actual prescription data from database
 */
function getActualPrescriptions($checkupIdField, $itemIdField, $amountField)
{
    global $db, $record_id;

    // Load the real/actual values from the prescription database
    $values = $db->select(
        TableNames::prescription_details, 
        [ $itemIdField, $amountField ],         // Fields to select 
        [ $checkupIdField => $record_id ]       // Condition
    );
     
    if (!empty($values))
    {
        $result = array();

        foreach($values as $obj)
        {
            $result["item_" . $obj[$itemIdField]] = $obj[$amountField]; 
        }
 
        ksort($result);

        return $result;
    }
 
    return [];
}
/**
 * Increase or Decrease an item's stock.
 * @param int $mode 0 = Subtract, 1 = Add
 */
function updateStocks($dataset, $pdo, $invenFields, $mode = -1)
{ 
    if (!in_array($mode, [0,1]))
        return;

    $operation = $mode == 1 ? "($invenFields->remaining + ?)" : "($invenFields->remaining - ?)";

    $sql = "UPDATE " . TableNames::inventory .
    " SET $invenFields->remaining = $operation WHERE $invenFields->id = ?";

    $sth = $pdo->prepare($sql);

    foreach ($dataset as $obj)
    {
        $sth->bindValue(1, $obj['amount']);
        $sth->bindValue(2, $obj['id']);
        $sth->execute(); 
    }
} 
/**
 * Update the amount of each medicine used in prescription.
 */
function updatePrescriptionQty($pdo, $dataset, $fields)
{
    if (empty($dataset))
        return;

    $sql = "UPDATE ". TableNames::prescription_details 
    ." SET $fields->amount = ? WHERE $fields->itemId = ? AND $fields->checkupFK = ?";

    $sth = $pdo->prepare($sql);
    
    foreach ($dataset as $obj)
    {
        $sth->bindValue(1, $obj['amount']);
        $sth->bindValue(2, $obj['itemId']);
        $sth->bindValue(3, $obj['checkupFK']);
     
        $sth->execute();
    } 
}