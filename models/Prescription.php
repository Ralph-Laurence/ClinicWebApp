<?php

namespace Models 
{
    class Prescription
    {
        public $id = "";
        public $checkupForeignKey = 0;
        public $itemId = "";
        public $amount = "";
        public $unitMeasure = "";

        // Get field names in table
        public static function getFields() : object
        {
            $obj = (object) [];

            $obj->id            = "id";
            $obj->checkupFK     = "checkup_fk_id";
            $obj->itemId        = "item_id";
            $obj->amount        = "amount";
            $obj->unitMeasure   = "unit_measure";
            $obj->stockFK       = "stock_fk_id";
            $obj->dateCreated   = "date_created";
        
            return $obj;
        }
    }
}

namespace TableFields 
{
    class PrescriptionFields
    {
        public static $id = "id";
        public static $checkupForeignKey = "checkup_fk_id";
        public static $itemId = "item_id";
        public static $amount = "amount";
        public static $unitMeasure = "unit_measure";
    }
}
