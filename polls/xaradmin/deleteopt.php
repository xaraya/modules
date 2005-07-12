<?php

/**
 * delete a poll option
 */
function polls_admin_deleteopt()
{
    // Start output
    $data = array();

    // Get parameters
    list($pid,
         $opt,
         $confirm) = xarVarCleanFromInput('pid',
                                         'opt',
                                         'confirm');
    if ((!isset($pid) || !isset($opt)) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    $poll = xarModAPIFunc('polls',
                           'user',
                           'get',
                           array('pid' => $pid));

    if (!isset($poll) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    if (!xarSecurityCheck('EditPolls',1,'All',"$poll[title]:All:$pid")) {
        return;
    }

    // Check that option exists
    if (!isset($poll['options'][$opt])) {
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_DATA');
        return;
    }

    // Check for confirmation
    if (empty($confirm)) {
        // No confirmation yet - get one

        $data['polltitle'] = $poll['title'];
        $data['pid'] = $pid;
        $data['option'] = $poll['options'][$opt]['name'];
        $data['opt'] = $opt;
        $data['confirm'] = 1;
        $data['warning'] = '';
        $data['authid'] = xarSecGenAuthKey();


        if (($poll['type'] == 'single') &&
            ($poll['options'][$opt]['votes'] != 0)) {
            $data['warning'] = xarML('This option has votes.  Delete anyway?');
        }

        $data['buttonlabel'] = 'Delete Option';
        $data['cancelurl'] = xarModURL('polls', 'admin', 'display', array('pid' => $pid));

        return $data;
    }

    // Confirm authorisation code
    if (!xarSecConfirmAuthKey()) return;

    // Pass to API
    if (xarModAPIFunc('polls',
                     'admin',
                     'deleteopt',
                     array('pid' => $pid,
                           'opt' => $opt))) {
        // Success
        xarSessionSetVar('statusmsg', xarML('Deleted option'));

    }

    xarResponseRedirect(xarModURL('polls', 'admin', 'display', array('pid' => $pid)));

    return true;
}

?>