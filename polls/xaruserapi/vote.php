<?php

/**
 * vote on an item
 * @param $args['pid'] id of poll to vote on
 * @param $args['opteselect'] id of poll to vote on
 * @returns array
 * @return item array, or false on failure
 */
function polls_userapi_vote($args)
{
    // Get arguments from argument array
    extract($args);

    // Check args
    if (!isset($pid) || !isset($options)) {
        $msg = xarML('Missing poll ID or option');
        xarExceptionSet(XAR_USER_EXCEPTION,
                    'BAD_DATA',
                     new DefaultUserException($msg));
        return;
    }

    // Confirm that the user has not already voted
    if (!xarModAPIFunc('polls',
                     'user',
                     'usercanvote',
                     array('pid' => $pid))) {
        $msg = xarML('You have already voted on this poll');
        xarExceptionSet(XAR_USER_EXCEPTION,
                    'BAD_USER',
                     new DefaultUserException($msg));
        return;
    }

    // Get information
    $poll = xarModAPIFunc('polls', 'user', 'get', array('pid' => $pid));

    if (!$poll) {
        $msg = xarML('Error retrieving Poll data');
        xarExceptionSet(XAR_USER_EXCEPTION,
                    'BAD_DATA',
                     new DefaultUserException($msg));
        return;
    }

    // Security check
    if (!xarSecurityCheck('VotePolls',1,'All',"$poll[title]:All:$poll[pid]")) {
        return;
    }

    // Ensure poll is still open
    if (!$poll['open']) {
        $msg = xarML('Poll is closed.');
        xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_USER', new DefaultUserException($msg));
        return;
    }

    // Get datbase setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $pollsinfotable = $xartable['polls_info'];
    $prefix = xarConfigGetVar('prefix');

    $voteinc = 0;

    if(!is_array($options)){
        $msg = xarML('Data type mismatch: array expected for $options');
        xarExceptionSet(XAR_USER_EXCEPTION,
                    'BAD_DATA',
                     new DefaultUserException($msg));
        return;
    }
    if(count($options) == 0){
        $msg = xarML('No vote received');
        xarExceptionSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    if ($poll['type'] == 'single') {
        if(count($options) != 1){
            $msg = xarML('Multiple votes not allowed on this Poll.');
            xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_DATA', new DefaultUserException($msg));
            return;
        }

        $option = array_shift($options) * 1;

        if(!is_int($option)){
            $msg = xarML('Data type mismatch: integer expected for option ID');
            xarExceptionSet(XAR_USER_EXCEPTION,
                         'BAD_DATA',
                         new DefaultUserException($msg));
			return;
        }
        if($option > $poll['opts'] || $option < 1){
            $msg = xarML('Invalid Vote');
            xarExceptionSet(XAR_USER_EXCEPTION,
                         'BAD_DATA',
                         new DefaultUserException($msg));
            return;
        }
        $sql = "UPDATE $pollsinfotable
                SET ".$prefix."_votes = ".$prefix."_votes + 1
                WHERE ".$prefix."_pid = " . xarVarPrepForStore($pid) . "
                  AND ".$prefix."_optnum = " . xarVarPrepForStore($option);
        $result = $dbconn->Execute($sql);
	    if (!$result) {
	        return;
	    }

        $voteinc++;
    }
    elseif ($poll['type'] == 'multi') {
        foreach($options as $option) {
            $option = $option * 1;
            if($option > $poll['opts'] || $option < 1){
                $msg = xarML('Invalid Vote');
                xarExceptionSet(XAR_USER_EXCEPTION,
                             'BAD_DATA',
                             new DefaultUserException($msg));
                return;
            }
            if(!is_int($option)){
                $msg = xarML('Data type mismatch: integer expected for option ID');
                xarExceptionSet(XAR_USER_EXCEPTION,
                             'BAD_DATA',
                             new DefaultUserException($msg));
                return;
            }
            $sql = "UPDATE $pollsinfotable
                    SET ".$prefix."_votes = ".$prefix."_votes + 1
                    WHERE ".$prefix."_pid = " . xarVarPrepForStore($pid) . "
                      AND ".$prefix."_optnum = " . xarVarPrepForStore($option);
            $result = $dbconn->Execute($sql);
            if (!$result) {
			    return;
			}
            $voteinc++;
        }
    }
    else {
        $msg = xarML('Poll type/vote mismatch');
        xarExceptionSet(XAR_USER_EXCEPTION,
                    'BAD_DATA',
                     new DefaultUserException($msg));
        return;
    }

    $pollstable = $xartable['polls'];
    $pollscolumn = &$xartable['polls_column'];

    $sql = "UPDATE $pollstable
            SET ".$prefix."_votes = ".$prefix."_votes + $voteinc
            WHERE ".$prefix."_pid = " . xarVarPrepForStore($pid);
    $result = $dbconn->Execute($sql);

    if (!$result) {
        return;
    }

    if(xarUserIsLoggedIn()){
        $votes = xarModGetUserVar('polls', 'uservotes');
        if (!$votes) {
            $msg = xarML('Error retrieving vote status');
            xarExceptionSet(XAR_USER_EXCEPTION,
                        'BAD_DATA',
                         new DefaultUserException($msg));
            return;
        }
        $uservotes = unserialize($votes);
        $uservotes[$pid] = time();
        $voteupdate = xarModSetUserVar('polls', 'uservotes', serialize($uservotes));
        if (!$voteupdate) {
            $msg = xarML('Error updating vote status');
            xarExceptionSet(XAR_USER_EXCEPTION,
                        'BAD_DATA',
                         new DefaultUserException($msg));
            return;
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
            $msg = xarML('Error updating vote status');
            xarExceptionSet(XAR_USER_EXCEPTION,
                        'BAD_DATA',
                         new DefaultUserException($msg));
            return;
        }
    }

    xarSessionSetVar('polls_statusmsg', xarML('Thank you for voting', 'polls'));

    return true;
}

?>