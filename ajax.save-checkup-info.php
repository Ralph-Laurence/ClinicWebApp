
<?php

@session_start();

require_once("database/configs.php");
require_once("database/dbhelper.php");
require_once("includes/system.php");

$request = new Requests();

if ($_SERVER['REQUEST_METHOD'] != 'POST' || !$request->isAjax())
{
    http_response_code(404);
    exit;
}

//=========================================================
//----- KEY NAMES ARE EXACTLY TABLE COLUMN NAMES ----------
//=========================================================

$jsonData = $_POST['jsonData']; 

$payload = json_decode($jsonData, true);

$fields =
[
    'checkup_date'          => $payload["input_checkupDate"] ?? "",
    'checkup_time'          => $payload["input_checkupTime"] ?? "",
    'form_number'           => $payload["input_formNumber"],

    'patient_fname'         => $payload["input_firstName"] ?? "",
    'patient_mname'         => $payload["input_middleName"] ?? "",
    'patient_lname'         => $payload["input_lastName"] ?? "",

    'patient_bday'          => $payload["input_bday"] ?? "",
    'patient_gender'        => $payload["input_gender"] ?? "",
    'patient_age'           => $payload["input_age"] ?? "",

    'patient_address'       => $payload["input_address"] ?? "",
    'patient_contact'       => $payload["input_contact"] ?? "",
    'patient_father_name'   => $payload["input_fathersName"] ?? "N/A",
    'patient_mother_name'   => $payload["input_mothersName"] ?? "N/A",
    //'patient_bp'            => $patientBp,
    'patient_weight'        => $payload["input_weight"] ?? "",
    'illness_id'            => $payload["input_illness_id"] ?? ""
];

// check for blood pressure inputs
// if valid, push them to fields array
$input_systolic = $payload["input_systolicBp"];
$input_diastolic = $payload["input_diastolicBp"];
// $input_systolic = $_POST['systolicBp'];
// $input_diastolic = $_POST['diastolicBp'];

if (empty($input_systolic) || empty($input_diastolic))
{
    Response::write(ResponseCodes::warning(), 
    "Blood pressure values are invalid.");
    exit;
}

$patientBp = $input_systolic . "/" . $input_diastolic;
$fields['patient_bp'] = $patientBp;

// Check all POST-ed fields if they have null or empty values
// then immediately stop the execution
foreach(array_values($fields) as $v)
{
    if (empty($v))
    {
        echo "You have one or more fields with empty or invalid values. Please double check your entrues before submitting.";
        http_response_code(400);
        exit;
    }
}

// If all goes well, then save the record to the database
try 
{
    $pdo = constant('pdo');
    $db = new DbHelper($pdo);
    
    $db->insert($pdo, TableNames::$checkup, $fields);

    Response::write(ResponseCodes::success(), "Record successfully saved!");
    exit;
} 
catch (\Throwable $th) 
{
    echo "Failed to save the record because of an error.";
    http_response_code(500);
    exit;
}