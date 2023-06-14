var btnGetStarted = undefined;

var effects = undefined;

$(document).ready(function(){
    onAwake();
});

function onAwake()
{
    effects = new Effects();

    btnGetStarted = $("#btn-get-started");

    onBind();
}

function onBind()
{
    // when the 'Get Started' button was clicked,
    // scroll down to the Get Started section
    btnGetStarted.click(() => effects.scrollTo("get-started", 1500));
}