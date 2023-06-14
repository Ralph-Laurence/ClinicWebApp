var dialog = undefined;

$(document).ready(function(){
    onAwake();
});

function onAwake()
{
    dialog = new AlertDialog();
    onBind();
}

function onBind()
{
    $(".btn-edit").click(() => editDoctor());

    $(document).on("click", ".dataset-body .btn-doctor-details", function()
    {
        var tr = $(this).closest("tr");
        var patientKey = tr.find('.patientKey').val().trim();

        if (System.isNullOrEmpty(patientKey))
        {
            dialog.danger("This action can't be completed. Please reload the page and try again.");
            return;
        }

        $(".frm-details #details-key").val(patientKey);
        $(".frm-details").trigger("submit");
    });
}

function editDoctor()
{
    var key = $(".frm-edit #record-key").val();

    if (System.isNullOrEmpty(key))
    {
        dialog.danger("This action can't be completed. Please reload the page and try again.");
        return;
    }

    $(".frm-edit").trigger("submit");
}