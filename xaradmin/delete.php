<?php

/**
 * $Id$
 * delete item
 * @param 'lid' the id of the item to be deleted
 * @param 'confirmation' confirmation that this item can be deleted
 */
function autolinks_admin_delete($args)
{
    // Get parameters from whatever input we need
    if (!xarVarFetch('lid', 'isset', $lid, NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('obid', 'isset', $obid, NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('confirm', 'isset', $confirmation, NULL, XARVAR_DONT_SET)) {return;}

    extract($args);

    if (!empty($obid)) {
        $lid = $obid;
    }

    // The user API function is called
    $link = xarModAPIFunc(
        'autolinks', 'user', 'get', array('lid' => $lid)
    );

    if ($link == false) {
        return;
    }

    // Security Check
    if (!xarSecurityCheck('DeleteAutolinks')) {return;}

    // Check for confirmation.
    if (empty($confirmation)) {
        $link['authid'] = xarSecGenAuthKey();
        return $link;
    }

    // If we get here it means that the user has confirmed the action

    // Confirm authorisation code
    if (!xarSecConfirmAuthKey()) {
        return;
    }

    // The API function is called
    if (!xarModAPIFunc('autolinks', 'admin', 'delete', array('lid' => $lid))) {
        return;
    }

    xarResponseRedirect(xarModURL('autolinks', 'admin', 'view'));

    // Return
    return true;
}

?>