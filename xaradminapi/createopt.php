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
 * create a poll option
 * @param $args['pid'] ID of poll
 * @param $args['option'] name of poll option
 * @param $args['votes'] number of votes for this option (import only)
 * @returns bool
 * @return true on success, false on failure
 */
function polls_adminapi_createopt($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if ((!isset($pid)) || (!isset($option))) {
        $msg = xarML('Missing poll ID or option');
        xarErrorSet(XAR_USER_EXCEPTION,
                    'BAD_DATA',
                     new DefaultUserException($msg));
        return;
    }

    // Get poll information
    $poll = xarModAPIFunc('polls', 'user', 'get', array('pid' => $pid));

    // Security check
    if (!xarSecurityCheck('EditPolls',1,'All',"$poll[title]:$poll[type]")) {
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $pollsinfotable = $xartable['polls_info'];

    $newitemnum = $poll['opts'] + 1;
    if (empty($votes)) {
        $votes = 0;
    }
    $sql = "INSERT INTO $pollsinfotable (
              xar_pid,
              xar_optnum,
              xar_votes,
              xar_optname)
            VALUES (?,?,?,?)";

    $bindvars = array($pid, $newitemnum, $votes, $option);
    $result = $dbconn->Execute($sql, $bindvars);

    if(!$result) {
        return;
    }

    $pollstable = $xartable['polls'];

    // Update poll information
    $sql = "UPDATE $pollstable
            SET xar_opts = xar_opts +1
            WHERE xar_pid = ?";

    $result2 = $dbconn->Execute($sql, array((int)$pid));
    if (!$result2) {
        return;
    }

    return true;
}

?>
