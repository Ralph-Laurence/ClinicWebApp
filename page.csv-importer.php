<?php
require_once("rootcwd.php");
require_once($rootCwd . "controllers/CsvImporterController.php");
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
                //setActiveLink(Navigation::NavIndex_Register);

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
                                <div class="image-icon">
                                    <img src="assets/images/icons/icn-utilities.png" width="22" height="22">
                                </div>
                                <div class="ms-2 fs-6 fw-bold">Utilities</div>
                                <div class="breadcrumb-arrow fas fa-play ms-3 me-2"></div>
                                <div class="image-icon">
                                    <img src="assets/images/icons/icn-csv.png" width="20" height="20">
                                </div>
                                <div class="ms-2 fs-6 fw-bold">Import CSV</div>
                            </div>

                            <!-- DIVIDER -->
                            <hr class="hr">

                            <div class="flex-column">
                                <div class="container-fluid border-start border-end bg-document w-100 office-menubar-wrapper">
                                    <div class="row office-menubar h-100">
                                        <div class="col-3 ps-0">
                                            <div class="d-flex align-items-center h-100">
                                                <button class="menubar-button btn-file h-100 border-0 px-2 text-uppercase active">File</button>
                                                <button class="menubar-button btn-view h-100 border-0 px-2 text-uppercase">View</button>
                                                <button class="menubar-button btn-import h-100 border-0 px-2 text-uppercase">Import</button>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="titlebar h-100 d-flex align-items-center justify-content-center">CSV Filename.csv</div>
                                        </div>
                                        <div class="col-3"></div>
                                    </div>
                                    <div class="row">
                                        <div class="control-ribbon d-flex align-items-center flex-row gap-2 bg-white border-bottom p-2">
                                            <div class="control-ribbon-item browse-button d-flex align-items-center flex-column">
                                                <img src="assets/images/icons/office-browse.png" width="24" height="24">
                                                <small>Browse</small>
                                            </div>
                                            <div class="control-ribbon-item help-button d-flex align-items-center flex-column">
                                                <img src="assets/images/icons/office-help.png" width="24" height="24">
                                                <small>Help</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="h-100 overflow-hidden">
                                <div id="officeCarousel" class="carousel slide h-100 w-100 overflow-hidden" data-mdb-ride="carousel" data-mdb-interval="false">
                                    <div class="carousel-inner h-100 overflow-hidden">

                                        <div class="carousel-item bg-warning h-100 active">
                                            SLIDE 1
                                        </div>

                                        <div class="carousel-item overflow-hidden h-100">

                                            <div class="d-flex h-100 flex-column flex-grow-1 bg-control">
                                                <div data-simplebar class="flex-grow-1 h-100 no-native-scroll">
                                                    
                                                    <table class="table table-sm table-hover dataset-table position-relative w-100">
                                                        <thead class="style-secondary position-sticky top-0 start-0">
                                                            <tr class="align-middle">
                                                                <?php drawInitialTableHeaders() ?>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="dataset-body bg-white">
                                                            <?php drawInitialTableBody(20) ?>
                                                        </tbody>

                                                    </table>
                                                </div>
                                            </div>

                                        </div>

                                        <div class="carousel-item">
                                            SLIDE 3
                                        </div>

                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- https://datatables.net/forums/discussion/50520/how-to-highlight-row-with-duplicate-column-data -->

    <!--SNACKBAR AND MODAL-->
    <?php

    require_once("components/alert-dialog/alert-dialog.php");
    require_once("components/confirm-dialog/confirm-dialog.php");
    require_once("components/snackbar/snackbar.php");
    require_once("components/toast/toast.php");
    ?>


    <!--SCRIPTS-->
    <script src="assets/lib/jquery/jquery-3.6.1.min.js"></script>
    <script src="assets/lib/jquery-ui-1.13.2.custom/jquery-ui.min.js"></script>
    <script src="assets/lib/mdb5/js/mdb.min.js"></script>
    <script src="assets/lib/mdb5/js/mdb.form-validation.js"></script>
    <script src="assets/lib/momentjs/moment-with-locales.js"></script>
    <script src="assets/lib/jquery.nicescroll/jquery.nicescroll.min.js"></script>
    <script src="assets/lib/simplebar/simplebar.min.js"></script>

    <script src="assets/js/nicescroll.js"></script>
    <script src="assets/js/system.js"></script>
    <script src="assets/js/base-ui.js"></script>
    <script src="assets/js/office.js"></script>

    <script src="components/alert-dialog/alert-dialog.js"></script>
    <script src="components/confirm-dialog/confirm-dialog.js"></script>
    <script src="components/snackbar/snackbar.js"></script>
    <script src="components/toast/toast.js"></script>
</body>

</html>