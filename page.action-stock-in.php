<?php require_once("controllers/Action_StockInController.php"); ?>

<body>

    <?php $masterPage->beginWorkarea(Navigation::NavIndex_Restock); //BEGIN WORKAREA LAYOUT 
    ?>

    <!-- MAIN WORKAREA -->
    <div class="main-workarea flex-grow-1 d-flex flex-column h-100 bg-white shadow-2-strong overflow-hidden px-4 py-2">


        <!-- BREADCRUMB-->
        <div class="breadcrumbs mb-2 w-100 d-flex flex-row align-items-center justify-content-start">
            <img src="assets/images/icons/sidenav-transaction.png" width="20" height="20">
            <div class="fs-6 fw-bold ms-2">Transaction</div>
            <div class="breadcrumb-arrow fsz-12 fas fa-play mx-3"></div>
            <img src="assets/images/icons/sidenav-restock.png" width="20" height="20">
            <div class="ms-2 fw-bold fs-6">Restock</div>
            <div class="breadcrumb-arrow fsz-12 fas fa-play mx-3"></div>
            <img src="assets/images/icn_stockin.png" width="20" height="20">
            <div class="ms-2 fw-bold fs-6">Stock In</div>
        </div>

        <!--DIVIDER-->
        <div class="ribbon-divider my-2"></div>

        <div class="row px-3">
            <div class="col bg-white shadow-2-strong p-2">
                <div class="d-flex flex-row justify-content-left gap-2">
                    <img src="assets/images/icons/bcrumb-details.png" width="20" height="20">
                    <h6>Item Information</h6>
                </div>
                <div class="my-2 details-card text-center">
                    <div class="d-flex align-items-center justify-content-center flex-column">
                        <div class="image-wrapper  d-flex">
                            <div class="border rounded-2">
                                <img src="<?= getPreviewImage() ?>" width="128" height="128">
                            </div>
                        </div>
                        <div class="text-wrap px-2 my-2">
                            <h5 class="text-primary"><?= getItemName() ?></h5>
                            <h6 class="text-muted"><?= getCategory() ?></h6>
                        </div>
                    </div>
                </div>
                <hr class="my-2">
                <div class="d-flex flex-row gap-2">
                    <h6 class="font-base me-auto w-50">Total Stock:</h6>
                    <?= getRemaining() ?>
                </div>
                <div class="d-flex flex-row gap-2">
                    <h6 class="font-base me-auto w-50">Restock Point:</h6>
                    <h6 class="text-wrap text-muted fw-normal flex-fill"><?= getRestockPoint() ?></h6>
                </div>
            </div>
            <div class="col mx-3 bg-white shadow-2-strong p-2">
                <div class="d-flex flex-row justify-content-left gap-2">
                    <img src="assets/images/icons/bcrumb-edit.png" width="20" height="20">
                    <h6>Stock Entry</h6>
                </div>
                <div class="my-2 input-groups-wrapper">
                    <form action="<?= Tasks::STOCK_IN ?>" class="frm-stockin" method="POST">
                        <div class="input-group mb-3">
                            <span class="input-group-text input-group-text-bg" id="basic-addon1" style="width: 140px; max-width: 140px;">SKU/Item Code</span>
                            <input type="text" class="form-control input-sku text-uppercase text-primary" name="sku" aria-describedby="basic-addon1" required />
                        </div>
                        <div class="input-group mb-3">
                            <span class="input-group-text input-group-text-bg" id="basic-addon2" style="width: 140px; max-width: 140px;">Stock Quantity</span>
                            <input type="text" class="form-control input-qty numeric" name="qty" aria-describedby="basic-addon2" required maxlength="5" />
                        </div>
                        <div class="input-group mb-3">
                            <span class="input-group-text input-group-text-bg" id="basic-addon3" style="width: 140px; max-width: 140px;">Expiry Date</span>
                            <input type="text" class="form-control input-expiry bg-white" name="expiry" aria-describedby="basic-addon3" required readonly />
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="" id="expiry-check" name="expiry-check" />
                            <label class="form-check-label" for="expiry-check">No expiry date</label>
                        </div>
                        <div class="error-box display-none bg-red-light font-red-dark fsz-14 p-2 rounded-2 mb-2">
                            Error
                        </div>
                        <div class="text-end">
                            <button type="button" class="btn btn-primary btn-base btn-save">Save</button>
                        </div>
                        <input type="hidden" name="item-key" value="<?= getItemKey() ?>">
                    </form>
                </div>
            </div>
            <div class="col bg-white shadow-2-strong p-2 overflow-hidden">
                <div class="d-flex flex-row justify-content-left gap-2">
                    <img src="assets/images/icons/option-icon-export.png" width="20" height="20">
                    <h6>Stock on hand</h6>
                </div>
                <div data-simplebar class="w-100 h-100 overflow-y-auto no-native-scroll">
                    <div class="table-wrapper mb-2 border" style="overflow-y: auto; max-height: 300px;">
                        <table class="table table-striped table-fixed table-sm">
                            <thead class="style-secondary position-sticky top-0 z-10">
                                <tr class="align-middle">
                                    <td class="text-center">Stock</td>
                                    <td class="text-center">Qty</td>
                                </tr>
                            </thead>
                            <tbody>
                                <?php getStockOnHand() ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="d-none">
        <textarea class="server-response"><?php getErrorMessage(); ?></textarea>
    </div>

    <?php $masterPage->endWorkarea(); //END WORKAREA LAYOUT 

    $masterPage->includeDialogs(true, false, false, false);
    ?>

    <!--SCRIPTS-->
    <script src="assets/lib/jquery/jquery-3.6.1.min.js"></script>
    <script src="assets/lib/jquery-ui-1.13.2.custom/jquery-ui.min.js"></script>
    <script src="assets/lib/datatables/datatables.min.js"></script>
    <script src="assets/lib/mdb5/js/mdb.min.js"></script>
    <script src="assets/lib/momentjs/moment-with-locales.js"></script>
    <script src="assets/lib/simplebar/simplebar.min.js"></script>

    <script src="components/alert-dialog/alert-dialog.js"></script>

    <script src="assets/js/base-ui.js"></script>
    <script src="assets/js/system.js"></script>
    <script src="assets/js/page.action-stock-in.js"></script>
    <script src="assets/js/workarea.commons.js"></script>
    <script src="assets/js/shared-effects.js"></script>

</body>

</html>