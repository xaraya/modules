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
    if (!isset($pid) || !isset($options)) {
        throw new BadParameterException(array($pid,$options),'Missing Poll id (#(1)), or Options (#(2))');
    }

    // Confirm that the user has not already voted
    if (!xarModAPIFunc('polls',
                     'user',
                     'usercanvote',
                     array('pid' => $pid))) {
        $msg = xarML('You have already voted on this poll');
            throw new EmptyParameterException($pid,'You have already voted on this poll');
    }

    // Get information
    $poll = xarModAPIFunc('polls', 'user', 'get', array('pid' => $pid));

    if (!$poll) {
              throw new EmptyParameterException($pid,'Error retrieving Poll data, poll id (#(1)) not found');
    }

    // Security check
    if (!xarSecurityCheck('VotePolls',0,'Polls',"$poll[pid]:$poll[type]")) {
        return;
    }

    // Ensure poll is still open
    if (!$poll['open']) {
              throw new EmptyParameterException($pid,'Poll is closed');
    }

    // Get datbase setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $pollsinfotable = $xartable['polls_info'];

    $voteinc = 0;

    if(!is_array($options)){
        $msg = xarML('Data type mismatch: array expected for $options');
        xarErrorSet(USER_EXCEPTION,
                    'BAD_DATA',
                     new DefaultUserException($msg));
        return;
    }
    if(count($options) == 0){
                throw new EmptyParameterException($options,'For voting a vote is necessary');
    }

    if ($poll['type'] == '0') {
        if(count($options) != 1){
                        throw new EmptyParameterException($options,'Multiple votes not allowed on this Poll.');
        }

        $option = array_shift($options) * 1;

        if(!is_int($option)){
                        throw new EmptyParameterException($options,'Data type mismatch: integer expected for option ID');
        }
        if($option > $poll['opts'] || $option < 1){
            throw new EmptyParameterException($options,'Invalid Vote');
        }
        $sql = "UPDATE $pollsinfotable
                SET votes = votes + 1
                WHERE pid = ?
                  AND optnum = ?";
        $result = $dbconn->Execute($sql, array((int)$pid, $option));
        if (!$result) {
            return;
        }

        $voteinc++;
    }
    elseif ($poll['type'] == '1') {
        foreach($options as $option) {
            $option = $option * 1;
            if($option > $poll['opts'] || $option < 1){
                            throw new EmptyParameterException($options,'Invalid Vote');
        }
            if(!is_int($option)){
                throw new EmptyParameterException($options,'Data type mismatch: integer expected for option ID');
                }
            $sql = "UPDATE $pollsinfotable
                    SET votes = votes + 1
                    WHERE pid = ?
                      AND optnum = ?";
            $result = $dbconn->Execute($sql, array((int)$pid, $option));
            if (!$result) {
                return;
            }
            $voteinc++;
        }
    } else {
        $msg = xarML('Poll type/vote mismatch');
                throw new EmptyParameterException($options,'Poll type/vote mismatch');
        }

    $pollstable = $xartable['polls'];
    $pollscolumn = &$xartable['polls_column'];

    $sql = "UPDATE $pollstable
            SET votes = votes + $voteinc
            WHERE pid = ?";
    $result = $dbconn->Execute($sql, array((int)$pid));

    if (!$result) {
        return;
    }

    if(xarUserIsLoggedIn()){
        $votes = xarModUserVars::get('polls', 'uservotes');
        if (!$votes) {
            throw new EmptyParameterException($votes,'Error retrieving vote status');
        }
        $uservotes = unserialize($votes);
        $uservotes[$pid] = time();
        $voteupdate = xarModUserVars::set('polls', 'uservotes', serialize($uservotes));

        if (!$voteupdate) {
            throw new EmptyParameterException($voteupdate,'Error updating vote status');
        }

    }
    else{
        $votes = xarSessionGetVar('uservotes');
        if (is_string($votes)) {
            $uservotes = unserialize($votes);
        }
        else {
            $uservotes = array();
        }
        $uservotes[$pid] = time();
        $voteupdate = xarSessionSetVar('uservotes', serialize($uservotes));
        if (!$voteupdate) {
            throw new EmptyParameterException($voteupdate,'Error updating vote status');
        }
    }

    xarSessionSetVar('polls_statusmsg', xarML('Thank you for voting', 'polls'));

    return true;
}

?>
