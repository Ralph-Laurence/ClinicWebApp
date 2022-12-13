class SnackBar
{
    snackbarParentClassName = undefined;

    constructor(snackbarParentClass = "snackbar")
    {
        this.snackbarParentClassName = snackbarParentClass; 
    }

    show(message, autoHide = true, timeOut = 2800)
    { 
        $('.' + this.snackbarParentClassName).text(message).fadeTo(400, 1);

        if (autoHide)
        {
            setTimeout(() => {
                this.hide();
            }, timeOut);
        }
    }

    hide()
    {
        $('.' + this.snackbarParentClassName).fadeTo(1000, 0, function() 
        {
            $(this).text('').hide();
        });
    }
}