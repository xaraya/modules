<?php

/**
 * Update or create a page type (form handler).
 */
function xarpages_admin_updatetype()
{
    if (!xarVarFetch('ptid', 'id', $ptid, 0, XARVAR_NOT_REQUIRED)) return;

    if (!xarVarFetch('name', 'pre:lower:ftoken:str:0:100', $name)) return;
    if (!xarVarFetch('desc', 'str:0:255', $desc)) return;

    // Confirm authorisation code
    if (!xarSecConfirmAuthKey()) {return;}

    // Pass to API
    if (!empty($ptid)) {
        if (!xarModAPIFunc(
            'xarpages', 'admin', 'updatetype',
            array(
                'ptid'  => $ptid,
                'name'  => $name,
                'desc'  => $desc
            )
        )) {return;}
    } else {
        // Pass to API
        $ptid = xarModAPIFunc(
            'xarpages', 'admin', 'createtype',
            array(
                'name'  => $name,
                'desc'  => $desc
            )
        );
        if (!$ptid) {return;}
    }

    if ($creating) {
        xarResponseRedirect(xarModUrl('xarpages', 'admin', 'viewtypes'));
    } else {
        xarResponseRedirect(xarModUrl('xarpages', 'admin', 'viewtypes'));
    }

    return true;
}

?>