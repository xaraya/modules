<?php

/**
 * create a new poll
 */
function polls_admin_create()
{
    // Get parameters
    list($title,
         $polltype,
         $private) = xarVarCleanFromInput('title',
                                      'polltype',
                                      'private');

    // Confirm authorisation code
    if (!xarSecConfirmAuthKey()) return;

    if (!xarSecurityCheck('AddPolls')) {
        return;
    }

    if (!isset($title) || !isset($polltype)){
        $msg = xarML('Missing required field title or polltype');
        xarExceptionSet(XAR_USER_EXCEPTION,
                    'MISSING_DATA',
                     new DefaultUserException($msg));
        return;
    }

    if ($polltype != 'single' && $polltype != 'multi'){
        $msg = xarML('Invalid poll type');
        xarExceptionSet(XAR_USER_EXCEPTION,
                    'MISSING_DATA',
                     new DefaultUserException($msg));
        return;
    }
    if ($private != 1){
        $private = 0;
    }

    // Pass to API
    $pid = xarModAPIFunc('polls',
                        'admin',
                        'create', array('title' => $title,
                                        'polltype' => $polltype,
                                        'private' => $private));
    if (!$pid) {
        // Something went wrong - return
        $msg = xarML('Unable to create poll');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'UNKNOWN');
        return;
    }

    $optlimit = xarModGetVar('polls', 'defaultopts');
    for ($i = 1; $i <= $optlimit; $i++) {
        $option = xarVarCleanFromInput("option_$i");
        if (!empty($option)) {
            xarModAPIFunc('polls',
                         'admin',
                         'createopt',
                         array('pid' => $pid,
                               'option' => $option));
        }
    }

    // Back to main page
    // Success
    xarSessionSetVar('polls_statusmsg', xarML('Poll Created Successfuly.'));
    xarResponseRedirect(xarModURL('polls', 'admin', 'list'));

    return true;
}

?>