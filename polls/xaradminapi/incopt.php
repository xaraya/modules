<?php

/**
 * increment poll option position
 * @param $args['pid'] the ID of the poll to increment
 * @param $args['optnum'] the number of the option to increment
 * @returns bool
 * @return true on success, false on failure
 */
function polls_adminapi_incopt($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if ((!isset($pid)) ||
        (!isset($opt))) {
        $msg = xarML('Missing poll ID or option');
        xarErrorSet(XAR_USER_EXCEPTION,
                    'BAD_DATA',
                     new DefaultUserException($msg));
        return;
    }

    // Get poll information
    $poll = xarModAPIFunc('polls',
                           'user',
                           'get',
                           array('pid' => $pid));

    if (!$poll) {
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'UNKNOWN');
        return;
    }

    // Security check
    if (!xarSecurityCheck('EditPolls',1,'All',"$poll[title]:All:$pid")) {
        return;
    }


    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $pollsinfotable = $xartable['polls_info'];
    $prefix = xarConfigGetVar('prefix');

    // Swap positions - three updates
    $sql = "UPDATE $pollsinfotable
            SET ".$prefix."_optnum = ".$prefix."_optnum + 900
            WHERE ".$prefix."_pid = ?
            AND ".$prefix."_optnum = ?";
    $result = $dbconn->Execute($sql, array((int)$pid, $opt));
    if(!$result){
        return;
    }
    $opt2=$opt - 1;
    $sql = "UPDATE $pollsinfotable
            SET ".$prefix."_optnum = ?
            WHERE ".$prefix."_pid = ?
            AND ".$prefix."_optnum = ?";
    $result = $dbconn->Execute($sql, array($opt, (int)$pid, $opt2));
    if(!$result){
        return;
    }
    $opt2=$opt + 900;
    $sql = "UPDATE $pollsinfotable
            SET ".$prefix."_optnum = ".$prefix."_optnum - 901
            WHERE ".$prefix."_pid = ?
            AND ".$prefix."_optnum = ?";
    $result = $dbconn->Execute($sql, array((int)$pid, $opt2));
    if(!$result){
        return;
    }

    return true;
}

?>
