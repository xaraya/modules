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
    } else {
        $extra = "WHERE xar_modid = " . xarModGetIDFromName('polls');
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
                   xar_reset
            FROM $pollstable
            $extra";

    if (!empty($pid)) {
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
    list($pid, $title, $type, $open, $private, $modid, $itemtype, $itemid, $opts, $votes, $reset) = $result->fields;

    $result->Close();

    // Security check
    if(!xarSecurityCheck('ViewPolls',0,'All',"$title:All:$pid")){
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
                  'reset' => $reset,
                  'options' => $options);

    // Return the item array
    return $item;
}

?>