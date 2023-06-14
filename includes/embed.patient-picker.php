<?php
require_once("rootcwd.inc.php");

require_once($rootCwd . "database/configs.php");
require_once($rootCwd . "database/dbhelper.php");

require_once($rootCwd . "includes/Security.php");
require_once($rootCwd . "errors/IError.php");

require_once($rootCwd . "models/Patient.php");

use Models\Patient;

class PatientPicker
{
    private DbHelper $db;
    private $fields;

    function __construct()
    {
        global $pdo;
        $this->db = new DbHelper($pdo);
        $this->fields = Patient::getFields();
    }

    function getPatients()
    {
        $select = implode(",",
            [
                $this->fields->id,
                $this->fields->idNumber,
                $this->fields->patientType,
                $this->fields->firstName,
                $this->fields->middleName,
                $this->fields->lastName,
            ]
        );

        $sql = "SELECT $select FROM " . TableNames::patients . " ORDER BY {$this->fields->lastName} ASC";

        try {
            $dataset = $this->db->fetchAll($sql);
        } catch (\Throwable $ex) {
            IError::Throw(500);
            exit;
        } catch (\Exception $ex) {
            IError::Throw(500);
            exit;
        }

        return $dataset;
    }

    function bindDataset()
    {
        $dataset  = $this->getPatients();
        $security = new Security();

        $p = $this->fields;

        foreach ($dataset as $obj) {
            $patientId    = $security->Encrypt($obj[$p->id]);
            $patientIdNum = $obj[$p->idNumber];
            $patientName  = $obj[$p->lastName] . ", " . $obj[$p->firstName] . " " . $obj[$p->middleName];
            $patientType  = PatientTypes::toDescription($obj[$p->patientType]);

            echo <<<TR
            <tr>
                <td class="text-primary fw-bold th-100 text-truncate">$patientIdNum</td>
                <td class="th-180 text-truncate">$patientName</td>
                <td class="th-75 text-truncate">$patientType</td>
                <td class="text-center th-75">
                    <button type="button" class="btn btn-secondary fw-bold py-1 px-2" data-mdb-dismiss="modal"
                    onclick="selectPatient('$patientId', '$patientIdNum', '$patientName', '$patientType')">
                    Select
                    </button>
                </td>
                <td class="d-none"></td>
            </tr>
            TR; // <td class="d-none">{$i_patientKey}</td>
        }
    }
}

$patientPicker = new PatientPicker();
?>
<div class="modal fade findPatientsModal" id="findPatientsModal" tabindex="-1" aria-labelledby="findPatientsModalLabel" aria-hidden="true" data-mdb-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content mt-0">
            <div class="modal-header py-0 ps-4 pe-0">
                <h6 class="modal-title fw-bold" id="findPatientsModalLabel">
                    <i class="fas fa-search me-1"></i>
                    Find Patient
                </h6>
                <button type="button" class="btn shadow-0 fs-5" data-mdb-dismiss="modal">
                    <i class="fas fa-times-circle"></i>
                </button>
            </div>

            <div class="modal-body">
                <div class="patient-picker-toolbar d-flex align-items-center mb-2">
                    <div class="me-auto custom-tooltip">
                        <span class="custom-tooltip-title-left">
                            <div class="d-flex text-start align-items-center">
                                <i class="fas fa-info-circle me-2 text-warning"></i>
                                <span>Results will be shown as you type</span>
                            </div>
                        </span>
                        <div class="input-group" style="max-height: 36px; height: 36px;">
                            <input type="text" class="form-control searchbar" placeholder="Find Patient" maxlength="32" style="max-height: 36px; height: 36px;" />    
                            <select id="patient-search-filter" class="combo-box">
                                <option value="0">By Name</option>
                                <option value="1">By ID No.</option>
                            </select>
                            <button class="btn btn-outline-primary btn-clear-search px-3 display-none" type="button" id="button-addon2" data-mdb-ripple-color="dark">
                                <i class="fas fa-undo"></i>
                            </button>
                        </div>
                    </div>
                    <div class="d-flex align-items-center justify-content-end">
                        <div class="me-1 d-inline">Show</div>
                        <div class="entries-filter-container"></div>
                        <div class="ms-1 d-inline">entries</div>
                    </div>
                </div>
                <div class="table-wrapperx patient-picker-table-wrapper" style="overflow-y: auto; max-height: 400px; height: 400px;">

                    <table class="table table-sm table-striped table-hover patient-picker-table">
                        <thead class="style-secondary position-sticky top-0 z-10">
                            <tr>
                                <th class="fw-bold th-100" scope="col">ID Number</th>
                                <th class="fw-bold th-180" scope="col">Patient Name</th>
                                <th class="fw-bold th-75" scope="col">Patient Type</th>
                                <th class="fw-bold th-75 text-center" scope="col">Action</th>
                                <th class="d-none history-data"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $patientPicker->bindDataset() ?>
                        </tbody>
                        <tfoot class="d-none">
                            <tr>
                                <th class="search-col-patient-id"></th>
                                <th class="search-col-patient-name"></th>
                                <th></th>
                                <th></th>
                                <th class="d-none"></th>
                            </tr>
                        </tfoot>
                    </table>

                </div>
            </div>

            <div class="modal-footer  py-2">
                <button class="btn btn-primary bg-base" type="button" data-mdb-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>