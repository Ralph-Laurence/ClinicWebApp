<?php

require_once("rootcwd.inc.php");
require_once($cwd . "controllers/BannerController.php");
require_once($cwd . "includes/urls.php");
?>

<!-- BANNER HEADINGS -->
<div class="flex-column headings-banner">
    <!-- <div class="title-headings bg-base">
        TITLE
    </div> -->
    <div class="title-banner py-2 px-4 bg-base d-flex align-items-center text-white w-100">

        <div class="left-content me-auto d-flex align-items-center">
            <img src="assets/images/banner-logo.png" alt="logo" width="45" height="45">
            <div class="ms-2 fs-6 d-flex flex-column lh-2">
                <span class="text-uppercase title-banner-text">Medicine Inventory & Patient Information System</span>
                <div class="d-inline-flex align-items-center gap-2 fst-italic">
                    <span class="fsz-10 font-amber">Pangasinan State University</span>
                    <i class="border border-start border-1 border-secondary banner-text-divider"></i>
                    <small class="fsz-10 text-secondary">Lingayen Campus</small>
                    <i class="border border-start border-1 border-secondary banner-text-divider"></i>
                    <small class="fsz-10 font-bright-blue">Dr. Marciano Cantor Jr. Infirmary</small>
                </div>
            </div>
        </div>

        <div class="right-content d-flex flex-row text-end">

            <!--PROFILE PIC, NOTIFICATIONS-->
            <div class="d-inline-flex align-items-center">
                
                <div class="dropdown me-3">
                    <a role="button" class="dropdown-toggle hidden-arrow" data-mdb-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-bell fa-lg text-white fsz-14"></i>
                        <span class="badge rounded-pill badge-notification notif-badge bg-danger display-none">0</span>
                    </a>
                    <ul class="dropdown-menu shadow-3-strong dropdown-menu-custom-light py-2 notif-queue" aria-labelledby="options-dropdown-button ul-li-pointer" style="width: 300px;">
                        <li class="d-flex align-items-center gap-2 px-3 py-1 mb-2">
                            <div class="dropdown-item-icon text-center">
                                <img src="assets/images/icons/icn-notif.png" width="20" height="20">
                            </div>
                            <small class="fw-bold text-uppercase notif-header font-base">Notifications (0)</small>
                        </li> 
                    </ul>
                </div>

                <div class="mx-auto">
                    <small class="username me-2"><?= UserAuth::getUsername() ?></small>
                </div>
                <div class="dropdown">
                    <a class="dropdown-toggle d-flex align-items-center" id="navbarDropdownMenuAvatar" role="button" data-mdb-toggle="dropdown" aria-expanded="false">

                        <div class="border border-2 border-primary rounded-circle p-1">
                            <img src="assets/images/avatars/<?= $avatar ?>" alt="avatar" width="30" height="30">
                        </div>
                    </a>
                    <div class="dropdown-menu shadow-3-strong dropdown-menu-end profile-dropdown p-3" aria-labelledby="navbarDropdownMenuAvatar">
                        <div class="row">
                            <div class="col-3 d-flex align-items-center justify-content-center">
                                <img src="assets/images/avatars/<?= $avatar ?>" alt="avatar" width="64" height="64">
                            </div>
                            <div class="col text-wrap text-break">
                                <h6 class="text-dark fw-bold"><?= $name ?></h6>
                                <h6 class="text-primary fsz-14"><?= UserAuth::getEmail() ?></h6>
                                <div class="role-display d-flex align-items-center gap-2">
                                    <img src="<?= $roleBadgeIcon ?>" width="20" height="20" />
                                    <span class="badge <?= $roleBadge ?>">
                                        <?= UserRoles::ToDescName(UserAuth::getRole()) ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="divider-separator border border-1 my-3"></div>
                        <div class="row">
                            <div class="col text-start align-middle">
                                <a href="<?= Pages::MY_PROFILE ?>">
                                    <i class="fas fa-user-cog"></i>
                                    <span class="ms-1">My Profile</span>
                                </a>
                            </div>
                            <div class="col text-end align-middle">
                                <a href="<?= Tasks::LOGOUT ?>" class="btn-red text-white p-1 px-2 rounded-5" role="button">
                                    <i class="fas fa-power-off"></i>
                                    <span class="ms-1">Logout</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="d-none">
    <input type="text" class="notifier-href" value="<?= ENV_SITE_ROOT . AsyncActions::INVENTORY_NOTIFIER ?>">
</div>