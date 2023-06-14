<?php
@session_start();

require_once("rootcwd.inc.php");

require_once($rootCwd . "database/configs.php");
require_once($rootCwd . "database/dbhelper.php");

require_once($rootCwd . "includes/Security.php");
require_once($rootCwd . "errors/IError.php");

require_once($rootCwd . "models/Illness.php");

use Models\Illness;

class IllnessPicker
{
    private DbHelper $DB;
    private $fields = null;

    function __construct()
    {
        global $pdo;

        $this->DB       = new DbHelper($pdo);
        $this->fields   = Illness::getFields();
    }

    function getIllnesses()
    {
        try {
            $illnessDataset = $this->DB->get(TableNames::illness, 'ASC', $this->fields->name);
        } catch (\Exception $ex) {
            IError::Throw(500);
            exit;
        } catch (\Throwable $th) {
            IError::Throw(500);
            exit;
        }

        return $illnessDataset;
    }

    function bindDataset()
    {
        $illnessDataset = $this->getIllnesses();
        $security       = new Security();

        foreach ($illnessDataset as $obj) {
            $id     = $security->Encrypt($obj[$this->fields->id]);
            $name   = $obj[$this->fields->name];

            echo
            "<tr>
            <th scope=\"row\">{$name}</th>
            <td class=\"text-end\">
                <button type=\"button\" class=\"btn btn-secondary fw-bold py-1 px-2\" data-mdb-dismiss=\"modal\"
                onclick=\"selectIllness('$id', '$name')\">
                Select
                </button>
            </td>
        </tr>";
        }
    }
}

$illnessPicker = new IllnessPicker();
?>

<div class="modal fade" id="findIllnessModal" tabindex="-1" aria-labelledby="findIllnessModalLabel" aria-hidden="true" data-mdb-backdrop="static">
    <div class="modal-dialog" style="width: 600px; max-width: 600px;">
        <div class="modal-content mt-4">
            <div class="modal-header py-0 ps-4 pe-0">
                <h6 class="modal-title" id="findIllnessModalLabel">Select Illness</h6>
                <button type="button" class="btn shadow-0 fs-5" data-mdb-dismiss="modal">
                    <i class="fas fa-times-circle"></i>
                </button>
            </div>

            <div class="modal-body">
                <div class="illness-picker-toolbar d-flex align-items-center mb-2">
                    <div class="me-auto custom-tooltip">
                        <span class="custom-tooltip-title-left">
                            <div class="d-flex text-start align-items-center">
                                <i class="fas fa-info-circle me-2 text-warning"></i>
                                <span>Results will be shown as you type</span>
                            </div>
                        </span>
                        <div class="input-group" style="max-height: 36px; height: 36px;">
                            <input type="text" class="form-control illness-searchbar" placeholder="Find Illness" maxlength="32" style="max-height: 36px; height: 36px;" />
                            <button class="btn btn-outline-primary display-none btn-clear-search px-3" type="button" id="button-addon2" data-mdb-ripple-color="dark">
                                <i class="fas fa-undo"></i>
                            </button>
                        </div>
                    </div>
                    <div class="d-flex align-items-center justify-content-end">
                        <div class="me-1 d-inline">Show</div>
                        <div class="illness-entries-filter-container"></div>
                        <div class="ms-1 d-inline">entries</div>
                    </div>
                </div>
                <div class="table-wrapper illness-table-wrapper" style="overflow-y: auto; max-height: 400px; height: 400px;">
                    <table class="table table-sm table-striped table-hover illness-picker-table">
                        <thead class="style-secondary position-sticky top-0 z-10">
                            <tr>
                                <th scope="col" class="fw-bold">Illness / Disease</th>
                                <th scope="col" class="fw-bold text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $illnessPicker->bindDataset(); ?>
                        </tbody>
                        <tfoot class="d-none">
                            <tr>
                                <th class="search-col-illness"></th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="modal-footer py-2">
                <button class="btn btn-primary bg-base" type="button" data-mdb-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>