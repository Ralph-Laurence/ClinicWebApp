<?php
require_once("rootcwd.php");
require_once($rootCwd . "controllers/UserDetailsController.php");
?>

<body>

    <?php $masterPage->beginWorkarea(Navigation::NavIndex_Users); //BEGIN WORKAREA LAYOUT 
    ?>

    <!-- MAIN WORKAREA -->
    <div class="main-workarea flex-grow-1 d-flex flex-column h-100 bg-white shadow-2-strong overflow-hidden pt-2 px-4 pb-3">

        <!-- BREADCRUMB-->
        <div class="breadcrumbs w-100 d-flex flex-row align-items-center justify-content-start">
            <img src="assets/images/icons/sidenav-view.png" width="20" height="20">
            <div class="fs-6 fw-bold ms-2">View</div>
            <div class="breadcrumb-arrow fsz-12 fas fa-play mx-3"></div>
            <img src="assets/images/icons/sidenav-users.png" width="20" height="20">
            <div class="ms-2 fw-bold fs-6">Users</div>
            <div class="breadcrumb-arrow fsz-12 fas fa-play mx-3"></div>
            <img src="assets/images/icons/bcrumb-details.png" width="20" height="20">
            <div class="ms-2 fw-bold fs-6">Details</div>
        </div>

        <!--DIVIDER-->
        <hr class="hr divider my-2">

        <div class="container-fluid h-100 p-0 overflow-hidden mb-2">
            <div class="row h-100 flex-grow-1">
                <div class="col-4">
                    <div class="p-2 w-100 ">
                        <div class="d-flex align-items-center">
                            <a href="<?= Pages::USERS ?>" role="button" class="btn btn-secondary me-auto px-2 py-1 mb-2">
                                <i class="fas fa-arrow-left me-2"></i>
                                Back
                            </a>
                            <?php bindChangePasswordBtn() ?>
                        </div>
                        <div class="w-100 shadow-2-strong bg-white">
                            <div class="p-2 bg-document w-100">
                                <h6 class="font-base d-inline">User's Information</h6>
                            </div>
                            <div class="bg-white doc-info-card text-center pt-3 pb-2 px-3">
                                <div class="w-100 d-flex mb-2">
                                    <div class="d-inline-flex mx-auto justify-content-center align-items-center border border-3 p-3 rounded-circle">
                                        <img src="<?= getAvatar() ?>" alt="" srcset="">
                                    </div>
                                </div>
                                <div class="text-wrap fs-6 fw-bold">
                                    <?= getName() ?>
                                </div>
                                <div class="text-wrap fs-6 text-primary">
                                    <?php getRole() ?>
                                </div>
                            </div>
                            <hr class="hr my-2">
                            <div class="bg-white doc-info-card px-3 py-2">
                                <div class="fs-6 d-flex">
                                    <div class="me-2" style="width: 24px;">
                                        <i class="fas fa-user-tag"></i>
                                    </div>
                                    <div class="flex-fill text-muted"><?= getUsername() ?></div>
                                </div>
                                <div class="fs-6 d-flex">
                                    <div class="me-2" style="width: 24px;">
                                        <i class="fas fa-envelope"></i>
                                    </div>
                                    <div class="flex-fill text-muted"><?= getEmail() ?></div>
                                </div>
                            </div>
                            <hr class="hr my-2">
                            <div class="bg-white text-center text-muted py-2 px-3">
                                <?= getJoinDate() ?>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="col overflow-hidden h-100">
                    <div class="p-2 w-100 h-100 col-child-wrapper">

                        <div class="w-100 h-100 d-flex flex-column shadow-2-strong bg-white">
                            <div class="p-2 flex-column bg-document w-100">
                                <h6 class="font-base d-inline">Access Permissions</h6>
                            </div>
                            <div class="flex-grow-1 d-flex flex-column h-100 overflow-hidden pb-2">
                                <div data-simplebar class="w-100 h-100 overflow-y-auto no-native-scroll datatable-wrapper display-nonex effect-reciever" data-transition-index="2" data-transition="fadein">
                                    <table class="table table-sm table-striped dataset-table position-relative">
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
                    </div>
                </div>
            </div>

        </div>
    </div>
 
    <textarea class="d-none error-msg"><?= getErrorMessage() ?></textarea>
    <textarea class="d-none success-msg"><?= getSuccessMessage() ?></textarea>
    <?php
    $masterPage->endWorkarea(); //END WORKAREA LAYOUT 
    $masterPage->includeDialogs(true, false, true, false);
    
    bindChangePasswModal();
    ?>

    <!--SCRIPTS-->
    <script src="assets/lib/jquery/jquery-3.6.1.min.js"></script>
    <script src="assets/lib/jquery-ui-1.13.2.custom/jquery-ui.min.js"></script>
    <script src="assets/lib/datatables/datatables.min.js"></script>
    <script src="assets/lib/mdb5/js/mdb.min.js"></script>
    <script src="assets/lib/momentjs/moment-with-locales.js"></script>
    <script src="assets/lib/jquery.nicescroll/jquery.nicescroll.min.js"></script>
    <script src="assets/lib/simplebar/simplebar.min.js"></script>

    <script src="assets/js/nicescroll.js"></script>
    <script src="assets/js/system.js"></script>
    <script src="assets/js/base-ui.js"></script>
    <script src="assets/js/page.view-user-details.js"></script>

    <script src="components/alert-dialog/alert-dialog.js"></script>
    <script src="components/snackbar/snackbar.js"></script>

</body>

</html>