<?php

/**
 * increment position for a poll option
 */
function polls_admin_incopt()
{
    // Get parameters
    list($pid,
         $opt) = xarVarCleanFromInput('pid',
                                        'opt');
    if (!isset($pid) || !isset($opt) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    // Confirm authorisation code
    if (!xarSecConfirmAuthKey()) return;

    // Pass to API
    $incremented = xarModAPIFunc('polls', 'admin', 'incopt', array('pid' => $pid,
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