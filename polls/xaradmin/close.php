<?php

/**
 * close a poll
 */
function polls_admin_close()
{
    // Get parameters
    $pid = xarVarCleanFromInput('pid');

    // Confirm authorisation code
    if (!xarSecConfirmAuthKey()) return;

    // Pass to API
    if (xarModAPIFunc('polls',
                     'admin',
                     'close',
                     array('pid' => $pid))) {
        // Success
        xarSessionSetVar('statusmsg', _POLLSCLOSEDPOLL);

    }

    xarResponseRedirect(xarModURL('polls', 'admin', 'list'));

    return true;
}

?>