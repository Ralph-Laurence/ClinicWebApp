<?php

namespace Models 
{

    use TableNames;

    class DoctorSpecialty
    { 
        public static $id = 0; 
        public static $spec = "";

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
            $obj->spec = "specialization";
        
            return $obj;
        }
        /**
         * Get all doctor specializations and return Assoc Array with
         * Id as its KEY and specs as its VALUE
         */
        public function getAll()
        {
            $raw = $this->db->get(TableNames::doctor_specialties);
            $specs = [];
            $fields = $this->getFields();

            foreach ($raw as $obj)
            {
                $specs[$obj[$fields->id]] = $obj[$fields->spec];
            }

            return $specs;
        }

        // public function getSpecIds()
        // {
        //     $ids = [];
        //     $fields = $this->getFields();
        //     $raw = $this->db->select(TableNames::doctor_specialties, [$fields->id]);

        //     foreach ($raw as $id)
        //     {
        //         $ids[] = $id[$fields->id];
        //     }

        //     return $ids;
        // }
    }
}

