<?php
/**
 * Display a single poll
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
 * Display poll
 *
 * This is a standard function to provide detailed information on a single poll
 * When a user is allowed to vote, a vote link is presented
 * When a user is allowed to see the results, a result link is presented
 *
 * @param id $pid Poll id
 */
function polls_user_display($args)
{

    if (!xarVarFetch('pid', 'id', $pid)) return;

    extract($args);

    if (!isset($pid)) {
            throw new EmptyParameterException($pid,'Poll id (#(1)) not found');
    }

    // Get item
    $poll = xarModAPIFunc('polls',
                           'user',
                           'get',
                           array('pid' => $pid));

    if (!$poll) {
            throw new EmptyParameterException($pid,'Error retrieving Poll data, poll id (#(1)) not found');
        return;
    }

    $data = array();
    $data['title'] = $poll['title'];
    $data['returnurl'] =  xarServerGetCurrentURL();

    // See if user is allowed to vote
    if (xarSecurityCheck('VotePolls',0,'Polls',"$poll[pid]:$poll[type]")){

        if ((xarModAPIFunc('polls', 'user', 'usercanvote', array('pid' => $pid)))) {
            // They have not voted yet, display voting options
            $data['canvote'] = 1;
            $data['type'] = $poll['type'];
            $data['private'] = $poll['private'];
            $data['resultsurl'] = xarModURL('polls',
                                  'user',
                                  'results',
                                  array('pid' => $poll['pid']));
            $data['previewresults'] = xarModVars::Get('polls', 'previewresults');

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
