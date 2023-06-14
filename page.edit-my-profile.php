<?php
require_once("rootcwd.php");
require_once($rootCwd . "controllers/EditMyProfileController.php");
?>

<body>

    <?php $masterPage->beginWorkarea(Navigation::NavIndex_Profile); //BEGIN WORKAREA LAYOUT 
    ?>

    <!-- MAIN WORKAREA -->
    <div class="main-workarea flex-grow-1 d-flex flex-column h-100 bg-white shadow-2-strong overflow-hidden px-4 py-2">

        <div data-simplebar class=" h-100 overflow-y-auto no-native-scroll">
            <div class="my-2 fs-5">Edit Profile</div>
            <div class="row mx-auto">
                <div class="col-3">
                    <div class="profile-card shadow-2-strong p-3 text-wrap text-center">
                        <div class="p-2 border border-2 rounded-2 d-inline-block mb-2">
                            <img class="avatar-preview" src="<?= loadAvatarSrc() ?>">
                        </div>
                        <div class="change-avatar-button-wrapper">
                            <button type="button" class="btn btn-secondary px-2 py-1" data-mdb-target="#findAvatarModal" data-mdb-toggle="modal">
                                Change Avatar
                                <i class="ms-1 fas fa-pen"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <form action="<?= Tasks::EDIT_PROFILE ?>" method="post" class="frm-edit-profile">
                        <input type="text" name="user-key" class="user-key d-none" value="<?= loadUserKey() ?>">
                        <input type="text" name="avatar" class="avatar d-none" value="<?= loadAvatar() ?>">
                        <div class="p-3 shadow-2-strong mb-3">
                            <h6 class="font-primary-dark">Basic Info</h6>
                            <small class="text-muted">Your name will be used to help other users identify you within the system. Only letters Aa-Zz, dots and dashes are allowed.</small>
                            <div class="d-flex flex-row flex-wrap gap-2 pt-2">
                                <div class="form-outline">
                                    <input type="text" id="firstname" name="firstname" class="form-control" value="<?= loadLastInput('firstname') ?>" required />
                                    <label class="form-label" for="firstname">Firstname</label>
                                </div>
                                <div class="form-outline">
                                    <input type="text" id="middlename" name="middlename" class="form-control" value="<?= loadLastInput("middlename") ?>" required />
                                    <label class="form-label" for="middlename">Middlename</label>
                                </div>
                                <div class="form-outline">
                                    <input type="text" id="lastname" name="lastname" class="form-control" value="<?= loadLastInput("lastname") ?>" required />
                                    <label class="form-label" for="lastname">Lastname</label>
                                </div>
                            </div>
                        </div>
                        <div class="p-3 shadow-2-strong mb-3">
                            <h6 class="font-primary-dark">Account</h6>
                            <small class="text-muted">Your account details will be used to authenticate you. Choose a username that is unique and easy to remember.</small>
                            <div class="d-flex flex-row flex-wrap gap-2 pt-2">
                                <div class="form-outline">
                                    <input type="text" id="username" name="username" class="form-control" value="<?= loadLastInput("username") ?>" required />
                                    <label class="form-label" for="username">Username</label>
                                </div>
                                <div class="form-outline">
                                    <input type="text" id="email" name="email" class="form-control" value="<?= loadLastInput("email") ?>" required />
                                    <label class="form-label" for="email">Email</label>
                                </div>
                            </div>
                        </div>
                        <div class="p-3 shadow-2-strong mb-3">
                            <div class="verification">
                                <h6 class="font-primary-dark">Verification</h6>
                                <small class="text-muted">To ensure the security of your account, we want to make sure it's really you. Please verify your identity by entering your password.</small>
                                <div class="form-outline w-50">
                                    <input type="text" id="password" name="password" class="form-control" />
                                    <label class="form-label" for="password">Password</label>
                                </div>
                            </div>
                            <hr>
                            <div class="error-msg rounded-2 bg-red-light p-2 fsz-14 font-red-dark display-none mb-3"></div>
                            <div class="control-buttons d-flex flex-row align-items-center justify-content-end gap-2">
                                <a role="button" href="<?= Pages::MY_PROFILE ?>" class="btn btn-secondary fw-bold btn-sm btn-cancel">
                                    Cancel
                                </a>
                                <button type="button" class="btn btn-secondary btn-base btn-sm btn-save">
                                    <i class="fas fa-shield-alt me-1"></i>
                                    Save
                                </button>
                            </div>
                        </div>
                    </form>

                </div>
                <div class="col"></div>
            </div>
        </div>

    </div>

    <?php $masterPage->endWorkarea(); //END WORKAREA LAYOUT 
    $masterPage->includeDialogs(true, false, true, true);
    require_once($rootCwd . "includes/embed.avatar-picker.php");
    ?>

    <div class="d-none">
        <textarea class="action-error d-none"><?= getErrorMessage() ?></textarea>
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
    <script src="assets/js/page.action-edit-profile.js"></script>
    <script src="assets/js/shared-effects.js"></script>

</body>

</html>