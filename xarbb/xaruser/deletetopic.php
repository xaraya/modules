<?php

function xarbb_user_deletetopic()
{
    // Get parameters
    list($tid,
         $confirmation) = xarVarCleanFromInput('tid',
                                              'confirmation');

	// for sec check
    if(!$topic = xarModAPIFunc('xarbb','user','gettopic',array('tid' => $tid))) return;

    // Security Check
    if(!xarSecurityCheck('ModxarBB',1,'Forum',$topic['catid'].':'.$topic['fid'])) return;

    // Check for confirmation.
    if (empty($confirmation)) {
        //Load Template
        $data['authid'] = xarSecGenAuthKey();
        $data['tid'] = $tid;
        return $data;
    }

    // If we get here it means that the user has confirmed the action

    // Confirm authorisation code.
    if (!xarSecConfirmAuthKey()) return;

    if (!xarModAPIFunc('xarbb',
		               'admin',
		               'deletetopics',
                        array('tid' => $tid))) return;

    // Redirect
    xarResponseRedirect(xarModURL('xarbb', 'user', 'viewforum',array("fid" => $topic['fid'])));

    // Return
    return true;
}

?>