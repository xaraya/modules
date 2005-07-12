<?php

/**
 * delete a poll
 */
function polls_admin_delete()
{
    // Get parameters
    list($pid,
         $confirm) = xarVarCleanFromInput('pid',
                                         'confirm');
    if (!isset($pid) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    $poll = xarModAPIFunc('polls',
                           'user',
                           'get',
                           array('pid' => $pid));

    if (!xarSecurityCheck('DeletePolls',1,'All',"$poll[title]:All:$pid")) {
        return;
    }

    // Check for confirmation
    if ($confirm != 1) {
        // No confirmation yet - get one

        $data = array();

        $data['polltitle'] = $poll['title'];
        $data['pid'] = $pid;
        $data['confirm'] = 1;
        $data['authid'] = xarSecGenAuthKey();
        $data['buttonlabel'] = 'Delete Poll';

        return $data;
    }

    // Confirm authorisation code
    if (!xarSecConfirmAuthKey()) return;

    // Pass to API
    if (xarModAPIFunc('polls',
                     'admin',
                     'delete', array('pid' => $pid))) {
        // Success
        xarSessionSetVar('statusmsg', xarML('Poll deleted'));

    }

    xarResponseRedirect(xarModURL('polls', 'admin', 'list'));

    return true;
}

?>