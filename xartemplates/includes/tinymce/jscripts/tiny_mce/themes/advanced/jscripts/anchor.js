function init() {
	document.forms[0].anchorName.value = tinyMCE.getWindowArg('name');
	document.forms[0].insert.value = tinyMCE.getLang('lang_' + tinyMCE.getWindowArg('action'), 'Insert', true);
}

function insertAnchor() {
	tinyMCEPopup.execCommand('mceAnchor', false, document.forms[0].anchorName.value);
	tinyMCEPopup.close();
}

function cancelAction() {
	tinyMCEPopup.close();
}
