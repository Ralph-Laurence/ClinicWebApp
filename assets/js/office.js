
var carousel = null;

$(document).ready(function(){
    onAwake();
});

function onAwake()
{
    const carouselParent = document.querySelector('#officeCarousel');
    carousel = new mdb.Carousel(carouselParent);

    onBind();
}

function onBind()
{
    $(".btn-file").click(function() 
    {
        scrollCarousel(0, $(this))
    }); 

    $(".btn-view").click(function() 
    {
        scrollCarousel(1, $(this))
    }); 

    $(".btn-import").click(function() 
    {
        scrollCarousel(2, $(this))
    }); 

    $(document).on("click", ".dataset-table tbody tr td", function()
    {
        $(".dataset-table thead th").removeClass("th-active");
        
        var td = this.cellIndex;
        $(".dataset-table thead").find(`th:eq(${td})`).addClass("th-active");
    });
}

function scrollCarousel(frame, sender)
{
    carousel.to(frame);
    $(".menubar-button").removeClass("active");

    $(sender).addClass("active");
}