<?php

function polls_admin_modifyopt()
{
    // Get parameters
    list($pid,
         $opt) = xarVarCleanFromInput('pid',
                                        'opt');
    // Check arguments
    if (empty($pid) || empty($opt)) {
        $msg = xarML('No poll or option specified');
        xarExceptionSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    // Start output
    $data = array();

    // Get poll information
    $poll = xarModAPIFunc('polls', 'user', 'get', array('pid' => $pid));

    if (!$poll) {
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'UNKNOWN');
        return;
    }

    // Security check
    if (!xarSecurityCheck('EditPolls',1,'All',"$poll[title]:All:$pid")) {
        return;
    }

    // Title
    $data['polltitle'] = $poll['title'];
    $data['authid'] = xarSecGenAuthKey();
    $data['pid'] = xarVarPrepHTMLDisplay($pid);
    $data['opt'] = $opt;

    // Name
    $data['option'] = xarVarPrepHTMLDisplay($poll['options'][$opt]['name']);

    // End form

    $data['buttonlabel'] = xarML('Modify Option');
    $data['cancelurl'] = xarModURL('polls',
                            'admin',
                            'display',
                            array('pid' => $pid));

    return $data;
}

?>