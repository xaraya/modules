<?php

/**
 * update a poll option
 * @param $args['pid'] ID of poll
 * @param $args['optnum'] number of poll option
 * @param $args['optname'] name of poll option
 */
function polls_adminapi_updateopt($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if ((!isset($pid)) ||
        (!isset($opt)) ||
        (!isset($option))) {
        $msg = xarML('Missing poll ID, option ID, or option text');
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
    $pollsinfocolumn = &$xartable['polls_info_column'];
    $prefix = xarConfigGetVar('prefix');

    $sql = "UPDATE $pollsinfotable
            SET ".$prefix."_optname = '" . xarVarPrepForStore($option) . "'
            WHERE ".$prefix."_pid = " . xarVarPrepForStore($pid) . "
              AND ".$prefix."_optnum = " . xarVarPrepForStore($opt);
    $result = $dbconn->Execute($sql);

    if (!$result) {
        return;
    }

    return true;
}

?>