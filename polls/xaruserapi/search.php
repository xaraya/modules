<?php

/**
 * Searches all active polls
 *
 * @author J. Cox
 * @access private
 * @returns mixed description of return
 */
function polls_userapi_search($args) {
    if (empty($args) || count($args) < 1) {
        return;
    }

    extract($args);
    if($q == ''){
        return;
    }
    // Optional arguments.
    if (!isset($startnum)) {
        $startnum = 1;
    }
    if (!isset($numitems)) {
        $numitems = xarModGetVar('polls', 'itemsperpage');
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $prefix = xarConfigGetVar('prefix');
    $pollstable = $xartable['polls'];
    $pollsinfotable = $xartable['polls_info'];

    $polls = array();
    $where = array();
    if ($title == 1){
        $where[] = $pollstable. '.' . $prefix . "_title LIKE '%$q%'";
    }
    $join = '';
    if ($options == 1){
        $join = "LEFT JOIN $pollsinfotable ON $pollstable." . $prefix . "_pid = $pollsinfotable." . $prefix . "_pid";
        $where[] = $pollsinfotable . '.' . $prefix . "_optname LIKE '%$q%'";
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
    $sql = "SELECT DISTINCT $pollstable.".$prefix."_pid,
                   $pollstable.".$prefix."_title,
                   $pollstable.".$prefix."_type,
                   $pollstable.".$prefix."_open,
                   $pollstable.".$prefix."_private,
                   $pollstable.".$prefix."_modid,
                   $pollstable.".$prefix."_itemtype,
                   $pollstable.".$prefix."_itemid,
                   $pollstable.".$prefix."_votes,
                   $pollstable.".$prefix."_reset

            FROM $pollstable $join
            WHERE $clause";

    $result =& $dbconn->SelectLimit($sql, $numitems, $startnum-1);
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
