// Get tinyMCE window
var win = window.opener ? window.opener : window.dialogArguments;

var tinyMCE = null;
var tinyMCELang = null;

// Use top window if not defined
if (!win)
	win = top;

var tinyMCE = win.tinyMCE;
var tinyMCELang = win.tinyMCELang;

if (!tinyMCE)
	alert("tinyMCE object reference not found from popup.");

// Setup window openerer
window.opener = win;

// Setup title
var re = new RegExp('{|\\\$|}', 'g');
var title = document.title.replace(re, "");
if (typeof tinyMCELang[title] != "undefined") {
	var divElm = document.createElement("div");
	divElm.innerHTML = tinyMCELang[title];
	document.title = divElm.innerHTML;
}

// Setup dir
if (tinyMCELang['lang_dir'])
	document.dir = tinyMCELang['lang_dir'];

function TinyMCEPlugin_onLoad() {
	if (!tinyMCE.getWindowArg('mce_isFrameset', false))
		document.body.innerHTML = tinyMCE.applyTemplate(document.body.innerHTML, tinyMCE.windowArgs);
}

// Add onload trigger
if (tinyMCE.isMSIE)
	attachEvent("onload", TinyMCEPlugin_onLoad);
else
	addEventListener("load", TinyMCEPlugin_onLoad, false);

// Output Popup CSS class
document.write('<link href="' + tinyMCE.getParam("popups_css") + '" rel="stylesheet" type="text/css">');
