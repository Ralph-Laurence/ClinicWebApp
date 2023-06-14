<?php
require_once("rootcwd.php");
require_once($rootCwd . "controllers/CheckupRecordsController.php");
?>

<body>

    <?php $masterPage->beginWorkarea(Navigation::NavIndex_CheckupRecords); //BEGIN WORKAREA LAYOUT 
    ?>

    <!-- MAIN WORKAREA -->
    <div class="main-workarea flex-grow-1 d-flex flex-column h-100 bg-white shadow-2-strong overflow-hidden px-4 py-2">

        <!-- BREADCRUMB-->
        <div class="breadcrumbs mb-2 w-100 d-flex flex-row align-items-center justify-content-start">
            <img src="assets/images/icons/sidenav-view.png" width="20" height="20">
            <div class="fs-6 fw-bold ms-2">View</div>
            <div class="breadcrumb-arrow fsz-12 fas fa-play mx-3"></div>
            <img src="assets/images/icons/sidenav-medical.png" width="20" height="20">
            <div class="ms-2 fw-bold fs-6">Medical Records</div>
            <div class="breadcrumb-arrow fsz-12 fas fa-play mx-3"></div>
            <img src="assets/images/icons/sidenav-checkup.png" width="20" height="20" alt="icon">
            <div class="ms-2 fw-bold fs-6">History</div>
        </div>

        <!--SEARCH BARS-->
        <div class="searchbar-wrapper display-none effect-reciever" data-transition-index="0" data-transition="fadein">
            <div class="d-flex flex-row-1 gap-2">
                <div class="left-half me-auto">
                    <div class="control-elements d-flex flex-row gap-2 flex-wrap">
                        <div class="form-outline">
                            <input type="text" class="form-control searchbar" maxlength="32" />
                            <label class="form-label" for="form12">Find Records</label>
                        </div>
                        <select class="combo-box">
                            <option value="0">By Patient Name</option>
                            <option value="1">By Checkup Number</option>
                            <option value="2">By Checkup Date</option>
                            <option value="3">By Illness</option>
                        </select>
                        <button type="button" class="btn btn-secondary btn-find px-3" data-mdb-toggle="tooltip" data-mdb-placement="top" title="Find">
                            <i class="fas fa-search"></i>
                        </button>
                        <button class="btn btn-primary display-none btn-clear-search px-3" data-mdb-toggle="tooltip" data-mdb-placement="right" title="Clear Search">
                            <i class="fas fa-undo"></i>
                        </button>
                    </div>
                </div>
                <div class="right-half d-flex flex-row gap-2">
                    <div class="dropdown actionbar-sort">
                        <button class="btn btn-secondary fw-bold dropdown-toggle btn-sort" type="button" id="sort-dropdown-button" data-mdb-toggle="dropdown" aria-expanded="false">
                            Sort
                        </button>
                        <ul class="dropdown-menu shadow-3-strong dropdown-menu-custom-light py-2" aria-labelledby="options-dropdown-button ul-li-pointer">

                            <li class="d-flex align-items-center gap-3 px-3 py-1">
                                <div class="dropdown-item-icon text-center">
                                    <img src="assets/images/icons/sort-asc.png" width="24" height="24">
                                </div>
                                <small class="fw-bold text-uppercase font-base">Sort Ascending</small>
                            </li>
                            <li onclick="sortBy(1, 1)" class="d-flex align-items-center gap-3 px-3 py-1 dropdown-item-custom-light">
                                <div class="dropdown-item-icon text-center"></div>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="assets/images/icons/sort-up.png" width="18" height="18">
                                    <div class="fs-6">Checkup Number</div>
                                </div>
                            </li>
                            <li onclick="sortBy(4, 1)" class="d-flex align-items-center gap-3 px-3 py-1 dropdown-item-custom-light">
                                <div class="dropdown-item-icon text-center"></div>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="assets/images/icons/sort-up.png" width="18" height="18">
                                    <div class="fs-6">Checkup Date</div>
                                </div>
                            </li>
                            <li onclick="sortBy(2, 1)" class="d-flex align-items-center gap-3 px-3 py-1 dropdown-item-custom-light">
                                <div class="dropdown-item-icon text-center"></div>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="assets/images/icons/sort-up.png" width="18" height="18">
                                    <div class="fs-6">Patient Name</div>
                                </div>
                            </li>
                            <li onclick="sortBy(3, 1)" class="d-flex align-items-center gap-3 px-3 py-1 dropdown-item-custom-light">
                                <div class="dropdown-item-icon text-center"></div>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="assets/images/icons/sort-up.png" width="18" height="18">
                                    <div class="fs-6">Illness</div>
                                </div>
                            </li>
                            <li>
                                <hr class="dropdown-divider" />
                            </li>
                            <li class="d-flex align-items-center gap-3 px-3 py-1">
                                <div class="dropdown-item-icon text-center">
                                    <img src="assets/images/icons/sort-desc.png" width="24" height="24">
                                </div>
                                <small class="fw-bold text-uppercase font-base">Sort Descending</small>
                            </li>
                            <li onclick="sortBy(1, -1)" class="d-flex align-items-center gap-3 px-3 py-1 dropdown-item-custom-light">
                                <div class="dropdown-item-icon text-center"></div>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="assets/images/icons/sort-down.png" width="18" height="18">
                                    <div class="fs-6">Checkup Number</div>
                                </div>
                            </li>
                            <li onclick="sortBy(4, -1)" class="d-flex align-items-center gap-3 px-3 py-1 dropdown-item-custom-light">
                                <div class="dropdown-item-icon text-center"></div>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="assets/images/icons/sort-down.png" width="18" height="18">
                                    <div class="fs-6">Checkup Date</div>
                                </div>
                            </li>
                            <li onclick="sortBy(2, -1)" class="d-flex align-items-center gap-3 px-3 py-1 dropdown-item-custom-light">
                                <div class="dropdown-item-icon text-center"></div>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="assets/images/icons/sort-down.png" width="18" height="18">
                                    <div class="fs-6">Patient Name</div>
                                </div>
                            </li>
                            <li onclick="sortBy(3, -1)" class="d-flex align-items-center gap-3 px-3 py-1 dropdown-item-custom-light">
                                <div class="dropdown-item-icon text-center"></div>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="assets/images/icons/sort-down.png" width="18" height="18">
                                    <div class="fs-6">Illness</div>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div class="dropdown actionbar-options">
                        <button class="btn btn-secondary fw-bold dropdown-toggle w-100" type="button" id="options-dropdown-button" data-mdb-toggle="dropdown" data-mdb-auto-close="inside" aria-expanded="false">
                            Options
                        </button>
                        <ul class="dropdown-menu shadow-3-strong dropdown-menu-custom-light py-2" aria-labelledby="options-dropdown-button ul-li-pointer">

                            <li class="d-flex align-items-center gap-3 px-3 py-1">
                                <div class="dropdown-item-icon text-center">
                                    <img src="assets/images/icons/dataset-filter.png" width="24" height="24">
                                </div>
                                <small class="fw-bold text-uppercase font-base">Filter Patient Types</small>
                            </li>
                            <?= createFilterItems(); ?>
                            <li>
                                <hr class="dropdown-divider" />
                            </li>
                            <li class="d-flex align-items-center gap-3 px-3 py-1">
                                <div class="dropdown-item-icon text-center">
                                    <img src="assets/images/icons/dataset-action.png" width="24" height="24">
                                </div>
                                <small class="fw-bold text-uppercase font-base">Record Actions</small>
                            </li>
                            <li>
                                <a href="<?= Pages::CHECKUP_FORM ?>" class="d-flex align-items-center gap-3 px-3 py-1 dropdown-item-custom-light">
                                    <div class="dropdown-item-icon text-center"></div>
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="assets/images/icons/sidenav-checkup-form.png" width="18" height="18">
                                        <div class="fs-6">Create New</div>
                                    </div>
                                </a>
                            </li>
                            <li>
                                <a role="button" data-mdb-toggle="modal" data-mdb-target="#changeYearModal"
                                class="d-flex align-items-center gap-3 px-3 py-1 dropdown-item-custom-light">
                                    <div class="dropdown-item-icon text-center"></div>
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="assets/images/icons/option-icon-year.png" width="18" height="18">
                                        <div class="fs-6">Viewing Year</div>
                                    </div>
                                </a>
                            </li>
                            <!-- <li>
                                <a href="#" class="d-flex align-items-center gap-3 px-3 py-1 dropdown-item-custom-light">
                                    <div class="dropdown-item-icon text-center"></div>
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="assets/images/icons/option-icon-export.png" width="18" height="18">
                                        <div class="fs-6">Export</div>
                                    </div>
                                </a>
                            </li> -->
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
        </div>

        <!--DIVIDER-->
        <hr class="hr divider my-2">

        <!-- Count Indicators -->
        <div class="row mb-2">
            <!-- <div class="col d-flex gap-2 align-items-center flex-wrap"> -->
            <div class="col col-count-indicators d-flex gap-2 align-items-center flex-wrap">

                <div class="display-none effect-reciever" data-transition-index="1" data-transition="fadein">
                    <div class="capsule-badge fsz-14 text-white d-flex align-items-center">
                        <div class="capsule-badge-bg rounded-start px-2">Total Records</div>
                        <div class="bg-red rounded-end px-2 capsule-badge-indicator"><?= $totalRecords ?></div>
                    </div>
                </div>
                <div class="display-none effect-reciever" data-transition-index="2" data-transition="fadein">
                    <div class="capsule-badge fsz-14 text-white d-flex align-items-center">
                        <div class="capsule-badge-bg rounded-start px-2">Record Year</div>
                        <div class="bg-mdb-indigo rounded-end px-2 capsule-badge-indicator"><?= $RECORD_YEAR ?></div>
                    </div>
                </div>
                <div class="display-none effect-reciever" data-transition-index="3" data-transition="fadein">
                    <div class="capsule-badge fsz-14 text-white d-flex align-items-center">
                        <div class="capsule-badge-bg rounded-start px-2">Yesterday</div>
                        <div class="bg-warning rounded-end px-2 capsule-badge-indicator"><?= $totalYesterday ?></div>
                    </div>
                </div>
                <div class="display-none effect-reciever" data-transition-index="4" data-transition="fadein">
                    <div class="capsule-badge fsz-14 text-white d-flex align-items-center">
                        <div class="capsule-badge-bg rounded-start px-2">Today</div>
                        <div class="bg-teal rounded-end px-2 capsule-badge-indicator"><?= $totalToday ?></div>
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
                <?php createFilterBadge() ?>
            </div>
            <?php // Pagination Entries Filter 
            ?>
            <div class="col-3 d-flex align-items-center justify-content-end">
                <div class="display-none effect-reciever" data-transition-index="6" data-transition="fadein">
                    <div class="d-flex align-items-center justify-content-end">
                        <div class="me-1 d-inline">Show</div>
                        <div class="entries-paginator-container"></div>
                        <div class="ms-1 d-inline">entries</div>
                    </div>
                </div>
            </div>
        </div>

        <div data-simplebar class="w-100 h-100 border border-1 overflow-y-auto no-native-scroll datatable-wrapper display-none effect-reciever" data-transition-index="2" data-transition="fadein">
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
                                <span class="label me-auto">Transaction No.</span>
                                <img class="sort-icon display-none" src="" width="16" height="16">
                            </div>
                        </th>
                        <th scope="col" data-orderable="false" class="fw-bold th-230">
                            <div class="d-flex align-items-center">
                                <span class="label me-auto">Patient Name</span>
                                <img class="sort-icon display-none" src="" width="16" height="16">
                            </div>
                        </th>
                        <th scope="col" data-orderable="false" class="fw-bold th-180">
                            <div class="d-flex align-items-center">
                                <span class="label me-auto">Illness</span>
                                <img class="sort-icon display-none" src="" width="16" height="16">
                            </div>
                        </th>
                        <th scope="col" data-orderable="false" class="fw-bold th-150">
                            <div class="d-flex align-items-center">
                                <span class="label me-auto">Checkup Date</span>
                                <img class="sort-icon display-none" src="" width="16" height="16">
                            </div>
                        </th>
                        <th scope="col" data-orderable="false" class="fw-bold text-center th-150">Action</th>
                        <th scope="col" data-orderable="false" class="d-none">RecordKey</th>

                    </tr>
                </thead>
                <tbody class="dataset-body bg-white">
                    <?php bindDatasetToTable(); ?>
                </tbody>
                <tfoot class="d-none">
                    <tr>
                        <th></th>
                        <th class="search-col-checkup-number"></th>
                        <th class="search-col-patient-name"></th>
                        <th class="search-col-illness-name"></th>
                        <th class="search-col-checkup-date"></th>
                        <th></th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <?php $masterPage->endWorkarea(); //END WORKAREA LAYOUT 
    require_once($rootCwd . "includes/embed.history-change-year.php");
    ?>

    <?php // Hidden form; Method=GET to preview checkup details  
    ?>
    <form action="<?= Pages::CHECKUP_DETAILS ?>" class="frm-details d-none" method="POST">
        <input type="text" name="record-key" id="record-key">
    </form>

    <?php // Hidden form; This will handle the edit action of a record 
    ?>
    <form action="<?= Pages::EDIT_CHECKUP_RECORD ?>" method="POST" class="frm-edit d-none">
        <input type="text" name="record-key" id="record-key">
    </form>

    <?php // Hidden form; This will handle a record's DELETE action 
    ?>
    <form action="<?= Tasks::DELETE_CHECKUP_RECORD ?>" method="POST" class="frm-delete d-none">
        <input type="text" name="record-key" id="record-key">
    </form>

    <?php
    // Store multiple record keys here as JSON string.  
    // We will use this for getting all checked rows in table
    ?>
    <form action="<?= Tasks::DELETE_CHECKUP_RECORDS ?>" method="POST" class="frm-delete-records d-none">
        <input type="text" name="record-keys" id="record-keys">
    </form>

    <?php // Hidden fields, to hold the last updated record and it's pagination page index 
    ?>
    <form class="frm-session-var">
        <input type="text" class="d-none success-message" value="<?= getActionSuccessMessage() ?>">
        <textarea type="text" class="d-none error-message"><?= getActionErrorMessage() ?></textarea>
        <textarea class="d-none edited-row-emphasis"><?php //= trim(getEditRowEmphasizeData()) 
                                                        ?></textarea>
    </form>

    <?php

    require_once($rootCwd . "components/alert-dialog/alert-dialog.php");
    require_once($rootCwd . "components/confirm-dialog/confirm-dialog.php");
    require_once($rootCwd . "components/snackbar/snackbar.php");
    ?>

    <!--SCRIPTS-->
    <script src="assets/lib/jquery/jquery-3.6.1.min.js"></script>
    <script src="assets/lib/jquery-ui-1.13.2.custom/jquery-ui.min.js"></script>
    <script src="assets/lib/datatables/datatables.min.js"></script>
    <script src="assets/lib/mdb5/js/mdb.min.js"></script>
    <script src="assets/lib/momentjs/moment-with-locales.js"></script>
    <script src="assets/lib/simplebar/simplebar.min.js"></script>

    <script src="assets/js/page.view-checkup-records.js"></script>
    <script src="assets/js/workarea.commons.js"></script>
    <script src="assets/js/shared-effects.js"></script>
    <script src="assets/js/system.js"></script>
    <script src="assets/js/base-ui.js"></script>

    <script src="components/alert-dialog/alert-dialog.js"></script>
    <script src="components/confirm-dialog/confirm-dialog.js"></script>
    <script src="components/snackbar/snackbar.js"></script>

</body>

</html>