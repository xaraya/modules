<?php
/**
 * Polls Module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage polls
 * @author Jim McDonalds, dracos, mikespub et al.
 */

/**
 * vote on a poll
 *
 * @param id $pid
 * @param returnurl
 * @param callingmod the module where the user comes from
 */
function polls_user_vote($args)
{
    // Get parameters

    if (!xarVarFetch('pid', 'id', $pid, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('returnurl', 'str:0:', $returnurl, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('callingmod', 'str:0:', $callingmod, XARVAR_DONT_SET)) return;

    extract($args);

    if(empty($pid)){
            throw new EmptyParameterException($pid,'Error retrieving Poll data, poll id must be set');
    }

    $canvote = xarModAPIFunc('polls',
                     'user',
                     'usercanvote',
                     array('pid' => $pid));
    if(!$canvote){
        xarSessionSetVar('polls_statusmsg', xarML('You cannot vote at this time.','polls'));
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
    // Get the poll
    $poll = xarModAPIFunc('polls',
                     'user',
                     'get',
                     array('pid' => $pid));

    if (!$poll) {
            throw new EmptyParameterException($pid,'Error retrieving Poll data, poll id (#(1)) not found');
    }
    $options = array();
    // Get selected options
    if($poll['type'] == '0'){
        xarVarFetch('option', 'isset', $opt, XARVAR_DONT_SET);
        $options[$opt] = $opt;
    }
    elseif($poll['type'] == '1'){
        for($i = 1; $i <= $poll['opts']; $i++){
            xarVarFetch('option_' . $i, 'isset', $opt[$i], XARVAR_DONT_SET);
            if($opt[$i] == $i){
                $options[$i] = $i;
            }
            $opt = '';
        }
    }
    if(count($options) == 0){
                throw new EmptyParameterException($options,'For voting a vote is necessary');
    }
    if(count($options) > 1 && $poll['type'] == 'single'){
          throw new EmptyParameterException($options,'Multiple votes not allowed on this Poll.');
    }

    // Pass vote to API
    $vote = xarModAPIFunc('polls',
                     'user',
                     'vote',
                     array('pid' => $pid,
                           'options' => $options));

    if (!$vote) {
            throw new EmptyParameterException($vote,'Error recording vote');
    }
    // CHECKME: find some cleaner way to update the page cache if necessary
    if (function_exists('xarOutputFlushCached')) {
        if (isset($callingmod) &&
            xarModVars::Get('xarcachemanager','FlushOnNewPollvote')) {
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
