<?php
/**
 * File: $Id$
 * 
 * Handle form hooks
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.org
 *
 * @subpackage  xarbb Module
 * @author John Cox
*/

function xarbb_userapi_formhooks()
{

    $hooks = array();
    // call the right hooks, i.e. not the ones for the comments module :)
    $hooks['formaction']              = xarModCallHooks('item', 'formaction', '', array(), 'xarbb');
    $hooks['formdisplay']             = xarModCallHooks('item', 'formdisplay', '', array(), 'xarbb');

    if (empty($hooks['formaction'])){
        $hooks['formaction'] = '';
    } elseif (is_array($hooks['formaction'])) {
        $hooks['formaction'] = join('',$hooks['formaction']);
    }

    if (empty($hooks['formdisplay'])){
        $hooks['formdisplay'] = '';
    } elseif (is_array($hooks['formdisplay'])) {
        $hooks['formdisplay'] = join('',$hooks['formdisplay']);
    }

    return $hooks;
}

?>
