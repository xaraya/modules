<?php
/**
 * Standard function to modify an item
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage maxercalls
 * @author Example module development team 
 */
/**
 * modify a call
 * 
 * @param  $ 'callid' the id of the item to be modified
 */
function maxercalls_admin_modifycall($args)
{ 
    extract($args);

    if (!xarVarFetch('callid', 'int:1:', $callid)) return;
    if (!xarVarFetch('objectid', 'str:1:', $objectid, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('invalid', 'str:1:', $invalid, XARVAR_NOT_REQUIRED)) return;

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
     
    // Security check - important to do this as early as possible to avoid
    // potential security holes or just too much wasted processing.  However,
    // in this case we had to wait until we could obtain the item name to
    // complete the instance information so this is the first chance we get to
    // do the check
    if (!xarSecurityCheck('EditMaxercalls', 1, 'Item', "$callid:All:$item[enteruid]")) {
        return;
    } 
    // Get menu variables - it helps if all of the module pages have a standard
    // menu at their head to aid in navigation
    // $menu = xarModAPIFunc('maxercalls','admin','menu','modify');
    $item['module'] = 'maxercalls';
    $hooks = xarModCallHooks('item', 'modify', $callid, $item);
    if (empty($hooks)) {
        $hooks = '';
    } elseif (is_array($hooks)) {
        $hooks = join('', $hooks);
    } 
    // Return the template variables defined in this function
    return array('authid'       => xarSecGenAuthKey(),
                 'callid'       => $callid,
                 'invalid'      => $invalid,
	//			 'enteruid'     => $enteruid,
	//			 'enterts'      => $enterts,
	//			 'remarks'      => $remarks,
                 'updatebutton' => xarVarPrepForDisplay(xarML('Update call')),
                 'hooks'        => $hooks,
				 'calldatelabel' => xarVarPrepForDisplay(xarML('Date of call YYYY-MM-DD')),
				 'calltimelabel' => xarVarPrepForDisplay(xarML('Time of call HH:MM')),
				 'calltextlabel' => xarVarPrepForDisplay(xarML('Type/Text of call')),
				 'calltypelabel' => xarVarPrepForDisplay(xarML('Type of call')),
				 'ownerlabel' => xarVarPrepForDisplay(xarML('Owner of maxer')),
				 'remarkslabel' => xarVarPrepForDisplay(xarML('Other remarks')),
                 'item'         => $item);
} 

?>
