var transitionQueue = {}; 
var endIndex = 0;
var transitionStep = 150;   // play transition every n millisec
var intervalId = undefined; // interval id will be used to stop the interval
var counter = 0;            // counter will be used to count upto queue length. this will
                            // force the interval to stop when queue length is reached.

$(document).ready(function()
{
    enqueueElements();

    // Count how many transition objects are in the queue .
    // Do not run transition when none
    if ((Object.keys(transitionQueue).length) < 1)
        return;

    // Start the transitions
    intervalId = setInterval(play, transitionStep);
});
// 
// To use transitions, an element must have 
// a class called: effect-reciever. Then
// it should have Attributes such as 
// data-transition-index, data-transition
//
function enqueueElements()
{
    // collect all elements with classname ".effect-reciever"
    var elements = $(".effect-reciever");

    var indeces = [];

    // foreach of those elements, grab a reference to their 
    // index and effect attributes. Then 
    // 
    $(elements).each(function(idx, el)
    {  
        // Get the transition index and the effect to use
        var index = $(el).attr("data-transition-index");
        var effect = $(el).attr("data-transition");

        indeces.push(parseInt(index));

        // The Transition key will be used for sorting the queue
        // and also for assigning it as a class for an element
        var key = `transition-index-${index}`;
 
        // Create an effect object then enqueue it
        // onto the transitions queue
        enqueueTransition(key, toEffectObject(index, effect));

        // Apply the transition onto the element by 
        // adding it as a classname
        $(el).addClass(key);
    });

    // Order the transition queue by keys
    transitionQueue = sort(transitionQueue);

    endIndex = Math.max.apply(Math, indeces);
} 
 
function play()
{ 
    if (counter > endIndex)
    {
        clearInterval(intervalId);
        //console.log("transition stopped");
        return;
    }
 
    var key = `transition-index-${counter}`;

    if (key in transitionQueue)
    {
        var fx = transitionQueue[key];
        var element = `.${key}`;

        switch (fx.effect) {
            case "fadein":
                $(element).fadeIn('fast');
                break;

            case "fadeout":
                $(element).fadeOut();
                break;
        } 
    }

    counter++;
    //console.log("transition running");
}

function enqueueTransition(key, value)
{
    if (key in transitionQueue)
        return;

    transitionQueue[key] = value;
}

function toEffectObject(index, effect)
{
    var obj = {
        index: index,
        effect: effect
    };

    return obj;
}

function sort(obj) 
{
    return Object.keys(obj).sort().reduce(function (result, key) {
        result[key] = obj[key];
        return result;
    }, {});
}