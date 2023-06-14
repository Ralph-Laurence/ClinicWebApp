<?php
require_once("rootcwd.php");
require_once($rootCwd . "controllers/MyProfileController.php");
?>

<body>

    <?php $masterPage->beginWorkarea(Navigation::NavIndex_Profile); //BEGIN WORKAREA LAYOUT 
    ?>

    <!-- MAIN WORKAREA -->
    <div class="main-workarea flex-grow-1 d-flex flex-column h-100 bg-white shadow-2-strong overflow-hidden px-4 py-2">

        <div data-simplebar class=" h-100 overflow-y-auto no-native-scroll">
            <div class="my-2 fs-5">My Profile</div>
            <div class="row mx-auto">
                <div class="col-4">
                    <div class="profile-card shadow-2-strong p-3 text-wrap text-center">
                        <div class="p-2 border border-2 rounded-2 d-inline-block mb-2">
                            <img src="<?= setProfile() ?>.png">
                        </div>
                        <h5 class="text-muted"><?= getUsername() ?></h5>
                        <h6 class=""><?= getFullname() ?></h6>
                        <h6 class="text-primary mb-4"><?= getEmail() ?></h6>
                        <a role="button" href="<?= Pages::EDIT_MY_PROFILE ?>" class="btn btn-secondary btn-sm">
                            Edit Info
                            <i class="ms-1 fas fa-pen"></i>
                        </a>
                        <div class="date-joined fsz-14 text-muted mt-4 border-top pt-2">
                            <?= getDateJoined() ?>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="p-3 shadow-2-strong mb-3">
                        <h6 class="font-primary-dark">Security Key</h6>
                        <small class="text-muted">Your security key is an automatically generated text with a combination of letters and numbers which will be used to reset your password. You should not share your key with anyone else.</small>
                        <div class="d-flex flex-row align-items-center gap-2">
                            <div class="form-outline flex-fill">
                                <input type="text" id="sec-key-field" class="form-control font-primary-dark py-0" value="<?= getGuid() ?>" />
                            </div>
                            <?php
                            if (!$revealGuid) {
                                echo <<<BTN
                                <button type="button" class="btn btn-primary p-1" data-mdb-toggle="modal" data-mdb-target="#securityKeyModal" style="width: 25px;">
                                    <i class="fas fa-eye fsz-14"></i>
                                </button>
                                BTN;
                            } else {
                                echo <<<BTN
                                <button type="button" class="btn btn-secondary btn-copy-sec-key p-1" data-mdb-toggle="tooltip" data-mdb-placement="right" title="Copy to clipboard" style="width: 25px;">
                                    <i class="fas fa-copy fsz-14"></i>
                                </button>
                                BTN;
                            }
                            ?>
                        </div>
                        <div class="rounded-2 bg-document p-2 fsz-14 mt-2">
                            <i class="fas fa-info-circle me-1"></i>
                            If you forgot your security key, you can request a new password from your system administrator. Resetting your password also resets your security key.
                        </div>
                    </div>
                    <div class="p-3 shadow-2-strong mb-3">
                        <h6 class="font-primary-dark password-header">Password</h6>
                        <div class="password-form-wrapper display-none">
                            <div class="bg-amber-light font-brown rounded-2 p-2 mb-3">
                                <ul class="list-unstyled my-0 fsz-14">
                                    <li class="mb-1"><i class="me-2 fw-bold">&#x25cf;</i>New password must be different from the old password.</li>
                                    <li class="mb-1"><i class="me-2 fw-bold">&#x25cf;</i>Password length must be at least four characters long.</li>
                                    <li class="mb-1"><i class="me-2 fw-bold">&#x25cf;</i>Spaces are not allowed.</li>
                                </ul>
                            </div>
                            <form action="<?= Tasks::UPDATE_PASSWORD ?>" method="post" class="frm-change-pass">
                                <div class="d-flex flex-column align-items-center justify-content-center mb-4">
                                    <div class="form-outline mb-3 w-75">
                                        <i class="fas fa-lock text-primary trailing"></i>
                                        <input type="password" name="old-password" class="form-control password-fields form-icon-trailing" id="input-old-password" />
                                        <label class="form-label" for="input-old-password">Old password</label>
                                    </div>
                                    <div class="form-outline mb-3 w-75">
                                        <i class="fas password-match-icon trailing"></i>
                                        <input type="password" name="new-password" id="input-new-password" class="form-control password-fields form-icon-trailing" />
                                        <label class="form-label" for="input-new-password">New password</label>
                                    </div>
                                    <div class="form-outline w-75">
                                        <i class="fas password-match-icon trailing"></i>
                                        <input type="password" name="confirm-password" id="input-confirm-password" class="form-control password-fields form-icon-trailing" />
                                        <label class="form-label" for="input-confirm-password">Confirm password</label>
                                    </div>
                                </div>
                                <div class="error-msg bg-red-light p-2 fsz-14 font-red-dark display-none mb-3"></div>
                                <div class="control-buttons d-flex flex-row align-items-center justify-content-end gap-2">
                                    <button type="reset" class="btn btn-secondary fw-bold btn-sm btn-cancel">
                                        Cancel
                                    </button>
                                    <button type="button" class="btn btn-secondary btn-base btn-sm btn-save">
                                        <i class="fas fa-shield-alt me-1"></i>
                                        Save
                                    </button>
                                </div>
                            </form>
                        </div>
                        <button class="btn btn-primary btn-sm btn-change-password">
                            <i class="fas fa-cog me-1"></i>
                            Change Password
                        </button>
                    </div>
                </div>
                <div class="col"></div>
            </div>
        </div>

    </div>

    <?php $masterPage->endWorkarea(); //END WORKAREA LAYOUT 
    $masterPage->includeDialogs(true, false, true, true);
    ?>

    <div class="modal fade" id="securityKeyModal" tabindex="-1" aria-labelledby="securityKeyModalLabel" aria-hidden="true" data-mdb-backdrop="static">
        <div class="modal-dialog" style="width: 350px; max-width: 350px;">
            <div class="modal-content mt-4">
                <div class="modal-header py-2 px-3">
                    <h6 class="modal-title d-flex align-items-center gap-2" id="securityKeyModalLabel">
                        <img src="assets/images/icons/icn-sec-key.png" width="20" height="20">
                        Reveal Security Key
                    </h6>
                </div>

                <div class="modal-body">
                    <div class="bg-document p-2 rounded-2 fsz-14 mb-2">
                        To ensure the security of your account, we want to make sure it's really you. Please verify your identity by entering your password.
                    </div>
                    <div class="form-outline mb-2">
                        <input type="password" id="input-password" class="form-control" />
                        <label class="form-label" for="form1">Password</label>
                    </div>
                    <div class="bg-red-light font-red-dark p-1 fsz-14 rounded-2 error-box display-none">Please enter your password!</div>
                </div>

                <div class="modal-footer py-2">
                    <button class="btn btn-secondary btn-cancel" type="button" data-mdb-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary btn-base btn-ok" type="button">OK</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="re_loginModal" tabindex="-1" aria-labelledby="re_loginModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header py-2">
                    <h6 class="modal-title font-primary-dark" id="re_loginModalLabel">
                        <i class="fas fa-cog me-1"></i>
                        Edit Profile
                    </h6>
                </div>
                <div class="modal-body">
                Your profile has been successfully updated. Please log in again for the changes to take effect.
                <textarea class="d-none edit-success-key"><?= getEditSuccessKey() ?></textarea>
                </div>
                <div class="modal-footer">
                    <a class="btn btn-secondary fw-bold" href="<?= Tasks::LOGOUT ?>">
                        OK, I understand
                    </a>
                </div>
            </div>
        </div>
    </div>

    <form action="<?= Tasks::REVEAL_GUID ?>" method="post" class="frm-see-guid d-none">
        <input type="text" name="see-guid-password" id="see-guid-password">
        <input type="text" name="profile-key" value="<?= getProfileKey() ?>">
    </form>
    <form action="<?= Tasks::EDIT_PROFILE ?>" method="post" class="frm-edit-profile d-none">
        <input type="text" name="edit-password" id="edit-password">
        <input type="text" name="profile-key" value="<?= getProfileKey() ?>">
    </form>
    <div class="d-none">
        <textarea class="action-error d-none"><?= getErrorMessage() ?></textarea>
        <textarea class="action-success d-none"><?= getSuccessMessage() ?></textarea>
    </div>

    <!--SCRIPTS-->
    <script src="assets/lib/jquery/jquery-3.6.1.min.js"></script>
    <script src="assets/lib/jquery-ui-1.13.2.custom/jquery-ui.min.js"></script>
    <script src="assets/lib/datatables/datatables.min.js"></script>
    <script src="assets/lib/mdb5/js/mdb.min.js"></script>
    <script src="assets/lib/momentjs/moment-with-locales.js"></script>
    <script src="assets/lib/simplebar/simplebar.min.js"></script>

    <script src="components/alert-dialog/alert-dialog.js"></script>
    <script src="components/snackbar/snackbar.js"></script>
    <script src="components/toast/toast.js"></script>

    <script src="assets/js/base-ui.js"></script>
    <script src="assets/js/system.js"></script>
    <script src="assets/js/page.my-profile.js"></script>
    <script src="assets/js/shared-effects.js"></script>

</body>

</html>