<?php 

// This class basically wraps every page URLs into a named variable
// for easy management for future use
class Navigation
{
    // Web page urls are prefixed with URL_
    public static $URL_CHECKUP_FORM             = "page.checkup-form.php";
    public static $URL_STOCKS_INVENTORY         = "page.stocks-inventory.php";
    public static $URL_PATIENT_RECORDS          = "page.patient-records.php";
    public static $URL_ADD_NEW_ITEM             = "page.add-new-item.php";

    // XHR (XMLHttpRequest) urls are prefixed with AJAX_
    public static $AJAX_ADD_NEW_ITEM            = "ajax.add-new-item.php";
    public static $AJAX_GET_MEDICINES           = "ajax.get-medicines.php";
    public static $AJAX_GENERATE_FORM_NUMBER    = "ajax.generate-form-number.php";
    public static $AJAX_SAVE_CHECKUP_INFO       = "ajax.save-checkup-info.php";
}