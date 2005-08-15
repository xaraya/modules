/* Functions for the advimage plugin popup */

var preloadImg = null;

function preinit() {
	// Initialize
	tinyMCE.setWindowArg('mce_windowresize', false);

	// Import external list url javascript
	var url = tinyMCE.getParam("external_image_list_url");
	if (url != null) {
		// Fix relative
		if (url.charAt(0) != '/')
			url = tinyMCE.documentBasePath + "/" + url;

		document.write('<sc'+'ript language="javascript" type="text/javascript" src="' + url + '"></sc'+'ript>');
	}
}

function convertURL(url, node, on_save) {
	return eval("tinyMCEPopup.windowOpener." + tinyMCE.settings['urlconverter_callback'] + "(url, node, on_save);");
}

function getImageSrc(str) {
	var pos = -1;

	if (!str)
		return "";

	if ((pos = str.indexOf('this.src=')) != -1) {
		var src = str.substring(pos + 10);

		src = src.substring(0, src.indexOf('\''));

		return src;
	}

	return "";
}

function init() {
	var formObj = document.forms[0];
	var inst = tinyMCE.getInstanceById(tinyMCE.getWindowArg('editor_id'));
	var elm = inst.getFocusElement();
	var action = "insert";

	// Resize some elements
	if (tinyMCE.getParam("file_browser_callback") != null) {
		document.getElementById('src').style.width = '260px';
		document.getElementById('onmouseoversrc').style.width = '260px';
		document.getElementById('onmouseoutsrc').style.width = '260px';
	}

	if (elm != null && elm.nodeName == "IMG")
		action = "update";

	formObj.insert.value = tinyMCE.getLang('lang_' + action, 'Insert', true); 

	if (action == "update") {
		var src = tinyMCE.getAttrib(elm, 'src');
		var onmouseoversrc = getImageSrc(tinyMCE.cleanupEventStr(tinyMCE.getAttrib(elm, 'onmouseover')));
		var onmouseoutsrc = getImageSrc(tinyMCE.cleanupEventStr(tinyMCE.getAttrib(elm, 'onmouseout')));

		// Fix for drag-drop/copy paste bug in Mozilla
		mceRealSrc = tinyMCE.getAttrib(elm, 'mce_real_src');
		if (mceRealSrc != "")
			src = mceRealSrc;

		src = convertURL(src, elm, true);

		if (onmouseoversrc != "")
			onmouseoversrc = convertURL(onmouseoversrc, elm, true);

		if (onmouseoutsrc != "")
			onmouseoutsrc = convertURL(onmouseoutsrc, elm, true);

		// Setup form data
		formObj.src.value    = src;
		formObj.alt.value    = tinyMCE.getAttrib(elm, 'alt');
		formObj.title.value  = tinyMCE.getAttrib(elm, 'title');
		formObj.border.value = tinyMCE.getAttrib(elm, 'border');
		formObj.vspace.value = tinyMCE.getAttrib(elm, 'vspace');
		formObj.hspace.value = tinyMCE.getAttrib(elm, 'hspace');
		formObj.width.value  = tinyMCE.getAttrib(elm, 'width');
		formObj.height.value = tinyMCE.getAttrib(elm, 'height');
		formObj.onmouseoversrc.value = onmouseoversrc;
		formObj.onmouseoutsrc.value  = onmouseoutsrc;
		formObj.id.value  = tinyMCE.getAttrib(elm, 'id');
		formObj.dir.value  = tinyMCE.getAttrib(elm, 'dir');
		formObj.lang.value  = tinyMCE.getAttrib(elm, 'lang');
		formObj.longdesc.value  = tinyMCE.getAttrib(elm, 'longdesc');
		formObj.usemap.value  = tinyMCE.getAttrib(elm, 'usemap');
		formObj.style.value  = elm.style.cssText.toLowerCase();

		// Select by the values
		selectByValue(formObj, 'align', tinyMCE.getAttrib(elm, 'align'));
		selectByValue(formObj, 'class', tinyMCE.getAttrib(elm, 'class'));
		selectByValue(formObj, 'imagelistsrc', src);
		selectByValue(formObj, 'imagelistover', onmouseoversrc);
		selectByValue(formObj, 'imagelistout', onmouseoutsrc);

		showPreviewImage(src);
		changeAppearance();

		window.focus();
	}

	// If option enabled default contrain proportions to checked
	if (tinyMCE.getParam("advimage_constrain_proportions", true))
		formObj.constrain.checked = true;

	// Check swap image if valid data
	if (formObj.onmouseoversrc.value != "" || formObj.onmouseoutsrc.value != "")
		setSwapImageDisabled(false);
	else
		setSwapImageDisabled(true);
}

function setSwapImageDisabled(state) {
	var formObj = document.forms[0];

	formObj.onmousemovecheck.checked = !state;

	if (state) {
		tinyMCE.switchClass(document.getElementById('overbrowser'),'mceButtonDisabled',true);
		tinyMCE.switchClass(document.getElementById('outbrowser'),'mceButtonDisabled',true);
	} else {
		tinyMCE.switchClass(document.getElementById('overbrowser'),'mceButtonNormal',false);
		tinyMCE.switchClass(document.getElementById('outbrowser'),'mceButtonNormal',false);
	}

	if (formObj.imagelistover)
		formObj.imagelistover.disabled = state;

	if (formObj.imagelistout)
		formObj.imagelistout.disabled = state;

	formObj.onmouseoversrc.disabled = state;
	formObj.onmouseoutsrc.disabled  = state;
}

function setAttrib(elm, attrib, value) {
	var formObj = document.forms[0];
	var valueElm = formObj.elements[attrib];

	if (typeof(value) == "undefined" || value == null) {
		value = "";

		if (valueElm)
			value = valueElm.value;
	}

	if (value != "") {
		elm.setAttribute(attrib, value);

		if (attrib == "style")
			attrib = "style.cssText";

		if (attrib == "longdesc")
			attrib = "longDesc";

		if (attrib == "width") {
			attrib = "style.width";
			value = value + "px";
		}

		if (attrib == "height") {
			attrib = "style.height";
			value = value + "px";
		}

		if (attrib == "class")
			attrib = "className";

		eval('elm.' + attrib + "=value;");
	} else
		elm.removeAttribute(attrib);
}

function makeAttrib(attrib, value) {
	var formObj = document.forms[0];
	var valueElm = formObj.elements[attrib];

	if (typeof(value) == "undefined" || value == null) {
		value = "";

		if (valueElm)
			value = valueElm.value;
	}

	if (value == "")
		return "";

	// XML encode it
	value = value.replace(/&/g, '&amp;');
	value = value.replace(/\"/g, '&quot;');
	value = value.replace(/</g, '&lt;');
	value = value.replace(/>/g, '&gr;');

	return ' ' + attrib + '="' + value + '"';
}

function insertAction() {
	var inst = tinyMCE.getInstanceById(tinyMCE.getWindowArg('editor_id'));
	var elm = inst.getFocusElement();
	var formObj = document.forms[0];
	var src = formObj.src.value;
	var onmouseoversrc = formObj.onmouseoversrc.value;
	var onmouseoutsrc = formObj.onmouseoutsrc.value;

	// Fix output URLs
	src = convertURL(src, tinyMCE.imgElement);

	if (onmouseoversrc && onmouseoversrc != "")
		onmouseoversrc = "this.src='" + convertURL(onmouseoversrc, tinyMCE.imgElement) + "';";

	if (onmouseoutsrc && onmouseoutsrc != "")
		onmouseoutsrc = "this.src='" + convertURL(onmouseoutsrc, tinyMCE.imgElement) + "';";

	if (elm != null && elm.nodeName == "IMG") {
		setAttrib(elm, 'src', src);
		setAttrib(elm, 'alt');
		setAttrib(elm, 'title');
		setAttrib(elm, 'border');
		setAttrib(elm, 'vspace');
		setAttrib(elm, 'hspace');
		setAttrib(elm, 'width');
		setAttrib(elm, 'height');
		setAttrib(elm, 'onmouseover', onmouseoversrc);
		setAttrib(elm, 'onmouseout', onmouseoutsrc);
		setAttrib(elm, 'id');
		setAttrib(elm, 'dir');
		setAttrib(elm, 'lang');
		setAttrib(elm, 'longdesc');
		setAttrib(elm, 'usemap');
		setAttrib(elm, 'style');
		setAttrib(elm, 'class', getSelectValue(formObj, 'class'));
		setAttrib(elm, 'align', getSelectValue(formObj, 'align'));
		tinyMCEPopup.execCommand("mceRepaint");
	} else {
		var html = "<img";

		html += makeAttrib('src', src);
		html += makeAttrib('alt');
		html += makeAttrib('title');
		html += makeAttrib('border');
		html += makeAttrib('vspace');
		html += makeAttrib('hspace');
		html += makeAttrib('width');
		html += makeAttrib('height');
		html += makeAttrib('onmouseover', onmouseoutsrc);
		html += makeAttrib('onmouseout', onmouseoutsrc);
		html += makeAttrib('id');
		html += makeAttrib('dir');
		html += makeAttrib('lang');
		html += makeAttrib('longdesc');
		html += makeAttrib('usemap');
		html += makeAttrib('style');
		html += makeAttrib('class', getSelectValue(formObj, 'class'));
		html += makeAttrib('align', getSelectValue(formObj, 'align'));
		html += " />";

		tinyMCEPopup.execCommand("mceInsertContent", false, html);
	}

	tinyMCEPopup.close();
}

function cancelAction() {
	tinyMCEPopup.close();
}

function changeAppearance() {
	var formObj = document.forms[0];
	var img = document.getElementById('alignSampleImg');

	if (img) {
		img.align = formObj.align.value;
		img.border = formObj.border.value;
		img.hspace = formObj.hspace.value;
		img.vspace = formObj.vspace.value;
	}
}

function changeMouseMove() {
	var formObj = document.forms[0];

	setSwapImageDisabled(!formObj.onmousemovecheck.checked);
}

function changeHeight() {
	var formObj = document.forms[0];

	if (!formObj.constrain.checked || !preloadImg)
		return;

	var temp = (formObj.width.value / preloadImg.width) * preloadImg.height;
	formObj.height.value = temp.toFixed(0);
}

function changeWidth() {
	var formObj = document.forms[0];

	if (!formObj.constrain.checked || !preloadImg)
		return;

	var temp = (formObj.height.value / preloadImg.height) * preloadImg.width;
	formObj.width.value = temp.toFixed(0);
}

function onSelectMainImage(target_form_element, name, value) {
	var formObj = document.forms[0];

	formObj.alt.value = name;
	formObj.title.value = name;

	resetImageData();
	showPreviewImage(formObj.elements[target_form_element].value);
}

function showPreviewImage(src) {
	selectByValue(document.forms[0], 'imagelistsrc', src);

	var elm = document.getElementById('prev');
	var src = src == "" ? src : tinyMCE.convertRelativeToAbsoluteURL(tinyMCE.settings['base_href'], src);

	if (src == "")
		elm.innerHTML = "";
	else
		elm.innerHTML = '<img src="' + src + '" border="0" />'

	getImageData(src);
}

function getImageData(src) {
	preloadImg = new Image();

	tinyMCE.addEvent(preloadImg, "load", updateImageData);
	tinyMCE.addEvent(preloadImg, "error", resetImageData);

	preloadImg.src = src;
}

function updateImageData() {
	var formObj = document.forms[0];

	if (formObj.width.value == "")
		formObj.width.value = preloadImg.width;

	if (formObj.height.value == "")
		formObj.height.value = preloadImg.height;
}

function resetImageData() {
	var formObj = document.forms[0];
	formObj.width.value = formObj.height.value = "";	
}

function selectByValue(form_obj, field_name, value) {
	if (!form_obj || !form_obj.elements[field_name])
		return;

	for (var i=0; i<form_obj.elements[field_name].options.length; i++) {
		var option = form_obj.elements[field_name].options[i];

		if (option.value == value)
			option.selected = true;
		else
			option.selected = false;
	}

	return false;
}

function getSelectValue(form_obj, field_name) {
	var elm = form_obj.elements[field_name];

	if (elm == null || elm.options == null)
		return "";

	return elm.options[elm.selectedIndex].value;
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

function renderLinkBrowser(id, target_form_element) {
	if (tinyMCE.getParam("file_browser_callback") == null)
		return;

	var html = "";

	html += '<img id="' + id + '" src="../../themes/advanced/images/browse.gif"';
	html += ' onmouseover="tinyMCE.switchClass(this,\'mceButtonOver\');"';
	html += ' onmouseout="tinyMCE.restoreClass(this);"';
	html += ' onmousedown="tinyMCE.restoreAndSwitchClass(this,\'mceButtonDown\');"';
	html += ' onclick="openLinkBrower(this, \'' + target_form_element + '\');"';
	html += ' width="20" height="18" border="0" title="' + tinyMCE.getLang('lang_browse') + '"';
	html += ' class="mceButtonNormal" alt="' + tinyMCE.getLang('lang_browse') + '" />';

	document.write(html);
}

function openLinkBrower(img, target_form_element) {
	if (img.className != "mceButtonDisabled")
		tinyMCE.openFileBrowser(target_form_element, document.forms[0].elements[target_form_element].value, 'file', window);
}

function renderImageList(elm_id, target_form_element, onchange_func) {
	if (typeof(tinyMCEImageList) == "undefined" || tinyMCEImageList.length == 0)
		return;

	var html = "";

	html += '<tr><td class="column1"><label for="' + elm_id + '">{$lang_image_list}:</label></td>';
	html += '<td colspan="2"><select id="' + elm_id + '" name="' + elm_id + '"';
	html += ' class="mceImageList" onchange="this.form.' + target_form_element + '.value=';
	html += 'this.options[this.selectedIndex].value;';

	if (typeof(onchange_func) != "undefined")
		html += onchange_func + '(\'' + target_form_element + '\',this.options[this.selectedIndex].text,this.options[this.selectedIndex].value);';

	html += '"><option value="">---</option>';

	for (var i=0; i<tinyMCEImageList.length; i++)
		html += '<option value="' + tinyMCEImageList[i][1] + '">' + tinyMCEImageList[i][0] + '</option>';

	html += '</select></td></tr>';

	document.write(html);

	// tinyMCE.debug('-- image list start --', html, '-- image list end --');
}

function renderClassesList(form_element_name) {
	var csses = tinyMCE.getCSSClasses(tinyMCE.getWindowArg('editor_id'));

	var html = "";

	html += '<tr><td class="column1"><label for="class">Class</label></td><td nowrap="nowrap">';
	html += '<select id="class" name="class" style="width: 150px">';
	html += '<option value="">' + tinyMCE.getLang("lang_not_set") + '</option>';

	for (var i=0; i<csses.length; i++)
		html += '<option value="' + csses[i] + '">' + csses[i] + '</option>';

	html += '</select>';

	document.write(html);
}

// While loading
preinit();
