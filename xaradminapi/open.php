<?php
/*
 *
 * Polls Module
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage polls
 * @author Jim McDonalds, dracos, mikespub et al.
 */

/**
 * reopen a poll
 * @param $args['pid'] ID of poll
 */
function polls_adminapi_open($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if (!isset($pid)) {
        $msg = xarML('Missing poll');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_DATA', new DefaultUserException($msg));
        return;
    }

    // Get poll information
    $poll = xarModAPIFunc('polls', 'user', 'get', array('pid' => $pid));

    // Security check
    if (!xarSecurityCheck('AdminPolls',1,'All',"$poll[title]:$poll[type]")) {
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $pollstable = $xartable['polls'];
    $prefix = xarConfigGetVar('prefix');

    $sql = "UPDATE $pollstable
            SET xar_end_date = ?,
            xar_open = ?
            WHERE xar_pid = ?";
    $result = $dbconn->Execute($sql, array(strtotime('now +1 day'), 1, (int)$pid));

    if (!$result) {
        return;
    }

    return true;
}

?>