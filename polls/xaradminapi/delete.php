<?php

/**
 * delete a poll
 * @param $args['pid'] ID of poll
 * @returns bool
 * @return true on success, false on failure
 */
function polls_adminapi_delete($args)
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
    if (!xarSecurityCheck('DeletePolls',1,'All',"$poll[title]:All:$pid")) {
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $pollsinfotable = $xartable['polls_info'];
    $prefix = xarConfigGetVar('prefix');

    $sql = "DELETE FROM $pollsinfotable
            WHERE ".$prefix."_pid = " . xarVarPrepForStore($pid);
    $result = $dbconn->Execute($sql);

    if (!$result) {
        return;
    }

    $pollstable = $xartable['polls'];

    $sql = "DELETE FROM $pollstable
            WHERE ".$prefix."_pid = " . xarVarPrepForStore($pid);
    $result = $dbconn->Execute($sql);

    if (!$result) {
        return;
    }

    return true;
}

?>