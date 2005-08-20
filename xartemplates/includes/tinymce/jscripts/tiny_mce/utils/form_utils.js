/**
 * $RCSfile: form_utils.js,v $
 * $Revision: 1.2 $
 * $Date: 2005/08/18 16:50:53 $
 *
 * Various form utilitiy functions.
 *
 * @author Moxiecode
 * @copyright Copyright © 2005, Moxiecode Systems AB, All rights reserved.
 */

function renderColorPicker(id, target_form_element) {
	var html = "";

	html += '<img id="' + id + '" src="../../themes/advanced/images/color.gif"';
	html += ' onmouseover="tinyMCE.switchClass(this,\'mceButtonOver\');"';
	html += ' onmouseout="tinyMCE.restoreClass(this);"';
	html += ' onmousedown="tinyMCE.restoreAndSwitchClass(this,\'mceButtonDown\');"';
	html += ' onclick="return tinyMCEPopup.pickColor(event,\'' + target_form_element +'\');"';
	html += ' width="20" height="16" border="0" title="' + tinyMCE.getLang('lang_browse') + '"';
	html += ' class="mceButtonNormal" alt="' + tinyMCE.getLang('lang_browse') + '" />';

	document.write(html);
}

function updateColor(img_id, form_element_id) {
	document.getElementById(img_id).style.backgroundColor = document.forms[0].elements[form_element_id].value;
}

function renderBrowser(id, target_form_element, type) {
	if (tinyMCE.getParam("file_browser_callback") == null)
		return;

	var html = "";

	html += '<img id="' + id + '" src="../../themes/advanced/images/browse.gif"';
	html += ' onmouseover="tinyMCE.switchClass(this,\'mceButtonOver\');"';
	html += ' onmouseout="tinyMCE.restoreClass(this);"';
	html += ' onmousedown="tinyMCE.restoreAndSwitchClass(this,\'mceButtonDown\');"';
	html += ' onclick="openBrower(this, \'' + target_form_element + '\', \'' + type + '\');"';
	html += ' width="20" height="18" border="0" title="' + tinyMCE.getLang('lang_browse') + '"';
	html += ' class="mceButtonNormal" alt="' + tinyMCE.getLang('lang_browse') + '" />';

	document.write(html);
}

function openBrower(img, target_form_element, type) {
	if (img.className != "mceButtonDisabled")
		tinyMCE.openFileBrowser(target_form_element, document.forms[0].elements[target_form_element].value, type, window);
}

function selectByValue(form_obj, field_name, value, add_custom) {
	if (!form_obj || !form_obj.elements[field_name])
		return;

	var sel = form_obj.elements[field_name];

	var found = false;
	for (var i=0; i<sel.options.length; i++) {
		var option = sel.options[i];

		if (option.value == value) {
			option.selected = true;
			found = true;
		} else
			option.selected = false;
	}

	if (!found && add_custom && value != '') {
		var option = new Option('Value: ' + value, value);
		option.selected = true;
		sel.options[sel.options.length] = option;
	}

	return found;
}

function getSelectValue(form_obj, field_name) {
	var elm = form_obj.elements[field_name];

	if (elm == null || elm.options == null)
		return "";

	return elm.options[elm.selectedIndex].value;
}

function addClassesToList(list_id, element_name) {
	// Setup class droplist
	var styleSelectElm = document.getElementById(list_id);
	var stylesAr = tinyMCE.getParam('theme_advanced_styles', false);
	if (stylesAr) {
		stylesAr = stylesAr.split(';');

		for (var i=0; i<stylesAr.length; i++) {
			var key, value;

			key = stylesAr[i].split('=')[0];
			value = stylesAr[i].split('=')[1];

			styleSelectElm.options[styleSelectElm.length] = new Option(key, value);
		}
	} else {
		var csses = tinyMCE.getCSSClasses(tinyMCE.getWindowArg('editor_id'));
		for (var i=0; i<csses.length; i++) {
			styleSelectElm.options[styleSelectElm.length] = new Option(csses[i], csses[i]);
		}
	}
}
