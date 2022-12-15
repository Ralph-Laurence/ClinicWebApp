<?php

require_once("database/configs.php");
require_once("includes/system.php");
require_once("includes/utils.php");
require_once("layout-header.php");

?>

<body>

    <!-- BEGIN CONTAINER -->
    <div class="container-fluid h-100 bg-document p-0">

        <!-- TITLE BANNER -->
        <?php include("layouts/banner.php") ?>
        <!-- TITLE BANNER -->

        <!-- MAIN CONTENT -->
        <main class="main-content-wrapper d-flex h-100 pt-5">

            <section class="d-flex flex-grow-1 mt-2 overflow-hidden">

                <!-- NAVIGATION -->
                <?php include("layouts/navigation.php") ?>

                <!--WORKAREA-->
                <section class="workarea w-100 pb-4">

                    <!--WELCOME BANNER-->
                    <?php include("layouts/welcome-banner.php"); ?>

                    <!--THE WORKSHEET WRAPPER-->
                    <div class="worksheet-wrapper p-4 w-100 h-100 overflow-hidden position-relative">

                        <div class="worksheet d-flex flex-column bg-white shadow-2-strong w-100 h-100 p-4 scrollable" style="overflow-y: auto;">

                            <!-- BREADCRUMB-->
                            <div class="breadcrumb w-100 d-flex flex-row align-items-center justify-content-start">
                                <h6 class="fas fa-folder-open font-accent"></h6>
                                <h6 class="ms-2 fw-bold">View</h6>
                                <h6 class="breadcrumb-arrow fas fa-play mx-3"></h6>
                                <h6 class="fas fa-notes-medical font-red"></h6>
                                <h6 class="ms-2 fw-bold">Patient Records</h6>
                            </div>

                            <!--SEARCH BARS-->
                            <div class="searchbar-wrapper d-flex flex-row gap-2">
                                <div class="form-outline w-25">
                                    <input type="text" id="form12" class="form-control" />
                                    <label class="form-label" for="form12">Find Records</label>
                                </div>
                                <select name="" id="find-patient-option">
                                    <option value="">By Firstname</option>
                                    <option value="">By Lastname</option>
                                    <option value="">By Month</option>
                                    <option value="">By Record Number</option>
                                </select>
                                <select name="" id="month-options">
                                    <option value="" disabled selected>Select Month</option>

                                </select>
                                <button class="btn btn-primary bg-base">
                                    <i class="fas fa-search me-2"></i>
                                    <span>Find</span>
                                </button>
                                <button class="btn btn-primary display-none">
                                    <i class="fas fa-undo me-2"></i>
                                    <span>Clear</span>
                                </button>
                            </div>

                            <!--DIVIDER--> 
                            <div class="divider-separator border border-1 border-bottom my-3"></div>

                            <!-- WORKSHEET TABLE-->
                            <div class="w-100 flex-grow-1 border border-1 border-secondary mb-2 worksheet-table-wrapper" style="overflow-y: auto;">

                                <table class="table table-sm table-striped table-hover position-relative">
                                    <thead class="bg-amber text-dark" style="position: sticky; top: 0;">
                                        <tr> 
                                            <th class="fw-bold" scope="col">Checkup #</th>
                                            <th class="fw-bold" scope="col">Patient</th>
                                            <th class="fw-bold" scope="col">Type</th>
                                            <th class="fw-bold" scope="col">Illness</th> 
                                            <th class="fw-bold" scope="col">Checkup Date</th>
                                            <th class="fw-bold" scope="col">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="checkup-dataset bg-white">
                                         
                                    </tbody>
                                </table>
                            </div> 
                        </div>

                    </div>

                </section>

            </section>
        </main>
        <!-- MAIN CONTENT -->

    </div>
    <!-- END CONTAINER -->

    <!--SCRIPTS-->
    <!--SCRIPTS-->
    <script src="assets/lib/jquery/jquery-3.6.1.min.js"></script>
    <script src="assets/lib/jquery-ui-1.13.2.custom/jquery-ui.min.js"></script>
    <script src="assets/lib/mdb5/js/mdb.min.js"></script>
    <script src="assets/lib/momentjs/moment-with-locales.js"></script>
    <script src="assets/lib/jquery.nicescroll/jquery.nicescroll.min.js"></script>

    <script src="assets/js/nicescroll.js"></script>
    <script src="assets/js/patient-records.js"></script>

</body>

</html>