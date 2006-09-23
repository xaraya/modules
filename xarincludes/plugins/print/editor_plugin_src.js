/**
 * $Id: editor_plugin_src.js 42 2006-08-08 14:32:24Z spocke $
 *
 * @author Moxiecode
 * @copyright Copyright © 2004-2006, Moxiecode Systems AB, All rights reserved.
 */

/* Import theme    specific language pack */
    .importPluginLanguagePack('print');

var     _PrintPlugin = {
    getInfo : function() {
        return {
            longname : 'Print',
            author : 'Moxiecode Systems',
            authorurl : 'http://    .moxiecode.com',
            infourl : 'http://    .moxiecode.com/    /docs/plugin_print.html',
            version :     .majorVersion + "." +     .minorVersion
        };
    },

    getControlHTML : function(cn)    {
        switch (cn) {
            case "print":
                return     .getButtonHTML(cn, 'lang_print_desc', '{$pluginurl}/images/print.gif', 'mcePrint');
        }

        return "";
    },

    /**
     * Executes    the    search/replace commands.
     */
    execCommand : function(editor_id, element, command,    user_interface,    value) {
        // Handle commands
        switch (command) {
            case "mcePrint":
                    .getInstanceById(editor_id).contentWindow.print();
                return true;
        }

        // Pass to next handler in chain
        return false;
    }
};

    .addPlugin("print",     _PrintPlugin);
