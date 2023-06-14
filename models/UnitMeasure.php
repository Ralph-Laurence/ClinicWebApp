<?php

namespace Models 
{

    use TableNames;

    class UnitMeasure
    {
        public $id = 0;
        public $measurement = "";

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
            $obj->measurement = "measurement";
        
            return $obj;
        }

        public function getAll()
        {
            return $this->db->get
            (
                TableNames::unit_measures, 
                $this->db->ORDER_MODE_ASC, 
                self::getFields()->measurement
            );
        }
    }
}

namespace TableFields 
{
    class UnitMeasureFields
    {
        public static $id = "id";
        public static $measurement = "measurement";
    }
}
