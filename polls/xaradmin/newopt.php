<?php

/**
 * display form for a new poll option
 */
function polls_admin_newopt()
{
    // Get parameters
    $pid = xarVarCleanFromInput('pid');
    if (!isset($pid) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    // Start output
    $data = array();

    $poll = xarModAPIFunc('polls',
                           'user',
                           'get',
                           array('pid' => $pid));

    if (!xarSecurityCheck('EditPolls',1,'All',"$poll[title]:All:$pid")) {
        return;
    }

    // Title
    $data['polltitle'] =  xarVarPrepHTMLDisplay($poll['title']);

    $data['authid'] = xarSecGenAuthKey();
    $data['pid'] = xarVarPrepForDisplay($pid);

    $data['buttonlabel'] = xarML('Create Option');
    $data['cancelurl'] = xarModURL('polls',
                            'admin',
                            'display',
                            array('pid' => $pid));

    return $data;
}

?>