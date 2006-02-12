/* Import theme specific language pack */
tinyMCE.importPluginLanguagePack('xarpagebreak', 'en,el');

/**
 * Inserts xaraya page break tag.
 */
function TinyMCE_xarpagebreak_getControlHTML(control_name) {
    switch (control_name) {
        case "xarpagebreak":
            return '<img id="{$editor_id}_xarpagebreak" src="{$pluginurl}/images/xarpagebreak.gif" title="{$lang_xarpagebreak_desc}" width="20" height="20" class="mceButtonNormal" onmouseover="tinyMCE.switchClass(this,\'mceButtonOver\');" onmouseout="tinyMCE.restoreClass(this);" onmousedown="tinyMCE.restoreAndSwitchClass(this,\'mceButtonDown\');" onclick="tinyMCE.execInstanceCommand(\'{$editor_id}\',\'mceXarPageBreak\');">';
    }
    return "";
}

/**
 * Executes the mceXarPageBreak command.
 */
function TinyMCE_xarpagebreak_execCommand(editor_id, element, command, user_interface, value) {
    // Handle commands
    switch (command) {
        case "mceXarPageBreak":
            tinyMCE.execCommand('mceInsertContent',false,xarpagebreakImgTag());
            return true;
    }
    // Pass to next handler in chain
    return false;
}

function TinyMCE_xarpagebreak_cleanup(type, content) {
    // Handle custom cleanup
    switch (type) {
        // Called when editor is filled with content
        case "insert_to_editor":
            content = content.replace(new RegExp('<!--pagebreak-->','gi'),xarpagebreakImgTag());
            break;
        // Called when editor is pass out content
        case "get_from_editor":
            content = restoreTag(content);
            break;
    }
    // Pass through to next handler in chain
    return content;
}

function restoreTag(content) {
    // Restore the pagebreak tag
    content = content.replace(new RegExp('<[ ]*img','gi'),'<img');

    var newContent = "";
    var startString;
    var stImgIndex;
    var endImgIndex;
    var imgString;
    var stIndex = 0;
    while( (stImgIndex = content.indexOf('<img',stIndex)) != -1 )
    {
        startString = content.substring(stIndex,stImgIndex);
        newContent = newContent.concat(startString);
        endImgIndex = content.indexOf('/>',stImgIndex);
        if ( endImgIndex == -1 )
            break;

        stIndex = endImgIndex+"/>".length;
        imgString = content.substring(stImgIndex,stIndex);

        if ( imgString.indexOf('mce_plugin_xarpagebreak') > -1 )
        {
            newContent = newContent.concat('<!--pagebreak-->');
        }
        else
        {
            newContent = newContent.concat(imgString);
        }
    }
    newContent = newContent.concat(content.substring(stIndex,content.length));
    return newContent;
}


function xarpagebreakImgTag() {
    // Return a custom img tag
    return '<img title="Page Break" alt="mce_plugin_xarpagebreak" style="display: block;" '+'src="' + tinyMCE.baseURL + '/plugins/xarpagebreak/images/xarpagebreakimg.gif" />';
}
