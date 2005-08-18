/* Functions for the advlink plugin popup */

var templates = {
	"window.open" : "window.open('${url}','${target}','${options}')"
};

function preinit() {
	// Initialize
	tinyMCE.setWindowArg('mce_windowresize', false);

	// Import external list url javascript
	var url = tinyMCE.getParam("external_link_list_url");
	if (url != null) {
		// Fix relative
		if (url.charAt(0) != '/')
			url = tinyMCE.documentBasePath + "/" + url;

		document.write('<sc'+'ript language="javascript" type="text/javascript" src="' + url + '"></sc'+'ript>');
	}
}

function init() {
	var formObj = document.forms[0];
	var inst = tinyMCE.getInstanceById(tinyMCE.getWindowArg('editor_id'));
	var elm = inst.getFocusElement();
	var action = "insert";

	// Resize some elements
	if (tinyMCE.getParam("file_browser_callback") != null) {
		document.getElementById('href').style.width = '260px';
	}

	elm = tinyMCE.getParentElement(elm, "a");
	if (elm != null && elm.nodeName == "A")
		action = "update";

	formObj.insert.value = tinyMCE.getLang('lang_' + action, 'Insert', true); 

	setPopupControlsDisabled(true);

	if (action == "update") {
		var href = tinyMCE.getAttrib(elm, 'href');

		// Fix for drag-drop/copy paste bug in Mozilla
		mceRealHref = tinyMCE.getAttrib(elm, 'mce_real_href');
		if (mceRealHref != "")
			href = mceRealHref;

		href = convertURL(href, elm, true);

		var onclick = tinyMCE.cleanupEventStr(tinyMCE.getAttrib(elm, 'onclick'));
		if (onclick == null || onclick == "")
			onclick = tinyMCE.cleanupEventStr(tinyMCE.getAttrib(elm, 'mce_onclick'));

		// Setup form data
		setFormValue('href', href);
		setFormValue('title', tinyMCE.getAttrib(elm, 'title'));
		setFormValue('id', tinyMCE.getAttrib(elm, 'id'));
		setFormValue('style', elm.style.cssText.toLowerCase());
		setFormValue('rel', tinyMCE.getAttrib(elm, 'rel'));
		setFormValue('rev', tinyMCE.getAttrib(elm, 'rev'));
		setFormValue('charset', tinyMCE.getAttrib(elm, 'charset'));
		setFormValue('hreflang', tinyMCE.getAttrib(elm, 'hreflang'));
		setFormValue('dir', tinyMCE.getAttrib(elm, 'dir'));
		setFormValue('lang', tinyMCE.getAttrib(elm, 'lang'));
		setFormValue('tabindex', tinyMCE.getAttrib(elm, 'tabindex', typeof(elm.tabindex) != "undefined" ? elm.tabindex : ""));
		setFormValue('accesskey', tinyMCE.getAttrib(elm, 'accesskey', typeof(elm.accesskey) != "undefined" ? elm.accesskey : ""));
		setFormValue('type', tinyMCE.getAttrib(elm, 'type'));
		setFormValue('onfocus', tinyMCE.cleanupEventStr(tinyMCE.getAttrib(elm, 'onfocus')));
		setFormValue('onblur', tinyMCE.cleanupEventStr(tinyMCE.getAttrib(elm, 'onblur')));
		setFormValue('onclick', onclick);
		setFormValue('ondblclick', tinyMCE.cleanupEventStr(tinyMCE.getAttrib(elm, 'ondblclick')));
		setFormValue('onmousedown', tinyMCE.cleanupEventStr(tinyMCE.getAttrib(elm, 'onmousedown')));
		setFormValue('onmouseup', tinyMCE.cleanupEventStr(tinyMCE.getAttrib(elm, 'onmouseup')));
		setFormValue('onmouseover', tinyMCE.cleanupEventStr(tinyMCE.getAttrib(elm, 'onmouseover')));
		setFormValue('onmousemove', tinyMCE.cleanupEventStr(tinyMCE.getAttrib(elm, 'onmousemove')));
		setFormValue('onmouseout', tinyMCE.cleanupEventStr(tinyMCE.getAttrib(elm, 'onmouseout')));
		setFormValue('onkeypress', tinyMCE.cleanupEventStr(tinyMCE.getAttrib(elm, 'onkeypress')));
		setFormValue('onkeydown', tinyMCE.cleanupEventStr(tinyMCE.getAttrib(elm, 'onkeydown')));
		setFormValue('onkeyup', tinyMCE.cleanupEventStr(tinyMCE.getAttrib(elm, 'onkeyup')));
		setFormValue('target', tinyMCE.getAttrib(elm, 'target'));
		setFormValue('classes', tinyMCE.getAttrib(elm, 'class'));

		// Parse onclick data
		if (onclick != null && onclick.indexOf('window.open') != -1)
			parseWindowOpen(onclick);
		else
			parseFunction(onclick);

		// Select by the values
		selectByValue(formObj, 'dir', tinyMCE.getAttrib(elm, 'dir'));
		selectByValue(formObj, 'rel', tinyMCE.getAttrib(elm, 'rel'));
		selectByValue(formObj, 'rev', tinyMCE.getAttrib(elm, 'rev'));
		selectByValue(formObj, 'linklisthref', href);

		if (href.charAt(0) == '#')
			selectByValue(formObj, 'anchorlist', href);

		selectByValue(formObj, 'class', tinyMCE.getAttrib(elm, 'class'), true);
		selectByValue(formObj, 'targetlist', tinyMCE.getAttrib(elm, 'target'), true);
	}

	window.focus();
}

function setFormValue(name, value) {
	document.forms[0].elements[name].value = value;
}

function convertURL(url, node, on_save) {
	return eval("tinyMCEPopup.windowOpener." + tinyMCE.settings['urlconverter_callback'] + "(url, node, on_save);");
}

function parseWindowOpen(onclick) {
	var formObj = document.forms[0];

	// Preprocess center code
	if (onclick.indexOf('return false;') != -1) {
		formObj.popupreturn.checked = true;
		onclick = onclick.replace('return false;', '');
	}

	var onClickData = parseLink(onclick);

	if (onClickData != null) {
		formObj.ispopup.checked = true;
		setPopupControlsDisabled(false);

		var onClickWindowOptions = parseOptions(onClickData['options']);

		formObj.popupname.value = onClickData['target'];
		formObj.popupurl.value = onClickData['url'];
		formObj.popupwidth.value = getOption(onClickWindowOptions, 'width');
		formObj.popupheight.value = getOption(onClickWindowOptions, 'height');

		formObj.popupleft.value = getOption(onClickWindowOptions, 'left');
		formObj.popuptop.value = getOption(onClickWindowOptions, 'top');

		if (formObj.popupleft.value.indexOf('screen') != -1)
			formObj.popupleft.value = "c";

		if (formObj.popuptop.value.indexOf('screen') != -1)
			formObj.popuptop.value = "c";

		formObj.popuplocation.checked = getOption(onClickWindowOptions, 'location') == "yes";
		formObj.popupscrollbars.checked = getOption(onClickWindowOptions, 'scrollbars') == "yes";
		formObj.popupmenubar.checked = getOption(onClickWindowOptions, 'menubar') == "yes";
		formObj.popupresizable.checked = getOption(onClickWindowOptions, 'resizable') == "yes";
		formObj.popuptoolbar.checked = getOption(onClickWindowOptions, 'toolbar') == "yes";
		formObj.popupstatus.checked = getOption(onClickWindowOptions, 'status') == "yes";
		formObj.popupdependent.checked = getOption(onClickWindowOptions, 'dependent') == "yes";
	}
}

function parseFunction(onclick) {
	var formObj = document.forms[0];
	var onClickData = parseLink(onclick);

	// TODO: Add stuff here
}

function getOption(opts, name) {
	return typeof(opts[name]) == "undefined" ? "" : opts[name];
}

function setPopupControlsDisabled(state) {
	var formObj = document.forms[0];

	formObj.popupname.disabled = state;
	formObj.popupurl.disabled = state;
	formObj.popupwidth.disabled = state;
	formObj.popupheight.disabled = state;
	formObj.popupleft.disabled = state;
	formObj.popuptop.disabled = state;
	formObj.popuplocation.disabled = state;
	formObj.popupscrollbars.disabled = state;
	formObj.popupmenubar.disabled = state;
	formObj.popupresizable.disabled = state;
	formObj.popuptoolbar.disabled = state;
	formObj.popupstatus.disabled = state;
	formObj.popupreturn.disabled = state;
	formObj.popupdependent.disabled = state;
}

function parseLink(link) {
	link = link.replace(new RegExp('&#39;', 'g'), "'");

	var fnName = link.replace(new RegExp("\\W*([A-Za-z0-9\.]*)\\W*\\(.*", "gi"), "$1");

	// Is function name a template function
	var template = templates[fnName];
	if (template) {
		// Build regexp
		var variableNames = template.match(new RegExp("'?\\$\\{[A-Za-z0-9\.]*\\}'?", "gi"));
		var regExp = "\\W*[A-Za-z0-9\.]*\\W*\\(";
		var replaceStr = "";
		for (var i=0; i<variableNames.length; i++) {
			// Is string value
			if (variableNames[i].indexOf("'${") != -1)
				regExp += "'(.*)'";
			else // Number value
				regExp += "([0-9]*)";

			replaceStr += "$" + (i+1);

			// Cleanup variable name
			variableNames[i] = variableNames[i].replace(new RegExp("[^A-Za-z0-9]", "gi"), "");

			if (i != variableNames.length-1) {
				regExp += "\\W*,\\W*";
				replaceStr += "<delim>";
			} else
				regExp += ".*";
		}

		regExp += "\\);?";

		// Build variable array
		var variables = new Array();
		variables["_function"] = fnName;
		var variableValues = link.replace(new RegExp(regExp, "gi"), replaceStr).split('<delim>');
		for (var i=0; i<variableNames.length; i++)
			variables[variableNames[i]] = variableValues[i];

		return variables;
	}

	return null;
}

function parseOptions(opts) {
	if (opts == null || opts == "")
		return new Array();

	// Cleanup the options
	opts = opts.toLowerCase();
	opts = opts.replace(/;/g, ",");
	opts = opts.replace(/[^0-9a-z=,]/g, "");

	var optionChunks = opts.split(',');
	var options = new Array();

	for (var i=0; i<optionChunks.length; i++) {
		var parts = optionChunks[i].split('=');

		if (parts.length == 2)
			options[parts[0]] = parts[1];
	}

	return options;
}

function buildOnClick() {
	var formObj = document.forms[0];

	if (!formObj.ispopup.checked) {
		formObj.onclick.value = "";
		return;
	}

	var onclick = "window.open('";

	onclick += formObj.popupurl.value + "','";
	onclick += formObj.popupname.value + "','";

	if (formObj.popuplocation.checked)
		onclick += "location=yes,";

	if (formObj.popupscrollbars.checked)
		onclick += "scrollbars=yes,";

	if (formObj.popupmenubar.checked)
		onclick += "menubar=yes,";

	if (formObj.popupresizable.checked)
		onclick += "resizable=yes,";

	if (formObj.popuptoolbar.checked)
		onclick += "toolbar=yes,";

	if (formObj.popupstatus.checked)
		onclick += "status=yes,";

	if (formObj.popupdependent.checked)
		onclick += "dependent=yes,";

	if (formObj.popupwidth.value != "")
		onclick += "width=" + formObj.popupwidth.value + ",";

	if (formObj.popupheight.value != "")
		onclick += "height=" + formObj.popupheight.value + ",";

	if (formObj.popupleft.value != "") {
		if (formObj.popupleft.value != "c")
			onclick += "left=" + formObj.popupleft.value + ",";
		else
			onclick += "left='+(screen.availWidth/2-" + (formObj.popupwidth.value/2) + ")+',";
	}

	if (formObj.popuptop.value != "") {
		if (formObj.popuptop.value != "c")
			onclick += "top=" + formObj.popuptop.value + ",";
		else
			onclick += "top='+(screen.availHeight/2-" + (formObj.popupheight.value/2) + ")+',";
	}

	if (onclick.charAt(onclick.length-1) == ',')
		onclick = onclick.substring(0, onclick.length-1);

	onclick += "');";

	if (formObj.popupreturn.checked)
		onclick += "return false;";

	// tinyMCE.debug(onclick);

	formObj.onclick.value = onclick;
}

function setAttrib(elm, attrib, value) {
	var formObj = document.forms[0];
	var valueElm = formObj.elements[attrib.toLowerCase()];

	if (typeof(value) == "undefined" || value == null) {
		value = "";

		if (valueElm)
			value = valueElm.value;
	}

	if (value != "") {
		elm.setAttribute(attrib.toLowerCase(), value);

		if (attrib == "style")
			attrib = "style.cssText";

		if (attrib == "href")
			elm.setAttribute("mce_real_href", value);

		if (attrib == "class")
			attrib = "className";

		eval('elm.' + attrib + "=value;");
	} else
		elm.removeAttribute(attrib);
}

function renderAnchorList(id, target) {
	var inst = tinyMCE.getInstanceById(tinyMCE.getWindowArg('editor_id'));
	var nodes = inst.getBody().getElementsByTagName("a");

	var html = "";

	html += '<tr><td class="column1"><label for="' + id + '">Anchors</label></td><td>';
	html += '<select id="' + id + '" name="' + id + '" class="mceAnchorList" onchange="this.form.' + target + '.value=';
	html += 'this.options[this.selectedIndex].value;">';
	html += '<option value="">---</option>';

	for (var i=0; i<nodes.length; i++) {
		if ((name = tinyMCE.getAttrib(nodes[i], "name")) != "")
			html += '<option value="#' + name + '">' + name + '</option>';
	}

	html += '</select>';

	document.write(html);
}

function insertAction() {
	var inst = tinyMCE.getInstanceById(tinyMCE.getWindowArg('editor_id'));
	var elm = inst.getFocusElement();

	elm = tinyMCE.getParentElement(elm, "a");

	// Create new anchor elements
	if (elm == null) {
		if (tinyMCE.isSafari)
			tinyMCEPopup.execCommand("mceInsertContent", false, '<a href="#mce_temp_url#">' + inst.getSelectedHTML() + '</a>');
		else
			tinyMCEPopup.execCommand("createlink", false, "#mce_temp_url#");

		var elementArray = tinyMCE.getElementsByAttributeValue(inst.contentDocument.body, "a", "href", "#mce_temp_url#");
		for (var i=0; i<elementArray.length; i++)
			setAllAttribs(elementArray[i]);
	} else
		setAllAttribs(elm);

	tinyMCEPopup.close();
}

function setAllAttribs(elm) {
	var formObj = document.forms[0];
	var href = formObj.href.value;

	href = convertURL(href, elm);

	setAttrib(elm, 'href', href);
	setAttrib(elm, 'title');
	setAttrib(elm, 'target');
	setAttrib(elm, 'id');
	setAttrib(elm, 'style');
	setAttrib(elm, 'class');
	setAttrib(elm, 'rel');
	setAttrib(elm, 'rev');
	setAttrib(elm, 'charset');
	setAttrib(elm, 'hreflang');
	setAttrib(elm, 'dir');
	setAttrib(elm, 'lang');
	setAttrib(elm, 'tabindex');
	setAttrib(elm, 'accesskey');
	setAttrib(elm, 'type');
	setAttrib(elm, 'onfocus');
	setAttrib(elm, 'onblur');
	setAttrib(elm, 'onclick');
	setAttrib(elm, 'ondblclick');
	setAttrib(elm, 'onmousedown');
	setAttrib(elm, 'onmouseup');
	setAttrib(elm, 'onmouseover');
	setAttrib(elm, 'onmousemove');
	setAttrib(elm, 'onmouseout');
	setAttrib(elm, 'onkeypress');
	setAttrib(elm, 'onkeydown');
	setAttrib(elm, 'onkeyup');

	// Refresh tabindex and accesskey
//	if (tinyMCE.isMSIE)
//		elm.outerHTML = elm.outerHTML;
}

function getSelectValue(form_obj, field_name) {
	var elm = form_obj.elements[field_name];

	if (elm == null || elm.options == null)
		return "";

	return elm.options[elm.selectedIndex].value;
}

function renderLinkList(elm_id, target_form_element, onchange_func) {
	if (typeof(tinyMCELinkList) == "undefined" || tinyMCELinkList.length == 0)
		return;

	var html = "";

	html += '<tr><td class="column1"><label for="' + elm_id + '">{$lang_link_list}:</label></td>';
	html += '<td colspan="2"><select id="' + elm_id + '" name="' + elm_id + '"';
	html += ' class="mceLinkList" onchange="this.form.' + target_form_element + '.value=';
	html += 'this.options[this.selectedIndex].value;';

	if (typeof(onchange_func) != "undefined")
		html += onchange_func + '(\'' + target_form_element + '\',this.options[this.selectedIndex].text,this.options[this.selectedIndex].value);';

	html += '"><option value="">---</option>';

	for (var i=0; i<tinyMCELinkList.length; i++)
		html += '<option value="' + tinyMCELinkList[i][1] + '">' + tinyMCELinkList[i][0] + '</option>';

	html += '</select></td></tr>';

	document.write(html);

	// tinyMCE.debug('-- image list start --', html, '-- image list end --');
}

function renderTargetList(elm_id, target_form_element) {
	var targets = tinyMCE.getParam('theme_advanced_link_targets', '').split(';');
	var html = '';

	html += '<select id="' + elm_id + '" name="' + elm_id + '" onchange="this.form.' + target_form_element + '.value=';
	html += 'this.options[this.selectedIndex].value;">';

	html += '<option value="_self">' + tinyMCE.getLang('lang_insert_link_target_same') + '</option>';
	html += '<option value="_blank">' + tinyMCE.getLang('lang_insert_link_target_blank') + '</option>';
	html += '<option value="_parent">' + tinyMCE.getLang('lang_insert_link_target_parent') + '</option>';
	html += '<option value="_top">' + tinyMCE.getLang('lang_insert_link_target_top') + '</option>';

	for (var i=0; i<targets.length; i++) {
		var key, value;

		if (targets[i] == "")
			continue;

		key = targets[i].split('=')[0];
		value = targets[i].split('=')[1];

		html += '<option value="' + key + '">' + value + '</option>';
	}

	html += '</select>';

	document.write(html);
}

function renderClassesList(form_element_name) {
	var csses = tinyMCE.getCSSClasses(tinyMCE.getWindowArg('editor_id'));
	if (csses.length == 0)
		return;

	var html = "";

	html += '<tr><td class="column1"><label for="class">Class</label></td><td nowrap="nowrap">';
	html += '<select id="class" name="class" style="width: 150px" onchange="this.form.classes.value=';
	html += 'this.options[this.selectedIndex].value;">';
	html += '<option value="">' + tinyMCE.getLang("lang_not_set") + '</option>';

	for (var i=0; i<csses.length; i++)
		html += '<option value="' + csses[i] + '">' + csses[i] + '</option>';

	html += '</select>';

	document.write(html);
}

// While loading
preinit();
