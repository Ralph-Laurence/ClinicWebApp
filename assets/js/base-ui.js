var sidenavDrawer = undefined; 
var sidenavChevron = undefined;
var sidenavTrigger = undefined; 

$(document).ready(function ()
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
        var currentDate = moment().format("ddd, MMMM DD, YYYY, h:mm A");
        $(".banner-date-text").text(currentDate);
    }, 1000); 

    // Scroll / focus onto the Active navlink item
    setTimeout(() => 
    {
        $(".side-nav .active")[0].scrollIntoView({ behavior: 'smooth', block: 'center' }); 
        $(".accordion").fadeTo(500, 1);
        //new Effects().scrollActiveNavLink();
    }, 350);

    // Track inventory stocks.
    // Then notify the user if ever there were stocks not in good condition
    notifyStockStatus();
}); 

function notifyStockStatus()
{
    if ($(".notifier-href").length < 1)
        return;

    $.ajax({
        url: $(".notifier-href").val(),
        type: "POST",
        success: function(res)
        {
            if (res)
                appendNotifications(res);
        },
        error: function(jqXHR, textStatus, errorThrown)
        {
            // handle error response
        }
    });
}

function appendNotifications(res) 
{   
    if (!res)
    {
        showNoNotifs();
        return;
    }
    
    var totalNotifs = 0;

    for (var key in res)
    { 
        var liMessage = "";

        switch (key)
        {
            case 'totalExpired':

                if (res.totalExpired > 0)
                {
                    $itemLabel = res.totalExpired == 1 ? "item" : "items";

                    liMessage =
                        `<div class="fw-bold text-uppercase font-base fsz-12">Stock Alert:</div>
                        <div>Inventory has <strong>${res.totalExpired} <span class="font-red">EXPIRED</span></strong> ${$itemLabel}</div>`;
                    
                    totalNotifs++;
                }
                else
                {
                    continue;
                }
                break;

            case 'totalCritical':

                if (res.totalCritical > 0)
                {
                    $itemLabel = res.totalCritical == 1 ? "item" : "items";

                    liMessage =
                        `<div class="fw-bold text-uppercase font-base fsz-12">Stock Alert:</div>
                        <div>Inventory has ${res.totalCritical} CRITICAL ${$itemLabel}</div>`;

                    totalNotifs++;
                }
                else
                {
                    continue;
                }
                break; 

            case 'totalSoldout':

                if (res.totalSoldout > 0)
                {
                    $itemLabel = res.totalSoldout == 1 ? 
                    `item needs to <span class="text-danger">RESTOCK</span></div>` : 
                    `items need to <span class="text-danger">RESTOCK</span></div>`;

                    liMessage =
                        `<div class="fw-bold text-uppercase font-base fsz-12">Stock Alert:</div>
                        <div>${res.totalSoldout} ${$itemLabel}`;

                    totalNotifs++;
                }
                else
                {
                    continue;
                }
                break; 
        }
 
        var li = 
        `<li class="px-3 py-2 rounded-0 border-top dropdown-item-custom-light">
            <div class="d-flex flex-row gap-2">
                <div class="dropdown-item-icon text-center">
                    <img src="assets/images/icons/icn-notif-queue.png" width="18" height="18">
                </div>
                <div class="text-start">
                    ${liMessage}
                </div>
            </div>
        </li>`;
        
        $(".notif-queue").append(li);
    }  

    // var  Object.keys(res).length;
    if (totalNotifs < 1)
    {
        showNoNotifs();
        return;
    } 

    $(".notif-badge").text(totalNotifs).show(); 
    $(".sidenav-badge-dot").show();
    $(".notif-header").text(`Notifications (${totalNotifs})`);
}

function showNoNotifs() 
{  
    var li =
        `<li class="px-3 border-top py-2"> 
            <div class="">You have no notifications</div> 
        </li>`;

    $(".notif-queue").append(li);
}