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

            return $obj;
        }      
        
        // Empty a stock into zero 
        public static function truncateQuantity($id)
        {
            $stock = self::getFields(); 

            self::$db->update(TableNames::stock, [$stock->quantity => 0], [$stock->id = $id]);
        }

        // Subtract an amount from the stock quantity
        public static function pullOut($amount, $id)
        {
            $stock = self::getFields(); 
            $table = TableNames::stock;
            $sql = "UPDATE $table SET $stock->quantity = $stock->quantity - ? WHERE $stock->id = ?";

            $stmt = self::$db->getInstance()->prepare($sql);
            $stmt->bindParam("ii", $amount, $id);
            $stmt->execute();
        }
    }
}