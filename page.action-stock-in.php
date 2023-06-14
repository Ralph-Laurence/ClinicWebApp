<?php require_once("controllers/StockInController.php"); ?>

<body>

    <?php $masterPage->beginWorkarea(Navigation::NavIndex_Restock); //BEGIN WORKAREA LAYOUT 
    ?>

    <!-- MAIN WORKAREA -->
    <div class="main-workarea flex-grow-1 d-flex flex-column h-100 bg-white shadow-2-strong overflow-hidden px-4 py-2">


        <!-- BREADCRUMB-->
        <div class="breadcrumbs mb-2 w-100 d-flex flex-row align-items-center justify-content-start">
            <img src="assets/images/icons/sidenav-transaction.png" width="20" height="20">
            <div class="fs-6 fw-bold ms-2">Transaction</div>
            <div class="breadcrumb-arrow fsz-12 fas fa-play mx-3"></div>
            <img src="assets/images/icons/sidenav-restock.png" width="20" height="20">
            <div class="ms-2 fw-bold fs-6">Restock</div>
            <div class="breadcrumb-arrow fsz-12 fas fa-play mx-3"></div>
            <img src="assets/images/icn_stockin.png" width="20" height="20">
            <div class="ms-2 fw-bold fs-6">Stock In</div>
        </div>

        <!--SEARCH BARS-->
        <div class="searchbar-wrapper display-none effect-reciever" data-transition-index="0" data-transition="fadein">
            <div class="d-flex flex-row-1 gap-2">
                <div class="left-half me-auto">
                    <div class="control-elements d-flex flex-row gap-2 flex-wrap">
                        <div class="form-outline">
                            <input type="text" class="form-control searchbar" maxlength="32" />
                            <label class="form-label" for="form12">Find Medicines</label>
                        </div>
                        <div class="selectmenu-wrapper">
                            <select class="combo-box">
                                <option value="0">By Name</option>
                                <option value="1">By Code/SKU</option> 
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
                            <button class="btn btn-secondary fw-bold dropdown-toggle btn-sort" type="button" id="options-dropdown-button" data-mdb-toggle="dropdown" aria-expanded="false">
                                Sort
                            </button>
                            <ul class="dropdown-menu shadow-3-strong dropdown-menu-custom-light py-2" aria-labelledby="options-dropdown-button ul-li-pointer">

                                <li class="d-flex align-items-center gap-3 px-3 py-1">
                                    <div class="dropdown-item-icon text-center">
                                        <img src="assets/images/icons/sort-asc.png" width="24" height="24">
                                    </div>
                                    <small class="fw-bold text-uppercase font-base">Sort Ascending</small>
                                </li>
                                <li onclick="sortBy(0, 1)" class="d-flex align-items-center gap-3 px-3 py-1 dropdown-item-custom-light">
                                    <div class="dropdown-item-icon text-center"></div>
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="assets/images/icons/sort-up.png" width="18" height="18">
                                        <div class="fs-6">Code / SKU</div>
                                    </div>
                                </li>
                                <li onclick="sortBy(1, 1)" class="d-flex align-items-center gap-3 px-3 py-1 dropdown-item-custom-light">
                                    <div class="dropdown-item-icon text-center"></div>
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="assets/images/icons/sort-up.png" width="18" height="18">
                                        <div class="fs-6">Brand Name</div>
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
                                <li onclick="sortBy(0, -1)" class="d-flex align-items-center gap-3 px-3 py-1 dropdown-item-custom-light">
                                    <div class="dropdown-item-icon text-center"></div>
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="assets/images/icons/sort-down.png" width="18" height="18">
                                        <div class="fs-6">Code / SKU</div>
                                    </div>
                                </li>
                                <li onclick="sortBy(1, -1)" class="d-flex align-items-center gap-3 px-3 py-1 dropdown-item-custom-light">
                                    <div class="dropdown-item-icon text-center"></div>
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="assets/images/icons/sort-down.png" width="18" height="18">
                                        <div class="fs-6">Brand Name</div>
                                    </div>
                                </li> 
                            </ul>
                        </div>
                        <div class="dropdown actionbar-options">
                            <button class="btn btn-secondary fw-bold dropdown-toggle w-100" type="button" id="options-dropdown-button" data-mdb-toggle="dropdown" aria-expanded="false">
                                Options
                            </button>
                            <ul class="dropdown-menu shadow-3-strong dropdown-menu-custom-light py-2" aria-labelledby="options-dropdown-button ul-li-pointer">

                                <li class="d-flex align-items-center gap-3 px-3 py-1">
                                    <div class="dropdown-item-icon text-center">
                                        <img src="assets/images/icons/dataset-filter.png" width="24" height="24">
                                    </div>
                                    <small class="fw-bold text-uppercase font-base">Filter Dataset</small>
                                </li>
                                <?php createFilterItems(); ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!--DIVIDER-->
        <div class="ribbon-divider my-2"></div>


        <!-- Count Indicators -->
        <div class="row mb-2">
            <div class="col col-count-indicators d-flex gap-2 align-items-center flex-wrap">

                <div class="display-none effect-reciever" data-transition-index="1" data-transition="fadein">
                    <div class="capsule-badge fsz-14 text-white d-flex align-items-center">
                        <div class="capsule-badge-bg rounded-start px-2">Total Medicines</div>
                        <div class="bg-teal rounded-end px-2 capsule-badge-indicator"><?= $totalMedicines ?></div>
                    </div>
                </div>
                <div class="display-none effect-reciever" data-transition-index="2" data-transition="fadein">
                    <div class="capsule-badge fsz-14 text-white d-flex align-items-center">
                        <div class="capsule-badge-bg rounded-start px-2">Critical</div>
                        <div class="bg-warning total-critical rounded-end px-2 capsule-badge-indicator">0</div>
                    </div>
                </div>
                <div class="display-none effect-reciever" data-transition-index="3" data-transition="fadein">
                    <div class="capsule-badge fsz-14 text-white d-flex align-items-center">
                        <div class="capsule-badge-bg rounded-start px-2">Out of stock</div>
                        <div class="bg-red total-soldout rounded-end px-2 capsule-badge-indicator">0</div>
                    </div>
                </div>
                <div class="display-none effect-reciever" data-transition-index="4" data-transition="fadein">
                    <div class="capsule-badge fsz-14 text-white d-flex align-items-center">
                        <div class="capsule-badge-bg rounded-start px-2">Expired</div>
                        <div class="bg-mdb-orange total-expired rounded-end px-2 capsule-badge-indicator">0</div>
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

                        <th scope="col" data-orderable="false" class="fw-bold th-150">
                            <div class="d-flex align-items-center">
                                <span class="label me-auto">Code / SKU</span>
                                <img class="sort-icon display-none" src="" width="16" height="16">
                            </div>
                        </th>
                        <th scope="col" data-orderable="false" class="fw-bold th-280">
                            <div class="d-flex align-items-center">
                                <span class="label me-auto">Medicine / Medical Supply</span>
                                <img class="sort-icon display-none" src="" width="16" height="16">
                            </div>
                        </th>
                        <th scope="col" data-orderable="false" class="fw-bold th-150">Stock</th>
                        <th scope="col" data-orderable="false" class="fw-bold th-100 px-1 text-truncate">
                            <div class="d-flex align-items-center">
                                <span class="label me-auto">Restock Point</span>
                                <!-- <img class="sort-icon display-none" src="" width="16" height="16"> -->
                                <i class="fas fa-info-circle text-primary" data-mdb-toggle="tooltip"
                                data-mdb-placement="left" title="A restock point is the level of stock at which you need to replenish so that you don't run out. It's like a reminder to restock before you run out of medicines."></i>
                            </div>
                        </th>
                        <th scope="col" data-orderable="false" class="fw-bold th-100 text-center">Action</th>
                        <th scope="col" data-orderable="false" class="d-none">ItemKey</th>
                    </tr>
                </thead>
                <tbody class="bg-white dataset-body">
                    <?php bindDataset(); ?>
                </tbody>
                <tfoot class="d-none">
                    <tr> 
                        <th class="search-col-sku"></th>
                        <th class="search-col-name"></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <form action="<?= Tasks::STOCK_IN ?>" method="post" class="frm-restock d-none">
        <input type="text" name="item-key" class="item-key">
        <input type="text" name="qty" class="qty"> 
        <input type="text" name="expiry-date" id="expiry-date">
        <textarea name="item-info" class="item-info"></textarea>
    </form>
    <form action="<?= Pages::STOCK_IN ?>" method="get" class="filter-form d-none">
        <input type="text" name="filter" id="filter" value="">
        <input type="text" name="query" id="query" value="">
    </form>
    <form action="<?= Tasks::DISCARD_ITEM ?>" method="post" class="frm-discard d-none">
        <input type="text" name="item-key" class="item-key">
        <input type="text" name="sender-key" value="<?= setSenderKey() ?>">
    </form>

    <div class="d-none">
        <textarea class="server-response"><?php getSuccessMessage(); ?></textarea>
        <textarea class="totals-counter"><?= getCountersData() ?></textarea>
    </div>
    
    <?php $masterPage->endWorkarea(); //END WORKAREA LAYOUT 

    $masterPage->includeDialogs(true, true, true, false);
    require_once($rootCwd . "includes/embed.category-picker.php");
    includeRestockModal(0);
    ?>

    <!--SCRIPTS-->
    <script src="assets/lib/jquery/jquery-3.6.1.min.js"></script>
    <script src="assets/lib/jquery-ui-1.13.2.custom/jquery-ui.min.js"></script>
    <script src="assets/lib/datatables/datatables.min.js"></script>
    <script src="assets/lib/mdb5/js/mdb.min.js"></script>
    <script src="assets/lib/momentjs/moment-with-locales.js"></script>
    <script src="assets/lib/simplebar/simplebar.min.js"></script>

    <script src="components/alert-dialog/alert-dialog.js"></script>
    <script src="components/confirm-dialog/confirm-dialog.js"></script>
    <script src="components/snackbar/snackbar.js"></script>

    <script src="assets/js/base-ui.js"></script>
    <script src="assets/js/system.js"></script>
    <script src="assets/js/page.transaction.restock.js"></script>
    <script src="assets/js/workarea.commons.js"></script>
    <script src="assets/js/shared-effects.js"></script>

</body>

</html>