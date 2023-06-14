<?php

class Dates
{
    public static function dateToday($format = "Y-m-d")
    {
        return date($format);
    }

    public static function getYear()
    {
        return date("Y");
    }

    public static function dateYesterday($format = "Y-m-d")
    {
        return date($format, strtotime("yesterday"));
    }

    public static function toString($raw, $format = "Y-m-d")
    {
        try
        {
            $date = date_create($raw);
            $result = date_format($date, $format);

            return $result;
        }
        catch (Exception $ex)
        {
            // If there was a problem with formatting the date,
            // return the original string
            return $raw;
        }
    }

    public static function createTimestamp()
    {
        $timestamp = date("Y-m-d H:i:s");
        return $timestamp;
    }

    public static function isPast($date)
    {
        return (!empty($date) && ( strtotime($date) < strtotime(date('Y-m-d')) ));
    }
} 

class IString
{
    public static function contains($haystack, $needle)
    {
        $contains = strpos($haystack, $needle) !== false;
        return $contains;
    }

    // https://stackoverflow.com/a/10473026/15013058
    public static function startsWith($haystack, $needle) {
        return substr_compare($haystack, $needle, 0, strlen($needle)) === 0;
    }

    public static function endsWith($haystack, $needle) {
        return substr_compare($haystack, $needle, -strlen($needle)) === 0;
    }

    public static function random($n)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';

        for ($i = 0; $i < $n; $i++) {
            $index = rand(0, strlen($characters) - 1);
            $randomString .= $characters[$index];
        }

        return $randomString;
    }
}

function dump($var)
{
    highlight_string("<?php\n// var dump\n" . var_export($var, true) . ";\n?>");
} 

class Response
{
    // HTTP Response codes
    public const Code200 = 200; 
    public const Code301 = 301; 
    public const Code400 = 400; 
    public const Code403 = 403; 
    public const Code404 = 404; 
    public const Code418 = 418; 
    public const Code500 = 500; 

    // HTTP Response messages
    private static $responseMessages = 
    [
        'code_200' => "OK",
        'code_301' => "Moved Permanently",
        'code_400' => "Bad Request",
        'code_403' => "Forbidden",
        'code_404' => "Not Found",
        'code_418' => "I'm a teapot",
        'code_500' => "Internal Server Error"
    ];

    public static function getMessage(int $code)
    {
        $key = "code_" . $code;
        return self::$responseMessages[$key];
    }

    /**
     * Redirect the user to a specific URL with extra message and HTTP status codes
     */
    public static function Redirect(string $url, int $httpCode, string $extraMessage = "", string $messageTitle = "")
    {
        // Set headers 
        if (!headers_sent())
            header("HTTP/1.1 $httpCode " . self::getMessage($httpCode));

        // If has extra message, save it to session
        if (!empty($extraMessage))
            $_SESSION[$messageTitle] = $extraMessage;

        // Redirect to URL
        header("Location: " . $url);
        exit;
    }
}
     
