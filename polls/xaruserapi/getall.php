<?php

/**
 * get all polls
 * @returns array
 * @return array of items, or false on failure
 */
function polls_userapi_getall($args)
{
    // Get parameters from argument array
    extract($args);

    // Optional arguments.
    if (!isset($startnum)) {
        $startnum = 1;
    }
    if (!isset($numitems)) {
        $numitems = -1;
    }

    if ((!isset($startnum)) ||
        (!isset($numitems))) {
        $msg = xarML('Missing request parameters');
        xarExceptionSet(XAR_USER_EXCEPTION,
                    'BAD_DATA',
                     new DefaultUserException($msg));
        return;
    }

    $polls = array();

    // Security check
	if(!xarSecurityCheck('ListPolls')){
		return;
	}

    // Get datbase setup
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();
    $pollstable = $xartable['polls'];
    $prefix = xarConfigGetVar('prefix');

    // Get polls
    $sql = "SELECT ".$prefix."_pid,
                   ".$prefix."_title,
                   ".$prefix."_type,
                   ".$prefix."_open,
                   ".$prefix."_private,
                   ".$prefix."_modid,
                   ".$prefix."_votes,
                   ".$prefix."_reset
            FROM $pollstable
            WHERE ".$prefix."_modid = ".xarModGetIDFromName('polls')."
            ORDER BY ".$prefix."_pid DESC";
    $result = $dbconn->SelectLimit($sql, $numitems, $startnum-1);

    if (!$result) {
        return;
    }

    // Put polls into result array.
    for (; !$result->EOF; $result->MoveNext()) {
        list($pid, $title, $type, $open, $private, $modid, $votes, $reset) = $result->fields;
        if (xarSecurityCheck('ViewPolls',0,'All',"$title:All:$pid")) {
            $polls[] = array('pid' => $pid,
                             'title' => $title,
                             'type' => $type,
                             'open' => $open,
                             'private' => $private,
                             'modid' => $modid,
                             'votes' => $votes,
                             'reset' => $reset);
        }
    }

    $result->Close();

    // Return the items
    return $polls;
}

?>