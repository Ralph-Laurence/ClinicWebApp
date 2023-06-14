<?php
require_once("rootcwd.php");
require_once($rootCwd . "controllers/HomeController.php");
?>

<body>

    <!-- BEGIN CONTAINER -->
    <div class="container-fluid h-100 bg-document p-0">

        <!-- MAIN HEADER -->
        <section class="main-header home-header-bg d-flex align-items-center p-4">
            <div class="bg-mask-color"></div>
            <div class="bg-mask-overlay"></div>
            <div class="bg-mask-overlay-right"></div>
            <div class="blog-logo me-auto" style="z-index: 15;">
                <img src="assets/images/logo-with-text.png" alt="logo" width="143" height="53">
            </div>
            <div class="profile-dropdown-wrapper d-flex h-100 align-items-center justify-content-end" style="z-index: 15;">
                <div class="d-inline-flex align-items-center">
                    <div class="me-auto">
                        <small class="username fw-bold font-gray me-2"><?= UserAuth::getUsername() ?></small>
                    </div>
                    <div class="dropdown">
                        <a class="dropdown-toggle d-flex align-items-center" id="navbarDropdownMenuAvatar" role="button" data-mdb-toggle="dropdown" aria-expanded="false">

                            <div class="border border-2 border-primary rounded-circle p-1">
                                <img src="assets/images/avatars/<?= $avatar ?>" alt="avatar" width="30" height="30">
                            </div>
                        </a>
                        <div class="dropdown-menu shadow-3-strong dropdown-menu-end profile-dropdown p-3" aria-labelledby="navbarDropdownMenuAvatar">
                            <div class="row">
                                <div class="col-3 d-flex align-items-center justify-content-center">
                                    <img src="assets/images/avatars/<?= $avatar ?>" alt="avatar" width="64" height="64">
                                </div>
                                <div class="col text-wrap text-break">
                                    <h6 class="text-dark fw-bold"><?= $name ?></h6>
                                    <h6 class="text-primary fsz-14"><?= UserAuth::getEmail() ?></h6>
                                    <div class="role-display d-flex align-items-center gap-2">
                                        <img src="<?= $roleBadgeIcon ?>" width="20" height="20" />
                                        <span class="badge <?= $roleBadge ?>">
                                            <?= UserRoles::ToDescName(UserAuth::getRole()) ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="divider-separator border border-1 my-3"></div>
                            <div class="row">
                                <div class="col text-start align-middle">
                                    <a href="<?= Pages::MY_PROFILE ?>">
                                        <i class="fas fa-user-cog"></i>
                                        <span class="ms-1">My Profile</span>
                                    </a>
                                </div>
                                <div class="col text-end align-middle">
                                    <a href="<?= Tasks::LOGOUT ?>" class="bg-red text-white p-1 px-2 rounded-5" role="button">
                                        <i class="fas fa-times"></i>
                                        <span class="ms-1">Logout</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- MAIN HEADER -->

        <!-- BLOG INTRO -->
        <main class="blog-intro p-4 bg-control">
            <div class="container">
                <div class="row">
                    <div class="col-2 d-flex align-items-center justify-content-end">
                        <div class="bounce-wrapper position-relative">
                            <div class="bounce bounce-25-rev">
                                <img class="rot-60" src="assets/images/icons/doctors-bag.png" width="32" height="32">
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="text-center">
                            <h1 class="fw-bold">Medicine Inventory <span class="fs-3">&amp;</span><br>
                                <span class="font-base">Patient Information System</span>
                            </h1>
                        </div>
                        <div class="text-center mt-5">
                            <h5 class="text-muted w-75 ms-auto me-auto">
                                Manage patient information and track inventory stocks and medical supplies used each day in a healthcare setting.
                            </h5>
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="bounce-wrapper position-relative">
                            <div class="bounce bounce-25">
                                <img class="rot-20 anim-delay-25" src="assets/images/icons/treatment.png" width="32" height="32">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="get-started-wrapper text-center mt-5">
                    <button class="btn btn-primary bg-base" id="btn-get-started">Get Started</button>
                </div>
            </div>
        </main>
        <!-- BLOG INTRO -->

        <!--BLOG CONTENT-->
        <section class="blog-content bg-white pt-5 pb-4">
            <div class="container pt-4">

                <!-- BLOG: CREATE RECORD -->
                <div class="row mb-5">
                    <div class="col">
                        <h2 class="blog-title fw-bold font-base">Create patient records.</h2>
                        <h5 class="blog-text text-muted mt-3">
                            If you're still passing papers from desk to desk, chasing after physical signatures, and
                            fixing the printer paper jams, then it's time to switch to paperless record keeping. With the
                            help of our system, healthcare providers can create records more effectively, reduce medical
                            errors, provide accurate, up-to-date, and complete information about patients at the point of care.
                        </h5>
                    </div>
                    <div class="col-1"></div>
                    <div class="col">
                        <img src="assets/images/blog/screenshot_checkup-form.png" width="456" height="377">
                    </div>
                </div>

                <!-- BLOG: TRACK INVENTORY -->
                <div class="row pt-5 mb-5">
                    <div class="col mt-2">
                        <img src="assets/images/blog/screenshot_inventory.png" width="533" height="271">
                    </div>
                    <div class="col-1 mt-2"></div>
                    <div class="col mt-2">
                        <h2 class="blog-title fw-bold font-base">Keep track of the Inventory.</h2>
                        <h5 class="blog-text text-muted mt-3">
                            Manually tracking inventory with spreadsheets is time-consuming, prone to error and hard to scale.
                            With the help of our system, manual tasks can be minimized. The system automatically updates data in centralized databases for you.
                        </h5>
                    </div>
                </div>

                <!-- BLOG: PAPERLESS -->
                <div class="row pt-5 mb-3">
                    <div class="col mt-2">
                        <h2 class="blog-title fw-bold font-base">Let's go paperless!</h2>
                        <h5 class="blog-text text-muted mt-3">
                            Reducing costs through decreased paperwork, improved safety, reduced printer inks, and improved environment health.
                            Going paperless saves the environment of all wastes and toxic substances derived from paper production.
                        </h5>
                    </div>
                    <div class="col-1 mt-2"></div>
                    <div class="col mt-2">
                        <img src="assets/images/blog/earth-tree.png" width="450" height="450">
                    </div>
                </div>

                <!-- BLOG: GET STARTED -->
                <div class="mb-3" id="get-started">
                    <div class="col-6">
                        <h2 class="blog-title fw-bold font-base">Choose how you want to get started</h2>
                        <h5 class="blog-text text-muted mt-3">
                            Don't know where to start? We've listed every functionalities below and we categorized them according to features.<br><br>
                            <?php 
                            if (UserAuth::getRole() != UserRoles::SUPER_ADMIN)
                            {
                                echo <<<DIV
                                <div class="alert alert-warning fs-6">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    Some features may not be accessible due to the permissions set by your System's Administrator.
                                </div> 
                                DIV;
                            }
                            ?>
                        </h5>
                    </div>
                    <div class="col"></div>
                </div>


                <!-- BLOG: FUNCTIONS AND FEATURES -->
                <div class="fs-4 my-2 text-dark fw-bold">Transactions</div>
                <div class="row mb-5">
                    <div class="col-5">
                        <div class="bg-white d-flex shadow border border-secondary rounded-2 p-3 get-started-card hover-red" <?= redirectTo(Pages::CHECKUP_FORM) ?>>
                            <div class="icon me-3">
                                <img src="assets/images/icons/sidenav-checkup-form.png" alt="icon">
                            </div>
                            <div class="descriptions">
                                <h5 class="">Checkup Form</h5>
                                <p>Create patient records, add prescriptions, etc...</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-5">
                        <div class="bg-white d-flex shadow border border-secondary rounded-2 p-3 get-started-card hover-purple" <?= redirectTo(Pages::RESTOCK) ?>>
                            <div class="icon me-3">
                                <img src="assets/images/icons/sidenav-restock.png" alt="icon">
                            </div>
                            <div class="descriptions">
                                <h5 class="">Restock</h5>
                                <p>Replenish the inventory or dispose stocks</p>
                            </div>
                        </div>
                    </div>
                    <div class="col"></div>
                </div>

                <div class="row mb-5">
                    <div class="col-5">
                        <div class="bg-white d-flex shadow border border-secondary rounded-2 p-3 get-started-card hover-amber" <?= redirectTo(Pages::REGISTER_PATIENT) ?>>
                            <div class="icon me-3">
                                <img src="assets/images/icons/sidenav-reg-patient.png" alt="icon">
                            </div>
                            <div class="descriptions">
                                <h5 class="">Register Patient</h5>
                                <p>Create patients of different types</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-5"></div>
                    <div class="col"></div>
                </div>

                <div class="fs-4 my-2 text-dark fw-bold">View Records</div>
                <div class="row mb-5">
                    <div class="col-5">
                        <div class="bg-white d-flex shadow border border-secondary rounded-2 p-3 get-started-card hover-teal" <?= redirectTo(Pages::MEDICINE_INVENTORY) ?>>
                            <div class="icon me-3">
                                <img src="assets/images/icons/sidenav-inventory.png" alt="icon">
                            </div>
                            <div class="descriptions">
                                <h5 class="">Medicine Inventory</h5>
                                <p>Manage a stock or medical supplies</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-5">
                        <div class="bg-white d-flex shadow border border-secondary rounded-2 p-3 get-started-card hover-amber" <?= redirectTo(Pages::SUPPLIERS) ?>>
                            <div class="icon me-3">
                                <img src="assets/images/icons/sidenav-supplier.png" alt="icon">
                            </div>
                            <div class="descriptions">
                                <h5 class="">Suppliers</h5>
                                <p>View and manage suppliers information</p>
                            </div>
                        </div>
                    </div>
                    <div class="col"></div>
                </div>
                <div class="row mb-5">
                    <div class="col-5">
                        <div class="bg-white d-flex shadow border border-secondary rounded-2 p-3 get-started-card hover-red" <?= redirectTo(Pages::PATIENTS) ?>>
                            <div class="icon me-3">
                                <img src="assets/images/icons/sidenav-patients.png" alt="icon">
                            </div>
                            <div class="descriptions">
                                <h5 class="">Patient Records</h5>
                                <p>View and manage patients' information</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-5">
                        <div class="bg-white d-flex shadow border border-secondary rounded-2 p-3 get-started-card hover-purple" <?= redirectTo(Pages::USERS) ?>>
                            <div class="icon me-3">
                                <img src="assets/images/icons/sidenav-users.png" alt="icon">
                            </div>
                            <div class="descriptions">
                                <h5 class="">Users</h5>
                                <p>Manage users or update their permissions</p>
                            </div>
                        </div>
                    </div>
                    <div class="col"></div>
                </div>

                <div class="row mb-5">
                    <div class="col-5">
                        <div class="bg-white d-flex shadow border border-secondary rounded-2 p-3 get-started-card hover-amber" <?= redirectTo(Pages::CHECKUP_RECORDS) ?>>
                            <div class="icon me-3">
                                <img src="assets/images/icons/sidenav-checkup.png" alt="icon">
                            </div>
                            <div class="descriptions">
                                <h5 class="">Checkup Records</h5>
                                <p>View and manage medical records</p>
                            </div>
                        </div>
                    </div> 
                    <div class="col-5">
                        <div class="bg-white d-flex shadow border border-secondary rounded-2 p-3 get-started-card hover-amber" <?= redirectTo(Pages::DOCTORS) ?>>
                            <div class="icon me-3">
                                <img src="assets/images/icons/sidenav-doctor.png" alt="icon">
                            </div>
                            <div class="descriptions">
                                <h5 class="">Doctors</h5>
                                <p>View and manage doctors</p>
                            </div>
                        </div>
                    </div> 
                    <div class="col"></div>
                </div>

                <div class="fs-4 my-2 text-dark fw-bold">Maintenance</div>
                <div class="row mb-5">
                    <div class="col-5">
                        <div class="bg-white d-flex shadow border border-secondary rounded-2 p-3 get-started-card hover-teal" <?= redirectTo(Pages::ILLNESS) ?>>
                            <div class="icon me-3">
                                <img src="assets/images/icons/sidenav-illness.png" alt="icon">
                            </div>
                            <div class="descriptions">
                                <h5 class="">Illness</h5>
                                <p>View and manage different kinds of illnesses</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-5">
                        <div class="bg-white d-flex shadow border border-secondary rounded-2 p-3 get-started-card hover-amber" <?= redirectTo(Pages::CATEGORIES) ?>>
                            <div class="icon me-3">
                                <img src="assets/images/icons/sidenav-categories.png" alt="icon">
                            </div>
                            <div class="descriptions">
                                <h5 class="">Categories</h5>
                                <p>Manage different categories used by items</p>
                            </div>
                        </div>
                    </div>
                    <div class="col"></div>
                </div>
                <div class="row mb-5">
                    <div class="col-5">
                        <div class="bg-white d-flex shadow border border-secondary rounded-2 p-3 get-started-card hover-purple" <?= redirectTo(Pages::SETTINGS) ?>>
                            <div class="icon me-3">
                                <img src="assets/images/icons/sidenav-settings.png" alt="icon">
                            </div>
                            <div class="descriptions">
                                <h5>Settings</h5>
                                <p>Adjust the system according to your preference</p>
                            </div>
                        </div>
                    </div>
                    <div class="col"></div>
                </div>

            </div>

        </section>
        <!--BLOG CONTENT-->

        <!-- FOOTER -->
        <footer class="bg-base p-2 text-white text-center">
            <small class="text-uppercase"><?= "&copy; " . Dates::dateToday("Y") ?> - Pangasinan State University</small>
            <div class=" mt-2 powered-by">
                <small class="fsz-10">Powered By</small>
            </div>
            <div class="d-flex align-items-center justify-content-center gap-4 my-4">
                <div class="d-flex flex-column justify-content-center">
                    <div style="height: 30px;">
                        <img src="assets/images/icons/icons8_icons8_48px_1.png" class="mx-auto" width="24" height="24">
                    </div>
                    <div class="fsz-12">Pichon Icons</div>
                </div>
                <div class="d-flex flex-column justify-content-center">
                    <div style="height: 30px;">
                        <img src="assets/images/icons/mdb5.png" class="mx-auto" width="52" height="19">
                    </div>
                    <div class="fsz-12">MDBootstap 5</div>
                </div>
                <div class="d-flex flex-column justify-content-center">
                    <div style="height: 30px;">
                        <img src="assets/images/icons/font-awesome.png" class="mx-auto" width="28" height="28">
                    </div>
                    <div class="fsz-12">Font Awesome</div>
                </div>
                <div class="d-flex flex-column justify-content-center">
                    <div style="height: 30px;">
                        <img src="assets/images/icons/infinity.png" class="mx-auto" width="38" height="18">
                    </div>
                    <div class="fsz-12">Infinity Free</div>
                </div>
                <div class="d-flex flex-column justify-content-center">
                    <div style="height: 30px;">
                        <img src="assets/images/icons/jqueryui.png" class="mx-auto" width="24" height="24">
                    </div>
                    <div class="fsz-12">JQuery UI</div>
                </div>
                <div class="d-flex flex-column justify-content-center">
                    <div style="height: 30px;">
                        <img src="assets/images/icons/moment.png" class="mx-auto" width="24" height="24">
                    </div>
                    <div class="fsz-12">MomentJs</div>
                </div>
            </div>
        </footer>
        <!-- FOOTER -->

    </div>

    <!--SCRIPTS-->
    <script src="assets/lib/jquery/jquery-3.6.1.min.js"></script>
    <script src="assets/lib/mdb5/js/mdb.min.js"></script>
    <!-- <script src="assets/lib/jquery.nicescroll/jquery.nicescroll.min.js"></script>

    <script src="assets/js/nicescroll.js"></script> -->
    <script src="assets/js/system.js"></script>
    <script src="assets/js/home.js"></script>

</body>