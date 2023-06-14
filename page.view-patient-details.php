<?php
require_once("rootcwd.php");
require_once($rootCwd . "controllers/PatientDetailsController.php");
?>

<body>

    <?php $masterPage->beginWorkarea(Navigation::NavIndex_Patients); //BEGIN WORKAREA LAYOUT 
    ?>

    <!-- MAIN WORKAREA -->
    <div class="main-workarea flex-grow-1 d-flex flex-column h-100 bg-white shadow-2-strong overflow-hidden pt-2 px-4 pb-3">

        <!-- BREADCRUMB-->
        <div class="breadcrumbs w-100 d-flex flex-row align-items-center justify-content-start">
            <img src="assets/images/icons/sidenav-view.png" width="20" height="20">
            <div class="fs-6 fw-bold ms-2">View</div>
            <div class="breadcrumb-arrow fsz-12 fas fa-play mx-3"></div>
            <img src="assets/images/icons/sidenav-medical.png" width="20" height="20">
            <div class="ms-2 fw-bold fs-6">Medical Records</div>
            <div class="breadcrumb-arrow fsz-12 fas fa-play mx-3"></div>
            <img src="assets/images/icons/sidenav-patients.png" width="20" height="20" alt="icon">
            <div class="ms-2 fw-bold fs-6">Patients</div>
            <div class="breadcrumb-arrow fsz-12 fas fa-play mx-3"></div>
            <img src="assets/images/icons/bcrumb-details.png" width="20" height="20" alt="icon">
            <div class="ms-2 fw-bold fs-6">Details</div>
        </div>

        <!--DIVIDER-->
        <hr class="hr divider my-2">

        <div class="container-fluid h-100 p-0 overflow-hidden mb-2">

            <div class="row px-3 h-100 patient-details-cover <?= setBackground() ?>">
                <!-- PATIENT PROFILE -->
                <div class="col-5 h-100 overflow-hidden p-4">
                    <div class="bg-white d-flex flex-column border rounded-3 shadow-3-strong h-100 overflow-hidden pb-4 pt-1">
                        <div class="flex-column mb-2 mx-2">
                            <a class="btn btn-link px-2" href="<?= $goBackLink ?>" role="button">
                                <i class="fas fa-arrow-left me-1"></i> Back
                            </a>
                        </div>
                        <div data-simplebar class="h-100 flex-grow-1 overflow-y-auto no-native-scroll px-4">

                            <div class="text-center mb-2">
                                <div class="profile-pic-wrapper d-flex align-items-center justify-content-center mb-3">
                                    <div class="profile-pic p-2 border border-4 rounded-circle">
                                        <img src="assets/images/icons/patient-details-profile.png">
                                    </div>
                                </div>
                                <div class="fs-5 fw-bold font-base-light"><?= $model->getFullname() ?></div>
                                <div class="text-primary fon-fam-special"><?= $model->idNumber ?></div>
                                <div class="text-muted fon-fam-special"><?= $model->describePatient() ?></div>
                            </div>
                            <div class="text-secondary fst-italic d-flex align-items-center">
                                <span class="me-auto ms-0">Personal Details</span>
                                <span class="fsz-12 ms-auto me-0">Scroll down to see more <i class="fas fa-arrow-down ms-1"></i></span>
                            </div>
                            <hr class="hr my-2">
                            <!-- Birthday -->
                            <div class="row">
                                <div class="col-4">
                                    <div class="d-flex align-items-center">
                                        <img src="assets/images/icons/patient-details-bday.png" width="20" height="20">
                                        <span class="ms-2 text-muted">Birthday</span>
                                    </div>
                                </div>
                                <div class="col text-break">
                                    <div class="me-auto"><?= Dates::toString($model->birthDay, "F d, Y") ?></div>
                                </div>
                            </div>
                            <hr class="hr my-2">
                            <!-- Age -->
                            <div class="row my-1">
                                <div class="col-4">
                                    <div class="d-flex align-items-center">
                                        <img src="assets/images/icons/patient-details-age.png" width="20" height="20">
                                        <span class="ms-2 text-muted">Age</span>
                                    </div>
                                </div>
                                <div class="col text-break">
                                    <div class="me-auto"><?= $model->age ?> Years old</div>
                                </div>
                            </div>
                            <hr class="hr my-2">
                            <!-- Gender -->
                            <div class="row my-1">
                                <div class="col-4">
                                    <div class="d-flex align-items-center">
                                        <img src="assets/images/icons/patient-details-gender.png" width="20" height="20">
                                        <span class="ms-2 text-muted">Gender</span>
                                    </div>
                                </div>
                                <div class="col text-break">
                                    <div class="me-auto"><?= GenderTypes::toDescription($model->gender) ?></div>
                                </div>
                            </div>
                            <hr class="hr my-2">
                            <!-- Weight -->
                            <div class="row my-1">
                                <div class="col-4">
                                    <div class="d-flex align-items-center">
                                        <img src="assets/images/icons/patient-details-weight.png" width="20" height="20">
                                        <span class="ms-2 text-muted">Weight</span>
                                    </div>
                                </div>
                                <div class="col text-break">
                                    <div class="me-auto"><?= $model->weight ?> Kilograms</div>
                                </div>
                            </div>
                            <hr class="hr my-2">
                            <!-- Height -->
                            <div class="row my-1">
                                <div class="col-4">
                                    <div class="d-flex align-items-center">
                                        <img src="assets/images/icons/patient-details-height.png" width="20" height="20">
                                        <span class="ms-2 text-muted">Height</span>
                                    </div>
                                </div>
                                <div class="col text-break">
                                    <div class="me-auto"><?= calculateHeight($model->height) ?></div>
                                </div>
                            </div>
                            <hr class="hr mt-2 mb-4">
                            <div class="text-secondary fst-italic">Address & Contact</div>
                            <hr class="hr mt-2">
                            <!-- Address -->
                            <div class="row my-1">
                                <div class="col-4">
                                    <div class="d-flex align-items-center">
                                        <img src="assets/images/icons/patient-details-address.png" width="20" height="20">
                                        <span class="ms-2 text-muted">Address</span>
                                    </div>
                                </div>
                                <div class="col text-break">
                                    <div class="me-auto"><?= $model->address ?></div>
                                </div>
                            </div>
                            <hr class="hr my-2">
                            <!-- Contact -->
                            <div class="row my-1">
                                <div class="col-4">
                                    <div class="d-flex align-items-center">
                                        <img src="assets/images/icons/patient-details-contact.png" width="20" height="20">
                                        <span class="ms-2 text-muted">Contact</span>
                                    </div>
                                </div>
                                <div class="col text-break">
                                    <div class="me-auto"><?= $model->contact ?></div>
                                </div>
                            </div>
                            <hr class="hr my-2">
                            <!-- Parent -->
                            <div class="row my-1">
                                <div class="col-4">
                                    <div class="d-flex align-items-center">
                                        <img src="assets/images/icons/patient-details-parent.png" width="20" height="20">
                                        <span class="ms-2 text-muted">Parent</span>
                                    </div>
                                </div>
                                <div class="col text-break">
                                    <div class="me-auto"><?= $model->parent ?></div>
                                </div>
                            </div>
                            <hr class="hr mt-2 mb-4">
                            <div class="text-secondary fst-italic">Record Details</div>
                            <hr class="hr my-2">
                            <!-- Entry Date-->
                            <div class="row my-1">
                                <div class="col text-break">
                                    <div class="d-flex align-items-center">
                                        <img src="assets/images/icons/patient-details-entry.png" width="20" height="20">
                                        <span class="ms-2 text-muted">Date of entry</span>
                                    </div>
                                </div>
                                <div class="col text-break text-end">
                                    <div class="me-auto"><?= Dates::toString($model->dateCreated, "M. d, Y") ?></div>
                                </div>
                            </div>
                            <hr class="hr my-2">
                            <!-- Address -->
                            <div class="row my-1">
                                <div class="col text-break">
                                    <div class="d-flex align-items-center">
                                        <img src="assets/images/icons/patient-details-updated.png" width="20" height="20">
                                        <span class="ms-2 text-muted">Last Updated</span>
                                    </div>
                                </div>
                                <div class="col text-break text-end">
                                    <div class="me-auto"><?= Dates::toString($model->dateUpdated, "M. d, Y") ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- CHECKUP HISTORY -->
                <div class="col h-100 overflow-hidden p-4">
                    <div class="d-flex flex-column bg-white border rounded-3 shadow-3-strong h-100 overflow-hidden pt-2 pb-4">
                        <div class="d-flex flex-row align-items-center px-3">
                            <div class="col-title d-flex align-items-center fs-6 me-auto">
                                <img src="assets/images/icons/patient-details-history.png" width="28" height="28">
                                <span class="ms-2 font-base-light">Checkup History (<?= $totalCheckupHistory ?>)</span>
                            </div>
                            <div class="me-0">
                                <form action="<?= Pages::CHECKUP_FORM ?>" method="post">
                                    <input type="text" name="appointment-patient-key" class="d-none" value="<?= $detailsKey ?>">
                                    <button type="submit" class="btn btn-secondary btn-base btn-sm">
                                        <i class="fas fa-plus me-1"></i>
                                        Appointment
                                    </button>
                                </form>
                            </div>
                        </div>
                        <hr class="hr my-2">
                        <div data-simplebar class="h-100 flex-grow-1 overflow-y-auto no-native-scroll py-2 px-4">
                            <div class="d-flex flex-wrap gap-3 w-100 h-100">
                                <?php bindCheckupHistory() ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>

    <form action="<?= Pages::CHECKUP_DETAILS ?>" method="post" class="frm-details d-none">
        <input type="text" name="record-key" id="record-key">
    </form>
    <!-- END MAIN WORKAREA -->

    <?php $masterPage->endWorkarea(); //END WORKAREA LAYOUT 
    ?>
    <!-- END: ROOT CONTAINER -->

    <?php

    require_once($rootCwd . "components/alert-dialog/alert-dialog.php");
    ?>

    <!--SCRIPTS-->
    <script src="assets/lib/jquery/jquery-3.6.1.min.js"></script>
    <script src="assets/lib/jquery-ui-1.13.2.custom/jquery-ui.min.js"></script>
    <script src="assets/lib/datatables/datatables.min.js"></script>
    <script src="assets/lib/mdb5/js/mdb.min.js"></script>
    <script src="assets/lib/momentjs/moment-with-locales.js"></script>
    <script src="assets/lib/jquery.nicescroll/jquery.nicescroll.min.js"></script>
    <script src="assets/lib/simplebar/simplebar.min.js"></script>

    <script src="assets/js/nicescroll.js"></script>
    <script src="assets/js/system.js"></script>
    <script src="assets/js/base-ui.js"></script>
    <script src="assets/js/page.view-patient-details.js"></script>

    <script src="components/alert-dialog/alert-dialog.js"></script>

</body>

</html>