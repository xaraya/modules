<?php
/**
 * File: $Id
 *
 * xarTinymce event API functions
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xartinymce
 * @author jojodee@xaraya.com
 */

/**
 * Tinymce event handler for the system event ServerRequest
 *
 * this function is called when the system triggers the
 * event in index.php on each Server Request
 *
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
 * @returns bool
 */
function tinymce_eventapi_OnServerRequest()
{

    if (xarModGetVar('base','editor') == 'tinymce' && (xarModIsAvailable('tinymce')) && (xarModGetVar('tinymce','tinyloadmode')<>'manual')) {
        $data=array();
         xarTpl_includeModuleTemplate('tinymce','tinymce_insert',$data);
    }
    return true;
}

?>
