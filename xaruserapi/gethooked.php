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
 * get a specific poll hooked to some external module item
 * @param $args['modname'] module name of the original item
 * @param $args['itemtype'] item type of the original item
 * @param $args['objectid'] object id of the original item
 * @returns array
 * @return item array, or false on failure
 */
function polls_userapi_gethooked($args)
{
    // Get arguments from argument array
    extract($args);

    if (empty($modname)) {
        $modname = xarModGetName();
    }
    $modid = xarModGetIDFromName($modname);
    if (empty($modid)) {
        return;
    }
    if (empty($itemtype)) {
        $itemtype = 0;
    }
    if (empty($objectid)) {
        $objectid = 0;
    }

    // Get datbase setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $pollstable = $xartable['polls'];

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
            WHERE  xar_modid = ?
               AND xar_itemtype = ?
               AND xar_itemid = ?";
    $bindvars = array((int)$modid, $itemtype, $objectid);
    $result =& $dbconn->SelectLimit($sql, 1, -1, $bindvars);

    // Error check
    if (!$result) {
        return;
    }

    // Check for no rows found, and if so return
    if ($result->EOF) {
        return;
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
        return;
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