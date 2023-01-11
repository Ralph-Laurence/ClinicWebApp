<?php
@session_start();

require_once("rootcwd.php");

require_once($rootCwd . "includes/urls.php");

// we must be logged in to view this page
if (!isset($_SESSION['isLoggedIn']) || $_SESSION['isLoggedIn'] !== true) {
    header("Location: " . Navigation::$URL_LOGIN);
    exit;
}

// REGISTER THE FILES FOR INCLUDE
//define('def_incAddItem', TRUE);

require_once($rootCwd . "database/configs.php");
require_once($rootCwd . "includes/system.php");
require_once($rootCwd . "includes/utils.php");
require_once($rootCwd . "layout-header.php");

require_once($rootCwd . "library/defuse-crypto.phar");

use Defuse\Crypto\Crypto;
use Defuse\Crypto\Key;

$defuseKey_Ascii = Helpers::getDefuseKey($pdo);
$defuseKey = Key::loadFromAsciiSafeString($defuseKey_Ascii);

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
                setActiveLink(Navigation::$NavIndex_Users);

                require_once("layouts/navigation.php");
                ?>

                <!--WORKAREA-->
                <section class="workarea w-100 pb-4">

                    <!--WELCOME BANNER-->
                    <?php include_once("layouts/welcome-banner.php"); ?>

                    <!--THE WORKSHEET WRAPPER-->
                    <div class="worksheet-wrapper pt-2 px-2 pb-4 w-100 h-100 overflow-hidden position-relative">

                        <div class="worksheet d-flex flex-column bg-white shadow-2-strong w-100 h-100 p-4 scrollable" style="overflow-y: auto;">

                            <!-- BREADCRUMB-->
                            <div class="breadcrumb w-100 d-flex flex-row align-items-center justify-content-start">
                                <h6 class="fas fa-folder-open font-accent"></h6>
                                <h6 class="ms-2 fw-bold">View</h6>
                                <h6 class="breadcrumb-arrow fas fa-play mx-3"></h6>
                                <h6 class="fas fa-users font-hilight"></h6>
                                <h6 class="ms-2 fw-bold">Users</h6>
                                <h6 class="breadcrumb-arrow fas fa-play mx-3"></h6>
                                <h6 class="fas fa-user-plus text-success"></h6>
                                <h6 class="ms-2 fw-bold">Create User</h6>
                            </div>

                            <!-- WORKAREA -->
                            <div class="work-area">
                                <table class="table table-striped table-sm">
                                    <thead class="bg-amber">
                                        <th class="fw-bold text-dark">User Type</th>
                                        <th class="fw-bold text-dark">Access Level</th>
                                        <th class="fw-bold text-dark">Description and Limitations</th>
                                        <th class="fw-bold text-dark text-center">Action</th>

                                    </thead>
                                    <tbody>
                                        <tr class="align-middle">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="assets/images/icons/isgn_s_admin.png" alt="icon" width="24" height="24">
                                                    <div class="fw-bold ms-2">Super Admin</div>
                                                </div>
                                            </td>
                                            <td>Full Control</td>
                                            <td>
                                                <div class="mb-2">
                                                    This user has full access to the system's features and functionality.
                                                </div>
                                                <ul class="list-unstyled">
                                                    <li><i class="fas fa-check-circle me-2 text-success"></i>Owner of the system</li>
                                                    <li><i class="fas fa-check-circle me-2 text-success"></i>Create all user types</li>
                                                    <li><i class="fas fa-check-circle me-2 text-success"></i>No restrictions</li>
                                                </ul>
                                            </td>
                                            <td class="text-center">
                                                <button class="btn btn-link" <?php echoOnclick(Navigation::$URL_CREATE_SUPER_ADMIN) ?>>
                                                    <span class="me-1 fw-bold">Create User</span>
                                                    <i class="fas fa-angle-double-right"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr class="align-middle">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="assets/images/icons/isgn_admin.png" alt="icon" width="24" height="24">
                                                    <div class="fw-bold ms-2">Admin</div>
                                                </div>
                                            </td>
                                            <td>High Privilege</td>
                                            <td>
                                                <div class="mb-2">
                                                    Has higher privilege than staffs but slightly lower than super admin.
                                                </div>
                                                <ul class="list-unstyled">
                                                    <li><i class="fas fa-check-circle me-2 text-success"></i>Create and manage Staff user accounts</li>
                                                    <li><i class="fas fa-check-circle me-2 text-warning"></i>Access features set by Super Admin</li>
                                                    <li><i class="fas fa-times-circle me-2 font-red"></i>Create another Admin</li>
                                                </ul>
                                            </td>
                                            <td class="text-center">
                                                <button class="btn btn-link" <?php echoOnclick(Navigation::$URL_CREATE_ADMIN) ?>>
                                                    <span class="me-1 fw-bold">Create User</span>
                                                    <i class="fas fa-angle-double-right"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr class="align-middle">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="assets/images/icons/isgn_staff.png" alt="icon" width="24" height="24">
                                                    <div class="fw-bold ms-2">Staff</div>
                                                </div>
                                            </td>
                                            <td>Moderate</td>
                                            <td>
                                                <div class="mb-2">
                                                    This user has limited access to the system's function and features.<br>
                                                    Its primary task is to create checkup record.
                                                    Some features can be<br>viewed but cannot make changes.
                                                </div>
                                                <ul class="list-unstyled">
                                                    <li><i class="fas fa-check-circle me-2 text-success"></i>Create checkup record</li>
                                                    <li><i class="fas fa-check-circle me-2 text-success"></i>Restock the inventory</li>
                                                    <li><i class="fas fa-check-circle me-2 text-warning"></i>Limited access to features</li>
                                                    <li><i class="fas fa-times-circle me-2 font-red"></i>Create another user</li>
                                                </ul>
                                            </td>
                                            <td class="text-center">
                                                <button class="btn btn-link" <?php echoOnclick(Navigation::$URL_CREATE_STAFF) ?>>
                                                    <span class="me-1 fw-bold">Create User</span>
                                                    <i class="fas fa-angle-double-right"></i>
                                                </button>
                                            </td>
                                        </tr>
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

    // modal window for showing the item details
    require_once("layouts/item-info-dialog.php");

    require_once("components/alert-dialog/alert-dialog.php");
    // require_once("components/snackbar/snackbar.php");
    // require_once("components/confirm-dialog/confirm-dialog.php");
    ?>

    <!--SCRIPTS-->
    <script src="assets/lib/jquery/jquery-3.6.1.min.js"></script>
    <script src="assets/lib/mdb5/js/mdb.min.js"></script>
    <script src="assets/lib/momentjs/moment-with-locales.js"></script>
    <script src="assets/lib/jquery.nicescroll/jquery.nicescroll.min.js"></script>

    <script src="assets/js/nicescroll.js"></script>
    <!-- <script src="assets/js/add-items.js"></script> -->
    <script src="assets/js/base-ui.js"></script>
    <script src="assets/js/system.js"></script>

    <script src="components/alert-dialog/alert-dialog.js"></script>
    <!-- <script src="components/confirm-dialog/confirm-dialog.js"></script>
    <script src="components/snackbar/snackbar.js"></script> -->

</body>

</html>