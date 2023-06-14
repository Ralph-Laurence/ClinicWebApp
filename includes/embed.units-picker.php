<?php

use Models\UnitMeasure;

@session_start();

require_once("rootcwd.inc.php");

require_once($rootCwd . "database/configs.php");
require_once($rootCwd . "database/dbhelper.php"); 
 
require_once($rootCwd . "includes/Security.php");
require_once($rootCwd . "errors/IError.php");

require_once($rootCwd . "models/UnitMeasure.php");
 
class UnitsPicker
{
    private DbHelper $DB;
    private $fields = null;

    function __construct()
    { 
        global $pdo;

        $this->DB       = new DbHelper($pdo);
        $this->fields   = UnitMeasure::getFields(); 
    }

    function getUnits()
    {
        try
        {
            $units = new UnitMeasure($this->DB);

            $unitsDataset = $units->getAll();
        }
        catch (\Exception $ex) { IError::Throw(500); exit; }
        catch (\Throwable $th) { IError::Throw(500); exit; }

        return $unitsDataset;
    }

    function bindDataset()
    { 
        $security = new Security();

        foreach ($this->getUnits() as $obj) 
        {
            $id   = $security->Encrypt($obj[$this->fields->id]);
            $unit = $obj[$this->fields->measurement];
 
            echo <<<TR
            <tr>
                <td class="th-230 text-truncate">$unit</th>
                <td class="text-center">
                    <button type="button" class="btn btn-secondary fw-bold py-1 px-2" data-mdb-dismiss="modal" 
                    onclick="selectUnits('$id', '$unit')">
                    Select
                    </button>
                </td>
            </tr>
            TR;
        }
    }
}

$unitsPicker = new UnitsPicker();
?>

<div class="modal fade" id="findUnitsModal" tabindex="-1" aria-labelledby="findUnitsModalLabel" aria-hidden="true" data-mdb-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content mt-4">
            <div class="modal-header py-0 ps-4 pe-0">
                <h6 class="modal-title" id="findUnitsModalLabel">Select Unit of Measure</h6>
                <button type="button" class="btn shadow-0 fs-5" data-mdb-dismiss="modal">
                    <i class="fas fa-times-circle"></i>
                </button>
            </div>

            <div class="modal-body">
                <div class="table-wrapper" style="overflow-y: auto; max-height: 450px; height: 450px;">
                    <table class="table table-sm table-striped table-hover">
                        <thead class="style-secondary position-sticky top-0 z-10">
                            <tr> 
                                <th scope="col" class="fw-bold th-230">Measurement</th>
                                <th scope="col" class="fw-bold th-75 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $unitsPicker->bindDataset(); ?>
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