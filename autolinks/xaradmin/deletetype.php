<?php

/**
 * $Id$
 * delete item
 * @param 'lid' the id of the item to be deleted
 * @param 'confirmation' confirmation that this item can be deleted
 */
function autolinks_admin_deletetype($args)
{
    // Get parameters from whatever input we need
    if (!xarVarFetch('tid',          'isset', $tid,          NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('obid',         'isset', $obid,         NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('confirmation', 'isset', $confirmation, NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('cascade',      'isset', $cascade,      0,    XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('ddremove',     'isset', $ddremove,     0,    XARVAR_NOT_REQUIRED)) {return;}

    // Security Check
    if(!xarSecurityCheck('DeleteAutolinks')) {return;}

    extract($args);

    if (!empty($obid)) {
        $tid = $obid;
    }

    // Get the details of the link type to delete.
    $type = xarModAPIFunc(
        'autolinks', 'user', 'gettype',
        array('tid' => $tid)
    );

    if ($type == false) {
        // The link type does not exist.
        return;
    }

    // Count links there may be for this type.
    $linkcount = xarModAPIFunc(
        'autolinks', 'user', 'countitems',
        array('tid' => $tid)
    );
    
    // Check for confirmation.
    if (empty($confirmation)) {
        // Is dd hooked?
        $type['ddhooked'] = xarModIsHooked('dynamicdata', 'autolinks', $type['itemtype']);
        $type['linkcount'] = $linkcount;
        $type['authid'] = xarSecGenAuthKey();
        return $type;
    }

    // If we get here it means that the user has confirmed the action

    // Confirm authorisation code
    if (!xarSecConfirmAuthKey()) {
        return;
    }

    // The API function is called
    if (xarModAPIFunc('autolinks', 'admin', 'deletetype',
        array('tid' => $tid, 'cascade' => $cascade))
    ) {
        // Remove the DD object too?
        if (xarModIsHooked('dynamicdata', 'autolinks', $type['itemtype']) && !empty($ddremove)) {
            // Get the object information.
            $objectinfo = xarModAPIFunc(
                'dynamicdata', 'user', 'getobjectinfo',
                array(
                    'moduleid' => xarModGetIDFromName('autolinks'),
                    'itemtype' => $type['itemtype']
                )
            );

            // Delete the object if it exists.
            if (isset($objectinfo['objectid'])) {
                $result = xarModAPIFunc(
                    'dynamicdata', 'admin', 'deleteobject',
                    array('objectid' => $objectinfo['objectid'])
                );
            }
        }
    } else {
        return;
    }

    // Return to the link type view page.
    xarResponseRedirect(xarModURL('autolinks', 'admin', 'viewtype'));
    return true;
}

?>