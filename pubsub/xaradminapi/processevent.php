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
 * process a pubsub event and add it to the Queue
 * @param $args['pubsubid'] subscription identifier
 * @param $args['objectid'] the specific object in the module
 * @returns int
 * @return handling ID on success, false on failure
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function pubsub_adminapi_processevent($args)
{
    // Get arguments from argument array
    extract($args);
    $invalid = array();
    if (!isset($pubsubid) || !is_numeric($pubsubid)) {
        $invalid[] = 'pubsubid';
    }
    if (!isset($objectid) || !is_numeric($objectid)) {
        $invalid[] = 'objectid';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) function #(3)() in module #(4)',
                    join(', ',$invalid), 'processevent', 'Pubsub');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    // Security check
    if (!xarSecurityCheck('AddPubSub')) return;

    // Get datbase setup
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();
    $pubsubprocesstable = $xartable['pubsub_process'];

    // Get next ID in table
    $nextId = $dbconn->GenId($pubsubprocesstable);

    // Add item
    $query = "INSERT INTO $pubsubprocesstable (
              xar_handlingid,
              xar_pubsubid,
              xar_objectid,
	      xar_status)
            VALUES (
              $nextId,
              " . xarVarPrepForStore($pubsubid) . ",
              " . xarvarPrepForStore($objectid) . ",
              " . xarvarPrepForStore('pending') . ")";
    $result = $dbconn->Execute($query);
    if (!$result) return;

    $nextId = $dbconn->PO_Insert_ID($pubsubprocesstable, 'xar_handlingid');

    // return handlingID
    return $nextId;

} // END processevent

?>
