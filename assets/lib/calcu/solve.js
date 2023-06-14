
$(document).ready(() => 
{ 
    $(".btn-conv").click(function(){
        var link = $(".converter-link").val();
        window.location.replace(link);
    });

    $(".btnSolve").click(function () {  
        Solve()
    });

    $(".lcd-screen").click(() => $(".expression-lcd").focus());

    $(".expression-lcd")
    .on("keypress", function(e)
    {  
        if(e.keyCode == 13)
            Solve();
    })
    .on("input", function (e) 
    {
        // display value 
        var value = $(this).val().replace(/[^0-9\u00B2\u221A\u0052\u0072\u006e\u004e\u0064\u0044\u221A\u00d7\u00f7\.\-\+\*\/\)\(]/g, "")
        .replace(/\*/g, '\u00d7')
        .replace(/\//g, '\u00f7') ;

        if (this.value != value)
            this.value = value; 
          
        // evaluation value (hidden)
        var eq = this.value
        .replace(/\u00d7/g, "*")
        .replace(/\u00f7/g, "/")
        .replace(/RND\(/g, "Math.round\(")
        .replace(/(\d+)\u00B2/g, (m, n) => Pow(+n))
        .replace(/\u221A\u0028/g, "Math.sqrt\(");

        $(".equation-lcd").val(eq);

        // if (this.value != newValue)
        //     this.value = newValue;
    })
    .on("blur", function(e)
    { 
        $(this).val($(this).val().trim());
    });  

    $(".expression-lcd").focus();
});
//
// SOLVE THE EQUATION
//
function Solve()
{
    try
    {
        // evaluate the equation
        var equation = $(".equation-lcd").val();
        var test = eval(equation);

        // Store only valid results
        if (Number.isFinite(test))
        {
            Ans = test;

            // Show the result
            $(".result-lcd").val(test);
        }
        else
        {
            // Show error
            $(".result-lcd").val("Math Error");
        } 
    }
    catch(ex) // Thanks for the 'instanceof' error catch -> https://stackoverflow.com/a/26347058
    {
        // Show syntax error
        if (ex instanceof SyntaxError) {
            $(".result-lcd").val("Syntax Error");
        }
        else
        {
            // For any kind of error, show INVALID
            $(".result-lcd").val("Invalid");
        }
    }
}

//
// WRITE TEXT TO EXPRESSION SCREEN
//
function WriteExp(exp)
{
    if ($("#result-lcd").val() != "")
        ClearResult();

    var equation = $(".expression-lcd").val() + exp;
     
    $(".expression-lcd").val(equation).focus();

     
    // Replace the SIN, COS, TAN etc FUNCTIONS
    var replaced = equation.replace(/\u00d7/g, "*")
                           .replace(/\u00f7/g, "/")
                           .replace(/RND\(/g, "Math.round\(")
                           .replace(/(\d+)\u00B2/g, (m, n) => Pow(+n))
                           .replace(/\u221A\u0028/g, "Math.sqrt\(");

    $(".equation-lcd").val(replaced);
}

//
// Create Factorial
//
function Pow(exp)
{  
    var out = Math.pow(exp, 2);
    return out;
}
//
// CLEAR THE RESULTS LCD
//
function ClearResult()
{
    $("#result-lcd").val("");
    $(".equation-lcd").val("");
}

//
// CLEAR THE MEMORY REGISTERS
//
function toFraction()
{
    Solve();

    var input = $(".result-lcd").val();

    if (!input || !parseFloat(input))
        return;

    var f = new Fraction( input );

    // build the fraction string
    var raw = f.toString();
 
    var vulgar = 
    {
        '0': {'sup': '\u2070', 'sub': '\u2080' },
        '1': {'sup': '\u00b9', 'sub': '\u2081' },
        '2': {'sup': '\u00b2', 'sub': '\u2082' },
        '3': {'sup': '\u00b3', 'sub': '\u2083' },
        '4': {'sup': '\u2074', 'sub': '\u2084' },
        '5': {'sup': '\u2075', 'sub': '\u2085' },
        '6': {'sup': '\u2076', 'sub': '\u2086' },
        '7': {'sup': '\u2077', 'sub': '\u2087' },
        '8': {'sup': '\u2078', 'sub': '\u2088' },
        '9': {'sup': '\u2079', 'sub': '\u2089' },
    }; 
 
    // get only the fractions (if mixed number)
    var fractions = raw.substring(raw.indexOf(' ') + 1);

    // split the fractions
    var fract = fractions.split('/');

    // convert numerators to superscript
    var numerator = [];

    for (n of fract[0]) { numerator.push(vulgar[n]['sup']); }

    // convert denominators to subscript
    var denominator = [];

    for (d of fract[1]) { denominator.push(vulgar[d]['sub']); }

    //$('#demo').val(`${numerator.join('')}\u2044${denominator.join('')}`);

    var replace = `${numerator.join('')}\u2044${denominator.join('')}`;

    var out = raw.replace(fractions, replace);

    $(".result-lcd").val(out);
}
//
// CLEAR SOLVE RESULTS
//
function ClearAll()
{
    ClearResult();
    $(".expression-lcd").val("");
}

//
// DELETE LAST CHARACTER FROM STRING
//
function Delete()
{
    // Cant delete when an answer/result is present
    var hasResult = $(".result-lcd").val() != "";

    if (!hasResult)
    {
        var original = $(".expression-lcd").val();

        var processed = original.slice(0,-1);
        $(".expression-lcd").val(processed);
    }
}