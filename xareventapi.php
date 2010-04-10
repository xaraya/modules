<?php
/**
 * xarTinyMCE eventapi
 *
 * @package modules
 * @copyright (C) 2004-2010 2skies.com
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html} 
 * @link http://xarigami.com/project/xartinymce
 *
 * @subpackage xartinymce module
 * @author Jo Dalle Nogare <icedlava@2skies.com>
 */

/**
 * Tinymce event handler for the system event ServerRequest
 *
 * This function is called when the system triggers the
 * event in index.php on each Server Request
 * In this case it loads tinymce javascript on every page load
 * if it is active and set as the default wysiwyg editor and mode is automatic
 *
 * @author Jo Dalle Nogare <jo@2skies.com>
 * @returns bool
 */
function tinymce_eventapi_OnServerRequest()
{
    if (xarModGetVar('tinymce','activetinymce') && (xarModIsAvailable('tinymce')) && (xarModGetVar('tinymce','tinyloadmode') <> 'manual')) {

        // auto mode should automatically activate any existing configs in text areas
        // and auto activate any textarea unless they have mceNoEditor present
        $data = xarModAPIFunc('tinymce','admin','loadconfig');
        $data['always_load'] = TRUE;
        $data['autoload'] = TRUE;
        xarTpl_includeModuleTemplate('tinymce','tinymce_insert',$data);

    }
    return true;
}

?>