<?php require_once("controllers/Action_StockOutController.php"); ?>

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
            <img src="assets/images/icn_stockout.png" width="20" height="20">
            <div class="ms-2 fw-bold fs-6">Stock Out</div>
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
            <div class="col mx-3 bg-white shadow-2-strong p-2 overflow-hidden">
                <div class="d-flex flex-row justify-content-left gap-2">
                    <img src="assets/images/icons/option-icon-export.png" width="20" height="20">
                    <h6>Stocks list</h6>
                </div>
                <div data-simplebar class="w-100 h-100 overflow-y-auto no-native-scroll">
                    <div class="table-wrapper mb-2 border" style="overflow-y: auto; max-height: 300px;">
                        <table class="table table-striped table-fixed table-sm">
                            <thead class="style-secondary position-sticky top-0 z-10">
                                <tr class="align-middle">
                                    <td class="text-center">Stock</td>
                                    <td class="text-center">Qty</td>
                                    <td class="d-none">Stock Key</td>
                                </tr>
                            </thead>
                            <tbody class="stocks-tbody">
                                <?php getStockOnHand() ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col bg-white shadow-2-strong p-2">
                <div class="d-flex flex-row justify-content-left gap-2">
                    <img src="assets/images/icons/bcrumb-edit.png" width="20" height="20">
                    <h6>Pull out stock</h6>
                </div>
                <div class="my-2 input-groups-wrapper">
                    <form action="<?= Tasks::STOCK_OUT ?>" class="frm-stockout" method="POST">
                        <div class="bg-light-blue rounded-2 border-primary border fsz-14 text-primary mb-2 p-2">
                            <i class="fas fa-info-circle me-1"></i>
                            Select a stock from stocks list on the left
                        </div>
                        <div class="input-group mb-2">
                            <span class="input-group-text input-group-text-bg" id="basic-addon1" style="width: 100px; max-width: 100px;">Stock</span>
                            <input type="text" class="form-control bg-white input-sku text-uppercase text-primary" aria-describedby="basic-addon1" readonly />
                        </div>
                        <div class="input-group mb-2">
                            <span class="input-group-text input-group-text-bg" id="basic-addon2" style="width: 100px; max-width: 100px;">Amount</span>
                            <input type="text" class="form-control input-qty numeric initial-lock" name="qty" aria-describedby="basic-addon2" required maxlength="5" disabled />
                            <div class="input-group-text">
                                <div class="form-check">
                                    <input class="form-check-input initial-lock" type="checkbox" value="" id="check-all-qty" disabled />
                                    <label class="form-check-label" for="check-all-qty">All</label>
                                </div>
                            </div>
                        </div>
                        <div class="input-group mb-2">
                            <div class="input-group-text" id="basic-addon2">
                                <div class="form-check">
                                    <input class="form-check-input initial-lock" type="checkbox" value="" id="check-disposal" disabled />
                                    <label class="form-check-label" for="check-disposal">Dispose</label>
                                </div>
                            </div>
                            <select name="waste-reason" id="waste-reason" class="waste-reason" disabled>
                                <option value="" selected disabled>Disposal Reason</option>
                                <?= getWasteReasons() ?>
                            </select>
                        </div>
                        <div class="help-link-label d-flex">
                            <small class="fst-italic px-2 py-1 rounded-6" data-mdb-toggle="modal" data-mdb-target="#helpModal">
                                <i class="fas fa-question-circle"></i>
                                Help
                            </small>
                        </div>
                        <div class="error-box display-none bg-red-light font-red-dark fsz-14 p-2 rounded-2 mb-2">
                            Error
                        </div>
                        <div class="text-end">
                            <button type="button" class="btn btn-primary btn-base btn-save initial-lock" disabled>Save</button>
                        </div>
                        <input type="hidden" name="input-item-key" class="input-item-key" value="<?= getItemKey() ?>">
                        <input type="hidden" name="input-stock-key" class="input-stock-key" value="">
                        <textarea name="payload" class="d-none payload"></textarea>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="helpModal" tabindex="-1" aria-labelledby="helpModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header py-2">
                    <h6 class="modal-title" id="helpModalLabel">
                        <i class="fas fa-question-circle text-primary"></i>
                        Help
                    </h6>
                </div>
                <div class="modal-body">
                    <p class="text-muted">
                        The "Stocks list" shows all stocks of the current item.
                    </p>
                    <ul class="list-unstyled">
                        <li class="mb-1"><i class="fas fa-dot-circle me-2 text-primary"></i><span class="font-base">Click on each row item from the list.</span><br> 
                            <ul class="list-unstyled ms-5">
                                <li class="mb-1">
                                    <i class="fas fa-chevron-right me-2 text-primary"></i><span class="text-muted">Note: You can't select an empty or expired stock</span>
                                </li>
                                <li class="mb-1">
                                    <i class="fas fa-chevron-right me-2 text-primary"></i><span class="text-muted">Upon selecting, the selected stock will be loaded into "Pull out stock" tab.</span>
                                </li> 
                            </ul> 
                        </li>
                        <li class="mb-1"><i class="fas fa-dot-circle me-2 text-primary"></i><span class="font-base">Enter the desired amount from the "Pull out stock" tab.</span>
                            <ul class="list-unstyled ms-5">
                                <li class="mb-1">
                                    <i class="fas fa-chevron-right me-2 text-primary"></i><span class="text-muted">To pullout the entire amount, click on "All"</span>
                                </li>
                                <li class="mb-1">
                                    <i class="fas fa-chevron-right me-2 text-primary"></i><span class="text-muted">To dispose of the stock with given a amount, click on "Dispose". (optional)</span>
                                </li>
                                <li class="mb-1">
                                    <i class="fas fa-chevron-right me-2 text-primary"></i><span class="text-muted">Then select a reason for disposal.</span>
                                </li>
                            </ul> 
                        </li>
                        <li class="mb-1"><i class="fas fa-dot-circle me-2 text-primary"></i><span class="font-base">Click "Save" to apply your changes.</span><br>
                            <ul class="list-unstyled ms-5">
                                <li class="mb-1">
                                    <i class="fas fa-chevron-right me-2 text-primary"></i><span class="text-muted">If you disposed a stock, you can view it in "Waste" page.</span>
                                </li> 
                            </ul> 
                        </li>
                    </ul>
                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-base btn-secondary" data-mdb-dismiss="modal">Close</button>
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
    <script src="assets/js/page.action-stock-out.js"></script>
    <script src="assets/js/workarea.commons.js"></script>
    <script src="assets/js/shared-effects.js"></script>

</body>

</html>