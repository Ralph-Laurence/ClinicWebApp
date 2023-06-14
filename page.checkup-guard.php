<?php
require_once("rootcwd.php");
require_once($rootCwd . "controllers/CheckupGuardController.php");
?>

<body>

    <?php $masterPage->beginWorkarea(Navigation::NavIndex_Checkup); //BEGIN WORKAREA LAYOUT 
    ?>

    <!-- MAIN WORKAREA -->
    <div class="main-workarea flex-grow-1 d-flex flex-column h-100 overflow-hidden px-4 py-2">

        <div data-simplebar class=" h-100 overflow-y-auto no-native-scroll">

            <div class="container">
                <?php checkRecords() ?>
            </div>

        </div>

    </div>

    <?php $masterPage->endWorkarea(); //END WORKAREA LAYOUT 
    $masterPage->includeDialogs(true, false, true, true);
    ?>

    <!--SCRIPTS-->
    <script src="assets/lib/jquery/jquery-3.6.1.min.js"></script>
    <script src="assets/lib/jquery-ui-1.13.2.custom/jquery-ui.min.js"></script>
    <script src="assets/lib/datatables/datatables.min.js"></script>
    <script src="assets/lib/mdb5/js/mdb.min.js"></script>
    <script src="assets/lib/momentjs/moment-with-locales.js"></script>
    <script src="assets/lib/simplebar/simplebar.min.js"></script>

    <script src="components/alert-dialog/alert-dialog.js"></script>
    <script src="components/snackbar/snackbar.js"></script>
    <script src="components/toast/toast.js"></script>

    <script src="assets/js/base-ui.js"></script>
    <script src="assets/js/system.js"></script>

</body>

</html>