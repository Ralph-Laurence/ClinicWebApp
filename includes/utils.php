<?php 

require_once("rootcwd.inc.php");
 
require_once($cwd . "env.php");

class Helpers
{
    /**
     * Find the last inserted id into table.
     * $conn must be of type PDO.
     */
    public static function getLastId($conn, $table, $id = "id")
    {
        if ($conn == null)
            die("Something went wrong while generating form data.");

        $sql = "SELECT $id FROM $table ORDER BY $id DESC LIMIT 1"; 
        $result = $conn->query($sql)->fetch(PDO::FETCH_COLUMN);

        if ($result == null)
            $result = 0;

        return $result;
    }

    public static function generateFormNumber($pdo)
    {
        $lastCheckupFormId = self::getLastId($pdo, TableNames::$checkup) + 1; 
        $checkupFormNumber = Dates::dateToday() . "-" . str_pad($lastCheckupFormId, 5, "0", STR_PAD_LEFT);

        return $checkupFormNumber;
    }

    // defuse key will be used for enc/decryption.
    // the defuse crypto key is stored in the database.
    // this is a bad idea however .. but for the purpose
    // of simplicity we will store it on the database anyway
    public static function getDefuseKey($pdo)
    {
        $table = TableNames::$configs_table;
        $sql = "SELECT configs_value FROM $table WHERE configs_key = 'defuse_key'";
        $defuseKey = $pdo->query($sql)->fetch(PDO::FETCH_COLUMN);

        return trim($defuseKey);
    }

    // Check if a string contains a substring
    public static function strContains($input, $substring)
    {
        return (strpos($input, $substring) !== false);
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