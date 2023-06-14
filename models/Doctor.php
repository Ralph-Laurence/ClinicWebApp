<?php

namespace Models 
{

    use DbHelper;
    use IString;
    use TableNames;
    use Utils;
    use SettingsIni;

    require_once("rootcwd.inc.php");

    require_once($rootCwd . "includes/system.php");

    require_once($rootCwd . "models/Checkup.php");
    require_once($rootCwd . "models/Degrees.php");
    require_once($rootCwd . "models/Patient.php");
    require_once($rootCwd . "models/Illness.php");
    require_once($rootCwd . "models/DoctorSpecialty.php");
    require_once($rootCwd . "models/SettingsIni.php");
 
    class Doctor
    { 
        private DbHelper $db;
        private $recordYear;

        public function __construct($db = null)
        {
            $this->db = $db;
            //
            // Load Settings from ini file
            // 
            $set = new SettingsIni();

            $this->recordYear = $set->GetValue($set->sect_General, $set->iniKey_RecordYear); 
        }
        /**
         * Get (SELECT) all doctors from database.
         * This will also retrieve and join all 
         * specializations for each doctors
         */
        public function getAll(string $order = "", string $by = "", bool $countPatients = false)
        {  
            $c = Checkup::getFields();
            $s = DoctorSpecialty::getFields();
            $d = self::getFields();
            $g = Degrees::getFields(); 

            $d_fields = Utils::prefixJoin("d.", [ $d->id, $d->regNum, $d->firstName, $d->middleName, $d->lastName, $d->contact, $d->address ]);

            $sort = "";

            if (!empty($order) && !empty($by))
                $sort = "ORDER BY $by $order";

            $totalPatients = "";

            // Count all patients of each doctor.
            // The counting of patients are specific to checkup year.
            //  
            if ($countPatients)
            {
                $totalPatients = ",(SELECT COUNT(DISTINCT $c->patientFK) FROM " .TableNames::checkup_details.
                " c WHERE c.$c->doctorId = d.$d->id AND c.$c->dateCreated LIKE '$this->recordYear%') AS total_patients";

                // SELECT COUNT(DISTINCT patient_fk_id) FROM checkup_details WHERE doctor_id = 18 AND date_created LIKE '2023%' 
            }

            $sql = 
            "SELECT $d_fields, s.$s->spec $totalPatients, g.$g->degree
            FROM ". TableNames::doctors ." AS d 
            LEFT JOIN ". TableNames::doctor_specialties ." s ON s.$s->id = d.$d->spec
            LEFT JOIN ". TableNames::doctor_degrees ." g ON g.$g->id = d.$d->degree 
            $sort ";

            $result = $this->db->fetchAll($sql);
            return $result;
        }
        
        /**
         * This function will be used to get Doctor Details
         */
        public function find(int $findId, string $order = "", string $by = "", bool $countPatients = false)
        {  
            if (empty($findId))
                return [];

            $c = Checkup::getFields();
            $s = DoctorSpecialty::getFields();
            $d = self::getFields();
            $g = Degrees::getFields(); 

            $d_fields = Utils::prefixJoin("d.", 
            [ 
                $d->id,       $d->regNum,  $d->firstName, $d->middleName, 
                $d->lastName, $d->contact, $d->address,   $d->dateCreated
            ]);

            $sort = "";

            if (!empty($order) && !empty($by))
                $sort = "ORDER BY $by $order";

            $totalPatients = "";

            // Count all patients of each doctor.
            // The counting of patients are specific to checkup year.
            //  
            if ($countPatients)
            {
                $totalPatients = ",(SELECT COUNT(DISTINCT $c->patientFK) FROM " .TableNames::checkup_details.
                " c WHERE c.$c->doctorId = d.$d->id AND c.$c->dateCreated LIKE '$this->recordYear%') AS total_patients";
            }

            $sql = 
            "SELECT $d_fields, s.$s->spec $totalPatients, g.$g->degree
            FROM ". TableNames::doctors ." AS d 
            LEFT JOIN ". TableNames::doctor_specialties ." s ON s.$s->id = d.$d->spec
            LEFT JOIN ". TableNames::doctor_degrees     ." g ON g.$g->id = d.$d->degree  
            WHERE d.$d->id = $findId $sort";

            $result = $this->db->fetchAll($sql, true);
            return $result;
        }

        public function getSpecializations()
        { 
            $d = self::getFields(); 

            $result = $this->db->get(TableNames::doctor_specialties, 'ASC', $d->spec);
            return $result;
        }

        public function getDegrees()
        { 
            $d = Degrees::getFields(); 

            $result = $this->db->get(TableNames::doctor_degrees, 'ASC', $d->degree);
            return $result;
        }   
         
        public function getPatients($doctorId)
        {
            $p = Patient::getFields();
            $i = Illness::getFields();
            $c = Checkup::getFields();
  
            $sql =
            "SELECT 

                p.$p->id AS patientId,
                CONCAT (p.$p->lastName,', ', p.$p->firstName,' ', p.$p->middleName) AS patientName,
                p.$p->patientType AS patientType,
                p.$p->idNumber as idNum,
                i.$i->name AS illness

            FROM ". TableNames::checkup_details     ." AS c 
                LEFT JOIN ". TableNames::patients   ." AS p ON p.$p->id = c.$c->patientFK
                LEFT JOIN ". TableNames::illness    ." AS i ON i.$i->id = c.$c->illnessId

            WHERE c.$c->doctorId = $doctorId AND c.$c->dateCreated LIKE '$this->recordYear%' ".
            //GROUP BY p.$p->id 
            "ORDER BY c.$c->dateCreated DESC";

            $result = $this->db->fetchAll($sql);
            return $result;
        }

        /**
         * Format the doctor name to include 
         * SPECIALIZATION and / or DEGREE
         */
        public function getTitle($doctorId, $emptyOnFail = false)
        { 
            $d = self::getFields();
            $g = Degrees::getFields();
            $s = DoctorSpecialty::getFields();

            $select = implode(",", [$d->firstName, $d->middleName, $d->lastName]);

            $sql = "SELECT $select, g.$g->degree, s.$s->spec
            FROM ". TableNames::doctors ." AS d
            LEFT JOIN ". TableNames::doctor_degrees     ." AS g ON g.$g->id = d.$d->degree 
            LEFT JOIN ". TableNames::doctor_specialties ." AS s ON s.$s->id = d.$d->spec 
            WHERE d.$d->id = $doctorId"; 

            $dataset = $this->db->fetchAll($sql, true);
              
            if (empty($dataset))
                return $emptyOnFail ? "" : "Unknown Doctor";

            $degree   = $dataset[$g->degree];
            $docTitle = 
            [
                $dataset[$d->firstName], 
                $dataset[$d->middleName], 
                $dataset[$d->lastName]
            ];
             
            if (!empty($degree) && IString::startsWith(strtolower($dataset[$g->degree]), "dr"))
                array_unshift($docTitle, $degree);
            else 
                array_push($docTitle, ", $degree");
         
            return implode(" ", $docTitle);
        }

        // Get field names in table
        public static function getFields() : object
        {
            $obj = (object) [];
         
            $obj->id           = "id";
            $obj->firstName    = "firstname";
            $obj->middleName   = "middlename";
            $obj->lastName     = "lastname";
            $obj->spec         = "specialization";
            $obj->contact      = "contact";
            $obj->regNum       = "reg_num";
            $obj->address      = "address";
            $obj->degree       = "degree";
            $obj->dateCreated  = "date_created";
        
            return $obj;
        }
    }
}

