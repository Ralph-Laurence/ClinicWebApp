<?php
@session_start();

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

require_once($rootCwd . "includes/inc.get-restock-items.php");
require_once($rootCwd . "includes/inc.get-waste-items.php");

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
                setActiveLink(Navigation::$NavIndex_Restock);

                require_once("layouts/navigation.php");
                ?>

                <!--WORKAREA-->
                <section class="workarea w-100 pb-4">

                    <!--WELCOME BANNER-->
                    <?php include_once("layouts/welcome-banner.php"); ?>

                    <!--THE WORKSHEET WRAPPER-->
                    <div class="worksheet-wrapper p-2 w-100 h-100 overflow-hidden position-relative">

                        <div class="worksheet d-flex flex-column bg-white shadow-2-strong w-100 h-100 p-4 scrollable" style="overflow-y: auto;">

                            <!-- BREADCRUMB-->
                            <div class="breadcrumb w-100 d-flex flex-row align-items-center justify-content-start">
                                <h6 class="fas fa-laptop font-teal"></h6>
                                <h6 class="ms-2 fw-bold">Transaction</h6>
                                <h6 class="breadcrumb-arrow fas fa-play mx-3"></h6>
                                <h6 class="fas fa-cube font-hilight"></h6>
                                <h6 class="ms-2 fw-bold">Restock</h6>
                            </div>

                            <!--CARDS PARENT WRAPPER-->
                            <div class="cards-parent-container d-flex flex-row flex-wrap gap-2">
                                <!-- STOCK IN CARD -->
                                <div class="card standard-item-card">
                                    <div class="text-center pt-2">
                                        <img src="assets/images/icn_stockin.png" width="96" height="96" alt="Box" />
                                    </div>
                                    <div class="card-body">
                                        <h5 class="card-title">Stock In</h5>
                                        <p class="card-text">Replenish the inventory with fresh items or supplies.</p>
                                    </div>
                                    <div class="card-card-footer px-4 pb-4">
                                        <button type="button" class="btn btn-primary w-100 bg-base card-stockin-button" data-mdb-toggle="modal" data-mdb-target="#itemPickerModal">
                                            Stock In
                                        </button>
                                    </div>
                                </div>
                                <!-- STOCK OUT CARD -->
                                <div class="card standard-item-card">
                                    <div class="text-center pt-2">
                                        <img src="assets/images/icn_stockout.png" width="96" height="96" alt="Box" />
                                    </div>
                                    <div class="card-body">
                                        <h5 class="card-title">Stock Out</h5>
                                        <p class="card-text">Pull out items or stocks from the inventory.</p>
                                    </div>
                                    <div class="card-card-footer px-4 pb-4">
                                        <button type="button" class="btn btn-danger bg-red w-100 card-stockout-button" data-mdb-toggle="modal" data-mdb-target="#itemPickerModal">
                                            Stock Out
                                        </button>
                                    </div>
                                </div>
                                <!-- WASTE CARD -->
                                <div class="card standard-item-card">
                                    <div class="text-center pt-2">
                                        <img src="assets/images/icn_waste.png" width="96" height="96" alt="Box" />
                                    </div>
                                    <div class="card-body">
                                        <h5 class="card-title">Waste</h5>
                                        <p class="card-text">Defective items or stocks will be disposed here.</p>
                                    </div>
                                    <div class="card-card-footer px-4 pb-4">
                                        <button type="button" class="btn btn-success w-100" data-mdb-toggle="modal" data-mdb-target="#wasteModal">Waste</button>
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>

                </section>

            </section>
        </main>
        <!-- MAIN CONTENT -->

        <!-- ITEMS PICKER MODAL -->
        <div class="modal fade" id="itemPickerModal" tabindex="-1" aria-labelledby="itemPickerModalLabel" aria-hidden="true" data-mdb-backdrop="static">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">

                    <div class="modal-header bg-base text-white py-0 ps-4 pe-0">
                        <h6 class="modal-title" id="stockDetailsModalLabel">Select an item ...</h6>
                        <button type="button" class="btn shadow-0 fs-5 text-white item-select-modal-close" data-mdb-dismiss="modal">
                            <i class="fas fa-times-circle"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="control-ribbon-wrapper d-flex align-items-center mb-2 pb-3 border-bottom">
                            <!-- PAGINATOR -->
                            <div class="d-flex align-items-center me-auto">
                                <div class="me-1 d-inline">Show</div>
                                <div class="entries-paginator-container"></div>
                                <div class="ms-1 d-inline">entries</div>
                            </div>
                            <!--SEARCHBAR-->
                            <div class="pagination-search-container">
                                <div class="form-outline">
                                    <input type="text" id="items-pagination-search-bar" class="form-control" />
                                    <label class="form-label" for="items-pagination-search-bar">Find Item</label>
                                </div>
                            </div>
                        </div>
                        <!-- ITEMS TABLE -->
                        <div class="table-wrapper items-table-wrapper" style="overflow-y: auto; height: 400px;">
                            <!-- PROGRESS BAR SPINNER -->
                            <div class="progress-loader-wrapper display-none">
                                <div class="progress-loader d-flex align-items-center justify-content-center my-3">
                                    <div class="progress-spinner spin-color"></div>
                                    <div class="mx-2 text-muted fs-6">Loading item data</div>
                                    <div class="dot-loader-wrapper pt-2">
                                        <div class="dot-loader"></div>
                                        <div class="dot-loader"></div>
                                        <div class="dot-loader"></div>
                                    </div>
                                </div>
                            </div>
                            <!-- DATA TABLE -->
                            <table class="table table-striped table-hover items-table">
                                <thead>
                                    <th class="d-none">Icon</th>
                                    <th class="d-none">Item</th>
                                    <th class="d-none">Category</th>
                                    <th class="d-none">Stock</th>
                                    <th class="d-none">Action</th>
                                </thead>
                                <tbody>

                                    <?php

                                    if (!empty($medicineDataSet)) {
                                        foreach ($medicineDataSet as $row) {
                                            $icon = $row['fas_icon'];
                                            $id = $row['id'];
                                            $itemName = $row['item_name'];
                                            $itemCode = $row['item_code'];
                                            $category = $row['category'];
                                            $remaining = $row['remaining'];
                                            $criticalLevel = $row['critical_level'];
                                            $measurement = $row['measurement'];

                                            $stockLabelColor = "";
                                            $stockStatus = "";

                                            // make text appear yellow for critical items
                                            if ($remaining > 0 && $remaining <= $criticalLevel) {
                                                $stockLabelColor = "stock-label-critical";
                                                $stockStatus = "critical";
                                            }

                                            // make text appear red for sold out items
                                            if ($remaining == 0) {
                                                $stockLabelColor = "stock-label-soldout";
                                                $stockStatus = "soldout";
                                            }

                                            // encrypt the item id
                                            $itemKey = Crypto::encrypt(strval($id), $defuseKey);

                                            echo "<tr class=\"align-middle\">
                                                <td scope=\"row\">
                                                    <img src=\"assets/images/inventory/$icon.png\" width=\"32\" height=\"32\">
                                                </td>
                                                <td>
                                                    <h6 class=\"fw-bold mb-2\">$itemName</h6>
                                                    <div class=\"d-flex align-items-center\">
                                                        <img src=\"assets/images/icons/tags.png\" width=\"16\" height=\"16\">
                                                        <small class=\"font-base ms-2\">$itemCode</small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <small class=\"font-teal\"><i class=\"fas fa-layer-group me-2\"></i>$category</small>
                                                </td>
                                                <td>
                                                    <span class=\"$stockLabelColor\">$remaining $measurement</span
                                                </td>
                                                <td>
                                                    <button type=\"button\" class=\"btn btn-link btn-sm btn-rounded fw-bold\" onclick=\"loadItemInfo('$itemKey')\">
                                                        Select
                                                    </button>
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
                        <button type="button" class="btn btn-secondary fw-bold item-select-modal-close" data-mdb-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- RESTOCK THE SELECTED ITEM HERE -->
        <div class="modal fade" id="restockModal" tabindex="-1" aria-labelledby="restockModalLabel" aria-hidden="true" data-mdb-backdrop="static">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-base text-white py-0 ps-4 pe-0">
                        <h6 class="modal-title" id="restockModalLabel">Restock</h6>
                        <button type="button" class="btn shadow-0 fs-5 text-white" data-mdb-dismiss="modal">
                            <i class="fas fa-times-circle"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3 d-flex align-items-center gap-2">
                            <div class="item-icon"></div>
                            <div class="fs-5 lbl-item-name flex-wrap">Item Name</div>
                        </div>
                        <div class="mb-2 section-item-information">

                            <!--ITEM CODE-->
                            <div class="row">
                                <div class="col-1 align-middle">
                                    <img src="assets/images/icons/tags.png" alt="tag" width="20" height="20">
                                </div>
                                <div class="col-3">Item Code:</div>
                                <div class="col">
                                    <div class="fs-6 font-base lbl-item-code">Code</div>
                                </div>
                            </div>
                            <!--ITEM CATEGORY-->
                            <div class="row">
                                <div class="col-1 align-middle">
                                    <i class="fas fa-layer-group font-teal"></i>
                                </div>
                                <div class="col-3">Category:</div>
                                <div class="col">
                                    <div class="fs-6 font-teal lbl-category">Category</div>
                                </div>
                            </div>
                            <!--ITEM STOCKS-->
                            <div class="row mb-3">
                                <div class="col-1 align-middle">
                                    <i class="fas fa-boxes text-warning"></i>
                                </div>
                                <div class="col-3">Total Stock:</div>
                                <div class="col">
                                    <div class="fs-6 text-dark lbl-stock">Stock</div>
                                </div>
                            </div>
                            <!--RESTOCK AMOUNT-->
                            <div class="action-indicator mb-3 mt-4 d-flex gap-2 align-items-center">
                                <img src="assets/images/icn_stockin.png" class="restock-mode-icon" alt="icon" width="24" height="24">
                                <div class="fs-6 text-uppercase fw-bold font-teal lbl-restock-mode">Restock Amount</div>
                            </div>
                            <div class="row">

                                <div class="col-4">
                                    <div class="form-outline">
                                        <input type="text" id="restock-input-amount" class="form-control" value="1" required />
                                        <label class="form-label" for="restock-input-amount">Enter Amount</label>
                                    </div>
                                </div>
                                <div class="col-2">
                                    <button type="button" class="btn btn-danger bg-red btn-stockin-minus">
                                        <i class="fas fa-minus text-white"></i>
                                    </button>
                                </div>
                                <div class="col-2">
                                    <button type="button" class="btn btn-success bg-teal btn-stockin-plus">
                                        <i class="fas fa-plus text-white"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="form-check form-check-add-to-waste mt-3 display-none">
                                <input class="form-check-input" type="checkbox" id="chk_addToWaste" checked />
                                <label class="form-check-label" for="chk_addToWaste">Add to waste</label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer  py-2">
                        <button type="button" class="btn btn-secondary fw-bold" data-mdb-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary bg-base btn-restock-submit">OK</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- WASTE MODAL -->
        <div class="modal fade" id="wasteModal" tabindex="-1" aria-labelledby="wasteModalLabel" aria-hidden="true" data-mdb-backdrop="static">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">

                    <div class="modal-header bg-green text-white py-0 ps-4 pe-0">
                        <h6 class="modal-title" id="wasteModalLabel">Inventory Waste</h6>
                        <button type="button" class="btn shadow-0 fs-5 text-white" data-mdb-dismiss="modal">
                            <i class="fas fa-times-circle"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="control-ribbon-wrapper row mb-2 pb-3 border-bottom">
                            <!-- PAGINATOR -->
                            <div class="d-flex align-items-center col-4">
                                <div class="me-1 d-inline">Show</div>
                                <div class="waste-entries-paginator-container"></div>
                                <div class="ms-1 d-inline">entries</div>
                            </div>
                            <!--SEARCHBAR-->
                            <div class="waste-pagination-search-container col">
                                <div class="form-outline">
                                    <input type="text" id="waste-pagination-search-bar" class="form-control" />
                                    <label class="form-label" for="waste-pagination-search-bar">Find Item</label>
                                </div>
                            </div>
                            <!-- 'DISPOSE ALL' BUTTON -->
                            <div class="col-3">
                                <button type="button" onclick="disposeAll()" class="btn btn-link font-red btn-sm btn-rounded">
                                    <i class="fas fa-trash me-2"></i>
                                    Dispose All
                                </button>
                            </div>
                        </div>
                        <!-- WASTE TABLE -->
                        <div class="table-wrapper waste-table-wrapper" style="overflow-y: auto; height: 400px;">

                            <!-- DATA TABLE -->
                            <table class="table table-striped table-hover waste-table">
                                <thead>
                                    <th class="d-none">Icon</th>
                                    <th class="d-none">Item</th>
                                    <th class="d-none">Amount</th>
                                    <th class="d-none">Action</th>
                                    <th class="d-none">ItemKey</th>
                                </thead>
                                <tbody>

                                    <?php

                                    if (!empty($wasteDataSet)) {
                                        foreach ($wasteDataSet as $row) {
                                            $icon = $row['fas_icon'];
                                            $id = $row['item_id'];
                                            $itemName = $row['item_name'];
                                            $itemCode = $row['item_code'];
                                            $category = $row['category'];
                                            $amount = $row['amount'];
                                            $measurement = $row['measurement'];

                                            // encrypt the item id
                                            $itemKey = Crypto::encrypt(strval($id), $defuseKey);

                                            echo "<tr class=\"align-middle\">
                                                <td scope=\"row\">
                                                    <img src=\"assets/images/inventory/$icon.png\" width=\"32\" height=\"32\">
                                                </td>
                                                <td>
                                                    <h6 class=\"fw-bold mb-2\">$itemName</h6>
                                                    <div class=\"d-flex align-items-center\">
                                                        <img src=\"assets/images/icons/tags.png\" width=\"16\" height=\"16\">
                                                        <small class=\"font-base ms-2\">$itemCode</small>
                                                    </div>
                                                    <small class=\"font-teal\"><i class=\"fas fa-layer-group me-2\"></i>$category</small>
                                                </td> 
                                                <td>
                                                    $amount $measurement
                                                </td>
                                                <td>
                                                    <button type=\"button\" onclick=\"restoreWaste('$itemKey')\"
                                                    class=\"btn btn-primary bg-base btn-sm btn-rounded me-2\">
                                                        <i class=\"fas fa-undo me-2\"></i>
                                                        Restore
                                                    </button>
                                                    <button type=\"button\" onclick=\"disposeWaste('$itemKey')\"
                                                    class=\"btn btn-danger bg-red btn-sm btn-rounded\">
                                                        <i class=\"fas fa-trash me-2\"></i>
                                                        Dispose
                                                    </button>
                                                </td>
                                                <td class=\"d-none\">$itemKey</td>
                                            </tr>";
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>

                        </div>
                    </div>
                    <div class="modal-footer  py-2">
                        <button type="button" class="btn btn-warning bg-amber text-dark fw-bold" data-mdb-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <!-- END CONTAINER -->

    <?php // HIDDEN FORM FOR PROCESSING STOCK IN / STOCK OUT ACTION 
    ?>
    <form action="<?= Navigation::$ACTION_RESTOCK ?>" class="frm-restock d-none" method="post">
        <input type="text" name="itemKey" id="itemKey">
        <input type="text" name="amount" id="amount" value="1">
        <input type="text" name="actionMode" id="actionMode">
        <input type="text" name="isAddToWaste" id="isAddToWaste" value="1">
    </form>

    <?php
    // Hidden form that handles the restock message action 
    $restockActionMessage = "";

    if (isset($_SESSION['restockMessage']))
        $restockActionMessage = $_SESSION['restockMessage'];
    ?>
    <input type="text" name="restock-status" class="restock-status d-none" value="<?= $restockActionMessage ?>">

    <?php // Hidden form that handles inventory waste action 
    ?>
    <form action="<?= Navigation::$ACTION_WASTE ?>" method="post" class="frm-waste d-none">
        <input type="text" name="itemKey" id="itemKey">
        <input type="text" name="actionMode" id="actionMode">
    </form>

    <?php

    // clear the restock messages
    unset($_SESSION['restockMessage']);

    // modal window for showing the item details
    require_once("layouts/item-info-dialog.php");

    require_once("components/alert-dialog/alert-dialog.php");
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
    <script src="assets/js/restock.js"></script>

    <script src="components/alert-dialog/alert-dialog.js"></script>
    <script src="components/snackbar/snackbar.js"></script>

</body>

</html>