<?php
/**
 * File: $Id$
 *
 * Pubsub Admin API
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage Pubsub Module
 * @author Chris Dudley <miko@xaraya.com>
 * @author Garrett Hunter <garrett@blacktower.com>
 */

/**
 * process a pubsub event, by adding a job for each subscriber to the process queue
 * @param $args['modid'] the module id for the event
 * @param $args['itemtype'] the itemtype for the event
 * @param $args['cid'] the category id for the event
 * @param $args['extra'] some extra group criteria // TODO: for later, and
 * @param $args['objectid'] the specific object in the module
 * @param $args['templateid'] the template id for the jobs
 * @returns bool
 * @return true on success, false on failure
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function pubsub_adminapi_processevent($args)
{
    // Get arguments from argument array
    extract($args);
    $invalid = array();
    if (empty($modid) || !is_numeric($modid)) {
        $invalid[] = 'modid';
    }
    if (!isset($cid) || !is_numeric($cid)) {
        $invalid[] = 'cid';
    }
    if (!isset($objectid) || !is_numeric($objectid)) {
        $invalid[] = 'objectid';
    }
    if (!isset($templateid) || !is_numeric($templateid)) {
        $invalid[] = 'templateid';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'admin', 'processevent', 'Pubsub');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    if (empty($itemtype) || !is_numeric($itemtype)) {
        $itemtype = 0;
    }

    // Security check - not via hooks
//    if (!xarSecurityCheck('AddPubSub')) return;

    // Get datbase setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $pubsubeventstable  = $xartable['pubsub_events'];
    $pubsubregtable     = $xartable['pubsub_reg'];
    $pubsubprocesstable = $xartable['pubsub_process'];

    $query = "SELECT xar_pubsubid
                FROM $pubsubeventstable, $pubsubregtable
               WHERE $pubsubeventstable.xar_eventid = $pubsubregtable.xar_eventid
                 AND $pubsubeventstable.xar_modid = " . xarVarPrepForStore($modid) . "
                 AND $pubsubeventstable.xar_itemtype = " . xarVarPrepForStore($itemtype) . "
                 AND $pubsubeventstable.xar_cid = " . xarVarPrepForStore($cid);

    $result =& $dbconn->Execute($query);
    if (!$result) return;

    for (; !$result->EOF; $result->MoveNext()) {
        list($pubsubid) = $result->fields;

        // Get next ID in table
        $nextId = $dbconn->GenId($pubsubprocesstable);

        // Add item
        $query = "INSERT INTO $pubsubprocesstable (
                  xar_handlingid,
                  xar_pubsubid,
                  xar_objectid,
                  xar_templateid,
	          xar_status)
                VALUES (
                  $nextId,
                  " . xarVarPrepForStore($pubsubid) . ",
                  " . xarvarPrepForStore($objectid) . ",
                  " . xarvarPrepForStore($templateid) . ",
                  '" . xarvarPrepForStore('pending') . "')";
        $result2 = $dbconn->Execute($query);
        if (!$result2) return;
    }

    return true;

} // END processevent

?>
