<?php
/**
 * Handle form hooks
 *
 * @package modules
 * @copyright (C) 2003-2006 The Digital Development Foundation.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage  xarbb Module
 * @link http://xaraya.com/index.php/release/300.html
 * @author John Cox
 * @author Jo Dalle Nogare
 */
/**
 * @param $itemtype - pass itemtype for multiple itemtype transformation
 * @author John Cox, jojodee
 * @return mixed The hooks that are called
 */
function xarbb_userapi_formhooks($args)
{
    extract($args);
    if (!isset($itemtype) || empty($itemtype)) {
       $itemtype = '0';
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