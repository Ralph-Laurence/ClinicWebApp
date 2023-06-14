<?php

namespace Models 
{

    use TableNames;

    require_once("rootcwd.inc.php");
    require_once($rootCwd . "models/Item.php");

    class Category
    {
        public $id = 0;
        public $name = "";
        public $icon = 0;
        //public $fasIcon = "";

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
            $obj->icon = "icon"; 
            $obj->dateCreated = "date_created";
            //$obj->fasIcon = "icon";
        
            return $obj;
        }

        public function getAll()
        {
            $fields = self::getFields();

            $result = $this->db->select(TableNames::categories,
            [
                $fields->id,
                $fields->name,
                $fields->icon
            ], [], $this->db->ORDER_MODE_ASC, $fields->name);

            return $result;
        }

        public function showAll()
        {
            $c = self::getFields();
            $i = Item::getFields();
            
            $items = TableNames::inventory;
            $categories = TableNames::categories;

            $sql = 
            "SELECT 
                c.$c->id,
                c.$c->name,
                c.$c->icon,
                c.$c->dateCreated,
            (SELECT COUNT(i.$i->id) FROM $items AS i WHERE i.$i->category = c.$c->id) AS 'totalItems'
            FROM $categories AS c 
            ORDER BY c.$c->name ASC";
 
            $result = $this->db->fetchAll($sql);

            return $result ?? [];
        }

        public function getIcons()
        { 
            $path = BASE_DIR . "assets/images/inventory/";

            // Get all files in directory
            $files = array_values(array_diff(scandir($path), array('.', '..'))); 
            
            // Natural sort (for alpha numeric sorting like how winExplorer displays files)
            natsort($files);

            //return $files;

            $out = [];

            foreach ($files as $file)
            {
                // remove .png from name
                $justName = str_replace(".png", "", $file);
                $sanitized = str_replace("_", " ", $justName);

                $out[$justName] = $sanitized;
            }

            return $out;
        }
    }
}
 