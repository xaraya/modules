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
 * delete a poll option
 * @param $args['pid'] ID of poll
 * @param $args['optnum'] poll option number
 * @returns bool
 * @return true on success, false on failure
 */
function polls_adminapi_deleteopt($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if ((!isset($pid))  || (!isset($opt))) {
        throw new BadParameterException(array($pid,$option),'Missing Poll id (#(1)), or Options (#(2))');
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

    $sql = "DELETE FROM $pollsinfotable
            WHERE pid = ?
              AND optnum = ?";

    $result = $dbconn->Execute($sql, array((int)$pid, $opt));

    if (!$result) {
        return;
    }

    // Decrement number of options
    $new_votes = ($poll['votes'] - $votes);
    $pollstable = $xartable['polls'];
    $sql = "UPDATE $pollstable
            SET opts = opts - 1,
            votes = ?
            WHERE pid = ?";

    $result = $dbconn->Execute($sql, array((int)$new_votes,(int)$pid));

    if (!$result) {
        return;
    }

    // Resequence
   // xarModAPIFunc('polls','admin','resequence',array('pid' => $pid));

    return true;
}

?>
