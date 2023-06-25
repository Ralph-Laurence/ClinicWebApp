<?php
require_once("rootcwd.php");
require_once($rootCwd . "controllers/MedStocksReportController.php");
?>

<body class="position-relative">

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
            <img src="assets/images/icons/icn_stock_list.png" width="20" height="20">
            <div class="ms-2 fw-bold fs-6">Medicine Stocks</div>
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
                                    <th class="text-center text-uppercase font-base" colspan="5">Medicines and Supplies List</th>
                                </tr>
                                <tr class="border">
                                    <th class="text-center font-base" colspan="5"><?= Dates::dateToday("F d, Y") ?></th>
                                </tr>
                                <tr class="border">
                                    <th class="bg-document" colspan="5"></th>
                                </tr>
                                <tr class="text-uppercase border">
                                    <th scope="col" style="width: 30%;">Medicine / Supply</th>
                                    <th scope="col" style="width: 5%;" class="border-start border-end">Qty.</th>
                                    <th scope="col" style="width: 18%;">Units</th>
                                    <th scope="col" style="width: 12%;" class="border-start px-1 text-center">Expiry Date</th>
                                    <th scope="col" style="width: 15%;" class="border-start">Remarks</th>
                                </tr>
                            </thead>
                            <tbody class="border">
                                <?php bindDataset(); ?>
                            </tbody> 
                        </table>
                    </div>
                    <div class="foot mt-4">
                        <div class="row mb-2">
                            <div class="col"></div>
                            <div class="col text-center">
                                <small class="d-block text-capitalize text-muted mb-2">Prepared by:</small>
                                <small class="d-block text-capitalize"><?= getPreparedBy('name') ?></small>
                                <small class="text-muted d-block"><?= getPreparedBy() ?></small>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <!-- FLOATING ACTION BUTTON -->
            <div class="fab fab-print rounded-circle me-5 mb-5 shadow-3-strong z-10 position-fixed end-0 bottom-0 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                <i class="fas fa-print"></i>
            </div>

            <!-- SPINNER MODAL -->
            <div class="spinnerModal position-fixed w-100 h-100 z-100 m-auto display-none align-items-center justify-content-center end-0 start-0 top-0 bottom-0"
            style="background-color: rgb(0,0,0,0.4);">
                <div class="spinner-bg-parent rounded-3 bg-white p-4">
                    <div class="progress-loader-wrapper">
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
    <script src="assets/js/common.report-printing.js"></script>
    <script src="assets/js/workarea.commons.js"></script>
    <script src="assets/js/shared-effects.js"></script>

    <script src="components/alert-dialog/alert-dialog.js"></script>
    <script src="components/snackbar/snackbar.js"></script>

</body>

</html>