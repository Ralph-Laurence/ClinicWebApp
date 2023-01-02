<?php 

// This class basically wraps every page URLs into a named variable
// for easy management for future use
class Navigation
{
    // Web page urls are prefixed with URL_
    public static $URL_CHECKUP_FORM             = "page.checkup-form.php";
    public static $URL_STOCKS_INVENTORY         = "page.stocks-inventory.php";
    public static $URL_RESTOCK                  = "page.restock.php";
    public static $URL_PATIENT_RECORDS          = "page.patient-records.php";
    public static $URL_ADD_NEW_ITEM             = "page.add-new-item.php";
    public static $URL_EDIT_ITEM                = "page.edit-item.php";
    public static $URL_DELETE_ITEM              = "action.delete-item.php";
    public static $URL_LOGIN                    = "login.php";
    public static $URL_LOGOUT                   = "logout.php";
    public static $URL_HOME                     = "page.home.php";

    // XHR (XMLHttpRequest) urls are prefixed with AJAX_
    public static $AJAX_ADD_NEW_ITEM            = "ajax.add-new-item.php";
    public static $AJAX_GET_MEDICINES           = "ajax.get-medicines.php";
    public static $AJAX_GENERATE_FORM_NUMBER    = "ajax.generate-form-number.php";
    public static $AJAX_SAVE_CHECKUP_INFO       = "ajax.save-checkup-info.php";

    // Side Nav Link Index/Indeces
    public static $NavIndex_Checkup = 1;
    public static $NavIndex_Restock = 2; 
    public static $NavIndex_Stocks = 3; 
    public static $NavIndex_Suppliers = 4; 
    public static $NavIndex_Patients = 5; 
    public static $NavIndex_Notifications = 6; 
    public static $NavIndex_Users = 7; 

    public static $NavIndex_Illness = 8; 
    public static $NavIndex_Categories = 9; 
    public static $NavIndex_Settings = 10; 
}

// we will use this flag / marker to identify which
// navigation link item should we highlight. The index
// should be equal to 1 or greater. If 0, then no item
// is marked active.
$activeNavIndex = 0;
//
// then we apply the active link flag using this function.
// We will call this method on the parent script which will
// include this script file.
function setActiveLink($index)
{
    global $activeNavIndex;
    $activeNavIndex = $index;
}
// 
// We will inline this function in the navlink item's class
// to apply the highlighting. The actual highlighting is 
// driven by CSS rule .side-nav-link-item .active
//
// Parameter $linkIndex describes the link item it was inlined to.
//
function highlightLink($linkIndex)
{
    global $activeNavIndex;

    if ($activeNavIndex >= 1 && $linkIndex == $activeNavIndex)
        echo "active";
}