<?php
session_start();

require_once("rootcwd.php");

require_once($rootCwd . "includes/urls.php");

// we must be logged in to view this page
if (!isset($_SESSION['isLoggedIn']) || $_SESSION['isLoggedIn'] !== true)
{
    header("Location: " . Navigation::$URL_LOGIN);
    exit;
}

require_once($rootCwd . "database/configs.php");
require_once($rootCwd . "includes/system.php");
require_once($rootCwd . "includes/utils.php");
require_once($rootCwd . "layout-header.php");
require_once($rootCwd . "includes/inc.get-stocks-inventory.php");

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
                setActiveLink(Navigation::$NavIndex_Stocks);

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
                                <h6 class="fas fa-warehouse font-teal"></h6>
                                <h6 class="ms-2 fw-bold">Stocks Inventory</h6>
                            </div>

                            <!--SEARCH BARS-->
                            <div class="searchbar-wrapper d-flex flex-row flex-wrap gap-2">
                                <div class="left-half me-auto d-flex flex-row gap-2 flex-wrap">
                                    <form action="" method="POST" class="d-flex flex-row flex-wrap gap-2 filter-form">
                                        <div class="form-outline">
                                            <input type="text" id="input-keyword" name="input-keyword" class="form-control" value="<?php if (!empty($keyword)) echo $keyword; ?>" <?php if (!empty($categoryOptions)) echo "disabled"; ?> />
                                            <label class="form-label" for="form12">Find Item(s)</label>
                                        </div>
                                        <select name="find-item-option" id="find-item-option">
                                            <option value="filter-item-name" <?php if (!empty($findBy) && $findBy == "filter-item-name") echo "selected"; ?>>By Item Name</option>
                                            <option value="filter-item-code" <?php if (!empty($findBy) && $findBy == "filter-item-code") echo "selected"; ?>>By Item Code</option>
                                            <option value="filter-category" <?php if (!empty($findBy) && $findBy == "filter-category") echo "selected"; ?>>By Category</option>
                                            <option value="filter-newest-item" <?php if (!empty($findBy) && $findBy == "filter-newest-item") echo "selected"; ?>>Newly Added</option>
                                            <option value="filter-critical-item" <?php if (!empty($findBy) && $findBy == "filter-critical-item") echo "selected"; ?>>Critical Items</option>
                                            <option value="filter-soldout-item" <?php if (!empty($findBy) && $findBy == "filter-soldout-item") echo "selected"; ?>>Out of Stock</option>
                                        </select>
                                        <select name="category-options" id="category-options" <?php if (empty($categoryOptions)) echo "disabled"; ?>>
                                            <option value="" disabled selected>Select Category</option>
                                            <?php
                                            if ($categoriesRecordsCount > 0) 
                                            {
                                                foreach ($medicineCategories as $k => $v) 
                                                {
                                                    $isSelectedFlag = "";

                                                    if (!empty($categoryOptions) && $categoryOptions == $v)
                                                        $isSelectedFlag = "selected";

                                                    echo "<option $isSelectedFlag value=\"$v\">$k</option>";
                                                }
                                            }
                                            ?>
                                        </select>
                                        <button type="button" class="btn btn-primary bg-base btn-find">
                                            <i class="fas fa-search me-2"></i>
                                            <span>Find</span>
                                        </button>
                                    </form>
                                    <button <?php echoOnclick(Navigation::$URL_STOCKS_INVENTORY); ?> class="btn btn-primary 
                                    <?php
                                    $display = "";

                                    if (empty($condition))
                                        $display = 'display-none';

                                    if ($findBy == "filter-newest-item" || $findBy == "lastupdated")
                                        $display = "";

                                    echo $display;
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

                                            <li <?php echoOnclick(Navigation::$URL_ADD_NEW_ITEM); ?> class="d-flex align-items-center gap-3 px-3 py-1 dropdown-item-custom-light">
                                                <div class="dropdown-item-icon text-center">
                                                    <i class="fas fa-plus fs-6 text-success"></i>
                                                </div>
                                                <div class="fs-6">Add New Item</div>
                                            </li>
                                            <li onclick="" class="d-flex align-items-center gap-3 px-3 py-1 dropdown-item-custom-light">
                                                <div class="dropdown-item-icon text-center">
                                                    <i class="fas fa-quote-right font-accent"></i>
                                                </div>
                                                <div class="fs-6">Export to CSV</div>
                                            </li>
                                            <li onclick="" class="d-flex align-items-center gap-3 px-3 py-1 dropdown-item-custom-light">
                                                <div class="dropdown-item-icon text-center">
                                                    <i class="fas fa-chart-pie font-hilight"></i>
                                                </div>
                                                <div class="fs-6">Create Report</div>
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
                                <span class="fs-6 me-2">Total Items Found: </span>
                                <span class="badge badge-primary me-4">
                                    <?php echo (!empty($medicineRecordsCount)) ? $medicineRecordsCount : "0"; ?>
                                </span>

                                <span class="fs-6 mx-2">Critical Items: </span>
                                <span class="badge badge-warning me-4">
                                    <?php echo $criticalItemsCount; ?>
                                </span>

                                <span class="fs-6 me-2">Out of Stock: </span>
                                <span class="badge badge-danger">
                                    <?php echo $soldOutItemsCount; ?>
                                </span>
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
                                            <th class="fw-bold" scope="col">Item</th>
                                            <th class="fw-bold" scope="col">Code</th>
                                            <th class="fw-bold" scope="col">Category</th>
                                            <th class="fw-bold" scope="col">Stock</th>
                                            <th class="fw-bold" scope="col">Reserved</th>
                                            <th class="fw-bold" scope="col">Action</th>
                                            <th class="d-none" scope="col">Supplier</th>
                                            <th class="d-none" scope="col">Date Added</th>
                                            <th class="d-none" scope="col">ScrollAnchor</th>
                                            <th class="d-none" scope="col">Units</th>
                                            <th class="d-none" scope="col">Status</th>
                                            <th class="d-none" scope="col">Description</th>
                                        </tr>
                                    </thead>
                                    <tbody class="stocks-dataset bg-white">
                                        <?php
                                        if (!empty($medicineDataSet)) 
                                        {
                                            foreach ($medicineDataSet as $row) 
                                            {
                                                $icon = $row['fas_icon'];
                                                $itemName = $row['item_name'];
                                                $category = $row['category'];
                                                $itemCode = $row['item_code'];

                                                $remaining = $row['remaining'];
                                                $criticalLevel = $row['critical_level'];

                                                $measurement = $row['measurement'];
                                                $supplier = $row['supplier_name'];
                                                $dateAdded = $row['date_added'];
                                                $remarks = $row['remarks'];

                                                $itemId = $row['id'];
                                                $itemID_Hashed = Crypto::encrypt(strval($itemId), $defuseKey);

                                                $stockLabelColor = "";
                                                $stockStatus = "";

                                                // make text appear yellow for critical items
                                                if ($remaining > 0 && $remaining <= $criticalLevel)
                                                {
                                                    $stockLabelColor = "stock-label-critical";
                                                    $stockStatus = "critical";
                                                }

                                                // make text appear red for sold out items
                                                if ($remaining == 0)
                                                {
                                                    $stockLabelColor = "stock-label-soldout";
                                                    $stockStatus = "soldout";
                                                }

                                                echo
                                                "<tr class=\"align-middle\">
                                                    <td>
                                                        <div class=\"form-check\">
                                                            <input class=\"form-check-input\" type=\"checkbox\" value=\"\" id=\"row-check-box\" />
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <img src=\"assets/images/inventory/$icon.png\" width=\"26\" height=\"26\">
                                                    </td>
                                                    <td class=\"fw-bold\">$itemName</td>
                                                    <td>$itemCode</td>
                                                    <td>$category</td>
                                                    <td>
                                                        <span class=\"$stockLabelColor\">$remaining $measurement</span
                                                    </td>
                                                    <td>$criticalLevel</td>
                                                    <td>
                                                        <div class=\"btn-group\">
                                                            <button type=\"button\" class=\"btn btn-primary btn-item-details px-2 text-center\">Details</button>
                                                            <button type=\"button\" class=\"btn btn-primary btn-split-arrow px-0 text-center dropdown-toggle dropdown-toggle-split\" data-mdb-toggle=\"dropdown\" aria-expanded=\"false\"></button>
                                                            <ul class=\"dropdown-menu dropdown-menu-custom-light-small\">
                                                                <li onclick=\"editItem('$itemID_Hashed')\" class=\"d-flex align-items-center gap-3 px-3 py-1 dropdown-item-custom-light\">
                                                                    <div class=\"dropdown-item-icon text-center\">
                                                                        <i class=\"fas fa-pen fs-6 text-warning\"></i>
                                                                    </div>
                                                                    <div class=\"fs-6\">Edit</div>
                                                                </li> 
                                                                <li onclick=\"deleteItem('$itemID_Hashed', '$itemName')\" class=\"d-flex align-items-center gap-3 px-3 py-1 dropdown-item-custom-light\">
                                                                    <div class=\"dropdown-item-icon text-center\">
                                                                        <i class=\"fas fa-trash fs-6 font-red\"></i>
                                                                    </div>
                                                                    <div class=\"fs-6\">Delete</div>
                                                                </li> 
                                                            </ul>
                                                        </div>
                                                    </td>
                                                    <td class=\"d-none\">$supplier</td>
                                                    <td class=\"d-none\">$dateAdded</td>
                                                    <td class=\"d-none\"></td>
                                                    <td class=\"d-none\">$measurement</td>
                                                    <td class=\"d-none\">$stockStatus</td>
                                                    <td class=\"d-none\">$remarks</td>
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
        <form action="<?= Navigation::$URL_EDIT_ITEM ?>" method="GET" class="frm-edit-item d-none">
            <input type="text" name="item-key" id="item-key">
            <input type="text" name="item-page" id="item-page">
        </form>

        <?php 
            $lastUpdated_ItemName = "";
            $lastUpdated_ItemPage = -1;
            
            if (isset($_SESSION['edit_item_success'], $_SESSION['edit_updated_item_name'], $_SESSION['edit_item_page'])) 
            {
                if ($_SESSION['edit_item_success'] === true)
                {
                    $lastUpdated_ItemName = $_SESSION['edit_updated_item_name'];
                    $lastUpdated_ItemPage = $_SESSION['edit_item_page'];
                }
            } 

            $deleteItem_Status = "";

            if (isset($_SESSION['delete-item-success'], $_SESSION['delete-item-status']))
            {
                if ($_SESSION['delete-item-success'] === true)
                    $deleteItem_Status = $_SESSION['delete-item-status'];
            }
        ?>
        
        <?php // Hidden fields, to hold the last updated item name and page index ?>
        <form class="frm-session-vars">
            <input type="text" class="d-none session-var-item-name" 
            value="<?= $lastUpdated_ItemName ?>">

            <input type="text" class="d-none session-var-item-page" 
            value="<?= $lastUpdated_ItemPage ?>">

            <input type="text" class="d-none session-var-delete-item-status"
            value="<?= $deleteItem_Status ?>">
        </form>

        <?php // Hidden form; This will handle an item's DELETE action ?>
        <form action="<?= Navigation::$ACTION_DELETE_ITEM ?>" method="POST" class="frm-delete-item d-none">
            <input type="text" name="item-key" id="item-key">
        </form>
        
    </div>
    <!-- END CONTAINER -->
 
    <?php

    unset(
        $_SESSION['edit_item_success'], 
        $_SESSION['edit_updated_item_name'], 
        $_SESSION['edit_item_page'],
        $_SESSION['delete-item-success'],
        $_SESSION['delete-item-status']
    );

    // modal window for showing the item details
    require_once("layouts/item-info-dialog.php");

    require_once("components/alert-dialog/alert-dialog.php");
    require_once("components/confirm-dialog/confirm-dialog.php");
    require_once("components/snackbar/snackbar.php");
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
    <script src="assets/js/stocks-inventory.js"></script>

    <script src="components/alert-dialog/alert-dialog.js"></script>
    <script src="components/confirm-dialog/confirm-dialog.js"></script>
    <script src="components/snackbar/snackbar.js"></script>
    
</body>

</html> 