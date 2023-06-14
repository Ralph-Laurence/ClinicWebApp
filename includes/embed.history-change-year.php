<?php

function prefillYears()
{
    $set = new SettingsIni();

    // Load year from ini file
    $loadYear = $set->GetValue($set->sect_General, $set->iniKey_RecordYear);

    $startYear = 2022;
    $year = $startYear;

    for ($i = 0; $i < 10; $i++) {
        // select current year
        $selected = $year == $loadYear ? "selected" : "";

        echo <<<OPTION
        <option value="y$i" $selected>$year</option>
        OPTION;

        $year++;
    }
}

function getInitiator()
{
    global $security;

    return $security->Encrypt("history");
}
?>

<!-- Modal -->
<div class="modal fade" id="changeYearModal" tabindex="-1" aria-labelledby="changeYearModalLabel" aria-hidden="true" data-mdb-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header d-flex justify-content-start gap-2 align-items-center py-2">
                <img src="assets/images/icons/option-icon-year.png" width="24" height="24" />
                <h6 class="modal-title font-primary-dark" id="changeYearModalLabel">Change Viewing Year</h6>
            </div>
            <div class="modal-body">
                <div class="p-2 rounded-2 bg-document fsz-14">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    Please note that changing the record year will apply globally to <span class="font-primary-dark">History</span> and <span class="font-primary-dark">Waste records.</span> 
                    You can change the record year anytime by going to settings or clicking the <span class="py-1 px-2 font-primary-dark rounded-3 bg-control">Options</span> button 
                    on this page.
                </div>
                <div class="mt-3">
                    <form action="<?= Tasks::CHANGE_REC_YEAR ?>" method="POST" class="frm-change-year">
                        <input type="text" name="new-record-year" class="new-rec-year d-none">
                        <input type="text" name="setting-initiator" class="setting-initiator d-none" value="<?= getInitiator() ?>">
                        <div class="selectmenu-wrapper">
                            <select class="combo-box" id="record-year" name="record-year">
                                <?php prefillYears() ?>
                            </select>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary fw-bold" data-mdb-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>