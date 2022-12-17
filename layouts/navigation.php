<?php 
require_once("rootcwd.php");

$cwd = constant("ROOT_URL");

function echoOnclick($url)
{
    global $cwd;
    $href = $cwd . $url;
    echo "onclick=\"navHref('$href')\"";
}

?>
<div class="side-nav h-100 border-2 border-end border-secondary">
    <!-- NAVIGATION TITLE TEXT -->
    <div class="bg-primary side-nav-title text-white py-2 px-4 align-items-center d-flex">
        <div class="fas fa-compass me-2"></div>
        <span>Navigation Menu</span>
    </div>

    <div class="accordion">
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button" type="button" data-mdb-toggle="collapse" data-mdb-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                    <i class="fas fa-laptop me-2 font-teal"></i>
                    <span>Transaction</span>
                </button>
            </h2>
            <div id="collapseOne" class="accordion-collapse collapse show">

                <div class="accordion-body">

                    <div class="row side-nav-link-item px-3 py-2" <?php echoOnclick('checkup-form.php'); ?> >
                        <div class="col-2">
                            <i class="fas fa-heartbeat me-2 font-red"></i>
                        </div>
                        <div class="col fw-bold">Checkup</div>
                    </div>

                    <div class="row side-nav-link-item px-3 py-2" >
                        <div class="col-2">
                            <i class="fas fa-prescription-bottle me-2 font-hilight"></i>
                        </div>
                        <div class="col fw-bold">Prescriptions</div>
                    </div>

                    <div class="row side-nav-link-item px-3 py-2">
                        <div class="col-2">
                            <i class="fas fa-upload me-2 font-teal"></i>
                        </div>
                        <div class="col fw-bold">Stock in / out</div>
                    </div>

                </div>
            </div>
        </div>

        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-mdb-toggle="collapse" data-mdb-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
                    <i class="fas fa-folder-open me-2 font-accent"></i>
                    View
                </button>
            </h2>
            <div id="collapseTwo" class="accordion-collapse collapse show">
                <div class="accordion-body">

                    <div class="row side-nav-link-item px-3 py-2">
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

                    <div class="row side-nav-link-item px-3 py-2" <?php echoOnclick('patient-records.php'); ?>>
                        <div class="col-2">
                            <i class="fas fa-notes-medical me-2 font-red"></i>
                        </div>
                        <div class="col fw-bold">Patient Records</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="accordion-item">
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

        <div class="accordion-item">
            <h2 class="accordion-header" id="headingProfile">
                <button class="accordion-button collapsed" type="button" data-mdb-toggle="collapse" data-mdb-target="#collapseProfile" aria-expanded="true" aria-controls="collapseProfile">
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
                            <i class="fas fa-sign-out-alt me-2 font-accent"></i>
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

        }
    }
</script>