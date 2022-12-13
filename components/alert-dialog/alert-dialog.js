class AlertDialog
{
    dialogModes = 
    {
        information: 1,
        warning: 2,
        error: 3
    }

    modalParentClassName = undefined;
    mdbModalObj = undefined;

    constructor(modalParentClass = "alertDialogModal")
    {
        this.modalParentClassName = modalParentClass;

        this.mdbModalObj = new mdb.Modal($("." + this.modalParentClassName), {backdrop: 'static'}); 
    }

    show(message, title = "Alert", mode = 1)
    {
        // skip if modal parent is undefined or null
        if (this.modalParentClassName == undefined || this.modalParentClassName == null)
            return;

        // set the title
        $("." + this.modalParentClassName + " #alertDialogModalLabel").text(title);

        // set the message
        $("." + this.modalParentClassName + " .alertDialogModalBody").text(message);

        this.mdbModalObj.show();
    }
}