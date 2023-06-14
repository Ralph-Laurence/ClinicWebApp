<?php

// Points to upper directory
$cwd = str_replace("\\", "/", dirname(__FILE__, 2)) . "/";
require_once($cwd . "env.php");
require_once($cwd . "models/SettingsIni.php");

$set = new SettingsIni();

try 
{
    $ini = $set->Read();

    $configData =
    [
        "host"  =>  $set->GetValue($set->sect_Database, $set->iniKey_Host),
        "uid"   =>  $set->GetValue($set->sect_Database, $set->iniKey_Uid),
        "pass"  =>  $set->GetValue($set->sect_Database, $set->iniKey_Pass),
        "db"    =>  $set->GetValue($set->sect_Database, $set->iniKey_DbName),
        "port"  =>  $set->GetValue($set->sect_Database, $set->iniKey_Port),
    ];
} 
catch (\Throwable $th) 
{
    die("Failed to read database server configuration.");
}

foreach (array_values($configData) as $v)
{
    if (empty($v))
        die("Failed to initialize the connection to the database server");
}

// The server we will be using for the deployment is currently
// located somewhere abroad. So, the server's time will be much
// different than our computers' time. In our case, as a client
// our computers are located here in Philippines .. Therefore,
// we will set the timezone to Asia/Manila to get the
// correct time.
date_default_timezone_set("Asia/Manila");

//define('base_url', "http://localhost/projects/clinic/");

// check wether we are running on production or development (local) server.
// we do this to let the server configure the database authentication for us.
function IsLocalhost($whitelist = ['127.0.0.1', '::1']) 
{
    return in_array($_SERVER['REMOTE_ADDR'], $whitelist);
}

// These variables below are database connection parameters which are
// required by the database connection expression string.
// The connection string is an expression that contains the 
// parameters required for the applications to connect to a database server. 
// Here, the connection strings include the server instance, database name, 
// authentication details, and some other settings to communicate with the database server.

// Server Instance          // For development      // For deployment 
$host = IsLocalhost()       ? "localhost"           : $configData['host']; // "sql310.epizy.com";

// Server Authentication    // For development      // For deployment
$uid = IsLocalhost()        ? "root"                : $configData['uid']; // "epiz_33161880";
$password = IsLocalhost()   ? ""                    : $configData['pass']; // "0WaqnWunBVB0FM";

// The Database             // For development      // For deployment
$dbName = IsLocalhost()     ? "patient_infosys"     : $configData['db']; //"epiz_33161880_patient_infosys";

// The development server and the deployment server uses the same PORT number
// so, we will leave it as 3306 in here. 
// These ports are used to identify connection ENDPOINTS.
$port =  $configData['port']; // 3306;

// The connection object which we will use later in many parts
// of this application / system
$pdo = null;

try 
{
    // build database connection provider string / expression
    $dsn = "mysql:host=$host;dbname=$dbName;port=$port";

    // the database connection (PDO means PHP Data Object)
    // pdo is more secure in a way that it prevents an attack
    // called SQL Injection.
    $pdo = new PDO($dsn, $uid, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
} 
catch (\Throwable $th) 
{
    // We abort the execution and throw an error message.
    die("Connection to server failed!");
} 
//
// This class wraps the table names into named variables
// for maintainability, brevity and integrity.
// This class does nothing other than store all references
// to the database table names
//
class TableNames
{
    public const categories           = "categories";
    public const category_icons       = "category_icons";
    public const checkup_details      = "checkup_details";
    public const chmod                = "chmod";
    public const configs              = "configurations";
    public const doctor_degrees       = "degrees";
    public const doctor_specialties   = "doctor_specialties";
    public const doctors              = "doctors";
    public const illness              = "illness";
    public const inventory            = "items";
    public const patient_types        = "patient_types";
    public const patients             = "patients";
    public const permissions          = "user_permissions";
    public const prescription_details = "prescription_details";
    public const settings             = "general_settings";
    public const stock                = "stock";
    public const suppliers            = "suppliers";
    public const unit_measures        = "unit_measures";
    public const users                = "users";
    public const waste                = "waste";
}
//
// This class describes all access levels.
// This class does nothing other than hold references
// to the access flags.
//
class AccessFlags
{
    public static $ALLOW    = "f";
    public static $DENIED   = "x";
}
//
// This class describes the different user levels 
// with their role id.
// There will be basically 3 types of users.
// 
// 3 = Super Admin  -> God Mode, The owner
// 2 = Admin        -> Higher Privilege than staff
// 1 = Staff        -> Moderate Access
// 0 - Inactive     -> User is registered but not allowed 
//                     to access the system
class UserRoles
{
    public const SUPER_ADMIN = 3;
    public const ADMIN = 2;
    public const STAFF = 1;
    public static $INACTIVE = 0;

    // Minimum total super admins to preserve
    public static $MIN_SUPER_ADMIN_COUNT = 2;

    // convert role number to its descriptive name
    public static function ToDescName($role) : string
    {
        $role = intval($role) ?? 0;
        $desc = "";

        switch($role)
        {
            case 3:
                $desc = "Super Admin";
                break;        
            case 2:
                $desc = "Admin";
                break;        
            case 1:
                $desc = "Staff";
                break;        
            default:
                $desc = "Guest";
                break;
        }

        return $desc;
    }
}

// Just a description of the app's version which can be changed later on.
$app_version = $set->GetValue($set->sect_SysInfo, $set->iniKey_AppVersion); //"1.4.0";