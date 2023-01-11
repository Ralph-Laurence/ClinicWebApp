<?php
session_start();

require_once("rootcwd.php");

require_once($rootCwd . "includes/urls.php");

// we must be logged in to view this page
if (!isset($_SESSION['isLoggedIn']) || $_SESSION['isLoggedIn'] !== true) {
    header("Location: " . Navigation::$URL_LOGIN);
    exit;
}

require_once($rootCwd . "database/configs.php");
require_once($rootCwd . "includes/system.php");
require_once($rootCwd . "includes/utils.php");
require_once($rootCwd . "layout-header.php");
require_once($rootCwd . "includes/inc.get-users.php");

require_once($rootCwd . "library/defuse-crypto.phar");

use Defuse\Crypto\Crypto;
use Defuse\Crypto\Key;

$defuseKey_Ascii = Helpers::getDefuseKey($pdo);
$defuseKey = Key::loadFromAsciiSafeString($defuseKey_Ascii);

?>

<body>
    <!-- BEGIN CONTAINER -->
    <div class="container-fluid h-100 bg-document p-0">

        <!-- TITLE BANNER -->
        <?php include_once("layouts/banner.php") ?>
        <!-- TITLE BANNER -->

        <!-- MAIN CONTENT -->
        <main class="main-content-wrapper d-flex h-100 pt-5">

            <section class="d-flex flex-grow-1 mt-2 overflow-hidden">

                <!-- NAVIGATION -->
                <?php
                // mark the active side nav link
                setActiveLink(Navigation::$NavIndex_Users);

                require_once("layouts/navigation.php");
                ?>

                <!--WORKAREA-->
                <section class="workarea w-100 pb-4">

                    <!--WELCOME BANNER-->
                    <?php include_once("layouts/welcome-banner.php"); ?>

                    <!--THE WORKSHEET WRAPPER-->
                    <div class="worksheet-wrapper p-2 pb-4 w-100 h-100 overflow-hidden position-relative">

                        <div class="worksheet d-flex flex-column bg-white shadow-2-strong w-100 h-100 px-4 pt-3 pb-2 scrollable" style="overflow-y: auto;">

                            <!-- BREADCRUMB-->
                            <div class="breadcrumb w-100 d-flex flex-row align-items-center justify-content-start">
                                <h6 class="fas fa-folder-open font-accent"></h6>
                                <h6 class="ms-2 fw-bold">View</h6>
                                <h6 class="breadcrumb-arrow fas fa-play mx-3"></h6>
                                <h6 class="fas fa-users font-hilight"></h6>
                                <h6 class="ms-2 fw-bold">Users</h6>
                            </div>

                            <!--SEARCH BARS-->
                            <div class="searchbar-wrapper d-flex flex-row flex-wrap gap-2">
                                <div class="left-half me-auto d-flex flex-row gap-2 flex-wrap">
                                    <form action="" method="POST" class="d-flex flex-row flex-wrap gap-2 filter-form">
                                        <div class="form-outline">
                                            <input type="text" id="input-keyword" name="input-keyword" class="form-control" value="<?php if (!empty($keyword)) echo $keyword; ?>" <?php if (!empty($categoryOptions)) echo "disabled"; ?> />
                                            <label class="form-label" for="form12">Find User(s)</label>
                                        </div>
                                        <select name="find-item-option" id="find-item-option">
                                            <option value="filter-item-name" <?php //if (!empty($findBy) && $findBy == "filter-item-name") echo "selected"; ?>>By Firstname</option>
                                            <option value="filter-item-code" <?php //if (!empty($findBy) && $findBy == "filter-item-code") echo "selected"; ?>>By Lastname</option>
                                            <option value="filter-item-code" <?php //if (!empty($findBy) && $findBy == "filter-item-code") echo "selected"; ?>>By Username</option>
                                            <option value="filter-item-code" <?php //if (!empty($findBy) && $findBy == "filter-item-code") echo "selected"; ?>>By Email</option>
                                            <option value="filter-category" <?php //if (!empty($findBy) && $findBy == "filter-category") echo "selected"; ?>>By Roles</option>
                                            <option value="filter-newest-item" <?php //if (!empty($findBy) && $findBy == "filter-newest-item") echo "selected"; ?>>Newly Added</option>
                                        </select>
                                        <select name="role-options" id="role-options" <?php //if (empty($categoryOptions)) echo "disabled"; ?>>
                                            <option value="" disabled selected>Select Role</option>
                                            <?php
                                            $isSelectedFlag = "";

                                            if (!empty($roleOptions))
                                                $isSelectedFlag = "selected";
                                        
                                            // encrypt role values
                                            $role_s_admin = Crypto::encrypt(strval(3), $defuseKey);
                                            $role_admin = Crypto::encrypt(strval(2), $defuseKey);
                                            $role_staff = Crypto::encrypt(strval(1), $defuseKey);

                                            echo "<option $isSelectedFlag value=\"$role_s_admin\">Super Admin</option>";
                                            echo "<option $isSelectedFlag value=\"$role_admin\">Admin</option>";
                                            echo "<option $isSelectedFlag value=\"$role_staff\">Staff</option>";
                                            ?>
                                        </select>
                                        <button type="button" class="btn btn-primary bg-base btn-find">
                                            <i class="fas fa-search me-2"></i>
                                            <span>Find</span>
                                        </button>
                                    </form>
                                    <button <?php echoOnclick(Navigation::$URL_USERS); ?> class="btn btn-primary 
                                    <?php
                                    // $display = "";

                                    // if (empty($condition))
                                    //     $display = 'display-none';

                                    // if ($findBy == "filter-newest-item" || $findBy == "lastupdated")
                                    //     $display = "";

                                    // echo $display;
                                    ?>">
                                        <i class="fas fa-undo me-2"></i>
                                        <span>Reset</span>
                                    </button>
                                </div>
                                <div class="right-half">
                                    <div class="dropdown">
                                        <button class="btn btn-secondary bg-base text-white dropdown-toggle w-100" type="button" id="options-dropdown-button" data-mdb-toggle="dropdown" aria-expanded="false">
                                            Options
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-custom-light" aria-labelledby="options-dropdown-button ul-li-pointer">

                                            <li <?php echoOnclick(Navigation::$URL_CREATE_USER); ?> class="d-flex align-items-center gap-3 px-3 py-1 dropdown-item-custom-light">
                                                <div class="dropdown-item-icon text-center">
                                                    <i class="fas fa-user-plus fs-6 text-success"></i>
                                                </div>
                                                <div class="fs-6">Create User</div>
                                            </li>
                                            <li onclick="" class="d-flex align-items-center gap-3 px-3 py-1 dropdown-item-custom-light">
                                                <div class="dropdown-item-icon text-center">
                                                    <i class="fas fa-quote-right font-accent"></i>
                                                </div>
                                                <div class="fs-6">Import from CSV</div>
                                            </li>
                                            <li>
                                                <hr class="dropdown-divider" />
                                            </li>
                                            <li class="d-flex align-items-center gap-3 px-3 py-1 dropdown-item-custom-light dropdown-option-delete-all-selected">
                                                <span class="dropdown-item-icon text-center">
                                                    <i class="fas fa-trash font-red"></i>
                                                </span>
                                                <span class="fs-6">Delete All Selected</span>
                                            </li>

                                        </ul>
                                        <input type="text" name="input-patient-type" class="input-patient-type d-none" value="" required>
                                    </div>
                                </div>
                            </div>

                            <!--DIVIDER-->
                            <div class="divider-separator border border-1 border-bottom mt-3 mb-2"></div>

                            <div class="total-items-counter d-flex align-items-center mb-2">
                                <!-- ITEM STATUS COUNTER -->
                                <div class="d-flex me-auto d-flex align-items-center">
                                    <span class="fs-6 me-2">Total Users: </span>
                                    <span class="badge badge-primary me-2">
                                        <?php echo (!empty($userRecordsCount)) ? $userRecordsCount : "0"; ?>
                                    </span>
                                    <span class="fsz-12 text-muted me-4">(1 hidden)</span>

                                    <span class="fs-6 me-2">Super Admin: </span>
                                    <span class="badge badge-success me-4">
                                        <?= $superAdminCount ?>
                                    </span>

                                    <span class="fs-6 me-2">Admin: </span>
                                    <span class="badge badge-primary me-4">
                                        <?= $adminCount ?>
                                    </span>

                                    <span class="fs-6 me-2">Staff: </span>
                                    <span class="badge badge-warning">
                                        <?= $staffCount ?>
                                    </span>
                                </div>
                                <!-- PAGINATOR -->
                                <div class="d-flex align-items-center">
                                    <div class="me-1 d-inline">Show</div>
                                    <!-- <select id="virtual-entries-paginator">
                                         
                                    </select> -->
                                    <div class="entries-paginator-container">
                                        
                                    </div>
                                    <div class="ms-1 d-inline">entries</div>
                                </div>
                            </div>


                            <!-- WORKSHEET TABLE-->
                            <div class="w-100 flex-grow-1 border border-1 border-secondary mb-2 worksheet-table-wrapper" style="overflow-y: auto;">
                                <table class="table table-sm table-striped table-hover position-relative stocks-table">
                                    <thead class="bg-amber text-dark position-sticky top-0 z-10">
                                        <tr>
                                            <!--CHECKBOX-->
                                            <th scope="col">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" value="" id="column-check-all" />
                                                </div>
                                            </th>
                                            <!--ICONS--> 
                                            <th scope="col"></th>
                                            <th class="fw-bold" scope="col">Name</th>
                                            <th class="fw-bold" scope="col">Role</th>
                                            <th class="fw-bold" scope="col">Username</th>
                                            <th class="fw-bold" scope="col">Email</th>
                                            <th class="fw-bold" scope="col">Action</th>
                                            <th class="d-none" scope="col">UserKey</th>  
                                        </tr>
                                    </thead>
                                    <tbody class="users-dataset bg-white">
                                        <?php
                                        if (!empty($usersDataSet)) 
                                        {
                                            foreach ($usersDataSet as $row) 
                                            {
                                                $avatar = $row['avatar'];
                                                $name = $row['name'];
                                                $guid = $row['guid'];
                                                $username = $row['username'];
                                                $email = $row['email'];
                                                $role = $row['role'];
                                                $guid = $row['guid'];

                                                // Do not show the currently logged-on user's account onto the table
                                                if ($guid == $_SESSION['guid'])
                                                {
                                                    continue;
                                                }

                                                $userKey = Crypto::encrypt($guid, $defuseKey);
                                                 
                                                $rank = "isgn_staff";
                                                

                                                // assign the right icon foreach role levels : 
                                                // 3 - S. Admin
                                                // 2 - Admin
                                                // 1 - Staff
                                                if ($role == 3)
                                                {
                                                    $rank = "isgn_s_admin";
                                                }
                                                else if ($role == 2)
                                                {
                                                    $rank = "isgn_admin";
                                                }
                                                else 
                                                {
                                                    $rank = "isgn_staff";
                                                }

                                                // Convert role numbers to text equivalent
                                                $roleDesc = UserRoles::ToDescName($role); 

                                                echo
                                                "<tr class=\"align-middle\">
                                                    <td>
                                                        <div class=\"form-check\">
                                                            <input class=\"form-check-input\" type=\"checkbox\" value=\"\" id=\"row-check-box\" />
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <img src=\"assets/images/avatars/avatar_$avatar.png\" width=\"32\" height=\"32\">
                                                    </td>
                                                    <td class=\"fw-bold\">$name</td>
                                                    <td>
                                                        <div class=\"d-flex align-items-center\">
                                                            <img src=\"assets/images/icons/$rank.png\" width=\"20\" height=\"20\">
                                                            <span class=\"ms-1\">$roleDesc</span>
                                                        </div> 
                                                    </td>
                                                    <td>$username</td>
                                                    <td>$email</td>
                                                    <td>
                                                        <div class=\"btn-group\">
                                                            <button type=\"button\" class=\"btn btn-primary btn-item-details px-2 text-center\">Details</button>
                                                            <button type=\"button\" class=\"btn btn-primary btn-split-arrow px-0 text-center dropdown-toggle dropdown-toggle-split\" data-mdb-toggle=\"dropdown\" aria-expanded=\"false\"></button>
                                                            <ul class=\"dropdown-menu dropdown-menu-custom-light-small\">
                                                                <li onclick=\"\" class=\"d-flex align-items-center gap-3 px-3 py-1 dropdown-item-custom-light\">
                                                                    <div class=\"dropdown-item-icon text-center\">
                                                                        <i class=\"fas fa-pen fs-6 text-warning\"></i>
                                                                    </div>
                                                                    <div class=\"fs-6\">Edit</div>
                                                                </li> 
                                                                <li onclick=\"\" class=\"d-flex align-items-center gap-3 px-3 py-1 dropdown-item-custom-light\">
                                                                    <div class=\"dropdown-item-icon text-center\">
                                                                        <i class=\"fas fa-trash fs-6 font-red\"></i>
                                                                    </div>
                                                                    <div class=\"fs-6\">Delete</div>
                                                                </li> 
                                                            </ul>
                                                        </div>
                                                    </td>
                                                    <td class=\"d-none\">$userKey</td>
                                                </tr>";
                                            }
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>

                </section>

            </section>
        </main>
        <!-- MAIN CONTENT -->

        <?php // Hidden form; This will handle an item's EDIT action ?>
        <!-- <form action="<?// Navigation::$URL_EDIT_ITEM ?>" method="GET" class="frm-edit-item d-none">
            <input type="text" name="item-key" id="item-key">
            <input type="text" name="item-page" id="item-page">
        </form> -->

        <?php // Hidden form; This will handle an item's DELETE action ?>
        <!-- <form action="<?// Navigation::$ACTION_DELETE_ITEM ?>" method="POST" class="frm-delete-item d-none">
            <input type="text" name="item-key" id="item-key">
        </form> -->

        <?php
        // Store multiple item keys here as AJAX string.  
        // We will use this for getting all checked rows in table
        ?>
        <!-- <form action="<?// Navigation::$ACTION_DELETE_ITEMS ?>" method="POST" class="frm-delete-items d-none">
            <input type="text" name="item-keys" id="item-keys">
        </form> -->

        <?php
        //
        // SESSION: EDIT ITEM
        //
        // $lastUpdated_ItemName = "";
        // $lastUpdated_ItemPage = -1;

        // if (isset($_SESSION['edit_item_success'], $_SESSION['edit_updated_item_name'], $_SESSION['edit_item_page'])) {
        //     if ($_SESSION['edit_item_success'] === true) {
        //         $lastUpdated_ItemName = $_SESSION['edit_updated_item_name'];
        //         $lastUpdated_ItemPage = $_SESSION['edit_item_page'];
        //     }
        // }
        //
        // SESSION: DELETE SINGLE ITEM
        //
        // $deleteItem_Status = "";

        // if (isset($_SESSION['delete-item-success'], $_SESSION['delete-item-status'])) {
        //     if ($_SESSION['delete-item-success'] === true)
        //         $deleteItem_Status = $_SESSION['delete-item-status'];
        // }
        //
        // SESSION: DELETE MULTIPLE ITEM
        //
        // $deleteItems_Status = "";

        // if (isset($_SESSION['delete-items-success'], $_SESSION['delete-items-status'])) {
        //     if ($_SESSION['delete-items-success'] === true)
        //         $deleteItems_Status = $_SESSION['delete-items-status'];
        // }
        ?>

        <?php // Hidden fields, to hold the last updated item name and page index ?>
        <!-- <form class="frm-session-vars">
            <input type="text" class="d-none session-var-item-name" value="<?= $lastUpdated_ItemName ?>">
            <input type="text" class="d-none session-var-item-page" value="<?= $lastUpdated_ItemPage ?>">
            <input type="text" class="d-none session-var-delete-item-status" value="<?= $deleteItem_Status ?>">
            <input type="text" class="d-none session-var-delete-items-status" value="<?= $deleteItems_Status ?>">
        </form> -->

    </div>
    <!-- END CONTAINER -->

    <?php

    //unset(
        // $_SESSION['edit_item_success'],
        // $_SESSION['edit_updated_item_name'],
        // $_SESSION['edit_item_page'],
        // $_SESSION['delete-item-success'],
        // $_SESSION['delete-item-status'],
        // $_SESSION['delete-items-success'],
        // $_SESSION['delete-items-status']
    //);

    // modal window for showing the item details
    require_once("layouts/item-info-dialog.php");

    require_once("components/alert-dialog/alert-dialog.php");
    // require_once("components/confirm-dialog/confirm-dialog.php");
    // require_once("components/snackbar/snackbar.php");
    ?>

    <!--SCRIPTS-->
    <script src="assets/lib/jquery/jquery-3.6.1.min.js"></script>
    <script src="assets/lib/jquery-ui-1.13.2.custom/jquery-ui.min.js"></script>
    <script src="assets/lib/datatables/datatables.min.js"></script>
    <script src="assets/lib/mdb5/js/mdb.min.js"></script>
    <script src="assets/lib/momentjs/moment-with-locales.js"></script>
    <script src="assets/lib/jquery.nicescroll/jquery.nicescroll.min.js"></script>

    <script src="assets/js/nicescroll.js"></script>
    <script src="assets/js/base-ui.js"></script>
    <script src="assets/js/system.js"></script>

    <script src="components/alert-dialog/alert-dialog.js"></script>
    <!-- <script src="components/confirm-dialog/confirm-dialog.js"></script>
    <script src="components/snackbar/snackbar.js"></script> -->

</body>

</html>