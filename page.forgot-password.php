<?php

require_once("rootcwd.php");
require_once($rootCwd . "controllers/ForgotPasswordController.php");

?>

<body>
    <div class="container-fluid d-flex align-items-center justify-content-center h-100 w-100 login-container">
        <div class="row bg-white shadow rounded-2 overflow-hidden text-white" style="width: 500px;">
            <div class="callout position-relative d-flex align-items-center justify-content-center bg-base text-center mb-4 p-2">
                Forgot Password
            </div>
            <div class="forms-wrapper mb-3 px-5">
                <form action="<?= Tasks::FORGOT_PASSWORD ?>" class="main-form" method="post">
                    <small class="text-primary">Verification</small>
                    <div class="input-group mb-3">
                        <span class="input-group-text" style="width: 42px;">
                            <i class="fas fa-user-tag"></i>
                        </span>
                        <input type="text" class="form-control" id="username" name="username" placeholder="Username" required 
                        value="<?= loadLastInput('username') ?>"/>
                    </div>
                    <div class="input-group mb-3">
                        <span class="input-group-text" style="width: 42px;">
                            <i class="fas fa-key"></i>
                        </span>
                        <input type="password" class="form-control" id="seckey" name="seckey" placeholder="Security Key" required 
                        value="<?= loadLastInput('seckey') ?>"/>
                    </div>
                    <small class="text-primary">Password</small>
                    <div class="bg-amber-light font-brown rounded-2 p-2 mb-3">
                        <ul class="list-unstyled my-0 fsz-14">
                            <li class="mb-1"><i class="me-2 fw-bold">&#x25cf;</i>New password must be different from the old password.</li>
                            <li class="mb-1"><i class="me-2 fw-bold">&#x25cf;</i>Password length must be at least 8 characters long.</li>
                        </ul>
                    </div>
                    <div class="input-group mb-3">
                        <span class="input-group-text" style="width: 42px;">
                            <i class="fab fa-flickr"></i>
                        </span>
                        <input type="password" class="form-control" id="new-pass" name="new-pass" placeholder="New Password" required 
                        value="<?= loadLastInput('new-pass') ?>"/>
                        <span class="input-group-text confirm-passw-check display-none" style="width: 42px;">
                            <i class="fas fa-check text-success"></i>
                        </span>
                    </div>
                    <div class="input-group mb-3">
                        <span class="input-group-text" style="width: 42px;">
                            <i class="fab fa-flickr"></i>
                        </span>
                        <input type="password" class="form-control" id="confirm-pass" name="confirm-pass" placeholder="Confirm Password" required />
                        <span class="input-group-text confirm-passw-check display-none" style="width: 42px;">
                            <i class="fas fa-check text-success"></i>
                        </span>
                    </div> 
                    <div class="error-label display-none bg-red-light fsz-14 mb-3 font-red-dark p-2 rounded-2">
                        
                    </div>
                    <div class="text-end">
                        <button type="button" class="btn btn-secondary btn-submit px-3 py-2">Reset Password</button>
                    </div>
                </form>
            </div>
            <div class="p-3 border-top d-flex align-items-center">
                <a href="<?= Pages::LOGIN ?>" class="me-auto fsz-14">
                    <i class="fas fa-lock me-2"></i>
                    Login
                </a>
                <a role="button" class="fsz-14" data-mdb-toggle="modal" data-mdb-target="#helpModal">
                    <i class="fas fa-question-circle me-2"></i>
                    Help
                </a>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="helpModal" tabindex="-1" aria-labelledby="helpModalLabel" aria-hidden="true" data-mdb-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content mt-4">
                <div class="modal-header px-3 py-2">
                    <h6 class="modal-title d-flex flex-row align-items-center gap-2" id="helpModalLabel">
                        <img src="assets/images/icons/office-help.png" alt="icon" width="20" height="20">
                        Help
                    </h6>
                </div>

                <div class="modal-body">
                    <h6 class="font-primary-dark">
                        <i class="fas fa-arrow-circle-right me-2"></i>
                        What is a Security Key?
                    </h6>
                    <p class="fsz-14">
                        Your security key is an automatically generated text with a combination of letters, numbers and dashes which will be used to reset your password.
                    </p>
                    <div class="fsz-14 bg-document rounded-2 mb-3 p-2">
                        <i class="fas fa-info-circle me-2"></i>
                        You should not share your key with anyone else.
                    </div>
                    <h6 class="font-primary-dark">
                        <i class="fas fa-arrow-circle-right me-2"></i>
                        I forgot my security key, what should I do?
                    </h6>
                    <p class="fsz-14">
                        If you forgot your security key, you can ask your system administrator to reset your <strong>password</strong> for you.
                    </p>
                    <div class="fsz-14 bg-document rounded-2 p-2">
                        <i class="fas fa-info-circle me-2"></i>
                        Resetting your password will also re-generate your security key.
                    </div>
                </div>

                <div class="modal-footer py-2">
                    <button class="btn btn-primary bg-base" type="button" data-mdb-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <textarea class="d-none err-message"><?= getErrorMessage() ?></textarea>
    <!--SCRIPTS-->
    <script src="assets/lib/jquery/jquery-3.6.1.min.js"></script>
    <script src="assets/lib/mdb5/js/mdb.min.js"></script>
    <script src="assets/lib/mdb5/js/mdb.form-validation.js"></script>
    <script src="assets/js/system.js"></script>
    <script src="assets/js/page.forgot-password.js"></script>

</body>

</html>