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
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function pubsub_userapi_subscribe($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    $invalid = array();
    if (!isset($eventid) || !is_numeric($eventid)) {
        $invalid[] = 'eventid';
    }
    if (!isset($actionid) || !is_numeric($actionid)) {
        $invalid[] = 'actionid';
    }
    if (count($invalid) > 0) {
        $msg = pnML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'user', 'subscribe', 'Pubsub');
        pnExceptionSet(PN_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
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
    	$msg = pnML('Not authorized to subscribe to #(1) items',
                    'Pubsub');
        pnExceptionSet(PN_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException($msg));
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
    	$msg = pnMLByKey('DATABASE_ERROR', $sql);
	pnExceptionSet(PN_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    // return pubsub ID 
    return $nextId;
}

/**
 * delete a pubsub subscription
 * @param $args['pubsubid'] ID of the subscription to delete
 * @returns bool
 * @return true on success, false on failure
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function pubsub_userapi_unsubscribe($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    $invalid = array();
    if (!isset($pubsubid) || !is_numeric($pubsubid)) {
        $invalid[] = 'pubsubid';
    }
    if (count($invalid) > 0) {
        $msg = pnML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'user', 'unsubscribe', 'Pubsub');
        pnExceptionSet(PN_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    // Security check
    if (!pnSecAuthAction(0, 'Pubsub', "$pubsubid::", ACCESS_DELETE)) {
    	$msg = pnML('Not authorized to ubsubscribe #(1) items',
                    'Pubsub');
        pnExceptionSet(PN_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException($msg));
        return;
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
     	$msg = pnMLByKey('DATABASE_ERROR', $sql);
        pnExceptionSet(PN_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    return true;
}

/**
 * update an existing pubsub subscription
 * @param $args['pubsubid'] the ID of the item
 * @param $args['actionid'] the new action id for the item
 * @returns bool
 * @return true on success, false on failure
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function pubsub_userapi_updatesubscription($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    $invalid = array();
    if (!isset($pubsubid) || !is_numeric($pubsubid)) {
        $invalid[] = 'pubsubid';
    }
    if (!isset($objectid) || !is_numeric($objectid)) {
        $invalid[] = 'objectid';
    }
    if (count($invalid) > 0) {
        $msg = pnML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'user', 'updatesubscription', 'Pubsub');
        pnExceptionSet(PN_SYSTEM_EXCEPTION, 'BAD_PARAM', 
                       new SystemException($msg));
        return;
    }

    // Security check
    if (!pnSecAuthAction(0, 'Pubsub', "$pubsubid::", ACCESS_EDIT)) {
    	$msg = pnML('Not authorized to edit #(1) items',
                    'Pubsub');
        pnExceptionSet(PN_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException($msg));
        return;
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
     	$msg = pnMLByKey('DATABASE_ERROR', $sql);
        pnExceptionSet(PN_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    return true;
}
?>
