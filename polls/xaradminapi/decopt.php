<?php

/**
 * decrement poll option position
 * @param $args['pid'] the ID of the poll to decrement
 * @param $args['optnum'] the number of the option to decrement
 * @returns bool
 * @return true on success, false on failure
 */
function polls_adminapi_decopt($args)
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

    if (empty($poll)) {
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
    $pollsinfocolumn = &$xartable['polls_info_column'];
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
    $optplusone=$opt + 1;
    $sql = "UPDATE $pollsinfotable
            SET ".$prefix."_optnum = ?
            WHERE ".$prefix."_pid = ?
            AND ".$prefix."_optnum = ?";
    $result = $dbconn->Execute($sql, array($opt, (int)$pid, $optplusone));
    if(!$result){
        return;
    }
    $optplus=$opt + 900;
    $sql = "UPDATE $pollsinfotable
            SET ".$prefix."_optnum = ".$prefix."_optnum - 899
            WHERE ".$prefix."_pid = ?
            AND ".$prefix."_optnum =?";
    $result = $dbconn->Execute($sql, array((int)$pid, $optplus));
    if(!$result){
        return;
    }

    return true;
}

?>
