<?php

/**
 * update a poll
 */
function polls_admin_update()
{

    // Get parameters
    list($pid,
         $title,
         $type,
         $private) = xarVarCleanFromInput('pid',
                                      'title',
                                      'polltype',
                                      'private');

    // Confirm authorisation code
    if (!xarSecConfirmAuthKey()) return;

    if($private != 1){
        $private = 0;
    }

    // Get poll info
    $poll = xarModAPIFunc('polls', 'user', 'get', array('pid' => $pid));

    if (!$poll) {
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'UNKNOWN');
        return;
    }

    // security check
    if (!xarSecurityCheck('EditPolls',1,'All',"$poll[title]:All:$pid")) {
        return;
    }
    $options = $poll['options'];

    // Pass to API
    $updated = xarModAPIFunc('polls',
                      'admin',
                      'update',
                      array('pid' => $pid,
                           'title' => $title,
                           'type' => $type,
                           'private' => $private));
    if(!$updated){
       return false;
    }

    // Success
    xarSessionSetVar('statusmsg', xarML('Poll Updated'));

    xarResponseRedirect(xarModURL('polls', 'admin', 'list'));

    return true;
}

?>