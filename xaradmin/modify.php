<?php
/**
 * File: $Id:
 * 
 * Standard function to modify an item
 * 
 * @copyright (C) 2004 by Jo Dalle Nogare
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.athomeandabout.com
 *
 * @subpackage xarcpshop
 * @author jojodee@xaraya.com
 */
/**
 * modify a shop
 * This is a standard function that is called whenever an administrator
 * wishes to modify a current module item
 * 
 * @param  $ 'sid' the id of the item to be modified
 */
function xarcpshop_admin_modify($args)
{ 
    extract($args);
 
    if (!xarVarFetch('storeid', 'int:1:', $storeid)) return;
    if (!xarVarFetch('objectid', 'str:1:', $objectid, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('invalid', 'str:1:', $invalid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('nickname', 'str:1:', $nickname, $nickname,XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('name', 'str:1:', $name, $name, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('toplevel', 'str:1:', $toplevel, $toplevel, XARVAR_NOT_REQUIRED)) return;

    $data = xarModAPIFunc('xarcpshop', 'admin', 'menu');
    // Initialise the variable that will hold the items, so that the template
    // doesn't need to be adapted in case of errors

    if (!empty($objectid)) {
        $storeid = $objectid;
    }
    $item = xarModAPIFunc('xarcpshop',
                          'user',
                          'get',
                          array('storeid' => $storeid));
    // Check for exceptions
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    if (!xarSecurityCheck('EditxarCPShop', 1, 'Item', "$item[name]:All:$storeid")) {
        return;
    }
    $item['module'] = 'xarcpshop';
    $hooks = xarModCallHooks('item', 'modify', $storeid, $item);
    if (empty($hooks)) {
        $hooks = '';
    } elseif (is_array($hooks)) {
        $hooks = join('', $hooks);
    }

    $data['authid']= xarSecGenAuthKey();
    $data['namelabel']    = xarVarPrepForDisplay(xarML('Cafe Press Shop ID:'));
    $data['name']         = xarVarPrepForDisplay($item['name']);
    $data['nicklabel']    = xarVarPrepForDisplay(xarML('Nick Name:'));
    $data['nickname']     = xarVarPrepForDisplay($item['nickname']);
    $data['levellabel']    = xarVarPrepForDisplay(xarML('Top Level:'));
    $data['toplevel']     = xarVarPrepForDisplay($item['toplevel']);
    $data['storeidlabel']  = xarVarPrepForDisplay(xarML('ID:'));
    $data['storeid']       = $item['storeid'];
    $data['invalid']      = $invalid;
    $data['updatebutton'] = xarVarPrepForDisplay(xarML('Update xarCPShop'));
    $data['hooks']        = $hooks;
    $data['item']         = $item;
    return $data;
}

?>
