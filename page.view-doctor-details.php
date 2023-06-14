<?php
require_once("rootcwd.php");
require_once($rootCwd . "controllers/DoctorDetailsController.php");
?>

<body>

    <?php $masterPage->beginWorkarea(Navigation::NavIndex_Doctors); //BEGIN WORKAREA LAYOUT 
    ?>

    <!-- MAIN WORKAREA -->
    <div class="main-workarea flex-grow-1 d-flex flex-column h-100 bg-white shadow-2-strong overflow-hidden pt-2 px-4 pb-3">

        <!-- BREADCRUMB-->
        <div class="breadcrumbs w-100 d-flex flex-row align-items-center justify-content-start">
            <img src="assets/images/icons/sidenav-view.png" width="20" height="20">
            <div class="fs-6 fw-bold ms-2">View</div>
            <div class="breadcrumb-arrow fsz-12 fas fa-play mx-3"></div>
            <img src="assets/images/icons/sidenav-doctor.png" width="20" height="20">
            <div class="ms-2 fw-bold fs-6">Doctors</div>
            <div class="breadcrumb-arrow fsz-12 fas fa-play mx-3"></div>
            <img src="assets/images/icons/bcrum-doc-info.png" width="20" height="20">
            <div class="ms-2 fw-bold fs-6">Details</div>
        </div>

        <!--DIVIDER-->
        <hr class="hr divider my-2">

        <div class="container-fluid h-100 p-0 overflow-hidden mb-2">

            <div class="row h-100 flex-grow-1">
                <div class="col-4">
                    <div class="p-2 w-100 ">
                        <div class="d-flex align-items-center">
                            <a href="<?= Pages::DOCTORS ?>" role="button" class="btn btn-secondary me-auto px-2 py-1 mb-2">
                                <i class="fas fa-arrow-left me-2"></i>
                                Back
                            </a>
                            <button type="button" class="btn btn-secondary btn-edit ms-auto px-2 py-1 mb-2">
                                <i class="fas fa-cog me-2"></i>
                                Edit
                            </button>
                        </div>
                        <div class="w-100 shadow-2-strong bg-white">
                            <div class="p-2 bg-document w-100">
                                <h6 class="font-base d-inline">Doctor Information</h6>
                            </div>
                            <div class="bg-white doc-info-card text-center pt-3 pb-2 px-3">
                                <div class="text-wrap fs-6 fw-bold">
                                    <?= getDocName() ?>
                                </div>
                                <div class="text-wrap fs-6 text-primary">
                                    &num; <?= getRegNum() ?>
                                </div>
                                <div class="text-wrap fs-6 text-muted">
                                    <?= getSpec() ?>
                                </div>
                            </div>
                            <hr class="hr my-2">
                            <div class="bg-white doc-info-card px-3 py-2">
                                <div class="fs-6 d-flex">
                                    <div class="me-2" style="width: 24px;">
                                        <i class="fas fa-phone"></i>
                                    </div>
                                    <div class="flex-fill text-primary"><?= getContact() ?></div>
                                </div>
                                <div class="fs-6 d-flex">
                                    <div class="me-2" style="width: 24px;">
                                        <i class="fas fa-home"></i>
                                    </div>
                                    <div class="flex-fill text-muted"><?= getAddress() ?></div>
                                </div>
                            </div>
                            <hr class="hr my-2">
                            <div class="bg-white text-center text-muted py-2 px-3">
                                <?= getJoinDate() ?>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="col overflow-hidden h-100">
                    <div class="p-2 w-100 h-100 col-child-wrapper">

                        <div class="w-100 h-100 d-flex flex-column shadow-2-strong bg-white">
                            <div class="p-2 flex-column bg-document w-100">
                                <h6 class="font-base d-inline">Referred Patients (<?= $totalReferredPatients ?>)</h6>
                            </div>
                            <div class="flex-grow-1 d-flex flex-column h-100 overflow-hidden pb-2">
                                <div data-simplebar class="w-100 h-100 overflow-y-auto no-native-scroll datatable-wrapper display-nonex effect-reciever" data-transition-index="2" data-transition="fadein">
                                    <table class="table table-sm table-striped table-hover dataset-table position-relative">
                                        <thead class="style-secondary position-sticky top-0 z-10 d-none">
                                            <tr class="align-middle">

                                                <th scope="col" data-orderable="false" class="fw-bold">
                                                    <div class="d-flex align-items-center">
                                                        <span class="label me-auto">Patient Name</span>
                                                        <img class="sort-icon display-none" src="" width="16" height="16">
                                                    </div>
                                                </th>
                                                <th scope="col" data-orderable="false" class="fw-bold th-150 text-center">Action</th>
                                                <th scope="col" data-orderable="false" class="d-none">PatientKey</th>

                                            </tr>
                                        </thead>
                                        <tbody class="bg-white dataset-body">
                                            <?php bindReferredPatients() ?>
                                        </tbody>

                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <form action="<?= Pages::EDIT_DOCTOR ?>" method="post" class="d-none frm-edit">
            <input type="text" name="record-key" id="record-key" value="<?= getDocKey() ?>">
        </form>
        <form action="<?= Pages::PATIENT_DETAILS ?>" method="post" class="d-none frm-details">
            <input type="text" name="details-key" id="details-key" value="">
        </form>
    </div>

    <?php
    $masterPage->endWorkarea(); //END WORKAREA LAYOUT 
    $masterPage->includeDialogs(true, false, false, false);
    ?>

    <!--SCRIPTS-->
    <script src="assets/lib/jquery/jquery-3.6.1.min.js"></script>
    <script src="assets/lib/jquery-ui-1.13.2.custom/jquery-ui.min.js"></script>
    <script src="assets/lib/datatables/datatables.min.js"></script>
    <script src="assets/lib/mdb5/js/mdb.min.js"></script>
    <script src="assets/lib/momentjs/moment-with-locales.js"></script>
    <script src="assets/lib/jquery.nicescroll/jquery.nicescroll.min.js"></script>
    <script src="assets/lib/simplebar/simplebar.min.js"></script>

    <script src="assets/js/nicescroll.js"></script>
    <script src="assets/js/system.js"></script>
    <script src="assets/js/base-ui.js"></script>
    <script src="assets/js/page.view-doctor-details.js"></script>

    <script src="components/alert-dialog/alert-dialog.js"></script>

</body>

</html>