<?php

namespace Models 
{ 
    class Checkup
    { 
        public $id = 0;
        public $patientForeignKey = 0; 
        public $doctorId = 0;
        public $illnessId =  0;
        public $bpSystolic = 0;
        public $bpDiastolic = 0;
        public $checkupNumber = "";
        public $dateCreated = "";
        public $dateUpdated = "";
        public $upatedByGuid = ""; 

        // Get field names in table
        public static function getFields() : object
        {
            $obj = (object) [];
         
            $obj->id = "id";
            $obj->patientFK     = "patient_fk_id";
            $obj->doctorId      = "doctor_id";
            $obj->illnessId     = "illness_id";
            $obj->bpSystolic    = "bp_systolic";
            $obj->bpDiastolic   = "bp_diastolic";
            $obj->checkupNumber = "checkup_number";
            $obj->dateCreated   = "date_created";
            $obj->dateUpdated   = "date_updated";
            $obj->createdBy     = "created_by";
            $obj->upatedByGuid  = "upated_by_guid";
        
            return $obj;
        }

        // What happens after the checkup record has been created
        public const ON_COMPLETE_STAY   = 1;
        public const ON_COMPLETE_PREVIEW = 2;
    }
}

namespace TableFields 
{
    class CheckupFields
    {
        public static $id = "id";
        public static $patientForeignKey = "patient_fk_id"; 
        public static $illnessId = "illness_id";
        public static $bpSystolic = "bp_systolic";
        public static $bpDiastolic = "bp_diastolic";
        public static $checkupNumber = "checkup_number";
        public static $dateCreated = "date_created";
        public static $dateUpdated = "date_updated";
        public static $upatedByGuid = "upated_by_guid";
    }
}
