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
                setActiveLink(Navigation::$NavIndex_Restock);

                require_once("layouts/navigation.php");
                ?>

                <!--WORKAREA-->
                <section class="workarea w-100 pb-4">

                    <!--WELCOME BANNER-->
                    <?php include_once("layouts/welcome-banner.php"); ?>

                    <!--THE WORKSHEET WRAPPER-->
                    <div class="worksheet-wrapper p-2 w-100 h-100 overflow-hidden position-relative">

                        <div class="worksheet d-flex flex-column bg-white shadow-2-strong w-100 h-100 p-4 scrollable" style="overflow-y: auto;">

                            <!-- BREADCRUMB-->
                            <div class="breadcrumb w-100 d-flex flex-row align-items-center justify-content-start">
                                <h6 class="fas fa-laptop font-teal"></h6>
                                <h6 class="ms-2 fw-bold">Transaction</h6>
                                <h6 class="breadcrumb-arrow fas fa-play mx-3"></h6>
                                <h6 class="fas fa-cube font-hilight"></h6>
                                <h6 class="ms-2 fw-bold">Restock</h6>
                            </div>

                            <!--CARDS PARENT WRAPPER-->
                            <div class="cards-parent-container d-flex flex-row flex-wrap gap-2">
                                <!-- STOCK IN CARD -->
                                <div class="card standard-item-card">
                                    <div class="text-center">
                                        <img src="assets/images/icn_stockin.png" width="96" height="96" alt="Box" />
                                    </div>
                                    <div class="card-body">
                                        <h5 class="card-title">Stock In</h5>
                                        <p class="card-text">Replenish the inventory with fresh items or supplies.</p>
                                    </div>
                                    <div class="card-card-footer px-4 pb-4">
                                        <button type="button" class="btn btn-primary w-100 bg-base">Stock In</button>
                                    </div>
                                </div>
                                <!-- STOCK OUT CARD -->
                                <div class="card standard-item-card">
                                    <div class="text-center">
                                        <img src="assets/images/icn_stockout.png" width="96" height="96" alt="Box" />
                                    </div>
                                    <div class="card-body">
                                        <h5 class="card-title">Stock Out</h5>
                                        <p class="card-text">Pull out items or stocks from the inventory.</p>
                                    </div>
                                    <div class="card-card-footer px-4 pb-4">
                                        <button type="button" class="btn btn-danger bg-red w-100">Stock Out</button>
                                    </div>
                                </div>
                                <!-- WASTE CARD -->
                                <div class="card standard-item-card">
                                    <div class="text-center">
                                        <img src="assets/images/icn_waste.png" width="96" height="96" alt="Box" />
                                    </div>
                                    <div class="card-body">
                                        <h5 class="card-title">Waste</h5>
                                        <p class="card-text">Defective products or items will be disposed here.</p>
                                    </div>
                                    <div class="card-card-footer px-4 pb-4">
                                        <button type="button" class="btn btn-success w-100">Waste</button>
                                    </div>
                                </div>
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
    require_once("components/snackbar/snackbar.php");
    require_once("components/confirm-dialog/confirm-dialog.php");
    ?>

    <!--SCRIPTS-->
    <script src="assets/lib/jquery/jquery-3.6.1.min.js"></script>
    <script src="assets/lib/mdb5/js/mdb.min.js"></script>
    <script src="assets/lib/momentjs/moment-with-locales.js"></script>
    <script src="assets/lib/jquery.nicescroll/jquery.nicescroll.min.js"></script>

    <script src="assets/js/nicescroll.js"></script>

    <script src="assets/js/base-ui.js"></script>
    <script src="assets/js/system.js"></script>

    <script src="components/alert-dialog/alert-dialog.js"></script>
    <script src="components/confirm-dialog/confirm-dialog.js"></script>
    <script src="components/snackbar/snackbar.js"></script>

</body>

</html>