<?php

/**
 * $Id$
 * modify an item
 * @param 'lid' the id of the link to be modified
 */
function autolinks_admin_modify($args)
{
    extract($args);

    // If we have come here using a tid, then redirect to the correct function.
    if (!xarVarFetch('tid',  'id', $tid,  NULL, XARVAR_NOT_REQUIRED)) {return;}
    if (!empty($tid)) {
        xarResponseRedirect(
            xarModURL('autolinks', 'admin', 'modifytype', array('tid' => $tid))
        );
        return true;
    }

    // Get parameters from whatever input we need
    if (!xarVarFetch('lid',  'id', $lid,  NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('obid', 'id', $obid, NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('startnumitem', 'id', $startnumitem, NULL, XARVAR_DONT_SET)) {return;}

    if (!empty($obid)) {
        $lid = $obid;
    }

    $link = xarModAPIFunc(
        'autolinks', 'user', 'get',
        array('lid' => $lid)
    );

    if ($link == false) {return;}

    // Security Check
    if(!xarSecurityCheck('EditAutolinks')) {return;}

    $link['cancelurl'] = xarModURL('autolinks', 'admin', 'view', array('startnumitem' => $startnumitem));
    $link['updateurl'] = xarModURL('autolinks', 'admin', 'update', array('startnumitem' => $startnumitem));
    $link['edittypeurl'] = xarModURL('autolinks', 'admin', 'modifytype', array('tid' => $link['tid']));
    $link['moveurl'] = xarModURL('autolinks', 'admin', 'move', array('lid' => $link['lid']));

    $link['authid'] = xarSecGenAuthKey();

    $hooks = xarModCallHooks(
        'item', 'modify', $lid, 
        array('itemtype' => $link['itemtype'], 'module' => 'autolinks')
    );
    $link['hooks'] = $hooks;

    return $link;
}

?>