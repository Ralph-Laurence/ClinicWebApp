<?php global $detailsKey; ?>
<div class="modal fade" id="updatePasswordModal" tabindex="-1" aria-labelledby="updatePasswordModalLabel" aria-hidden="true" data-mdb-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content mt-4">
            <div class="modal-header p-2">
                <div class="modal-title fs-6" id="updatePasswordModalLabel">
                    <img src="assets/images/icons/modal-icn-update-passw.png" width="24" height="24">
                    Update Password
                </div>

            </div>

            <div class="modal-body">
                <form class="password-form" action="<?= Tasks::CHANGE_PASSWORD ?>" method="post">
                    <input type="text" name="user-key" class="d-none" value="<?= $detailsKey ?>">
                    <div class="bg-amber-light font-brown rounded-2 p-2 mb-3">
                        <ul class="list-unstyled my-0 fsz-14">
                            <li class="mb-1"><i class="me-2 fw-bold">&#x25CF;</i>New password must be different from the old password.</li>
                            <li class="mb-1"><i class="me-2 fw-bold">&#x25CF;</i>Password length must be at least four characters long.</li>
                        </ul>
                    </div>
                    <div class="row">
                        <div class="col pe-1">
                            <div class="form-outline w-100">
                                <input type="password" id="password" name="new-password" class="form-control input-validation" maxlength="32" required />
                                <label class="form-label" for="password">New Password</label>
                            </div>
                        </div>
                        <div class="col ps-1">
                            <div class="form-outline w-100">
                                <input type="password" id="retype-password" name="retype-password" class="form-control input-validation" maxlength="32" required />
                                <label class="form-label" for="retype-password">Retype Password</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col pe-1">
                            <small class="font-red error-tag error-tag-1 display-none">Please add a valid password</small>
                        </div>
                        <div class="col ps-1 text-end">
                            <small class="font-red error-tag error-tag-2 display-none">Passwords didn't match</small>
                        </div>
                    </div>

                    <div class="fsz-14 p-1 bg-document px-2 my-3">
                        <?= "We want to make sure that it's you (<strong>" . implode(" ", [UserAuth::getFirstname(), UserAuth::getMiddlename(), UserAuth::getLastname()]) . "</strong>) who's trying to change the password. Enter your username and password to confirm." ?>
                    </div>
                    <div class="d-flex flex-row align-items-center gap-2">
                        <div class="form-outline w-100">
                            <input type="text" id="username" name="username" class="form-control input-validation" maxlength="32" required />
                            <label class="form-label" for="username">Your Username</label>
                        </div>
                        <div class="form-outline w-100">
                            <input type="password" id="your-password" name="your-password" class="form-control input-validation" maxlength="32" required />
                            <label class="form-label" for="your-password">Your Password</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col pe-1">
                            <small class="font-red error-tag error-tag-3 display-none">Please enter your username</small>
                        </div>
                        <div class="col ps-1 text-end">
                            <small class="font-red error-tag error-tag-4 display-none">Please enter your password</small>
                        </div>
                    </div>
                    <hr class="hr divider">
                    <div class="d-flex align-items-center justify-content-end flex-row gap-2">
                        <button type="reset" class="btn btn-secondary fw-bold" data-mdb-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary btn-base btn-update">
                            <div class="d-flex align-items-center gap-2">
                                <img src="assets/images/icons/shield-windows.png" width="16" height="16">
                                <span>Update</span>
                            </div>
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>