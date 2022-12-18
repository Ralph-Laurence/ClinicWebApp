var sidenavDrawer = undefined; 
var sidenavChevron = undefined;
var sidenavTrigger = undefined; 

$(document).ready(() => 
{ 
    sidenavDrawer = $(".side-nav"); 
    sidenavChevron = $(".btn-hide-sidenav");
    sidenavTrigger = $(".btn-show-sidenav");

    sidenavChevron.on("click", () => 
    {
        sidenavDrawer.hide("fast", () =>
        { 
            sidenavTrigger.show();
        });
    }); 

    sidenavTrigger.on("click", () => 
    {
        sidenavTrigger.hide();
        sidenavDrawer.show("fast");
    });  

    setInterval(function () 
    {
        var currentDate = moment().format("dddd, MMMM DD, YYYY, h:mm A");
        $(".banner-date-text").text(currentDate);
    }, 1000); 
}); 