<?php 
require_once("rootcwd.inc.php");
require_once($cwd . "env.php"); 
require_once($cwd . "includes/urls.php");
require_once($cwd . "includes/system.php");

require_once($cwd . "errors/IError.php");

class Chmod
{ 
    // Table Fieldnames
    public const id            = 'id';
    public const userFK        = 'user_fk_id';

    // Permission Key names
    public const PK_MAINTENANCE = "perm_maintenance";
    public const PK_MEDICAL     = "perm_medical";
    public const PK_INVENTORY   = "perm_inventory";
    public const PK_SUPPLIERS   = "perm_suppliers";
    public const PK_DOCTORS     = "perm_doctors";
    public const PK_USERS       = "perm_users";
    
    // Permission Flags
    public const FLAG_READ  = "r";      // VIEW ONLY
    public const FLAG_WRITE = "w";      // CREATE, READ, UPDATE, DELETE
    public const FLAG_DENY  = "x";      // DENY ACCESS

    private const CH_PERM_MAP =
    [
        "110" => "w",
        "100" => "r",
        "000" => "x"
    ];

    private $db;

    function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Load permission data for target user.
     */
    function getPerm(string $permKey, int $userId)
    {
        if (empty($permKey) || empty($userId))
            IError::ThrowSecErr(IError::ERR_CODE_EMPTY_PERM_KEY);

        try 
        {
            $perm = $this->db->selectValue(TableNames::chmod, $permKey, [
                self::userFK => $userId
            ]);
 
            return strval($perm) ?? "";
        } 
        catch (\Exception $ex) { IError::ThrowSecErr(IError::ERR_CODE_GET_PERM_DATA); exit; } 
        catch (\Throwable $th) { IError::ThrowSecErr(IError::ERR_CODE_GET_PERM_DATA); exit; } 
    } 

    public static function chmodToPerm(string $chmod)
    {
        $chkey = str_replace(',', '', trim($chmod));
        
        if (!in_array($chkey, array_keys(self::CH_PERM_MAP)))
        {
            IError::ThrowSecErr(IError::ERR_CODE_PERM_KEY_UNRECOGNIZED); 
            exit;
        }

        return self::CH_PERM_MAP[$chkey];
    }

    /**
     * Convert permission flag to chmod code.
     * Explode = true |> will return array of codes
     */
    public static function permToChCode($permFlag, $explode = false)
    {
        $map =
        [
            "w" => "110",
            "r" => "100",
            "x" => "000",
        ];

        if (!$explode && !in_array($permFlag, array_keys($map)))
        {
            IError::ThrowSecErr(IError::ERR_CODE_PERM_KEY_UNRECOGNIZED); 
            exit;
        }

        $code =
        [
            "w" => [1,1,0],
            "r" => [1,0,0],
            "x" => [0,0,0],
        ];

        if ($explode && !in_array($permFlag, array_keys($code)))
        {
            IError::ThrowSecErr(IError::ERR_CODE_PERM_KEY_UNRECOGNIZED); 
            exit;
        }
 
        return $explode ? $code[$permFlag] : $map[$permFlag];
    }
}