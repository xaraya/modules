<?php
/**
 * Standard function to modify an item
 *
 * @package modules
 * @copyright (C) 2005-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Maxercalls Module
 * @link http://xaraya.com/index.php/release/247.html
 * @author Maxercalls module development team
 */
/**
 * modify a call
 *
 * @param int callid The id of the item to be modified
 * @return array
 */
function maxercalls_admin_modifycall($args)
{
    extract($args);

    if (!xarVarFetch('callid',   'int:1:', $callid)) return;
    if (!xarVarFetch('objectid', 'str:1:', $objectid, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('invalid',  'str:1:', $invalid, XARVAR_NOT_REQUIRED)) return;

    if (!empty($objectid)) {
        $callid = $objectid;
    }
    // The user API function is called.
    $item = xarModAPIFunc('maxercalls',
                          'user',
                          'get',
                          array('callid' => $callid));
    // Check for exceptions
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    // Security check
    if (!xarSecurityCheck('EditMaxercalls', 1, 'Item', "$callid:All:$item[enteruid]")) {
        return;
    }
    // Get menu variables - it helps if all of the module pages have a standard
    // menu at their head to aid in navigation
    // $menu = xarModAPIFunc('maxercalls','admin','menu','modify');
    $item['module'] = 'maxercalls';
    $item['itemtype'] = 1;
    $hooks = xarModCallHooks('item', 'modify', $callid, $item);
    if (empty($hooks)) {
        $hookoutput = array();
    } else {
        $hookoutput = $hooks;
    }
    // Return the template variables defined in this function
    return array('authid'       => xarSecGenAuthKey(),
                 'callid'       => $callid,
                 'invalid'      => $invalid,
                 'updatebutton' => xarVarPrepForDisplay(xarML('Update call')),
                 'hookoutput'   => $hookoutput,
                 'item'         => $item);
}
?>
