<?php

function xarbb_admin_delete()
{
    // Get parameters
    list($fid,
         $confirmation) = xarVarCleanFromInput('fid',
                                              'confirmation');

    // The user API function is called.
    $data = xarModAPIFunc('xarbb',
                          'user',
                          'getforum',
                          array('fid' => $fid));

    if ($data == false) return;

    // Security Check
    if(!xarSecurityCheck('DeletexarBB')) return;

    // Check for confirmation.
    if (empty($confirmation)) {
        //Load Template
        $data['authid'] = xarSecGenAuthKey();
        return $data;
    }

    // If we get here it means that the user has confirmed the action

    // Confirm authorisation code.
    if (!xarSecConfirmAuthKey()) return;

    if (!xarModAPIFunc('xarbb',
		               'admin',
		               'delete', 
                        array('fid' => $fid))) return;

    if (!xarModAPIFunc('xarbb',
		               'admin',
		               'deletealltopics', 
                        array('fid' => $fid))) return;

    // Redirect
    xarResponseRedirect(xarModURL('xarbb', 'admin', 'view'));

    // Return
    return true;
}

?>