<?php

function release_admin_deleteid()
{
    // Get parameters
    if (!xarVarFetch('rid', 'int:1:', $rid)) return;
    if (!xarVarFetch('obid', 'str:1:', $obid, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('confirmation','str:1:',$confirmation,'',XARVAR_NOT_REQUIRED)) return;
    
    extract($args);

    if (!empty($obid)) {
        $rid = $obid;
    } 

    // The user API function is called.
    $data = xarModAPIFunc('release',
                          'user',
                          'getid',
                          array('rid' => $rid));

    if ($data == false) return;

    // Security Check
    if(!xarSecurityCheck('DeleteRelease')) return;

    // Check for confirmation.
    if (empty($confirmation)) {
        //Load Template
        $data['authid'] = xarSecGenAuthKey();
        return $data;
    }

    // If we get here it means that the user has confirmed the action

    // Confirm authorisation code.
    if (!xarSecConfirmAuthKey()) return;

    if (!xarModAPIFunc('release',
                       'admin',
                       'deleteid', 
                        array('rid' => $rid))) return;

    // Redirect
    xarResponseRedirect(xarModURL('release', 'admin', 'viewids'));

    // Return
    return true;
}

?>