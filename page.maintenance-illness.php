<?php
require_once("rootcwd.php");
require_once($rootCwd . "controllers/IllnessRecordsController.php");
?>

<body>

    <?php $masterPage->beginWorkarea(Navigation::NavIndex_Illness); //BEGIN WORKAREA LAYOUT 
    ?>

    <!-- MAIN WORKAREA -->
    <div class="main-workarea flex-grow-1 d-flex flex-column h-100 bg-white shadow-2-strong overflow-hidden px-4 py-2">

        <!-- BREADCRUMB-->
        <div class="breadcrumbs mb-2 w-100 d-flex flex-row align-items-center justify-content-start">
            <img src="assets/images/icons/sidenav-view.png" width="20" height="20">
            <div class="fs-6 fw-bold ms-2">View</div>
            <div class="breadcrumb-arrow fsz-12 fas fa-play mx-3"></div>
            <img src="assets/images/icons/sidenav-maintenance.png" width="20" height="20">
            <div class="ms-2 fw-bold fs-6">Maintenance</div>
            <div class="breadcrumb-arrow fsz-12 fas fa-play mx-3"></div>
            <img src="assets/images/icons/sidenav-illness.png" width="20" height="20">
            <div class="ms-2 fw-bold fs-6">Illness</div>
        </div>

        <!--DIVIDER-->
        <hr class="hr divider my-2">

        <div class="row">
            <div class="col">
                <div class="toolbar border-bottom py-2 d-flex flex-row gap-2 flex-wrap">
                    <div class="control-elements d-flex flex-row gap-2 flex-wrap me-auto">
                        <div class="form-outline">
                            <input type="text" class="form-control searchbar" maxlength="32" />
                            <label class="form-label" for="form12">Find Illness</label>
                        </div>
                        <button type="button" class="btn btn-secondary btn-find px-3" data-mdb-toggle="tooltip" data-mdb-placement="top" title="Find">
                            <i class="fas fa-search"></i>
                        </button>
                        <button class="btn btn-primary display-none btn-clear-search px-3" data-mdb-toggle="tooltip" data-mdb-placement="right" title="Clear Search">
                            <i class="fas fa-undo"></i>
                        </button>
                    </div>
                    <div class="dropdown actionbar-options">
                        <button class="btn btn-secondary fw-bold dropdown-toggle w-100" type="button" id="options-dropdown-button" data-mdb-toggle="dropdown" data-mdb-auto-close="inside" aria-expanded="false">
                            Options
                        </button>
                        <ul class="dropdown-menu shadow-3-strong dropdown-menu-custom-light py-2" aria-labelledby="options-dropdown-button ul-li-pointer">

                            <li class="d-flex align-items-center gap-3 px-3 py-1">
                                <div class="dropdown-item-icon text-center">
                                    <img src="assets/images/icons/sort-asc.png" width="24" height="24">
                                </div>
                                <small class="fw-bold text-uppercase font-base">Sort Illness</small>
                            </li>
                            <li onclick="sortBy(1, 1)" class="d-flex align-items-center gap-3 px-3 py-1 dropdown-item-custom-light">
                                <div class="dropdown-item-icon text-center"></div>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="assets/images/icons/sort-up.png" width="18" height="18">
                                    <div class="fs-6">Ascending</div>
                                </div>
                            </li>
                            <li onclick="sortBy(1, -1)" class="d-flex align-items-center gap-3 px-3 py-1 dropdown-item-custom-light">
                                <div class="dropdown-item-icon text-center"></div>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="assets/images/icons/sort-down.png" width="18" height="18">
                                    <div class="fs-6">Descending</div>
                                </div>
                            </li>
                            <li>
                                <hr class="dropdown-divider" />
                            </li>
                            <li class="d-flex align-items-center gap-3 px-3 py-1">
                                <div class="dropdown-item-icon text-center">
                                    <img src="assets/images/icons/dataset-action.png" width="24" height="24">
                                </div>
                                <small class="fw-bold text-uppercase font-base">Record Actions</small>
                            </li>
                            <li class="d-flex align-items-center gap-3 px-3 py-1 dropdown-item-custom-light" data-mdb-toggle="modal" data-mdb-target="#addIllnessModal">
                                <div class="dropdown-item-icon text-center"></div>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="assets/images/icons/icn_plus.png" width="18" height="18">
                                    <div class="fs-6">New Illness</div>
                                </div>
                            </li>
                            <li class="d-flex align-items-center gap-3 px-3 py-1 dropdown-item-custom-light option-delete-selected">
                                <div class="dropdown-item-icon text-center"></div>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="assets/images/icons/dataset-delete.png" width="18" height="18">
                                    <div class="fs-6">Delete Selected</div>
                                </div>
                            </li>
                            <li>
                                <hr class="dropdown-divider" />
                            </li>
                            <li class="d-flex align-items-center gap-3 px-3 py-1 dropdown-item-custom-light li-close-options-menu">
                                <div class="dropdown-item-icon text-center"></div>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="assets/images/icons/icn-clear.png" width="18" height="18">
                                    <div class="fs-6">Close Menu</div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-4"></div>
        </div>

        <!-- Count Indicators -->
        <div class="row my-2">
            <div class="col">

                <div class="col-count-indicators d-flex gap-2 align-items-center flex-wrap">

                    <div class="me-auto d-flex gap-2 align-items-center flex-wrap">
                        <div class="display-nonex effect-reciever" data-transition-index="1" data-transition="fadein">
                            <div class="capsule-badge fsz-14 text-white d-flex align-items-center">
                                <div class="capsule-badge-bg rounded-start px-2">Total Illness</div>
                                <div class="bg-green rounded-end px-2 capsule-badge-indicator"><?= $totalIllness ?></div>
                            </div>
                        </div>

                        <div class="capsule-badge capsule-badge-search fsz-14 align-items-center display-none">
                            <div class="capsule-badge-bg text-white rounded-start px-2">Find</div>
                            <div class="bg-light-indigo text-dark rounded-end px-2">
                                <span class="fw-bold">&quot;</span>
                                <span class="fst-italic capsule-badge-search-keyword"></span>
                                <span class="fw-bold">&quot;</span>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center justify-content-end">
                        <div class="display-nonex effect-reciever" data-transition-index="6" data-transition="fadein">
                            <div class="d-flex align-items-center justify-content-end">
                                <div class="me-1 d-inline">Show</div>
                                <div class="entries-paginator-container"></div>
                                <div class="ms-1 d-inline">entries</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-4"></div>
        </div>


        <div class="row flex-grow-1 h-100 overflow-hidden">
            <div class="col overflow-hidden h-100 flex-grow-1">

                <div data-simplebar class="w-100 h-100 overflow-y-auto no-native-scroll datatable-wrapper display-none effect-reciever" data-transition-index="2" data-transition="fadein">
                    <table class="table table-sm table-striped table-hover dataset-table position-relative">
                        <thead class="style-secondary position-sticky top-0 z-10">
                            <tr class="align-middle">

                                <th class="px-2 text-center mx-0 row-check-parent" scope="col" data-orderable="false">
                                    <div class="d-inline">
                                        <input class="form-check-input px-0 mx-0" type="checkbox" id="column-check-all" value="" />
                                    </div>
                                </th>
                                <th scope="col" data-orderable="false" class="fw-bold th-180">
                                    <div class="d-flex align-items-center">
                                        <span class="label me-auto">Illness</span>
                                        <img class="sort-icon display-none" src="" width="16" height="16">
                                    </div>
                                </th>
                                <th scope="col" data-orderable="false" class="fw-bold th-100">
                                    <div class="d-flex align-items-center">
                                        <span class="label me-auto">Records</span>
                                        <img class="sort-icon display-none" src="" width="16" height="16">
                                    </div>
                                </th>
                                <th scope="col" data-orderable="false" class="fw-bold text-center th-100">Action</th>
                                <th scope="col" data-orderable="false" class="d-none">RecordKey</th>
                                <th scope="col" data-orderable="false" class="d-none">Details</th>
                            </tr>
                        </thead>
                        <tbody class="dataset-body bg-white">
                            <?php bindDataset() ?>
                        </tbody>
                        <tfoot class="d-none">
                            <tr>
                                <th></th>
                                <th class="search-col-illness-name"></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="col-4"></div>
        </div>
    </div>
    <!-- END MAIN WORKAREA -->
    <form class="frm-session-var">
        <textarea type="text" class="d-none success-message"><?= getSuccessMessage() ?></textarea>
        <textarea type="text" class="d-none error-message"><?= getErrorMessage() ?></textarea>
    </form>
    <?php
    // Store multiple record keys here as JSON string.  
    // We will use this for getting all checked rows in table
    ?>
    <form action="<?= Tasks::DELETE_ILLNESS_RECORDS ?>" method="POST" class="frm-delete-records d-none">
        <input type="text" name="record-keys" id="record-keys">
    </form>
    <?php
    $masterPage->endWorkarea(); //END WORKAREA LAYOUT    
    $masterPage->includeDialogs(true, true, true, false);
    ?>

    <div class="modal fade" id="addIllnessModal" tabindex="-1" aria-labelledby="addIllnessModalLabel" aria-hidden="true" data-mdb-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content mt-4">
                <div class="modal-header py-2 px-3">
                    <h6 class="modal-title" id="addIllnessModalLabel">
                        <i class="fas fa-plus-circle text-success me-1"></i>
                        Add New Illness
                    </h6>
                </div>

                <div class="modal-body">
                    <form action="<?= Tasks::REGISTER_ILLNESS ?>" method="post" class="register-illness-form mb-2">
                        <div class="form-outline mb-2" style="width: 300px;">
                            <input type="text" name="illness-name" class="form-control text-primary input-add-illness-name" />
                            <label class="form-label" for="form1">Illness Name *</label>
                        </div>
                        <div class="form-outline">
                            <textarea class="form-control" name="illness-desc" style="height: 120px; min-height: 120px; max-height: 120px;" maxlength="200" data-mdb-showcounter="true"></textarea>
                            <label class="form-label" for="textAreaExample">Short Description (Optional)</label>
                            <div class="form-helper"></div>
                        </div>
                    </form>
                    <div class="p-2 rounded-2 bg-red-light font-red-dark mt-4 register-error-msg display-none"></div>
                </div>

                <div class="modal-footer py-2">
                    <button class="btn btn-secondary btn-cancel-register" type="button" data-mdb-dismiss="modal">Cancel</button>
                    <button class="btn btn-secondary btn-base btn-add-illness" type="button">Save</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="updateIllnessModal" tabindex="-1" aria-labelledby="updateIllnessModalLabel" aria-hidden="true" data-mdb-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content mt-4">
                <div class="modal-header py-2 px-3">
                    <h6 class="modal-title" id="updateIllnessModalLabel">
                        <i class="fas fa-plus-circle text-success me-1"></i>
                        Update Illness
                    </h6>
                </div>

                <div class="modal-body">
                    <form action="<?= Tasks::UPDATE_ILLNESS ?>" method="post" class="update-illness-form mb-2">
                        <input type="text" name="update-illness-key" class="update-illness-key d-none" value="">
                        <div class="form-outline mb-2" style="width: 300px;">
                            <input type="text" name="update-illness-name" class="form-control text-primary input-update-illness-name" />
                            <label class="form-label" for="form1">Illness Name *</label>
                        </div>
                        <div class="form-outline">
                            <textarea class="form-control update-illness-desc" name="update-illness-desc" style="height: 120px; min-height: 120px; max-height: 120px;" maxlength="200" data-mdb-showcounter="true"></textarea>
                            <label class="form-label" for="textAreaExample">Short Description (Optional)</label>
                            <div class="form-helper"></div>
                        </div>
                    </form>
                    <div class="p-2 rounded-2 bg-red-light font-red-dark mt-4 register-error-msg display-none"></div>
                </div>

                <div class="modal-footer py-2">
                    <button class="btn btn-secondary btn-cancel-update" type="button" data-mdb-dismiss="modal">Cancel</button>
                    <button class="btn btn-secondary btn-base btn-update-illness" type="button">Update</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="aboutIllnessModal" tabindex="-1" aria-labelledby="aboutIllnessModalLabel" aria-hidden="true" data-mdb-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content mt-4">
                <div class="modal-header py-2 px-3">
                    <h6 class="modal-title" id="aboutIllnessModalLabel">
                        <img src="assets/images/icons/sidenav-illness.png" width="24" height="24">
                        About Illness
                    </h6>
                </div>

                <div class="modal-body">
                    <h6 class="font-primary-dark illness-name mb-3">Illness Name</h6>
                    <div class="form-outline mb-2">
                        <textarea class="form-control bg-white illness-descript" style="height: 120px; min-height: 120px; max-height: 120px;" readonly></textarea>
                    </div>
                    <div class="descriptors">
                        <small class="text-muted illness-date d-block">Entry Date:</small>
                        <hr class="hr my-2">
                        <small class="fst-italic text-muted d-block illness-total-records"></small>
                    </div>
                </div>

                <div class="modal-footer py-2">
                    <button class="btn btn-secondary btn-base" type="button" data-mdb-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>

    <form action="<?= Tasks::DELETE_ILLNESS ?>" method="post" class="frm-delete d-none">
        <input type="text" name="delete-key" id="delete-key">
    </form>

    <!--SCRIPTS-->
    <script src="assets/lib/jquery/jquery-3.6.1.min.js"></script>
    <script src="assets/lib/jquery-ui-1.13.2.custom/jquery-ui.min.js"></script>
    <script src="assets/lib/datatables/datatables.min.js"></script>
    <script src="assets/lib/mdb5/js/mdb.min.js"></script>
    <script src="assets/lib/momentjs/moment-with-locales.js"></script>
    <script src="assets/lib/simplebar/simplebar.min.js"></script>

    <script src="assets/js/system.js"></script>
    <script src="assets/js/base-ui.js"></script>
    <script src="assets/js/page.maintenance-illness.js"></script>
    <script src="assets/js/workarea.commons.js"></script>
    <script src="assets/js/shared-effects.js"></script>

    <script src="components/alert-dialog/alert-dialog.js"></script>
    <script src="components/snackbar/snackbar.js"></script>
    <script src="components/confirm-dialog/confirm-dialog.js"></script>

</body>

</html>