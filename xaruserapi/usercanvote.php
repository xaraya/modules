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
 * utility function to determine if current user has voted
 * @param $args['pid'] id of poll to vote on
 * @return bool
 * @return user vote status for a poll
 *          true user can vote
 *          false user cannot vote
 */
function polls_userapi_usercanvote($args)
{
    extract($args);

    // Check args
    if (!isset($pid)) {
        $msg = xarML('Missing poll ID in checkvote');
        xarErrorSet(XAR_USER_EXCEPTION,
                    'BAD_DATA',
                     new DefaultUserException($msg));
        return false;
    }
    if(xarUserIsLoggedIn()){
        $votes = xarModGetUserVar('polls', 'uservotes');
    }
    else{
        $votes = xarSessionGetVar("uservotes");
    }
    if(!is_string($votes)){
        return true;
    }
    $uservotes = unserialize($votes);
    if(!isset($uservotes[$pid])){
        return true;
    }
    else{
        $vote = $uservotes[$pid];
    }
    $poll = xarModAPIFunc('polls',
                           'user',
                           'get',
                           array('pid' => $pid));
    if(!$poll['open']){
        return false;
    }
    $now = time();
    $reset = $poll['reset'];

    $interval = xarModGetVar('polls', 'voteinterval');

    // Allow voting on a poll reset since vote, regardless of vote interval
    if($vote < $reset){
        return true;
    }
    switch($interval){
        case -1:
            if(isset($uservotes[$pid])){
                return false;
            }
            break;
        case 86400:
        case 604800:
            if($now < ($vote + $interval)){
                return false;
            }
            break;
        case 2592000:
            $votetime = getdate($vote);
            $nowtime = getdate($now);
            if(($nowtime['mon'] == $votetime['mon']) && ($nowtime['year'] == $votetime['year'])){
                return false;
            }
        default:
            $msg = xarML('Cannot determine vote status');
            xarErrorSet(XAR_USER_EXCEPTION,
                        'BAD_DATA',
                        new DefaultUserException($msg));
            return false;
    }
    return true;
}

?>
