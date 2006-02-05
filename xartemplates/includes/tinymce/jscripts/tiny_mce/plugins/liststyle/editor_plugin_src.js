/* Copyright 2005-2006 PolicyPoint Technologies Pty. Ltd. */
/* License: LGPL */

/* Import plugin specific language pack */
tinyMCE.importPluginLanguagePack('liststyle', 'en,sv');

function TinyMCE_liststyle_getInfo() {
	return {
		longname : 'List style',
		author : 'Scott Eade - PolicyPoint Technologies Pty. Ltd.',
		authorurl : 'http://policypoint.net',
		infourl : 'http://policypoint.net/tinymce/docs/plugin_liststyle.html',
		version : '1.0.2'
	};
};

function TinyMCE_liststyle_getControlHTML(control_name) {
	switch (control_name) {
		case 'liststyle':
			return '<a href="javascript:tinyMCE.execInstanceCommand(\'{$editor_id}\',\'mceListStyle\');" target="_self" onmousedown="return false;"><img id="{$editor_id}_liststyle" src="{$pluginurl}/images/liststyle.gif" title="{$lang_liststyle_desc}" width="20" height="20" class="mceButtonNormal" onmouseover="tinyMCE.switchClass(this,\'mceButtonOver\');" onmouseout="tinyMCE.restoreClass(this);" onmousedown="tinyMCE.restoreAndSwitchClass(this,\'mceButtonDown\');" /></a>';
	}
	return '';
}

/**
 * Executes the mceListStyle command.
 */
function TinyMCE_liststyle_execCommand(editor_id, element, command, user_interface, value) {
	// Handle commands
	switch (command) {
		case 'mceListStyle':
			var template = new Array();

			template['file']   = '../../plugins/liststyle/liststyle.htm'; // Relative to theme
			template['width']  = 300;
			template['height'] = 230;
			var listStyleType = '', list = '';
			var inst = tinyMCE.getInstanceById(editor_id);
			var selectedElement = inst.getFocusElement();
			while (selectedElement != null && selectedElement.nodeName != 'LI')
				selectedElement = selectedElement.parentNode;
			if (selectedElement != null) {
				var listElement = tinyMCE.getParentElement(selectedElement, 'ol,ul');
				if (listElement != null) {
					list = listElement.nodeName.toLowerCase();
					listStyleType = listElement.style.listStyleType ? listElement.style.listStyleType : list == 'ol' ? 'decimal' : 'disc';
					//alert('listStyleType = ' + listStyleType);
				}
				tinyMCE.openWindow(template, {editor_id : editor_id, listStyleType : listStyleType, list : list, mceDo : 'update'});
			}

		return true;
	}
	// Pass to next handler in chain
	return false;
}

function TinyMCE_liststyle_handleNodeChange(editor_id, node, undo_index, undo_levels, visual_aid, any_selection) {
	tinyMCE.switchClassSticky(editor_id + '_liststyle', 'mceButtonDisabled');

	if (node == null)
		return;

	do {
		if (node.nodeName == 'LI')
			tinyMCE.switchClassSticky(editor_id + '_liststyle', 'mceButtonNormal');
	} while ((node = node.parentNode));

	return true;
}