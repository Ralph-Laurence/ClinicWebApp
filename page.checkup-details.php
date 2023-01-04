<?php
@session_start();

require_once("rootcwd.php");
   
require_once($rootCwd . "layout-header.php");
require_once($rootCwd . "includes/urls.php");

// we must be logged in to view this page
if (!isset($_SESSION['isLoggedIn']) || $_SESSION['isLoggedIn'] !== true)
{
    header("Location: " . Navigation::$URL_LOGIN);
    exit;
}

// this script file / module retrieves the checkup details and 
// prescription information matching the checkup form number
// supplied after clicking the "Details" button
require_once($rootCwd . "includes/inc.get-checkup-details.php");

// We expect that the record / dataset retrieved has rows in it.
// If empty, this page wont execute ... 

// Cache the data and other things to
// display into the UI
$patientName =  $checkupDataset['patient_fname'] ." ". 
                $checkupDataset['patient_mname'] ." ". 
                $checkupDataset['patient_lname'];

$patientType = $checkupDataset['patientType'];

$birthday = date("d M Y", strtotime($checkupDataset['patient_bday']));
$gender = $checkupDataset['patient_gender'];
$age = $checkupDataset['patient_age'];
$contact = $checkupDataset['patient_contact'];
$address = $checkupDataset['patient_address'];
$parent = $checkupDataset['parent_guardian_name'];
$checkupDate = date("d M Y", strtotime($checkupDataset['checkup_date']));
$checkupTime = date("h:i A", strtotime($checkupDataset['checkup_time']));
$patientWeight = $checkupDataset['patient_weight'];
$patientBp = $checkupDataset['patient_bp'];
$illness = $checkupDataset['illness'];
$remarks = $checkupDataset['remarks'];
?>

<body>

    <!-- BEGIN CONTAINER -->
    <div class="container-fluid h-100 bg-document p-0">

        <!-- TITLE BANNER -->
        <?php include_once("layouts/banner.php") ?>
        <!-- TITLE BANNER -->

        <!-- MAIN CONTENT -->
        <main class="main-content-wrapper d-flex h-100 pt-5">

            <section class="d-flex flex-grow-1 mt-2 overflow-hidden">

                <!-- NAVIGATION -->
                <?php 
                // mark the active side nav link
                setActiveLink(Navigation::$NavIndex_Patients);

                require_once("layouts/navigation.php");
                ?>

                <!--WORKAREA-->
                <section class="workarea w-100 pb-4">

                    <!--WELCOME BANNER-->
                    <?php include_once("layouts/welcome-banner.php"); ?>

                    <!--THE WORKSHEET WRAPPER-->
                    <div class="worksheet-wrapper p-2 w-100 h-100 overflow-hidden position-relative">

                        <div class="worksheet d-flex flex-column bg-white shadow-2-strong w-100 h-100 p-4" style="overflow-y: hidden;">

                            <!-- BREADCRUMB-->
                            <div class="breadcrumb w-100 d-flex flex-row align-items-center justify-content-start">
                                <h6 class="fas fa-folder-open font-accent"></h6>
                                <h6 class="ms-2 fw-bold">View</h6>
                                <h6 class="breadcrumb-arrow fas fa-play mx-3"></h6>
                                <h6 class="fas fa-notes-medical me-2 font-red"></h6>
                                <h6 class="ms-2 fw-bold">Patient Records</h6>
                                <h6 class="breadcrumb-arrow fas fa-play mx-3"></h6>
                                <h6 class="fas fa-info-circle text-primary"></h6>
                                <h6 class="ms-2 fw-bold">Checkup Details</h6>
                                <h6 class="breadcrumb-arrow fas fa-play mx-3"></h6>
                                <h6 class="fas fa-hashtag font-teal"></h6>
                                <h6 class="ms-2 fw-bold lbl-checkup-no"><?= $formNumber ?></h6>
                            </div>

                            <!--Checkup Certificate Wrapper-->
                            <div class="checkup-certificate-wrapper flex-grow-1 pt-2 pb-4 scrollable">
                                
                                <!-- Checkup Certificate --> 
                                <div class="checkup-certificate bg-white mx-auto pt-2 pb-5 px-5 border border-1 border-secondary shadow" style="max-width: 800px;">

                                    <!-- HEADINGS -->
                                    <div class="row cert-headings border-bottom border-secondary pb-4">
                                        <div class="col font-base">
                                            <div class="logo d-flex align-items-center">
                                                <img src="assets/images/logo-xs.png" alt="logo" width="45" height="45">
                                                <div class="logo-brand ms-2">
                                                    <div class="fon-fam-tertiary mt-4">Pangasinan State University</div>
                                                    <div class="fsz-10 mb-2" style="letter-spacing: 0.1em;">Dr. Marciano Cantor Jr. Infirmary</div>
                                                    <div class="divider-separator border-bottom"></div>
                                                    <div class="fsz-12 text-center text-uppercase border-bottom">LINGAYEN CAMPUS</div>
                                                </div>
                                            </div>
                                            <div class="fsz-10 d-flex align-items-center pt-1" style="letter-spacing: 0.1em;">
                                                <span class="">lingayencampus@psu.edu.ph</span>
                                                <span class="mx-2 border-end border-secondary" style="height: 12px;"></span>
                                                <span>0926-275-8092</span>
                                            </div>
                                        </div>
                                        <div class="col pt-4 text-end font-base">
                                            <h6 class="fon-fam-tertiary">Checkup Report</h6>
                                            <small class="d-block"><?php echo date("F d, Y") ?></small>
                                            <small class="font-hilight fon-fam-tertiary">#<?= $formNumber ?></small>
                                        </div>
                                    </div>

                                    <!-- MAIN DETAILS --> 
                                    <div class="row cert-contents pt-3">
                                        <div class="col">
                                            
                                            <h6 class="fon-fam-tertiary text-primary mb-3">Patient Information</h6>

                                            <div class="row">
                                                <div class="col"><small>Name:</small></div>
                                                <div class="col"><h6 class="fw-bold"><?= $patientName ?></h6></div>
                                            </div>

                                            <div class="row">
                                                <div class="col"><small>Classification:</small></div>
                                                <div class="col"><h6><?= $patientType ?></h6></div>
                                            </div>

                                            <div class="row">
                                                <div class="col"><small>Birth Date:</small></div>
                                                <div class="col"><h6><?= $birthday ?></h6></div>
                                            </div>

                                            <div class="row">
                                                <div class="col"><small>Address:</small></div>
                                                <div class="col"><h6><?= $address ?></h6></div>
                                            </div>

                                            <div class="row">
                                                <div class="col"><small>Contact #:</small></div>
                                                <div class="col"><h6><?= $contact ?></h6></div>
                                            </div>

                                            <div class="row">
                                                <div class="col"><small>Gender:</small></div>
                                                <div class="col"><h6><?= $gender ?></h6></div>
                                            </div>

                                            <div class="row">
                                                <div class="col"><small>Age:</small></div>
                                                <div class="col"><h6><?= $age ?></h6></div>
                                            </div>

                                            <div class="row">
                                                <div class="col"><small>Parent / Guardian:</small></div>
                                                <div class="col"><h6><?= $parent ?></h6></div>
                                            </div>
                                             
                                        </div>
                                        <div class="col">
                                            <h6 class="fon-fam-tertiary text-primary mb-3">Medical Information</h6>

                                            <div class="row">
                                                <div class="col"><small>Checkup Date:</small></div>
                                                <div class="col"><h6><?= $checkupDate ?></h6></div>
                                            </div>

                                            <div class="row">
                                                <div class="col"><small>Checkup Time:</small></div>
                                                <div class="col"><h6><?= $checkupTime ?></h6></div>
                                            </div>

                                            <div class="row">
                                                <div class="col"><small>Patient Weight (Kg):</small></div>
                                                <div class="col">
                                                    <h6>
                                                        <?php echo empty($patientWeight) ? "N/A" : $patientWeight;  ?>
                                                    </h6>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col"><small>Blood Pressure:</small></div>
                                                <div class="col"><h6 class="fon-fam-tertiary"><?= $patientBp ?></h6></div>
                                            </div>

                                            <div class="row">
                                                <div class="col"><small>Illness:</small></div>
                                                <div class="col"><h6 class="font-red fon-fam-tertiary"><?= $illness ?></h6></div>
                                            </div>

                                            <div class="row">
                                                <div class="col"><small>Remarks:</small></div>
                                                <div class="col"><small><?= $remarks ?></small></div>
                                            </div>

                                        </div>
                                    </div>

                                    <!-- PRESCRIPTIONS --> 
                                    <h6 class="fon-fam-tertiary text-primary my-3">Medical Prescription</h6>

                                    <div class="row fw-bold">
                                        <div class="col-3"><small>Medicine</small></div> 
                                        <div class="col-3"><small>Amount</small></div>
                                    </div>
                                    

                                    <?php 
                                    // only list down the prescriptions if available..
                                    if (!empty($prescriptionDataset))
                                    {
                                        foreach($prescriptionDataset as $p)
                                        {
                                            $medicine = $p['item_name'];
                                            $amount = $p['amount'];
                                            $units = $p['measurement'];

                                            echo "<div class=\"row\">
                                                <div class=\"col-3\"><small>$medicine</small></div> 
                                                <div class=\"col-3\"><small>$amount $units(s)</small></div>
                                            </div>";
                                        }
                                    }
                                    ?>

                                    <!-- FOOTER -->
                                    <div class="divider my-4 border-secondary border-bottom"></div>
                                    <div class="warning-text text-primary text-center fst-italic">
                                        <small>* This report has been generated electronically. Information contained in this document
                                        is CONFIDENTIAL *</small>
                                    </div>
                                </div> 

                                <!-- CONTROL BUTTONS --> 
                                <div class="control-buttons mx-auto text-end pt-4" style="max-width: 800px;">
                                    <button type="button" class="btn btn-primary bg-base" <?= echoOnclick(Navigation::$URL_PATIENT_RECORDS) ?> >OK</button>
                                </div>
                            </div>
                            
                        </div>

                    </div>

                </section>

            </section>
        </main>
        <!-- MAIN CONTENT -->

    </div>
    <!-- END CONTAINER -->

    <?php

    // modal window for showing the item details
    require_once("layouts/item-info-dialog.php");

    require_once("components/alert-dialog/alert-dialog.php");
    require_once("components/snackbar/snackbar.php");
    require_once("components/confirm-dialog/confirm-dialog.php");
    ?>

    <!--SCRIPTS-->
    <script src="assets/lib/jquery/jquery-3.6.1.min.js"></script>
    <script src="assets/lib/mdb5/js/mdb.min.js"></script>
    <script src="assets/lib/momentjs/moment-with-locales.js"></script>
    <script src="assets/lib/jquery.nicescroll/jquery.nicescroll.min.js"></script>

    <script src="assets/js/nicescroll.js"></script>

    <script src="assets/js/base-ui.js"></script>
    <script src="assets/js/system.js"></script>

    <script src="components/alert-dialog/alert-dialog.js"></script>
    <script src="components/confirm-dialog/confirm-dialog.js"></script>
    <script src="components/snackbar/snackbar.js"></script>

</body>

</html>