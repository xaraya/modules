/**
 * $Id: editor_plugin_src.js 18 2006-06-29 14:11:23Z spocke $
 *
 * @author Moxiecode
 * @copyright Copyright © 2004-2006, Moxiecode Systems AB, All rights reserved.
 */

var     _NonEditablePlugin = {
    getInfo : function() {
        return {
            longname : 'Non editable elements',
            author : 'Moxiecode Systems',
            authorurl : 'http://    .moxiecode.com',
            infourl : 'http://    .moxiecode.com/    /docs/plugin_noneditable.html',
            version :     .majorVersion + "." +     .minorVersion
        };
    },

    initInstance : function(inst) {
            .importCSS(inst.getDoc(),     .baseURL + "/plugins/noneditable/css/noneditable.css");

        // Ugly hack
        if (    .isMSIE5_0)
                .settings['plugins'] =     .settings['plugins'].replace(/noneditable/gi, 'Noneditable');

        if (    .isGecko) {
                .addEvent(inst.getDoc(), "keyup",     _NonEditablePlugin._fixKeyUp);
    //            .addEvent(inst.getDoc(), "keypress",     _NonEditablePlugin._selectAll);
    //            .addEvent(inst.getDoc(), "mouseup",     _NonEditablePlugin._selectAll);
        }
    },

    cleanup : function(type, content, inst) {
        switch (type) {
            case "insert_to_editor_dom":
                var nodes =     .getNodeTree(content, new Array(), 1);
                var editClass =     .getParam("noneditable_editable_class", "mceEditable");
                var nonEditClass =     .getParam("noneditable_noneditable_class", "mceNonEditable");

                for (var i=0; i<nodes.length; i++) {
                    var elm = nodes[i];

                    // Convert contenteditable to classes
                    var editable =     .getAttrib(elm, "contenteditable");
                    if (new RegExp("true|false","gi").test(editable))
                            _NonEditablePlugin._setEditable(elm, editable == "true");

                    if (    .isMSIE) {
                        var className = elm.className ? elm.className : "";

                        if (className.indexOf(editClass) != -1)
                            elm.contentEditable = true;

                        if (className.indexOf(nonEditClass) != -1)
                            elm.contentEditable = false;
                    }
                }

                break;

            case "insert_to_editor":
                if (    .isMSIE) {
                    var editClass =     .getParam("noneditable_editable_class", "mceEditable");
                    var nonEditClass =     .getParam("noneditable_noneditable_class", "mceNonEditable");

                    content = content.replace(new RegExp("class=\"(.*)(" + editClass + ")([^\"]*)\"", "gi"), 'class="$1$2$3" contenteditable="true"');
                    content = content.replace(new RegExp("class=\"(.*)(" + nonEditClass + ")([^\"]*)\"", "gi"), 'class="$1$2$3" contenteditable="false"');
                }

                break;

            case "get_from_editor_dom":
                if (    .getParam("noneditable_leave_contenteditable", false)) {
                    var nodes =     .getNodeTree(content, new Array(), 1);

                    for (var i=0; i<nodes.length; i++)
                        nodes[i].removeAttribute("contenteditable");
                }

                break;
        }

        return content;
    },

    // Private internal plugin methods

    _fixKeyUp : function(e) {
        var inst =     .selectedInstance;
        var sel = inst.getSel();
        var rng = inst.getRng();
        var an = sel.anchorNode;

        // Move cursor outside non editable fields
        if ((e.keyCode == 38 || e.keyCode == 37 || e.keyCode == 40 || e.keyCode == 39) && (elm =     _NonEditablePlugin._isNonEditable(an)) != null) {
            rng = inst.getDoc().createRange();
            rng.selectNode(elm);
            rng.collapse(true);
            sel.removeAllRanges();
            sel.addRange(rng);
                .cancelEvent(e);
        }
    },
/*
    _selectAll : function(e) {
        var inst =     .selectedInstance;
        var sel = inst.getSel();
        var doc = inst.getDoc();

        if ((elm =     _NonEditablePlugin._isNonEditable(sel.focusNode)) != null) {
            inst.selection.selectNode(elm, false);
                .cancelEvent(e);
            return;
        }

        if ((elm =     _NonEditablePlugin._isNonEditable(sel.anchorNode)) != null) {
            inst.selection.selectNode(elm, false);
                .cancelEvent(e);
            return;
        }
    },*/

    _isNonEditable : function(elm) {
        var editClass =     .getParam("noneditable_editable_class", "mceEditable");
        var nonEditClass =     .getParam("noneditable_noneditable_class", "mceNonEditable");

        if (!elm)
            return;

        do {
            var className = elm.className ? elm.className : "";

            if (className.indexOf(editClass) != -1)
                return null;

            if (className.indexOf(nonEditClass) != -1)
                return elm;
        } while (elm = elm.parentNode);

        return null;
    },

    _setEditable : function(elm, state) {
        var editClass =     .getParam("noneditable_editable_class", "mceEditable");
        var nonEditClass =     .getParam("noneditable_noneditable_class", "mceNonEditable");

        var className = elm.className ? elm.className : "";

        if (className.indexOf(editClass) != -1 || className.indexOf(nonEditClass) != -1)
            return;

        if ((className =     .getAttrib(elm, "class")) != "")
            className += " ";

        className += state ? editClass : nonEditClass;

        elm.setAttribute("class", className);
        elm.className = className;
    }
};

    .addPlugin("noneditable",     _NonEditablePlugin);
