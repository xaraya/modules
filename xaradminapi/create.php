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
 * create a poll
 * @param $args['title'] title of poll
 * @param $args['polltype'] type of poll ('0' for one selection
 *                                        '1' for multiple selections)
 * @param $args['time'] time when the poll was created (import only)
 * @param $args['votes'] number of votes for this poll (import only)
 * @param $args['module'] module of the item this poll relates to (hooks only)
 * @param $args['itemtype'] itemtype of the item this poll relates to (hooks only)
 * @param $args['itemid'] itemid of the item this poll relates to (hooks only)
 * @returns int
 * @return ID of poll, false on failure
 */
function polls_adminapi_create($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if ((!isset($title)) || (!isset($polltype))) {
            throw new EmptyParameterException($pid,'Missing Poll title or type ');
    }
    if ($private != 1){
        $private = 0;
    }
    if (!isset($start_date) || !is_numeric($start_date)) {
        $start_date = time();
        }
    if (!isset($end_date) || !is_numeric($end_date)) {
        $end_date = 0;
        }

    // Security check
    if (!xarSecurityCheck('AddPolls')) {
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $pollstable = $xartable['polls'];
    //$prefix = xarConfigVars::get('prefix');
    $prefix = xarConfigVars::get(null, 'prefix');

    $nextId = $dbconn->GenId($pollstable);

    if (empty($time)) {
        $time = time();
    }
    if (empty($votes)) {
        $votes = 0;
    }
    if (empty($module)) {
        $module = 'polls';
    }
    $modid = xarModGetIDFromName($module);
    if (empty($itemtype)) {
        $itemtype = 0;
    }
    if (empty($itemid)) {
        $itemid = 0;
    }
    $sql = "INSERT INTO $pollstable (
              pid,
              title,
              type,
              open,
              private,
              votes,
              modid,
              itemtype,
              itemid,
              start_date,
              end_date,
              reset)
            VALUES (?,?,?,1,?,?,?,?,?,?,?,?)";

    $bindvars = array($nextId, $title, $polltype, $private, $votes, (int)$modid, $itemtype, $itemid, $start_date, $end_date, $time);
    $result = $dbconn->Execute($sql, $bindvars);


    if (!$result) {
        return;
    }
    $pid = $dbconn->PO_Insert_ID($pollstable, 'pid');

    $args['pid'] = $pid;
    $args['module'] = 'polls';
    $args['itemtype'] = 0;
    $args['itemid'] = $pid;

    xarModCallHooks('item', 'create', $pid, $args);

    return $pid;
}

?>
