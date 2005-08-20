var url = tinyMCE.getParam("flash_external_list_url");
if (url != null) {
	// Fix relative
	if (url.charAt(0) != '/')
		url = tinyMCE.documentBasePath + "/" + url;
}

document.write('<sc'+'ript language="javascript" type="text/javascript" src="' + url + '"></sc'+'ript>');

function init() {
	var formObj = document.forms[0];
	var swffile   = tinyMCE.getWindowArg('swffile');
	var swfwidth  = '' + tinyMCE.getWindowArg('swfwidth');
	var swfheight = '' + tinyMCE.getWindowArg('swfheight');

	if (swfwidth.indexOf('%')!=-1) {
		formObj.width2.value = "%";
		formObj.width.value  = swfwidth.substring(0,swfwidth.length-1);
	} else {
		formObj.width2.value = "px";
		formObj.width.value  = swfwidth;
	}

	if (swfheight.indexOf('%')!=-1) {
		formObj.height2.value = "%";
		formObj.height.value  = swfheight.substring(0,swfheight.length-1);
	} else {
		formObj.height2.value = "px";
		formObj.height.value  = swfheight;
	}

	formObj.file.value = swffile;
	formObj.insert.value = tinyMCE.getLang('lang_' + tinyMCE.getWindowArg('action'), 'Insert', true);

	// Handle file browser
	if (tinyMCE.getParam("file_browser_callback") != null) {
		document.getElementById('file').style.width = '230px';

		var html = '';

		html += '<img id="browserBtn" src="../../themes/advanced/images/browse.gif"';
		html += ' onmouseover="tinyMCE.switchClass(this,\'mceButtonOver\');"';
		html += ' onmouseout="tinyMCE.restoreClass(this);"';
		html += ' onmousedown="tinyMCE.restoreAndSwitchClass(this,\'mceButtonDown\');"';
		html += ' onclick="javascript:tinyMCE.openFileBrowser(\'file\',document.forms[0].file.value,\'flash\',window);"';
		html += ' width="20" height="18" border="0" title="' + tinyMCE.getLang('lang_browse') + '"';
		html += ' class="mceButtonNormal" alt="' + tinyMCE.getLang('lang_browse') + '" />';

		document.getElementById('browser').innerHTML = html;
	}

	// Auto select flash in list
	if (typeof(tinyMCEFlashList) != "undefined" && tinyMCEFlashList.length > 0) {
		for (var i=0; i<formObj.link_list.length; i++) {
			if (formObj.link_list.options[i].value == tinyMCE.getWindowArg('swffile'))
				formObj.link_list.options[i].selected = true;
		}
	}
}

function insertFlash() {
	var formObj = document.forms[0];
	var html      = '';
	var file      = formObj.file.value;
	var width     = formObj.width.value;
	var height    = formObj.height.value;
	if (formObj.width2.value=='%') {
		width = width + '%';
	}
	if (formObj.height2.value=='%') {
		height = height + '%';
	}

	if (width == "")
		width = 100;

	if (height == "")
		height = 100;

	html += ''
		+ '<img src="' + (tinyMCE.getParam("theme_href") + "/images/spacer.gif") + '" '
		+ 'width="' + width + '" height="' + height + '" '
		+ 'border="0" alt="' + file + '" title="' + file + '" class="mceItemFlash" />';

	tinyMCEPopup.execCommand("mceInsertContent", true, html);
	tinyMCE.selectedInstance.repaint();

	tinyMCEPopup.close();
}
