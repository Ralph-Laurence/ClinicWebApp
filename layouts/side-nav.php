<?php 
require_once("rootcwd.php");
require_once($rootCwd . "controllers/SideNavController.php");
?>
<div class="side-nav border-2 bg-white border-end side-nav-border-end overflow-hidden position-relative">

    <!-- SIDENAV TITLE TEXT -->
    <div class="side-nav-title style-secondary py-2 align-items-center d-flex">
        <button type="button" class="btn px-3 border-0 shadow-0 btn-hide-sidenav" data-mdb-toggle="tooltip" title="Click to hide Navigation">
            <i class="fas fa-chevron-left"></i>
        </button>
        <div class="ms-1 mt-1 me-auto">Navigation Menu</div>
        <div class="end px-2">
            <a class="btn btn-primary shadow-0 btn-nav-home px-2 py-1" href="<?= Pages::HOME ?>" role="button">
                <i class="fas fa-home"></i>
            </a>
        </div>
    </div>

    <!-- SIDENAV ITEMS -->
    <div data-simplebar class="sidenav-items flex-grow-1 h-100 overflow-y-auto no-native-scroll">

        <div class="accordion px-2 opac-0">

            <!-- <div class="accordion-item border-0">
                <h2 class="accordion-header">
                    <button class="accordion-button" type="button" data-mdb-toggle="collapse" data-mdb-target="#collapseTransaction" aria-expanded="true" aria-controls="collapseTransaction">
                        <img src="assets/images/icons/sidenav-insights.png" width="24" height="24" alt="icon">
                        <span class="ms-2 fon-fam-special">Insights</span>
                    </button>
                </h2>
                <div id="collapseTransaction" class="accordion-collapse collapse show">

                    <div class="accordion-body">

                        <div class="row side-nav-link-item px-3 py-2 <?php //= highlightLink(Navigation::NavIndex_Statistics) ?>" <?php //redirectTo(Pages::STATISTICS); ?>>
                            <div class="col-2">
                                <img src="assets/images/icons/sidenav-stats.png" width="20" height="20" alt="icon">
                            </div>
                            <div class="col fw-bold">Statistics</div>
                        </div>

                        <div class="row side-nav-link-item px-3 py-2 <?php //= highlightLink(Navigation::NavIndex_Forecast) ?>" <?php //redirectTo(Pages::CHECKUP_FORM); ?>>
                            <div class="col-2">
                                <img src="assets/images/icons/sidenav-forecast.png" width="20" height="20" alt="icon">
                            </div>
                            <div class="col fw-bold">Forecast</div>
                        </div> 

                    </div>
                </div>
            </div> -->

            <div class="accordion-item border-0">
                <h2 class="accordion-header">
                    <button class="accordion-button" type="button" data-mdb-toggle="collapse" data-mdb-target="#collapseTransaction" aria-expanded="true" aria-controls="collapseTransaction">
                        <!-- <i class="fas fa-laptop me-2 font-teal"></i> -->
                        <img src="assets/images/icons/sidenav-transaction.png" width="24" height="24" alt="icon">
                        <span class="ms-2 fon-fam-special">Transaction</span>
                    </button>
                </h2>
                <div id="collapseTransaction" class="accordion-cole collapse show">

                    <div class="accordion-body">

                        <div id="sidenav-scroll-target" class="row side-nav-link-item px-3 py-2 <?= highlightLink(Navigation::NavIndex_Register) ?>" <?= redirectTo(Pages::REGISTER_PATIENT); ?>>
                            <div class="col-2">
                                <!-- <i class="fas fa-receipt me-2 font-accent"></i> -->
                                <img src="assets/images/icons/sidenav-reg-patient.png" width="20" height="20" alt="icon">
                            </div>
                            <div class="col fw-bold">Register Patient</div>
                        </div>

                        <div id="sidenav-scroll-target" class="row side-nav-link-item px-3 py-2 <?= highlightLink(Navigation::NavIndex_Checkup) ?>" <?= redirectTo(Pages::CHECKUP_FORM); ?>>
                            <div class="col-2">
                                <!-- <i class="fas fa-heartbeat me-2 font-red"></i> -->
                                <img src="assets/images/icons/sidenav-checkup-form.png" width="20" height="20" alt="icon">
                            </div>
                            <div class="col fw-bold">Checkup Form</div>
                        </div>

                        <?php 

                        if (hasFeatureAccess([UserRoles::SUPER_ADMIN, UserRoles::ADMIN])) 
                        {
                            $feature_hilight = highlightLink(Navigation::NavIndex_Restock);
                            $feature_redirect = redirectTo(Pages::RESTOCK);

                            echo <<<DIV
                            <div id="sidenav-scroll-target" class="row side-nav-link-item px-3 py-2 $feature_hilight" $feature_redirect>
                                <div class="col-2">
                                    <img src="assets/images/icons/sidenav-restock.png" width="20" height="20" alt="icon">
                                </div>
                                <div class="col fw-bold">
                                    <span>Restock</span>
                                    <span class="position-absolute ms-2 mt-2 bg-danger display-none sidenav-badge-dot"></span>
                                </div>
                            </div>
                            DIV; 
                        }
                        ?>
                        
                    </div>
                </div>
            </div>

            <div class="accordion-item border-top border-start-0 border-end-0">
                <h2 class="accordion-header">
                    <button class="accordion-button" type="button" data-mdb-toggle="collapse" data-mdb-target="#collapseView" aria-expanded="true" aria-controls="collapseView">
                        <img src="assets/images/icons/sidenav-view.png" width="24" height="24" alt="icon">
                        <span class="ms-2 fon-fam-special">View</span>
                    </button>
                </h2>
                <div id="collapseView" class="accordion-collapse collapse show">
                    <div class="accordion-body">
 
                        <?php 

                        if (hasFeatureAccess([UserRoles::SUPER_ADMIN, UserRoles::ADMIN])) 
                        {
                            $feature_hilight = highlightLink(Navigation::NavIndex_Stocks);
                            $feature_redirect = redirectTo(Pages::MEDICINE_INVENTORY);

                            echo <<<DIV
                            <div id="sidenav-scroll-target" class="row side-nav-link-item px-3 py-2 $feature_hilight" $feature_redirect>
                                <div class="col-2">
                                    <img src="assets/images/icons/sidenav-inventory.png" width="20" height="20" alt="icon">
                                </div>
                                <div class="col fw-bold">
                                    <span>Medicine Inventory</span>
                                    <span class="position-absolute ms-2 mt-2 bg-danger display-none sidenav-badge-dot"></span>
                                </div>
                            </div>
                            DIV;

                            $feature_hilight = highlightLink(Navigation::NavIndex_Suppliers);
                            $feature_redirect = redirectTo(Pages::SUPPLIERS);
                        
                            echo <<<DIV
                            <div id="sidenav-scroll-target" class="row side-nav-link-item px-3 py-2 $feature_hilight" $feature_redirect>
                                <div class="col-2">
                                    <img src="assets/images/icons/sidenav-supplier.png" width="20" height="20" alt="icon">
                                </div>
                                <div class="col fw-bold">Suppliers</div>
                            </div>
                            DIV;
                        }

                        ?>

                        <div class="row px-3 py-2">
                            <div class="col-2">
                                <!-- <i class="fas fa-notes-medical me-2 font-red"></i> -->
                                <img src="assets/images/icons/sidenav-medical.png" width="20" height="20" alt="icon">
                            </div>
                            <div class="col fw-bold">Medical Records</div>
                        </div>

                        <!--BEGIN SUBMENUS -->
                        <div id="sidenav-scroll-target" class="row px-3 py-2 side-nav-link-item <?= highlightLink(Navigation::NavIndex_Patients) ?>" <?= redirectTo(Pages::PATIENTS); ?>>
                            <div class="col-2 ps-5">
                                <!-- <i class="fas fa-wheelchair me-2 font-teal"></i>  -->
                                <img src="assets/images/icons/sidenav-patients.png" width="20" height="20" alt="icon">
                            </div>
                            <div class="col fw-bold">Patients</div>
                        </div>
                        <div id="sidenav-scroll-target" class="row px-3 py-2 side-nav-link-item <?= highlightLink(Navigation::NavIndex_CheckupRecords) ?>" <?= redirectTo(Pages::CHECKUP_RECORDS); ?>>
                            <div class="col-2 ps-5">
                                <!-- <i class="fas fa-poll-h me-2 font-teal"></i> -->
                                <img src="assets/images/icons/sidenav-checkup.png" width="20" height="20" alt="icon">
                            </div>
                            <div class="col fw-bold">History</div>
                        </div>
                        <!--END SUBMENUS -->

                        <?php 

                        if (hasFeatureAccess([UserRoles::SUPER_ADMIN, UserRoles::ADMIN])) 
                        {
                            $feature_hilight = highlightLink(Navigation::NavIndex_Doctors);
                            $feature_redirect = redirectTo(Pages::DOCTORS);

                            echo <<<DIV
                            <hr class="hr my-1">

                            <div id="sidenav-scroll-target" class="row side-nav-link-item px-3 py-2 $feature_hilight" $feature_redirect>
                                <div class="col-2">
                                    <img src="assets/images/icons/sidenav-doctor.png" width="20" height="20" alt="icon">
                                </div>
                                <div class="col fw-bold">Doctors</div>
                            </div>
                            DIV;

                            $feature_hilight = highlightLink(Navigation::NavIndex_Users);
                            $feature_redirect = redirectTo(Pages::USERS);

                            echo <<<DIV
                            <div id="sidenav-scroll-target" class="row side-nav-link-item px-3 py-2 $feature_hilight" $feature_redirect>
                                <div class="col-2">
                                    <img src="assets/images/icons/sidenav-users.png" width="20" height="20" alt="icon">
                                </div>
                                <div class="col fw-bold">Users</div>
                            </div>
                            DIV;
                        }

                        ?>

                    </div>
                </div>
            </div>

            <div class="accordion-item border-start-0 border-end-0">
                <h2 class="accordion-header">
                    <button class="accordion-button" type="button" data-mdb-toggle="collapse" data-mdb-target="#collapseExtras" aria-expanded="true" aria-controls="collapseExtras">
                        <img src="assets/images/icons/sidenav-toolbox.png" width="24" height="24" alt="icon">
                        <span class="ms-2 fon-fam-special">Accessories</span>
                    </button>
                </h2>
                <div id="collapseExtras" class="accordion-collapse collapse show">
                    <div class="accordion-body">

                        <div id="sidenav-scroll-target" class="row side-nav-link-item px-3 py-2 <?= highlightLink(Navigation::NavIndex_Calcu) ?>" <?= redirectTo(Pages::EXTRAS_CALCU); ?>>
                            <div class="col-2">
                                <img src="assets/images/icons/sidenav-calcu.png" width="20" height="20" alt="icon">
                            </div>
                            <div class="col fw-bold">Calculator</div>
                        </div>

                        <!-- <div id="sidenav-scroll-target" class="row side-nav-link-item px-3 py-2 <?php //= highlightLink(Navigation::NavIndex_Converter) ?>" <?php //= redirectTo(Pages::EXTRAS_CONVERTER); ?>>
                            <div class="col-2">
                                <img src="assets/images/icons/sidenav-conv.png" width="20" height="20" alt="icon">
                            </div>
                            <div class="col fw-bold">Unit Converter</div>
                        </div> -->

                    </div>
                </div>
            </div>
            <?php 

            if (hasFeatureAccess([UserRoles::SUPER_ADMIN, UserRoles::ADMIN])) 
            {
                echo <<<DIV
                <div class="accordion-item border-start-0 border-end-0">
                    <h2 class="accordion-header" id="headingMaintenance">
                        <button class="accordion-button" type="button" data-mdb-toggle="collapse" data-mdb-target="#collapseMaintenance" aria-expanded="true" aria-controls="collapseMaintenance">
                            <img src="assets/images/icons/sidenav-maintenance.png" width="24" height="24" alt="icon">
                            <span class="ms-1 fon-fam-special">Maintenance</span>
                        </button>
                    </h2>
                    <div id="collapseMaintenance" class="accordion-collapse collapse show">
                        <div class="accordion-body">
                DIV;

                $feature_hilight = highlightLink(Navigation::NavIndex_Illness);
                $feature_redirect = redirectTo(Pages::ILLNESS);

                echo <<<DIV
                            <div id="sidenav-scroll-target" class="row side-nav-link-item px-3 py-2 $feature_hilight" $feature_redirect>
                                <div class="col-2">
                                    <img src="assets/images/icons/sidenav-illness.png" width="22" height="22" alt="icon">
                                </div>
                                <div class="col fw-bold">Illness</div>
                            </div>
                DIV;

                $feature_hilight = highlightLink(Navigation::NavIndex_Categories);
                $feature_redirect = redirectTo(Pages::CATEGORIES);

                echo <<<DIV
                            <div id="sidenav-scroll-target" class="row side-nav-link-item px-3 py-2 $feature_hilight" $feature_redirect>
                                <div class="col-2">
                                    <img src="assets/images/icons/sidenav-categories.png" width="20" height="20" alt="icon">
                                </div>
                                <div class="col fw-bold">Categories</div>
                            </div>
                DIV;
                
                $feature_hilight = highlightLink(Navigation::NavIndex_Settings);
                $feature_redirect = redirectTo(Pages::SETTINGS);

                echo <<<DIV
                            <div id="sidenav-scroll-target" class="row side-nav-link-item px-3 py-2 $feature_hilight" $feature_redirect>
                                <div class="col-2">
                                    <img src="assets/images/icons/sidenav-settings.png" width="20" height="20" alt="icon">
                                </div>
                                <div class="col fw-bold">Settings</div>
                            </div>
                
                        </div>
                    </div>
                </div>
                DIV;
            }
            ?>

            <div class="accordion-item border-start-0 border-end-0">
                <h2 class="accordion-header" id="headingProfile">
                    <button class="accordion-button" type="button" data-mdb-toggle="collapse" data-mdb-target="#collapseProfile" aria-expanded="true" aria-controls="collapseProfile">
                        <!-- <i class="fas fa-user-alt me-2 text-primary"></i> -->
                        <img src="assets/images/icons/sidenav-profile.png" width="20" height="20" alt="icon">
                        <span class="ms-2 fon-fam-special">Profile</span>
                    </button>
                </h2>
                <div id="collapseProfile" class="accordion-collapse collapse show">
                    <div class="accordion-body">

                        <div id="sidenav-scroll-target" class="row side-nav-link-item px-3 py-2 <?= highlightLink(Navigation::NavIndex_Profile) ?>" <?= redirectTo(Pages::MY_PROFILE) ?>>
                            <div class="col-2">
                                <!-- <i class="fas fa-shield-alt me-2 font-teal"></i> -->
                                <img src="assets/images/icons/sidenav-my-account.png" width="20" height="20" alt="icon">
                            </div>
                            <div class="col fw-bold">My Account</div>
                        </div>

                        <div class="row side-nav-link-item px-3 py-2" <?= redirectTo(Tasks::LOGOUT); ?>>
                            <div class="col-2">
                                <i class="fas fa-power-off me-2 font-red"></i>
                            </div>
                            <div class="col fw-bold">Logout</div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

    </div>
</div>