<?php
@session_start();

require_once("rootcwd.php");

require_once($rootCwd . "includes/urls.php");
require_once($rootCwd . "database/configs.php");
require_once($rootCwd . "includes/system.php");
require_once($rootCwd . "includes/utils.php");
require_once($rootCwd . "layout-header.php");

if (isset($_SESSION['isLoggedIn'])) 
{
    if ($_SESSION['isLoggedIn'] === true)
    {
        throw_response_code(200, Pages::HOME);
        exit;
    }
}

?>

<body>

    <!-- BEGIN CONTAINER -->
    <div class="container-fluid h-100 bg-document p-0">

        <div class="login-alert bg-base text-white fsz-14 p-2 d-flex w-100 align-items-center justify-content-center">
            <i class="fas fa-info-circle me-2"></i>
            <span>Log into your account to begin using the system</span>
        </div>

        <!-- MAIN HEADER -->
        <section class="main-header home-header-bg d-flex align-items-center p-4">
            <div class="bg-mask-color"></div>
            <div class="bg-mask-overlay"></div>
            <div class="bg-mask-overlay-right"></div>
            <div class="blog-logo me-auto" style="z-index: 15;">
                <img src="assets/images/logo-with-text.png" alt="logo" width="143" height="53">
            </div>
            <div class="login-link" style="z-index: 15;">
                <a href="<?= Pages::LOGIN ?>" class="btn btn-secondary btn-rounded btn-base">Login</a>
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