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
        $where = " WHERE $pollstable.xar_open = ?";
        $bindvars[]= (int) $status;
        if (isset($hook) && is_numeric($hook)) {
            $where .= " AND $pollstable.xar_itemid = ?";
            $bindvars[]= (int) $hook;
            }
    } else {
        if (isset($modid) && is_numeric($modid)) {
        $where = " WHERE $pollstable.xar_modid = ?";
        $bindvars[]= (int) $modid;
    } else {
        $where = '';
    }
    }
    if (!empty($catid) && xarModIsHooked('categories','polls')) {
        // Get the LEFT JOIN ... ON ...  and WHERE parts from categories
        $categoriesdef = xarModAPIFunc('categories','user','leftjoin',
                                       array('modid' => xarModGetIDFromName('polls'),
                                             'catid' => $catid));
        if (!empty($categoriesdef)) {
            $catwhere = " LEFT JOIN $categoriesdef[table]
                          ON $categoriesdef[field] = xar_pid
                          $categoriesdef[more]
                          WHERE $categoriesdef[where] ";
            if (empty($where)) {
                $where = $catwhere;
            } else {
                $where = preg_replace('/ WHERE /','',$where);
                $where = $catwhere . ' AND ' . $where;
            }
        }
    }
    // Get polls
    $sql = "SELECT $pollstable.xar_pid,
                   $pollstable.xar_title,
                   $pollstable.xar_type,
                   $pollstable.xar_open,
                   $pollstable.xar_private,
                   $pollstable.xar_modid,
                   $pollstable.xar_itemtype,
                   $pollstable.xar_itemid,
                   $pollstable.xar_votes,
                   $pollstable.xar_reset
            FROM $pollstable
            $where
            ORDER BY $pollstable.xar_pid DESC";
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
