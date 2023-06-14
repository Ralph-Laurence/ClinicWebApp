<?php

namespace Models 
{

    require_once("rootcwd.inc.php");
    require_once($rootCwd . "models/Item.php");
    require_once($rootCwd . "models/Category.php");
    require_once($rootCwd . "models/UnitMeasure.php");
    require_once($rootCwd . "models/Supplier.php");

    use TableNames;
    use Utils;

    class Item
    {
        public $id = 0;
        public $itemName = "";
        public $category = 0;
        public $code = "";
        public $unitMeasure = 0;
        public $supplierId = 0;
        public $remaining = 0;
        public $criticalLevel = 0;
        public $dateAdded = "";
        public $remarks = "";
        public $dateUpdated = "";

        public $descriptiveUnitMeasure = "";
        public $descriptiveCategory = "";

        private $db;

        public const StockoutReasons = 
        [
            '1' => [ "reason" => "Overstocking",        "description" => "Having more stock than is necessary."],
            '2' => [ "reason" => "Human Error",         "description" => "Inventory discrepancies that may arise such as recording mistakes, disorganization, etc."],
            '3' => [ "reason" => "Expired / Defective", "description" => "Damaged items or stocks that have reached the end of their lifecycle which are no longer safe to use."],
            '4' => [ "reason" => "Inadequate Control",  "description" => "Occurs when you fail to keep track of your inventory and replenish it on time." ]
        ];

        function __construct($dbHelper)
        {
            $this->db = $dbHelper;
        }

        // Get field names in table
        public static function getFields() : object
        {
            $obj = (object) [];
          
            $obj->id            = "id";
            $obj->itemName      = "item_name";
            $obj->category      = "item_category";
            $obj->itemCode      = "item_code";
            $obj->itemImage     = "image";
            $obj->unitMeasure   = "unit_measure";
            $obj->supplierId    = "supplier_id";
            $obj->remaining     = "remaining";
            $obj->criticalLevel = "critical_level";
            $obj->dateAdded     = "date_added";
            $obj->expiryDate    = "expiryDate";
            $obj->remarks       = "remarks";
            $obj->dateUpdated   = "date_updated";
        
            return $obj;
        }

        /**
         * This function will be used to display those rows in table
         */
        public function showAll()
        {
            $i = $this->getFields();
            $c = Category::getFields();
            $u = UnitMeasure::getFields();

            $categories = TableNames::categories;
            $inventory = TableNames::inventory;
            $units = TableNames::unit_measures;

            $select = Utils::prefixJoin("i.", 
            [
                $i->id,
                $i->remaining,
                $i->criticalLevel,
                $i->itemName,
                $i->itemCode,
                $i->itemImage,
                $i->expiryDate,
                "$i->category AS categoryID"
            ]);

            $sql = 
            "SELECT $select, c.$c->name AS 'category', c.$c->icon AS 'icon', u.$u->measurement AS 'units'
            FROM $inventory AS i
            LEFT JOIN $categories   AS c ON c.$c->id = i.$i->category
            LEFT JOIN $units        AS u ON u.$u->id = i.$i->unitMeasure
            ORDER BY i.$i->itemName ASC";

            $result = $this->db->fetchAll($sql);

            return $result ?? [];
        }

        public function find($id)
        {
            $i = self::getFields();
            $u = UnitMeasure::getFields();
            $c = Category::getFields();
            $s = Supplier::getFields();
 
            $sql =
            "SELECT             
            i.$i->id            AS 'itemId',
            i.$i->itemName      AS 'item',
            i.$i->category      AS 'categoryId',
            i.$i->itemCode      AS 'sku',
            i.$i->unitMeasure   AS 'unitsId',
            i.$i->supplierId    AS 'suppId',
            i.$i->remaining     AS 'stock',
            i.$i->criticalLevel AS 'reserve',
            i.$i->remarks       AS 'remarks',
            i.$i->expiryDate    AS 'expiryDate',
            i.$i->itemImage    AS 'image',
            
            u.$u->measurement, s.$s->name AS 'suppName', c.$c->name AS 'category', c.$c->icon       
        
            FROM ".      TableNames::inventory      ." AS i
            LEFT JOIN ". TableNames::categories     ." AS c ON c.$c->id = i.$i->category
            LEFT JOIN ". TableNames::unit_measures  ." AS u ON u.$u->id = i.$i->unitMeasure
            LEFT JOIN ". TableNames::suppliers      ." AS s ON s.$s->id = i.$i->supplierId
            
            WHERE i.$i->id = $id
            LIMIT 1";

            $result = $this->db->fetchAll($sql, true);

            return $result;
        }

        /**
         * Update or remove the expiration date of an item
         */
        public function resetExpiry($itemId, $newDate = "")
        { 
            $fields = $this->getFields();
 
            $this->db->update(TableNames::inventory, [ $fields->expiryDate => !empty($newDate) ? $newDate : '' ],
            [
                $fields->id => $itemId
            ]);
        }

         /**
         * Update or remove the expiration date of items
         */
        public function resetExpiryDates($itemIds = [], $newDate = "")
        { 
            $fields = $this->getFields();
            
            $expiry = !empty($newDate) ? $newDate : '';
            $ids = implode(",", $itemIds);

            $sql = "UPDATE ". TableNames::inventory ." SET $fields->expiryDate = '$expiry' WHERE $fields->id IN ($ids)";
            
            $this->db->query($sql);
        }

        public static function getStockoutReason($reasonId)
        {
            if (array_key_exists($reasonId, self::StockoutReasons))
            {
                return self::StockoutReasons[$reasonId]['reason'];
            }

            return "Other";
        }
    } 
}
  

namespace TableFields 
{
    class ItemFields
    {
        public static $id = "id";
        public static $itemName = "item_name";
        public static $category = "item_category";
        public static $itemCode = "item_code";
        public static $unitMeasure = "unit_measure";
        public static $supplierId = "supplier_id";
        public static $remaining = "remaining";
        public static $criticalLevel = "critical_level";
        public static $dateAdded = "date_added";
        public static $remarks = "remarks";
        public static $dateUpdated = "date_updated";
    }
}
