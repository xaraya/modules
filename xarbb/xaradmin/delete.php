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

    if (empty($data)) return;

    // Security Check
    if(!xarSecurityCheck('DeletexarBB', 1, 'Forum', $data['catid'].':'.$data['fid'])) return;

    // Check for confirmation.
    if (empty($confirmation)) {
        // for forums that lost their category
        if (!isset($data['fid'])) {
            $data['fid'] = $fid;
        }
        $data['authid'] = xarSecGenAuthKey();
        //Load Template
        return $data;
    }

    // If we get here it means that the user has confirmed the action

    // Confirm authorisation code.
    if (!xarSecConfirmAuthKey()) return;

    // need to delete the topics first then the forum.
    if (!xarModAPIFunc('xarbb',
		               'admin',
		               'deletealltopics',
                        array('fid' => $fid))) return;

    if (!xarModAPIFunc('xarbb',
		               'admin',
		               'delete',
                        array('fid' => $fid))) return;

    // Redirect
    xarResponseRedirect(xarModURL('xarbb', 'admin', 'view'));

    // Return
    return true;
}

?>
