<?php
require_once("rootcwd.inc.php");
require_once($cwd . "env.php");
  
// Root volume directory 
$rootDir = str_replace("\\", "/", getcwd());
  
// avatars folder path
$avatarsDir = "assets/images/avatars/";

// path to physical drive where avatar images are stored
$avatarsPath = $rootDir ."/". $avatarsDir;
  
// retrieve all image files in the path given above
$avatarsGlob = glob($avatarsPath . "*.{png}", GLOB_BRACE);
 
// we will store all filenames here
$avatars = [];

// get just the filename and store it on array
foreach ($avatarsGlob as $avatar) 
{
    // basename($avatar)
    array_push($avatars, pathinfo($avatar, PATHINFO_FILENAME));
}
 
// the avatar's human-readable names are stored here
$avatarMap = Helpers::getAvatarMap();

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
                <div class="table-wrapper" style="overflow-y: auto; height: 400px;">
                    <table class="table table-striped">
                         
                        <tbody> 
                            <?php
                            if (!empty($avatars)) 
                            {
                                foreach($avatars as $a)
                                {
                                    $avatarIcon = ENV_SITE_ROOT . $avatarsDir . $a; 
                                    $name = $avatarMap[$a];
                                      
                                    echo 
                                    "<tr>
                                        <th scope=\"row\">
                                            <img src=\"$avatarIcon.png\" width=\"48\" height=\"48\">
                                        </th>
                                        <td>$name</td>
                                        <td>
                                        <button type=\"button\" class=\"btn btn-link fw-bold\" onclick=\"setAvatarOnSelect('$a')\" data-mdb-dismiss=\"modal\">Select</button>
                                        </td> 
                                    </tr>";
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

            </div>
            <div class="modal-footer  py-2">
                <button class="btn btn-secondary" type="button" data-mdb-dismiss="modal">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>