<?php

/**
 * $Id$
 * modify an item
 * @param 'lid' the id of the link to be modified
 */
function autolinks_admin_modify($args)
{
    extract($args);

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

    // Prepare some strings for the form.
    $link['prepcomment'] = xarVarPrepForDisplay($link['comment']);
    $link['prepkeyword'] = xarVarPrepForDisplay($link['keyword']);
    $link['preptitle'] = xarVarPrepForDisplay($link['title']);
    $link['prepsample'] = xarVarPrepForDisplay($link['sample']);
    $link['prepname'] = xarVarPrepForDisplay($link['name']);
    $link['preptypename'] = xarVarPrepForDisplay($link['type_name'] );
    $link['preptype_desc'] = xarVarPrepForDisplay($link['type_desc'] );

    $link['cancelurl'] = xarModURL('autolinks', 'admin', 'view', array('startnumitem' => $startnumitem));
    $link['updateurl'] = xarModURL('autolinks', 'admin', 'update', array('startnumitem' => $startnumitem));
    $link['edittypeurl'] = xarModURL('autolinks', 'admin', 'modifytype', array('tid' => $link['tid']));

    $link['authid'] = xarSecGenAuthKey();

    $hooks = xarModCallHooks(
        'item', 'modify', $lid, 
        array('itemtype' => $link['itemtype'], 'module' => 'autolinks')
    );
    $link['hooks'] = $hooks;

    return $link;
}

?>
