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
                                <div class="fas fa-folder-open font-accent"></div>
                                <div class="ms-2 fw-bold fs-6">View</div>
                                <div class="breadcrumb-arrow fas fa-play mx-3"></div>
                                <div class="fas fa-users font-hilight"></div>
                                <div class="ms-2 fw-bold fs-6">Users</div>
                                <div class="breadcrumb-arrow fas fa-play mx-3"></div>
                                <div class="fas fa-user-plus text-success"></div>
                                <div class="ms-2 fw-bold fs-6">Create User</div>
                                <div class="breadcrumb-arrow fas fa-play mx-3"></div>
                                <div class="d-flex align-items-center">
                                    <img src="assets/images/icons/isgn_staff.png" alt="icon" width="24" height="24">
                                    <div class="fw-bold ms-2 fs-6">Staff</div>
                                </div>
                            </div>

                            <!-- WORKAREA -->
                            <div class="work-area">
                                
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