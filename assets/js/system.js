class Input
{
    /**
     * Force textbox to accept only letters, numbers, dashes and underscores
     * @param {string} inputClass - The input field's classname. Requires type=text
     */
    static forceAlphaNums(inputClass)
    {
        $("." + inputClass).on("input", function(e)
        {
            var newValue = this.value.replace(/[^A-Za-z0-9\s\-_]/gi, "");

            if (this.value != newValue)
                this.value = newValue;
        });
    }

    static whiteList(inputClass, expression)
    {
        $("." + inputClass).on("input", function(e)
        {
            var newValue = this.value.replace(expression, "");

            if (this.value != newValue)
                this.value = newValue;
        });
    }

    static forceRemarks(inputClass)
    {
        $("." + inputClass).on("input", function(e)
        {
            var newValue = this.value.replace(/[^A-Za-z0-9.\s\-_]/gi, "");

            if (this.value != newValue)
                this.value = newValue;
        });
    }
    static forceAddress(inputClass)
    {
        $("." + inputClass).on("input", function(e)
        {
            var newValue = this.value.replace(/[^A-Za-z0-9.\s\-_#]/gi, "");

            if (this.value != newValue)
                this.value = newValue;
        });
    }
    /**
     * Force textbox to only accept numbers
     * @param {string} inputClass - The input field's classname. Requires type=text
     */
    static forceNumeric(inputClass)
    {
        $("." + inputClass).on("input", function(e)
        {
            var newValue = this.value.replace(/[^0-9]/g, '').replace(/(\..*)\./g, '$1');
 
            if (this.value != newValue)
                this.value = newValue;
        });
    }
    /**
     * Force textbox to only accept decimals with
     * Only one period allowed
     * @param {string} inputClass - The input field's classname. Requires type=text
     */
    static forceDecimals(inputClass)
    {
        $("." + inputClass).on("input", function(e)
        {
            var newValue = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');
 
            if (this.value != newValue)
                this.value = newValue;
        });
    } 

    /**
    * Force newly appended textbox to only accept numbers
    * @param {string} inputClass - The input field's classname. Requires type=text
    */
    static forceNumericOnAppend(inputClass) 
    {
        $(document).on("input", "." + inputClass, function (e) 
        {
            var newValue = this.value.replace(/[^0-9]/g, '').replace(/(\..*)\./g, '$1');

            if (this.value != newValue)
                this.value = newValue;
        });
    }
}

class System
{
    /**
     * Get an element's class name
     * @param {string} elem | the HTML element
     * @returns class name of an element
     */
    static getClass(elem)
    {
        return elem.attr('class').split(' ').join('.');
    }
    /**
     * Prevents a form from submitting when enter key was pressed
     * @param {element} form | the form element
     */
    static preventPostOnEnter(form)
    {
        form.on('keyup keypress', function(e) 
        {
            var keyCode = e.keyCode || e.which;
            if (keyCode === 13) {
                e.preventDefault();
                return false;
            }
        });
    }
    /**
     * Check if a value is null or empty string
     * @param {any} value - the value to check 
     * @returns bool
     */
    static isNullOrEmpty(value)
    {
        return (!value && value.length == 0);
    }
}