<?php

/**
 * display item
 * This is a standard function to provide detailed informtion on a single item
 * available from the module.
 */
function polls_user_display($args)
{
    // Get parameters
    $pid = xarVarCleanFromInput('pid');

    extract($args);

    if (!isset($pid)) {
        $msg = xarML('Missing poll id');
        xarExceptionSet(XAR_USER_EXCEPTION,
                    'BAD_DATA',
                     new DefaultUserException($msg));
        return;
    }

    // Get item
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

    $data = array();
    $data['title'] = $poll['title'];
    $data['returnurl'] =  xarServerGetCurrentURL();

    // See if user is allowed to vote
    if (xarSecurityCheck('VotePolls',0,'All',"$poll[title]:All:$poll[pid]")){
        if ((xarModAPIFunc('polls', 'user', 'usercanvote', array('pid' => $pid)))) {
            // They have not voted yet, display voting options
            $data['canvote'] = 1;
            $data['type'] = $poll['type'];
            $data['private'] = $poll['private'];
            $data['resultsurl'] = xarModURL('polls',
                                  'user',
                                  'results',
                                  array('pid' => $poll['pid']));
            $data['previewresults'] = xarModGetVar('polls', 'previewresults');
        
            $data['authid'] = xarSecGenAuthKey('polls');
            $data['pid'] =  $poll['pid'];
            $data['options'] = $poll['options'];
        }
        else {
            // They have voted, display current results
            xarResponseRedirect(xarModURL('polls',
                                                 'user',
                                                 'results',
                                                 array('pid' => $pid)));
            return;
        }
    }
    else {
        $data['canvote'] = 0;
    }

    if ($poll['modid'] == xarModGetIDFromName('polls')) {
        // Let hooks know we're displaying a poll, so they can provide us with related stuff
        $item = $poll;
        $item['module'] = 'polls';
        $item['returnurl'] = xarModURL('polls','user', 'display', array('pid' => $poll['pid']));
        $hooks = xarModCallHooks('item','display', $poll['pid'], $item);

        $data['hookoutput'] = join('',$hooks);
    } else {
        $data['hookoutput'] = '';
    }

    $data['buttonlabel'] = xarML('Vote');
    // Return output
    return $data;
}

?>
