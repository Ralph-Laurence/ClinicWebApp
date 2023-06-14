var blueShade0 = "#3F51B5";
var blueShade1 = "#448AFF";
var blueShade2 = "#2B377B";
var cyanShade0 = "#00BCD4";
var cyanShade1 = "#009688";

var colors =
{
    // Purple to Pink
    palette0: { shade1: "#3a0ca3", shade2: "#560bad", shade3: "#7209b7", shade4: "#b5179e", shade5: "#f72585" },

    // Red to Gray
    palette1: { shade1: "#FD413C", shade2: "#FF8600", shade3: "#FEBC2C", shade4: "#5808D9", shade5: "#8F33D1" },
}

$(document).ready(function () {
    
    onAwake();
});

function onAwake()
{
    drawDailyRecords();

    drawWeeklyRecords();

    drawMonthlyRecords();
}

function drawDailyRecords()
{  
    var xValues = [];
    var yValues = [];
    var totalRecords = 0;

    var dailyData = JSON.parse($(".daily-insights").val());

    $.each(dailyData, function(index, item) 
    { 
        xValues.push(item.day);
        yValues.push(item.recordsCount); 
        totalRecords += item.recordsCount;
    }); 

    $(".donut-inner .inner-total-label").text(totalRecords);

    var barColors = 
    [
        colors.palette1.shade1,
        colors.palette1.shade2,
        colors.palette1.shade3,
        colors.palette1.shade4,
        colors.palette1.shade5
    ];

    new Chart("daily-activities", 
    {
        type: "doughnut",
        data: {
            labels: xValues,
            datasets: [{
                backgroundColor: barColors,
                data: yValues
            }]
        },
        options: {
            title: {
                display: true,
                text: "Daily Appointments",
                fontSize: 16,
                fontColor: "#263238"
            },
            cutoutPercentage: 65,
        }
    });
}

function drawWeeklyRecords() 
{
    var chart = new CanvasJS.Chart("areaChartContainer",
    {
        animationEnabled: true,
        title:
        {
            text: "Weekly Appointments",
            fontFamily: "arial",
            fontWeight: "bold",
            fontColor: "#263238",
            fontSize: 16,
            margin: 20,
            horizontalAlign: 2,
        },
        axisY:
        {
            title: "Total Records",
            gridColor: "#DFDFDF",
            lineThickness: 2,
            lineColor: "#C0C0C0"
        },
        axisX:{
            interval: 1,
            lineThickness: 2,
            lineColor: "#C0C0C0"
          },
        data: [{
            type: "area",
            color: "rgba(253,65,60,.7)",
            toolTipContent: "<span style='\"'color: #FF1F3C;'\";'>{toolTipX}</span>: {y} Total Records",
            markerSize: 8, 
            dataPoints: []
        }]
    });

    var weeklyData = JSON.parse($(".weekly-insights").val());
    var weekPrefix = ["1st", "2nd", "3rd", "4th", "5th", "6th", "7th", "8th", "9th"];
    
    $.each(weeklyData, function(index, item) 
    {
        var weekLabel = `${weekPrefix[index]} Week`;
 
        if (index == weeklyData.length - 1)
            weekLabel = "Current Week";

        var daysOfWeek = System.getDaysOfWeek(item.week, 2023);

        chart.options.data[0].dataPoints.push({ 
            x: item.week, 
            y: item.recordsCount, 
            label: weekLabel,
            toolTipX: `${weekLabel} (${moment(daysOfWeek[0]).format("MMM DD")} to ${moment(daysOfWeek[daysOfWeek.length - 1]).format("MMM DD")})`
        });
    }); 

    chart.render();
} 

function drawMonthlyRecords()
{
    var chart = new CanvasJS.Chart("barChartContainer", 
    {
        animationEnabled: true,
        
        title:
        {
            text: "Monthly Appointments",
            fontFamily: "arial",
            fontWeight: "bold",
            fontColor: "#263238",
            fontSize: 16,
            margin: 20,
            horizontalAlign: 2,
        },
        axisX:{
            interval: 1,
        }, 
        axisY2:{
            interlacedColor: "rgba(1,77,101,.2)",
            gridColor: "rgba(1,77,101,.1)",
            title: "Total Records",
        },
        data: [{
            type: "bar",
            name: "companies",
            axisYType: "secondary",
            color: "#014D65",
            dataPoints: [
                { y: 3, label: "Sweden" },
                { y: 7, label: "Taiwan" },
                { y: 5, label: "Russia" },
                { y: 9, label: "Spain" },
                { y: 7, label: "Brazil" },
                { y: 7, label: "India" },
                { y: 9, label: "Italy" },
                { y: 8, label: "Australia" },
                { y: 11, label: "Canada" },
                { y: 15, label: "South Korea" },
                { y: 12, label: "Netherlands" },
                { y: 15, label: "Switzerland" },
                { y: 25, label: "Britain" },
                { y: 28, label: "Germany" },
                { y: 29, label: "France" },
                { y: 52, label: "Japan" },
                { y: 103, label: "China" },
                { y: 134, label: "US" }
            ]
        }]
    });
    chart.render();
}