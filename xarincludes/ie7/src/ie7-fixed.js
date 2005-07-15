/*
    W3C compliance for Microsoft Internet Explorer

    this module forms part of IE7
    IE7 version 0.7.2 (alpha) 2004/08/22
    by Dean Edwards, 2004
*/
if (window.IE7) IE7.addModule("ie7-fixed", function() {
    // some things to consider for this hack.
    // the document body requires a fixed background. even if
    //  it is just a blank image.
    // you have to use setExpression instead of onscroll, this
    //  together with a fixed body background help avoid the
    //  annoying screen flicker of other solutions.

    CSSFixes.addRecalc("position\\s*:\\s*fixed", positionFixed);
    CSSFixes.addRecalc("background[\\w\\s-]*:[^};]*fixed", backgroundFixed);

    // scrolling is relative to the documentElement (HTML tag) when in
    //  standards mode, otherwise it's relative to the document body
    var body = document.body;
    var scrollParent$ = (quirksMode) ? "body" : "documentElement";
    var scrollParent = eval(scrollParent$);

    // this is requied by both position:fixed and background-attachment:fixed.
    // it is necessary for the document to also have a fixed background image.
    // we can fake this with a blank image if necessary
    if (body.currentStyle.backgroundAttachment != "fixed") {
        if (body.currentStyle.backgroundImage == "none") {
            body.runtimeStyle.backgroundImage = "url(http:)"; // dummy
        }
        body.runtimeStyle.backgroundAttachment = "fixed";
    }

    var ie7_tmp = tmpElement("img");

    // clone a "left" function to create a "top" function
    function topFunction(leftFunction) {
        return String(leftFunction)
        .replace(/Left/g, "Top")
        .replace(/left/g, "top")
        .replace(/Width/g, "Height")
        .replace(/X/g, "Y");
    };

// -----------------------------------------------------------------------
//  backgroundAttachment: fixed
// -----------------------------------------------------------------------

    function backgroundFixed(element) {
        if (element.currentStyle.backgroundAttachment != "fixed") return;
        if (!element.contains(body)) {
            backgroundFixed[backgroundFixed.count++] = element;
            backgroundLeft(element);
            backgroundTop(element);
            backgroundPosition(element);
        }
    };
    backgroundFixed.count = 0;

    function backgroundPosition(element) {
        ie7_tmp.src = element.currentStyle.backgroundImage.slice(5, -2);
        var parentElement = (element.canHaveChildren) ? element : element.parentElement;
        parentElement.appendChild(ie7_tmp);
        setOffsetLeft(element);
        setOffsetTop(element);
        parentElement.removeChild(ie7_tmp);
    };

    function backgroundLeft(element) {
        element.style.backgroundPositionX = element.currentStyle.backgroundPositionX;
        var expression = "(parseInt(runtimeStyle.offsetLeft)+document." + scrollParent$ + ".scrollLeft)||0";
        element.runtimeStyle.setExpression("backgroundPositionX", expression);
    };
    eval(topFunction(backgroundLeft));

    function setOffsetLeft(element) {
        element.runtimeStyle.offsetLeft = getOffsetLeft(element, element.style.backgroundPositionX) -
            element.getBoundingClientRect().left - element.clientLeft;
    };
    eval(topFunction(setOffsetLeft));

    function getOffsetLeft(element, position) {
        switch (position) {
            case "left":
            case "top":
                return 0;
            case "right":
            case "bottom":
                return scrollParent.clientWidth - ie7_tmp.offsetWidth;
            case "center":
                return (scrollParent.clientWidth - ie7_tmp.offsetWidth) / 2;
            default:
                if (/%$/.test(position)) {
                    return parseInt((scrollParent.clientWidth - ie7_tmp.offsetWidth) * parseFloat(position) / 100);
                }
                ie7_tmp.style.left = position;
                return ie7_tmp.offsetLeft;
        }
    };
    eval(topFunction(getOffsetLeft));

// -----------------------------------------------------------------------
//  position: fixed
// -----------------------------------------------------------------------

    function positionFixed(element) {
        if (element.currentStyle.position != "fixed") return;
        positionFixed[positionFixed.count++] = element;
        // we'll move the element about ourselves
        element.runtimeStyle.position = "absolute";
        foregroundPosition(element);
    };
    positionFixed.count = 0;

    function foregroundPosition(element, recalc) {
        element.parentElement.appendChild(ie7_tmp);
        positionLeft(element);
        positionTop(element);
        element.parentElement.removeChild(ie7_tmp);
    };

    function positionLeft(element, recalc) {
        element.runtimeStyle.left = "";
        element.runtimeStyle.screenLeft = element.getBoundingClientRect().left - 2;
        if (element.currentStyle.marginLeft != "auto") {
            ie7_tmp.style.left = element.currentStyle.marginLeft;
            element.runtimeStyle.screenLeft -= ie7_tmp.offsetLeft;
        }
        // onsrcoll produces jerky movement - this is better
        var expression = "runtimeStyle.screenLeft+document." + scrollParent$ + ".scrollLeft";
        if (!recalc) element.runtimeStyle.setExpression("pixelLeft", expression);
    };
    eval(topFunction(positionLeft));

// -----------------------------------------------------------------------
//  capture window resize
// -----------------------------------------------------------------------

    function resize() {
        for (var i = 0; i < backgroundFixed.count; i++)
            backgroundPosition(backgroundFixed[i]);
        for (i = 0; i < positionFixed.count; i++)
            foregroundPosition(positionFixed[i], true);
        timer = 0;
    };

    var timer;
    addEventHandler(window, "onresize", function() {
        if (!timer) timer = setTimeout(resize, 1);
    });

});
