<?php 
@session_start();

require_once("rootcwd.inc.php");
require_once($cwd . "database/configs.php");
require_once($cwd . "database/dbhelper.php");
require_once($cwd . "includes/system.php");
require_once($cwd . "errors/IError.php");
require_once($cwd . "models/Configuration.php");
require_once($cwd . "includes/Chmod.php");

require_once($cwd . "library/defuse-crypto.phar");

use Defuse\Crypto\Crypto;
use Defuse\Crypto\Key;
use Models\Configuration;

class Security
{ 
    private $DEFUSE_KEY_CONFIG_KEYNAME = "defuse_key";
 
    private $configsFields = null;
    private $requiredPerms = array();

    function __construct(bool $requireLogin = true)
    { 
        // Force the user to login.
        if ($requireLogin)
        {
            if (!isset($_SESSION['isLoggedIn']) || $_SESSION['isLoggedIn'] !== true) 
            {
                Response::Redirect((ENV_SITE_ROOT . Pages::LOGIN), Response::Code403);
                exit;
            }
        }

        $configsModel = new Configuration();
        $this->configsFields = $configsModel->getFields();
    }
 
    ////////////////////////////////////////////////////
    //          ENCRYPTION & DECRYPTION LOGIC         //
    ///////////////////////////////////////////////////
    /**
    * The input must not be empty. Otherwise, redirect to security error page.
    * 
    * @param string $raw -> The plain text string.
    * @return string 
    */
    public function Encrypt(string $raw)
    {
        try 
        {
            $defuseKey = $this->LoadDefuseKey();
            $encrypt = Crypto::encrypt(strval($raw), $defuseKey);

            return $encrypt;
        } 
        catch (\Exception $ex) { $this->onSecurityError(IError::ERR_CODE_CRYPT); } 
        catch (\Throwable $th) { $this->onSecurityError(IError::ERR_CODE_CRYPT); }
    }
    /**
    * The hash must not be empty. Otherwise, redirect to security error page
    * 
    * @param string $hash -> The encrypted string hash.
    * @return string 
    */
    public function Decrypt($hash)
    {
        try 
        {
            $defuseKey = $this->LoadDefuseKey();
            $decrypt = Crypto::decrypt($hash, $defuseKey);

            return $decrypt;
        } 
        catch (\Exception $ex) { $this->onSecurityError(IError::ERR_CODE_CRYPT); } 
        catch (\Throwable $ex) { $this->onSecurityError(IError::ERR_CODE_CRYPT); }
    }
    /**
    * Check if a hash is a valid hash. We do the checking by decryption
    * 
    * @param string $hash -> The encrypted string hash.
    * @return string 
    */
    public function isValidHash($hash)
    {
        try 
        {
            $defuseKey = $this->LoadDefuseKey();
            $d = Crypto::decrypt($hash, $defuseKey);

            return true;
        } 
        catch (\Exception $ex) { return false; }  
        catch (\Throwable $ex) { return false; }  
    }
    // Defuse key will be used for encryption and decryption. 
    // The key is stored in a database table.
    // This is a bad idea however .. but for the purpose
    // of simplicity we will store it on the database anyway  
    private function LoadDefuseKey()
    {
        global $pdo;  

        $defuseKeyAscii = "";

        try 
        {
            // Check the session var first if there was already a defuse key loaded
            if (isset($_SESSION["DEFUSE_KEY"])) 
            {
                $defuseKeyAscii = $_SESSION["DEFUSE_KEY"];
            }
            // Otherwise, load it from database. Then store it in a session var
            else 
            {
                $table = TableNames::configs;

                // Field names
                $fields =
                    [
                        $this->configsFields->configKey,
                        $this->configsFields->configValue
                    ];

                $sql = "SELECT $fields[1] FROM $table WHERE $fields[0] = '{$this->DEFUSE_KEY_CONFIG_KEYNAME}'";
                $defuseKeyAscii = $pdo->query($sql)->fetch(PDO::FETCH_COLUMN);
                $_SESSION["DEFUSE_KEY"] = $defuseKeyAscii;
            }

            $defuseKey = Key::loadFromAsciiSafeString(trim($defuseKeyAscii));
        } 
        catch (\Exception $ex) { $this->onSecurityError(IError::ERR_CODE_CRYPT); } 
        catch (\Throwable $th) { $this->onSecurityError(IError::ERR_CODE_CRYPT); }

        return $defuseKey;
    }
 
    ////////////////////////////////////////////////////
    //              ACCESS RESTRICTIONS               //
    ///////////////////////////////////////////////////

    /**
    * Block any access to the parent script
    * where this function was attached to.
    * This will only allow POST requests.
    */
    public function BlockNonPostRequest()
    {
        // make sure that this script file will only execute when accessed
        // with POST request.
        if ($_SERVER['REQUEST_METHOD'] != 'POST') 
        {
            IError::Throw(404);
            exit;
        }
    }   
    /**
    * Block any access to the parent script
    * where this function was attached to.
    * This will only allow AJAX requests.
    */
    public function BlockNonAjaxRequest()
    {
        if (!$this->IsAjax()) 
        {
            IError::Throw(404);
            exit;
        }
    }
    //
    // Check if request is coming from AJAX
    //
    private function IsAjax(): bool
    {
        $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) and
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

        return $isAjax;
    }
 
    public function requirePermission($permKey, $permFlag)
    {
        if (empty($permFlag) || empty($permKey))
            $this->onSecurityError(IError::ERR_CODE_NO_PERM_KEY);
 
        // When permission is already defined, update its flags
        if (array_key_exists($permKey, $this->requiredPerms)) 
        {
            $existingPerm = $this->requiredPerms[$permKey];

            array_push($existingPerm, $permFlag);
            $this->requiredPerms[$permKey] = $existingPerm;
        } 
        // Otherwise, define the new permission
        else 
        { 
            $this->requiredPerms[$permKey] = [$permFlag];
        }   
    }
    /**
     * Check if a user(id) has access to a specific feature.
     */
    public function checkAccess($permKey, $userId)
    {  
        if (empty($userId) || empty($permKey))
            $this->onSecurityError();

        global $pdo;
        $db = new DbHelper($pdo);
        $chmod = new Chmod($db);
 
        // Perm key must be present first
        if (!array_key_exists($permKey, $this->requiredPerms))
            $this->onDeny();

        // Get the user's perm flag value
        $flag = $chmod->getPerm($permKey, $userId);
         
        // If perm is W, allow the user
        if ($flag == Chmod::FLAG_WRITE)
            return; 

        // Get the required perm flag value
        $permFlagValues = $this->requiredPerms[$permKey];
  
        // Match the permissions
        if (in_array($flag, $permFlagValues))
            return;

        $this->onDeny();
    }
    //
    // Throw error page
    //
    function onSecurityError($errCode = "")
    { 
        IError::ThrowSecErr($errCode);
        exit;
    }
    //
    //
    //
    function onDeny()
    {
        IError::Throw(Response::Code403);
        exit;
    } 
}