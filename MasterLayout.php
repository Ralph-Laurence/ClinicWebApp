<?php
require_once("rootcwd.php");

// This script should go first before including the 
// layout-header.php file

class MasterLayout
{
    public $styles = [];

    /**
     * All styles should be stored at "assets/css".
     * @param $css -> The filename
     */
    function includeStyle($css)
    {
        $style = "<link rel=\"stylesheet\" href=\"assets/css/$css\">";
        array_push($this->styles, $style);
    }

    function injectStyle($css)
    {
        $style = 
"<style>
$css
</style>";
        array_push($this->styles, $style);
    }
    /**
     * All css library should be stored at "assets/lib"
     */
    function includeCssLib($path)
    {
        $style = "<link rel=\"stylesheet\" href=\"assets/lib/$path\">";
        array_push($this->styles, $style);
    }
    //
    // Write the stylesheets
    //
    function loadStyles()
    {
        if (!empty($this->styles)) {
            foreach ($this->styles as $css) {
                echo $css . "\n";
            }
        }
    }

    function beginWorkarea(int $activeSidenavLink)
    {
        echo <<<ROOT_CONTAINER
        <!-- ROOT CONTAINER -->
        <div class="container-fluid p-0 d-flex flex-column main-wrapper w-100 h-100 bg-document overflow-hidden">
        <!-- HEADINGS BANNER -->
        ROOT_CONTAINER;

        require_once("layouts/headings-banner.php");

        echo <<<MAIN_CONTENT_WRAPPER
        <!-- MAIN CONTENT WRAPPER -->
        <div class="main-content-wrapper flex-column flex-grow-1 h-100 overflow-hidden">
        <!-- MAIN CONTENT -->
        <div class="main-content d-flex h-100 w-100 overflow-hidden">
        <!-- SIDE NAVIGATION [LEFT-HALF] -->
        MAIN_CONTENT_WRAPPER;

        // mark the active side nav link
        setActiveLink($activeSidenavLink);
        // Then include the side navigation
        require_once("layouts/side-nav.php");

        echo <<<WORKSPACE_WRAPPER
        <!-- WORKSPACE WRAPPER [RIGHT-HALF] -->
        <div class="workspace-wrapper d-flex flex-column flex-fill overflow-hidden">
        <!--WELCOME BANNER-->
        WORKSPACE_WRAPPER;

        require_once("layouts/welcome-banner.php");

        echo <<<WORKSPACE_CONTENT
        <!--BEGIN WORKSPACE CONTENT-->
        <div class="workspace-content d-flex flex-column p-2 h-100 overflow-hidden">
        WORKSPACE_CONTENT;
    }

    function endWorkArea()
    {
        echo
        "</div>
        <!-- END WORKSPACE CONTENT -->
        </div>
        <!-- END WORKSPACE WRAPPER [RIGHT-HALF] -->
        </div>
        <!-- END MAIN CONTENT -->
        </div>
        <!-- END MAIN CONTENT WRAPPER -->
        </div>
        <!-- END ROOT CONTAINER -->";
    }
    /**
     * @param bool $alertDialog - Alert Dialog
     * @param bool $confirmDialog - Confirm Dialog
     * @param bool $snackbar - Snack Bar
     * @param bool $toast - Toast
     */
    function includeDialogs(bool $alertDialog, bool $confirmDialog = false, bool $snackbar = false, bool $toast = false)
    {
        global $rootCwd;

        if ($alertDialog)
            require_once($rootCwd . "components/alert-dialog/alert-dialog.php");

        if ($confirmDialog)
            require_once($rootCwd . "components/confirm-dialog/confirm-dialog.php");

        if ($snackbar)
            require_once($rootCwd . "components/snackbar/snackbar.php");

        if ($toast)
            require_once($rootCwd . "components/toast/toast.php");
    }
}

// Instantiate master layout object.
$masterPage = new MasterLayout();
