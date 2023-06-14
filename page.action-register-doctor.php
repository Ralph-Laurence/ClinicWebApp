<?php
require_once("rootcwd.php");
require_once($rootCwd . "controllers/RegisterDoctorController.php");

?>

<body>

    <?php $masterPage->beginWorkarea(Navigation::NavIndex_Doctors); //BEGIN WORKAREA LAYOUT 
    ?>

    <!-- MAIN WORKAREA -->
    <div class="main-workarea flex-grow-1 d-flex flex-column h-100 bg-white shadow-2-strong overflow-hidden px-4 py-2">

        <!-- BREADCRUMB-->
        <div class="breadcrumbs w-100 d-flex flex-row align-items-center justify-content-start">
            <img src="assets/images/icons/sidenav-view.png" width="20" height="20">
            <div class="fs-6 fw-bold ms-2">View</div>
            <div class="breadcrumb-arrow fsz-12 fas fa-play mx-3"></div>
            <img src="assets/images/icons/sidenav-doctor.png" width="20" height="20">
            <div class="ms-2 fw-bold fs-6">Doctors</div>
            <div class="breadcrumb-arrow fsz-12 fas fa-play mx-3"></div>
            <img src="assets/images/icons/bcrumb-reg-doc.png" width="20" height="20">
            <div class="ms-2 fw-bold fs-6">Register Doctor</div>
        </div>
        <!--DIVIDER-->
        <hr class="hr" />
        <div data-simplebar class="w-100 h-100 overflow-y-auto no-native-scroll">

            <form action="<?= Tasks::REGISTER_DOCTOR ?>" method="POST" id="register-form" class="overflow-hidden">
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
                                <span class="ms-1">
                                    If a required field does not apply to you, enter "<span class="fw-bold text-primary">N/A</span>"
                                    or zero "<span class="fw-bold text-primary">0</span>" for numeric fields.
                                </span>
                            </small>
                        </div>
                        <div class="note-item">
                            <small>
                                <i class="fas fa-arrow-alt-circle-right font-base me-1"></i>
                                <span class="fw-bold">
                                    Please fill out all fields with valid and accurate information.
                                </span>
                                <span class="ms-1">Double check all entries before submitting.</span>
                            </small>
                        </div>
                    </div>
                </div>

                <? // Form Section Headers 
                ?>
                <div class="row mb-2">
                    <div class="col-3">
                        <h6 class="font-base">Personal Information</h6>
                    </div>
                    <div class="col-3">
                        <h6 class="font-base">Profession</h6>
                    </div>
                    <div class="col-3">
                        <h6 class="font-base">Contact & Address</h6>
                    </div>
                </div>

                <? // Form Fields 
                ?>
                <div class="row">
                    <div class="col-3">

                        <div class="form-outline mb-3">
                            <input type="text" name="fname" id="fname" class="form-control alpha" value="<?= loadName('f') ?>" maxlength="32" required />
                            <label class="form-label" for="fname">Firstname *</label>
                        </div>
                        <div class="form-outline mb-3">
                            <input type="text" name="mname" id="mname" class="form-control alpha" value="<?= loadName('m') ?>" maxlength="32" required />
                            <label class="form-label" for="mname">Middlename *</label>
                        </div>
                        <div class="form-outline mb-3">
                            <input type="text" name="lname" id="lname" class="form-control alpha" value="<?= loadName('l') ?>" maxlength="32" required />
                            <label class="form-label" for="lname">Lastname *</label>
                        </div>

                    </div>

                    <div class="col-3">

                        <div class="selectmenu-wrapper w-100 h-36 mb-3">
                            <select name="spec" id="spec-selectmenu" class="w-100 spec-option">
                                <option disabled selected>Specialization *</option>
                                <?php bindDoctorSpecs()
                                ?>
                            </select>
                        </div>
                        <div class="selectmenu-wrapper h-36 w-100 mb-3">
                            <select name="degree" id="degree-selectmenu" class="w-100 degree-option">
                                <option selected disabled>Degree (Optional)</option>
                                <?php bindDoctorDegrees()
                                ?>
                            </select>
                        </div>
                        <div class="form-outline">
                            <input class="form-control bg-white numeric" name="regnum" id="regnum" 
                            value="<?= loadRegNum() ?>" type="text" maxlength="32" required />
                            <label class="form-label" for="regnum">Registration No. *</label>
                        </div>
                    </div>

                    <div class="col-3">
                        <div class="form-outline">
                            <input type="text" name="contact" id="contact" class="form-control numeric" required maxlength="16" 
                            value="<?= loadContact() ?>"/>
                            <label class="form-label" for="contact">Phone# *</label>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-outline">
                            <input type="text" name="address" id="address" class="form-control address" maxlength="64" 
                            value="<?= loadAddress() ?>"/>
                            <label class="form-label" for="address">Address (Optional)</label>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-3"></div>
                    <div class="col-3"></div>
                    <div class="col pt-4">
                        <div class="bg-red-light p-2 rounded-2 mb-3 error-message display-none">
                            <i class="fas fa-exclamation-triangle font-red-dark me-1"></i>
                            <small class="font-red-dark error-label"></small>
                        </div>
                        <div class="d-flex flex-row justify-content-end gap-2">
                            <button type="button" class="btn btn-secondary fw-bold btn-reset">Clear</button>
                            <button type="button" class="btn btn-primary btn-base btn-save">Register</button>
                        </div>
                    </div>
                </div>
                <input type="text" name="specLabel" class="specLabel d-none" value="<?= loadSpec() ?>">
                <input type="text" name="degreeLabel" class="degreeLabel d-none" value="<?= loadDegree() ?>">
            </form>
        </div>
        <textarea class="success-msg d-none"><?= getSuccessMessage(); ?></textarea>
        <textarea class="error-msg d-none"><?= getErrorMessage(); ?></textarea>
    </div>

    <?php 
        $masterPage->endWorkarea(); //END WORKAREA LAYOUT 
        $masterPage->includeDialogs(true, true, true, true);
    ?>
 
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
    <script src="assets/js/page.view-doctors-register.js"></script>

    <script src="components/alert-dialog/alert-dialog.js"></script>
    <script src="components/confirm-dialog/confirm-dialog.js"></script>
    <script src="components/snackbar/snackbar.js"></script>
    <script src="components/toast/toast.js"></script>
</body>

</html>