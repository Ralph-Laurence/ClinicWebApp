<?php
require_once("rootcwd.php");
require_once($rootCwd . "controllers/SettingsController.php");
?>

<body>

    <?php $masterPage->beginWorkarea(Navigation::NavIndex_Settings); //BEGIN WORKAREA LAYOUT 
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
            <img src="assets/images/icons/sidenav-settings.png" width="20" height="20">
            <div class="ms-2 fw-bold fs-6">Settings</div>
        </div>

        <!--DIVIDER-->
        <hr class="hr divider my-2">

        <div data-simplebar class="w-100 h-100 pe-2 overflow-y-auto no-native-scroll display-none effect-reciever" data-transition-index="2" data-transition="fadein">
            <div class="w-75">
                <div class="setting-header mb-2">
                    <div class="d-flex align-items-center flex-row gap-2 mb-2">
                        <img src="assets/images/icons/icn-general-setting.png" width="24" height="24">
                        <div class="text-uppercase fsz-14 fw-bold text-primary mt-1">General Settings</div>
                    </div>
                    <small class="text-muted">Adjust the system's behavior to better suit your needs.</small>
                </div>
                <div class="general-settings-group">
                    <ul class="list-group list-group-light">

                        <li class="list-group-item">
                            <div class="d-flex gap-2">
                                <div class="icon-wrapper">
                                    <i class="fas fa-cog fsz-12"></i>
                                </div>
                                <div class="settings-item-label">
                                    <div class="fs-6">Record viewing year</div>
                                    <small class="text-muted fst-italic fsz-14">(Only applies to Checkup and Waste records)</small>
                                </div>
                                <div class="ms-auto me-1">
                                    <form action="<?= Tasks::CHANGE_REC_YEAR ?>" method="POST" class="frm-change-year">
                                        <input type="text" name="new-record-year" class="new-rec-year d-none">
                                        <div class="selectmenu-wrapper">
                                            <select class="combo-box" id="record-year" name="record-year">
                                                <?php prefillYears() ?>
                                            </select>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="d-flex gap-2">
                                <div class="icon-wrapper">
                                    <i class="fas fa-cog fsz-12"></i>
                                </div>
                                <div class="settings-item-label">
                                    <div class="fs-6">Lock editing after max days</div>
                                    <small class="text-muted fst-italic fsz-14">(Only applies to Checkup records)</small>
                                </div>
                                <div class="ms-auto me-1">
                                    <form action="<?= Tasks::CHANGE_MAX_DAYS ?>" method="POST" class="frm-change-max-days">
                                        <input type="text" name="new-max-days" class="new-max-days d-none">
                                        <div class="selectmenu-wrapper">
                                            <select class="combo-box" id="max-days" name="max-days">
                                                <?php prefillEditMaxDays() ?>
                                            </select>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="d-flex gap-2">
                                <div class="icon-wrapper">
                                    <i class="fas fa-cog fsz-12"></i>
                                </div>
                                <div class="settings-item-label">
                                    <div class="fs-6">Checkup form action on complete</div>
                                    <small class="text-muted fst-italic fsz-14">(Choose what happens after you create a new checkup record)</small>
                                </div>
                                <div class="ms-auto me-1">
                                    <form action="<?= Tasks::CHANGE_CHECKUP_ACTION ?>" method="POST" class="frm-change-form-action">
                                        <input type="text" name="new-form-action" class="new-form-action d-none">
                                        <div class="selectmenu-wrapper">
                                            <select class="combo-box" id="checkup-form-action" name="checkup-form-action">
                                                <?php prefillFormActions() ?>
                                            </select>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </li>
                        <li></li>
                    </ul>
                </div>

                <div class="setting-header mb-2">
                    <div class="d-flex align-items-center flex-row gap-2 mb-2">
                        <img src="assets/images/icons/icn-sys-info.png" width="24" height="24">
                        <div class="text-uppercase fsz-14 fw-bold text-primary mt-1">System Information</div>
                    </div>
                    <small class="text-muted">These are the default parameters used by the system.</small>
                </div>
                <div class="general-settings-group">
                    <ul class="list-group list-group-light">

                        <li class="list-group-item">
                            <div class="d-flex gap-2">

                                <div class="settings-item-label">
                                    <div class="fs-6">
                                        <i class="fas fa-info-circle fsz-12 me-1"></i>
                                        App Version
                                    </div>
                                </div>
                                <div class="ms-auto me-1">
                                    <div class="fs-6 text-primary"><?= getAppVersion() ?></div>
                                </div>
                            </div>
                        </li>

                        <li></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!-- END MAIN WORKAREA -->
    <form class="frm-session-var">
        <textarea type="text" class="d-none success-message"><?= getSuccessMessage() ?></textarea>
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
    <script src="assets/lib/simplebar/simplebar.min.js"></script>

    <script src="assets/js/system.js"></script>
    <script src="assets/js/base-ui.js"></script>
    <script src="assets/js/page.maintenance-settings.js"></script>
    <script src="assets/js/workarea.commons.js"></script>
    <script src="assets/js/shared-effects.js"></script>

    <script src="components/alert-dialog/alert-dialog.js"></script>
    <script src="components/snackbar/snackbar.js"></script>

</body>

</html>