/* Import plugin specific language pack */
tinyMCE.importPluginLanguagePack('lorem', 'en,hu');

function TinyMCE_lorem_getControlHTML(control_name) {
    switch (control_name) {
        case "lorem":
            return '<img id="{$editor_id}_lorem" src="{$pluginurl}/images/lorem.gif" title="{$lang_insert_lorem_desc}" width="20" height="20" class="mceButtonNormal" onmouseover="tinyMCE.switchClass(this,\'mceButtonOver\');" onmouseout="tinyMCE.restoreClass(this);" onmousedown="tinyMCE.restoreAndSwitchClass(this,\'mceButtonDown\');" onclick="tinyMCE.execInstanceCommand(\'{$editor_id}\',\'mceLorem\');" />';
    }
    return "";
}

/**
 * Executes the lorem command.
 */
function TinyMCE_lorem_execCommand(editor_id, element, command, user_interface, value) {
    // Handle commands
    switch (command) {
        case "mceLorem":
            var template = new Array();
            template['file']   = '../../plugins/lorem/loremipsum.htm'; // Relative to theme
            template['width']  = 300;
            template['height'] = 200;            
           
            tinyMCE.openWindow(template, {editor_id : editor_id});              
       return true;
   }
   // Pass to next handler in chain
   return false;
}