
<?php

@session_start();

require_once("../database/configs.php");
require_once("../database/dbhelper.php"); 

$redirect = constant('base_url') . "checkup-form.php";  

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST["submit"]))
    processFormData();

$_SESSION['checkupFormMsg'] = ""; 

 
function processFormData()
{
    global $redirect;
  
    $input_systolic = $_POST['input-systolic'];
    $input_diastolic = $_POST['input-diastolic'];
    $patientBp = $input_systolic ."/". $input_diastolic;

    if (empty($input_systolic) || empty($input_diastolic)) 
        return;

    $inputs =
    [
        'checkup_date'          => $_POST['input-checkup-date'],
        'checkup_time'          => $_POST['input-checkup-time'],
        'form_number'           => $_POST['input-form-number'],

        'patient_fname'         => $_POST["input-fname"],
        'patient_mname'         => $_POST['input-mname'],
        'patient_lname'         => $_POST['input-lname'],

        'patient_bday'          => $_POST['input-bday'],
        'patient_gender'        => $_POST['input-gender'],
        'patient_age'           => $_POST['input-age'],

        'patient_address'       => $_POST['input-address'],
        'patient_contact'       => $_POST['input-contact'],
        'patient_father_name'   => $_POST['input-fathers-name'] ?? "N/A",
        'patient_mother_name'   => $_POST['input-mothers-name'] ?? "N/A",
        'patient_bp'            => $patientBp,
        'patient_weight'        => $_POST['input-weight'],
        //'illness_id'
    ];

    if (isset($inputs)) 
    {
        try 
        {
            $pdo = constant('pdo');
            $db = new DbHelper($pdo);

            $db->insert($pdo, "checkup", $inputs);

            unset($_POST);

            $_SESSION['checkupFormMsg'] = "Checkup information successfully saved.";
            $_SESSION['checkup-form-submmited'] = true;
            header("Location: $redirect", true, 303);
            exit;
        } 
        catch (Exception $ex) 
        {
            $_SESSION['checkupFormMsg'] = "The transaction cannot be completed because an error has occurred."; 
        } 
    }
}
