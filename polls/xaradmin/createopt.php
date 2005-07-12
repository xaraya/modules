<?php

/**
 * create new poll option
 */
function polls_admin_createopt()
{
    // Get parameters
    list($pid,
         $option) = xarVarCleanFromInput('pid',
                                         'option');
    if (!isset($pid) || !isset($option) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    // Confirm authorisation code
    if (!xarSecConfirmAuthKey()) return;

    $poll = xarModAPIFunc('polls',
                           'user',
                           'get', array('pid' => $pid));

    if (!isset($poll) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    if (!xarSecurityCheck('EditPolls',1,'All',"$poll[title]:All:$pid")) {
        return;
    }

    // Pass to API
    $created = xarModAPIFunc('polls',
                           'admin',
                           'createopt', array('pid' => $pid,
                                              'option' => $option));
    if (!$created && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    // Success
    xarSessionSetVar('statusmsg', xarML('Created option'));

    xarResponseRedirect(xarModURL('polls',
                        'admin',
                        'display',
                        array('pid' => $pid)));

    return true;
}

?>