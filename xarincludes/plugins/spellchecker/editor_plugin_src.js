/**
 * $RCSfile: editor_plugin_src.js,v $
 * $Revision: 1.1 $
 * $Date: 2006/03/03 16:10:54 $
 *
 * @author Moxiecode
 * @copyright Copyright © 2004-2006, Moxiecode Systems AB, All rights reserved.
 */

tinyMCE.importPluginLanguagePack('spellchecker', 'en');

// Plucin static class
var TinyMCE_SpellCheckerPlugin = {
	_contextMenu : new TinyMCE_Layer('mceSpellcheckerMenu'),
	_menuItems : new Array(),

	getInfo : function() {
		return {
			longname : 'Spellchecker',
			author : 'Moxiecode Systems AB',
			authorurl : 'http://tinymce.moxiecode.com',
			infourl : 'http://tinymce.moxiecode.com/tinymce/docs/plugin_spellchecker.html',
			version : tinyMCE.majorVersion + "." + tinyMCE.minorVersion
		};
	},

	handleEvent : function(e) {
		var elm = tinyMCE.isMSIE ? e.srcElement : e.target;
		var inst = tinyMCE.selectedInstance;
		var self = TinyMCE_SpellCheckerPlugin;
		var p, p2, x, y, sx, sy, h;

		if (e.type == "blur" || e.type == "click")
			self._contextMenu.hide();

		// Handle click on word
		if ((e.type == "click" || e.type == "contextmenu") && elm) {
			do {
				if (tinyMCE.getAttrib(elm, 'class') == "mceItemHiddenSpellWord") {
					p = tinyMCE.getAbsPosition(inst.iframeElement);
					p2 = tinyMCE.getAbsPosition(elm);
					h = parseInt(elm.offsetHeight);
					sx = inst.getBody().scrollLeft;
					sy = inst.getBody().scrollTop;
					x = p.absLeft + p2.absLeft - sx;
					y = p.absTop + p2.absTop - sy + h;

					// DO AJAX HERE
					self._buildMenu(new Array('arne', 'pelle', 'stina'));
					self._contextMenu.moveTo(x, y);
					self._contextMenu.show();

					inst.selection.selectNode(elm, false, false);
					inst.spellCheckerElm = elm;

					tinyMCE.cancelEvent(e);
					return false;
				}
			} while ((elm = elm.parentNode));
		}

		// Block events
		if (inst && tinyMCE.getParam("spellchecker_read_only") && inst.spellcheckerOn && (e.type == "keydown" || e.type == "keypress")) {
			tinyMCE.cancelEvent(e);
			return false;
		}

		return true;
	},

	initInstance : function(inst) {
		var d = document, e = d.getElementById('mceSpellcheckerMenu');

		tinyMCE.importCSS(inst.getDoc(), tinyMCE.baseURL + "/plugins/spellchecker/css/content.css");

		if (!e) {
			tinyMCE.importCSS(d, tinyMCE.baseURL + "/plugins/spellchecker/css/spellchecker.css");
			e = d.createElement('div');
			e.setAttribute('id','mceSpellcheckerMenu');
			d.body.appendChild(e);
		}
	},

	setupContent : function(editor_id, body, doc) {
		TinyMCE_SpellCheckerPlugin._removeWords(doc);
	},

	getControlHTML : function(cn) {
		switch (cn) {
			case "spellchecker":
				return tinyMCE.getButtonHTML(cn, 'lang_spellchecker_desc', '{$pluginurl}/images/spellchecker.gif', 'mceSpellCheck');
		}

		return "";
	},

	execCommand : function(editor_id, element, command, user_interface, value) {
		var inst, b, self = TinyMCE_SpellCheckerPlugin;

		inst = tinyMCE.getInstanceById(editor_id);

		// Handle commands
		switch (command) {
			case "mceSpellCheck":
				if (!inst.spellcheckerOn) {
					b = inst.selection.getBookmark();
					self._markWords(inst.getDoc(), inst.getBody(), new Array('has', 'some', 'is'));
					inst.selection.moveToBookmark(b);
					inst.spellcheckerOn = true;
					tinyMCE.switchClass(editor_id + '_spellchecker', 'mceButtonSelected');
				} else {
					self._removeWords(inst.getDoc());
					inst.spellcheckerOn = false;
					tinyMCE.switchClass(editor_id + '_spellchecker', 'mceButtonNormal');
				}

				self._contextMenu.hide();

				return true;

			case "mceSpellCheckReplace":
				if (inst.spellCheckerElm)
					tinyMCE.setOuterHTML(inst.spellCheckerElm, value);

				self._contextMenu.hide();

				return true;
		}

		// While spellchecking, block all other commands
		if (inst.spellcheckerOn && tinyMCE.getParam("spellchecker_read_only")) {
			if (!new RegExp('mceStartTyping|mceEndTyping|mceBeginUndoLevel|mceEndUndoLevel|mceAddUndoLevel', 'gi').test(command))
				alert("This command is disabled while spellchecking.");

			return true;
		}

		// Pass to next handler in chain
		return false;
	},

	_removeWords : function(doc) {
		var i, c, nl = doc.getElementsByTagName("span");

		for (i=nl.length-1; i>=0; i--) {
			c = tinyMCE.getAttrib(nl[i], 'class');

			if (c == 'mceItemHiddenSpellWord' || c == 'mceItemHidden')
				tinyMCE.setOuterHTML(nl[i], nl[i].innerHTML);
		}
	},

	_markWords : function(doc, n, wl) {
		var i, nv, nn, nl = tinyMCE.getNodeTree(n, new Array(), 3);
		var r1, r2, r3, r4, w = '';

		for (i=0; i<wl.length; i++)
			w += wl[i] + ((i == wl.length-1) ? '' : '|');

		r1 = new RegExp('^(' + w + ')', 'gi');
		r2 = new RegExp('(' + w + ')([\\.!\\?]?)$', 'gi');
		r3 = new RegExp('^(' + w + ')([\\.!\\?]?)$', 'gi');
		r4 = new RegExp('(["\\s\\(])(' + w + ')(["\\s\\)])', 'gi');

		for (i=0; i<nl.length-1; i++) {
			nv = nl[i].nodeValue;
			if (r1.test(nv) || r2.test(nv) || r3.test(nv) || r4.test(nv)) {
				nv = nv.replace(r1, '<span class="mceItemHiddenSpellWord">$1</span>');
				nv = nv.replace(r2, '<span class="mceItemHiddenSpellWord">$1</span>$2');
				nv = nv.replace(r3, '<span class="mceItemHiddenSpellWord">$1</span>$2');
				nv = nv.replace(r4, '$1<span class="mceItemHiddenSpellWord">$2</span>$3');

				nn = doc.createElement('span');
				nn.className = "mceItemHidden";
				nn.innerHTML = nv;

				// Remove old text node
				nl[i].parentNode.replaceChild(nn, nl[i]);
			}
		}
	},

	cleanup : function(type, content, inst) {
		switch (type) {
			case "get_from_editor_dom":
				TinyMCE_SpellCheckerPlugin._removeWords(content);
				inst.spellcheckerOn = false;
				break;
		}

		return content;
	},

	_buildMenu : function(sg) {
		var i, self = TinyMCE_SpellCheckerPlugin;

		self._menuItems = new Array();
		self._menuItems[self._menuItems.length] = {text : 'Suggestions'};

		for (i=0; i<sg.length; i++)
			self._menuItems[self._menuItems.length] = {text : sg[i], js : 'tinyMCE.execCommand("mceSpellCheckReplace",false,"' + sg[i] + '");'};

		self._menuItems[self._menuItems.length] = {separator : true};
		self._menuItems[self._menuItems.length] = {text : 'Add to word list', js : 'alert(\'ERROR!\');'};

		self._generateMenu();
	},

	_generateMenu : function() {
		var i, h = '', m = TinyMCE_SpellCheckerPlugin._menuItems;
		var e = TinyMCE_SpellCheckerPlugin._contextMenu.getElement();

		h += '<table border="0" cellpadding="0" cellspacing="0">';

		for (i=0; i<m.length; i++) {
			if (m[i].separator)
				h += '<tr class="mceSeparatorRow"><td></td></tr>';
			else if (!m[i].js)
				h += '<tr><td class="mceTitleRow"><span>' + tinyMCE.xmlEncode(m[i].text) + '</span></td></tr>';
			else
				h += '<tr><td><a href="javascript:void(0);" onmousedown="' + tinyMCE.xmlEncode(m[i].js) + ';return false;"><span>' + tinyMCE.xmlEncode(m[i].text) + '</span></a></td></tr>';
		}

		h += '</table>';

		e.innerHTML = h;
	}
};

// Register plugin
tinyMCE.addPlugin('spellchecker', TinyMCE_SpellCheckerPlugin);
