<?php
/*
 *
 * Polls Module
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 *
 * @subpackage polls
 * @author Jim McDonalds, dracos, mikespub et al.
 */

/**
 * get all polls
 * @param $args['modid'] module id for the polls to get
 * @returns array
 * @return array of items, or false on failure
 */
function polls_userapi_getall($args)
{
    // Get parameters from argument array
    extract($args);

    $polls = array();

    // Security check
    if(!xarSecurityCheck('ListPolls')){
        return;
    }

    // Get datbase setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $pollstable = $xartable['polls'];

    $bindvars = array();

    if (isset($status) && is_numeric($status)) {
        $where = " WHERE xar_open = ?";
        $bindvars[]= (int) $status;
        if (isset($hook) && is_numeric($hook)) {
            $where .= " AND xar_itemid = ?";
            $bindvars[]= (int) $hook;
            }
    } else {
        if (isset($modid) && is_numeric($modid)) {
        $where = " WHERE xar_modid = ?";
        $bindvars[]= (int) $modid;
    } else {
        $where = '';
    }
    }
    // Get polls
    $sql = "SELECT xar_pid,
                   xar_title,
                   xar_type,
                   xar_open,
                   xar_private,
                   xar_modid,
                   xar_itemtype,
                   xar_itemid,
                   xar_votes,
                   xar_reset
            FROM $pollstable
            $where
            ORDER BY xar_pid DESC";
    $result = $dbconn->execute($sql, $bindvars);

    if (!$result) {
        return;
    }

    // Put polls into result array.
    for (; !$result->EOF; $result->MoveNext()) {
        list($pid, $title, $type, $open, $private, $modid, $itemtype, $itemid, $votes, $reset) = $result->fields;
        if (xarSecurityCheck('ViewPolls',0,'All',"$title:All:$pid")) {
            $polls[] = array('pid' => $pid,
                             'title' => $title,
                             'type' => $type,
                             'open' => $open,
                             'private' => $private,
                             'modid' => $modid,
                             'itemtype' => $itemtype,
                             'itemid' => $itemid,
                             'votes' => $votes,
                             'reset' => $reset);
        }
    }

    $result->Close();

    // Return the items
    return $polls;
}

?>