<?php
/**
 * File: $Id:
 * 
 * Standard function to delete an item
 * 
 * @copyright (C) 2004 by Jo Dalle Nogare
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.athomeandabout.com
 *
 * @subpackage xarcpshop
 * @author jojodee@xaraya.com
 */
/**
 * delete item
 *
 * @param  $ 'storeid' the id of the item to be deleted
 * @param  $ 'confirm' confirm that this item can be deleted
 */
function xarcpshop_admin_delete($args)
{ 
    extract($args);

    if (!xarVarFetch('storeid', 'int:1:', $storeid)) return;
    if (!xarVarFetch('objectid', 'str:1:', $objectid, NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('confirm', 'str:1:', $confirm, '', XARVAR_NOT_REQUIRED)) return;
    if (!empty($objectid)) {
        $storeid = $objectid;
    }
    $item = xarModAPIFunc('xarcpshop','user','get',
                    array('storeid' => $storeid));
    // Check for exceptions
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    if (!xarSecurityCheck('DeletexarCPShop', 1, 'Item', "$item[name]:All:$storeid")) {
        return;
    } 
    // Check for confirmation.
    if (empty($confirm)) {
      $data = xarModAPIFunc('xarcpshop', 'admin', 'menu');
        // Specify for which item you want confirmation
        $data['storeid'] = $storeid;
        // Add some other data you'll want to display in the template
        $data['confirmtext'] = xarML('Please confirm deletion of this item ?');
        $data['itemidlabel'] = xarML('Item ID');
        $data['itemid'] = xarVarPrepForDisplay($item['storeid']);
        $data['namelabel'] = xarML('Cafe Press Shop ID');
        $data['namevalue'] = xarVarPrepForDisplay($item['name']);
        $data['confirmbutton'] = xarML('Confirm');
        // Generate a one-time authorisation code for this operation
        $data['authid'] = xarSecGenAuthKey();
        // Return the template variables defined in this function
        return $data;
    }
    if (!xarSecConfirmAuthKey()) return;
    if (!xarModAPIFunc('xarcpshop','admin','delete',
            array('storeid' => $storeid))) {
        return; // throw back
    } 
    // This function generated no output, and so now it is complete we redirect
    // the user to an appropriate page for them to carry on their work
    xarResponseRedirect(xarModURL('xarcpshop', 'admin', 'view'));
    // Return
    return true;
} 

?>
