<?php
/**
 * Add new ephemerids to database.
 */
function ephemerids_admin_add()
{
    // Get parameters from whatever input we need
    if (!xarVarFetch('did','int:1:',$did)) return;
    if (!xarVarFetch('mid','int:1:',$mid)) return;
    if (!xarVarFetch('yid','int:1:',$yid)) return;
    if (!xarVarFetch('content','str:1:',$content, '',XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('elanguage','str:1:',$elanguage, 'ALL',XARVAR_NOT_REQUIRED)) return;

    // Confirm Auth
    if (!xarSecConfirmAuthKey()) return;

    // Security Check
    if(!xarSecurityCheck('AddEphemerids')) return;

    // The API function is called.  
    $emp = xarModAPIFunc('ephemerids',
                         'admin',
                         'add',
                         array('did' => $did, 
                               'mid' => $mid, 
                               'yid' => $yid, 
                               'content' => $content, 
                               'elanguage' => $elanguage));

    // The return value of the function is checked here
    if (!isset($emp) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back
    // This function generated no output, and so now it is complete we redirect
    // the user to an appropriate page for them to carry on their work
    xarResponseRedirect(xarModURL('ephemerids', 'admin', 'view'));
    // Return
    return true;
}

?>