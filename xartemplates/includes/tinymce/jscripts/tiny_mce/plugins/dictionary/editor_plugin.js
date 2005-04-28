
/**
 * Dictionary plugin for TinyMCE by http://mudbomb.com
 */
function TinyMCE_dictionary_getControlHTML(control_name) {
    switch (control_name) {
        case "dictionary":
            return '<img id="{$editor_id}_dictionary" src="{$pluginurl}/images/dictionary.gif" title="Dictionary" width="20" height="20" class="mceButtonNormal" onmouseover="tinyMCE.switchClass(this,\'mceButtonOver\');" onmouseout="tinyMCE.restoreClass(this);" onmousedown="tinyMCE.restoreAndSwitchClass(this,\'mceButtonDown\');" onclick="tinyMCE.execInstanceCommand(\'{$editor_id}\',\'mcedictionary\');">';
    }

    return "";
}

/**
 * Executes the mcedictionary command.
 */
function TinyMCE_dictionary_execCommand(editor_id, element, command, user_interface, value) {
    // Handle commands
    switch (command) {
        case "mcedictionary":
            var template = new Array();

            template['file'] = '../../plugins/dictionary/dictionary.htm'; // Relative to theme
            template['width'] = 340;
            template['height'] = 100;

            tinyMCE.openWindow(template, {editor_id : editor_id});

            return true;
    }

    // Pass to next handler in chain
    return false;
}
