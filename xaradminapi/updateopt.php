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
    if ((!isset($pid)) || (!isset($opt)) || (!isset($option))) {
        $msg = xarML('Missing poll ID, option ID, or option text');
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
    $pollsinfocolumn = &$xartable['polls_info_column'];

    $sql = "UPDATE $pollsinfotable
            SET xar_optname = ?
            WHERE xar_pid = ?
              AND xar_optnum = ?";
    $bindvars = array($option, (int)$pid, $opt);
    $result = $dbconn->Execute($sql, $bindvars);

    if (!$result) {
        return;
    }

    return true;
}

?>
