<?php
/**
 * Polls Module
 *
 * @package modules
 * @copyright (C) 2002-2006, 2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage polls
 * @author Jim McDonalds, dracos, mikespub et al.
 */

/**
 * utility function to determine if current user has voted
 * @param $args['pid'] id of poll to vote on
 * @param $args['poll'] existing poll, can be passed in if available
 * @return bool
 * @return user vote status for a poll
 *          true user can vote
 *          false user cannot vote
 */
function polls_userapi_usercanvote($args)
{
    extract($args);

    // If a poll is passed in, then get the pid from it.
    if (!isset($pid) && isset($poll['pid'])) $pid = $poll['pid'];

    // Check args
    if (!isset($pid) && !isset($poll['pid'])) {
        $msg = xarML('Missing poll ID in checkvote');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_DATA', new DefaultUserException($msg));
        return false;
    }

    // Get the current voting status from the user settings or the session.
    if (xarUserIsLoggedIn()) {
        $votes = xarModGetUserVar('polls', 'uservotes');
    } else{
        $votes = xarSessionGetVar('uservotes');
    }

    // Get the vote time, if it is available.
    if (!is_string($votes)){
        $votetime = 0;
    } else {
        $uservotes = @unserialize($votes);
        if (!isset($uservotes[$pid])){
            $votetime = 0;
        } else{
            $votetime = $uservotes[$pid];
        }
    }

    // Fetch the poll, if we don't already have it.
    if (!isset($poll)) $poll = xarModAPIFunc('polls', 'user', 'get', array('pid' => $pid));

    // Nobody can vote on a closed poll.
    if (empty($poll['open'])) {
        return false;
    }

    // Everything past here involves a check against the voting time.
    // If there is no voting time, then the user ahs not voted, and
    // so is allowed to.
    if ($votetime == 0) {
        return true;
    }

    // Allow voting on a poll reset since vote, regardless of vote interval
    if ($votetime < $poll['reset']) {
        return true;
    }

    $now = time();
    $interval = xarModGetVar('polls', 'voteinterval');

    // FIXME: hard-coded durations could cause problems if they are extended at source.
    // Do we need a switch statement at all? Just a numeric check ought to be enough.
    switch($interval){
        case -1:
            // Only one vote ever permitted.
            // No further checks required.
            return false;

        // 24 hours.
        case 86400:
        // 7 days
        case 604800:
            if ($now < ($votetime + $interval)) {
                return false;
            }
            break;

        // 30 days (1 month)
        // CHECKME: the check just prevents voting within the same calendar month. Is that right?
        case 2592000:
            if (date('Y-m', $now) == date('Y-m', $votetime)) {
                return false;
            }
            break;

        default:
            // CHECKME: should we just say 'no'?
            $msg = xarML('Cannot determine vote status');
            xarErrorSet(XAR_USER_EXCEPTION, 'BAD_DATA', new DefaultUserException($msg));
            return false;
    }

    // Passed all the tests; yes we can vote.
    return true;
}

?>