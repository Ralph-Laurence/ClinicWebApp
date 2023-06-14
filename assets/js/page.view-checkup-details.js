var carousel = undefined;
var cloneFailed = false;
var printBtn = undefined;
var dialog = undefined;
var toast = undefined;

$(document).ready(function(){
    onAwake();
});
//
// All initialization code is contained here ..
// which is called by $(document).ready event above
//
function onAwake()
{ 
    dialog = new AlertDialog();
    toast = new Toast();
    carousel = new mdb.Carousel($("#checkup-details-carousel"));
    printBtn = $(".btn-print");

    onBind();

    var clonePrintable = new Promise((resolve, reject) => 
    {
        var cloneDiv = $(".details-view").clone();

        if (cloneDiv) {
 
            cloneDiv.addClass("px-3").appendTo(".paper-view");

            resolve();
        }
        else {
            reject();
        }
    }); 

    clonePrintable.then(
        
        // Success
        function() {
            printBtn.prop("disabled", false);
        },

        // Fail
        function() {
            cloneFailed = true;
        }
    );

    // Notify
    var success = $(".checkup-success-message").val();

    if (success)
    {
        toast.show(success, 'Recent Appointment', toast.toastTypes.SUCCESS);
    }

}
//
// After initialization, we can now bind (attach) events
// onto elements .. Again, for readability, we put all 
// logic / code of event bindings in onBind() function
//
function onBind()
{
    $(".btn-details").click(function()
    {
        cycleFrameAt(0, $(this));
    });

    $(".btn-export").click(function()
    {
        cycleFrameAt(1, $(this));
    });

    printBtn.click(function()
    {
        if (cloneFailed)
        {
            dialog.danger("This action can't be completed because of an error. Please reload the page and try again.");
            return;
        }
 
        // Show the progress bar
        $(".progress-loader-wrapper").show();

        // Disable the print buttion (this), then 
        // begin the print process
        $(this).prop("disabled", true);

        beginPrint();
    }); 
} 

function cycleFrameAt(frame, sender)
{
    carousel.to(frame);
    $(".checkup-details-btn-wrapper button").removeClass("active");
    sender.addClass("active");
}

function beginPrint()
{
    $(".paper-view").printThis(
    {
        importCSS: true,
        printContainer: false,
        //pageTitle: `Medical-Report-${moment().format("MMM-DD-YYYY-h:mm-A")}`,
        afterPrint: function(e) {
            onAfterPrint();
        }
    });
}

function onAfterPrint()
{
    // Hide the progress bar then 
    // enable the print button
    $(".progress-loader-wrapper").hide();
    printBtn.prop("disabled", false);
}