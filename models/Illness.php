<?php

namespace Models 
{

    use TableNames;
    
    require_once("rootcwd.inc.php");
    require_once($rootCwd . "models/Checkup.php");

    class Illness
    {
        public $id = 0;
        public $name = ""; 

        private $db;

        function __construct($dbHelper)
        {
            $this->db = $dbHelper;
        }

        // Get field names in table
        public static function getFields() : object
        {
            $obj = (object) [];
            $obj->id = "id";
            $obj->name = "name";
            $obj->description = "illness_description";
            $obj->dateCreated = "date_created";

            return $obj;
        }

        public function showAll()
        {
            $c = Checkup::getFields();
            $i = self::getFields();
            $checkup = TableNames::checkup_details;
            $illness = TableNames::illness;

            $sql = 
            "SELECT i.*,
            (SELECT COUNT(c.$c->id) FROM $checkup AS c WHERE c.$c->illnessId = i.$i->id) AS 'totalCheckupRecords'
            FROM $illness AS i
            ORDER BY i.$i->name ASC";

            $result = $this->db->fetchAll($sql);

            return $result ?? [];
        }
    }
}

namespace TableFields 
{
    class IllnessFields
    {
        public static $id = "id";
        public static $name = "name";
    }
}

