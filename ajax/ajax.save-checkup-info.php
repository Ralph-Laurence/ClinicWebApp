
<?php

@session_start();

require_once("rootcwd.inc.php");

require_once($cwd . "database/configs.php");
require_once($cwd . "database/dbhelper.php");
require_once($cwd . "includes/system.php");
require_once($cwd . "includes/utils.php");

$request = new Requests();

if ($_SERVER['REQUEST_METHOD'] != 'POST' || !$request->isAjax())
{
    http_response_code(404);
    die();
}
 
//=========================================================
//----- KEY NAMES ARE EXACTLY TABLE COLUMN NAMES ----------
//=========================================================

// checkup form data
$jsonData = $_POST['jsonData']; 
$payload = json_decode($jsonData, true);

$db = new DbHelper($pdo);

// generate new form number 
$checkupFormNumber = Helpers::generateFormNumber($pdo);

$fields =
[
    'checkup_date'          => $payload["input_checkupDate"] ?? "",
    'checkup_time'          => $payload["input_checkupTime"] ?? "",
    'form_number'           => $checkupFormNumber, //$payload["input_formNumber"],

    'patient_fname'         => $payload["input_firstName"] ?? "",
    'patient_mname'         => $payload["input_middleName"] ?? "",
    'patient_lname'         => $payload["input_lastName"] ?? "",

    'patient_bday'          => $payload["input_bday"] ?? "",
    'patient_gender'        => $payload["input_gender"] ?? "",
    'patient_age'           => $payload["input_age"] ?? "",

    'patient_address'       => $payload["input_address"] ?? "",
    'patient_contact'       => $payload["input_contact"] ?? "",
    'parent_guardian_name'  => $payload["input_parentsGuardian"] ?? "N/A",
    'patient_type'          => $payload["input_patientType"] ?? "0", 

    'illness_id'            => $payload["input_illness_id"] ?? ""
];

// check for blood pressure inputs
// if valid, push them to fields array
$input_systolic = $payload["input_systolicBp"];
$input_diastolic = $payload["input_diastolicBp"];

if ($input_systolic == "" || $input_diastolic == "")
{
    echo "Blood pressure values are invalid.";
    exit;
}

$patientBp = $input_systolic . "/" . $input_diastolic;
$fields['patient_bp'] = $patientBp;
 
// Check all POST-ed fields if they have null or empty values
// then immediately stop the execution
foreach($fields as $k => $v)
{
    if ($v == "" || $v == null)
    {
        echo "You have one or more fields with empty or invalid values. Please double check your entries before submitting.";
        http_response_code(400);
        die();
    }
} 

// OPTIONAL FIELDS
$fields['remarks'] = $payload["input_remarks"];
$fields['patient_weight'] = $payload["input_weight"];

// prescription data
$prescriptionData = $_POST['prescription'] ?? "";
$hasPrescriptions = !empty($prescriptionData);
 
// If all goes well, then save the record to the database
try 
{ 
    // checkup info
    $db->insert($pdo, TableNames::$checkup, $fields);

    // make it sure that there are prescriptions selected
    // before we store the record into database
    if ($hasPrescriptions) 
    { 
        $prescriptions = json_decode($prescriptionData, true); 

        foreach ($prescriptions as $obj) 
        {
            $itemId = $obj['itemId'];
            $amount = $obj['amount'];

            $rx_obj =
            [
                "item_id" => $itemId,
                "amount" => $amount,
                "unit_measure" => $obj['units'],
                "checkup_number" => $checkupFormNumber
            ];

            // save prescription info
            $db->insert($pdo, TableNames::$prescription, $rx_obj); 

            $itemsTable = TableNames::$items;

            // deduct medicine stock
            $sql = "UPDATE $itemsTable SET remaining = (remaining - ?) WHERE id = ?";
            $sth = $pdo->prepare($sql);
            $sth->bindValue(1, $amount);
            $sth->bindValue(2, $itemId);
            $sth->execute();
        }
    }
 
    $response =
    [
        "statusCode" => ResponseCodes::success(),
        "message" => "Record successfully saved!"
    ];

    echo json_encode($response);
    exit;
} 
catch (\Throwable $th) 
{
    echo "Failed to save the record because of an error. " . $th->getMessage();
    http_response_code(500);
    exit;
}