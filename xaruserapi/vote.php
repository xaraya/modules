<?php
/**
 * Polls Module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Polls Module
 * @link http://xaraya.com/index.php/release/23.html
 * @author Jim McDonalds, dracos, mikespub et al.
 */
/**
 * vote on an item
 * @param $args['pid'] id of poll to vote on
 * @param $args['options'] options with vote information
 * @return array with item, or false on failure
 */
function polls_userapi_vote($args)
{
    // Get arguments from argument array
    extract($args);

    // Check args
    if ((!isset($pid) && !isset($poll)) || !isset($options)) {
        $msg = xarML('Missing poll ID or option');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_DATA', new DefaultUserException($msg));
        return;
    }

    // Get poll info, if not already passed in.
    if (!isset($poll)) $poll = xarModAPIFunc('polls', 'user', 'get', array('pid' => $pid));

    if (!$poll) {
        $msg = xarML('Error retrieving Poll data');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_DATA', new DefaultUserException($msg));
        return;
    }

    if (empty($pid)) $pid = $poll['pid'];

    // Confirm that the user has not already voted
    if (!xarModAPIFunc('polls', 'user', 'usercanvote', array('pid' => $pid))) {
        $msg = xarML('You have already voted on this poll');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_USER', new DefaultUserException($msg));
        return;
    }

    // Security check
    if (!xarSecurityCheck('VotePolls',0,'Polls',"$poll[title]:$poll[type]")) {
        return;
    }

    // Ensure poll is still open. Take into account the 'open' flag as well as the date ranges.
    if (!$poll['open'] || $poll['state'] == 'closed') {
        $msg = xarML('Poll is closed.');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_USER', new DefaultUserException($msg));
        return;
    }

    // Get datbase setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $pollsinfotable = $xartable['polls_info'];

    $voteinc = 0;

    if (!is_array($options)){
        $msg = xarML('Data type mismatch: array expected for $options');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_DATA', new DefaultUserException($msg));
        return;
    }
    if (count($options) == 0){
        $msg = xarML('No vote received');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    if ($poll['type'] == 'single') {
        if(count($options) != 1){
            $msg = xarML('Multiple votes not allowed on this Poll.');
            xarErrorSet(XAR_USER_EXCEPTION, 'BAD_DATA', new DefaultUserException($msg));
            return;
        }

        $option = array_shift($options) * 1;

        if(!is_int($option)){
            $msg = xarML('Data type mismatch: integer expected for option ID');
            xarErrorSet(XAR_USER_EXCEPTION, 'BAD_DATA', new DefaultUserException($msg));
            return;
        }
        if($option > $poll['opts'] || $option < 1){
            $msg = xarML('Invalid Vote');
            xarErrorSet(XAR_USER_EXCEPTION, 'BAD_DATA', new DefaultUserException($msg));
            return;
        }

        $sql = "UPDATE $pollsinfotable
                SET xar_votes = xar_votes + 1
                WHERE xar_pid = ?
                  AND xar_optnum = ?";
        $result = $dbconn->Execute($sql, array((int)$pid, $option));
        if (!$result) {
            return;
        }

        $voteinc++;
    } elseif ($poll['type'] == 'multi') {
        foreach($options as $option) {
            $option = $option * 1;
            if($option > $poll['opts'] || $option < 1){
                $msg = xarML('Invalid Vote');
                xarErrorSet(XAR_USER_EXCEPTION, 'BAD_DATA', new DefaultUserException($msg));
                return;
            }
            if(!is_int($option)){
                $msg = xarML('Data type mismatch: integer expected for option ID');
                xarErrorSet(XAR_USER_EXCEPTION, 'BAD_DATA', new DefaultUserException($msg));
                return;
            }

            $sql = "UPDATE $pollsinfotable
                    SET xar_votes = xar_votes + 1
                    WHERE xar_pid = ?
                      AND xar_optnum = ?";
            $result = $dbconn->Execute($sql, array((int)$pid, $option));
            if (!$result) {
                return;
            }
            $voteinc++;
        }
    } else {
        $msg = xarML('Poll type/vote mismatch');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_DATA', new DefaultUserException($msg));
        return;
    }

    $pollstable = $xartable['polls'];
    $pollscolumn = &$xartable['polls_column'];

    $sql = "UPDATE $pollstable
            SET xar_votes = xar_votes + $voteinc
            WHERE xar_pid = ?";
    $result = $dbconn->Execute($sql, array((int)$pid));

    if (!$result) {
        return;
    }

    if (xarUserIsLoggedIn()){
        $votes = xarModGetUserVar('polls', 'uservotes');

        if (!$votes) {
            $msg = xarML('Error retrieving vote status');
            xarErrorSet(XAR_USER_EXCEPTION, 'BAD_DATA', new DefaultUserException($msg));
            return;
        }

        $uservotes = @unserialize($votes);
        if (!is_array($uservotes)) $uservotes = array();

        $uservotes[$pid] = time();
        $voteupdate = xarModSetUserVar('polls', 'uservotes', serialize($uservotes));

        if (!$voteupdate) {
            $msg = xarML('Error updating vote status');
            xarErrorSet(XAR_USER_EXCEPTION, 'BAD_DATA', new DefaultUserException($msg));
            return;
        }

    } else{
        $votes = xarSessionGetVar('uservotes');

        if (is_string($votes)) {
            $uservotes = @unserialize($votes);
            if (!is_array($uservotes)) $uservotes = array();
        } else {
            $uservotes = array();
        }

        $uservotes[$pid] = time();
        $voteupdate = xarSessionSetVar('uservotes', serialize($uservotes));

        if (!$voteupdate) {
            $msg = xarML('Error updating vote status');
            xarErrorSet(XAR_USER_EXCEPTION, 'BAD_DATA', new DefaultUserException($msg));
            return;
        }
    }

    xarSessionSetVar('polls_statusmsg', xarML('Thank you for voting', 'polls'));

    return true;
}

?>