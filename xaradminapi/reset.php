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
 * reset a poll
 * @param $args['pid'] ID of poll
 * @returns bool
 * @return true on success, false on failure
 */
function polls_adminapi_reset($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if (!isset($pid)) {
            throw new IDNotFoundException($pid,'Unable to find poll id (#(1))');
    }

    // Get poll information
    $poll = xarModAPIFunc('polls', 'user', 'get', array('pid' => $pid));

    // Security check
    if (!xarSecurityCheck('EditPolls',1,'All',"$poll[pid]:$poll[type]")) {
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $pollsinfotable = $xartable['polls_info'];
    //$prefix = xarConfigVars::get('prefix');
    $prefix = xarConfigVars::get(null, 'prefix');

    $sql = "UPDATE $pollsinfotable
            SET votes = 0
            WHERE pid = ?";
    $result = $dbconn->Execute($sql, array((int)$pid));

    if (!$result) {
        return;
    }

    $pollstable = $xartable['polls'];

    $sql = "UPDATE $pollstable
            SET votes = 0,
            reset = ".time()."
            WHERE pid = ?";
    $result = $dbconn->Execute($sql, array((int)$pid));

    if (!$result) {
        return;
    }

    return true;
}

?>
