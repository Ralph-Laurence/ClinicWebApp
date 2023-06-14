<?php
require_once("rootcwd.php");
require_once($rootCwd . "controllers/EditUserController.php");

?>

<body>

    <?php $masterPage->beginWorkarea(Navigation::NavIndex_Users); //BEGIN WORKAREA LAYOUT 
    ?>

    <!-- MAIN WORKAREA -->
    <div class="main-workarea flex-grow-1 d-flex flex-column h-100 bg-white shadow-2-strong overflow-hidden px-4 py-2">

        <!-- BREADCRUMB-->
        <div class="breadcrumbs mb-3 w-100 d-flex flex-row align-items-center justify-content-start">
            <img src="assets/images/icons/sidenav-view.png" width="20" height="20">
            <div class="fs-6 fw-bold ms-2">View</div>
            <div class="breadcrumb-arrow fsz-12 fas fa-play mx-3"></div>
            <img src="assets/images/icons/sidenav-users.png" width="20" height="20">
            <div class="ms-2 fw-bold fs-6">Users</div>
            <div class="breadcrumb-arrow fsz-12 fas fa-play mx-3"></div>
            <img src="assets/images/icons/bcrumb-edit.png" width="20" height="20">
            <div class="ms-2 fw-bold fs-6">Edit User</div>
        </div>
        <!--DIVIDER-->
        <!-- <hr class="hr divider" /> -->
        <!--  -->

        <? // Form Section Headers 
        ?>
        <div class="row flex-grow-1 overflow-hidden">
            <div class="col-8 h-100 flex-grow-1 overflow-hidden d-flex flex-column">
                <div class="mb-3 notes">
                    <div class="d-flex rounded-2 bg-light-blue">
                        <div class="me-2 py-2 px-1 bg-primary rounded-start">
                            <i class="fas fa-info-circle text-white"></i>
                        </div>
                        <div class="note-items p-2 fst-italic">
                            <div class="note-item">
                                <small>
                                    <i class="fas fa-arrow-alt-circle-right font-base me-1"></i>
                                    <span class="fw-bold">Fields marked with asterisk (<span class="fw-bold text-primary">*</span>) are required.</span>
                                </small>
                            </div>
                            <div class="note-item">
                                <small>
                                    <i class="fas fa-arrow-alt-circle-right font-base me-1"></i>
                                    <span class="fw-bold">Fill out all fields with accurate information.</span>
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- CAROUSEL SCROLLER -->
                <div class="mb-2 w-100 bg-document">
                    <div class="w-100 d-flex align-items-center justify-content-start carousel-buttons" style="box-shadow: 0 -3px 0 0 #E2E3ED inset;">
                        <button class="btn btn-link rounded-0 carousel-linkbtn btn-user-bio active"><i class="fas fa-user me-1"></i>User Bio</button>
                        <button class="btn btn-link rounded-0 carousel-linkbtn btn-account"><i class="fas fa-shield-alt me-1"></i>Account</button>
                        <button class="btn btn-link rounded-0 carousel-linkbtn btn-security"><i class="fas fa-lock me-1"></i>Permission</button>
                    </div>
                </div>
                <div data-simplebar class="w-100 h-100 flex-grow-1 overflow-y-auto no-native-scroll">
                    <!-- BEGIN CAROUSEL -->
                    <div id="carousel-main" class="carousel slide h-100 flex-grow-1" data-mdb-ride="carousel" data-mdb-interval="false">
                        <form action="<?= Tasks::EDIT_USER ?>" method="POST" id="register-form" class="overflow-hidden">
                            <input type="text" name="user-key" class="d-none" value="<?= $edit_user_key ?>">
                            <div class="carousel-inner">

                                <!-- USER BIO -->
                                <div class="carousel-item carousel-user-bio active">
                                    <div class="row">
                                        <div class="col-4">
                                            <div class="w-100 h-100 p-2">
                                                <div class="form-outline mb-3">
                                                    <input type="text" name="fname" id="fname" class="form-control apply-validation alpha" value="<?= loadLastInput($userFields->firstName) ?>" maxlength="32" required />
                                                    <label class="form-label" for="fname">Firstname *</label>
                                                </div>
                                                <div class="form-outline mb-3">
                                                    <input type="text" name="mname" id="mname" class="form-control apply-validation alpha" value="<?= loadLastInput($userFields->middleName) ?>" maxlength="32" />
                                                    <label class="form-label" for="mname">Middlename</label>
                                                </div>
                                                <div class="form-outline">
                                                    <input type="text" name="lname" id="lname" class="form-control apply-validation alpha" value="<?= loadLastInput($userFields->lastName) ?>" maxlength="32" required />
                                                    <label class="form-label" for="lname">Lastname *</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="w-100 h-100 p-2">
                                                <div class="form-outline mb-3">
                                                    <input class="form-control bg-white apply-validation alphanum" name="email" id="email" value="<?= loadLastInput($userFields->email) ?>" type="text" maxlength="32" required />
                                                    <label class="form-label" for="email">Email *</label>
                                                </div>
                                                <div class="avatar-picker d-flex gap-2 align-items-center">
                                                    <div class="d-inline-flex p-1 rounded-circle border border-2 border-primary">
                                                        <img class="avatar-preview" src="<?= loadAvatarImage(loadAvatar()) ?>" width="36" height="36">
                                                    </div>
                                                    <button type="button" class="btn btn-secondary fw-bold py-1 px-2" data-mdb-toggle="modal" data-mdb-target="#findAvatarModal">
                                                        Select Avatar
                                                    </button>
                                                </div>
                                                <input type="text" name="avatar-data" class="avatar-data d-none" value="<?= loadAvatar() ?>">
                                            </div>
                                        </div>
                                        <div class="col-1"></div>
                                    </div>
                                </div>

                                <!-- ACCOUNT INFO -->
                                <div class="carousel-item carousel-export">
                                    <div class="row">
                                        <div class="col-4">
                                            <div class="w-100 h-100 p-2">
                                                <div class="form-outline mb-3">
                                                    <input type="text" name="uname" id="username" class="form-control apply-validation alphanum" required maxlength="32" value="<?= loadLastInput($userFields->username) ?>" />
                                                    <label class="form-label" for="username">Username *</label>
                                                </div>
                                                <h6 class="text-muted">Passwords can be changed in User Details</h6>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="w-100 h-100 p-2">
                                                <div class="selectmenu-wrapper w-100 mb-3">
                                                    <select name="user-type" class="w-100 user-type combo-box">
                                                        <option disabled selected>Select User Type *</option>
                                                        <?php bindUserTypes() ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-4"></div>
                                        <div class="col"></div>
                                    </div>
                                </div>

                                <!-- SECURITY INFO -->
                                <div class="carousel-item carousel-security">

                                    <div class="perm-notes display-none">
                                        <div class="d-flex align-items-center w-100 fsz-14" style="height: 28px; max-height: 28px; color: #73510D;">
                                            <div class="ms-0 note-icon bg-amber px-1 rounded-start h-100 d-flex align-items-center">
                                                <i class="fas fa-info-circle"></i>
                                            </div>
                                            <div class="perm-note-content flex-fill px-2 h-100 bg-amber-light d-flex align-items-center pt-1">
                                                <!-- Super Admin accounts always have full access to features. To restrict a user from a specific feature, change the user type. -->
                                            </div>
                                        </div>
                                    </div>
                                    <div class="w-100 h-100 p-2 perm-table-wrapper">
                                        <table class="table table-sm table-striped table-hover dataset-table position-relative">
                                            <thead>
                                                <tr>
                                                    <th class="fw-bold th-180" scope="col">Feature</th>
                                                    <th class="fw-bold th-65 text-center" scope="col" data-mdb-toggle="tooltip" data-mdb-html="true" data-mdb-placement="top" title="<span class='tooltip-title tooltip-title-amber'>View-Only</span><br>Can view any records but can't execute any actions">
                                                        <i class="fas fa-info-circle text-primary fsz-12"></i>
                                                        <span>Read</span>
                                                    </th>
                                                    <th class="fw-bold th-65 text-center" scope="col" data-mdb-toggle="tooltip" data-mdb-html="true" data-mdb-placement="top" title="<span class='tooltip-title tooltip-title-green'>Full Control</span><br>View, create and modify any records">
                                                        <i class="fas fa-info-circle text-primary fsz-12"></i>
                                                        <span>Write</span>
                                                    </th>
                                                    <th class="fw-bold th-65 text-center" scope="col" data-mdb-toggle="tooltip" data-mdb-html="true" data-mdb-placement="top" title="<span class='tooltip-title tooltip-title-red'>No Access</span><br>Prevent a user from accessing a feature<br>">
                                                        <i class="fas fa-info-circle text-primary fsz-12"></i>
                                                        <span>Deny</span>
                                                    </th>
                                                    <th class="th-100"></th>
                                                </tr>
                                            </thead>
                                            <tbody class="dataset-body">
                                                <?php bindPermissionFlags() ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <hr class="hr divider">
                            <div class="row">
                                <div class="col-3">
                                    <button type="button" class="btn btn-secondary fw-bold btn-back display-none">
                                        <i class="fas fa-arrow-left me-1"></i>
                                        Back
                                    </button>
                                </div>
                                <div class="col-3"></div>
                                <div class="col">
                                    <div class="bg-red-light p-2 rounded-2 mb-3 error-message display-none">
                                        <i class="fas fa-exclamation-triangle font-red-dark me-1"></i>
                                        <small class="font-red-dark error-label"></small>
                                    </div>
                                    <div class="d-flex flex-row justify-content-end gap-2">
                                        <button type="button" class="btn btn-secondary fw-bold btn-cancel">Cancel</button>
                                        <button type="button" class="btn btn-primary btn-base btn-save display-none">Save</button>
                                        <button type="button" class="btn btn-primary btn-base btn-next">
                                            Next
                                            <i class="fas fa-arrow-right ms-1"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <textarea name="chmod-json" class="chmod-json d-none"></textarea>
                        </form>
                    </div>
                    <!-- END CAROUSEL -->
                </div>

            </div>
            <div class="col"></div>
        </div>
        <!-- <textarea class="success-msg d-none"><?php //= getSuccessMessage(); 
                                                    ?></textarea> -->
        <textarea class="error-msg d-none"><?= getErrorMessage(); ?></textarea>
        <textarea class="on-cancel d-none"><?= (ENV_SITE_ROOT . Pages::USERS) ?></textarea>
    </div>

    <?php
    $masterPage->endWorkarea(); //END WORKAREA LAYOUT 

    //alertDialog, confirmDialog snackbar, toast
    $masterPage->includeDialogs(true, true, true, true);

    require_once($rootCwd . "includes/embed.avatar-picker.php");
    ?>

    <!--SCRIPTS-->
    <script src="assets/lib/jquery/jquery-3.6.1.min.js"></script>
    <script src="assets/lib/jquery-ui-1.13.2.custom/jquery-ui.min.js"></script>
    <script src="assets/lib/mdb5/js/mdb.min.js"></script>
    <script src="assets/lib/mdb5/js/mdb.form-validation.js"></script>
    <script src="assets/lib/momentjs/moment-with-locales.js"></script>
    <script src="assets/lib/simplebar/simplebar.min.js"></script>

    <script src="assets/js/system.js"></script>
    <script src="assets/js/base-ui.js"></script>
    <script src="assets/js/page.action-edit-user.js"></script>

    <script src="components/alert-dialog/alert-dialog.js"></script>
    <script src="components/confirm-dialog/confirm-dialog.js"></script>
    <script src="components/snackbar/snackbar.js"></script>
    <script src="components/toast/toast.js"></script>
</body>

</html>