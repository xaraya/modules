<?php
/*
 *
 * Polls Module
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 *
 * @subpackage polls
 * @author Jim McDonalds, dracos, mikespub et al.
 */

/**
 * vote on an item
 */
function polls_user_vote($args)
{
    // Get parameters

    if (!xarVarFetch('pid', 'id', $pid, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('returnurl', 'str:0:', $returnurl, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('callingmod', 'str:0:', $callingmod, XARVAR_DONT_SET)) return;

    extract($args);

    if(empty($pid)){
        $msg = xarML('No poll specified');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
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
        xarErrorSet(XAR_USER_EXCEPTION,
                    'BAD_DATA',
                     new DefaultUserException($msg));
        return;
    }
    $options = array();
    // Get selected options
    if($poll['type'] == 'single'){
        xarVarFetch('option', 'isset', $opt, XARVAR_DONT_SET);
        $options[$opt] = $opt;
    }
    elseif($poll['type'] == 'multi'){
        for($i = 1; $i <= $poll['opts']; $i++){
            xarVarFetch('option_' . $i, 'isset', $opt[$i], XARVAR_DONT_SET);
            if($opt[$i] == $i){
                $options[$i] = $i;
            }
            $opt = '';
        }
    }
    if(count($options) == 0){
        $msg = xarML('No vote received');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }
    if(count($options) > 1 && $poll['type'] == 'single'){
        $msg = xarML('Multiple votes not allowed on this Poll.');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_DATA', new DefaultUserException($msg));
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
        xarErrorSet(XAR_USER_EXCEPTION,
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