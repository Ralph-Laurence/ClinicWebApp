class ConfirmDialog
{
    confirmModalParentClassName = undefined;
    mdbConfirmModalObj = undefined; 

    confirm_BtnOK = undefined;
    confirm_BtnCancel = undefined;
    confirm_ModalClose = undefined;

    actionOnOK = undefined;
    actionOnCancel = undefined;

    constructor(modalParentClass = "confirmDialogModal")
    {
        this.confirmModalParentClassName = modalParentClass;

        this.mdbConfirmModalObj = new mdb.Modal($("." + this.confirmModalParentClassName), {backdrop: 'static'}); 

        // have a reference to the ok and cancel button
        this.confirm_BtnOK = $(".confirmDialogModalFooter .confirm-btn-ok");
        this.confirm_BtnCancel = $(".confirmDialogModalFooter .confirm-btn-cancel");
        this.confirm_ModalClose = $(".confirmDialogModalFooter .confirmModalClose");

        // fire this action after the ok button was clicked
        this.confirm_BtnOK.click(() => 
        {
            if (this.actionOnOK != undefined)
            {
                this.actionOnOK();
                this.actionOnOK = undefined;
            }
        });

        // fire this action after the cancel button was clicked
        this.confirm_BtnCancel.click(() => 
        {
            if (this.actionOnCancel != undefined)
            {
                this.actionOnCancel();
                this.actionOnCancel = undefined;
            }
        });

        // fire this action after the close button was clicked
        this.confirm_ModalClose.click(() => 
        {
            if (this.actionOnCancel != undefined)
            {
                this.actionOnCancel();
                this.actionOnCancel = undefined;
            }
        });
    }
    //
    //-----------------------------------
    //---- SHOW CONFIRMATION DIALOG -----
    //-----------------------------------
    //
    show(message, title = "Attention")
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
        $("." + this.confirmModalParentClassName + " .confirmDialogTitleSpan").text(title);

        // set the message
        $("." + this.confirmModalParentClassName + " .confirmDialogModalBody").text(message);
 
        this.mdbConfirmModalObj.show();
    } 
}