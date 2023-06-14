<?php

namespace Models 
{ 
    class Degrees
    { 
        public $id      = 0;
        public $degree  = "";

        // Get field names in table
        public static function getFields() : object
        {
            $obj = (object) [];
         
            $obj->id     = "id";
            $obj->degree = "degree"; 
        
            return $obj;
        }
    }
}