<?php

require_once("rootcwd.php");
require_once($rootCwd . "controllers/LoginController.php");

?>

<body>
    <div class="container-fluid d-flex align-items-center justify-content-center h-100 w-100 login-container">
        <div class="login-box-wrapper row shadow">

            <!-- LEFT HALF => COVER PHOTO -->
            <div class="col-6 bg-base rounded-start position-relative">

                <div class="login-background-col position-absolute start-0 end-0 top-0 bottom-0 w-100 h-100"></div>
                <div class="login-background-overlay-col z-10 position-absolute start-0 end-0 top-0 bottom-0 w-100 h-100
                            d-flex flex-column align-items-center flex-wrap px-4 pb-4 pt-5">
                    <img src="assets/images/logo.png" alt="logo" class="mbt-4 mb-3" width="86" height="86">
                    <h6 class="text-white text-center text-uppercase">Medicine Inventory & Patient Information System</h6>
                    <div class="d-inline-flex align-items-center gap-2 mb-5">
                        <span class="fst-italic fsz-12 font-amber">Pangasinan State University</span>
                        <i class="border border-start border-1 border-secondary banner-text-divider"></i>
                        <small class="fst-italic fsz-12 font-document-background">Lingayen Campus</small>
                        <i class="border border-start border-1 border-secondary banner-text-divider"></i>
                        <small class="fsz-12 font-bright-blue">Infirmary</small>
                    </div>
                    <div class="text-start px-3 d-flex flex-column">
                        <small class="text-white mb-5">Manage patient information and track inventory stocks and medical supplies used each day in a healthcare setting.</small>
                        <small class="version-label text-white text-start mt-5 fst-italic">
                            <?php
                            if (!empty($app_version)) {
                                echo "Version " . $app_version;
                            }
                            ?>
                        </small>
                    </div>
                </div>

            </div>
            <!-- RIGHT HALF => MAIN LOGIN FORM -->
            <div class="col bg-white rounded-end d-flex flex-column align-items-center px-5 pt-5">
                <h6 class="fw-bold mb-4">Log into your account</h6>
                <div class="inputs-wrapper w-100 mx-2">

                    <form action="" method="POST" class="main-form">

                        <!-- AUTHENTICATION WARNING-->
                        <div class="row mb-2">
                            <div class="col-1"></div>
                            <div class="col">
 
                                <div class="alert alert-danger auth-warning display-none p-2">
                                    <div class="row">
                                        <div class="col-1">
                                            <i class="fas fa-exclamation-triangle"></i>
                                        </div>
                                        <div class="col text-wrap">
                                            <div class="ms-2 me-1 auth-msg fsz-14"></div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <!-- USERNAME -->
                        <div class="row mb-3">
                            <div class="col-1 d-flex align-items-center justify-content-center">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="col">
                                <div class="form-outline w-100">
                                    <input type="text" name="input-username" id="input-username" class="form-control" value="<?= loadLastUsername() ?>" required />
                                    <label class="form-label" for="input-username">Username or Email</label>
                                    <div class="invalid-feedback">Please enter your username!</div>
                                </div>
                            </div>
                        </div>
                        <!-- PASSWORD -->
                        <div class="row">
                            <div class="col-1 d-flex align-items-center justify-content-center">
                                <i class="fas fa-lock"></i>
                            </div>
                            <div class="col">
                                <div class="form-outline w-100">
                                    <input type="password" name="input-password" id="input-password" class="form-control" value="" required />
                                    <label class="form-label" for="input-password">Password</label>
                                    <div class="invalid-feedback">Please enter your password!</div>
                                </div>
                            </div>
                        </div>
                        <!--FORGOT PASSWORD-->
                        <div class="forgot-password text-start mx-4 my-2">
                            <small>
                                <a href="<?= Pages::FORGOT_PASSWORD ?>">I forgot my password</a>
                            </small>
                        </div>
                        <!--LOGIN BUTTON-->
                        <div class="form-submit text-end">
                            <button type="submit" class="btn btn-primary btn-base btn-login">Login</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <textarea class="d-none err-msg"><?= getErrorMessage() ?></textarea>
    <!--SCRIPTS-->
    <script src="assets/lib/jquery/jquery-3.6.1.min.js"></script>
    <script src="assets/lib/mdb5/js/mdb.min.js"></script>
    <script src="assets/js/system.js"></script>
    <script src="assets/js/login.js"></script>

</body>

</html>