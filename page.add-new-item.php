<?php
@session_start();

require_once("rootcwd.php");

require_once($rootCwd . "includes/urls.php");

// we must be logged in to view this page
if (!isset($_SESSION['isLoggedIn']) || $_SESSION['isLoggedIn'] !== true)
{
    header("Location: " . Navigation::$URL_LOGIN);
    exit;
}

// REGISTER THE FILES FOR INCLUDE
//define('def_incAddItem', TRUE);

require_once($rootCwd . "database/configs.php");
require_once($rootCwd . "includes/system.php");
require_once($rootCwd . "includes/utils.php");
require_once($rootCwd . "layout-header.php");

require_once($rootCwd . "includes/inc.add-item.php");

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
                    <div class="worksheet-wrapper p-4 w-100 h-100 overflow-hidden position-relative">

                        <div class="worksheet d-flex flex-column bg-white shadow-2-strong w-100 h-100 p-4 scrollable" style="overflow-y: auto;">

                            <!-- BREADCRUMB-->
                            <div class="breadcrumb w-100 d-flex flex-row align-items-center justify-content-start">
                                <h6 class="fas fa-folder-open font-accent"></h6>
                                <h6 class="ms-2 fw-bold">View</h6>
                                <h6 class="breadcrumb-arrow fas fa-play mx-3"></h6>
                                <h6 class="fas fa-warehouse font-teal"></h6>
                                <h6 class="ms-2 fw-bold">Stocks Inventory</h6>
                                <h6 class="breadcrumb-arrow fas fa-play mx-3"></h6>
                                <h6 class="fas fa-plus text-success"></h6>
                                <h6 class="ms-2 fw-bold">Add New Item</h6>
                            </div>

                            <!--MID SIZED BOX-->
                            <div class="outline-box-container d-flex align-items-center justify-content-center w-100">
                                <div class="forms-container p-2 w-75" style="max-width: 743px;">
                                    <!-- NOTE -->
                                    <div class="note note-warning mb-3">
                                        <div class="d-flex">
                                            <h6 class="fw-bold mb-2 me-auto">Guidelines on naming an item:</h6>
                                            <small class="text-primary fw-bold">
                                                <span class="note-action-clickable user-select-none">
                                                    Click to <span class="note-action">collapse</span>
                                                    <i class="fas note-action-icon fa-chevron-up"></i>
                                                </span>
                                            </small>
                                        </div>
                                        <ul class="list-unstyled note-guidelines">
                                            <li class="mb-1"><i class="fas fa-long-arrow-alt-right me-2 text-info"></i>An item name must be accurate and unique.</li>
                                            <li class="mb-1"><i class="fas fa-long-arrow-alt-right me-2 text-info"></i>Avoid overloading item names with more information than absolutely necessary.</li>
                                            <li><i class="fas fa-long-arrow-alt-right me-2 text-info"></i>Information such as supplier, category etc. should not be included in the item name.</li>
                                        </ul>
                                    </div>

                                    <!-- FORM -->
                                    <div class="form-wrapper">
                                        <form action="" method="POST" class="mb-4" id="main-form">
                                            <!-- ROW SECTION: ITEM PROPTS -->
                                            <div class="row mb-3">
                                                <h6 class="font-base">Item Properties</h6>
                                                <div class="col">
                                                    <div class="form-outline">
                                                        <input type="text" class="form-control input-item-name" maxlength="64"/>
                                                        <label class="form-label" for="form12">Item Name</label>
                                                    </div>

                                                </div>
                                                <div class="col">
                                                    <div class="select-box-wrapper">
                                                        <div class="form-outline">
                                                            <input type="text" class="form-control input-item-code"  maxlength="64"/>
                                                            <label class="form-label" for="form12">Item Code</label>
                                                        </div>

                                                    </div>
                                                </div>
                                                <div class="col">
                                                    <div class="dropdown">
                                                        <button class="btn btn-primary bg-base dropdown-toggle w-100" type="button" id="dropdownCategories" data-mdb-toggle="dropdown" aria-expanded="false">
                                                            Item Category
                                                        </button>
                                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                            <div class="dropdown-menu-scrollable">
                                                                <?php
                                                                if (count($categoriesDataSet) > 0) 
                                                                {
                                                                    foreach ($categoriesDataSet as $row) 
                                                                    {
                                                                        $id = $row["id"];
                                                                        $name = $row["name"];

                                                                        $value = Crypto::encrypt(strval($id), $defuseKey);
                                                                        $onclick = "setCategoryValue('$value', '$name')";

                                                                        echo "<li onclick=\"$onclick\">
                                                                        <span class=\"dropdown-item dropdown-item-custom-light\">$name</span>
                                                                        </li>";
                                                                    }
                                                                }
                                                                ?>
                                                            </div>
                                                        </ul>
                                                        <input type="text" class="input-category d-none">
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- ROW SECTION: STOCK DETAILS -->
                                            <div class="row mb-3">
                                                <h6 class="font-base">Stock Details</h6>
                                                <div class="col">
                                                    <div class="form-outline">
                                                        <input type="text" class="form-control input-total-stock" />
                                                        <label class="form-label" for="input-total-stock">Total Stock</label>
                                                    </div>
                                                </div>
                                                <div class="col">
                                                    <div class="form-outline">
                                                        <input type="text" class="form-control input-reserve-stock" />
                                                        <label class="form-label" for="input-reserve-stock">Reserved Stock</label>
                                                    </div>

                                                </div>
                                                <div class="col">
                                                    <div class="dropdown">
                                                        <button class="btn btn-primary bg-base dropdown-toggle w-100" type="button" id="dropdownUnits" data-mdb-toggle="dropdown" aria-expanded="false">
                                                            Unit Measurement
                                                        </button>
                                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                            <div class="dropdown-menu-scrollable">
                                                                <?php
                                                                if (count($unitsDataSet) > 0) 
                                                                {
                                                                    foreach ($unitsDataSet as $row) 
                                                                    {
                                                                        $id = $row["id"];
                                                                        $name = $row["measurement"];

                                                                        $value = Crypto::encrypt(strval($id), $defuseKey);
                                                                        $onclick = "setUnitsValue('$value', '$name')";

                                                                        echo "<li onclick=\"$onclick\">
                                                                        <span class=\"dropdown-item dropdown-item-custom-light\">$name</span>
                                                                        </li>";
                                                                    }
                                                                }
                                                                ?>
                                                            </div>
                                                        </ul>
                                                        <input type="text" class="input-units d-none">
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- ROW SECTION: OTHER INFORMATION -->
                                            <div class="row mb-3">
                                                <h6 class="font-base">Other Information</h6>
                                                <div class="col-4">
                                                    <div class="dropdown">
                                                        <button class="btn btn-outline-dark fw-bold border-control-2 dropdown-toggle w-100 text-truncate" type="button" id="dropdownSupplier" data-mdb-toggle="dropdown" aria-expanded="false">
                                                            Select Supplier
                                                        </button>
                                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                            <div class="dropdown-menu-scrollable">
                                                                
                                                                <?php
                                                                $noneEnc = Crypto::encrypt("none", $defuseKey);
                                                                $noneOnClick = "setSupplierValue('$noneEnc', 'None')";

                                                                echo "<li onclick=\"$noneOnClick\">
                                                                    <span class=\"dropdown-item dropdown-item-custom-light\">None</span>
                                                                </li>";

                                                                if (count($suppliersDataSet) > 0) 
                                                                {
                                                                    foreach ($suppliersDataSet as $row) 
                                                                    {
                                                                        $id = $row["id"];
                                                                        $name = $row["supplier_name"];

                                                                        $value = Crypto::encrypt(strval($id), $defuseKey);
                                                                        $onclick = "setSupplierValue('$value', '$name')";

                                                                        echo "<li onclick=\"$onclick\">
                                                                        <span class=\"dropdown-item dropdown-item-custom-light\">$name</span>
                                                                        </li>";
                                                                    }
                                                                }
                                                                ?>
                                                            </div>
                                                        </ul>
                                                        <input type="text" class="input-supplier d-none">
                                                    </div>


                                                </div>
                                                <div class="col">
                                                    <div class="form-outline">
                                                        <input type="text" class="form-control input-remarks"  maxlength="100"/>
                                                        <label class="form-label" for="input-remarks">Item Description (Optional)</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                        <div class="form-control-buttons text-end">
                                            <button type="button" class="btn btn-secondary fw-bold btn-cancel">Cancel</button>
                                            <button class="d-none btn-cancel-all" <?php echoOnclick(Navigation::$URL_STOCKS_INVENTORY); ?>></button>
                                            <button type="button" class="btn btn-primary btn-save">Save</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>

                </section>

            </section>
        </main>
        <!-- MAIN CONTENT -->

    </div>
    <!-- END CONTAINER -->

    <?php

    // modal window for showing the item details
    require_once("layouts/item-info-dialog.php");

    require_once("components/alert-dialog/alert-dialog.php");
    require_once("components/snackbar/snackbar.php");
    require_once("components/confirm-dialog/confirm-dialog.php");
    ?>

    <!--SCRIPTS-->
    <script src="assets/lib/jquery/jquery-3.6.1.min.js"></script> 
    <script src="assets/lib/mdb5/js/mdb.min.js"></script>
    <script src="assets/lib/momentjs/moment-with-locales.js"></script>
    <script src="assets/lib/jquery.nicescroll/jquery.nicescroll.min.js"></script>

    <script src="assets/js/nicescroll.js"></script>
    <script src="assets/js/add-items.js"></script>
    <script src="assets/js/base-ui.js"></script>
    <script src="assets/js/system.js"></script>

    <script src="components/alert-dialog/alert-dialog.js"></script>
    <script src="components/confirm-dialog/confirm-dialog.js"></script>
    <script src="components/snackbar/snackbar.js"></script>

</body>

</html>