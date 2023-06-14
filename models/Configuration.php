<?php

namespace Models 
{ 
    class Configuration
    { 
        public $configId    = 0;
        public $configKey   = "";
        public $configValue = "";

        // Get field names in table
        public static function getFields() : object
        {
            $obj = (object) [];
         
            $obj->configId      = "id";
            $obj->configKey     = "configs_Key";
            $obj->configValue   = "configs_value"; 
        
            return $obj;
        }
    }
}

