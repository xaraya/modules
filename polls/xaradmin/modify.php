<?php

/**
 * modify a poll
 */
function polls_admin_modify()
{
    // Get parameters
    $pid = xarVarCleanFromInput('pid');

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
    $data['authid'] = xarSecGenAuthKey();
    $data['pid'] = $pid;

    $data['buttonlabel'] = xarML('Modify Poll');

    $data['polltitle'] = xarVarPrepHTMLDisplay($poll['title']);
    $data['polltype'] = $poll['type'];
    $data['private'] = $poll['private'];

    $defaultopts = xarModGetVar('polls', 'defaultopts');
    if(count($poll['options']) > $defaultopts){
        $defaultopts = count($poll['options']);
    }

    $options = array();
    for($i = 1; $i <= $defaultopts;$i++){
        if(isset($poll['options'][$i])){
            $options[$i]['name'] = xarVarPrepHTMLDisplay($poll['options'][$i]['name']);
        }
        else{
            $options[$i]['name'] = '';
        }
        $options[$i]['field'] = "option_$i";
        $options[$i]['counter'] = $i;
    }

    $data['options'] = $options;

    return $data;
}

?>