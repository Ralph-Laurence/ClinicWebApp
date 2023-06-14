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
                    <div class="mb-3 d-flex align-items-center gap-2">
                        <div class="bg-document px-2 rounded-6 text-white lbl-category-icon"></div>
                        <div class="fs-5 font-base lbl-item-name flex-wrap">Item Name</div>
                    </div> 
                    <div class="mb-2 section-item-information">

                        <!--ITEM CATEGORY-->
                        <div class="row">
                            <div class="col-1">
                                <i class="fas fa-layer-group text-info"></i>
                            </div>
                            <div class="col-3">Category:</div>
                            <div class="col">
                                <span class="fs-6 font-base lbl-category">Category</span>
                            </div>
                        </div>
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
                                <i class="fas fa-truck text-info"></i>
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
                        <div class="row">
                            <div class="col-1">
                                <i class="fas fa-th-large text-info"></i>
                            </div>
                            <div class="col-3">Reserve:</div>
                            <div class="col">
                                <span class="font-base lbl-reserve"></span>
                            </div>
                        </div>
                        <!--STOCK DESCRIPTION-->
                        <div class="row mb-3">
                            <div class="col-1">
                                <i class="fas fa-info-circle text-info"></i>
                            </div>
                            <div class="col-3">Description:</div>
                            <div class="col">
                                <div class="form-outline">
                                    <input type="text" class="form-control bg-white item-description" value="N/A" readonly>
                                </div>
                            </div>
                        </div>
                        <!--DATE ADDED-->
                        <div class="row">
                            <div class="col-1">
                                <i class="fas fa-calendar-plus font-base"></i>
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