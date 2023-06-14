$(document).ready(() => onAwake());

function onAwake()
{

}

function viewCheckupDetails(recordKey)
{
    var inputKey = $(".frm-details #record-key").val(recordKey);

    if (System.isNullOrEmpty(inputKey))
    {
        dialog.danger("There was a problem while trying to read the record details. Please reload the page and try again.");
        return;
    }
    
    $(".frm-details").trigger("submit");
}