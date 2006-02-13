<?php
/**
 * Standard function to Delete and item
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Legis Module
 * @link http://xaraya.com/index.php/release/593.html
 * @author jojodee
 */
/**
 * Standard function to Delete an item
 *

 *
 * @author jojodee
 * @param  $ 'cdid' the id of the item to be deleted
 * @param  $ 'confirm' confirm that this item can be deleted
 */
function legis_admin_delete($args)
{ 
     extract($args);

    if (!xarVarFetch('cdid',     'id', $cdid)) return;
    if (!xarVarFetch('objectid', 'id', $objectid, NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('confirm',  'str:1:', $confirm, '', XARVAR_NOT_REQUIRED)) return;
    if (!empty($objectid)) {
        $exid = $objectid;
    }
    $item = xarModAPIFunc('legis','user','get',array('cdid' => $cdid));
        /* Check for exceptions */
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; /* throw back */

    if (!xarSecurityCheck('DeleteLegis', 1, 'Item', "$item[cdtitle]:All:$cdid")) {
        return;
    }
    /* Check for confirmation. */
    if (empty($confirm)) {
        $data = xarModAPIFunc('legis', 'admin', 'menu');

        /* Specify for which item you want confirmation */
        $data['cdid'] = $cdid;
        $data['item'] = $item;
        /* Add some other data you'll want to display in the template */
        $data['itemid'] = xarML('Item ID');
        $data['cdtitle'] = xarVarPrepForDisplay($item['cdtitle']);

        /* Generate a one-time authorisation code for this operation */
        $data['authid'] = xarSecGenAuthKey();

        /* Return the template variables defined in this function */
        return $data;
    }
    if (!xarSecConfirmAuthKey()) return;

    if (!xarModAPIFunc('legis','admin','delete',
            array('cdid' => $cdid))) {
        return; // throw back
    }
    xarResponseRedirect(xarModURL('legis', 'admin', 'view'));
    
    /* Return */
    return true;
}
?>
