<?php
function ephemerids_admin_update($args)
{
    // Get parameters from whatever input we need
    if (!xarVarFetch('did','int:1:',$did)) return;
    if (!xarVarFetch('mid','int:1:',$mid)) return;
    if (!xarVarFetch('yid','int:1:',$yid)) return;
    if (!xarVarFetch('content','str:1:',$content, '',XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('elanguage','str:1:',$elanguage, 'ALL',XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('eid','int:1:',$eid)) return;
    if (!xarVarFetch('objectid','str:1:',$objectid,$eid,XARVAR_NOT_REQUIRED)) return;
    extract($args);
    // Confirm authorisation code.
    if (!xarSecConfirmAuthKey()) return;

    // The API function is called.
    if(!xarModAPIFunc('ephemerids',
                    'admin',
                    'update',
                    array('eid' => $eid,
                          'did' => $did,
                          'mid' => $mid,
                          'yid' => $yid,
                          'content' => $content,
                          'elanguage' => $elanguage))) {
        return; // throw back
    }
    //Redirect
    xarResponseRedirect(xarModURL('ephemerids', 'admin', 'view'));
    // Return
    return true;
}
?>