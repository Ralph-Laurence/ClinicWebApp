<?php 

require_once("rootcwd.inc.php");
require_once($cwd . "rootcwd.php");

// This class basically wraps every page URLs into a named variable
// for easy management for future use
class Navigation
{         
    // Side Nav Link Index/Indeces
    public const NavIndex_Statistics        = 1; 
    public const NavIndex_Forecast          = 2; 
    public const NavIndex_Register          = 3; 
    public const NavIndex_Checkup           = 4; 
    public const NavIndex_Restock           = 5; 
    public const NavIndex_Stocks            = 6; 
    public const NavIndex_Suppliers         = 7; 
    public const NavIndex_Patients          = 8;  
    public const NavIndex_CheckupRecords    = 9;  
    public const NavIndex_Doctors           = 10; 
    public const NavIndex_Users             = 11; 
    public const NavIndex_Calcu             = 12; 
    public const NavIndex_Converter         = 13; 
    public const NavIndex_Illness           = 14;  
    public const NavIndex_Categories        = 15;  
    public const NavIndex_Settings          = 16;   
    public const NavIndex_Profile           = 17; 
}

class AsyncActions
{
    public const INVENTORY_NOTIFIER       = "async/inventory-notifier.php";
    public const GET_MEDICAL_HISTORY      = "async/get-medical-history.php";
}

class Tasks
{
    public const CREATE_CHECKUP           = "tasks/create-checkup-details.php";
    public const REGISTER_PATIENT         = "tasks/register-patient.php";
    public const REGISTER_DOCTOR          = "tasks/register-doctor.php";
    public const DELETE_DOCTOR            = "tasks/delete-doctor.php";
    public const DELETE_DOCTORS           = "tasks/delete-doctors.php";
    public const EDIT_DOCTOR              = "tasks/update-doctor.php";
    public const DEFAULT_DOCTOR           = "tasks/change-default-doctor.php";

    public const DELETE_USER              = "tasks/delete-user.php";
    public const DELETE_USERS             = "tasks/delete-users.php";
    public const EDIT_USER                = "tasks/update-user.php";
    public const CREATE_USER              = "tasks/register-user.php";
    public const CHANGE_PASSWORD          = "tasks/change-password.php";
    public const UPDATE_PASSWORD          = "tasks/update-password.php";

    public const UPDATE_PATIENT           = "tasks/update-patient.php";
    public const UPDATE_CHECKUP_DETAILS   = "tasks/update-checkup-details.php";

    public const DELETE_CHECKUP_RECORD    = "tasks/delete-checkup-record.php";
    public const DELETE_CHECKUP_RECORDS   = "tasks/delete-checkup-records.php";
    public const DELETE_PATIENT           = "tasks/delete-patient.php";
    public const DELETE_PATIENTS          = "tasks/delete-patients.php";

    public const STOCK_IN                 = "tasks/stock-in.php";
    public const STOCK_OUT                = "tasks/stock-out.php";

    public const REGISTER_SUPPLIER        = "tasks/register-supplier.php";
    public const EDIT_SUPPLIER            = "tasks/update-supplier.php";
    public const DELETE_SUPPLIERS         = "tasks/delete-suppliers.php";
    public const DELETE_SUPPLIER          = "tasks/delete-supplier.php";

    public const REGISTER_ITEM            = "tasks/register-item.php";
    public const EDIT_ITEM                = "tasks/update-item.php";
    public const DELETE_ITEM              = "tasks/delete-item.php";
    public const DELETE_ITEMS             = "tasks/delete-items.php";
    public const DISCARD_ITEM             = "tasks/discard-item.php";
    public const DISCARD_STOCK            = "tasks/discard-stock.php";
    public const UPDATE_STOCK_EXPIRY      = "tasks/change-stock-expiry.php";

    public const DELETE_WASTE_ITEM        = "tasks/delete-waste.php";
    public const DELETE_WASTE_RECORDS     = "tasks/delete-waste-records.php";
    public const CLEAR_WASTE              = "tasks/clear-waste.php";
    public const CHANGE_REC_YEAR          = "tasks/change-rec-year.php";
    public const CHANGE_MAX_DAYS          = "tasks/change-max-days.php";
    public const CHANGE_CHECKUP_ACTION    = "tasks/change-checkupform-action.php";

    public const REGISTER_ILLNESS         = "tasks/register-illness.php";
    public const UPDATE_ILLNESS           = "tasks/update-illness.php";
    public const DELETE_ILLNESS           = "tasks/delete-illness.php";
    public const DELETE_ILLNESS_RECORDS   = "tasks/delete-illnesses.php";

    public const REGISTER_CATEGORY        = "tasks/register-category.php";
    public const UPDATE_CATEGORY          = "tasks/update-category.php";
    public const DELETE_CATEGORY          = "tasks/delete-category.php";
    public const DELETE_CATEGORIES        = "tasks/delete-categories.php";

    public const REVEAL_GUID              = "tasks/profile-see-guid.php";
    public const EDIT_PROFILE             = "tasks/update-profile.php";
 
    public const FORGOT_PASSWORD          = "tasks/forgot-password.php";

    public const LOGOUT                   = "logout.php";
}

class Pages
{
    public const LOGIN                = "login.php";
    public const HOME                 = "page.home.php";
    public const FORGOT_PASSWORD      = "page.forgot-password.php";

    // INSIGHTS
    public const STATISTICS           = "page.insights-statistics.php";

    // TRANSACTIONS
    public const CHECKUP_FORM         = "page.transaction-checkup-form.php";
    public const CHECKUP_GUARD        = "page.checkup-guard.php";
    public const REGISTER_PATIENT     = "page.transaction-register-form.php";
    public const RESTOCK              = "page.transaction-restock.php";

    // VIEWING
    public const CHECKUP_RECORDS      = "page.view-checkup-records.php";
    public const CHECKUP_DETAILS      = "page.view-checkup-details.php";
    public const PATIENTS             = "page.view-patient-records.php";
    public const PATIENT_DETAILS      = "page.view-patient-details.php";
    public const DOCTOR_DETAILS       = "page.view-doctor-details.php";
    public const DOCTORS              = "page.view-doctor-records.php"; 
    public const USERS                = "page.view-user-records.php";
    public const USER_DETAILS         = "page.view-user-details.php";
    public const SUPPLIERS            = "page.view-supplier-records.php";
    public const SUPPLIER_DETAILS     = "page.view-supplier-details.php";
    public const MEDICINE_INVENTORY   = "page.view-medicine-inventory.php";
    public const ITEM_DETAILS         = "page.view-item-details.php";
    public const SETTINGS             = "page.maintenance-settings.php";
    public const ILLNESS              = "page.maintenance-illness.php";
    public const CATEGORIES           = "page.maintenance-categories.php";

    // ACTIONS
    public const REGISTER_ITEM        = "page.action-register-item.php";
    public const REGISTER_DOCTOR      = "page.action-register-doctor.php";
    public const REGISTER_SUPPLIER    = "page.action-register-supplier.php";
    public const CREATE_USER          = "page.action-create-user.php";
    
    public const STOCK_IN             = "page.transaction-stock-in.php";
    public const ACTION_STOCK_IN      = "page.action-stock-in.php";
    public const STOCK_OUT            = "page.transaction-stock-out.php";
    public const ACTION_STOCK_OUT     = "page.action-stock-out.php";
    public const WASTE                = "page.waste.php";

    public const EDIT_USER            = "page.action-edit-user.php";    
    public const EDIT_DOCTOR          = "page.action-edit-doctor.php";
    public const EDIT_PATIENT         = "page.action-edit-patient.php";
    public const EDIT_CHECKUP_RECORD  = "page.action-edit-checkup.php";
    public const EDIT_SUPPLIER        = "page.action-edit-supplier.php";
    public const EDIT_ITEM            = "page.action-edit-item.php";

    public const EXTRAS_CALCU         = "page.accessories.calcu.php";
    public const EXTRAS_CONVERTER     = "page.accessories.converter.php";

    public const MY_PROFILE           = "page.my-profile.php";
    public const EDIT_MY_PROFILE      = "page.edit-my-profile.php";

    public static $CSV_IMPORTER       = "page.csv-importer.php";
}

// we will use this flag / marker to identify which
// navigation link item should we highlight. The index
// should be equal to 1 or greater. If 0, then no item
// is marked active.
$activeNavIndex = -1;
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

    if ($activeNavIndex >= 0 && $linkIndex == $activeNavIndex)
        return "active";
} 
//
// DOM onclick event will fire the javascript function navHref()
//
function redirectTo($url)
{
    global $rootUrl;
    $href = $rootUrl . $url;
    return "onclick=\"navHref('$href')\"";
}
//
// Redirect to specific link
//
function redirect($url)
{
    echo "onclick=\"navHref('$url')\"";
}