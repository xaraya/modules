<?php

/**
 * close a poll
 * @param $args['pid'] ID of poll
 */
function polls_adminapi_close($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if (!isset($pid)) {
        $msg = xarML('Missing poll');
        xarExceptionSet(XAR_USER_EXCEPTION,
                    'BAD_DATA',
                     new DefaultUserException($msg));
        return;
    }

    // Get poll information
    $poll = xarModAPIFunc('polls', 'user', 'get', array('pid' => $pid));

    // Security check
    if (!xarSecurityCheck('AdminPolls',1,'All',"$poll[title]:All:$pid")) {
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $pollstable = $xartable['polls'];
    $prefix = xarConfigGetVar('prefix');

    $sql = "UPDATE $pollstable
            SET ".$prefix."_open = 0
            WHERE ".$prefix."_pid = " . xarVarPrepForStore($pid);
    $result = $dbconn->Execute($sql);

    if (!$result) {
        return;
    }

    return true;
}


?>