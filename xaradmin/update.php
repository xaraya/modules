<?php
/**
 * File: $Id:
 * 
 * Standard function to update a current item
 * 
 * @copyright (C) 2004 by Jo Dalle Nogare
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.athomeandabout.com
 *
 * @subpackage xarcpshop
 * @author jojodee@xaraya.com
 */
/**
 * @param  $ 'storeid' the id of the item to be updated
 * @param  $ 'name' the name of the item to be updated
 * @param  $ 'storeid' the storeid of the item to be updated
 */
function xarcpshop_admin_update($args)
{ 
    extract($args);

    if (!xarVarFetch('storeid', 'int:1:', $storeid, $storeid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('objectid', 'str:1:', $objectid, $objectid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('invalid', 'str:1:', $invalid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('nickname', 'str:1:', $nickname, $nickname, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('name', 'str:1:', $name, $name, XARVAR_NOT_REQUIRED)) return;
   if (!xarVarFetch('toplevel', 'str:1:', $toplevel, $toplevel, XARVAR_NOT_REQUIRED)) return;
    if (!empty($objectid)) {
        $storeid = $objectid;
    }
    if (!xarSecConfirmAuthKey()) return;

    $invalid = array();
    if (empty($storeid) || !is_numeric($storeid)) {
        $invalid['storeid'] = 1;

    }
    if (empty($nickname) || !is_string($nickname)) {
        $invalid['nickname'] = 1;

    }
    if (empty($name) || !is_string($name)) {
        $invalid['name'] = 1;

    }
   if (empty($nickname) || !is_string($nickname)) {
        $nickname='';

    }

    // check if we have any errors
    if (count($invalid) > 0) {
        // call the admin_new function and return the template vars
        // (you need to copy admin-new.xd to admin-create.xd here)
        return xarModFunc('xarcpshop', 'admin', 'modify',
                          array('name'     => $name,
                                'storeid'   => (int)$storeid,
                                'nickname' => $nickname,
                                'invalid'  => $invalid));
    }

    if (!xarModAPIFunc('xarcpshop','admin','update',
                       array('storeid'   => (int)$storeid,
                             'name'   => $name,
                             'nickname' => $nickname,
                             'toplevel' => $toplevel))) {
        return; // throw back
    }
    xarSessionSetVar('statusmsg', xarML('xarCPShop Item was successfully updated!'));
    // This function generated no output, and so now it is complete we redirect
    // the user to an appropriate page for them to carry on their work
    xarResponseRedirect(xarModURL('xarcpshop', 'admin', 'view'));
    // Return
    return true;
} 

?>
