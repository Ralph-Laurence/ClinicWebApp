<?php
require_once("rootcwd.php");
require_once($rootCwd . "controllers/MedDistReportController.php");
?>

<body>

    <?php $masterPage->beginWorkarea(Navigation::NavIndex_Stocks); //BEGIN WORKAREA LAYOUT 
    ?>

    <!-- MAIN WORKAREA -->
    <div class="main-workarea flex-grow-1 d-flex flex-column h-100 bg-white shadow-2-strong overflow-hidden px-4 py-2">

        <!-- BREADCRUMB-->
        <div class="breadcrumbs mb-2 w-100 d-flex flex-row align-items-center justify-content-start">
            <img src="assets/images/icons/sidenav-view.png" width="20" height="20">
            <div class="fs-6 fw-bold ms-2">View</div>
            <div class="breadcrumb-arrow fsz-12 fas fa-play mx-3"></div>
            <img src="assets/images/icons/icn_export_inven.png" width="20" height="20">
            <div class="ms-2 fw-bold fs-6">Documents</div>
            <div class="breadcrumb-arrow fsz-12 fas fa-play mx-3"></div>
            <img src="assets/images/icons/icn_handout.png" width="20" height="20">
            <div class="ms-2 fw-bold fs-6">Medicine Distribution</div>
        </div>

        <!--DIVIDER-->
        <hr class="hr divider my-2">

        <div data-simplebar class="w-100 h-100 pe-2 overflow-y-auto no-native-scroll" data-transition-index="2" data-transition="fadein">

            <div class="print-background d-flex align-items-center justify-content-center bg-document p-5">

                <div class="print-paper fon-fam-special bg-white p-3 shadow-3" style="width: 800px; min-height: 1123px; border: 1px solid #C7C7C7;">
                    <div class="row">
                        <div class="col-2"></div>
                        <div class="col pt-5 text-center">
                            <h6 class="text-uppercase font-base">Pangasinan State University</h6>
                            <h6 class="fsz-12 text-uppercase">Lingayen Campus</h6>
                            <h6 class="fsz-12 text-uppercase">Lingayen, Pangasinan</h6>
                            <h6 class="text-uppercase mt-4">Medical Unit</h6>
                        </div>
                        <div class="col-2 pt-2">
                            <img src="<?= getLogo() ?>" alt="" width="64" height="64">
                        </div>
                    </div>
                    <div class="table-wrapper my-3">
                        <table class="table table-sm table-striped">
                            <thead>
                                <tr>
                                    <th class="text-center text-uppercase font-base" colspan="4">Medicine Distribution Report</th>
                                </tr>
                                <tr class="border">
                                    <th class="text-center font-base" colspan="4"><?= Dates::dateToday("F d, Y") ?></th>
                                </tr>
                                <tr class="border">
                                    <th class="bg-document" colspan="4"></th>
                                </tr>
                                <tr class="text-uppercase border">
                                    <th scope="col" style="width: 50%;">Medicines Distributed</th>
                                    <th scope="col" class="border-start border-end">Students</th>
                                    <th scope="col">Faculty</th>
                                    <th scope="col" class="border-start">Staff</th>
                                </tr>
                            </thead>
                            <tbody class="border">
                                <?php bindDataset(); ?>
                            </tbody>
                            <tfoot>
                                <tr class="border">
                                    <th class="fw-bold font-base">No. Medicines Given</th>
                                    <th class="fw-bold font-base border-start border-end""><?= getTotal(PatientTypes::$STUDENT) ?></th>
                                    <th class="fw-bold font-base"><?= getTotal(PatientTypes::$TEACHER) ?></th>
                                    <th class="fw-bold font-base border-start"><?= getTotal(PatientTypes::$STAFF) ?></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="foot mt-4">
                        <div class="text-center mb-5">
                            <h6 class="font-primary-dark">Total No. Medicines Given: <?= getOverall() ?></h6>
                        </div>
                        <div class="row mt-4 mb-2">
                            <div class="col text-center">
                                <small class="d-block text-capitalize text-muted mb-2">Prepared by:</small>
                                <small class="d-block text-capitalize"><?= getPreparedBy('name') ?></small>
                                <small class="text-muted d-block"><?= getPreparedBy() ?></small>
                            </div>
                            <div class="col text-center">
                                <small class="d-block text-capitalize text-muted mb-2">Approved by:</small>
                                <small class="d-block text-capitalize"><?= getApprovedBy() ?></small>
                                <small class="text-muted d-block">University Physician</small>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
    <!-- END MAIN WORKAREA -->
    <?php
    $masterPage->endWorkarea(); //END WORKAREA LAYOUT    
    $masterPage->includeDialogs(true, false, false, false);
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
    <script src="assets/js/page.maintenance-settings.js"></script>
    <script src="assets/js/workarea.commons.js"></script>
    <script src="assets/js/shared-effects.js"></script>

    <script src="components/alert-dialog/alert-dialog.js"></script>
    <script src="components/snackbar/snackbar.js"></script>

</body>

</html>