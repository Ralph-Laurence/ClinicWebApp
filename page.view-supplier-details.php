<?php
require_once("rootcwd.php");
require_once($rootCwd . "controllers/SupplierDetailsController.php");
?>

<body>

    <?php $masterPage->beginWorkarea(Navigation::NavIndex_Suppliers); //BEGIN WORKAREA LAYOUT 
    ?>

    <!-- MAIN WORKAREA -->
    <div class="main-workarea flex-grow-1 d-flex flex-column h-100 bg-white shadow-2-strong overflow-hidden pt-2 px-4 pb-3">

        <!-- BREADCRUMB-->
        <div class="breadcrumbs w-100 d-flex flex-row align-items-center justify-content-start">
            <img src="assets/images/icons/sidenav-view.png" width="20" height="20">
            <div class="fs-6 fw-bold ms-2">View</div>
            <div class="breadcrumb-arrow fsz-12 fas fa-play mx-3"></div>
            <img src="assets/images/icons/sidenav-supplier.png" width="20" height="20">
            <div class="ms-2 fw-bold fs-6">Suppliers</div>
            <div class="breadcrumb-arrow fsz-12 fas fa-play mx-3"></div>
            <img src="assets/images/icons/bcrumb-details.png" width="20" height="20">
            <div class="ms-2 fw-bold fs-6">Details</div>
        </div>

        <!--DIVIDER-->
        <hr class="hr divider my-2">

        <div class="container-fluid h-100 p-0 overflow-hidden mb-2">
            <div class="row h-100 flex-grow-1">
                <div class="col-4">
                    <div class="p-2 w-100 ">
                        <div class="d-flex align-items-center">
                            <a href="<?= Pages::SUPPLIERS ?>" role="button" class="btn btn-secondary me-auto px-2 py-1 mb-2">
                                <i class="fas fa-arrow-left me-2"></i>
                                Back
                            </a>
                            <button type="button" class="btn btn-secondary ms-auto px-2 py-1 mb-2" data-mdb-toggle="modal" data-mdb-target="#descriptModal">
                                <i class="fas fa-info-circle me-2"></i>
                                About
                            </button>
                        </div>
                        <div class="w-100 shadow-2-strong bg-white">
                            <div class="p-2 bg-document w-100">
                                <h6 class="font-base d-inline">Supplier's Information</h6>
                            </div>
                            <div class="bg-white doc-info-card text-center pt-3 pb-2 px-3">

                                <div class="text-wrap fs-6 fw-bold"><?= getName() ?></div>
                                <div class="text-wrap fs-6 text-primary">
                                    <i class="fas fa-phone me-2"></i>
                                    <?= getContact() ?>
                                </div>
                            </div>
                            <hr class="hr my-2">
                            <div class="bg-white doc-info-card px-3 py-2 ">
                                <div class="fs-6 d-flex">
                                    <div class="me-2" style="width: 24px;">
                                        <i class="fas fa-envelope"></i>
                                    </div>
                                    <div class="flex-fill font-base"><?= getEmail() ?></div>
                                </div>
                                <div class="fs-6 d-flex">
                                    <div class="me-2" style="width: 24px;">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </div>
                                    <div class="flex-fill text-muted"><?= getAddress() ?></div>
                                </div>
                            </div>

                        </div>

                    </div>
                </div>
                <div class="col overflow-hidden h-100">
                    <div class="p-2 w-100 h-100 col-child-wrapper">

                        <div class="w-100 h-100 d-flex flex-column shadow-2-strong bg-white">
                            <div class="p-2 flex-column bg-document w-100">
                                <h6 class="font-base d-inline">Medical Supplies</h6>
                            </div>
                            <div class="flex-grow-1 d-flex flex-column h-100 overflow-hidden pb-2">
                                <div data-simplebar class="w-100 h-100 overflow-y-auto no-native-scroll datatable-wrapper display-nonex effect-reciever" data-transition-index="2" data-transition="fadein">
                                    <table class="table table-sm table-striped dataset-table position-relative">
                                        <thead class="d-none">
                                            <tr>
                                                <th scope="col" class="fw-bold th-230"></th>
                                                <th scope="col" data-orderable="false" class="fw-bold th-75 text-center">Action</th>
                                                <th class="d-none"></th>
                                            </tr>
                                        </thead>
                                        <tbody class="dataset-body">
                                            <?php bindMedicalSupplies() ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>


    <div class="modal fade" id="descriptModal" tabindex="-1" aria-labelledby="descriptModalLabel" aria-hidden="true" data-mdb-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content mt-4">
                <div class="modal-header p-2">
                    <div class="modal-title fs-6" id="descriptModalLabel">
                        <img src="assets/images/icons/sidenav-supplier.png" width="24" height="24">
                        <span class="fs-6 fw-bold ms-2">Supplier Description</span>
                    </div>

                </div>

                <div class="modal-body">
                    <textarea class="w-100 border-0 border-light" rows="10" style="min-height: 260px; height: 260px; max-height: 260px;"><?= getDesc() ?></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-base py-1" data-mdb-dismiss="modal">OK, Close</button>
                </div>
            </div>
        </div>
    </div> 
    <form action="<?= Pages::ITEM_DETAILS ?>" class="frm-details d-none" method="POST">
        <input type="text" name="details-key" id="details-key">
    </form>
    <?php
    $masterPage->endWorkarea(); //END WORKAREA LAYOUT 
    $masterPage->includeDialogs(true, false, true, false);
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
    <script src="assets/js/page.view-supplier-details.js"></script>

    <script src="components/alert-dialog/alert-dialog.js"></script>
    <script src="components/snackbar/snackbar.js"></script>

</body>

</html>