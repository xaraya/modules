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
 * close a poll
 * @param $args['pid'] ID of poll
 */
function polls_adminapi_close($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if (!isset($pid)) {
            throw new EmptyParameterException($pid,'Missing id, Poll id must be set');
    }

    // Get poll information
    $poll = xarModAPIFunc('polls', 'user', 'get', array('pid' => $pid));

    // Security check
    if (!xarSecurityCheck('AdminPolls',1,'All',"$poll[pid]:$poll[type]")) {
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $pollstable = $xartable['polls'];
//    $prefix = xarConfigVars::get('prefix');
    $prefix = xarConfigVars::get(null, 'prefix');

    $sql = "UPDATE $pollstable
            SET end_date = ?,
            open = ?
            WHERE pid = ?";
    $result = $dbconn->Execute($sql,array(time(),(int)0,(int)$pid));

    if (!$result) {
        return;
    }

    return true;
}


?>
