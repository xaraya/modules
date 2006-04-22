<?php
/**
 * Handle form hooks
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.org
 * @param $itemtype - pass itemtype for multiple itemtype transformation
 * @subpackage  xarbb Module
 * @author John Cox, jojodee
*/
 
function xarbb_userapi_formhooks($args)
{
    extract($args);
    if (!isset($itemtype) || empty($itemtype)) {
       $itemtype='0';
    }

    $hooks = array();
    // call the right hooks, i.e. not the ones for the comments module :)
    //<jojodee> also add the correct itemtype - we can then call specific form transform
    $hooks['formaction'] =  xarModCallHooks('item', 'formaction', '', array(), 'xarbb', $itemtype);
    $hooks['formdisplay'] = xarModCallHooks('item', 'formdisplay','', array(), 'xarbb', $itemtype);

    if (empty($hooks['formaction'])){
        $hooks['formaction'] = '';
    } elseif (is_array($hooks['formaction'])) {
        $hooks['formaction'] = join('', $hooks['formaction']);
    }

    if (empty($hooks['formdisplay'])){
        $hooks['formdisplay'] = '';
    } elseif (is_array($hooks['formdisplay'])) {
        $hooks['formdisplay'] = join('', $hooks['formdisplay']);
    }

    return $hooks;
}

?>