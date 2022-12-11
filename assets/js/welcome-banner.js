$(document).ready(() => 
{ 
    setInterval(function()
    {
        var currentDate = moment().format("dddd, MMMM DD, YYYY, h:mm A");
        $(".banner-date-text").text(currentDate);
    }, 1000);

});