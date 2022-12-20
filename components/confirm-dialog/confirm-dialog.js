class ConfirmDialog
{
    confirmModalParentClassName = undefined;
    mdbConfirmModalObj = undefined;

    constructor(modalParentClass = "confirmDialogModal")
    {
        this.confirmModalParentClassName = modalParentClass;

        this.mdbConfirmModalObj = new mdb.Modal($("." + this.confirmModalParentClassName), {backdrop: 'static'}); 
    }
    //
    //-----------------------------------
    //---- SHOW CONFIRMATION DIALOG -----
    //-----------------------------------
    //
    show(message, title = "Confirmation")
    {
        // skip if modal parent is undefined or null
        if (this.confirmModalParentClassName == undefined || this.confirmModalParentClassName == null)
            return;

        // set header background
        $("." + this.confirmModalParentClassName + " .confirmDialogModalHeader")
            .removeClass("bg-red")
            .removeClass("bg-accent")
            .addClass("bg-base");

        // set the title
        $("." + this.confirmModalParentClassName + " #confirmDialogModalLabel").text(title);

        // set the message
        $("." + this.confirmModalParentClassName + " .confirmDialogModalBody").text(message);
 
        this.mdbConfirmModalObj.show();
    }
}