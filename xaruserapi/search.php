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
 * Searches all active polls
 *
 * @author J. Cox
 * @access private
 * @returns mixed description of return
 */
function polls_userapi_search($args)
{
    if (empty($args) || count($args) < 1) {
        return;
    }

    extract($args);
    if($q == ''){
        return;
    }
    // Optional arguments.

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $pollstable = $xartable['polls'];
    $pollsinfotable = $xartable['polls_info'];

    $polls = array();
    $where = array();
    if ($title == 1){
        $where[] = "$pollstable .xar_title LIKE '%$q%'";
    }
    $join = '';
    if ($options == 1){
        $join = "LEFT JOIN $pollsinfotable ON $pollstable.xar_pid = $pollsinfotable.xar_pid";
        $where[] = "$pollsinfotable .xar_optname LIKE '%$q%'";
    }
    if(count($where) > 1){
        $clause = join($where, ' OR ');
    }
    elseif(count($where) == 1){
        $clause = $where[0];
    }
    else {
        return;
    }

    // Get item
    $sql = "SELECT DISTINCT $pollstable.xar_pid,
                   $pollstable.xar_title,
                   $pollstable.xar_type,
                   $pollstable.xar_open,
                   $pollstable.xar_private,
                   $pollstable.xar_modid,
                   $pollstable.xar_itemtype,
                   $pollstable.xar_itemid,
                   $pollstable.xar_votes,
                   $pollstable.xar_reset

            FROM $pollstable $join
            WHERE $clause";

    $result =& $dbconn->Execute($sql);
        if (!$result) return;

    // Put polls into result array
    for (; !$result->EOF; $result->MoveNext()) {
        list($pid, $title, $type, $open, $private, $modid, $itemtype, $itemid, $votes, $reset) = $result->fields;
        if (xarSecurityCheck('ViewPolls',0)) {
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


    // Return the users
    return $polls;

}
?>