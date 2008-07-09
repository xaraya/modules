<?php
/**
 * xarTinyMCE eventapi
 *
 * @package modules
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xartinymce module
 * @copyright (C) 2002-2008 2skies.com
 * @link http://xarigami.com/projects/xartinymce
 * @author Jo Dalle Nogare <icedlava@2skies.com>
 */

/**
 * Tinymce event handler for the system event ServerRequest
 *
 * This function is called when the system triggers the
 * event in index.php on each Server Request
 * In this case it loads tinymce javascript on every page load
 * if it is active and set as the default wysiwyg editor and mode is automatice
 *
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
 * @returns bool
 */
function tinymce_eventapi_OnServerRequest()
{

    if (xarModGetVar('tinymce','activetinymce') == 1 && (xarModIsAvailable('tinymce')) && (xarModGetVar('tinymce','tinyloadmode')<>'manual')) {
        $data=array();
        xarTpl_includeModuleTemplate('tinymce','tinymce_insert',$data);
    }
    return true;
}

?>