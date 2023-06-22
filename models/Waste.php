<?php

namespace Models 
{

    use SettingsIni;
    use TableNames;

    require_once("rootcwd.inc.php");
    require_once($rootCwd . "models/Item.php");
    require_once($rootCwd . "models/Stock.php");
    require_once($rootCwd . "models/UnitMeasure.php");
    require_once($rootCwd . "models/SettingsIni.php");
  
    class Waste
    {  
        private $db;
        private $recordYear;

        function __construct($dbHelper)
        {
            $this->db = $dbHelper;
            //
            // Load Settings from ini file
            // 
            $set = new SettingsIni();

            $this->recordYear = $set->GetValue($set->sect_General, $set->iniKey_RecordYear);
        }

        // Get field names in table
        public static function getFields() : object
        {
            $obj = (object) [];
          
            $obj->id            = "id";
            $obj->itemId        = "item_id";
            $obj->amount        = "amount";
            $obj->reason        = "reason";
            $obj->dateCreated   = "date_created";
            $obj->sku           = "sku"; 
        
            return $obj;
        }
        /**
         * This function will be used to display those rows in table
         */
        public function showAll()
        {
            $i = Item::getFields();
            $c = Category::getFields();
            $u = UnitMeasure::getFields();
            $w = self::getFields(); 

            $year = $this->getRecordYear();

            $categories = TableNames::categories;
            $items = TableNames::inventory;
            $units = TableNames::unit_measures;
            $waste = TableNames::waste;
            $stox  = TableNames::stock;

            $sql = 
            "SELECT 
                w.*,
                i.$i->itemImage     AS 'image',
                i.$i->itemName      AS 'item',
                i.$i->itemCode      AS 'code',
                i.$i->category      AS 'categoryID',
                c.$c->name          AS 'category',
                c.$c->icon          AS 'icon',
                u.$u->measurement   AS 'units',
                w.$w->sku           AS 'sku'

            FROM $waste AS w
            LEFT JOIN $items        AS i ON i.$i->id = w.$w->itemId
            LEFT JOIN $categories   AS c ON c.$c->id = i.$i->category
            LEFT JOIN $units        AS u ON u.$u->id = i.$i->unitMeasure 
            
            WHERE w.$w->dateCreated LIKE '$year%'
            ORDER BY w.$w->dateCreated DESC";
            
            $result = $this->db->fetchAll($sql);

            return $result ?? [];
        }
        /**
         * Dispose of an expired item. Move all of its stocks onto waste
         */
        public function discardExpiredItem($itemId)
        {
            $w = $this->getFields();
            $i = Item::getFields();
            $items = TableNames::inventory;
 
            $wasteFields = implode(",", [ $w->itemId, $w->amount, $w->reason ]);
            $itemFields = implode(",", [ $i->id, $i->remaining ]);
            
            // Move stock to WASTE table
            $sql = "INSERT INTO ". TableNames::waste 
            ." ($wasteFields) SELECT $itemFields ,'Expired' FROM ". TableNames::inventory
            ." WHERE $i->id = $itemId";

            $this->db->query($sql);

            // Reset stock and expiry date
            $sql = "UPDATE $items SET $i->remaining = 0, $i->expiryDate = '' WHERE $i->id = $itemId";

            $this->db->query($sql);
        }
        /**
         * Dispose of an expired item. Move all of its stocks onto waste
         */
        public function moveStockToWaste($itemId, $amount, $reason)
        { 
            $w = $this->getFields();
            $i = Item::getFields();
            $waste = TableNames::waste;
            $items = TableNames::inventory;

            // Move stock to WASTE table
            $sql = "INSERT INTO $waste($w->itemId, $w->amount, $w->reason) VALUES ($itemId, $amount, \"$reason\")";

            $this->db->query($sql);

            // Decrease inventory stock
            $sql = "UPDATE $items SET $i->remaining = ($i->remaining - $amount) WHERE $i->id = $itemId";

            $this->db->query($sql);
        }
  
        public function getRecordYear()
        {
            return $this->recordYear;
        }
    } 
}
  