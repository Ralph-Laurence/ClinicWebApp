<?php
require_once("rootcwd.php");
require_once($rootCwd . "controllers/EditCheckupController.php");
?>

<body>

    <!-- ROOT CONTAINER -->
    <div class="container-fluid p-0 d-flex flex-column main-wrapper w-100 h-100 bg-document overflow-hidden">

        <!-- HEADINGS BANNER -->
        <?php require_once("layouts/headings-banner.php") ?>

        <!-- MAIN CONTENT WRAPPER -->
        <div class="main-content-wrapper flex-column flex-grow-1 h-100 overflow-hidden">

            <!-- MAIN CONTENT -->
            <div class="main-content d-flex h-100 w-100 overflow-hidden">

                <!-- SIDE NAVIGATION [LEFT-HALF] -->
                <?php
                // mark the active side nav link
                setActiveLink(Navigation::NavIndex_CheckupRecords);

                // Then include the side navigation
                require_once("layouts/side-nav.php");
                ?>

                <!-- WORKSPACE WRAPPER [RIGHT-HALF] -->
                <div class="workspace-wrapper d-flex flex-column flex-fill overflow-hidden">

                    <!--WELCOME BANNER-->
                    <?php include_once("layouts/welcome-banner.php"); ?>

                    <!-- WORKSPACE CONTENT -->
                    <div class="workspace-content d-flex flex-column p-2 h-100 overflow-hidden">

                        <!-- MAIN WORKAREA -->
                        <div id="main-workarea" class="flex-grow-1 d-flex flex-column h-100 bg-white shadow-2-strong overflow-hidden px-4 py-2">

                            <? // BREADCRUMB 
                            ?>
                            <div class="breadcrumbs w-100 d-flex flex-row align-items-center justify-content-start">
                                <img src="assets/images/icons/sidenav-view.png" width="20" height="20">
                                <div class="fs-6 fw-bold ms-2">View</div>
                                <div class="breadcrumb-arrow fsz-12 fas fa-play mx-3"></div>
                                <img src="assets/images/icons/sidenav-medical.png" width="20" height="20">
                                <div class="ms-2 fw-bold fs-6">Medical Records</div>
                                <div class="breadcrumb-arrow fsz-12 fas fa-play mx-3"></div>
                                <img src="assets/images/icons/sidenav-checkup.png" width="20" height="20" alt="icon">
                                <div class="ms-2 fw-bold fs-6">Checkup</div>
                                <div class="breadcrumb-arrow fsz-12 fas fa-play mx-3"></div>
                                <img src="assets/images/icons/bcrumb-edit.png" width="20" height="20" alt="icon">
                                <div class="ms-2 fw-bold fs-6">Edit</div>
                            </div>

                            <hr class="hr divider" />

                            <? // PATIENT INFORMATION SECTION 
                            ?>
                            <div data-simplebar class="flex-grow-1 checkup-form-fields px-3 h-100 overflow-y-auto no-native-scroll">

                                <form action="<?= Tasks::UPDATE_CHECKUP_DETAILS ?>" class="overflow-hidden" method="POST" id="checkup-form">

                                    <? // AUTOGENERATED CHECKUP FORM NUMBER 
                                    ?>
                                    <div class="row">
                                        <div class="col-3">
                                            <h6>Transaction Number</h6>
                                            <h6 class="fw-bold font-indigo"><?= $checkupData['checkupNumber'] ?></h6>
                                        </div>
                                        <div class="col-3"></div>
                                        <div class="col text-start">
                                            <h6>Checkup Date &sol; Time</h6>
                                            <h6 class="fw-bold font-teal"><?= $checkupData['dateCreated'] ?></h6>
                                        </div>
                                    </div>

                                    <hr class="hr divider my-2" />

                                    <? // FORM COLUMN SUBHEADINGS 
                                    ?>
                                    <div class="row">
                                        <div class="col-3">
                                            <div class="fs-6 fw-bold font-base mb-2">Patient Information</div>
                                        </div>
                                        <div class="col-3"></div>
                                        <div class="col-3">
                                            <div class="fs-6 fw-bold font-base mb-2">Medical Information</div>
                                        </div>
                                    </div>

                                    <? // ERROR MESSAGE WRAPPER 
                                    ?>
                                    <div class="row">
                                        <div class="col">
                                            <div class="bg-red-light p-2 rounded-2 mb-3 display-none error-message">
                                                <i class="fas fa-exclamation-triangle font-red-dark me-1"></i>
                                                <small class="font-red-dark error-label">Error Message</small>
                                            </div>
                                        </div>
                                    </div>

                                    <?php // HIDDEN VALUES FOR SENSITIVE DATA 
                                    ?>
                                    <input type="text" name="record-key" class="d-none record-key" value="<?= $recordKey ?>">
                                    <input type="text" name="illness-id" class="d-none illness-id" value="<?= $illnessData['id'] ?>">
                                    <input type="text" name="doctor-key" class="d-none doctor-key" value="<?= getPhysician('id') ?>">
                                    <textarea name="prescriptions" class="prescriptions d-none"></textarea>

                                    <div class="row mb-3">
                                        <div class="col-3">
                                            <div class="form-outline">
                                                <i class="fas fa-lock trailing"></i>
                                                <input type="text" class="form-control form-icon-trailing bg-white text-primary patient-idnum" name="input-idnum" value="<?= $patientData['idNumber'] ?>" readonly required data-mdb-toggle="tooltip" data-mdb-placement="top" title="You can't make changes to this field." />
                                                <label for="input-idnum" class="form-label">ID Number</label>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <button type="button" class="btn btn-secondary fw-bold px-2 me-2 disabled">
                                                <i class="fas fa-search"></i>
                                                <span class="ms-1">Find</span>
                                            </button>
                                            <button type="button" class="btn btn-secondary fw-bold px-2 disabled">
                                                <i class="fas fa-plus"></i>
                                                <span class="ms-1">New</span>
                                            </button>
                                        </div>
                                        <div class="col-4">
                                            <div class="w-100 d-flex">
                                                <div class="form-outline ms-0 me-2 flex-fill">
                                                    <input type="text" name="input-illness" class="form-control input-illness text-primary bg-white" value="<?= $illnessData['name'] ?>" readonly>
                                                    <label class="form-label" for="input-illness">Illness / Disease *</label>
                                                </div>
                                                <button type="button" class="btn btn-warning bg-amber px-3 me-0 ms-auto" data-mdb-toggle="modal" data-mdb-target="#findIllnessModal">
                                                    <i class="fas fa-pen text-dark"></i>
                                                </button>
                                            </div>

                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-3">
                                            <div class="form-outline">
                                                <input type="text" class="form-control bg-white text-primary patient-name" value="<?= $patientData['fullName'] ?>" readonly data-mdb-toggle="tooltip" data-mdb-placement="bottom" title='Click "Find" to open the list of registered patients' />
                                                <label class="form-label" for="patient-name">Patient Name</label>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="d-flex align-items-center">
                                                <?php $patientData['patientType'] ?>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="fs-6 fw-bold text-muted">Blood Pressure</div>
                                            <small>(Required if illness is Hypertension / Anemia)</small>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-3">
                                            <div class="form-outline mb-3">
                                                <input type="text" class="form-control bg-white text-primary physician" value="<?= getPhysician('name') ?>" readonly />
                                                <label class="form-label" for="physician">Physician</label>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <button type="button" class="btn btn-secondary fw-bold px-2 me-2" data-mdb-toggle="modal" data-mdb-target="#findDoctorModal">
                                                <i class="fas fa-caret-down"></i>
                                                <span class="ms-1">Select</span>
                                            </button>
                                        </div>
                                        <div class="col-4">
                                            <div class="d-flex align-items-center">
                                                <div class="form-outline ms-0 me-2">
                                                    <input type="text" name="bp-systolic" class="form-control input-systolic numeric" value="<?= getBp(0) ?>">
                                                    <label class="form-label" for="input-systolic">Systolic</label>
                                                </div>
                                                <div class="fraction mx-auto fs-6">&sol;</div>
                                                <div class="form-outline ms-2 me-0">
                                                    <input type="text" name="bp-diastolic" class="form-control input-diastolic numeric" value="<?= getBp(1) ?>">
                                                    <label class="form-label" for="input-diastolic">Diastolic</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <hr class="hr divider" />

                                    <? // PRESCRIPTION HEADINGS 
                                    ?>
                                    <div class="row mb-2">
                                        <div class="col-6 d-flex align-items-center">
                                            <div class="fs-6 fw-bold font-base mb-2">Medical Prescription</div>
                                        </div>
                                        <div class="col text-end">
                                            <button type="button" class="btn btn-primary bg-teal py-1 px-2 me-2" data-mdb-toggle="modal" data-mdb-target="#selectMedicineModal">
                                                <i class="fas fa-plus"></i>
                                                <span class="ms-1">Add Medicine</span>
                                            </button>
                                            <!-- <button type="button" class="btn btn-secondary btn-clear-prescription fw-bold py-1 px-2 me-2">
                                                <i class="fas fa-undo"></i>
                                                <span class="ms-1">Clear</span>
                                            </button> -->
                                        </div>
                                    </div>
                                    <? // PRESCRIPTION TABLE 
                                    ?>
                                    <div class="row">
                                        <div class="col">
                                            <div class="table-wrapper" style="overflow-y: auto; max-height: 310px;">
                                                <table class="table table-sm table-borderless table-striped table-hover prescription-table">
                                                    <thead class="style-secondary position-sticky top-0 z-10">
                                                        <tr>
                                                            <th class="fw-bold" scope="col"style="max-width: 200px; width: 200px;">Medicine</th>
                                                            <th class="fw-bold text-center" scope="col" style="max-width: 200px; width: 200px;">Stock</th>
                                                            <th class="fw-bold text-center" scope="col" style="max-width: 200px; width: 200px;">Available</th>
                                                            <th class="fw-bold text-center" scope="col" style="max-width: 200px; width: 200px;">Quantity</th>
                                                            <th class="fw-bold text-center" scope="col" style="max-width: 140px; width: 140px;">Action</th>
                                                            <th class="d-none" scope="col">ItemKey</th>
                                                            <th class="d-none" scope="col">Stock Data</th>
                                                            <th class="d-none" scope="col"></th> <?php //  <-- This will hold the original amount ?>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="prescription-body">
                                                        <?php bindPrescriptions() ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <hr class="hr divider" />

                                    <div class="control-buttons d-flex align-items-center flex-row justify-content-end gap-2">
                                        <button type="button" class="btn btn-secondary fw-bold btn-cancel">Cancel</button>
                                        <button type="button" class="btn btn-primary bg-base btn-update">Update</button>
                                    </div>
                                </form>
                            </div>

                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>


    <?php // SERVER MESSAGES SAVED ONTO SESSION 
    ?>
    <form class="d-none">
        <input type="text" class="checkup-success-message" value="<?= getSuccessMessage() ?>">
        <input type="text" class="checkup-error" value="<?= getErrorMessage() ?>">
        <input type="text" class="go-back" value="<?= Pages::CHECKUP_RECORDS ?>">
    </form>

    <!--SNACKBAR AND MODAL-->
    <?php

    require_once($rootCwd . "components/confirm-dialog/confirm-dialog.php");
    require_once($rootCwd . "components/alert-dialog/alert-dialog.php");
    require_once($rootCwd . "components/snackbar/snackbar.php");
    require_once($rootCwd . "components/toast/toast.php");

    require_once($rootCwd . "includes/embed.medicine-picker.php");
    require_once($rootCwd . "includes/embed.illness-picker.php");
    require_once($rootCwd . "includes/embed.physician-picker.php");
    ?>


    <!--SCRIPTS-->
    <script src="assets/lib/jquery/jquery-3.6.1.min.js"></script>
    <script src="assets/lib/jquery-ui-1.13.2.custom/jquery-ui.min.js"></script>
    <script src="assets/lib/datatables/datatables.min.js"></script>
    <script src="assets/lib/mdb5/js/mdb.min.js"></script>
    <script src="assets/lib/mdb5/js/mdb.form-validation.js"></script>
    <script src="assets/lib/momentjs/moment-with-locales.js"></script>
    <script src="assets/lib/jquery.nicescroll/jquery.nicescroll.min.js"></script>
    <script src="assets/lib/simplebar/simplebar.min.js"></script>

    <script src="assets/js/nicescroll.js"></script>
    <script src="assets/js/system.js"></script>
    <script src="assets/js/base-ui.js"></script>
    <script src="assets/js/page.action-edit-checkup.js"></script>

    <script src="components/confirm-dialog/confirm-dialog.js"></script>
    <script src="components/alert-dialog/alert-dialog.js"></script>
    <script src="components/snackbar/snackbar.js"></script>
    <script src="components/toast/toast.js"></script>
</body>

</html>