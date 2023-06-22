<?php

namespace Models 
{

    use TableNames;

    require_once("rootcwd.inc.php");

    class Stock
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
            $obj->item_id       = "item_id";
            $obj->quantity      = "quantity";
            $obj->sku           = "sku";
            $obj->expiry_date   = "expiry_date"; 
            $obj->dateCreated   = "date_created";

            return $obj;
        }      
        
        // Pull out all units
        public function pullOutAll($itemId)
        {
            $stock = self::getFields(); 

            $this->db->update(TableNames::stock, [$stock->quantity => 0], [$stock->item_id = $itemId]);
        }

        // Subtract an amount from the stock quantity
        public function pullOut($amount, $itemId)
        {
            $stock = self::getFields(); 
            $table = TableNames::stock;
            $sql = "UPDATE $table SET $stock->quantity = $stock->quantity - ? WHERE $stock->item_id = ?";

            $stmt = $this->db->getInstance()->prepare($sql);
            $stmt->bindValue(1, $amount);
            $stmt->bindValue(2, $itemId);
            $stmt->execute();
        }

        /**
         * Return back the quantity of a stock coming from prescriptions
         */
        public function returnAndIncreaseStock()
        {
            
        }

        public function getItemsQuantity($itemIds = array())
        {
            $s = self::getFields(); 
            $table = TableNames::stock;
            $paramBinders = $this->db->generateBinders($itemIds);

            $sql = "SELECT $s->item_id, $s->quantity FROM $table WHERE $s->item_id IN ($paramBinders) ORDER BY $s->expiry_date ASC";
            $stmt = $this->db->getInstance()->prepare($sql);

            foreach ($itemIds as $k => $v)
            {
                $stmt->bindValue(($k + 1), $v);
            }

            $stmt->execute();
            
            $obj = $stmt->fetchAll();

            return $obj;
        }
    }
}