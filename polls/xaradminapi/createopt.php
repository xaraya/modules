<?php

/**
 * create a poll option
 * @param $args['pid'] ID of poll
 * @param $args['option'] name of poll option
 * @param $args['votes'] number of votes for this option (import only)
 * @returns bool
 * @return true on success, false on failure
 */
function polls_adminapi_createopt($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if ((!isset($pid)) || (!isset($option))) {
        $msg = xarML('Missing poll ID or option');
        xarExceptionSet(XAR_USER_EXCEPTION,
                    'BAD_DATA',
                     new DefaultUserException($msg));
        return;
    }

    // Get poll information
    $poll = xarModAPIFunc('polls', 'user', 'get', array('pid' => $pid));

    // Security check
    if (!xarSecurityCheck('EditPolls',1,'All',"$poll[title]:All:$pid")) {
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $pollsinfotable = $xartable['polls_info'];
    $prefix = xarConfigGetVar('prefix');

    $newitemnum = $poll['opts'] + 1;
    if (empty($votes)) {
        $votes = 0;
    }
    $sql = "INSERT INTO $pollsinfotable (
              ".$prefix."_pid,
              ".$prefix."_optnum,
              ".$prefix."_votes,
              ".$prefix."_optname)
            VALUES (
              " . xarVarPrepForStore($pid) . ",
              " . xarVarPrepForStore($newitemnum) . ",
              " . xarVarPrepForStore($votes) . ",
              '" . xarVarPrepForStore($option) . "')";
    $result = $dbconn->Execute($sql);

    if(!$result) {
        return;
    }

    $pollstable = $xartable['polls'];

    // Update poll information
    $sql = "UPDATE $pollstable
            SET ".$prefix."_opts = ".$prefix."_opts +1
            WHERE ".$prefix."_pid = " . xarVarPrepForStore($pid);

    $result2 = $dbconn->Execute($sql);
    if (!$result2) {
        return;
    }

    return true;
}

?>
