<?php

/**
 * reset a poll
 * @param $args['pid'] ID of poll
 * @returns bool
 * @return true on success, false on failure
 */
function polls_adminapi_reset($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if (!isset($pid)) {
        $msg = xarML('Missing poll ID');
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

    $sql = "UPDATE $pollsinfotable
            SET ".$prefix."_votes = 0
            WHERE ".$prefix."_pid = " . xarVarPrepForStore($pid);
    $result = $dbconn->Execute($sql);

    if (!$result) {
        return;
    }

    $pollstable = $xartable['polls'];

    $sql = "UPDATE $pollstable
            SET ".$prefix."_votes = 0,
            ".$prefix."_reset = ".xarVarPrepForStore(time())."
            WHERE ".$prefix."_pid = " . xarVarPrepForStore($pid);
    $result = $dbconn->Execute($sql);

    if (!$result) {
        return;
    }

    return true;
}

?>