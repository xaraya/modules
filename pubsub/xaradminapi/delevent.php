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
 * delete a pubsub event
 * @param $args['eventid'] ID of the item
 * @returns bool
 * @return true on success, false on failure
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function pubsub_adminapi_delevent($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    $invalid = array();
    if (!isset($eventid) || !is_numeric($eventid)) {
        $invalid[] = 'eventid';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) in function #(3)() in module #(4)',
                    join(', ',$invalid), 'delevent', 'Pubsub');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    // Security check
    if (!xarSecurityCheck('DeletePubSub')) return;

    // Get datbase setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $pubsubeventstable = $xartable['pubsub_events'];

    // Delete item from events table
    $query = "DELETE FROM $pubsubeventstable
            WHERE xar_eventid = " . xarVarPrepForStore($eventid);
    $dbconn->Execute($query);
    if (!$result) return;

    return true;

} // END delevent

?>
