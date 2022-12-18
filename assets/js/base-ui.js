$(document).ready(() => onAwake());

function onAwake()
{
    // Welcome banner calendar | timer
    setInterval(function () 
    {
        var currentDate = moment().format("dddd, MMMM DD, YYYY, h:mm A");
        $(".banner-date-text").text(currentDate);
    }, 1000);


    // Bind events after the page has fully loaded
    onBind();
}

function onBind()
{

}