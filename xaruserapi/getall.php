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
 * @param $args['modid'] select polls for module id
 * @param $args['itemtype'] select polls for item type
 * @param $args['objectid'] select polls for object id (item id)
 * @param $args['getoptions'] boolean, true to fetch options with each poll
 * @param $args['startnum'] start poll (row number)
 * @param $args['numitems'] maximum number of polls to fetch at the database level
 * @param $args['pid'] poll ID (fetch a single poll)
 * @param $args['fetchone'] boolean, true to stop after fetching one poll.
 * @returns array
 * @return array of items, or false on failure
 */
function polls_userapi_getall($args)
{
    // Get parameters from argument array
    extract($args);

    $polls = array();

    // Security check
    if (!xarSecurityCheck('ListPolls')){
        return;
    }

    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $pollstable = $xartable['polls'];
    $pollsinfotable = $xartable['polls_info'];
    $barscale = xarModGetVar('polls', 'barscale');

    // Arrays used to build the SQL.
    $bindvars = array();
    $where = array();
    $join = array();

    $now = (int)time();

    // Restrict polls according to 'status', where status can be:
    // 0: ALL (No restrictions.)
    // 1: OPEN (Has already started, and has not yet finished, and has not been manually closed.)
    // 2: UPCOMING (Has not yet started.)
    // 3: CLOSED (The end date is set and has already passed.)
    // The status is implicit in the dates.
    // FIXED: we also  consider the 'zar_open' column that allows a poll to be closed early.

    if (isset($status) && is_numeric($status)) {
        if ($status == 1) {
            // OPEN
            $where[] = "$pollstable.xar_open = 1 AND $pollstable.xar_start_date <= ? AND ($pollstable.xar_end_date >= ? OR $pollstable.xar_end_date = 0)";
            $bindvars[]= $now;
            $bindvars[]= $now;
        } elseif ($status == 2) {
            // UPCOMING
            $where[] = "$pollstable.xar_open = 1 AND $pollstable.xar_start_date >= ?";
            $bindvars[]= $now;
        } elseif ($status == 3) {
            // CLOSED
            $where[] = "($pollstable.xar_open = 0 OR ($pollstable.xar_end_date <= ? AND $pollstable.xar_end_date > 0))";
            $bindvars[]= $now;
        }

        // ?
        if ($status >= 1) {
            if (isset($hook) && is_numeric($hook)) {
                $where[] = "$pollstable.xar_itemid = ?";
                $bindvars[]= (int)$hook;
            }
        }
    }

    // Other selection criteria.

    // These next three are used by hooks.
    if (isset($modid) && is_numeric($modid)) {
        $where[] = "$pollstable.xar_modid = ?";
        $bindvars[]= (int)$modid;
    }

    if (isset($itemtype) && is_numeric($itemtype)) {
        $where[] = "$pollstable.xar_itemtype = ?";
        $bindvars[]= (int)$itemtype;
    }

    if (isset($objectid) && is_numeric($objectid)) {
        $where[] = "$pollstable.xar_itemid = ?";
        $bindvars[]= (int)$objectid;
    }

    if (isset($pid) && is_numeric($pid)) {
        $where[] = "$pollstable.xar_pid = ?";
        $bindvars[]= (int)$pid;
    }

    // Join to categories if hooked (for selection).
    if (!empty($catid) && xarModIsHooked('categories', 'polls')) {
        // Get the LEFT JOIN ... ON ...  and WHERE parts from categories
        $categoriesdef = xarModAPIFunc('categories', 'user', 'leftjoin',
            array('modid' => xarModGetIDFromName('polls'), 'catid' => $catid)
        );

        if (!empty($categoriesdef)) {
            $join[] = "LEFT JOIN $categoriesdef[table]"
                . " ON $categoriesdef[field] = xar_pid"
                . " $categoriesdef[more]";

            $where[] = "$categoriesdef[where]";
        }
    }

    // Flatten out the where clauses and joins.
    if (!empty($where)) {
        $where = ' WHERE ' . implode(' AND ', $where);
    } else {
        $where = '';
    }
    $join = implode(' ', $join);
    
    // Get polls
    $sql = "
        SELECT
            $pollstable.xar_pid,
            $pollstable.xar_title,
            $pollstable.xar_type,
            $pollstable.xar_open,
            $pollstable.xar_private,
            $pollstable.xar_modid,
            $pollstable.xar_itemtype,
            $pollstable.xar_itemid,
            $pollstable.xar_opts,
            $pollstable.xar_votes,
            $pollstable.xar_start_date,
            $pollstable.xar_end_date,
            $pollstable.xar_reset
        FROM
            $pollstable
            $join
            $where
        ORDER BY
            $pollstable.xar_pid DESC";

    // Handle number of rows.
    if (!isset($startnum)) $startnum = 1;
    if (!empty($numitems)) {
        $result = $dbconn->SelectLimit($sql, $numitems, $startnum-1, $bindvars);
    } else {
        $result = $dbconn->execute($sql, $bindvars);
    }

    if (!$result) {
        return;
    }

    // Put polls into result array.
    for (; !$result->EOF; $result->MoveNext()) {
        list($pid, $title, $type, $open, $private, $modid, $itemtype, $itemid, $opts, $votes, $start_date, $end_date, $reset) = $result->fields;

        if (xarSecurityCheck('ViewPolls', 0, 'Polls', "$title:$type")) {
            // Get the options if we need them.
            // Also calcultate the bar scales.

            $options = array();
            if (!empty($getoptions)) {
                $sql_options = "SELECT xar_optnum, xar_optname, xar_votes
                    FROM $pollsinfotable
                    WHERE xar_pid = ?
                    ORDER BY xar_optnum";
                $result_options = $dbconn->Execute($sql_options, array((int)$pid));

                for(; !$result_options->EOF; $result_options->MoveNext()) {
                    list($optnum, $optname, $optvotes) = $result_options->fields;

                    if ($votes == 0) {
                        $percentage = 0;
                    } else {
                        $percentage = round(($optvotes / $votes) * 100, 1);
                    }

                    $options[$optnum] = array(
                        'name' => $optname,
                        'votes' => $optvotes,
                        'percentage' => $percentage,
                        'barwidth' => $percentage * $barscale,
                    );
                }
                $result_options->Close();
            }

            // Determine the state of the poll wrt its time periodperiod.
            // The poll will be 'open', 'closed' or 'upcoming'.
            if (!empty($start_date) && $start_date > $now) {
                $state = 'upcoming';
            } else {
                if (empty($end_date) || $end_date > $now) {
                    $state = 'open';
                } else {
                    $state = 'closed';
                }
            }

            $polls[] = array(
                'pid' => $pid,
                'title' => $title,
                'type' => $type,
                'open' => $open,
                'private' => $private,
                'modid' => $modid,
                'itemtype' => $itemtype,
                'itemid' => $itemid,
                'opts' => $opts,
                'votes' => $votes,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'reset' => $reset,
                'options' => $options,
                'state' => $state,
            );

            // Break out of the loop if we want to stop after the first poll found.
            if (!empty($fetchone)) break;
        }
    }

    $result->Close();

    // Return the items
    return $polls;
}

?>