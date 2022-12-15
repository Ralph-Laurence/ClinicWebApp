var inputKeyword = undefined; 
var btnFind = undefined;
var dialog = undefined;

$(document).ready(() => 
{
    onAwake();
});

function onAwake()
{ 
    inputKeyword = $("#input-keyword"); 
    btnFind = $(".btn-find");
    dialog = new AlertDialog();

    $(function () 
    {
        $("#find-patient-option").selectmenu({
            width: 200,
            change: function(event, ui)
            {
                var selected = $(this).val();

                if (selected == "filter-month")
                {
                    $( "#month-options" ).selectmenu( "option", "disabled", false );
                    inputKeyword.prop("disabled", true); 
                }
                else 
                {
                    $( "#month-options" ).selectmenu( "option", "disabled", true );
                    inputKeyword.prop("disabled", false); 
                } 
            }
        });

        $("#month-options").selectmenu({
            width: 180
        });
    }); 

    onBind();
}

function onBind()
{
    btnFind.click(() => searchRecord());
} 

function searchRecord()
{
    var filter = $("#find-patient-option").val();

    if (filter != "filter-month" && inputKeyword.val() == "") 
    {
        dialog.warn("Please enter a search term.");
        return;
    }

    if (filter == "filter-month" && $("#month-options").val() == null)
    {
        dialog.warn("Please select a month.");
        return;
    }

    $(".filter-form").trigger("submit");
}
 