<?php 
// File: $Id$
// ----------------------------------------------------------------------
// Xaraya eXtensible Management System
// Copyright (C) 2002 by the Xaraya Development Team.
// http://www.xaraya.org
// ----------------------------------------------------------------------

/**
 * Subscribe to an event
 * @param $args['eventid'] Event to subscribe to
 * @param $args['actionid'] Requested action for this subscription
 * @param $args['userid'] UID of User to subscribe (optional)
 * @returns bool
 * @return pubsub ID on success, false if not
 */
function pubsub_userapi_subscribe($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if ((!isset($eventid)) ||
        (!isset($actionid))) {
        pnSessionSetVar('errormsg', _MODARGSERROR);
        return;
    }

    // Anonymous user cannot subscribe to events
    if (pnUserLoggedIn()) {
        // if no userid was supplied then subscribe the currently logged in user
        if (!isset($userid)) {
	    $userid = pnUserGetVar('userid');
	}
    } else {
        pnSessionSetVar('errormsg', _PUBSUBANONERROR);
	return;
    }

    // Security check
    if (!pnSecAuthAction(0, 'Pubsub::', "$eventid:$actionid", ACCESS_READ)) {
        return;
    }

    // Database information
    list($dbconn) = pnDBGetConn();
    $pntable = pnDBGetTables();
    $pubsubregtable = $pntable['pubsub_reg'];

    // Get next ID in table
    $nextId = $dbconn->GenId($pubsubregtable);

    // Add item
    $sql = "INSERT INTO $pubsubregtable (
              pn_pubsubid,
              pn_eventid,
              pn_userid,
              pn_actionid)
            VALUES (
              $nextId,
              '" . pnVarPrepForStore($eventid) . "',
              '" . pnVarPrepForStore($userid) . "',
              '" . pnvarPrepForStore($actionid) . "')";
    $dbconn->Execute($sql);

    if ($dbconn->ErrorNo() != 0) {
        pnSessionSetVar('errormsg', _CREATEFAILED);
        return false;
    }

    // return pubsub ID 
    return $nextId;
}

/**
 * delete a pubsub subscription
 * @param $args['pubsubid'] ID of the subscription to delete
 * @returns bool
 * @return true on success, false on failure
 */
function pubsub_userapi_unsubscribe($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if (!isset($pubsubid)) {
        pnSessionSetVar('errormsg', _MODARGSERROR);
        return false;
    }

    // Security check
    if (!pnSecAuthAction(0, 'Pubsub', "$pubsubid::", ACCESS_DELETE)) {
        pnSessionSetVar('errormsg', _PUBSUBNOAUTH);
        return false;
    }
    
    // Get datbase setup
    list($dbconn) = pnDBGetConn();
    $pntable = pnDBGetTables();
    $pubsubregtable = $pntable['pubsub_reg'];

    // Delete item
    $sql = "DELETE FROM $pubsubregtable
            WHERE pn_pubsubid = '" . pnVarPrepForStore($pubsubid) . "'";
    $dbconn->Execute($sql);

    if ($dbconn->ErrorNo() != 0) {
        pnSessionSetVar('errormsg', _DELETEFAILED);
        return false;
    }

    return true;
}

/**
 * update an existing pubsub subscription
 * @param $args['pubsubid'] the ID of the item
 * @param $args['actionid'] the new action id for the item
 * @returns bool
 * @return true on success, false on failure
 */
function pubsub_userapi_updatesubscription($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if ((!isset($pubsubid)) ||
        (!isset($actionid))) {
        pnSessionSetVar('errormsg', _MODARGSERROR);
	        return false;
    }

    // Security check
    if (!pnSecAuthAction(0, 'Pubsub', "$pubsubid::", ACCESS_EDIT)) {
        pnSessionSetVar('errormsg', _PUBSUBNOAUTH);
        return false;
    }

    // Get database setup
    list($dbconn) = pnDBGetConn();
    $pntable = pnDBGetTables();
    $pubsubregtable = $pntable['pubsub_reg'];

    // Update the item
    $sql = "UPDATE $pubsubregtable
            SET pn_actionid = '" . pnVarPrepForStore($actionid) . "'
            WHERE pn_pubsubid = '" . pnVarPrepForStore($pubsubid) . "'";
    $dbconn->Execute($sql);

    if ($dbconn->ErrorNo() != 0) {
        pnSessionSetVar('errormsg', _UPDATEFAILED);
        return false;
    }

    return true;
}
?>
