$(document).ready(() => 
{
    onAwake();
});

function onAwake()
{ 
    $(function () 
    {
        $("#find-patient-option").selectmenu({
            width: 164
        });
        $("#month-options").selectmenu({
            width: 180
        });
    });

    appendMonthNames();

    loadCheckupRecord();

    onBind();
}

function onBind()
{

}

function appendMonthNames()
{
    $("#month-options")
    .empty()
    .append(`<option selected disabled value=''>Select Month</option>`);

    var months = moment.months();

    months.forEach(m => {
        $("#month-options").append(`<option>${m}</option>`);
    });

    $(function () 
    {
        $("#month-options").selectmenu("refresh");
    }); 
}

function loadCheckupRecord(filter = 'all')
{
    $.ajax({
        url: "ajax.get-checkup-records.php",
        type: "POST",
        dataType: "json",
        data: {filter: filter},
        success: function(res)
        {
            if (res)
            {
                $(".checkup-dataset").empty();

                res.dataSet.forEach(d => 
                {
                    var checkupDate = moment(`${d.checkup_date} ${d.checkup_time}`).format('MMM DD, YYYY h:mm A')

                    $(".checkup-dataset").
                    append(`
                    <tr>
                        <td>${d.form_number}</td>
                        <td>${d.patient_name}</td>
                        <td>${d.patient_type}</td>
                        <td>${d.illness}</td>
                        <td>${checkupDate}</td>
                        <td>action</td>
                    </tr>
                    `);
                });
            }
        },
        error: function(jqXHR, exception)
        {

        }
    });
}