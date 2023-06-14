<?php
require_once("rootcwd.php");
require_once($rootCwd . "controllers/EditSupplierController.php");
?>

<body>

    <?php $masterPage->beginWorkarea(Navigation::NavIndex_Suppliers); //BEGIN WORKAREA LAYOUT 
    ?>

    <!-- MAIN WORKAREA -->
    <div class="main-workarea flex-grow-1 d-flex flex-column h-100 bg-white shadow-2-strong overflow-hidden px-2 py-2">

        <!-- BREADCRUMB-->
        <div class="breadcrumbs px-2 w-100 d-flex flex-row align-items-center justify-content-start">
            <img src="assets/images/icons/sidenav-view.png" width="20" height="20">
            <div class="fs-6 fw-bold ms-2">View</div>
            <div class="breadcrumb-arrow fsz-12 fas fa-play mx-3"></div>
            <img src="assets/images/icons/sidenav-supplier.png" width="20" height="20">
            <div class="ms-2 fw-bold fs-6">Suppliers</div>
            <div class="breadcrumb-arrow fsz-12 fas fa-play mx-3"></div>
            <img src="assets/images/icons/bcrumb-edit.png" width="20" height="20">
            <div class="ms-2 fw-bold fs-6">Edit Supplier</div>
        </div>
        <!--DIVIDER-->
        <hr class="hr divider mx-2 my-2" />

        <div data-simplebar class="w-100 h-100 overflow-y-auto no-native-scroll">
            <form action="<?= Tasks::EDIT_SUPPLIER ?>" method="POST" id="register-form" class="container overflow-hidden needs-validation" novalidate>

                <!-- NOTES -->
                <div class="row">
                    <div class="col-7">
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
                                        <span class="fw-bold">
                                            Please fill out all fields with valid and accurate information.
                                        </span>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-4"></div>
                </div>
                <!-- FORM HEADERS -->
                <div class="row mb-2">
                    <div class="col-4">
                        <h6 class="font-base">Supplier Information</h6>
                    </div>
                    <div class="col-3">
                        <h6 class="font-base">Contact Details</h6>
                    </div>
                </div>

                <div class="row">
                    <div class="col-7">
                        <div class="bg-red-light p-2 rounded-2 mb-3 error-message display-none">
                            <i class="fas fa-exclamation-triangle font-red-dark me-1"></i>
                            <small class="font-red-dark error-label"></small>
                        </div>
                    </div>
                    <div class="col-4"></div>
                </div>

                <!-- FORM FIELDS -->
                <div class="row">
                    <div class="col-4">

                        <!-- NAME -->
                        <div class="form-outline mb-3">
                            <input type="text" name="supname" class="form-control text-primary input-supname alphanum" value="<?= loadLastInput($fields->name) ?>" required maxlength="32" />
                            <label class="form-label" for="input-supname">Supplier Name *</label>
                        </div>
                        <!-- ADDRESS -->
                        <div class="form-outline mb-3">
                            <input type="text" name="address" class="form-control input-address" value="<?= loadLastInput($fields->address) ?>">
                            <label class="form-label" for="input-address">Address</label>
                        </div>
                    </div>

                    <div class="col-3">
                        <div class="form-outline mb-3">
                            <input type="text" name="contact" class="form-control text-primary input-contact numerics" maxlength="13" value="<?= loadLastInput($fields->contact) ?>">
                            <label class="form-label" for="input-contact">Contact# *</label>
                        </div>

                        <div class="form-outline mb-3">
                            <input type="text" name="email" class="form-control input-email" value="<?= loadLastInput($fields->email) ?>">
                            <label class="form-label" for="input-email">Email</label>
                        </div>

                    </div>

                    <div class="col-3">

                    </div>
                </div>

                <div class="row">
                    <div class="col-4">
                        <!-- SHORT DETAILS -->
                        <div class="form-outline">
                            <textarea class="form-control text-muted" id="description" name="description" rows="4" 
                            style="min-height: 100px; height: 150px; max-height: 150px;"
                            data-mdb-showcounter="true" maxlength="320"><?= loadLastInput($fields->description) ?></textarea>
                            <label class="form-label" for="description">Short Description (Optional)</label>
                            <div class="form-helper"></div>
                        </div>
                    </div>
                    <div class="col-3"></div>
                    <div class="col-3"></div>
                </div>

                <div class="row"> 
                    <div class="col-4"></div>
                    <div class="col-3">
                        <div class="d-flex flex-row justify-content-end gap-2">
                            <button type="button" class="btn btn-secondary fw-bold btn-cancel">Cancel</button>
                            <button type="button" class="btn btn-primary btn-base btn-submit">Save</button>
                        </div>
                    </div>
                    <div class="col-3"></div>
                </div>
                <input type="text" name="edit-key" class="d-none" value="<?= $supplierKey ?>">
            </form>
        </div>

    </div>

    <?php 
    $masterPage->endWorkarea(); //END WORKAREA LAYOUT 
    $masterPage->includeDialogs(true, true, false, false);
    ?>
 
    <textarea class="err-msg d-none"><?= getErrorMessage() ?></textarea>
    <textarea class="goback d-none"><?= Pages::SUPPLIERS ?></textarea>

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
    <script src="assets/js/page.action-register-supplier.js"></script>

    <script src="components/alert-dialog/alert-dialog.js"></script> 
    <script src="components/confirm-dialog/confirm-dialog.js"></script>  
</body>

</html>