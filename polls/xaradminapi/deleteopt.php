<?php

/**
 * delete a poll option
 * @param $args['pid'] ID of poll
 * @param $args['optnum'] poll option number
 * @returns bool
 * @return true on success, false on failure
 */
function polls_adminapi_deleteopt($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if ((!isset($pid))  ||
        (!isset($opt))) {
        $msg = xarML('Missing poll ID or option ID');
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

    $sql = "DELETE FROM $pollsinfotable
            WHERE ".$prefix."_pid = " . xarVarPrepForStore($pid) . "
              AND ".$prefix."_optnum = " . xarVarPrepForStore($opt);

    $result = $dbconn->Execute($sql);

    if (!$result) {
        return;
    }

    // Decrement number of options
    $pollstable = $xartable['polls'];
    $sql = "UPDATE $pollstable
            SET ".$prefix."_opts = ".$prefix."_opts - 1
            WHERE ".$prefix."_pid = " . xarVarPrepForStore($pid);

    $result = $dbconn->Execute($sql);

    if (!$result) {
        return;
    }

    // Resequence
    xarModAPIFunc('polls','admin','resequence',array('pid' => $pid));

    return true;
}

?>