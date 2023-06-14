class Toast
{
    toastParentClassName = undefined;

    toastTypes = 
    {
        INFO:       0,  // Blue
        SUCCESS:    1,  // Green
        WARN:       2,  // Yellow
        DANGER:     3   // Red
    }; 

    constructor(toastParentClass = "itoast")
    {
        this.toastParentClassName = toastParentClass; 

        $('.itoast-btn-close').click(() => this.hide());
    }

    show(message, title = "Information", type = 0, autoHide = true, timeOut = 2800)
    {  
        var color = "";

        // Identify which color to use
        switch(type)
        {
            case this.toastTypes.INFO:
                color = "itoast-primary";
                break;
            case this.toastTypes.SUCCESS:
                color = "itoast-success";
                break;
            case this.toastTypes.WARN:
                color = "itoast-warn";
                break;
            case this.toastTypes.DANGER:
                color = "itoast-danger";
                break;
        }

        $('.' + this.toastParentClassName)
        .removeClass("itoast-primary itoast-success itoast-warn itoast-danger")
        .addClass(color);

        // Prepare toast message and timestamp
        var timeStamp = moment().format("h:mm A");
        $(".itoast-timestamp").text(timeStamp);
        $('.itoast-body').text(message); 
        $('.itoast-title').text(title); 

        // Show the toast
        $('.' + this.toastParentClassName).fadeTo(400, 1);

        if (autoHide)
        {
            setTimeout(() => {
                this.hide();
            }, timeOut);
        }
    }

    hide()
    {
        $('.' + this.toastParentClassName).fadeTo(400, 0, function() 
        {
            $('.itoast-body').text(''); 
            $(this).hide();
        });
    }
}