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
 * create a poll
 * @param $args['title'] title of poll
 * @param $args['polltype'] type of poll ('single' for select one
 *                                      'multi' for select many)
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
        $msg = xarML('Missing poll title or type');
        xarErrorSet(XAR_USER_EXCEPTION,
                    'BAD_DATA',
                     new DefaultUserException($msg));
        return;
    }
    if ($private != 1){
        $private = 0;
    }
    // Security check
    if (!xarSecurityCheck('AddPolls')) {
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $pollstable = $xartable['polls'];
    $prefix = xarConfigGetVar('prefix');

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
              xar_pid,
              xar_title,
              xar_type,
              xar_open,
              xar_private,
              xar_votes,
              xar_modid,
              xar_itemtype,
              xar_itemid,
              xar_reset)
            VALUES (?,?,?,1,?,?,?,?,?,?)";

    $bindvars = array((int)$nextId, $title, $polltype, $private, $votes, (int)$modid, $itemtype, $itemid, $time);
    $result = $dbconn->Execute($sql, $bindvars);


    if (!$result) {
        return;
    }
    $pid = $dbconn->PO_Insert_ID($pollstable, 'xar_pid');

    $args['pid'] = $pid;
    $args['module'] = 'polls';
    $args['itemtype'] = 0;
    $args['itemid'] = $pid;

    xarModCallHooks('item', 'create', $pid, $args);

    return $pid;
}

?>