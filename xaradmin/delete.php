<?php

/**
 * delete existing xlink definition
 */
function xlink_admin_delete($args)
{ 
    extract($args);

    if (!xarVarFetch('itemid', 'id', $itemid)) return;
    if (!xarVarFetch('confirm',  'isset', $confirm,  NULL, XARVAR_NOT_REQUIRED)) {return;}

    if (!xarSecurityCheck('AdminXLink')) return;

    $data = array();
    $data['object'] =& xarModAPIFunc('dynamicdata','user','getobject',
                                     array('module' => 'xlink'));
    if (!isset($data['object'])) return;

    // Get current item
    $newid = $data['object']->getItem(array('itemid' => $itemid));
    if (empty($newid) || $newid != $itemid) return;

    if (!empty($confirm)) {
        // Confirm authorisation code
        if (!xarSecConfirmAuthKey()) return; 

        // delete the item here
        $itemid = $data['object']->deleteItem();
        if (empty($itemid)) return; // throw back

        // let's go back to the admin view
        xarResponseRedirect(xarModURL('xlink', 'admin', 'view'));
        return true;
    }

    $data['itemid'] = $itemid;
    $data['authid'] = xarSecGenAuthKey();
    $data['confirm'] = xarML('Delete');
    return $data;
}

?>
