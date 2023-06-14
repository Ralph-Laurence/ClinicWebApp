<?php
require_once("rootcwd.php");
require_once($rootCwd . "controllers/UserRecordsController.php");
?>

<body>

    <?php $masterPage->beginWorkarea(Navigation::NavIndex_Users); //BEGIN WORKAREA LAYOUT 
    ?>

    <!-- MAIN WORKAREA -->
    <div class="main-workarea flex-grow-1 d-flex flex-column h-100 bg-white shadow-2-strong overflow-hidden px-4 py-2">

        <!-- BREADCRUMB-->
        <div class="breadcrumbs mb-2 w-100 d-flex flex-row align-items-center justify-content-start">
            <img src="assets/images/icons/sidenav-view.png" width="20" height="20">
            <div class="fs-6 fw-bold ms-2">View</div>
            <div class="breadcrumb-arrow fsz-12 fas fa-play mx-3"></div>
            <img src="assets/images/icons/sidenav-users.png" width="20" height="20">
            <div class="ms-2 fw-bold fs-6">Users</div> 
        </div>

        <!--SEARCH BARS-->
        <div class="searchbar-wrapper display-none effect-reciever" data-transition-index="0" data-transition="fadein">
            <div class="d-flex flex-row-1 gap-2">
                <div class="left-half me-auto">
                    <div class="control-elements d-flex flex-row gap-2 flex-wrap">
                        <div class="form-outline">
                            <input type="text" class="form-control searchbar" maxlength="32" />
                            <label class="form-label" for="form12">Find Users</label>
                        </div>
                        <div class="selectmenu-wrapper">
                            <select class="combo-box">
                                <option value="0">By Name</option>
                                <option value="1">By Email</option>
                                <option value="2">By Username</option>
                            </select>
                        </div>
                        <button type="button" class="btn btn-secondary btn-find px-3">
                            <i class="fas fa-search"></i>
                        </button>
                        <button class="btn btn-primary display-none btn-clear-search px-3" data-mdb-toggle="tooltip" data-mdb-placement="right" title="Clear Search">
                            <i class="fas fa-undo"></i>
                        </button>
                    </div>
                </div>
                <div class="right-half">
                    <div class="control-elements d-flex flex-row gap-2 flex-wrap">
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
                                <li onclick="sortBy(2, 1)" class="d-flex align-items-center gap-3 px-3 py-1 dropdown-item-custom-light">
                                    <div class="dropdown-item-icon text-center"></div>
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="assets/images/icons/sort-up.png" width="18" height="18">
                                        <div class="fs-6">Name</div>
                                    </div>
                                </li>
                                <li onclick="sortBy(4, 1)" class="d-flex align-items-center gap-3 px-3 py-1 dropdown-item-custom-light">
                                    <div class="dropdown-item-icon text-center"></div>
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="assets/images/icons/sort-up.png" width="18" height="18">
                                        <div class="fs-6">Email</div>
                                    </div>
                                </li>
                                <li onclick="sortBy(5, 1)" class="d-flex align-items-center gap-3 px-3 py-1 dropdown-item-custom-light">
                                    <div class="dropdown-item-icon text-center"></div>
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="assets/images/icons/sort-up.png" width="18" height="18">
                                        <div class="fs-6">Username</div>
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
                                <li onclick="sortBy(2, -1)" class="d-flex align-items-center gap-3 px-3 py-1 dropdown-item-custom-light">
                                    <div class="dropdown-item-icon text-center"></div>
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="assets/images/icons/sort-down.png" width="18" height="18">
                                        <div class="fs-6">Name</div>
                                    </div>
                                </li>
                                <li onclick="sortBy(4, -1)" class="d-flex align-items-center gap-3 px-3 py-1 dropdown-item-custom-light">
                                    <div class="dropdown-item-icon text-center"></div>
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="assets/images/icons/sort-down.png" width="18" height="18">
                                        <div class="fs-6">Email</div>
                                    </div>
                                </li>
                                <li onclick="sortBy(5, -1)" class="d-flex align-items-center gap-3 px-3 py-1 dropdown-item-custom-light">
                                    <div class="dropdown-item-icon text-center"></div>
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="assets/images/icons/sort-down.png" width="18" height="18">
                                        <div class="fs-6">Username</div>
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
                                    <small class="fw-bold text-uppercase font-base">Filter User Types</small>
                                </li>
                                <?php createFilterItems(); ?>
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
                                    <a href="<?= Pages::CREATE_USER ?>" class="d-flex align-items-center gap-3 px-3 py-1 dropdown-item-custom-light">
                                        <div class="dropdown-item-icon text-center"></div>
                                        <div class="d-flex align-items-center gap-2">
                                            <img src="assets/images/icons/bcrumb-create-user.png" width="18" height="18">
                                            <div class="fs-6">Add User</div>
                                        </div>
                                    </a>
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
            </div>
        </div>

        <!--DIVIDER-->
        <hr class="hr divider my-2">

        <!-- Count Indicators -->
        <div class="row mb-2">
            <div class="col col-count-indicators d-flex gap-2 align-items-center flex-wrap">

                <div class="display-none effect-reciever" data-transition-index="1" data-transition="fadein">
                    <div class="capsule-badge fsz-14 text-white d-flex align-items-center">
                        <div class="capsule-badge-bg rounded-start px-2">Total Users</div>
                        <div class="bg-hilight rounded-end px-2 capsule-badge-indicator"><?= $totalUsers ?></div>
                    </div>
                </div>
                <div class="display-none effect-reciever" data-transition-index="2" data-transition="fadein">
                    <div class="capsule-badge fsz-14 text-white d-flex align-items-center">
                        <div class="capsule-badge-bg rounded-start px-2">Super Admin</div>
                        <div class="bg-success rounded-end px-2 capsule-badge-indicator"><?= $totalSAdmin ?></div>
                    </div>
                </div>
                <div class="display-none effect-reciever" data-transition-index="3" data-transition="fadein">
                    <div class="capsule-badge fsz-14 text-white d-flex align-items-center">
                        <div class="capsule-badge-bg rounded-start px-2">Admin</div>
                        <div class="bg-primary rounded-end px-2 capsule-badge-indicator"><?= $totalAdmin ?></div>
                    </div>
                </div>
                <div class="display-none effect-reciever" data-transition-index="4" data-transition="fadein">
                    <div class="capsule-badge fsz-14 text-white d-flex align-items-center">
                        <div class="capsule-badge-bg rounded-start px-2">Staff</div>
                        <div class="bg-warning rounded-end px-2 capsule-badge-indicator"><?= $totalStaff ?></div>
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

        <div data-simplebar class="w-100 h-100 overflow-y-auto no-native-scroll datatable-wrapper border display-none effect-reciever" data-transition-index="2" data-transition="fadein">
            <table class="table table-sm table-striped table-hover dataset-table position-relative">
                <thead class="style-secondary position-sticky top-0 z-10">
                    <tr class="align-middle">

                        <th class="px-2 text-center mx-0 row-check-parent" scope="col" data-orderable="false">
                            <div class="d-inline">
                                <input class="form-check-input px-0 mx-0" type="checkbox" id="column-check-all" value="" />
                            </div>
                        </th>
                        <th scope="col" data-orderable="false" class="th-75"></th>
                        <th scope="col" data-orderable="false" class="fw-bold th-230">
                            <div class="d-flex align-items-center">
                                <span class="label me-auto">Name</span>
                                <img class="sort-icon display-none" src="" width="16" height="16">
                            </div>
                        </th>
                        <th scope="col" data-orderable="false" class="fw-bold th-150">
                            <div class="d-flex align-items-center">
                                <span class="label me-auto">Role</span>
                                <img class="sort-icon display-none" src="" width="16" height="16">
                            </div>
                        </th>
                        <th scope="col" data-orderable="false" class="fw-bold th-180">
                            <div class="d-flex align-items-center">
                                <span class="label me-auto">Email</span>
                                <img class="sort-icon display-none" src="" width="16" height="16">
                            </div>
                        </th>
                        <th scope="col" data-orderable="false" class="fw-bold th-150">
                            <div class="d-flex align-items-center">
                                <span class="label me-auto">Username</span>
                                <img class="sort-icon display-none" src="" width="16" height="16">
                            </div>
                        </th> 
                        <th scope="col" data-orderable="false" class="fw-bold th-150 text-center">Action</th>
                        <th scope="col" data-orderable="false" class="d-none">UserKey</th>
                    </tr>
                </thead>
                <tbody class="bg-white dataset-body no-native-scroll">
                    <?php bindDataset(); ?>
                </tbody>
                <tfoot class="d-none">
                    <tr>
                        <th></th>
                        <th></th>
                        <th class="search-col-names"></th>
                        <th></th>
                        <th class="search-col-email"></th>
                        <th class="search-col-username"></th>
                        <th></th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <!-- END MAIN WORKAREA -->

    <?php
    $masterPage->endWorkarea(); //END WORKAREA LAYOUT 
    ?>
    <form action="<?= Pages::USER_DETAILS ?>" class="frm-details d-none" method="POST">
        <input type="text" name="details-key" id="details-key">
    </form>
    <form action="<?= Tasks::DELETE_USER ?>" method="POST" class="frm-delete d-none">
        <input type="text" name="delete-key" id="delete-key">
    </form>
    <form action="<?= Pages::EDIT_USER ?>" method="POST" class="frm-edit d-none">
        <input type="text" name="user-key" id="user-key">
    </form>
    <form action="<?= Tasks::DELETE_USERS ?>" method="POST" class="frm-delete-records d-none">
        <input type="text" name="record-keys" id="record-keys">
    </form>
    <form action="<?= Pages::USERS ?>" method="get" class="filter-form d-none">
        <input type="text" name="usertype" class="usertype">
    </form>
    <textarea class="success-message d-none"><?= getSuccessMessage() ?></textarea>
    <textarea class="error-message d-none"><?= getErrorMessage() ?></textarea>
    <?php

    $masterPage->includeDialogs(true, true, true, false);
    ?>

    <!--SCRIPTS-->
    <script src="assets/lib/jquery/jquery-3.6.1.min.js"></script>
    <script src="assets/lib/jquery-ui-1.13.2.custom/jquery-ui.min.js"></script>
    <script src="assets/lib/datatables/datatables.min.js"></script>
    <script src="assets/lib/mdb5/js/mdb.min.js"></script>
    <script src="assets/lib/momentjs/moment-with-locales.js"></script>
    <script src="assets/lib/simplebar/simplebar.min.js"></script>

    <script src="assets/js/system.js"></script>
    <script src="assets/js/base-ui.js"></script>
    <script src="assets/js/shared-effects.js"></script>
    <script src="assets/js/page.view-user-records.js"></script>
    <script src="assets/js/workarea.commons.js"></script>

    <script src="components/alert-dialog/alert-dialog.js"></script>
    <script src="components/confirm-dialog/confirm-dialog.js"></script>
    <script src="components/snackbar/snackbar.js"></script>

</body>

</html>