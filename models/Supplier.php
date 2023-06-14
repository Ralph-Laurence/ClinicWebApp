<?php

namespace Models 
{
    require_once("rootcwd.inc.php");
    require_once($rootCwd . "models/Item.php");
    require_once($rootCwd . "models/Category.php");

    use TableNames;
    use Utils;

    class Supplier
    { 
        private $db;

        function __construct($dbHelper)
        {
            $this->db = $dbHelper;    
        }

        // Get field names in table
        public static function getFields(): object
        {
            $obj = (object) [];

            $obj->id            = "id";
            $obj->name          = "name";
            $obj->contact       = "contact";
            $obj->address       = "address";
            $obj->email         = "email";
            $obj->description   = "description";

            return $obj;
        }
 
        public function getAll()
        {
            $dataset = $this->db->get(TableNames::suppliers, 'ASC', $this->getFields()->name);
            return $dataset;
        }

        public function find($id)
        {
            $fields = $this->getFields();
            $dataset = $this->db->findWhere(TableNames::suppliers, [$fields->id => $id]);

            return $dataset;
        }

        public function getMedicalSupplies($supplierId)
        {
            $items = TableNames::inventory;
            $categories = TableNames::categories;

            $i = Item::getFields();
            $c = Category::getFields();
            
            $fields = Utils::prefixJoin("i.", [$i->id, $i->itemName, $i->itemCode]);

            $sql = "SELECT $fields, c.$c->name AS category FROM $items AS i ".
            "LEFT JOIN $categories AS c ON i.$i->category = c.$c->id ". 
            "WHERE i.$i->supplierId = $supplierId ".
            "ORDER BY ". $i->itemName ." ASC";

            $medicines = $this->db->fetchAll($sql);
             
            return $medicines;
        }
    }
}