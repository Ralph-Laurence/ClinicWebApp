<?php
require_once("rootcwd.inc.php");
require_once($cwd . "models/User.php");
use Models\User;
/** 
 * This class wraps the information of the currently 
 * logged in (Authenticated) user into static functions.
 * All these information are saved in a session var
*/
class UserAuth
{       
    //-------- USERNAME --------//
    public static function getUsername() : string
    {
        $field = User::getFields();

        if (isset($_SESSION[$field->username]))
            return $_SESSION[$field->username];

        return "";
    }

    public static function setUsername($username)
    { 
        $_SESSION[User::getFields()->username] = $username;
    }
    
    //-------- EMAIL --------//
    public static function getEmail() : string
    {
        $field = User::getFields();

        if (isset($_SESSION[$field->email]))
            return $_SESSION[$field->email];

        return "";
    }

    public static function setEmail($email)
    { 
        $_SESSION[User::getFields()->email] = $email;
    }

    // ------- ROLE ------- //
    public static function getRole() : int 
    {
        $field = User::getFields();

        if (isset($_SESSION[$field->role]))
            return intval($_SESSION[$field->role]);

        return -1;
    }

    public static function setRole($role)
    { 
        $_SESSION[User::getFields()->role] = $role;
    } 

    // ------ GUID ----- //
    public static function getGuid() : string
    {
        $field = User::getFields();

        if (isset($_SESSION[$field->guid]))
            return $_SESSION[$field->guid];

        return "";
    }

    public static function setGuid($guid)
    { 
        $_SESSION[User::getFields()->guid] = $guid;
    }

    // ------ FIRSTNAME ------ //
    public static function getFirstname() : string
    {
        $field = User::getFields();

        if (isset($_SESSION[$field->firstName]))
            return $_SESSION[$field->firstName];

        return "";
    }

    public static function setFirstname($firstName)
    { 
        $_SESSION[User::getFields()->firstName] = $firstName;
    }

    // ---- MIDDLENAME ---- //
    public static function getMiddlename() : string
    {
        $field = User::getFields();

        if (isset($_SESSION[$field->middleName]))
            return $_SESSION[$field->middleName];

        return "";
    }

    public static function setMiddlename($middlename)
    { 
        $_SESSION[User::getFields()->middleName] = $middlename;
    }

    // ---- LASTNAME ---- //
    public static function getLastname() : string
    {
        $field = User::getFields();

        if (isset($_SESSION[$field->lastName]))
            return $_SESSION[$field->lastName];

        return "";
    }

    public static function setLastname($lastname)
    { 
        $_SESSION[User::getFields()->lastName] = $lastname;
    }

    // ------ AVATAR ------ //
    public static function getAvatar() : string
    {
        $field = User::getFields();

        if (isset($_SESSION[$field->avatar])) 
            return $_SESSION[$field->avatar];

        return "";
    }

    public static function setAvatar($avatar)
    { 
        $_SESSION[User::getFields()->avatar] = $avatar;
    }

    // public static function getPassword() : string
    // {
    //     $field = User::getFields();

    //     if (isset($_SESSION[$field->password])) 
    //         return $_SESSION[$field->password];

    //     return "";
    // }

    public static function getId()
    {
        $field = User::getFields();

        if (isset($_SESSION[$field->id])) 
            return $_SESSION[$field->id];

        return "";
    }
}