<?php
require_once("rootcwd.php");
require_once("controllers/CalcuController.php");
?>

<body>

    <?php $masterPage->beginWorkarea(Navigation::NavIndex_Calcu); //BEGIN WORKAREA LAYOUT 
    ?>

    <!-- MAIN WORKAREA -->
    <div class="main-workarea flex-grow-1 d-flex flex-column h-100 bg-white shadow-2-strong overflow-hidden px-4 py-2">

        <!-- BREADCRUMB-->
        <div class="breadcrumbs  w-100 d-flex flex-row align-items-center justify-content-start">
            <img src="assets/images/icons/sidenav-toolbox.png" width="20" height="20">
            <div class="fs-6 fw-bold ms-2">Accessories</div>
            <div class="breadcrumb-arrow fsz-12 fas fa-play mx-3"></div>
            <img src="assets/images/icons/sidenav-calcu.png" width="20" height="20">
            <div class="ms-2 fw-bold fs-6">Calculator</div>
        </div>

        <div data-simplebar class="w-100 h-100 flex-grow-1 overflow-y-auto no-native-scroll">
            <div class="w-100 p-2">
                <div class="calculator-wrapper">
                    <div class="d-flex flex-row">
                        <div class="ms-0">
                            <div class="text-light text-uppercase fw-bold fon-fam-tertiary fsz-14">Calculator</div>
                            <div class="fsz-12 text-danger fst-italic">Accessories</div>
                        </div>
                        <div class="flex-fill text-white d-flex justify-content-end">
                            <div class="solar-panel">
                                <div class="solar-cell solar-cell-start"></div>
                                <div class="solar-cell"></div>
                                <div class="solar-cell"></div>
                            </div>
                        </div>
                    </div> 
                    <div class="lcd-screen">
                        <div class="indicator-text">
                            <input type="text" name="shift-indicator" id="shift-indicator" class="shift-indicator" readonly>
                            <input type="text" value="Math" readonly class="math-indicator">
                        </div>
                        <input type="hidden" name="equation-lcd" id="equation-lcd" class="equation-lcd" value="">
                        <input type="text" name="expression-lcd" id="expression-lcd" class="expression-lcd" value="">
                        <input type="text" name="result-lcd" id="result-lcd" class="result-lcd" value="" readonly>
                    </div> 
                    <div class="buttons-row">
                        <button class="small-key modifier" type="button" onclick="WriteExp('\u221A\u0028')">&#8730;</button>
                        <button class="small-key function math-symbol" type="button" onclick="WriteExp('\u00B2')">x<sup>2</sup></button>
                        <button class="small-key function" type="button" onclick="WriteExp('\u0028')">(</button>
                        <button class="small-key function" type="button" onclick="WriteExp('\u0029')">)</button>
                        <button class="small-key function fon-fam-special" type="button" onclick="toFraction()">&#xBD;</button>
                    </div>
                    <div class="buttons-row">
                        <button class="big-key numpad" type="button" onclick="WriteExp('7')">7</button>
                        <button class="big-key numpad" type="button" onclick="WriteExp('8')">8</button>
                        <button class="big-key numpad" type="button" onclick="WriteExp('9')">9</button>
                        <button class="big-key action-key" type="button" onclick="Delete()">DEL</button>
                        <button class="big-key action-key" type="button" onclick="ClearAll()">AC</button>
                    </div>
                    <div class="buttons-row">
                        <button class="big-key numpad" type="button" onclick="WriteExp('4')">4</button>
                        <button class="big-key numpad" type="button" onclick="WriteExp('5')">5</button>
                        <button class="big-key numpad" type="button" onclick="WriteExp('6')">6</button>
                        <button class="big-key operator" type="button" onclick="WriteExp('\u00d7')">&times;</button>
                        <button class="big-key operator" type="button" onclick="WriteExp('\u00f7')">&divide;</button>
                    </div>
                    <div class="buttons-row">
                        <button class="big-key numpad" type="button" onclick="WriteExp('1')">1</button>
                        <button class="big-key numpad" type="button" onclick="WriteExp('2')">2</button>
                        <button class="big-key numpad" type="button" onclick="WriteExp('3')">3</button>
                        <button class="big-key operator" type="button" onclick="WriteExp('+')">&plus;</button>
                        <button class="big-key operator" type="button" onclick="WriteExp('-')">&minus;</button>
                    </div>
                    <div class="buttons-row">
                        <button class="big-key numpad" type="button" onclick="WriteExp('0')">0</button>
                        <button class="big-key numpad" type="button" onclick="WriteExp('RND\u0028')">Rnd</button>
                        <button class="big-key special-key btn-conv" type="button">Conv</button>
                        <button class="big-key numpad" type="button" onclick="WriteExp('.')">.</button>
                        <button class="big-key operator btnSolve" type="button">&equals;</button> 
                    </div>
                </div>
            </div>
        </div>
    <textarea class="d-none converter-link"><?= Pages::EXTRAS_CONVERTER ?></textarea>
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
    <script src="assets/lib/calcu/solve.js"></script>
    <script src="assets/lib/calcu/fraction.js"></script>

    <script src="assets/js/base-ui.js"></script>
    <script src="assets/js/system.js"></script>

</body>

</html>