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
        xarExceptionSet(XAR_USER_EXCEPTION,
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
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'UNKNOWN');
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
            WHERE ".$prefix."_pid = " . xarVarPrepForStore($pid) . "
            AND ".$prefix."_optnum = " . xarVarPrepForStore($opt);
    $result = $dbconn->Execute($sql);
    if(!$result){
        return;
    }
    $sql = "UPDATE $pollsinfotable
            SET ".$prefix."_optnum = " . xarVarPrepForStore($opt) . "
            WHERE ".$prefix."_pid = " . xarVarPrepForStore($pid) . "
            AND ".$prefix."_optnum = " . xarVarPrepForStore($opt + 1);
    $result = $dbconn->Execute($sql);
    if(!$result){
        return;
    }
    $sql = "UPDATE $pollsinfotable
            SET ".$prefix."_optnum = ".$prefix."_optnum - 899
            WHERE ".$prefix."_pid = " . xarVarPrepForStore($pid) . "
            AND ".$prefix."_optnum = " . xarVarPrepForStore($opt + 900);
    $result = $dbconn->Execute($sql);
    if(!$result){
        return;
    }

    return true;
}

?>