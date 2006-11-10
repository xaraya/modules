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
        xarErrorSet(XAR_USER_EXCEPTION,
                    'BAD_DATA',
                     new DefaultUserException($msg));
        return;
    }

    // Get poll information
    $poll = xarModAPIFunc('polls', 'user', 'get', array('pid' => $pid));

    // Security check
    if (!xarSecurityCheck('DeletePolls',1,'All',"$poll[title]:$poll[type]")) {
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $pollsinfotable = $xartable['polls_info'];
    $prefix = xarConfigGetVar('prefix');

    $sql = "DELETE FROM $pollsinfotable
            WHERE xar_pid = ?";
    $result = $dbconn->Execute($sql, array((int)$pid));

    if (!$result) {
        return;
    }

    $pollstable = $xartable['polls'];

    $sql = "DELETE FROM $pollstable
            WHERE xar_pid = ?";
    $result = $dbconn->Execute($sql, array((int)$pid));
    if (!$result) {
        return;
    }

    $args['pid'] = $pid;
    $args['module'] = 'polls';
    $args['itemtype'] = 0;
    $args['itemid'] = $pid;

    xarModCallHooks('item', 'delete', $pid, $args);

    return true;
}

?>
