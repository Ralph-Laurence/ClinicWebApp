<?php
require_once("rootcwd.php");
require_once($rootCwd . "controllers/RegisterPatientController.php");

require_once($rootCwd . "models/Patient.php");

use TableFields\PatientFields as Fields;
?>

<body>

    <?php $masterPage->beginWorkarea(Navigation::NavIndex_Register); //BEGIN WORKAREA LAYOUT 
    ?>

    <!-- MAIN WORKAREA -->
    <div class="main-workarea flex-grow-1 d-flex flex-column h-100 bg-white shadow-2-strong overflow-hidden py-2">

        <!-- BREADCRUMB-->
        <div class="breadcrumbs px-4 w-100 d-flex flex-row align-items-center justify-content-start">
            <img src="assets/images/icons/sidenav-transaction.png" width="20" height="20">
            <div class="fs-6 fw-bold ms-2">Transaction</div>
            <div class="breadcrumb-arrow fsz-12 fas fa-play mx-3"></div>
            <img src="assets/images/icons/sidenav-reg-patient.png" width="20" height="20">
            <div class="ms-2 fw-bold fs-6">Register Patient</div>
        </div>

        <!--DIVIDER-->
        <hr class="hr" />

        <div data-simplebar class="w-100 h-100 overflow-y-auto no-native-scroll">
            <form action="<?= Tasks::REGISTER_PATIENT ?>" method="POST" id="register-form" class="px-4 overflow-hidden needs-validation" novalidate>

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
                    <div class="col-3"></div>
                    <div class="col-3">
                        <h6 class="font-base">Contact & Address</h6>
                    </div>
                </div>

                <div class="row">
                    <div class="col">
                        <div class="bg-red-light p-2 rounded-2 mb-3 error-message display-none">
                            <i class="fas fa-exclamation-triangle font-red-dark me-1"></i>
                            <small class="font-red-dark error-label"></small>
                        </div>
                    </div>
                    <div class="col"></div>
                </div>

                <? // Form Fields 
                ?>
                <div class="row">
                    <div class="col-3">

                        <!-- ID NUMBER -->
                        <div class="form-outline mb-3">
                            <input type="text" name="input-idnum" class="form-control input-idnum alphanum" value="<?= loadLastInput(Fields::$idNumber) ?>" required maxlength="64" style="height: 36px; max-height: 36px;" />
                            <label class="form-label" for="input-idnum">ID Number *</label>
                        </div>

                        <!-- FIRSTNAME -->
                        <div class="form-outline mb-3">
                            <input type="text" name="input-fname" class="form-control input-fname alpha" value="<?= loadLastInput(Fields::$firstName) ?>" required maxlength="32" style="height: 36px; max-height: 36px;" />
                            <label class="form-label" for="input-fname">Firstname *</label>
                        </div>
                        <!-- MIDDLENAME -->
                        <div class="form-outline mb-3">
                            <input type="text" name="input-mname" class="form-control input-mname alpha" value="<?= loadLastInput(Fields::$middleName) ?>" required maxlength="32" style="height: 36px; max-height: 36px;" />
                            <label class="form-label" for="input-mname">Middlename *</label>
                            <div class="invalid-feedback">Please choose a username.</div>
                        </div>
                        <!-- LASTNAME -->
                        <div class="form-outline mb-3">
                            <input type="text" name="input-lname" class="form-control input-lname alpha" value="<?= loadLastInput(Fields::$lastName) ?>" required maxlength="32" style="height: 36px; max-height: 36px;" />
                            <label class="form-label" for="input-lname">Lastname *</label>
                        </div>
                    </div>

                    <div class="col-3">
                        <!--BIRTHDAY-->


                        <div class="d-flex align-items-center gap-2">
                            <div class="form-outline mb-3">
                                <input type="text" name="input-birthday" class="form-control input-birthday bg-white" value="<?= loadLastInput(Fields::$birthDay) ?>" readonly required style="height: 36px; max-height: 36px;" />
                                <label class="form-label" for="input-birthday">Birthday *</label>
                            </div>
                            <div class="form-outline mb-3" style="max-width: 80px;">
                                <input type="text" name="input-age" class="form-control bg-white input-age numerics" maxlength="3" value="<?= loadLastInput(Fields::$age) ?>" readonly required data-mdb-toggle="tooltip" title="Age is automatically calculated upon selecting birthday." style="height: 36px; max-height: 36px;">
                                <label class="form-label" for="input-age">Age *</label>
                            </div>
                        </div>

                        <div class="mb-3 d-flex align-items-center h-36">
                            <div class="me-auto fs-6">Gender *</div>
                            <select name="select-gender" id="select-gender" required>
                                <option value="" disabled selected>Select</option>
                                <?php
                                $gender_selectedValue = loadLastInput(Fields::$gender, 0);

                                create_genderOption(GenderTypes::$MALE, $gender_selectedValue);
                                create_genderOption(GenderTypes::$FEMALE, $gender_selectedValue);
                                ?>
                            </select>
                        </div>

                        <!-- PATIENT TYPE -->
                        <div class="mb-3 d-flex align-items-center h-36">
                            <div class="me-auto fs-6">Patient Type *</div>
                            <select name="select-patient-type" id="select-patient-type" required>
                                <option value="" disabled selected>Select</option>
                                <?php
                                $patientType_selectedValue = loadLastInput(Fields::$patientType, 0);

                                create_patientTypeOption(PatientTypes::$STUDENT, $patientType_selectedValue);
                                create_patientTypeOption(PatientTypes::$STAFF, $patientType_selectedValue);
                                create_patientTypeOption(PatientTypes::$TEACHER, $patientType_selectedValue);
                                ?>
                            </select>
                        </div>

                        <div class="row mb-3">
                            <div class="col">
                                <div class="form-outline">
                                    <input type="text" name="input-weight" class="form-control input-weight decimals" value="<?= loadLastInput(Fields::$weight) ?>" maxlength="6">
                                    <label class="form-label" for="input-weight">Weight (Kg)</label>
                                </div>
                            </div>
                            <div class="col ps-0">
                                <div class="form-outline">
                                    <input type="text" name="input-height" class="form-control input-height decimals" value="<?= loadLastInput(Fields::$height) ?>" maxlength="6">
                                    <label class="form-label" for="input-height">Height (cm)</label>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="col-3">
                        <div class="form-outline mb-3">
                            <input type="text" name="input-contact" class="form-control input-contact numerics" maxlength="13" value="<?= loadLastInput(Fields::$contact) ?>">
                            <label class="form-label" for="input-contact">Contact#</label>
                        </div>

                        <div class="form-outline mb-3">
                            <input type="text" name="input-parent-guardian" class="form-control input-parent-guardian" value="<?= loadLastInput(Fields::$parent) ?>">
                            <label class="form-label" for="input-parent-guardian">Parent / Guardian</label>
                        </div>

                    </div>

                    <div class="col-3">
                        <div class="form-outline mb-5">
                            <input type="text" name="input-address" class="form-control input-address" value="<?= loadLastInput(Fields::$address) ?>">
                            <label class="form-label" for="input-address">Address</label>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-3">

                    </div>
                    <div class="col-3"></div>
                    <div class="col"></div>
                </div>

                <div class="row pt-4">
                    <div class="col-3"></div>
                    <div class="col-3"></div>
                    <div class="col">
                        <div class="d-flex flex-row justify-content-end gap-2">
                            <button type="button" class="btn btn-secondary fw-bold btn-reset">Clear</button>
                            <button type="button" class="btn btn-primary btn-base btn-submit">Register</button>
                        </div>
                    </div>
                </div>

            </form>
        </div>

    </div>

    <?php $masterPage->endWorkarea(); //END WORKAREA LAYOUT 
    ?>

    <!--SNACKBAR AND MODAL-->
    <?php

    unset(
        $_SESSION['register-last-inputs'],
    );

    $masterPage->includeDialogs(true, true, true, true);
    ?>
    <textarea class="err-msg d-none"><?= getErrorMessage() ?></textarea>

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
    <script src="assets/js/page.transaction-registration-form.js"></script>

    <script src="components/alert-dialog/alert-dialog.js"></script>
    <script src="components/confirm-dialog/confirm-dialog.js"></script>
    <script src="components/snackbar/snackbar.js"></script>
    <script src="components/toast/toast.js"></script>
</body>

</html>