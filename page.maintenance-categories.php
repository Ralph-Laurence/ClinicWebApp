<?php
require_once("rootcwd.php");
require_once($rootCwd . "controllers/CategoryRecordsController.php");
?>

<body>

    <?php $masterPage->beginWorkarea(Navigation::NavIndex_Categories); //BEGIN WORKAREA LAYOUT 
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
            <img src="assets/images/icons/sidenav-categories.png" width="20" height="20">
            <div class="ms-2 fw-bold fs-6">Categories</div>
        </div>

        <!--DIVIDER-->
        <hr class="hr divider my-2">

        <div class="row">
            <div class="col">
                <div class="toolbar border-bottom py-2 d-flex flex-row gap-2 flex-wrap">
                    <div class="control-elements d-flex flex-row gap-2 flex-wrap me-auto">
                        <div class="form-outline">
                            <input type="text" class="form-control searchbar" maxlength="32" />
                            <label class="form-label" for="form12">Find Category</label>
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
                                <small class="fw-bold text-uppercase font-base">Sort Categories</small>
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
                            <li class="d-flex align-items-center gap-3 px-3 py-1 dropdown-item-custom-light li-action-add-category" data-mdb-toggle="modal" data-mdb-target="#categoryModal">
                                <div class="dropdown-item-icon text-center"></div>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="assets/images/icons/icn_plus.png" width="18" height="18">
                                    <div class="fs-6">Add Category</div>
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
                                <div class="capsule-badge-bg rounded-start px-2">Total Categories</div>
                                <div class="bg-warning rounded-end px-2 capsule-badge-indicator"><?= $totalCategories ?></div>
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
                                        <span class="label me-auto">Category</span>
                                        <img class="sort-icon display-none" src="" width="16" height="16">
                                    </div>
                                </th>
                                <th scope="col" data-orderable="false" class="fw-bold th-100">
                                    <div class="d-flex align-items-center">
                                        <span class="label me-auto">Total Items</span>
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
                                <th class="search-col-category-name"></th>
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
    <form action="<?= Tasks::DELETE_CATEGORY ?>" method="post" class="frm-delete d-none">
        <input type="text" name="delete-key" id="delete-key">
    </form>
    <?php
    // Store multiple record keys here as JSON string.  
    // We will use this for getting all checked rows in table
    ?>
    <form action="<?= Tasks::DELETE_CATEGORIES ?>" method="POST" class="frm-delete-records d-none">
        <input type="text" name="record-keys" id="record-keys">
    </form>
    <?php
    $masterPage->endWorkarea(); //END WORKAREA LAYOUT    
    $masterPage->includeDialogs(true, true, true, false);
    ?>

    <div class="modal fade" id="categoryModal" tabindex="-1" aria-labelledby="categoryModalLabel" aria-hidden="true" data-mdb-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content mt-4">
                <div class="modal-header py-2 px-3">
                    <h6 class="modal-title" id="categoryModalLabel">
                        <i class="fas fa-plus-circle text-success me-1"></i>
                        Add New Category
                    </h6>
                </div>

                <div class="modal-body">
                    <form method="post" class="categories-form mb-2">
                        <input type="text" class="category-action-mode d-none">
                        <textarea class="d-none action-register-category"><?= Tasks::REGISTER_CATEGORY ?></textarea>
                        <textarea class="d-none action-update-category"><?= Tasks::UPDATE_CATEGORY ?></textarea>
                        <div class="form-outline mb-3" style="width: 300px;">
                            <input type="text" name="category-name" class="form-control text-primary input-category-name" maxlength="64" />
                            <label class="form-label" for="form1">Category Name *</label>
                        </div>
                        <h6 class="text-muted">
                            Select Icon<small class="ms-2 fsz-12 fst-italic">(Optional)</small>
                        </h6>
                        <div class="fsz-14 fst-italic text-muted mb-2">In the absence of an item or product image, the category icon will be used instead. You can, however use each icon multiple times for every category.</div>
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <div class="d-inline-flex border rounded-2 p-2 align-items-center justify-content-center">
                                <img src="assets/images/icons/icn_no_image.png" width="48" height="48" class="icon-preview">
                            </div>
                            <div class="d-flex flex-column justify-content-start gap-2">
                                <button type="button" class="btn btn-secondary px-2 py-1 btn-choose-icon">
                                    Choose Icon
                                    <i class="fas fa-caret-down caret-icon"></i>
                                </button>
                                <small class="selected-icon-name text-capitalize"></small>
                            </div>
                        </div>
                        <div class="icon-picker-horizontal overflow-x-scroll display-none">
                            <ul class="list-group list-group-horizontal">
                                <?php bindIconPicker() ?>
                            </ul>
                        </div>
                        <input type="text" name="category-icon" class="category-icon d-none">
                        <input type="hidden" class="default-icon d-none" value="assets/images/icons/icn_no_image.png">
                        <input type="text" name="update-key" class="update-key d-none">
                    </form>
                    <div class="p-2 rounded-2 bg-red-light font-red-dark mt-4 form-error-msg display-none"></div>
                </div>

                <div class="modal-footer py-2">
                    <button class="btn btn-secondary btn-cancel-category" type="button" data-mdb-dismiss="modal">Cancel</button>
                    <button class="btn btn-secondary btn-base btn-save-category" type="button">Save</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="aboutCategoryModal" tabindex="-1" aria-labelledby="aboutCategoryModalLabel" aria-hidden="true" data-mdb-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content mt-4">
                <div class="modal-header py-2 px-3">
                    <h6 class="modal-title" id="aboutCategoryModalLabel">
                        <img src="assets/images/icons/sidenav-categories.png" width="24" height="24">
                        About Category
                    </h6>
                </div>

                <div class="modal-body">
                    <div class="about-headers mb-3">
                        <h6 class="font-primary-dark about-category-name mb-1">Category Name</h6>
                        <small class="text-muted fst-italic about-category-date">Entry Date:</small>
                    </div>
                    <div class="form-outline">
                        <textarea class="form-control bg-white category-descript" style="height: 120px; min-height: 120px; max-height: 120px;" readonly></textarea>
                    </div>
                </div>

                <div class="modal-footer py-2">
                    <button class="btn btn-secondary btn-base" type="button" data-mdb-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>

    <!--SCRIPTS-->
    <script src="assets/lib/jquery/jquery-3.6.1.min.js"></script>
    <script src="assets/lib/jquery-ui-1.13.2.custom/jquery-ui.min.js"></script>
    <script src="assets/lib/datatables/datatables.min.js"></script>
    <script src="assets/lib/mdb5/js/mdb.min.js"></script>
    <script src="assets/lib/momentjs/moment-with-locales.js"></script>
    <script src="assets/lib/simplebar/simplebar.min.js"></script>

    <script src="assets/js/system.js"></script>
    <script src="assets/js/base-ui.js"></script>
    <script src="assets/js/page.maintenance-categories.js"></script>
    <script src="assets/js/workarea.commons.js"></script>
    <script src="assets/js/shared-effects.js"></script>

    <script src="components/alert-dialog/alert-dialog.js"></script>
    <script src="components/snackbar/snackbar.js"></script>
    <script src="components/confirm-dialog/confirm-dialog.js"></script>

</body>

</html>