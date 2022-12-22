    <!-- STOCK / ITEM INFORMATION DIALOG -->
    <div class="modal fade stockDetailsModal" id="stockDetailsModal" tabindex="-1" aria-labelledby="stockDetailsModalLabel" aria-hidden="true" data-mdb-backdrop="static">
        <div class="modal-dialog pt-3">
            <div class="modal-content mt-4">
                <div class="modal-header bg-base text-white py-0 ps-4 pe-0">
                    <h6 class="modal-title" id="stockDetailsModalLabel">Item Information</h6>
                    <button type="button" class="btn shadow-0 fs-5 text-white" data-mdb-dismiss="modal">
                        <i class="fas fa-times-circle"></i>
                    </button>
                </div>

                <div class="modal-body px-4">
                    <div class="d-flex flex-wrap mb-2">
                        <h5 class="font-base fw-bold lbl-item-name">Item Name</h5>
                    </div>
                    <div class="mb-3 d-flex align-items-center gap-2">
                        <span class="fs-5 bg-teal px-2 rounded-6 text-white lbl-category-icon">
                            <i class="fas fa-prescription-bottle"></i>
                        </span>
                        <span class="fw-bold fs-6 lbl-category">Category</span>
                    </div>
                    <div class="mb-2 section-item-information">

                        <!--ITEM CODE-->
                        <div class="row">
                            <div class="col-1">
                                <i class="fas fa-tag text-info"></i>
                            </div>
                            <div class="col-3">Item Code:</div>
                            <div class="col">
                                <span class="font-base lbl-item-code"></span>
                            </div>
                        </div>
                        <!--UNIT MEASURES-->
                        <div class="row">
                            <div class="col-1">
                                <i class="fas fa-ruler-combined text-info"></i>
                            </div>
                            <div class="col-3">Measures:</div>
                            <div class="col">
                                <span class="font-base lbl-unit-measure"></span>
                            </div>
                        </div>
                        <!--SUPPLIERS-->
                        <div class="row">
                            <div class="col-1">
                                <i class="fas fa-parachute-box text-info"></i>
                            </div>
                            <div class="col-3">Supplier:</div>
                            <div class="col">
                                <span class="font-base lbl-supplier"></span>
                            </div>
                        </div>
                        <!--TOTAL STOCK-->
                        <div class="row">
                            <div class="col-1">
                                <i class="fas fa-boxes text-info"></i>
                            </div>
                            <div class="col-3">Total Stock:</div>
                            <div class="col">
                                <span class="font-base lbl-total-stock"></span>
                            </div>
                        </div>
                        <!--RESERVE STOCK-->
                        <div class="row mb-3">
                            <div class="col-1">
                                <i class="fas fa-th-large text-info"></i>
                            </div>
                            <div class="col-3">Reserve:</div>
                            <div class="col">
                                <span class="font-base lbl-reserve"></span>
                            </div>
                        </div>
                        <!--DATE ADDED-->
                        <div class="row">
                            <div class="col-1">
                                <i class="fas fa-calendar-plus font-hilight"></i>
                            </div>
                            <div class="col-3">Created on:</div>
                            <div class="col">
                                <span class="font-base lbl-date-added"></span>
                            </div>
                        </div>
                    </div>
                    <div class="item-status-warning"></div>
                </div>
                <div class="modal-footer  py-2">
                    <button class="btn btn-primary bg-base" type="button" data-mdb-dismiss="modal">
                        OK
                    </button>
                </div>
            </div>
        </div>
    </div>