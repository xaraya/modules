<?php

/**
 * $Id$
 * modify an item
 * @param 'lid' the id of the link to be modified
 */
function autolinks_admin_modify($args)
{
    // Get parameters from whatever input we need
    if (!xarVarFetch('lid',  'id', $lid,  NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('obid', 'id', $obid, NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('startnumitem', 'id', $startnumitem, NULL, XARVAR_DONT_SET)) {return;}

    extract($args);

    if (!empty($obid)) {
        $lid = $obid;
    }

    $link = xarModAPIFunc('autolinks',
                         'user',
                         'get',
                         array('lid' => $lid));

    if ($link == false) return;

    // Security Check
    if(!xarSecurityCheck('EditAutolinks')) return;

    // Prepare some strings for the form.
    $link['prepcomment'] = xarVarPrepForDisplay($link['comment']);
    $link['prepkeyword'] = xarVarPrepForDisplay($link['keyword']);
    $link['preptitle'] = xarVarPrepForDisplay($link['title']);

    $link['cancelurl'] = xarModURL('autolinks', 'admin', 'view', array('startnumitem' => $startnumitem));
    $link['updateurl'] = xarModURL('autolinks', 'admin', 'update', array('startnumitem' => $startnumitem));

    $link['authid'] = xarSecGenAuthKey();
    return $link;

}

?>
