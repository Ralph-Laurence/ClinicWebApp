<?php
require_once("rootcwd.php");
require_once($rootCwd . "controllers/StatisticsController.php");
?>

<body>

    <?php
    $masterPage->beginWorkarea(Navigation::NavIndex_Statistics); //BEGIN WORKAREA LAYOUT 
    ?>

    <!-- BEGIN MAIN WORKAREA -->
    <div id="main-workarea" class="flex-grow-1 d-flex flex-column h-100 bg-white shadow-2-strong overflow-hidden py-2">

        <? // BREADCRUMB 
        ?>
        <div class="breadcrumbs px-4 w-100 d-flex flex-row align-items-center justify-content-start">
            <img src="assets/images/icons/sidenav-insights.png" width="20" height="20">
            <div class="fs-6 fw-bold ms-2">Insights</div>
            <div class="breadcrumb-arrow fsz-12 fas fa-play mx-3"></div>
            <img src="assets/images/icons/sidenav-stats.png" width="20" height="20">
            <div class="ms-2 fw-bold fs-6">Statistics</div>
        </div>

        <hr class="hr divider" />

        <div data-simplebar class="flex-grow-1 checkup-form-fields h-100 overflow-y-auto no-native-scroll">

            <!-- STAT CARDS -->
            <div class="row mb-2 mx-2 stat-cards-top">
                <div class="col d-flex justify-content-center px-0">
                    <div class="stat-card stat-card-aqua shadow-2-strong p-3 rounded-3">
                        <div class="stat-card-bg"></div>
                        <h6 class="stat-card-title">Total Patients</h6>
                        <h3 class="stat-card-label mb-0"><?= getTotalPatients("all") ?></h3>
                    </div>
                </div>
                <div class="col d-flex justify-content-center px-0">
                    <div class="stat-card stat-card-purple shadow-2-strong p-3 rounded-3">
                        <div class="stat-card-bg"></div>
                        <h6 class="stat-card-title">Students</h6>
                        <h3 class="stat-card-label mb-0"><?= getTotalPatients(PatientTypes::$STUDENT) ?></h3>
                    </div>
                </div>
                <div class="col d-flex justify-content-center px-0">
                    <div class="stat-card stat-card-yellow shadow-2-strong p-3 rounded-3">
                        <div class="stat-card-bg"></div>
                        <h6 class="stat-card-title">Teachers</h6>
                        <h3 class="stat-card-label mb-0"><?= getTotalPatients(PatientTypes::$TEACHER) ?></h3>
                    </div>
                </div>
                <div class="col d-flex justify-content-center px-0">
                    <div class="stat-card stat-card-red shadow-2-strong p-3 rounded-3">
                        <div class="stat-card-bg"></div>
                        <h6 class="stat-card-title">Staffs</h6>
                        <h3 class="stat-card-label mb-0"><?= getTotalPatients(PatientTypes::$STAFF) ?></h3>
                    </div>
                </div>
            </div>

            <hr class="hr">

            <!-- ACTIVITIES -->
            <div class="row mx-2">
                <div class="col-8 p-2">
                    <div class="shadow-2-strong w-100 p-2">
                        <div id="areaChartContainer" style="height: 300px; width: 100%;"></div>
                    </div>
                </div>
                <div class="col-4 p-2">
                    <div class="d-flex align-items-center justify-content-center position-relative shadow-2-strong p-2">
                        <canvas class="z-10" id="daily-activities" style="width:100%; max-width: 300px;"></canvas>
                        <div class="donut-inner position-absolute start-0 end-0 top-0 bottom-0 w-100 h-100 
                            d-flex align-items-center justify-content-center flex-column pt-5">
                            <h5 class="mt-5 inner-total-label">0</h5>
                            <span>Total Records</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ACTIVITIES -->
            <div class="row mx-2">
                <div class="col-8 p-2">
                    <div class="shadow-2-strong w-100 p-2">
                        <div id="barChartContainer" style="height: 300px; width: 100%;"></div> 
                    </div>
                </div>
                <div class="col-4 p-2">
                     
                </div>
            </div>

        </div>

    </div>
    <div class="d-none">
        <textarea class="weekly-insights"><?= getWeeklyTotalRecords(); ?></textarea>
        <textarea class="daily-insights"><?= getDailyTotalRecords(); ?></textarea>
        <textarea class="monthly-insights"><?= getMonthlyTotalRecords(); ?></textarea>
    </div>
    <!-- END MAIN WORKAREA -->

    <?php $masterPage->endWorkarea(); //END WORKAREA LAYOUT 
    ?>

    <!--SNACKBAR AND MODAL-->
    <?php

    $masterPage->includeDialogs(true, true, true, true);

    ?>


    <!--SCRIPTS-->
    <script src="assets/lib/jquery/jquery-3.6.1.min.js"></script>
    <script src="assets/lib/jquery-ui-1.13.2.custom/jquery-ui.min.js"></script>
    <script src="assets/lib/datatables/datatables.min.js"></script>
    <script src="assets/lib/mdb5/js/mdb.min.js"></script>
    <script src="assets/lib/mdb5/js/mdb.form-validation.js"></script>
    <script src="assets/lib/momentjs/moment-with-locales.js"></script>
    <script src="assets/lib/chartjs/Chart.min.js"></script>
    <script src="assets/lib/canvasjs/jquery.canvasjs.min.js"></script>
    <script src="assets/lib/simplebar/simplebar.min.js"></script>

    <script src="assets/js/system.js"></script>
    <script src="assets/js/base-ui.js"></script>
    <script src="assets/js/page.transaction-checkup-form.js"></script>
    <script src="assets/js/page.insights-stats.js"></script>

    <script src="components/confirm-dialog/confirm-dialog.js"></script>
    <script src="components/alert-dialog/alert-dialog.js"></script>
    <script src="components/snackbar/snackbar.js"></script>
    <script src="components/toast/toast.js"></script>
</body>

</html>