<?php

/**
 * update a poll
 * @param $args['pid'] ID of poll
 * @param $args['title'] ID of poll
 */
function polls_adminapi_update($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if ((!isset($pid)) ||
        (!isset($title)) ||
        (!isset($type))) {
        $msg = xarML('Missing poll ID, title, or type');
        xarExceptionSet(XAR_USER_EXCEPTION,
                    'BAD_DATA',
                     new DefaultUserException($msg));
        return;
    }
    if($private != 1){
        $private = 0;
    }

    // Get poll information
    $poll = xarModAPIFunc('polls', 'user', 'get', array('pid' => $pid));

    // Security check
    if (!xarSecurityCheck('EditPolls',1,'All',"$poll[title]:All:$pid")) {
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $pollstable = $xartable['polls'];
    $prefix = xarConfigGetVar('prefix');

    $sql = "UPDATE $pollstable
            SET ".$prefix."_title = '" . xarVarPrepForStore($title) . "',
            ".$prefix."_type = '" . xarVarPrepForStore($type) . "',
            ".$prefix."_private = '" . xarVarPrepForStore($private) . "'
            WHERE ".$prefix."_pid = " . xarVarPrepForStore($pid);
    $result = $dbconn->Execute($sql);

    if (!$result) {
        return;
    }

    return true;
}

?>