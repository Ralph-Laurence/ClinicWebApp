<?php 
require_once("rootcwd.inc.php"); 
require_once($rootCwd . "includes/Security.php"); 

require_once($rootCwd . "models/Item.php");

use Models\Item;
 
function bindReasons()
{
    $security = new Security();

    foreach (Item::StockoutReasons as $k => $obj) 
    {
        $key = $security->Encrypt($k);

        echo <<<TR
        <tr>
            <td class="th-230">{$obj['reason']}</td>
            <td class="th-75">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-info-circle text-primary" data-mdb-toggle="tooltip" data-mdb-html="true" data-mdb-placement="right" title="<span class='tooltip-title tooltip-title-amber'>{$obj['reason']}</span><br>{$obj['description']}"></i>
                    <button type="button" class="btn btn-secondary py-1 px-2" 
                    onclick="filterReason('$key')">Select</button>
                </div>
            </td>
        </tr>
        TR; 
    }
}
?>

<div class="modal fade" id="findReasonModal" tabindex="-1" aria-labelledby="findReasonModalLabel" aria-hidden="true" data-mdb-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content mt-4">
            <div class="modal-header py-0 ps-4 pe-0">
                <h6 class="modal-title" id="findReasonModalLabel">Select Disposal Reason</h6>
                <button type="button" class="btn shadow-0 fs-5" data-mdb-dismiss="modal">
                    <i class="fas fa-times-circle"></i>
                </button>
            </div>

            <div class="modal-body">
                <table class="table table-sm table-striped table-hover">
                    <thead class="d-none">
                        <tr>
                            <th scope="col" class="fw-bold th-230">Reason</th>
                            <th scope="col" class="fw-bold th-75 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php bindReasons() ?>
                    </tbody>
                </table>
            </div>

            <div class="modal-footer py-2">
                <button class="btn btn-primary bg-base" type="button" data-mdb-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>