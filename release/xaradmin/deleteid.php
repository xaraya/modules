<?php

function release_admin_deleteid()
{
    // Get parameters
    list($rid,
         $confirmation) = xarVarCleanFromInput('rid',
                                              'confirmation');

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