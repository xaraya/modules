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
        $where[] = "$pollstable .title LIKE '%$q%'";
    }
    $join = '';
    if ($options == 1){
        $join = "LEFT JOIN $pollsinfotable ON $pollstable.pid = $pollsinfotable.pid";
        $where[] = "$pollsinfotable .optname LIKE '%$q%'";
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
    $sql = "SELECT DISTINCT $pollstable.pid,
                   $pollstable.title,
                   $pollstable.type,
                   $pollstable.open,
                   $pollstable.private,
                   $pollstable.modid,
                   $pollstable.itemtype,
                   $pollstable.itemid,
                   $pollstable.votes,
                   $pollstable.reset

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
