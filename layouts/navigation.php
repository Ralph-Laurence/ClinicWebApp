<?php 
require_once("rootcwd.php");
require_once($cwd . "includes/urls.php");

$cwd = $rootUrl;

function echoOnclick($url)
{
    global $cwd;
    $href = $cwd . $url;
    echo "onclick=\"navHref('$href')\"";
}

?>
<div class="side-nav h-100 border-2 bg-white border-end border-secondary scrollable" style="overflow-y: auto;">
    <!-- NAVIGATION TITLE TEXT -->
    <div class="bg-primary side-nav-title text-white py-2 px-4 align-items-center d-flex position-sticky top-0 z-100">
        <button type="button" class="btn px-3 me-2 border-0 shadow-0 btn-hide-sidenav" 
        data-mdb-toggle="tooltip" title="Click to hide Navigation">
            <i class="fas fa-chevron-left"></i>
        </button>
        <span>Navigation Menu</span>
    </div>

    <div class="accordion px-2">
        <div class="accordion-item border-start-0 border-end-0">
            <h2 class="accordion-header">
                <button class="accordion-button" type="button" data-mdb-toggle="collapse" data-mdb-target="#collapseTransaction" aria-expanded="true" aria-controls="collapseTransaction">
                    <i class="fas fa-laptop me-2 font-teal"></i>
                    <span>Transaction</span>
                </button>
            </h2>
            <div id="collapseTransaction" class="accordion-collapse collapse show">

                <div class="accordion-body">

                    <div class="row side-nav-link-item px-3 py-2" <?php echoOnclick(Navigation::$URL_CHECKUP_FORM); ?> >
                        <div class="col-2">
                            <i class="fas fa-heartbeat me-2 font-red"></i>
                        </div>
                        <div class="col fw-bold">Checkup</div>
                    </div> 

                    <div class="row side-nav-link-item px-3 py-2">
                        <div class="col-2">
                            <i class="fas fa-cube me-2 font-hilight"></i>
                        </div>
                        <div class="col fw-bold">Stock in / out</div>
                    </div>

                </div>
            </div>
        </div>

        <div class="accordion-item border-start-0 border-end-0">
            <h2 class="accordion-header">
                <button class="accordion-button" type="button" data-mdb-toggle="collapse" data-mdb-target="#collapseView" aria-expanded="true" aria-controls="collapseView">
                    <i class="fas fa-folder-open me-2 font-accent"></i>
                    View
                </button>
            </h2>
            <div id="collapseView" class="accordion-collapse collapse show">
                <div class="accordion-body">

                    <div class="row side-nav-link-item px-3 py-2" <?php echoOnclick(Navigation::$URL_STOCKS_INVENTORY); ?>>
                        <div class="col-2">
                            <i class="fas fa-warehouse me-2 font-teal"></i>
                        </div>
                        <div class="col fw-bold">Stocks Inventory</div>
                    </div>

                    <div class="row side-nav-link-item px-3 py-2">
                        <div class="col-2">
                            <i class="fas fa-ticket-alt me-2 font-accent"></i>
                        </div>
                        <div class="col fw-bold">Suppliers</div>
                    </div>

                    <div class="row side-nav-link-item px-3 py-2" <?php echoOnclick(Navigation::$URL_PATIENT_RECORDS); ?>>
                        <div class="col-2">
                            <i class="fas fa-notes-medical me-2 font-red"></i>
                        </div>
                        <div class="col fw-bold">Patient Records</div>
                    </div>

                    <div class="row side-nav-link-item px-3 py-2">
                        <div class="col-2">
                            <i class="fas fa-bell me-2 font-hilight"></i>
                        </div>
                        <div class="col fw-bold">Notifications</div>
                    </div>

                </div>
            </div>
        </div>

        <div class="accordion-item border-start-0 border-end-0">
            <h2 class="accordion-header" id="headingMaintenance">
                <button class="accordion-button collapsed" type="button" data-mdb-toggle="collapse" data-mdb-target="#collapseMaintenance" aria-expanded="false" aria-controls="collapseMaintenance">
                    <i class="fas fa-wrench me-2 font-hilight"></i>
                    Maintenance
                </button>
            </h2>
            <div id="collapseMaintenance" class="accordion-collapse collapse">
                <div class="accordion-body">

                    <div class="row side-nav-link-item px-3 py-2">
                        <div class="col-2">
                            <i class="fas fa-bacterium me-2 font-teal"></i>
                        </div>
                        <div class="col fw-bold">Illness</div>
                    </div>

                    <div class="row side-nav-link-item px-3 py-2">
                        <div class="col-2">
                            <i class="fas fa-layer-group me-2 font-accent"></i>
                        </div>
                        <div class="col fw-bold">Categories</div>
                    </div>

                    <div class="row side-nav-link-item px-3 py-2">
                        <div class="col-2">
                            <i class="fas fa-cogs me-2 font-hilight"></i>
                        </div>
                        <div class="col fw-bold">Settings</div>
                    </div>

                </div>
            </div>
        </div>

        <div class="accordion-item border-start-0 border-end-0">
            <h2 class="accordion-header" id="headingProfile">
                <button class="accordion-button" type="button" data-mdb-toggle="collapse" data-mdb-target="#collapseProfile" aria-expanded="true" aria-controls="collapseProfile">
                    <i class="fas fa-user-alt me-2 text-primary"></i>
                    Profile
                </button>
            </h2>
            <div id="collapseProfile" class="accordion-collapse collapse show">
                <div class="accordion-body">

                    <div class="row side-nav-link-item px-3 py-2">
                        <div class="col-2">
                            <i class="fas fa-shield-alt me-2 font-teal"></i>
                        </div>
                        <div class="col fw-bold">My Account</div>
                    </div>

                    <div class="row side-nav-link-item px-3 py-2">
                        <div class="col-2">
                            <i class="fas fa-power-off me-2 font-accent"></i>
                        </div>
                        <div class="col fw-bold">Logout</div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function navHref(url)
    {
        window.location.replace(url)

        try
        {
            window.history.pushState(null, null, window.location.href);
            window.onpopstate = function () {
                window.history.go(1);
            };
        }
        catch
        {
            window.location.href = url;
        }
    }
</script>