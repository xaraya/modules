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
 * Process the queue and run all pending jobs (executed by the scheduler module)
 * @returns bool
 * @return number of jobs run on success, false if not
 * @raise DATABASE_ERROR
 */
function pubsub_adminapi_processq($args)
{
    // Get arguments from argument array
    extract($args);

    // Database information
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();
    $pubsubprocesstable = $xartable['pubsub_process'];

    // Get all jobs in pending state
    $query = "SELECT xar_handlingid,
                     xar_pubsubid,
                     xar_objectid
              FROM $pubsubprocesstable
              WHERE xar_status = " . xarVarPrepForStore('pending');
    $result = $dbconn->Execute($query);
    if (!$result) return;

    // set count to 0
    $count = 0;

    while (!$result->EOF) {
        list($handlingid,$pubsubid,$objectid) = $result->fields;
        // run the job passing it the handling, pubsub and object ids.
        xarModAPIFunc('pubsub','admin','runjob',
                      array('handlingid' => $handlingid,
                            'pubsubid' => $pubsubid,
                            'objectid' => $objectid));
        $count++;
        $result->MoveNext();
    }
    return $count;

} // END processq

?>
