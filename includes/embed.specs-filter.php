<?php
@session_start();

require_once("rootcwd.inc.php");

require_once($rootCwd . "database/configs.php");
require_once($rootCwd . "database/dbhelper.php"); 
require_once($rootCwd . "includes/Security.php");
 
require_once($rootCwd . "models/DoctorSpecialty.php");
 
use Models\DoctorSpecialty;

class SpecsPicker
{ 
    private DbHelper $db;  

    function __construct()
    { 
        global $pdo;

        $this->db = new DbHelper($pdo);  
    }

    function getSpecs()
    {  
        try 
        { 
            $specs = new DoctorSpecialty($this->db);
            $dataset = $specs->getAll(); 
        } 
        catch (\Exception $ex) { IError::Throw(500); exit; } 
        catch (\Throwable $ex) { IError::Throw(500); exit; }

        return $dataset;
    }

    function bindDataset()
    {   
        $security = new Security();
        $dataset = $this->getSpecs();

        foreach($dataset as $k => $v)
        {
            $id = $security->Encrypt( $k ); 

            echo <<<TR
            <tr> 
                <td class="text-truncate th-230">$v</td>
                <td class="th-150 text-center">
                    <button type="button" class="btn btn-secondary fw-bold py-1 px-2 btn-select-specfilter" data-mdb-dismiss="modal">
                    Select
                    </button>
                </td>
                <td class="d-none">
                    <input type="text" class="specs-filter-key" value="$id" />
                </td>
            </tr>
            TR;
        }
    }
}
 
$specsFilter = new SpecsPicker();
  
?>
<div class="modal fade" id="findSpecsModal" tabindex="-1" aria-labelledby="findSpecsModalLabel" aria-hidden="true" data-mdb-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content mt-4">
            <div class="modal-header py-0 ps-4 pe-0">
                <h6 class="modal-title" id="findSpecsModalLabel">Filter doctors by Specialization</h6>
                <button type="button" class="btn shadow-0 fs-5" data-mdb-dismiss="modal">
                    <i class="fas fa-times-circle"></i>
                </button>
            </div>

            <div class="modal-body">
                <div class="table-wrapper" style="overflow-y: auto; max-height: 450px; height: 450px;">
                    <table class="table table-sm table-striped table-hover">
                        <thead class="style-secondary position-sticky top-0 z-10">
                            <tr>
                                <th scope="col" class="fw-bold th-230">Specialization</th>
                                <th scope="col" class="fw-bold th-150 text-center">Action</th>
                                <th scope="col" class="d-none"></th><?php //Encrypted ID ?>
                            </tr>
                        </thead>
                        <tbody class="specs-filter-body">
                            <?php $specsFilter->bindDataset(); ?>
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