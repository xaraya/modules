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
 * delete a pubsub event from hooks
 * @param $args['extrainfo']
 * @returns bool
 * @return true on success, false on failure
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function pubsub_adminapi_deletehook($args)
{
    // This has to be an argument
    if (!isset($extrainfo)) {
//        $msg = xarML('Invalid #(1) in function #(2)() in module #(3)',
//                    'extrainfo', 'deletehook', 'pubsub');
//        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
//                       new SystemException($msg));
        return;
    }
    $cid = $extrainfo['cid'];
    $itemtype = $extrainfo['itemtype'];

    // When called via hooks, the module name may be empty, so we get it from
    // the current module
    if (empty($extrainfo['modid'])) {
        $modname = xarModGetName();
        $modid = xarModGetIDFromName($modname);
        if (!$modid) return; // throw back
    } else {
        $modid = $extrainfo['modid'];
    }

    // Get datbase setup
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();
    $pubsubeventstable = $xartable['pubsub_events'];
    $pubsubeventcidstable = $xartable['pubsub_eventcids'];

    // check this event isn't already in the DB
    $query = "SELECT $pubsubeventstable.xar_eventid
        FROM  $pubsubeventstable, $pubsubeventcidstable
        WHERE $pubsubeventstable.xar_modid = " . xarVarPrepForStore($modid) . "
        AND   $pubsubeventstable.xar_itemtype = " . xarVarPrepForStore($itemtype) . "
        AND   $pubsubeventstable.xar_eventid = $pubsubeventcidstable.xar_eid
        AND   $pubsubeventcidstable.xar_cid = " . xarVarPrepForStore($cid);

    $result = $dbconn->Execute($query);
    if (!$result) return;
    $eventid = $result->fields[0];

    // call delete function
    return pubsub_adminapi_delevent($eventid);

} // END deletehook

?>