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
        xarErrorSet(XAR_USER_EXCEPTION,
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
            SET ".$prefix."_title = ?,
            ".$prefix."_type = ?,
            ".$prefix."_private = ?
            WHERE ".$prefix."_pid = ?";

    $bindvars = array($title, $type, $private, (int)$pid);
    $result = $dbconn->Execute($sql, $bindvars);

    if (!$result) {
        return;
    }

    return true;
}

?>
