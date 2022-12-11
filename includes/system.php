<?php 

class Dates
{
    public static function dateToday($format = "Y-m-d")
    { 
        return date($format);
    }
}

class Requests
{ 

    /**
    * Check if request is an AJAX call
    */ 
    public function isAjax() : bool
    {
        $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'; 

        return $isAjax;
    }
}

class ResponseCodes
{
    public static function success() : int { return 0; }
    public static function warning() : int { return 1; }
    public static function error() : int { return -1; }
}