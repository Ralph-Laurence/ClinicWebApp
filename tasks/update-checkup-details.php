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
require_once($rootCwd . "models/PrescriptionHistory.php");
require_once($rootCwd . "models/Item.php");
require_once($rootCwd . "models/Stock.php");

require_once($rootCwd . "includes/Auth.php");
require_once($rootCwd . "includes/Security.php");
 
use Models\Checkup;
use Models\Item;
use Models\Prescription;
use Models\PrescriptionHistory;
use Models\Stock;

$security = new Security();
$security->requirePermission(Chmod::PK_MEDICAL, Chmod::FLAG_WRITE);
$security->checkAccess(Chmod::PK_MEDICAL, UserAuth::getId());
$security->BlockNonPostRequest();               // Do not run this script when not accessed with POST

$db = new DbHelper($pdo);                       // Db helper wraps CRUD operations as functions
 
$checkupModel = new Checkup();                  // Model will hold values as object
$checkupFields = $checkupModel->getFields();

$rxModel    = new Prescription();
$rxFields   = $rxModel->getFields();
$rxTable    = TableNames::prescription_details; 
$inventory  = TableNames::inventory;

$inventoryModel = new Item($db); 
$invFields      = $inventoryModel->getFields();

$stocks = new Stock($db);
$prescriptionHistory = new PrescriptionHistory($db);

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

    //================================================//
    //         TASK 1: UPDATE CHECKUP DETAILS         //
    //================================================//

    $illness_id = $security->Decrypt($illness_id_raw);
    $doctor_id  = $security->Decrypt($doctor_id_raw);

    $db->update( TableNames::checkup_details, 
    // Values
    [
        $checkupFields->illnessId   => $illness_id,
        $checkupFields->bpSystolic  => $bp_systolic,
        $checkupFields->bpDiastolic => $bp_diastolic,
        $checkupFields->doctorId    => $doctor_id
    ],
    // Conditions
    [ $checkupFields->id => $record_id ]);
 
    //================================================//
    //         TASK 2: UPDATE PRESCRIPTION            //
    //================================================//

    // If no prescription data is available, just exit after update
    if (empty($prescriptions))
        onComplete();
  
    // The update values for prescriptions will be stored here
    $updatePrescriptionDataSource = [];

    // Store newly added prescriptions here
    $newPrescriptionDataSource = [];

    // Store IDs of removed medicines here
    $removedMedicines = [];

    // Store the IDs and amounts of returned medicines
    $returnedMedicineStocks = [];
 
    foreach ($prescriptions as $obj)
    { 
        // Decrypt item id
        $itemId = $security->Decrypt($obj['itemKey']);
 
        // This check is for identifying which prescriptions are newly added or
        // has been there before. Usually, new prescriptions do not have the
        // "flagReturn" and/or "flagRemove" keys.

        // Task: Check if a medicine item will be returned or removed. These
        // values are from the prescriptions table on the checkup form
        if (array_key_exists("flagReturn", $obj) && array_key_exists("flagRemove", $obj))
        {
            // Get all medicine items keys that will be removed from prescription.
            // If Medicine is just removed [flag remove = 1], and [flag return = 0],
            // it will not be returned to stocks.
            if ($obj['flagRemove'] == 1)
                $removedMedicines[] = $itemId;
            //
            // When a medicine was removed [flag remove=1] from the prescriptions,
            // and the user wants to return the medicine, [flag return=1], Get all 
            // medicine ids with quantities that will be returned back to stocks. 
            //
            // This comes from UI, there is an pop-up option for user if he will 
            // return the stock or not.
            if ($obj['flagReturn'] == 1) 
            {
                $returnedMedicineStocks[] = 
                [
                    'itemId'    => $itemId,
                    'quantity'  => $obj['quantity'],
                    'stockId'   => $obj['stockId']
                ];
            }
             
            // Update the stocks of a prescription from the UI prescriptions table
            // Collect all the values for update. 
            $updatePrescriptionDataSource[$itemId] = 
            [
                'itemId'    => $itemId,
                'stockId'   => $obj['stockId'],
                'quantity'  => $obj['quantity']
            ];  

            // Items with flag remove or return are automatically
            // ignored and not considered new item. So we continue
            // with the Next iteration and skip adding 
            // this current item as a New item
            continue;
        }

        // Collect the newly added prescriptions 
        $newPrescriptionDataSource[] = 
        [
            $rxFields->checkupFK  => $record_id,        //  'checkupFK' 
            $rxFields->itemId     => $itemId,           // 'itemId'
            $rxFields->amount     => $obj['quantity'],  // 'quantity'
            $rxFields->stockFK    => $obj['stockId']    // 'stockId'
        ];  
    }

    // Update the quantities used in each prescriptions
    updatePrescriptions($record_id, $updatePrescriptionDataSource);

    // Add the newly added prescriptions
    insertNewPrescriptions($record_id, $newPrescriptionDataSource);

    // Just Remove prescriptions without returning the stocks
    onRemovePrescriptions($record_id, $removedMedicines);

    // Return the stocks of removed medicines
    onReturnStocks($returnedMedicineStocks);

    // Exit and return a success message
    onComplete();
}
//catch (\Throwable $ex) {  onError(); }
catch (\Exception $ex) { echo "{$ex->getMessage()} {$ex->getLine()}"; exit; onError(); }

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

//====================================================================
// ::::::::::::::::::::::::: REVISED FUNCTIONS :::::::::::::::::::::::
//====================================================================

function updatePrescriptions($checkupFK, array $updateDataSource)
{
    if (empty($updateDataSource))
        return;

    global $db, $stocks, $prescriptionHistory, $rxTable, $rxFields, $inventory, $invFields;

    $rxhistoryTable = TableNames::prescription_history;
    $rxHistoryFields = $prescriptionHistory->getFields();

    $stocksTable = TableNames::stock;
    $stockFields = $stocks->getFields();

    // Prepare query to Load the prescription history
    $stmt_get_rxHistory = $db->getInstance()->prepare
    (
        "SELECT $rxHistoryFields->quantityUsed
        FROM    $rxhistoryTable
        WHERE   $rxHistoryFields->checkupFK = ? AND $rxHistoryFields->stockId = ?"
    );

    // Prepare query to load the current quantity of a stock
    $stmt_get_stock_qty = $db->getInstance()->prepare
    (
        "SELECT $stockFields->quantity
        FROM    $stocksTable
        WHERE   $stockFields->item_id = ? AND $stockFields->id = ?"
    );

    // Prepare query to update quantity of a stock
    $stmt_update_stock_qty = $db->getInstance()->prepare
    (
        "UPDATE $stocksTable SET $stockFields->quantity = ? 
        WHERE   $stockFields->item_id = ? AND $stockFields->id = ?"
    );
    
    // Preapre the query to udate the amount in prescriptions history
    $stmt_update_rx_history = $db->getInstance()->prepare
    (
        "UPDATE $rxhistoryTable
            SET $rxHistoryFields->quantityUsed = ? 
        WHERE $rxHistoryFields->checkupFK = ? AND $rxHistoryFields->stockId = ?"
    );

    // Prepare the query for updating the amount of each prescription
    $stmt_update_prescription = $db->getInstance()->prepare
    (
        "UPDATE $rxTable 
            SET $rxFields->amount = ? 
        WHERE $rxFields->itemId = ? AND $rxFields->checkupFK = ?"
    );

    // Prepare the query to update the inventory table's quantities
    // which are the total quantities of each stocks with matching
    // item Id from the stocks table  
    $stmt_update_inventory = $db->getInstance()->prepare
    ( 
        "UPDATE $inventory AS i
        SET i.$invFields->remaining = 
        (
            SELECT SUM(s.$stockFields->quantity)
            FROM $stocksTable AS s
            WHERE s.$stockFields->item_id = i.$invFields->id
        )
        WHERE i.$invFields->id = ?;"
    ); 

    foreach ($updateDataSource as $k => $obj)
    {  
        $itemId     = $obj['itemId'];
        $updateQty  = $obj['quantity'];
        $stockFk    = $obj['stockId'];

        // Load the prescriptions history
        $stmt_get_rxHistory->execute([$checkupFK, $stockFk]);

        // Load the last saved quantity from history
        $lastQty = $stmt_get_rxHistory->fetchColumn();

        // Load original stock
        $stmt_get_stock_qty->execute([$itemId, $stockFk]);

        $stox = $stmt_get_stock_qty->fetchColumn();

        $originalStocks = $stox + $lastQty;

        // Get the new amount
        $newQty = $originalStocks - $updateQty;

        // Update stock qty
        $stmt_update_stock_qty->execute([$newQty, $itemId, $stockFk]);

        // Update prescription history
        $stmt_update_rx_history->execute([$updateQty, $checkupFK, $stockFk]);

        // Update prescription details 
        $stmt_update_prescription->execute([$updateQty, $itemId, $checkupFK]);

        // Update the inventory table
        $stmt_update_inventory->execute([$itemId]);
    }
}

function insertNewPrescriptions($checkupFK, array $insertDataSource)
{ 
    if (empty($insertDataSource))
        return;

    global $db, $stocks, $rxFields, $inventory, $invFields;
 
    $stocksTable = TableNames::stock;
    $stockFields = $stocks->getFields();

    // Query for updating the stocks
    $stmt_update_stocks = $db->getInstance()->prepare
    (
        "UPDATE $stocksTable 
            SET $stockFields->quantity = ($stockFields->quantity - ?)
        WHERE $stockFields->id = ? AND $stockFields->item_id = ?"
    );

    // Prepare the query to update the inventory table's quantities
    // which are the total quantities of each stocks with matching
    // item Id from the stocks table  
    $stmt_update_inventory = $db->getInstance()->prepare
    ( 
        "UPDATE $inventory AS i
        SET i.$invFields->remaining = 
        (
            SELECT SUM(s.$stockFields->quantity)
            FROM $stocksTable AS s
            WHERE s.$stockFields->item_id = i.$invFields->id
        )
        WHERE i.$invFields->id = ?;"
    );

    $history    = new PrescriptionHistory($db);
    $rxHistory  = [];
    $rxValues   = [];

    foreach($insertDataSource as $obj)
    {
        
        $itemId     = $obj[ $rxFields->itemId  ];
        $qty        = $obj[ $rxFields->amount  ];
        $stockId    = $obj[ $rxFields->stockFK ]; 

        $rxValues[] = 
        [
            $checkupFK,
            $itemId,
            $qty,
            $stockId
        ];

        // Update the stocks table
        $stmt_update_stocks->execute([$qty, $stockId, $itemId]);

        // Update the inventory table
        $stmt_update_inventory->execute([$itemId]);

        // Collect prescription history data
        $rxHistory[] = ['stockId' => $stockId, 'checkupId' => $checkupFK, 'used' => $qty]; 
    }

    // Save prescriptions history
    $history->push($rxHistory);

    // Save the new prescriptions into database
    $db->insertRange(TableNames::prescription_details,
        [   
            $rxFields->checkupFK,
            $rxFields->itemId,
            $rxFields->amount,
            $rxFields->stockFK
        ],
        $rxValues
    );
}

function onRemovePrescriptions($checkupFK, array $itemIds)
{
    if (empty($itemIds))
        return;

    global $db, $rxTable, $rxFields;

    $bindings = $db->generateBinders($itemIds);
 
    $stmt_delete_rx = $db->getInstance()->prepare
    (
        "DELETE FROM $rxTable WHERE $rxFields->checkupFK = ? AND $rxFields->itemId IN ($bindings)"
    ); 
    
    $stmt_delete_rx->execute(array_merge([ $checkupFK ], $itemIds));
}

function onReturnStocks(array $dataSource)
{ 
    global $db, $stocks, $inventory, $invFields;

    $stocksTable = TableNames::stock;
    $stockFields = $stocks->getFields();

    // Prepare the query to return the stock
    $stmt_restore_stock = $db->getInstance()->prepare
    (
        "UPDATE $stocksTable 
            SET $stockFields->quantity = ($stockFields->quantity + ?)
        WHERE $stockFields->item_id = ? AND $stockFields->id = ?"
    );

    // Prepare the query to update the inventory table's quantities
    // which are the total quantities of each stocks with matching
    // item Id from the stocks table  
    $stmt_update_inventory = $db->getInstance()->prepare
    ( 
        "UPDATE $inventory AS i
        SET i.$invFields->remaining = 
        (
            SELECT SUM(s.$stockFields->quantity)
            FROM $stocksTable AS s
            WHERE s.$stockFields->item_id = i.$invFields->id
        )
        WHERE i.$invFields->id = ?;"
    );

    foreach ($dataSource as $obj)
    {
        $itemId     = $obj['itemId'];
        $quantity   = $obj['quantity'];
        $stockId    = $obj['stockId'];
        
        // Return the quantity back to stocks table
        $stmt_restore_stock->execute([ $quantity, $itemId, $stockId ]);

        // Update the inventory
        $stmt_update_inventory->execute([ $itemId ]);
    }
}