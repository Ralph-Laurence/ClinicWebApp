<?php
@session_start();

require_once("rootcwd.php");

require_once($rootCwd . "includes/urls.php");

// we must be logged in to view this page
if (!isset($_SESSION['isLoggedIn']) || $_SESSION['isLoggedIn'] !== true)
{
    header("Location: " . Navigation::$URL_LOGIN);
    exit;
}

require_once($rootCwd . "database/configs.php");
require_once($rootCwd . "includes/system.php");
require_once($rootCwd . "includes/utils.php");
require_once($rootCwd . "layout-header.php");
require_once($rootCwd . "includes/inc.get-checkup-records.php");

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
                <?php require_once("layouts/navigation.php") ?>

                <!--WORKAREA-->
                <section class="workarea w-100 pb-4">

                    <!--WELCOME BANNER-->
                    <?php include_once("layouts/welcome-banner.php"); ?>

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
                                <form action="" method="POST" class="d-flex flex-row gap-2 filter-form">
                                    <div class="form-outline">
                                        <input type="text" id="input-keyword" name="input-keyword" class="form-control"
                                        value="<?php if (!empty($keyword)) echo $keyword; ?>" 
                                        <?php if (!empty($monthOptions)) echo "disabled"; ?>/>
                                        <label class="form-label" for="form12">Find Records</label>
                                    </div>
                                    <select name="find-patient-option" id="find-patient-option">
                                        <option value="filter-fname" <?php if (!empty($findBy) && $findBy == "filter-fname") echo "selected"; ?> >By Firstname</option>
                                        <option value="filter-lname" <?php if (!empty($findBy) && $findBy == "filter-lname") echo "selected"; ?> >By Lastname</option>
                                        <option value="filter-month" <?php if (!empty($findBy) && $findBy == "filter-month") echo "selected"; ?> >By Month</option>
                                        <option value="filter-rec-num" <?php if (!empty($findBy) && $findBy == "filter-rec-num") echo "selected"; ?> >By Record Number</option>
                                    </select>
                                    <select name="month-options" id="month-options" <?php if (empty($monthOptions)) echo "disabled"; ?>>
                                        <option value="" disabled selected>Select Month</option>
                                        <?php 
                                            
                                        for($i = 1; $i <= 12; $i++)
                                        {
                                            $monthName = date("F", mktime(0, 0, 0, $i, 10));
                                            $monthIndex = str_pad($i, 2, '0', STR_PAD_LEFT);
                                            $selected = $monthIndex == $monthOptions ? "selected" : "";
                                            echo "<option $selected value='$monthIndex'>$monthName</option>";
                                        }
                                            
                                        ?>
                                    </select>
                                    <button type="button" class="btn btn-primary bg-base btn-find">
                                        <i class="fas fa-search me-2"></i>
                                        <span>Find</span>
                                    </button>
                                </form>
                                <button <?php echoOnclick(Navigation::$URL_PATIENT_RECORDS); ?> class="btn btn-primary <?php if (empty($condition)) echo 'display-none'; ?>">
                                    <i class="fas fa-undo me-2"></i>
                                    <span>Clear</span>
                                </button>
                            </div>

                            <!--DIVIDER-->
                            <div class="divider-separator border border-1 border-bottom my-3"></div>

                            <h6>Total Records Found: <?php if(!empty($checkupDataSet)) echo count($checkupDataSet) ?></h6>

                            <!-- WORKSHEET TABLE-->
                            <div class="w-100 flex-grow-1 border border-1 border-secondary mb-2 worksheet-table-wrapper" style="overflow-y: auto;">
                                <table class="table table-sm table-striped table-hover position-relative">
                                    <thead class="bg-amber text-dark" style="position: sticky; top: 0;">
                                        <tr>
                                            <th class="fw-bold" scope="col">Checkup #</th>
                                            <th class="fw-bold" scope="col">Patient</th>
                                            <th class="fw-bold" scope="col">Classification</th>
                                            <th class="fw-bold" scope="col">Illness</th>
                                            <th class="fw-bold" scope="col">Checkup Date</th>
                                            <th class="fw-bold" scope="col">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="checkup-dataset bg-white">
                                        <?php
                                        if (!empty($checkupDataSet)) {
                                            foreach ($checkupDataSet as $row) {
                                                $formNumber = $row['form_number'];
                                                $patientName = $row['patient_name'];
                                                $patientType = $row['patient_type'];
                                                $illness = $row['illness'];
                                                $checkupDate = date("M d, Y h:i A");

                                                echo
                                                "<tr class=\"align-middle\">
                                                    <td>$formNumber</td>
                                                    <td>$patientName</td>
                                                    <td>$patientType</td>
                                                    <td>$illness</td>
                                                    <td>$checkupDate</td>
                                                    <td>
                                                        <div class=\"d-flex flex-row gap-2\">
                                                            <button type=\"button\" class=\"btn btn-primary bg-base px-3\">
                                                                <i class=\"fas fa-clone text-white\"></i>
                                                            </button>
                                                            <button type=\"button\" class=\"btn btn-warning bg-amber px-3\">
                                                                <i class=\"fas fa-pen text-dark\"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>";
                                            }
                                        }
                                        ?>
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

    <?php 
        require_once("components/alert-dialog/alert-dialog.php");
    ?>

    <!--SCRIPTS-->
    <script src="assets/lib/jquery/jquery-3.6.1.min.js"></script>
    <script src="assets/lib/jquery-ui-1.13.2.custom/jquery-ui.min.js"></script>
    <script src="assets/lib/mdb5/js/mdb.min.js"></script>
    <script src="assets/lib/momentjs/moment-with-locales.js"></script>
    <script src="assets/lib/jquery.nicescroll/jquery.nicescroll.min.js"></script>

    <script src="assets/js/nicescroll.js"></script>
    <script src="assets/js/patient-records.js"></script>
    <script src="assets/js/base-ui.js"></script>

    <script src="components/alert-dialog/alert-dialog.js"></script>

</body>

</html>