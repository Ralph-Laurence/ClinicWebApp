<?php
@session_start();

require_once("rootcwd.php");

require_once($rootCwd . "includes/urls.php");
require_once($rootCwd . "includes/inc.get-avatars.php");

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
                                    <img src="assets/images/icons/isgn_s_admin.png" alt="icon" width="20" height="20">
                                    <div class="fw-bold ms-2 fs-6">Super Admin</div>
                                </div>
                            </div>

                            <!-- WORKAREA -->
                            <div class="work-area">

                                <div class="fs-6 text-primary text-uppercase">Personal Information</div>
                                <div class="row mb-4 pt-2">
                                    <div class="col-3">
                                        <div class="form-outline">
                                            <input type="text" id="firstname" class="form-control" maxlength="32" required />
                                            <label class="form-label" for="firstname">Firstname</label>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="form-outline">
                                            <input type="text" id="middlename" class="form-control" maxlength="32" required />
                                            <label class="form-label" for="middlename">Middlename</label>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="form-outline">
                                            <input type="text" id="lastname" class="form-control" maxlength="32" required />
                                            <label class="form-label" for="lastname">Lastname</label>
                                        </div>
                                    </div>
                                    <div class="col-3 align-middle">
                                        <div class="d-flex align-items-center h-100">
                                            <div class="avatar-image border border-2 border-primary p-1 rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                                                <img src="assets/images/avatars/avatar_0.png" alt="avatar" width="24" height="24">
                                            </div>
                                            <button class="btn btn-secondary btn-rounded ms-2" data-mdb-toggle="modal" data-mdb-target="#avatarPickerModal">Choose Avatar</button>
                                        </div>
                                    </div>
                                </div>

                                <div class="fs-6 text-primary text-uppercase">Account Details</div>
                                <div class="row mb-4 pt-2">
                                    <div class="col-3">
                                        <div class="form-outline">
                                            <input type="email" id="email" class="form-control" required maxlength="64" />
                                            <label class="form-label" for="email">Email</label>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="form-outline">
                                            <input type="text" id="username" class="form-control" maxlength="32" required />
                                            <label class="form-label" for="username">Username</label>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="form-outline">
                                            <input type="password" id="password" class="form-control"/>
                                            <label class="form-label" for="password">Password</label>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <p>
                                            <small>Default password is <span class="fst-italic text-primary">LASTNAME_PSU</span></small>
                                        </p>
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
    require_once("layouts/create-users-avatar-picker.php");
    
    // require_once("layouts/item-info-dialog.php");

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