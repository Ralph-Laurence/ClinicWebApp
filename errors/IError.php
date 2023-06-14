<?php 
@session_start();

require_once("rootcwd.inc.php");
require_once($cwd . "env.php");
 
/**
 * Wrapper for HTTP Error Headers with custom error page
 */
class IError
{   
    public const ERR_CODE_ACCESS_CONTROL = "SEC_ERR_ACCESS_CONTROL";

    // when CHMOD key is not in CH_PERM_MAP, ie. 112, 104 etc
    public const ERR_CODE_PERM_KEY_UNRECOGNIZED = "CH_UNKNOWN_PERM_KEY";    

    // Happens on Encryption/Decryption
    public const ERR_CODE_CRYPT = "SEC_ERR_CRYPT";

    // When no perm data was found in Db or from post
    public const ERR_CODE_EMPTY_PERM_KEY  = "CH_EMPTY_PERM_KEY";
    public const ERR_CODE_EMPTY_PERM_DATA = "CH_EMPTY_PERM_DATA";

    public const ERR_CODE_NO_PERM_KEY = "SEC_ERR_NO_PERM_KEY";

    // When there was a problem while loading the perm data in Db
    public const ERR_CODE_GET_PERM_DATA = "CH_GET_PERM_DATA";

    private static function getMessage(int $code)
    {
        $response = 
        [
            '200' => "OK",
            '301' => "Moved Permanently",
            '400' => "Bad Request",
            '403' => "Forbidden",
            '404' => "Not Found",
            '500' => "Internal Server Error"
        ];

        return $response[$code];
    }
  
    public static function Throw(int $code)
    {  
        // Generic Error 500
        if (!in_array($code, [200,301,400,403,404,500]))
        {
            header("HTTP/1.1 500 Internal Server Error");
            exit;
        }

        // Custom Error Response
        $response = self::getMessage($code);
        $redirect = ENV_SITE_ROOT . "errors/" . $code . ".php";
        
        if (!headers_sent())
        {
            header("HTTP/1.1 $code $response");
            header("Location: " . $redirect);
        }
        else
        {
            echo <<<JS
                <script type="text/javascript">
                    window.location.href="{$redirect}";
                </script>
            JS;
        }
        exit();
    }

    public static function ThrowSecErr($errCode = "")
    {   
        $_SESSION['sec-err'] = 1;
        $_SESSION['sec-err-code'] = $errCode;

        $redirect = ENV_SITE_ROOT . "errors/security-error.php";
        header("Location: " . $redirect);
        exit();
    }
}