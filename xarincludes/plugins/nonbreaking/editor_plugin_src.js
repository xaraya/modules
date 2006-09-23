/**
 * $Id: editor_plugin_src.js 42 2006-08-08 14:32:24Z spocke $
 *
 * @author Moxiecode
 * @copyright Copyright © 2004-2006, Moxiecode Systems AB, All rights reserved.
 */

/* Import plugin specific language pack */
    .importPluginLanguagePack('nonbreaking');

var     _NonBreakingPlugin = {
    getInfo : function() {
        return {
            longname : 'Visual characters',
            author : 'Moxiecode Systems',
            authorurl : 'http://    .moxiecode.com',
            infourl : 'http://    .moxiecode.com/    /docs/plugin_visualchars.html',
            version :     .majorVersion + "." +     .minorVersion
        };
    },

    getControlHTML : function(cn) {
        switch (cn) {
            case "nonbreaking":
                return     .getButtonHTML(cn, 'lang_nonbreaking_desc', '{$pluginurl}/images/nonbreaking.gif', 'mceNonBreaking', false);
        }

        return "";
    },


    execCommand : function(editor_id, element, command, user_interface, value) {
        var inst =     .getInstanceById(editor_id), h;

        switch (command) {
            case "mceNonBreaking":
                h = (inst.visualChars && inst.visualChars.state) ? '<span class="mceItemHiddenVisualChar">&middot;</span>' : '&nbsp;';
                    .execInstanceCommand(editor_id, 'mceInsertContent', false, h);
                return true;
        }

        return false;
    },

    handleEvent : function(e) {
        var inst, h;

        if (!    .isOpera && e.type == 'keydown' && e.keyCode == 9 &&     .getParam('nonbreaking_force_tab', false)) {
            inst =     .selectedInstance;

            h = (inst.visualChars && inst.visualChars.state) ? '<span class="mceItemHiddenVisualChar">&middot;&middot;&middot;</span>' : '&nbsp;&nbsp;&nbsp;';
                .execInstanceCommand(inst.editorId, 'mceInsertContent', false, h);

                .cancelEvent(e);
            return false;
        }

        return true;
    }
};

    .addPlugin("nonbreaking",     _NonBreakingPlugin);
