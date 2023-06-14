<?php

namespace Models 
{

    use PatientTypes;

    class Patient
    {
        public $id = 0;
        public $patientKey = "";
        public $idNumber = "";
        public $patientType = 0;
        public $firstName = "";
        public $middleName = "";
        public $lastName = "";
        public $birthDay = "";
        public $gender = 0;
        public $age = 0;
        public $address = "";
        public $weight = 0.0;
        public $height = 0.0;
        public $contact = "";
        public $parent = "";
        public $dateCreated = "";
        public $dateUpdated = ""; 
 
        // Get field names in table
        public static function getFields(): object
        {
            $obj = (object) [];

            $obj->id = "id";
            $obj->patientKey = "patient_key";
            $obj->idNumber = "id_number";
            $obj->patientType = "patient_type";
            $obj->firstName = "firstname";
            $obj->middleName = "middlename";
            $obj->lastName = "lastname";
            $obj->birthDay = "birthday";
            $obj->gender = "gender";
            $obj->age = "age";
            $obj->address = "address";
            $obj->weight = "weight";
            $obj->height = "height";
            $obj->contact = "contact";
            $obj->parent = "parent";
            $obj->dateCreated = "date_created";
            $obj->dateUpdated = "date_updated";

            return $obj;
        }

        /**
         * Concatenate the firstname, middlename and lastname to
         * create a fullname.
         * @param $lastnameFirst -> if true, then the lastname goes first.
         * @return string
         */
        public function getFullname(bool $lastnameFirst = false) : string
        { 
            if ($lastnameFirst)
            {
                // add comma if lastname is available
                $lname = !empty($this->lastName) ? "$this->lastName, " : "$this->lastName ";
                $out = $lname . $this->firstName ." ". $this->middleName;

                return trim($out);
            }

            $default = $this->firstName ." ". $this->middleName ." ". $this->lastName;
            return trim($default);
        }

        /**
         * Get the descriptive equivalent of patient type.
         * @return string
         */
        public function describePatient() : string
        {
            if ($this->patientType > 0)
            {
                return PatientTypes::toDescription($this->patientType);
            }

            return "Unknown";
        }

        /**
         * Returns a string interpretation of the patient's height.
         * 
         * @param bool $full -> Should we include Feet (true) or just CM (false)
         */
        public function describeHeight(bool $full = true)
        {
            // If height is not valid, leave it as 0
            if (empty($this->height) || $this->height < 1) {
                return $full ? "0 Cm (0 Ft.)" : "0 Cm";
            }

            // Convert CM to FT => n = n / 30.48
            $ft = $this->height / 30.48;

            // Round to 2 decimals
            $heightFt = ceil($ft * 100) / 100;

            return $full ? "$this->height Cm ($heightFt Ft)" : "0 Cm";
        }
    }
}

namespace TableFields 
{
    class PatientFields
    {
        public static $id = "id";
        public static $patientKey = "patient_key";
        public static $idNumber = "id_number";
        public static $patientType = "patient_type";
        public static $firstName = "firstname";
        public static $middleName = "middlename";
        public static $lastName = "lastname";
        public static $birthDay = "birthday";
        public static $gender = "gender";
        public static $age = "age";
        public static $address = "address";
        public static $weight = "weight";
        public static $height = "height";
        public static $contact = "contact";
        public static $parent = "parent";
        public static $dateCreated = "date_created";
        public static $dateUpdated = "date_updated";
    }
}
