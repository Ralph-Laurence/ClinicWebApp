class Input
{
    /**
     * Force textbox to accept only letters, numbers, dashes and underscores
     * @param {string} inputClass - The input field's classname. Requires type=text
     */
    static forceAlphaNums(inputClass, allowSpace = true)
    {  
        $("." + inputClass).on("input", function(e)
        {
            var newValue = this.value.replace(/[^A-Za-z0-9\s\.\-\_\@]/gi, "");
            
            if (!allowSpace)
                newValue = this.value.replace(/[^A-Za-z0-9\.\-\_\@]/gi, ""); 

            if (this.value != newValue)
                this.value = newValue;
        })
        .on("blur", function (e) {
            $(this).val($(this).val().trim());
        });
    }

    static forceAlpha(inputClass, allowSpace = true) 
    {
        $("." + inputClass).on("input", function (e) 
        {
            var newValue = this.value.replace(/[^A-Za-z\s\.\-]/gi, "");;

            if (!allowSpace)
                newValue = this.value.replace(/[^A-Za-z\.\-]/gi, "");

            if (this.value != newValue)
                this.value = newValue;
        })
        .on("blur", function(e)
        { 
            $(this).val($(this).val().trim());
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
        })
        .on("blur", function(e)
        { 
            $(this).val($(this).val().trim());
        });
    }
    static forceAddress(inputClass)
    {
        $("." + inputClass).on("input", function(e)
        {
            var newValue = this.value.replace(/[^A-Za-z0-9.\s\-_#,]/gi, "");

            if (this.value != newValue)
                this.value = newValue;
        })
        .on("blur", function(e)
        {
            $(this).val($(this).val().trim()); 
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
        return (!value || value === "" || value == null);
    }
    /**
     * Checks if an object is empty
     * @param {Object} obj 
     * @returns 
     */
    static isObjectEmpty(obj) {
        return Object.keys(obj).length === 0;
    }

    static TryParseInt(str, defaultValue) 
    {
        var retValue = defaultValue;

        if (str !== null) 
        {
            if (str.length > 0) 
            {
                if (!isNaN(str)) {
                    retValue = parseInt(str);
                }
            }
        }
        return retValue;
   }

    static getDaysOfWeek(weekNumber, year) 
    {
        var date = new Date(year, 0, 1 + (weekNumber - 1) * 7);
        var days = [];
        
        for (var i = 0; i < 7; i++) 
        {
            days.push(new Date(date));
            date.setDate(date.getDate() + 1);
        }

        return days;
    }
}

class Effects
{
    // animated scrolling to element with
    // given duration in milliseconds
    scrollTo(elementId, duration = 1000)
    {
        $('html, body').animate({
            scrollTop: $("#" + elementId).offset().top
        }, duration);
    }

    scrollActiveNavLink(offset = 4, block = 'center')
    {
        var container = $('.side-nav .simplebar-content-wrapper');
        var focusTarget = container.find('.active').attr('id');

        if (!focusTarget)
        {
            $(".side-nav .active")[0].scrollIntoView({ behavior: 'smooth', block: block }); // block: 'center'
            return;
        }

        container.animate({
            scrollTop: $("#" + focusTarget).offset().top / offset
        }, 700);
    }
}

function navHref(url) 
{
    window.location.replace(url)

    try 
    {
        window.history.pushState(null, null, window.location.href);
        window.onpopstate = function () {
            window.history.go(1);
        };
    }
    catch
    {
        window.location.href = url;
    }
}

function redirectTo(url) 
{
    window.location.replace(url)

    try 
    {
        window.history.pushState(null, null, window.location.href);
        window.onpopstate = function () {
            window.history.go(1);
        };
    }
    catch
    {
        window.location.href = url;
    }
}