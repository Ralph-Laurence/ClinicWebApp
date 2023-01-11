<?php
require_once("rootcwd.php");
 
?>
<div class="modal fade avatarPickerModal" id="avatarPickerModal" tabindex="-1" aria-labelledby="avatarPickerModalLabel" aria-hidden="true" data-mdb-backdrop="static">
    <div class="modal-dialog pt-3">
        <div class="modal-content mt-4">
            <div class="modal-header bg-base text-white py-0 ps-4 pe-0">
                <h6 class="modal-title" id="avatarPickerModalLabel">Choose Avatar</h6>
                <button type="button" class="btn shadow-0 fs-5 text-white" data-mdb-dismiss="modal">
                    <i class="fas fa-times-circle"></i>
                </button>
            </div>

            <div class="modal-body px-4">
                <?php 
                    $avatarsPath = $rootCwd . "assets/images/avatars/";
                    $avatars = glob($avatarsPath . "*.{png}", GLOB_BRACE);
                     
                    foreach ($avatars as $avatar)
                    {
                        echo $avatar . "\n";
                    }
                ?>
            </div>
            <div class="modal-footer  py-2">
                <button class="btn btn-secondary" type="button" data-mdb-dismiss="modal">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>