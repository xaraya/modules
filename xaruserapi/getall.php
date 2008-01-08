<?php
/**
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

        if ($status == 1) {
            $where = " WHERE $pollstable.start_date <= ? and ($pollstable.end_date >= ? or $pollstable.end_date = 0)";
            $bindvars[]= (int) time();
            $bindvars[]= (int) time();
            if (isset($hook) && is_numeric($hook)) {
                $where .= " AND $pollstable.itemid = ?";
                $bindvars[]= (int) $hook;
                }
        } elseif ($status == 2) {
            $where = " WHERE $pollstable.start_date >= ?";
            $bindvars[]= time();
            if (isset($hook) && is_numeric($hook)) {
                $where .= " AND $pollstable.itemid = ?";
                $bindvars[]= (int) $hook;
                }
        } elseif ($status == 3) {
            $where = " WHERE $pollstable.end_date <= ? and $pollstable.end_date > 0";
            $bindvars[]= time();
        if (isset($hook) && is_numeric($hook)) {
            $where .= " AND $pollstable.itemid = ?";
            $bindvars[]= (int) $hook;
            }
        }

    } else {
        if (isset($modid) && is_numeric($modid)) {
        $where = " WHERE $pollstable.modid = ?";
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
                          ON $categoriesdef[field] = pid
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
    $sql = "SELECT $pollstable.pid,
                   $pollstable.title,
                   $pollstable.type,
                   $pollstable.open,
                   $pollstable.private,
                   $pollstable.modid,
                   $pollstable.itemtype,
                   $pollstable.itemid,
                   $pollstable.votes,
                   $pollstable.start_date,
                   $pollstable.end_date,
                   $pollstable.reset
            FROM $pollstable
            $where
            ORDER BY $pollstable.pid DESC";
    $result = $dbconn->execute($sql, $bindvars);

    if (!$result) {
        return;
    }

    // Put polls into result array.
    for (; !$result->EOF; $result->MoveNext()) {
        list($pid, $title, $type, $open, $private, $modid, $itemtype, $itemid, $votes, $start_date, $end_date, $reset) = $result->fields;
        if (xarSecurityCheck('ViewPolls',0,'Polls',"$pid:$type")) {
            $polls[] = array('pid' => $pid,
                             'title' => $title,
                             'type' => $type,
                             'open' => $open,
                             'private' => $private,
                             'modid' => $modid,
                             'itemtype' => $itemtype,
                             'itemid' => $itemid,
                             'votes' => $votes,
                             'start_date' => $start_date,
                             'end_date' => $end_date,
                             'reset' => $reset);
        }
    }

    $result->Close();

    // Return the items
    return $polls;
}

?>
