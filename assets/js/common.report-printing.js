var spinnerModal = undefined;

$(document).ready(function () 
{ 
    onAwake();
});

function onAwake()
{
    spinnerModal = $(".spinnerModal");
    onBind();
}

function onBind()
{
    $(".fab-print").click(() => 
    {
        $(this).prop("disabled", true); 
        spinnerModal.toggleClass('display-none d-flex');

        $(".print-paper").printThis(
        {
            importCSS: true,
            printContainer: false, 
            afterPrint: function(e) 
            {
                $(this).prop("disabled", false);
                spinnerModal.toggleClass('d-flex display-none');
            }
        });
    });
}