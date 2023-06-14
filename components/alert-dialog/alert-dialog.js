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
    show(message, title = "Information")
    {
        // skip if modal parent is undefined or null
        if (this.modalParentClassName == undefined || this.modalParentClassName == null)
            return;

        // set header background
        $("." + this.modalParentClassName + " .alertDialogModalHeader")
            .removeClass("bg-red")
            .removeClass("bg-amber")
            .addClass("bg-base");

        // set the title
        $("." + this.modalParentClassName + " #alertDialogModalLabel").text(title).removeClass("text-dark").addClass("text-white");

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
            .addClass("bg-amber");

        // set the title
        $("." + this.modalParentClassName + " #alertDialogModalLabel").text(title).addClass("text-dark");

        // set the message
        $("." + this.modalParentClassName + " .alertDialogModalBody").text(message);

        this.mdbModalObj.show();
    }
    //
    //-----------------------------------
    // SHOW ERROR DIALOG
    //-----------------------------------
    //
    danger(message, title = "Failure")
    {
        // skip if modal parent is undefined or null
        if (this.modalParentClassName == undefined || this.modalParentClassName == null)
            return;

        // set header background
        $("." + this.modalParentClassName + " .alertDialogModalHeader")
            .removeClass("bg-amber")
            .removeClass("bg-base")
            .addClass("bg-red");

        // set the title
        $("." + this.modalParentClassName + " #alertDialogModalLabel").text(title).removeClass("text-dark").addClass("text-white");

        // set the message
        $("." + this.modalParentClassName + " .alertDialogModalBody").text(message);

        this.mdbModalObj.show();
    }
}