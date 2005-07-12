<?php

function polls_admin_updateopt()
{
    // Get parameters
    list($pid,
         $opt,
         $option) = xarVarCleanFromInput('pid',
                                         'opt',
                                         'option');

    if ((!isset($pid) || !isset($opt) || !isset($option)) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    // Confirm authorisation code
    if (!xarSecConfirmAuthKey()) return;

    // Pass to API
    $updated = xarModAPIFunc('polls',
                     'admin',
                     'updateopt',
                     array('pid' => $pid,
                           'opt' => $opt,
                           'option' => $option));
    if(!$updated && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    xarResponseRedirect(xarModURL('polls',
                        'admin',
                        'display',
                        array('pid' => $pid)));
    return true;
}

?>