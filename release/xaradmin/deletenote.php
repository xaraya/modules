<?php

function release_admin_deletenote()
{
    // Get parameters
    list($rnid,
         $confirmation) = xarVarCleanFromInput('rnid',
                                              'confirmation');

    // The user API function is called.
    $data = xarModAPIFunc('release',
                          'user',
                          'getnote',
                          array('rnid' => $rnid));

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
                       'deletenote', 
                        array('rnid' => $rnid))) return;

    // Redirect
    xarResponseRedirect(xarModURL('release', 'admin', 'viewnotes'));

    // Return
    return true;
}

?>