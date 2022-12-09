<?php 
include("database/configs.php");
include("includes/system.php");
include("layout-header.php") ;
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
                <section class="workarea w-100 pb-5">

                    <!--WELCOME BANNER-->
                    <div class="bg-secondary welcome-banner bg-control d-flex align-items-center px-2 fw-bold fs-6">
                        <span class="welcome-text me-auto">Welcome, user!</span>
                        <span class="date-banner"><?php echo Dates::dateToday("l, F d, Y"); ?></span>
                    </div>

                    <!--THE CHECKUP FORM-->
                    <div class="checkup-form-wrapper p-4 w-100 h-100">

                        <div class="checkup-form bg-white shadow-2-strong w-100 h-100">

                        </div>

                    </div>

                </section>

            </section>
        </main> 
        <!-- MAIN CONTENT -->

    </div>
    <!-- END CONTAINER -->


    <!--SCRIPTS-->
    <script src="lib/jquery/jquery-3.6.1.min.js"></script>
    <script src="lib/mdb5/js/mdb.min.js"></script>
</body>
</html>