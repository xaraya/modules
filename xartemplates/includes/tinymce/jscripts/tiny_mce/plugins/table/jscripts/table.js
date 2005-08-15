var action;

function insertTable() {
	var args = new Array();

	args["cols"] = document.forms[0].cols.value;
	args["rows"] = document.forms[0].rows.value;
	args["border"] = document.forms[0].border.value;
	args["cellpadding"] = document.forms[0].cellpadding.value;
	args["cellspacing"] = document.forms[0].cellspacing.value;
	args["width"] = document.forms[0].width.value;
	args["height"] = document.forms[0].height.value;
	args["bordercolor"] =	document.forms[0].bordercolor.value;
	args["bgcolor"] =	document.forms[0].bgcolor.value;
	args["align"] = document.forms[0].align.options[document.forms[0].align.selectedIndex].value;
	args["className"] = document.forms[0].styleSelect.options[document.forms[0].styleSelect.selectedIndex].value;
	args["action"] = action;

	tinyMCEPopup.execCommand("mceInsertTable", false, args);
	tinyMCEPopup.close();
}

function renderColorPicker(id, target_form_element) {
	var html = "";

	html += '<img id="' + id + '" src="images/color.gif"';
	html += ' onmouseover="tinyMCE.switchClass(this,\'mceButtonOver\');"';
	html += ' onmouseout="tinyMCE.restoreClass(this);"';
	html += ' onmousedown="tinyMCE.restoreAndSwitchClass(this,\'mceButtonDown\');"';
	html += ' onclick="return tinyMCEPopup.pickColor(event,\'' + target_form_element +'\');"';
	html += ' width="20" height="16" border="0" title="' + tinyMCE.getLang('lang_browse') + '"';
	html += ' class="mceButtonNormal" alt="' + tinyMCE.getLang('lang_browse') + '" />';

	document.write(html);
}

function init() {
	action = tinyMCE.getWindowArg('action');

	for (var i=0; i<document.forms[0].align.options.length; i++) {
		if (document.forms[0].align.options[i].value == tinyMCE.getWindowArg('align'))
			document.forms[0].align.options.selectedIndex = i;
	}

	var className = tinyMCE.getWindowArg('className');
	var styleSelectElm = document.forms[0].styleSelect;
	var stylesAr = tinyMCE.getParam('theme_advanced_styles', false);
	if (stylesAr) {
		stylesAr = stylesAr.split(';');

		for (var i=0; i<stylesAr.length; i++) {
			var key, value;

			key = stylesAr[i].split('=')[0];
			value = stylesAr[i].split('=')[1];

			styleSelectElm.options[styleSelectElm.length] = new Option(key, value);
			if (value == className)
				styleSelectElm.options.selectedIndex = styleSelectElm.options.length-1;
		}
	} else {
		var csses = tinyMCE.getCSSClasses(tinyMCE.getWindowArg('editor_id'));
		for (var i=0; i<csses.length; i++) {
			styleSelectElm.options[styleSelectElm.length] = new Option(csses[i], csses[i]);
			if (csses[i] == className)
				styleSelectElm.options.selectedIndex = styleSelectElm.options.length-1;
		}
	}

	if (tinyMCE.getWindowArg('action') == "update") {
		document.forms[0].cols.disabled = true;
		document.forms[0].rows.disabled = true;
	}

	var formObj = document.forms[0];
	formObj.cols.value = tinyMCE.getWindowArg('cols');
	formObj.rows.value = tinyMCE.getWindowArg('rows');
	formObj.border.value = tinyMCE.getWindowArg('border');
	formObj.cellpadding.value = tinyMCE.getWindowArg('cellpadding');
	formObj.cellspacing.value = tinyMCE.getWindowArg('cellspacing');
	formObj.width.value = tinyMCE.getWindowArg('width');
	formObj.height.value = tinyMCE.getWindowArg('height');
	formObj.bordercolor.value = tinyMCE.getWindowArg('bordercolor');
	formObj.bgcolor.value = tinyMCE.getWindowArg('bgcolor');
	formObj.insert.value = tinyMCE.getLang('lang_' + action, 'Insert', true); 

	document.getElementById('bordercolor_pick').style.backgroundColor = formObj.bordercolor.value;
	document.getElementById('bgcolor_pick').style.backgroundColor = formObj.bgcolor.value;

	updateColor('bordercolor_pick', 'bordercolor');
	updateColor('bgcolor_pick', 'bgcolor');

	// Resize some elements
	if (tinyMCE.getParam("file_browser_callback") != null) {
		document.getElementById('backgroundimage').style.width = '180px';
	}
}

function updateColor(img_id, form_element_id) {
	document.getElementById(img_id).style.backgroundColor = document.forms[0].elements[form_element_id].value;
}

function renderImageBrowser(id, target_form_element) {
	if (tinyMCE.getParam("file_browser_callback") == null)
		return;

	var html = "";

	html += '<img id="' + id + '" src="../../themes/advanced/images/browse.gif"';
	html += ' onmouseover="tinyMCE.switchClass(this,\'mceButtonOver\');"';
	html += ' onmouseout="tinyMCE.restoreClass(this);"';
	html += ' onmousedown="tinyMCE.restoreAndSwitchClass(this,\'mceButtonDown\');"';
	html += ' onclick="openImageBrower(this, \'' + target_form_element + '\');"';
	html += ' width="20" height="18" border="0" title="' + tinyMCE.getLang('lang_browse') + '"';
	html += ' class="mceButtonNormal" alt="' + tinyMCE.getLang('lang_browse') + '" />';

	document.write(html);
}

function openImageBrower(img, target_form_element) {
	if (img.className != "mceButtonDisabled")
		tinyMCE.openFileBrowser(target_form_element, document.forms[0].elements[target_form_element].value, 'image', window);
}

function cancelAction() {
	// Close the dialog
	tinyMCEPopup.close();
}
