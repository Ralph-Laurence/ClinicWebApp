<?php

require_once("rootcwd.inc.php");

require_once($cwd . "includes/urls.php");
?>

<div class="title-banner py-2 px-4 bg-base d-flex align-items-center text-white sticky-top position-absolute w-100">

    <div class="left-content me-auto d-flex align-items-center">
        <img src="assets/images/logo.png" alt="logo" width="42" height="42">
        <div class="ms-2 fs-6 d-flex flex-column lh-2">
            <span class="text-uppercase title-banner-text">Clinic Inventory & Information System</span>
            <div class="d-inline-flex align-items-center gap-2">
                <span class="fst-italic fsz-10 font-amber">Pangasinan State University</span>
                <i class="border border-start border-1 border-secondary banner-text-divider"></i>
                <small class="fst-italic fsz-10 text-secondary">Lingayen Campus</small>
                <i class="border border-start border-1 border-secondary banner-text-divider"></i>
                <small class="fsz-10 font-bright-blue">Infirmary</small>
            </div>
        </div>
    </div>

    <div class="right-content d-flex flex-row text-end">
        <?php
        $avatar = "avatar_0.png";

        if (isset($_SESSION['avatar'])) {
            $icon = $_SESSION['avatar'];
            $avatar = "avatar_" . $icon . ".png";
        }

        $name = "Anonymous";
        $username = "";

        if (isset($_SESSION['firstname'], $_SESSION['middlename'], $_SESSION['lastname'], $_SESSION['username'])) 
        {
            $name = $_SESSION['firstname'] . " " . $_SESSION['middlename'] . " " . $_SESSION['lastname'];
            $username = $_SESSION['username'];
        }

        $email = "";

        if (isset($_SESSION['email'])) {
            $email = $_SESSION['email'];
        }

        $role = "";

        if (isset($_SESSION['role'])) {
            $role = UserRoles::ToDescName($_SESSION['role']);
        }

        ?>
        <!--PROFILE PIC-->
        <div class="d-inline-flex align-items-center">
            <div class="me-auto">
                <small class="username me-2"><?= $username ?></small>
            </div>
            <div class="dropdown">
                <a class="dropdown-toggle d-flex align-items-center" id="navbarDropdownMenuAvatar" role="button" data-mdb-toggle="dropdown" aria-expanded="false">

                    <div class="border border-2 border-primary rounded-circle p-1">
                        <img src="assets/images/avatars/<?= $avatar ?>" alt="avatar" width="30" height="30">
                    </div>
                </a>
                <div class="dropdown-menu dropdown-menu-end profile-dropdown p-3" aria-labelledby="navbarDropdownMenuAvatar">
                    <div class="row">
                        <div class="col-3 d-flex align-items-center justify-content-center">
                            <img src="assets/images/avatars/<?= $avatar ?>" alt="avatar" width="64" height="64">
                        </div>
                        <div class="col text-wrap text-break">
                            <h6 class="text-dark fw-bold"><?= $name ?></h6>
                            <h6 class="text-primary fsz-14"><?= $email ?></h6>
                            <span class="badge badge-success"><?= $role ?></span>
                        </div>
                    </div>
                    <div class="divider-separator border border-1 my-3"></div>
                    <div class="row">
                        <div class="col text-start align-middle">
                            <a href="">
                                <i class="fas fa-user-cog"></i>
                                <span class="ms-1">My Profile</span>
                            </a>
                        </div>
                        <div class="col text-end align-middle">
                            <a href="<?= Navigation::$URL_LOGOUT ?>" class="bg-danger text-white p-1 px-2 rounded-5" role="button">
                                <i class="fas fa-times"></i>
                                <span class="ms-1">Logout</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>