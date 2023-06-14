<?php

@session_start();

require_once("rootcwd.inc.php");
 
require_once($cwd . "env.php");
require_once($cwd . "database/configs.php");

class Helpers
{  
    public static function generateFormNumber()
    {
        global $dbName, $pdo;
        $table = TableNames::checkup_details;

        // return $checkupFormNumber;
        $sql = 
        "SELECT `AUTO_INCREMENT`
        FROM   `information_schema`.`tables`
        WHERE  `table_name` = '$table'
        AND    `table_schema` = '$dbName'";

        $autoInc = $pdo->query($sql)->fetch(PDO::FETCH_COLUMN);
        $checkupFormNumber = Dates::dateToday() . "-" . str_pad($autoInc, 5, "0", STR_PAD_LEFT);
        
        return $checkupFormNumber;
    }

    // defuse key will be used for enc/decryption.
    // the defuse crypto key is stored in the database.
    // this is a bad idea however .. but for the purpose
    // of simplicity we will store it on the database anyway
    public static function getDefuseKey($pdo)
    {
        $table = TableNames::configs;
        $sql = "SELECT configs_value FROM $table WHERE configs_key = 'defuse_key'";
        $defuseKey = $pdo->query($sql)->fetch(PDO::FETCH_COLUMN);

        return trim($defuseKey);
    }

    // Check if a string contains a substring
    public static function strContains($input, $substring)
    {
        return (strpos($input, $substring) !== false);
    }

    public static function getAvatarMap() : array
    {
        //
        // Avatar filenames with their human-readable name
        //
        $avatarMap =
        [
            "avatar_0" => "Male",
            "avatar_1" => "Female",
            "avatar_2" => "Pineapple",
            "avatar_3" => "Banana",
            "avatar_4" => "Watermelon",
            "avatar_5" => "Black Widow",
            "avatar_6" => "Money Heist",
            "avatar_7" => "Matrix Architect",
            "avatar_8" => "Joker",
            "avatar_9" => "Ninja Turtle",
            "avatar_10" => "Minecraft",
            "avatar_11" => "Cat",
            "avatar_12" => "Bad Piggy",
            "avatar_13" => "Lemur",
            "avatar_14" => "Bandicoot",
            "avatar_15" => "Madagascar",
            "avatar_16" => "Dinosaur",
            "avatar_17" => "Shrek",
            "avatar_18" => "Dog",
            "avatar_19" => "Hulk",
            "avatar_20" => "Ninja",
            "avatar_21" => "Knight",
            "avatar_22" => "Administrator",
            "avatar_23" => "Super Mario",
            "avatar_24" => "Male 2",
            "avatar_25" => "Worker",
            "avatar_26" => "Female 2",
            "avatar_27" => "Female Agent",
            "avatar_28" => "Agent Smith",
            "avatar_29" => "Hitman",
            "avatar_30" => "Star Wars",
            "avatar_31" => "Jake"
        ];

        return $avatarMap;
    }
}
/**
 * This class wraps the currently logged on user's 
 * Access Permissions into static functions
 */
class UserPerms
{
    public static $permKey_checkup      = 'perm_checkup_form';
    public static $permKey_restock      = 'perm_restock';
    public static $permKey_suppliers    = 'perm_suppliers';
    public static $permKey_inventory    = 'perm_inventory';
    public static $permKey_patients     = 'perm_patient_records';
    public static $permKey_illness      = 'perm_illness';
    public static $permKey_categories   = 'perm_categories';
    public static $permKey_users        = 'perm_users';

    public static function getCheckupAccess() : string
    {
        if (isset($_SESSION[self::$permKey_checkup]))
            return $_SESSION[self::$permKey_checkup];

        return "";
    }

    public static function getRestockAccess() : string
    {
        if (isset($_SESSION[self::$permKey_restock]))
            return $_SESSION[self::$permKey_restock];

        return "";
    }

    public static function getSuppliersAccess() : string
    {
        if (isset($_SESSION[self::$permKey_suppliers]))
            return $_SESSION[self::$permKey_suppliers];

        return "";
    }

    public static function getInventoryAccess() : string
    {
        if (isset($_SESSION[self::$permKey_inventory]))
            return $_SESSION[self::$permKey_inventory];

        return "";
    }

    public static function getPatientsAccess() : string
    {
        if (isset($_SESSION[self::$permKey_patients]))
            return $_SESSION[self::$permKey_patients];

        return "";
    }

    public static function getIllnessAccess() : string
    {
        if (isset($_SESSION[self::$permKey_illness]))
            return $_SESSION[self::$permKey_illness];

        return "";
    }

    public static function getCategoriesAccess() : string
    {
        if (isset($_SESSION[self::$permKey_categories]))
            return $_SESSION[self::$permKey_categories];

        return "";
    }

    public static function getUsersAccess() : string
    {
        if (isset($_SESSION[self::$permKey_users]))
            return $_SESSION[self::$permKey_users];

        return "";
    }

    public static function hasAccess($perm)
    { 
        if (empty($perm))   
            return false;

        if ($perm == 'f')
            return true;

        if ($perm == 'x')
            return false;
    }
}

class PatientTypes
{
    static $types = 
    [
        1 => "Student",
        2 => "Teacher",
        3 => "Staff"
    ];

    public static $STUDENT = 1;
    public static $TEACHER = 2;
    public static $STAFF = 3;

    public static function toDescription($type) : string
    {
        $desc = "Unknown";

        switch($type)
        {
            case 1:
                $desc = self::$types[self::$STUDENT];
                break;
            case 2:
                $desc = self::$types[self::$TEACHER];
                break;
            case 3:
                $desc = self::$types[self::$STAFF];
                break;
        }

        return $desc;
    }
}

class GenderTypes
{
    static $types = 
    [
        1 => "Male",
        2 => "Female"
    ];

    public static $MALE = 1;
    public static $FEMALE = 2;

    /**
     * Get the descriptive equivalent of gender.
     * 
     * @param bool $short Shorten the gender to just initials 
     */
    public static function toDescription($type, bool $short = false) : string
    {
        $desc = "Unknown";

        switch($type)
        {
            case 1:
                $desc = $short ? self::$types[self::$MALE][0] : self::$types[self::$MALE];
                break;
            case 2:
                $desc = $short ? self::$types[self::$FEMALE][0] : self::$types[self::$FEMALE];
                break; 
        }

        return $desc;
    }
}

class Utils
{
    /**
     * Add a prefix to every array element
     */
    public static function prefixArray(string $affix, array $array)
    {
        return preg_filter("/^/", $affix, $array);
    }
    /**
     * Add a suffix to every array elements
     */
    public static function postfixArray(string $affix, array $array)
    {
        return preg_filter("/$/", $affix, $array);
    }
    /**
     * Check for empty values
     */
    public static function hasEmpty(array $values)
    {
        foreach ($values as $v)
        {
            if (empty($v))
                return true;
        }

        return false;
    }
    /**
     * Prefix an array then join as single string using comma
     */
    public static function prefixJoin(string $affix, array $array, $separator = ",")
    {
        return implode($separator, self::prefixArray($affix, $array));
    }
    /**
     * Suffix an array then join as single string using comma
     */
    public static function suffixJoin(string $affix, array $array, $separator = ",")
    {
        return implode($separator, self::postfixArray($affix, $array));
    }
}

function throw_response_code($code, $redirect = "")
{
    $responseCodes = 
    [
        200 => "OK",
        301 => "Moved Permanently",
        400 => "Bad Request",
        403 => "Forbidden",
        404 => "Not Found",
        500 => "Internal Server Error"
    ];

    $responseDesc = $responseCodes[$code];
    header("HTTP/1.1 $code $responseDesc");
    
    if (!empty($redirect))
    {
        header("Location: " . $redirect);
        exit();
    }
    
    header("Location: " . ENV_SITE_ROOT . "errors/" . $code . ".php");
    
    exit();
}

