class AlertDialog
{
    modalParentClassName = undefined;
    mdbModalObj = undefined;

    constructor(modalParentClass = "alertDialogModal")
    {
        this.modalParentClassName = modalParentClass;

        this.mdbModalObj = new mdb.Modal($("." + this.modalParentClassName), {backdrop: 'static'}); 
    }
    //
    //-----------------------------------
    // SHOW INFORMATION | DEFAULT DIALOG
    //-----------------------------------
    //
    show(message, title = "Alert")
    {
        // skip if modal parent is undefined or null
        if (this.modalParentClassName == undefined || this.modalParentClassName == null)
            return;

        // set header background
        $("." + this.modalParentClassName + " .alertDialogModalHeader")
            .removeClass("bg-red")
            .removeClass("bg-accent")
            .addClass("bg-base");

        // set the title
        $("." + this.modalParentClassName + " #alertDialogModalLabel").text(title);

        // set the message
        $("." + this.modalParentClassName + " .alertDialogModalBody").text(message);
 
        this.mdbModalObj.show();
    }
    //
    //-----------------------------------
    // SHOW WARNING DIALOG
    //-----------------------------------
    //
    warn(message, title = "Warning")
    {
        // skip if modal parent is undefined or null
        if (this.modalParentClassName == undefined || this.modalParentClassName == null)
            return;

        // set header background
        $("." + this.modalParentClassName + " .alertDialogModalHeader")
            .removeClass("bg-red")
            .removeClass("bg-base")
            .addClass("bg-accent");

        // set the title
        $("." + this.modalParentClassName + " #alertDialogModalLabel").text(title);

        // set the message
        $("." + this.modalParentClassName + " .alertDialogModalBody").text(message);

        this.mdbModalObj.show();
    }
    //
    //-----------------------------------
    // SHOW ERROR DIALOG
    //-----------------------------------
    //
    danger(message, title = "Failure", mode = 1)
    {
        // skip if modal parent is undefined or null
        if (this.modalParentClassName == undefined || this.modalParentClassName == null)
            return;

        // set header background
        $("." + this.modalParentClassName + " .alertDialogModalHeader")
            .removeClass("bg-accent")
            .removeClass("bg-base")
            .addClass("bg-red");

        // set the title
        $("." + this.modalParentClassName + " #alertDialogModalLabel").text(title);

        // set the message
        $("." + this.modalParentClassName + " .alertDialogModalBody").text(message);

        this.mdbModalObj.show();
    }
}