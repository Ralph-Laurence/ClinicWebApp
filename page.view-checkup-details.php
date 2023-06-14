<?php
require_once("rootcwd.php");
require_once($rootCwd . "controllers/CheckupDetailsController.php");
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
                        <div class="main-workarea flex-grow-1 d-flex flex-column h-100 bg-white shadow-2-strong overflow-hidden px-4 py-2">

                            <!-- BREADCRUMB-->
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
                                <img src="assets/images/icons/bcrumb-details.png" width="20" height="20" alt="icon">
                                <div class="ms-2 fw-bold fs-6">Details</div>
                            </div>

                            <!--DIVIDER-->
                            <!-- <div class="divider-separator border border-1 border-bottom my-2"></div> -->
                            <div class="my-2 mx-auto px-4 w-100 checkup-details-btn-wrapper">
                                <div class="w-100 d-flex align-items-center justify-content-start " style="box-shadow: 0 -3px 0 0 #EAEBF5 inset;">
                                    <a class="btn btn-link rounded-0" role="button" href="<?= Pages::CHECKUP_RECORDS ?>" data-mdb-toggle="tooltip" data-mdb-placement="top" title="Go Back">
                                        <i class="fas fa-arrow-left"></i>
                                    </a>
                                    <button class="btn btn-link rounded-0 carousel-linkbtn btn-details active">Details</button>
                                    <button class="btn btn-link rounded-0 carousel-linkbtn btn-export">Export</button>
                                </div>
                            </div>

                            <div class="w-100 h-100 flex-grow-1 overflow-hidden position-relative mx-auto" id="checkup-details-wrapper">
                                <div data-simplebar class="w-100 h-100 overflow-y-auto no-native-scroll">

                                    <!-- BEGIN CAROUSEL -->
                                    <div id="checkup-details-carousel" class="carousel slide mx-4" data-mdb-ride="carousel" data-mdb-interval="false">
                                        <div class="carousel-inner">

                                            <!-- DETAILS VIEW -->
                                            <div class="carousel-item carousel-details active">

                                                <div class="details-view bg-white p-3 w-100 h-100">

                                                    <div class="logo-header d-flex align-items-center mb-4">
                                                        <div class="me-auto d-flex align-items-center logo-wrapper">
                                                            <img src="<?= ENV_SITE_ROOT . "assets/images/logo-s.png" ?>" width="48" height="48">
                                                            <div class="ms-2 fit-content">
                                                                <div class="text-uppercase fs-6 font-base fon-fam-special">Pangasinan State University</div>
                                                                <div class="fsz-12">PSU Lingayen | Dr. Marciano Cantor Jr. Infirmary</div>
                                                            </div>
                                                        </div>
                                                        <div class="title-header flex-fill text-end">
                                                            <div class="fs-6 fon-fam-special text-primary">Medical Report</div>
                                                            <div class="fsz-14"><?= Dates::dateToday("F d, Y") ?></div>
                                                        </div>
                                                    </div>
                                                    <div class="divider-separator border border-1 border-bottom my-2"></div>

                                                    <h6 class="fw-bold font-base my-2">Patient Information</h6>
                                                    <table class="table table-borderless table-sm checkup-details-table">
                                                        <thead class="d-none">
                                                            <tr>
                                                                <th></th>
                                                                <th></th>
                                                                <th></th>
                                                                <th></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td class="py-1 ps-0 td-1">Checkup No.</td>
                                                                <td class="py-1 font-teal"><?= $checkupDetails->checkupNumber ?></td>
                                                                <td class="py-1 td-1">Gender :</td>
                                                                <td class="py-1 px-0 td-1 text-start">
                                                                    <span class="fw-bold"><?= $checkupDetails->gender ?></span>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="py-1 ps-0 td-1 fw-bold">Patient Name :</td>
                                                                <td class="py-1 fw-bold"><?= $checkupDetails->patientName ?></td>
                                                                <td class="py-1 td-1">Weight & Height :
                                                                </td>
                                                                <td class="py-1 px-0 td-1 text-end">
                                                                    <div class="w-100 d-flex align-items-center">
                                                                        <div class="me-auto"><?= $checkupDetails->weight ?></div>
                                                                        <div><?= $checkupDetails->height ?></div>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="py-1 ps-0 td-1">Patient ID :</td>
                                                                <td class="py-1"><?= $checkupDetails->idNumber ?></td>
                                                                <td class="py-1 td-1">Age, Birthday :</td>
                                                                <td class="py-1 px-0 td-1 text-start">
                                                                    <div class="w-100 d-flex align-items-center">
                                                                        <div class="me-auto"><?= $checkupDetails->age ?></div>
                                                                        <div><?= $checkupDetails->birthday ?></div>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="py-1 ps-0 td-1">Classification :</td>
                                                                <td class="py-1"><?= $checkupDetails->type ?></td>
                                                                <td class="py-1 td-1">Entry Date :</td>
                                                                <td class="py-1 px-0 td-1 text-start">
                                                                    <div class="w-100 d-flex align-items-center">
                                                                        <div class="me-auto"><?= $checkupDetails->date ?></div>
                                                                        <div><?= $checkupDetails->time ?></div>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                    <div class="divider-separator border border-1 border-bottom my-2"></div>
                                                    <h6 class="fw-bold font-base my-2">Medical Information</h6>
                                                    <table class="table table-fixed table-borderless table-sm checkup-details-table mb-3">
                                                        <thead class="d-none">
                                                            <tr>
                                                                <th></th>
                                                                <th></th>
                                                                <th></th>
                                                                <th></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td class="py-1 ps-0 td-1">Illness/Disease</td>
                                                                <td class="py-1 fw-bold"><?= $checkupDetails->illness ?></td>
                                                                <td class="py-1 td-1">Blood Pressure</td>
                                                                <td class="py-1 px-0 td-1 text-start"><?= $checkupDetails->bp ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td class="py-1 ps-0 td-1 fw-bold font-base">Prescriptions</td>
                                                                <td colspan="2" class="py-1"><?= getTotalPrescriptions() ?></td>
                                                                <td class="py-1 td-1"></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                    <?= bindPrescriptionToTable() ?>
                                                    <div class="fst-italic text-primary text-center fsz-14 mt-4 mb-3">&quot; This report has been approved electronically. Information contained in this document is CONFIDENTIAL. &quot;</div>
                                                    <div class="divider-separator border border-1 border-bottom my-2"></div>
                                                    <div class="row pt-4 pb-2">
                                                        <div class="col">
                                                            <div class="d-flex justify-content-start">
                                                                <div class="encoded-by fit-content">
                                                                    <div class="text-center text-secondary mb-3 fsz-14">Requesting Physician</div>
                                                                    <div class="fs-6"><?= $checkupDetails->doctor ?></div>
                                                                    <div class="text-center fsz-14"><?= $checkupDetails->ward ?></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col">
                                                            <div class="d-flex justify-content-end">
                                                                <div class="encoded-by fit-content">
                                                                    <div class="text-center text-secondary mb-3 fsz-14">Verified by</div>
                                                                    <div class="fs-6"><?= $checkupDetails->encodedBy ?></div>
                                                                    <div class="text-center fsz-14"><?= $checkupDetails->role ?></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- EXPORT VIEW -->
                                            <div class="carousel-item carousel-export">
                                                <div class="carousel-ribbon d-flex align-items-center gap-3 mt-2 mb-3">
                                                    <button type="button" class="btn btn-primary btn-print" disabled>
                                                        <i class="fas fa-download me-1"></i>
                                                        Print / PDF
                                                    </button>

                                                    <!-- PROGRESS BAR SPINNER -->
                                                    <div class="progress-loader-wrapper display-none">
                                                        <div class="progress-loader d-flex align-items-center justify-content-center">
                                                            <div class="progress-spinner spin-color"></div>
                                                            <div class="mx-2 text-muted fs-6">Preparing printable copy</div>
                                                            <div class="dot-loader-wrapper pt-2">
                                                                <div class="dot-loader"></div>
                                                                <div class="dot-loader"></div>
                                                                <div class="dot-loader"></div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>
                                                <div class="cloned-dropzone-wrapper w-100 h-100 bg-document p-4">
                                                    <div class="paper-view w-100 p-3 h-100 bg-white shadow-3-strong">

                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    <!-- END CAROUSEL -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="d-none">
            <input type="text" class="checkup-success-message" value="<?= getSuccessMessage() ?>">
        </div>
    </div>
    <!-- END: ROOT CONTAINER -->
    <?php

    require_once($rootCwd . "components/alert-dialog/alert-dialog.php");
    // require_once($rootCwd . "components/confirm-dialog/confirm-dialog.php");
    require_once($rootCwd . "components/toast/toast.php");
    ?>

    <!--SCRIPTS-->
    <script src="assets/lib/jquery/jquery-3.6.1.min.js"></script>
    <script src="assets/lib/jquery-ui-1.13.2.custom/jquery-ui.min.js"></script>
    <script src="assets/lib/datatables/datatables.min.js"></script>
    <script src="assets/lib/mdb5/js/mdb.min.js"></script>
    <script src="assets/lib/momentjs/moment-with-locales.js"></script>
    <script src="assets/lib/simplebar/simplebar.min.js"></script>
    <script src="assets/lib/printThis/printThis.js"></script>

    <script src="assets/js/system.js"></script>
    <script src="assets/js/base-ui.js"></script>
    <script src="assets/js/page.view-checkup-details.js"></script>
    <!-- <script src="assets/js/shared-effects.js"></script>-->

    <script src="components/alert-dialog/alert-dialog.js"></script>
    <script src="components/toast/toast.js"></script>
    <!--<script src="components/snackbar/snackbar.js"></script> -->

</body>

</html>