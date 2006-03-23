/**
 * $Id$
 *
 * @author Scott Eade
 * @copyright Copyright 2005-2006, PolicyPoint Technologies Pty. Ltd.
 */

/* Import plugin specific language pack */ 
tinyMCE.importPluginLanguagePack('liststyle', 'en,sv');

var TinyMCE_ListStylePlugin = {
	getInfo : function() {
		return {
			longname : 'List style',
			author : 'Scott Eade - PolicyPoint Technologies Pty. Ltd.',
			authorurl : 'http://policypoint.net',
			infourl : 'http://policypoint.net/tinymce/docs/plugin_liststyle.html',
			version : '1.1.0'
		};
	},

	initInstance : function(inst) {
	},

	getControlHTML : function(cn) {
		switch (cn) {
			case 'liststyle':
				//return '<a href="javascript:tinyMCE.execInstanceCommand(\'{$editor_id}\',\'mceListStyle\');" target="_self" onmousedown="return false;"><img id="{$editor_id}_liststyle" src="{$pluginurl}/images/liststyle.gif" title="{$lang_liststyle_desc}" width="20" height="20" class="mceButtonNormal" onmouseover="tinyMCE.switchClass(this,\'mceButtonOver\');" onmouseout="tinyMCE.restoreClass(this);" onmousedown="tinyMCE.restoreAndSwitchClass(this,\'mceButtonDown\');" /></a>';
				return tinyMCE.getButtonHTML(cn, 'lang_liststyle_desc', '{$pluginurl}/images/liststyle.gif', 'mceListStyle', true);
		}
		return '';
	},

	execCommand : function(editor_id, element, command, user_interface, value) {
		// Handle commands
		switch (command) {
			case 'mceListStyle':
				var template = new Array();
				template['file']   = '../../plugins/liststyle/liststyle.htm';
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
	},

	handleNodeChange : function(editor_id, node, undo_index, undo_levels, visual_aid, any_selection) {
		//tinyMCE.switchClassSticky(editor_id + '_liststyle', 'mceButtonDisabled');
		tinyMCE.switchClass(editor_id + '_liststyle', 'mceButtonNormal');
		if (node == null)
			return;
		do {
			if (node.nodeName == 'LI') {
				//tinyMCE.switchClassSticky(editor_id + '_liststyle', 'mceButtonNormal');
				tinyMCE.switchClass(editor_id + '_liststyle', 'mceButtonSelected');
				// Stop once LI is found.
				return;
			}
		} while ((node = node.parentNode));
	},

};

tinyMCE.addPlugin('liststyle', TinyMCE_ListStylePlugin);
