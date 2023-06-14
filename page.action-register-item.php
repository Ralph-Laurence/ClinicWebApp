<?php
require_once("rootcwd.php");
require_once($rootCwd . "controllers/RegisterItemController.php");
?>

<body>

    <?php $masterPage->beginWorkarea(Navigation::NavIndex_Stocks); //BEGIN WORKAREA LAYOUT 
    ?>

    <!-- MAIN WORKAREA -->
    <div class="main-workarea flex-grow-1 d-flex flex-column h-100 bg-white shadow-2-strong overflow-hidden px-2 py-2">

        <!-- BREADCRUMB-->
        <div class="breadcrumbs px-2 w-100 d-flex flex-row align-items-center justify-content-start">
            <img src="assets/images/icons/sidenav-view.png" width="20" height="20">
            <div class="fs-6 fw-bold ms-2">View</div>
            <div class="breadcrumb-arrow fsz-12 fas fa-play mx-3"></div>
            <img src="assets/images/icons/sidenav-inventory.png" width="20" height="20">
            <div class="ms-2 fw-bold fs-6">Medicine Inventory</div>
            <div class="breadcrumb-arrow fsz-12 fas fa-play mx-3"></div>
            <img src="assets/images/icons/icn-add-medicine.png" width="20" height="20">
            <div class="ms-2 fw-bold fs-6">Add New Item / Med. Supply</div>
        </div>
        <!--DIVIDER-->
        <hr class="hr divider mx-2 my-2" />

        <div data-simplebar class="w-100 h-100 overflow-y-auto no-native-scroll">
            <form action="<?= Tasks::REGISTER_ITEM ?>" enctype="multipart/form-data" method="POST" id="register-form" class="container overflow-hidden needs-validation" novalidate>

                <div class="row">
                    <div class="col-8">
                        <div class="mb-3 d-flex rounded-2 bg-light-blue">
                            <div class="me-2 py-2 px-1 bg-primary rounded-start">
                                <i class="fas fa-info-circle text-white"></i>
                            </div>
                            <div class="note-items p-2 fst-italic">
                                <div class="note-item">
                                    <small>
                                        <i class="fas fa-arrow-alt-circle-right font-base me-1"></i>
                                        <span class="fw-bold">
                                            Fields with asterisk (<span class="fw-bold text-primary">*</span>) are required.
                                        </span>
                                    </small>
                                </div>
                                <div class="note-item">
                                    <small>
                                        <i class="fas fa-arrow-alt-circle-right font-base me-1"></i>
                                        <span class="fw-bold">Avoid overloading item names with more information than absolutely necessary.</span>
                                    </small>
                                </div>
                                <div class="note-item">
                                    <small>
                                        <i class="fas fa-arrow-alt-circle-right font-base me-1"></i>
                                        <span class="fw-bold">Information such as supplier, category etc. should not be included in the item name.</span>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-4"></div>
                </div>

                <div class="row my-1">
                    <div class="col">
                        <div class="fsz-18">Item Details</div>
                    </div>
                </div>
                <hr class="hr my-2">

                <div class="row" id="error-box">
                    <div class="col-8">
                        <div class="bg-red-light p-2 rounded-2 mb-3 error-message display-none">
                            <i class="fas fa-exclamation-triangle font-red-dark me-1"></i>
                            <small class="font-red-dark error-label"></small>
                        </div>
                    </div>
                    <div class="col-4"></div>
                </div>

                <div class="row mb-2">
                    <div class="col">
                        <div class="d-flex h-100 align-items-center">
                            <div class="file-upload">
                                <label class="form-label text-primary" for="item-image">Item Image (Optional)</label>
                                <input type="file" class="form-control mb-2" id="item-image" name="item-image" accept=".jpg, .jpeg, .png" value="" />
                                <div class="fst-italic fsz-14 text-muted">For best results, the image should have the SAME width and height.</div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <img src="assets/images/icons/icn-icon.png" class="item-image-preview border mb-2 border-1 rounded-2" width="128" height="128">
                    </div>
                    <div class="col"></div>
                </div>

                <div class="row">
                    <div class="col p-2">
                        <div class="form-outline mb-3">
                            <input type="text" id="item-name" name="item-name" class="form-control" value="<?= loadItemName() ?>" />
                            <label class="form-label" for="item-name">Name*</label>
                        </div>
                        <div class="form-outline mb-3">
                            <input type="text" id="item-code" name="item-code" class="form-control" value="<?= loadItemCode() ?>" />
                            <label class="form-label" for="item-code">Code / SKU*</label>
                        </div>
                        <div class="input-group mb-3">
                            <span class="input-group-text input-group-text-bg">Opening Stock*</span>
                            <input type="text" class="form-control" name="opening-stock" id="opening-stock" placeholder="Amount" value="<?= loadStock() ?>" />
                            <span class="input-group-text" data-mdb-toggle="tooltip" data-mdb-html="true" data-mdb-placement="top" title="<span class='tooltip-title tooltip-title-amber'>Opening Stock</span><br>The stock available for use right after you add this item">
                                <i class="fas fa-question-circle"></i>
                            </span>
                        </div>
                        <div class="input-group mb-3">
                            <span class="input-group-text input-group-text-bg">Restock Point*</span>
                            <input type="text" class="form-control" name="reserve-stock" id="reserve-stock" placeholder="Amount" value="<?= loadReserved() ?>" />
                            <span class="input-group-text" data-mdb-toggle="tooltip" data-mdb-html="true" data-mdb-placement="top" title="<span class='tooltip-title tooltip-title-amber'>Restock Point</span><br>You must replenish the stock when it reaches this point. Stocks below this point will be marked as &quot;Critical&quot;">
                                <i class="fas fa-question-circle"></i>
                            </span>
                        </div>
                    </div>
                    <div class="col p-2">
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" name="category-label" id="category-label" placeholder="Category* (Type to add)" />
                            <span class="input-group-text" data-mdb-toggle="tooltip" title="Category Icon" data-mdb-placement="top">
                                <img id="category-icon" src="assets/images/icons/icn-icon.png" width="16" height="16">
                            </span>
                            <button class="btn btn-secondary fw-bold" type="button" id="button-addon2" data-mdb-toggle="modal" data-mdb-target="#findCategoryModal">
                                Select
                            </button>
                        </div>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" name="units-label" id="units-label" placeholder="Units* (Type to add)" />
                            <span class="input-group-text" data-mdb-toggle="tooltip" data-mdb-html="true" data-mdb-placement="top" title="<span class='tooltip-title tooltip-title-amber'>Unit Measures</span><br>The item will be measured in terms of this unit<br>(ex: Box, Kg, Pack, Dozen)">
                                <i class="fas fa-question-circle"></i>
                            </span>
                            <button class="btn btn-secondary fw-bold" type="button" id="button-addon2" data-mdb-toggle="modal" data-mdb-target="#findUnitsModal">
                                Select
                            </button>
                        </div>

                        <div class="d-flex align-items-center gap-3">
                            <div class="form-outline me-auto mb-3 flex-fill">
                                <input type="text" name="expiry-date" class="form-control expiry-date bg-white" value="<?php //= loadLastInput(Fields::$birthDay) ?>" readonly required />
                                <label class="form-label" for="expiry-date">Expiry Date *</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" value="" name="no-expiry" id="chk-expiry" />
                                <label class="form-check-label fsz-14" for="chk-expiry">No Expiry</label>
                            </div>
                        </div>
                    </div>
                    <div class="col p-2">

                    </div>
                </div>
                <hr class="hr my-2">
                <div class="row my-1">
                    <div class="col">
                        <div class="text-muted">Other Details (Optional)</div>
                    </div>
                </div>
                <div class="row">
                    <div class="col p-2">
                        <div class="form-outline mb-3">
                            <textarea class="form-control" id="description" name="description" rows="4" style="min-height: 100px; height: 100px; max-height: 100px;" data-mdb-showcounter="true" maxlength="320"><?= loadDescription() ?></textarea>
                            <label class="form-label" for="description">Short notes about this item</label>
                            <div class="form-helper"></div>
                        </div>
                    </div>
                    <div class="col p-2">
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" name="supplier-label" id="supplier-label" placeholder="Supplier (Type to add)" />
                            <button class="btn btn-secondary fw-bold" type="button" id="button-addon2" data-mdb-toggle="modal" data-mdb-target="#findSupplierModal">
                                Select
                            </button>
                        </div>
                    </div>
                    <div class="col p-2"></div>
                </div>
                <hr class="hr my-2">
                <div class="row">
                    <div class="col p-2"></div>
                    <div class="col p-2 text-end">
                        <button type="button" class="btn btn-secondary fw-bold me-1 btn-cancel">Cancel</button>
                        <!-- <button type="button" class="btn btn-secondary fw-bold me-1 btn-reset">Reset</button> -->
                        <button type="button" class="btn btn-secondary btn-base btn-save">Save</button>
                    </div>
                    <div class="col p-2"></div>
                </div>
                <div class="d-none">
                    <input type="text" name="units-key" id="units-key">
                    <input type="text" name="category-key" id="category-key">
                    <input type="text" name="supplier-key" id="supplier-key">
                    <input type="text" name="icn-category" id="icn-category">
                    <textarea class="d-none data-last-input"><?= loadSpecialInputs() ?></textarea>
                </div>
            </form>
        </div>

    </div>

    <?php
    $masterPage->endWorkarea(); //END WORKAREA LAYOUT 
    $masterPage->includeDialogs(true, true, false, false);

    require_once($rootCwd . "includes/embed.category-picker.php");
    require_once($rootCwd . "includes/embed.units-picker.php");
    require_once($rootCwd . "includes/embed.supplier-picker.php");
    ?>

    <textarea class="err-msg d-none"><?= getErrorMessage() ?></textarea>
    <textarea class="goback d-none"><?= Pages::MEDICINE_INVENTORY ?></textarea>

    <!--SCRIPTS-->
    <script src="assets/lib/jquery/jquery-3.6.1.min.js"></script>
    <script src="assets/lib/jquery-ui-1.13.2.custom/jquery-ui.min.js"></script>
    <script src="assets/lib/mdb5/js/mdb.min.js"></script>
    <script src="assets/lib/mdb5/js/mdb.form-validation.js"></script>
    <script src="assets/lib/momentjs/moment-with-locales.js"></script>
    <script src="assets/lib/jquery.nicescroll/jquery.nicescroll.min.js"></script>
    <script src="assets/lib/simplebar/simplebar.min.js"></script>

    <script src="assets/js/nicescroll.js"></script>
    <script src="assets/js/system.js"></script>
    <script src="assets/js/base-ui.js"></script>
    <script src="assets/js/page.action-register-item.js"></script>

    <script src="components/alert-dialog/alert-dialog.js"></script>
    <script src="components/confirm-dialog/confirm-dialog.js"></script>
</body>

</html>