<?php
@session_start();

require_once("rootcwd.inc.php");

require_once($rootCwd . "database/configs.php");
require_once($rootCwd . "database/dbhelper.php"); 
 
require_once($rootCwd . "includes/urls.php");

require_once($rootCwd . "includes/Security.php");
require_once($rootCwd . "errors/IError.php");
  
class AvatarsPicker
{ 
    private $avatarMap =
    [
        "avatar_0"  => "Male",
        "avatar_1"  => "Female",
        "avatar_2"  => "Pineapple",
        "avatar_3"  => "Banana",
        "avatar_4"  => "Cookie Monster",
        "avatar_5"  => "Black Widow",
        "avatar_6"  => "Money Heist",
        "avatar_7"  => "Matrix Architect",
        "avatar_8"  => "Joker",
        "avatar_9"  => "Ninja Turtle",
        "avatar_10" => "Minecraft",
        "avatar_11" => "Cat",
        "avatar_12" => "Bad Piggy",
        "avatar_13" => "Lemur",
        "avatar_14" => "Bandicoot",
        "avatar_15" => "Madagascar",
        "avatar_16" => "Dinosaur",
        "avatar_17" => "Shrek",
        "avatar_18" => "Dog",
        "avatar_19" => "Hulk",
        "avatar_20" => "Ninja",
        "avatar_21" => "Knight",
        "avatar_22" => "Administrator",
        "avatar_23" => "Super Mario",
        "avatar_24" => "Male 2",
        "avatar_25" => "Worker",
        "avatar_26" => "Female 2",
        "avatar_27" => "Female Agent",
        "avatar_28" => "Agent Smith",
        "avatar_29" => "Hitman",
        "avatar_30" => "Star Wars",
        "avatar_31" => "Jake",
        "avatar_32" => "Finn",
        "avatar_33" => "Abra",
        "avatar_34" => "Shaggy",
        "avatar_35" => "Scooby Doo",
        "avatar_36" => "Spider Man",
        "avatar_37" => "Venom",
        "avatar_38" => "Tom",
        "avatar_39" => "Jerry",
        "avatar_40" => "X Men",
        "avatar_41" => "Itachi",
        "avatar_42" => "Kakashi",
        "avatar_43" => "Gaara",
        "avatar_44" => "Naruto",
        "avatar_45" => "Sakura",
        "avatar_46" => "Tsunade",
        "avatar_47" => "Circle Guard",
        "avatar_48" => "Square Guard",
        "avatar_49" => "Triangle Guard",
        "avatar_50" => "Deathstroke",
        "avatar_51" => "Darth Vader",
        "avatar_52" => "Taehyung",
        "avatar_53" => "Barack Obama",
        "avatar_54" => "Donald Trump",
        "avatar_55" => "Vladimir Putin",
        "avatar_56" => "Adolf Hitler",
        "avatar_57" => "Messi",
        "avatar_58" => "Neymar",
        "avatar_59" => "Ronaldo",
        "avatar_60" => "Iron Man",
        "avatar_61" => "Police",
        "avatar_62" => "Guard",
        "avatar_63" => "Soldier",
        "avatar_64" => "Hunter",
        "avatar_65" => "Alien",
        "avatar_66" => "Lantern",
        "avatar_67" => "Monster",
        "avatar_68" => "Raccoon",
        "avatar_69" => "Thanos",
        "avatar_70" => "Thor", 
        "avatar_71" => "Elon Musk", 
    ];

    private $path = "";
    private $security;

    function __construct()
    {
        $this->path = (dirname(__DIR__, 1) . "/assets/images/avatars/");
        $this->path = str_replace("\\", "/", $this->path);
        
        $this->security = new Security();
    }

    function loadAvatars()
    {
        try 
        { 
            // Get all files in directory
            $files = array_values(array_diff(scandir($this->path), array('.', '..'))); 
            
            // Natural sort (for alpha numeric sorting like how winExplorer displays files)
            natsort($files);

            // collect avatar names (keys)
            $avatarKeys  = array_keys($this->avatarMap); 

            // verified avatars will be stored here
            $out = [];

            // Test each file if it is registered in avatar maps
            foreach ($files as $filename) 
            {
                // remove .png from name
                $f = str_replace(".png", "", $filename);

                // add the avatar  into the output with its keys as filename
                // and its values as descriptive names
                if (in_array($f, $avatarKeys))
                {
                    $out[$filename] = $this->avatarMap[$f];
                }
            }

            return $out;
        } 
        catch (\Exception $th) 
        { 
            IError::Throw(Response::Code404);
            exit;
        }
    }
    //
    // Display the avatars into picker window
    //
    function bindDataset()
    {
        $avatars = $this->loadAvatars();
 
        foreach ($avatars as $filename => $description)
        {
            $file = $this->security->Encrypt($filename);
            $src = (ENV_SITE_ROOT . "assets/images/avatars/$filename");

            echo <<<TR
            <tr>
                <td class="th-75">
                    <img class="avatar-img" src="$src" width="28" height="28">
                </th>
                <td class="th-150">{$description}</th>
                <td class="th-75">
                    <button type="button" class="btn btn-secondary fw-bold py-1 px-2 btn-select-avatar" data-mdb-dismiss="modal">
                    Select
                    </button>
                </td>
                <td class="d-none">
                    <input class="td-avatar" value="$file"/>
                </td>
            </tr>
            TR;
        }
    }
} 

$avatarPicker = new AvatarsPicker();
?>

<div class="modal fade" id="findAvatarModal" tabindex="-1" aria-labelledby="findAvatarModalLabel" aria-hidden="true" data-mdb-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content mt-4">
            <div class="modal-header py-0 ps-4 pe-0">
                <h6 class="modal-title" id="findAvatarModalLabel">Select Avatar</h6>
                <button type="button" class="btn shadow-0 fs-5" data-mdb-dismiss="modal">
                    <i class="fas fa-times-circle"></i>
                </button>
            </div>

            <div class="modal-body">
                <div class="table-wrapper" style="overflow-y: auto; max-height: 450px; height: 450px;">
                    <table class="table table-sm table-striped table-hover">
                        <thead class="style-secondary position-sticky top-0 z-10">
                            <tr>
                                <th scope="col" class="fw-bold th-75">Avatar</th>
                                <th scope="col" class="fw-bold th-th-150"></th>
                                <th scope="col" class="fw-bold th-75 text-center">Action</th>
                                <th scope="col" class="d-none"></th>
                            </tr>
                        </thead>
                        <tbody class="avatar-dataset">
                            <?php $avatarPicker->bindDataset(); ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="modal-footer py-2">
                <button class="btn btn-primary bg-base" type="button" data-mdb-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>