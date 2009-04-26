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
 * show results
 *
 * Show the results for a single poll
 *
 * @param id $pid poll id
 */
function polls_user_results($args)
{
    // Get parameters
    if (!xarVarFetch('pid', 'id', $pid, XARVAR_DONT_SET)) return;

    extract($args);

    if (!xarSecurityCheck('ViewPolls')){
        return;
    }

    if (!isset($pid)) {
        $msg = xarML('Missing poll ID');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_DATA', new DefaultUserException($msg));
        return;
    }

    // Get item
    $poll = xarModAPIFunc('polls', 'user', 'get', array('pid' => $pid));

    if (empty($poll)) {
        $msg = xarML('Error retrieving Poll data');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_DATA', new DefaultUserException($msg));
        return;
    }

    // Check if user can vote on this poll.
    // Pass the poll in to avoid another database query.
    $canvote = xarModAPIFunc('polls', 'user', 'usercanvote', array('poll' => $poll));

    // If results can be previewed before voting, then just display the results immediately.
    if (!xarModGetVar('polls', 'previewresults') && $canvote && $poll['state'] == 'open'){
        xarResponseRedirect(xarModURL('polls', 'user', 'display', array('pid' => $pid)));
    }

    // Check voting permission.
    if ($canvote && !xarSecurityCheck('VotePolls', 0, 'Polls', "$poll[title]:$poll[type]")) {
        $canvote = 0;
    }

    $data = $poll;

    // Number of participants
    $data['totalvotes'] = $poll['votes'];

    $data['voteurl'] = xarModURL('polls', 'user', 'display', array('pid' => $pid));
    $data['listurl'] = xarModURL('polls', 'user', 'list', array('pid' => $pid));

    $data['showtotalvotes'] = xarModGetVar('polls', 'showtotalvotes');
    $voteinterval = xarModGetVar('polls', 'voteinterval');

    $data['canvote'] = $canvote;

    if ($voteinterval == 86400){
        $data['votelimit'] = xarML('per day');
    } elseif($voteinterval == 604800){
        $data['votelimit'] = xarML('per week');
    } elseif($voteinterval == 2592000){
        $data['votelimit'] = xarML('per month');
    } else{
        $data['votelimit'] = xarML('per user');
    }

    // Add the bar widths to the list of results.
    // TODO: this can go into the getall() API to avoid duplication.

    // 0=never; 1=blocks; 2=module; 3=blocks and modules
    // TODO: this check could be moved to the templates
    $imggraph = xarModGetVar('polls', 'imggraph');
    $data['imggraph'] = ($imggraph >= 2 ? 1 : 0);


    if ($poll['modid'] == xarModGetIDFromName('polls')) {
        // Let hooks know we're displaying a poll, so they can provide us with related stuff
        $item = $poll;
        $item['module'] = 'polls';
        $item['returnurl'] = xarModURL('polls','user', 'results', array('pid' => $poll['pid']));
        $hooks = xarModCallHooks('item', 'display', $poll['pid'], $item);

        // Return hook data as both a string and an array.
        $data['hookoutput'] = trim(join('', $hooks));
        $data['hooks'] = $hooks;
    } else {
        $data['hookoutput'] = '';
    }

    // Return data to the template.
    return $data;
}

?>