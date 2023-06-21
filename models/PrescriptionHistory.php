<?php

namespace Models 
{

    use TableNames;

    require_once("rootcwd.inc.php");

    class PrescriptionHistory
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
            $obj->checkupFK     = "checkup_fk_id";
            $obj->stockId       = "stock_id";
            $obj->quantityUsed  = "quantity_used";    
            $obj->dateCreated   = "date_created";

            return $obj;
        }      

        // Add history record
        public function push($data)
        {
            $table = TableNames::prescription_history;
            $fields = $this->getFields();

            $sql = "INSERT INTO $table($fields->checkupFK, $fields->stockId, $fields->quantityUsed) VALUES(?,?,?)";
            $stmt = $this->db->getInstance()->prepare($sql);

            foreach ($data as $row)
            {
                $checkupId      = $row['checkupId'];    // Id of record in checkup_details table 
                $stockId        = $row['stockId'];      // Id of record in stocks table
                $qty            = $row['used'];         // The amount that was subtracted from stock

                $stmt->execute([$checkupId, $stockId, $qty]);
            }
        }

        /**
         * Return the quantity back to stocks table
         */
        public function returnStock()
        {

        }
    }
}