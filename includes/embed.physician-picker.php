<?php
@session_start();

require_once("rootcwd.inc.php");

require_once($rootCwd . "database/configs.php");
require_once($rootCwd . "database/dbhelper.php"); 
require_once($rootCwd . "includes/Security.php");

require_once($rootCwd . "models/Degrees.php"); 
require_once($rootCwd . "models/Doctor.php"); 
require_once($rootCwd . "models/DoctorSpecialty.php");

use Models\Degrees;
use Models\Doctor;
use Models\DoctorSpecialty;

class PhysicianPicker
{ 
    private DbHelper $DB;
    private $d = null;
    private $s = null;

    function __construct()
    { 
        global $pdo;

        $this->DB = new DbHelper($pdo);
        $this->d = Doctor::getFields();
        $this->s = DoctorSpecialty::getFields();
    }

    function getPhysicians()
    {  
        try 
        {
            $d   = $this->d;
            $doc = new Doctor($this->DB);
            $dataset = $doc->getAll("ASC", $d->firstName);
        } 
        catch (\Exception $ex) { IError::Throw(500); exit; } 
        catch (\Throwable $ex) { IError::Throw(500); exit; }

        return $dataset;
    }

    function bindDataset()
    {  
        $d = $this->d;
        $s = $this->s;
    
        $security = new Security();
        $dataset = $this->getPhysicians();
        $g = Degrees::getFields();

        foreach($dataset as $obj)
        {
            $docId      = $security->Encrypt($obj[$d->id]);
            
            $degree   = $obj[$g->degree];
            $docTitle = 
            [
                $obj[$d->firstName], 
                $obj[$d->middleName], 
                $obj[$d->lastName]
            ];
             
            if (!empty($degree) && IString::startsWith(strtolower($obj[$g->degree]), "dr"))
                array_unshift($docTitle, $degree);
            else 
                array_push($docTitle, ", $degree");
         
            $doctorName = implode(" ", $docTitle);
    
            $actionButton = 
            "<button type=\"button\" class=\"btn btn-secondary fw-bold py-1 px-2 btn-select-doctor\" data-mdb-dismiss=\"modal\">
                Select
            </button>";
    
            echo <<<TR
            <tr>
                <td class="text-truncate th-230 td-doctor-name">$doctorName</td>
                <td class="text-truncate th-230">{$obj[$s->spec]}</td>
                <td class="th-150 text-center">$actionButton</td>
                <td class="d-none td-doctor-key">$docId</td>
            </tr>
            TR;
        }
    }
}
 
$physicianPicker = new PhysicianPicker();
  
?>
<div class="modal fade" id="findDoctorModal" tabindex="-1" aria-labelledby="findDoctorModalLabel" aria-hidden="true" data-mdb-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content mt-4">
            <div class="modal-header py-0 ps-4 pe-0">
                <h6 class="modal-title" id="findDoctorModalLabel">Select Physician</h6>
                <button type="button" class="btn shadow-0 fs-5" data-mdb-dismiss="modal">
                    <i class="fas fa-times-circle"></i>
                </button>
            </div>

            <div class="modal-body">
                <div class="table-wrapper" style="overflow-y: auto; max-height: 450px; height: 450px;">
                    <table class="table table-sm table-striped table-hover">
                        <thead class="style-secondary position-sticky top-0 z-10">
                            <tr>
                                <th scope="col" class="fw-bold th-230">Physician</th>
                                <th scope="col" class="fw-bold th-230">Specialization</th>
                                <th scope="col" class="fw-bold th-150 text-center">Action</th>
                                <th scope="col" class="d-none"></th><?php //PHYSICIAN ID ?>
                            </tr>
                        </thead>
                        <tbody class="doctor-picker-body">
                            <?php $physicianPicker->bindDataset(); ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="modal-footer py-2">
                <button class="btn btn-primary bg-base" type="button" data-mdb-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>