<?php

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
$host = IsLocalhost()       ? "localhost"           : "sql310.epizy.com";

// Server Authentication    // For development      // For deployment
$uid = IsLocalhost()        ? "root"                : "epiz_33161880";
$password = IsLocalhost()   ? ""                    : "0WaqnWunBVB0FM";

// The Database             // For development      // For deployment
$dbName = IsLocalhost()     ? "patient_infosys"     : "epiz_33161880_patient_infosys";

// The development server and the deployment server uses the same PORT number
// so, we will leave it as 3306 in here. 
// These ports are used to identify connection ENDPOINTS.
$port = 3306;

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
    public static $category_icons   = "category_icons";
    public static $categories       = "categories";
    public static $checkup          = "checkup";
    public static $illness          = "illness";
    public static $items            = "items";
    public static $prescription     = "prescription";
    public static $suppleirs        = "suppliers";
    public static $unit_measures    = "unit_measures";
    public static $patient_types    = "patient_types";
    public static $configs_table    = "configurations";
    public static $users            = "users";
    public static $permissions      = "user_permissions";
}
//
// This class describes all access levels.
// This class does nothing other than hold references
// to the access flags.
//
class AccessFlags
{
    //---------------------------------------------------------------------------------------------------------
    //  ACCESS FLAGS    TITLE       DESCRIPTION
    //  
    //  w               WRITE       -- This user can CREATE and save a record into database
    //  v               VIEW        -- This user can only view the page but cannot execute actions
    //  m               MODIFY      -- This user can EDIT and DELETE a record from database
    //  f               FULL        -- This user has FULL permission to CREATE, EDIT and DELETE
    //  x               DENIED      -- This user CANNOT access the page or any functionality
    //  
    //---------------------------------------------------------------------------------------------------------
    // :::::::::::::::::::::::::::::::::: SAMPLE USAGE OF ACCESS LEVELS :::::::::::::::::::::::::::::::::::::::
    //---------------------------------------------------------------------------------------------------------
    // MODULE           USER        ACCESS FLAG     ACTION              DESCRIPTION 
    // 
    // Checkup Form     user1       w               CREATE RECORD       -- This user can only view and create
    //                                                                     records from checkup form
    // 
    // Patient Record   user2       x               ACCESS DENIED       -- This user cannot access the features
    //                                                                     and functions of patient record page.
    //
    // ReStock          user3       f               FULL ACCESS         -- This user has the FULL access to all
    //                                                                     functions and features (God Mode)
    //                                                                     of the restock page.
    //---------------------------------------------------------------------------------------------------------

    // For maintainability, brevity and integrity, we will store those flags in a static variable.

    public static $WRITE    = "w";
    public static $VIEW     = "v";
    public static $MODIFY   = "m";
    public static $FULL     = "f";
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
    public static $SUPER_ADMIN = 3;
    public static $ADMIN = 2;
    public static $STAFF = 1;
    public static $INACTIVE = 0;

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
$app_version = "1.0.0";