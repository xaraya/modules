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
 * get a specific item
 * @param $args['pid'] id of poll to get (optional)
 * @returns array
 * @return item array, or false on failure
 */
function polls_userapi_get($args)
{
    // Get arguments from argument array
    extract($args);

    // Get datbase setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $pollstable = $xartable['polls'];

    $bindvars  = array();
    // Selection check
    if (!empty($pid)) {
        $extra = "WHERE xar_pid = ?";
        $bindvars[]=(int)$pid;
// inserire una condizione sul polls con data futura
    } else {
        $extra = "WHERE xar_modid = " . xarModGetIDFromName('polls');
        if (isset ($act)) {
         $extra .= " AND xar_open = 1 AND xar_start_date <= ? ";
         $bindvars[]= time();
         }
        $extra .= " ORDER BY xar_pid DESC";
    }

    // Get item
    $sql = "SELECT xar_pid,
                   xar_title,
                   xar_type,
                   xar_open,
                   xar_private,
                   xar_modid,
                   xar_itemtype,
                   xar_itemid,
                   xar_opts,
                   xar_votes,
                   xar_start_date,
                   xar_end_date,
                   xar_reset
            FROM $pollstable
            $extra";

    if (!empty($pid) || !empty($act)) {
         $result = $dbconn->execute($sql, $bindvars);
    }else {
         $result = $dbconn->execute($sql);
    }

    // Error check
    if (!$result) {
        return false;
    }

    // Check for no rows found, and if so return
    if ($result->EOF) {
        return false;
    }

    // Obtain the poll information from the result set
    list($pid, $title, $type, $open, $private, $modid, $itemtype, $itemid, $opts, $votes, $start_date, $end_date, $reset) = $result->fields;

    $result->Close();

    // Security check
    if(!xarSecurityCheck('ViewPolls',0,'Polls',"$title:$type")){
        return;
    }

    // Get the options for this poll
    $pollsinfotable = $xartable['polls_info'];

    $sql = "SELECT xar_optnum,
                   xar_optname,
                   xar_votes
            FROM $pollsinfotable
            WHERE xar_pid = ?
            ORDER BY xar_optnum";
    $result = $dbconn->Execute($sql, array((int)$pid));

    if (!$result) {
        return false;
    }

    $options = array();
    for(; !$result->EOF; $result->MoveNext()) {
        list($optnum, $optname, $optvotes) = $result->fields;
        $options[$optnum] = array('name' => $optname,
                                  'votes' => $optvotes);
    }
    $result->Close();

    // Create the item array
    $item = array('pid' => $pid,
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
                  'options' => $options);

    // Return the item array
    return $item;
}

?>
