<?php

/**
 * decrement position for a poll option
 */
function polls_admin_decopt()
{
    // Get parameters
    list($pid,
         $opt) = xarVarCleanFromInput('pid',
                                        'opt');
    if ((!isset($pid) || !isset($opt)) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    // Confirm authorisation code
    if (!xarSecConfirmAuthKey()) return;

    // Pass to API
    $incremented = xarModAPIFunc('polls', 'admin', 'decopt', array('pid' => $pid,
                                                         'opt' => $opt));
    if (!$incremented && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    // Redirect
    xarResponseRedirect(xarModURL('polls',
                        'admin',
                        'display',
                        array('pid' => $pid)));
    return true;
}

?>