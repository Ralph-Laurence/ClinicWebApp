<?php

namespace Models 
{
    
    use DbHelper;
    use TableNames;
    use UserRoles;
    use Utils;

    require_once("rootcwd.inc.php");

    class User
    {
        public $id = "";
        public $username = "";
        public $email = "";
        public $password = "";
        public $role = "";
        public $guid = "";
        public $firstName = "";
        public $middleName = "";
        public $lastName = "";
        public $avatar = "";
        public $date_created = "";

        private DbHelper $db; 

        public function __construct($db = null)
        {
            $this->db = $db; 
        }

        public function showAll(string $order = "ASC", string $by = "", $filterRoles = 0)
        {
            $fields = User::getFields();

            $cols = 
            [
                $fields->id,
                $fields->firstName,
                $fields->middleName,
                $fields->lastName, 
                $fields->username,
                $fields->email,
                $fields->role,
                $fields->avatar    
            ];

            $roles = [UserRoles::ADMIN, UserRoles::SUPER_ADMIN, UserRoles::STAFF];

            // If the role filter is not recognized as a valid role, show all users
            if (empty($filterRoles) || !in_array($filterRoles, $roles))
            { 
                $result = $this->db->selectCols($cols, TableNames::users, false, $order, $by);
            }
            // Apply the filter otherwise
            else 
            { 
                $result = $this->db->select(TableNames::users, $cols, [ $fields->role => $filterRoles ], $order, $by);
            }

            return $result ?? [];
        }

        public function find($id)
        {
            $u = $this->getFields();
            $select = implode(",", [$u->id, $u->firstName, $u->middleName, $u->lastName, $u->username, $u->email, $u->role, $u->avatar, $u->date_created]);

            $sql = "SELECT $select FROM ". TableNames::users ." WHERE $u->id = $id";
            $result = $this->db->fetchAll($sql, true);
            
            return $result ?? [];
        } 
        
        public static function getFields(): object
        {
            $obj = (object) [];

            $obj->id = "id";
            $obj->username = "username";
            $obj->email = "email";
            $obj->password = "password";
            $obj->role = "role";
            $obj->guid = "guid";
            $obj->firstName = "firstname";
            $obj->middleName = "middlename";
            $obj->lastName = "lastname";
            $obj->avatar = "avatar";
            $obj->date_created = "date_created";

            return $obj;
        }
    }
}
