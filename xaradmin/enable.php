<?php

/**
 * $Id$
 * enable or disable item
 * @param 'lid' the id of the item to be deleted
 * @param 'enabled' true=enable; false=disable; null=toggle
 */
function autolinks_admin_enable($args)
{
    extract($args);

    // Get parameters from whatever input we need
    if (!xarVarFetch('lid',     'isset', $lid,       NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('obid',    'isset', $obid,      NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('enabled', 'int:0:1', $enabled, NULL, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('startnumitem', 'id', $startnumitem, NULL, XARVAR_DONT_SET)) {return;}

    if (!empty($obid)) {
        $tid = $obid;
    }

    // The user API function is called
    $link = xarModAPIFunc(
        'autolinks', 'user', 'get',
        array('lid' => $lid)
    );

    if ($link == false) {
        return;
    }

    // Security Check
    if(!xarSecurityCheck('EditAutolinks')) {return;}

    // Confirm authorisation code
    if (!xarSecConfirmAuthKey()) {
        return;
    }

    // Toggle the enabled flag.
    if ($enabled == NULL) {
        $enabled = $link['enabled'] ^ 1;
    }

    // The API function is called
    if(!xarModAPIFunc(
        'autolinks', 'admin', 'update',
        array('lid' => $lid, 'enabled' => $enabled)
        )
    ) {return;}

    if (!empty($startnumitem)) {
        xarResponseRedirect(xarModURL('autolinks', 'admin', 'view', array('startnumitem' => $startnumitem)));
    } else {
        xarResponseRedirect(xarModURL('autolinks', 'admin', 'view'));
    }

    // Return
    return true;
}

?>
