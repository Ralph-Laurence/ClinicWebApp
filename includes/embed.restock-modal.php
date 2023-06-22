<?php
// Mode => 0 = Stock In; 1 = Stock Out
if (!in_array($mode, [0, 1])) {
    IError::Throw(500);
    exit;
}

$title = ($mode == 1) ? "Stock Out" : "Stock In";

?>
<div class="modal fade" id="restockModal" tabindex="-1" aria-labelledby="restockModalLabel" aria-hidden="true" data-mdb-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header py-2">
                <h5 class="modal-title" id="restockModalLabel"><?= $title ?></h5>
            </div>
            <div class="modal-body">
                <div class="d-flex flex-column align-items-center justify-content-center mb-3">
                    <div class="image-wrapper border rounded-2 fit-content p-2 d-flex align-items-center justify-content-center mb-2">
                        <img src="" class="restock-item-icon rounded-2" alt="icon" width="80" height="80">
                    </div>
                    <div class="text-wrap px-2">
                        <div class="fs-5 font-base restock-lbl-itemname"></div>
                    </div>
                </div>
                <hr class="hr">
                <div class="row mb-2">
                    <div class="col-4">
                        <div class="fs-6 p-1">Enter SKU / Code:</div>
                    </div>
                    <div class="col text-truncate text-start py-1">
                        <div class="d-flex flex-row align-items-center gap-2">
                            <div class="form-outline flex-fill">
                                <input type="text" class="form-control fon-fam-special text-primary text-uppercase alpha" name="sku" id="input-sku" placeholder="" maxlength="32" required />
                            </div>
                            <div class="ms-auto" data-mdb-toggle="tooltip" data-mdb-placement="top" data-mdb-html="true" data-mdb-title="<span class='tooltip-title tooltip-title-amber'>Stock Keeping Unit</span><br><br>SKU is a unique code used to track items.<br><br>For example, WAL_BLK_25 refers to a pen made from walnut with black ink and a 2.5mm point.">
                                <i class="fas fa-info-circle text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- <div class="row">
                    <div class="col-4">
                        <div class="fs-6 p-1">Total Qty:</div>
                    </div>
                    <div class="col text-truncate">
                        <div class="current-stock text-truncate bg-document p-1 rounded-1 restock-lbl-current-stock"></div>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-4">
                        <div class="fs-6 p-1">Reserve Qty:</div>
                    </div>
                    <div class="col text-truncate text-start">
                        <div class="reserve-stock text-text-truncate p-1 text-primary restock-lbl-reserve-stock"></div>
                    </div>
                </div> -->
                <div class="row mb-3">
                    <div class="col-4">
                        <div class="d-flex align-items-center h-100">
                            <div class="fs-6 ms-1 me-2">Enter amount :</div>
                        </div>
                    </div>
                    <div class="col text-truncate">
                        <div class="d-flex gap-2 align-items-center text-truncate">
                            <button type="button" class="btn btn-warning bg-accent py-1 px-2 btn-qty-minus">
                                <i class="fas fa-minus"></i>
                            </button>
                            <div class="input-wrapper" style="width: 80px;">
                                <div class="form-outline">
                                    <input type="text" class="form-control text-center fon-fam-special fs-5 py-0 numeric" name="qty" id="input-stock-qty" data-min-qty="1" value="1" data-max-qty="" maxlength="6" required />
                                </div>
                            </div>
                            <button type="button" class="btn btn-primary bg-teal py-1 px-2 btn-qty-plus">
                                <i class="fas fa-plus"></i>
                            </button>
                            <div class="units-label ms-auto flex-fill text-truncate text-start"></div>
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-4">
                        <div class="d-flex align-items-center h-100">
                            <div class="fs-6 ms-1 me-2">Expiry Date :</div>
                        </div>
                    </div>
                    <div class="col text-truncate">
                        <div class="d-flex align-items-center py-1 gap-2">
                            <div class="form-outline w-50">
                                <input type="text" name="input-expiry" class="form-control h-36 input-expiry bg-white hasDatepicker" readonly required>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault" />
                                <label class="form-check-label" for="flexCheckDefault">No Expiry</label>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
                if ($mode == 1) {
                    $title = "Stock Out";

                    echo <<<DIV
                    <hr class="hr">
                    <div class="row">
                        <div class="col-4 d-flex align-items-center">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="moveToWaste" />
                                <label class="form-check-label" for="flexCheckDefault">Move to waste</label>
                            </div>
                        </div>
                        <div class="col">
                            <div class="dropdown">
                                <div class="form-outline">
                                    <input type="text" id="form1" class="form-control dropdown-toggle bg-white txt-reason" data-mdb-toggle="dropdown" aria-expanded="false" readonly required disabled/>
                                    <label class="form-label" for="form1">Reason</label>
                                    <ul class="dropdown-menu" aria-labelledby="dropdownExampleAnimation">
                    DIV;

                    generateStockoutReasons();

                    echo <<<DIV
                                    </ul>
                                    <input type="text" name="stockout-reason" class="d-none stockout-reason">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="restock-validation-error bg-red-light font-red-dark mt-2 p-2 rounded-2 text-center display-none"></div>
                    DIV;
                } else if ($mode == 0) {
                    echo <<<DIV
                    <div class="expiry-date-wrapper display-none">
                        <hr class="hr">
                        <div class="row mb-2">
                            <div class="col-4 d-flex align-items-center">Use-By Date:</div>
                            <div class="col d-flex align-items-center gap-2">
                                <div class="form-outline me-auto flex-fill">
                                    <input type="text" class="form-control stockin-expiry-date bg-white" value="" readonly/>
                                    <label class="form-label" for="stockin-expiry-date">Expiry Date *</label>
                                </div>
                                <button type="button" class="px-3 py-2 btn btn-secondary btn-clear-expiry">
                                    <i class="fas fa-undo"></i>
                                </button>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-4"></div>
                            <div class="col font-primary-dark fsz-14">
                            This item does not have an expiration date. You can choose to select or leave it empty.
                            </div>
                        </div>
                    </div>
                    DIV;
                }
                ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary fw-bold btn-cancel-restock" data-mdb-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary btn-base btn-ok">OK</button>
            </div>
        </div>
    </div>
</div>