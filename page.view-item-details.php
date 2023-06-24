<?php
require_once("rootcwd.php");
require_once($rootCwd . "controllers/ItemDetailsController.php");
?>

<body>

    <?php $masterPage->beginWorkarea(Navigation::NavIndex_Stocks); //BEGIN WORKAREA LAYOUT 
    ?>

    <!-- MAIN WORKAREA -->
    <div class="main-workarea flex-grow-1 d-flex flex-column h-100 bg-white shadow-2-strong overflow-hidden pt-2 px-4 pb-3">

        <!-- BREADCRUMB-->
        <div class="breadcrumbs w-100 d-flex flex-row align-items-center justify-content-start">
            <img src="assets/images/icons/sidenav-view.png" width="20" height="20">
            <div class="fs-6 fw-bold ms-2">View</div>
            <div class="breadcrumb-arrow fsz-12 fas fa-play mx-3"></div>
            <img src="assets/images/icons/sidenav-inventory.png" width="20" height="20">
            <div class="ms-2 fw-bold fs-6">Medicine Inventory</div>
            <div class="breadcrumb-arrow fsz-12 fas fa-play mx-3"></div>
            <img src="assets/images/icons/bcrumb-details.png" width="20" height="20">
            <div class="ms-2 fw-bold fs-6">Item Details</div>
        </div>

        <!--DIVIDER-->
        <hr class="hr divider my-2">

        <div class="container-fluid h-100 p-0 overflow-hidden mb-2">
            <div class="row h-100 flex-grow-1">

                <div class="col overflow-hidden h-100">
                    <div class="p-2 w-100 h-100 col-child-wrapper">

                        <div class="w-100 h-100 d-flex flex-column shadow-2-strong bg-white">

                            <div class="flex-grow-1 d-flex flex-column h-100 overflow-hidden pb-2">
                                <div data-simplebar class="w-100 h-100 overflow-y-auto no-native-scroll datatable-wrapper display-nonex effect-reciever" data-transition-index="2" data-transition="fadein">
                                    <div class="d-flex flex-row p-3">
                                        <div class="ms-0 me-4">
                                            <div class="border rounded-2">
                                                <img src="<?= loadItemImage() ?>" width="150" height="150">
                                            </div>
                                        </div>
                                        <div class="flex-fill text-wrap">
                                            <div class="d-flex align-items-center mb-2">
                                                <a class="btn btn-secondary p-0 me-auto bg-white" href="<?= $goBack ?>" role="button">
                                                    <i class="fas fa-arrow-left me-1"></i>
                                                    Back to inventory
                                                </a>
                                                <a class="btn btn-secondary p-0 bg-white btn-edit" role="button">
                                                    <i class="fas fa-pen me-1"></i>
                                                    Edit this item
                                                </a>
                                            </div>
                                            <div class="fs-4 mb-2 label-item-name"><?= loadItemName() ?></div>
                                            <div class="fs-6 text-muted text-uppercase mb-2"><?= loadCategory() ?></div>
                                            <div class="text-muted item-description fsz-14 mb-2">
                                                <i class="fas fa-info-circle me-1"></i>
                                                <?= loadDescription() ?>
                                            </div>
                                             
                                            <div class="fsz-14 mb-2 d-flex flex-row align-items-center">
                                                <div class="ms-0" style="width: 80px;">Supplier:</div>
                                                <div class="text-muted flex-fill text-wrap font-base ms-1 me-auto w-25"><?= loadSupplier() ?></div> 
                                                <?php loadCondition() ?> 
                                            </div> 
                                            <hr class="hr my-2">
                                            <div class="d-flex gap-3">
                                                <h6 class="rounded-7 px-2 py-1 border border-control-2">Stock Details</h6>
                                                <h6 class="rounded-7 px-2 py-1 border border-control-2">
                                                    <span class="text-primary"><?= loadStock() ?>(s)</span><span class="text-muted ms-1">Total</span>
                                                </h6>
                                                <h6 class="rounded-7 px-2 py-1 border border-control-2">
                                                    <span class="text-primary"><?= loadReserve() ?>(s)</span><span class="text-muted ms-1">Reserved</span>
                                                </h6>
                                            </div>
                                            <div class="d-flex align-items-center flex-row gap-2">
                                                <table class="table table-striped table-hover table-fixed">
                                                    <thead>
                                                        <tr> 
                                                            <td class="fw-bold" style="width: 20%;">Entry Date</td>
                                                            <td class="fw-bold" style="width: 30%">Stock Code</td>
                                                            <td class="fw-bold" style="width: 10%">Quantity</td>
                                                            <td class="fw-bold" style="width: 20%">Expiry Date</td> 
                                                            <td class="fw-bold" style="width: 15%">Action</td> 
                                                            <td class="d-none"></td> 
                                                        </tr>
                                                    </thead>
                                                    <tbody class="stocks-dataset-body">
                                                        <?php loadStocks() ?>
                                                    </tbody>
                                                </table> 
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-1"></div>

            </div>

        </div>
    </div>
    <form action="<?= Tasks::UPDATE_STOCK_EXPIRY ?>" method="POST" class="frm-edit-expiry d-none">
        <input type="text" name="item-key" class="item-key" value="<?= $itemKey ?>">
        <input type="text" name="stock-key" class="stock-key" value="">
        <input type="text" name="new-expiry" class="new-expiry" value="">
    </form>
    <form action="<?= Pages::EDIT_ITEM ?>" method="POST" class="frm-edit d-none">
        <input type="text" name="item-key" class="item-key" value="<?= $itemKey ?>">
    </form>
    <form action="<?= Tasks::DISCARD_STOCK ?>" method="post" class="frm-discard d-none">
        <input type="text" name="item-key" class="item-key" value="<?= $itemKey ?>">
        <input type="text" name="stock-key" class="stock-key" value="">
        <input type="text" name="sender-key" value="<?= setSenderKey() ?>">
    </form>
    <textarea class="success-message d-none"><?= getSuccessMessage(); ?></textarea>
    <textarea class="error-message d-none"><?= getErrorMessage(); ?></textarea>
    <!-- <form action="<?php //= Tasks::DISCARD_ITEM ?>" method="post" class="frm-discard d-none">
        <input type="text" name="item-key" class="item-key" value="<?php //= $itemKey ?>">
        <input type="text" name="sender-key" value="<?php //= setSenderKey() ?>">
    </form> -->
    <form action="<?= Pages::ITEM_DETAILS ?>" class="frm-details d-none" method="POST">
        <input type="text" name="details-key" id="details-key" value="<?= getItemKey() ?>">
        <input type="text" name="filter" id="filter">
    </form>
    <?php
    $masterPage->endWorkarea(); //END WORKAREA LAYOUT 
    $masterPage->includeDialogs(true, true, true, false);
    ?>

    <!--SCRIPTS-->
    <script src="assets/lib/jquery/jquery-3.6.1.min.js"></script>
    <script src="assets/lib/jquery-ui-1.13.2.custom/jquery-ui.min.js"></script>
    <script src="assets/lib/datatables/datatables.min.js"></script>
    <script src="assets/lib/mdb5/js/mdb.min.js"></script>
    <script src="assets/lib/momentjs/moment-with-locales.js"></script>
    <script src="assets/lib/jquery.nicescroll/jquery.nicescroll.min.js"></script>
    <script src="assets/lib/simplebar/simplebar.min.js"></script>

    <script src="assets/js/nicescroll.js"></script>
    <script src="assets/js/system.js"></script>
    <script src="assets/js/base-ui.js"></script>
    <script src="assets/js/page.view-item-details.js"></script>

    <script src="components/alert-dialog/alert-dialog.js"></script> 
    <script src="components/confirm-dialog/confirm-dialog.js"></script> 
    <script src="components/snackbar/snackbar.js"></script> 

</body>

</html>