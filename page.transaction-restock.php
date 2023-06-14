<?php
require_once("rootcwd.php");
require_once($rootCwd . "controllers/RestockController.php");
?>

<body>

    <?php $masterPage->beginWorkarea(Navigation::NavIndex_Restock); //BEGIN WORKAREA LAYOUT 
    ?>

    <!-- MAIN WORKAREA -->
    <div class="main-workarea flex-grow-1 d-flex flex-column h-100 bg-white shadow-2-strong overflow-hidden px-4 py-2">


        <!-- BREADCRUMB-->
        <div class="breadcrumbs w-100 d-flex flex-row align-items-center justify-content-start">
            <img src="assets/images/icons/sidenav-transaction.png" width="20" height="20">
            <div class="fs-6 fw-bold ms-2">Transaction</div>
            <div class="breadcrumb-arrow fsz-12 fas fa-play mx-3"></div>
            <img src="assets/images/icons/sidenav-restock.png" width="20" height="20">
            <div class="ms-2 fw-bold fs-6">Restock</div>
        </div>

        <hr class="hr">

        <div data-simplebar class="w-100 h-100 overflow-y-auto no-native-scroll">
            <!--CARDS PARENT WRAPPER-->
            <div class="cards-parent-container w-100 h-100 flex-grow-1 d-flex flex-row flex-wrap gap-3 p-3">
                
                <!-- STOCK IN CARD -->
                <div class="card standard-item-card shadow-3-strong display-none effect-reciever" 
                     data-transition-index="0" data-transition="fadein">
                    <div class="text-center pt-2">
                        <img src="assets/images/icn_stockin.png" width="96" height="96" alt="Box" />
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">Stock In</h5>
                        <p class="card-text">Replenish the inventory with fresh medicines or supplies.</p>
                    </div>
                    <div class="card-card-footer px-4 pb-4 display-none effect-reciever" 
                        data-transition-index="4" data-transition="fadein">
                        <a href="<?= Pages::STOCK_IN ?>" role="button" class="btn btn-primary w-100 btn-base">
                            Stock In
                        </a>
                    </div>
                </div>

                <!-- STOCK OUT CARD -->
                <div class="card standard-item-card shadow-3-strong display-none effect-reciever"
                     data-transition-index="1" data-transition="fadein">
                    <div class="text-center pt-2">
                        <img src="assets/images/icn_stockout.png" width="96" height="96" alt="Box" />
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">Stock Out</h5>
                        <p class="card-text">Pull out medicines or stocks from the inventory.</p>
                    </div>
                    <div class="card-card-footer px-4 pb-4 display-none effect-reciever" 
                        data-transition-index="5" data-transition="fadein">
                        <a href="<?= Pages::STOCK_OUT ?>" role="button" class="btn btn-danger w-100 btn-red">
                            Stock Out
                        </a>
                    </div>
                </div>
                
                <!-- WASTE CARD -->
                <div class="card standard-item-card shadow-3-strong display-none effect-reciever"
                     data-transition-index="2" data-transition="fadein">
                    <div class="text-center pt-2">
                        <img src="assets/images/icn_waste.png" width="96" height="96" alt="Box" />
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">Waste</h5>
                        <p class="card-text">Defective medicines or stocks will be disposed here.</p>
                    </div>
                    <div class="card-card-footer px-4 pb-4 display-none effect-reciever" 
                        data-transition-index="6" data-transition="fadein">
                        <a href="<?= Pages::WASTE ?>" role="button" class="btn btn-success w-100">
                            Waste
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <?php $masterPage->endWorkarea(); //END WORKAREA LAYOUT 
    ?>
  

    <!--SCRIPTS-->
    <script src="assets/lib/jquery/jquery-3.6.1.min.js"></script>
    <script src="assets/lib/jquery-ui-1.13.2.custom/jquery-ui.min.js"></script>
    <script src="assets/lib/datatables/datatables.min.js"></script>
    <script src="assets/lib/mdb5/js/mdb.min.js"></script>
    <script src="assets/lib/momentjs/moment-with-locales.js"></script>
    <script src="assets/lib/simplebar/simplebar.min.js"></script>

    <script src="assets/js/base-ui.js"></script>
    <script src="assets/js/system.js"></script>
    <script src="assets/js/shared-effects.js"></script> 

</body>

</html>