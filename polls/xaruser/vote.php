<?php

/**
 * vote on an item
 */
function polls_user_vote($args)
{
    // Get parameters
    list($pid,
         $returnurl,
         $callingmod) = xarVarCleanFromInput('pid', 'returnurl', 'callingmod');
    extract($args);
    if(empty($pid)){
        $msg = xarML('No poll specified');
        xarExceptionSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    $canvote = xarModAPIFunc('polls',
                     'user',
                     'usercanvote',
                     array('pid' => $pid));
    if(!$canvote){
        xarSessionSetVar('polls_statusmsg', xarML('You cannot vote at this time.',
                    'polls'));
        if (!empty($returnurl)) {
            xarResponseRedirect($returnurl);
        } else {
            xarResponseRedirect(xarModURL('polls', 'user', 'results',
                                          array('pid' => $pid)));
        }
        return true;
    }
    // Confirm authorisation code
    if (!xarSecConfirmAuthKey()) return;

    $poll = xarModAPIFunc('polls',
                     'user',
                     'get',
                     array('pid' => $pid));

    if (!$poll) {
        $msg = xarML('Error retrieving Poll data');
        xarExceptionSet(XAR_USER_EXCEPTION,
                    'BAD_DATA',
                     new DefaultUserException($msg));
        return;
    }
    $options = array();
    // Get selected options
    if($poll['type'] == 'single'){
        $opt = xarVarCleanFromInput('option');
        $options[$opt] = $opt;
    }
    elseif($poll['type'] == 'multi'){
        for($i = 1; $i <= $poll['opts']; $i++){
            $opt = xarVarCleanFromInput('option_' . $i);
            if($opt == $i){
                $options[$i] = $i;
            }
            $opt = '';
        }
    }
    if(count($options) == 0){
        $msg = xarML('No vote received');
        xarExceptionSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }
    if(count($options) > 1 && $poll['type'] == 'single'){
        $msg = xarML('Multiple votes not allowed on this Poll.');
        xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_DATA', new DefaultUserException($msg));
        return;
    }

    // Pass to API
    $vote = xarModAPIFunc('polls',
                     'user',
                     'vote',
                     array('pid' => $pid,
                           'options' => $options));

    if (!$vote) {
        $msg = xarML('Error recording vote');
        xarExceptionSet(XAR_USER_EXCEPTION,
                    'BAD_DATA',
                     new DefaultUserException($msg));
        return;
    }
    // CHECKME: find some cleaner way to update the page cache if necessary
    if (function_exists('xarOutputFlushCached')) {
        if (isset($callingmod) && 
            xarModGetVar('xarcachemanager','FlushOnNewPollvote')) {
            xarPageFlushCached("$callingmod-user-display-");
        } else {
            xarOutputFlushCached("polls-user-");
        }
    }

    // Success, Redirect
    if (!empty($returnurl)) {
        xarResponseRedirect($returnurl);
    } else {
        xarResponseRedirect(xarModURL('polls', 'user', 'results', array('pid' => $pid)));
    }

    return true;
}

?>
