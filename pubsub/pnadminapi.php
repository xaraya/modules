<?php 
// File: $Id$
// ----------------------------------------------------------------------
// Xaraya eXtensible Management System
// Copyright (C) 2002 by the Xaraya Development Team.
// http://www.xaraya.org
// ----------------------------------------------------------------------

/**
 * create a new pubsub event
 * @param $args['module'] name of the module this event applies to
 * @param $args['eventtype'] the event type
 * @returns int
 * @return event ID on success, false on failure
 */
function pubsub_adminapi_addevent($args)
{
// This function will create a new 

    // Get arguments from argument array
    extract($args);

    // Security check
    if (!pnSecAuthAction(0, 'Pubsub', '::', ACCESS_ADD)) {
        pnSessionSetVar('errormsg', _PUBSUBNOAUTH);
        return false;
    }

    // Get datbase setup
    list($dbconn) = pnDBGetConn();
    $pntable = pnDBGetTables();
    $pubsubeventstable = $pntable['pubsub_events'];

    // Get next ID in table
    $nextId = $dbconn->GenId($pubsubeventstable);

    // Add item
    $sql = "INSERT INTO $pubsubeventstable (
              pn_eventid,
              pn_module,
              pn_eventtype)
            VALUES (
              $nextId,
              '" . pnVarPrepForStore($module) . "',
              '" . pnvarPrepForStore($eventtype) . "')";
    $dbconn->Execute($sql);

    if ($dbconn->ErrorNo() != 0) {
        pnSessionSetVar('errormsg', _CREATEFAILED);
        return false;
    }

    // return eventID
    return $nextId;
}

/**
 * delete a pubsub event
 * @param $args['eventid'] ID of the item
 * @returns bool
 * @return true on success, false on failure
 */
function pubsub_adminapi_deleteevent($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if (!isset($eventid)) {
        pnSessionSetVar('errormsg', _MODARGSERROR);
        return false;
    }

    // Security check
    if (!pnSecAuthAction(0, 'Pubsub', '::', ACCESS_DELETE)) {
        pnSessionSetVar('errormsg', _PUBSUBNOAUTH);
        return false;
    }

    // Get datbase setup
    list($dbconn) = pnDBGetConn();
    $pntable = pnDBGetTables();
    $pubsubeventstable = $pntable['pubsub_events'];

    // Delete item
    $sql = "DELETE FROM $pubsubeventstable
            WHERE pn_eventid = '" . pnVarPrepForStore($eventid) . "'";
    $dbconn->Execute($sql);

    if ($dbconn->ErrorNo() != 0) {
        pnSessionSetVar('errormsg', _DELETEFAILED);
        return false;
    }

    return true;
}

/**
 * update an existing pubsub event
 * @param $args['eventid'] the ID of the item
 * @param $args['module'] the new module name of the item
 * @param $args['eventtype'] the new event type of the item
 * @param $args['groupdescr'] the new group description of the item
 * @param $args['actionid'] the new action id for the item
 * @returns bool
 * @return true on success, false on failure
 */
function pubsub_adminapi_updateevent($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if ((!isset($eventid)) ||
        (!isset($module)) ||
        (!isset($eventtype)) ||
        (!isset($groupdescr)) ||
        (!isset($actionid))) {
        pnSessionSetVar('errormsg', _MODARGSERROR);
        return false;
    }

    // Security check
    if (!pnSecAuthAction(0, 'Pubsub', "$name::$eventid", ACCESS_EDIT)) {
        pnSessionSetVar('errormsg', _PUBSUBNOAUTH);
        return false;
    }

    // Get database setup
    list($dbconn) = pnDBGetConn();
    $pntable = pnDBGetTables();
    $pubsubeventstable = $pntable['pubsub_events'];

    // Update the item
    $sql = "UPDATE $pubsubeventstable
            SET pn_module = '" . pnVarPrepForStore($module) . "',
                pn_eventtype = '" . pnVarPrepForStore($eventtype) . "',
                pn_groupdescr = '" . pnVarPrepForStore($groupdescr) . "',
                pn_actionid = '" . pnVarPrepForStore($actionid) . "'
            WHERE pn_eventid = '" . pnVarPrepForStore($eventid) . "'";
    $dbconn->Execute($sql);

    if ($dbconn->ErrorNo() != 0) {
        pnSessionSetVar('errormsg', _UPDATEFAILED);
        return false;
    }

    return true;
}

/**
 * process a pubsub event and add it to the Queue
 * @param $args['pubsubid'] subscription identifier
 * @param $args['objectid'] the specific object in the module
 * @returns int
 * @return handling ID on success, false on failure
 */
function pubsub_adminapi_processevent($args)
{
// This function will create a new 

    // Get arguments from argument array
    extract($args);

    // Security check
    if (!pnSecAuthAction(0, 'Pubsub', '::', ACCESS_ADD)) {
        pnSessionSetVar('errormsg', _PUBSUBNOAUTH);
        return false;
    }

    // Get datbase setup
    list($dbconn) = pnDBGetConn();
    $pntable = pnDBGetTables();
    $pubsubprocesstable = $pntable['pubsub_process'];

    // Get next ID in table
    $nextId = $dbconn->GenId($pubsubprocesstable);

    // Add item
    $sql = "INSERT INTO $pubsubprocesstable (
              pn_handlingid,
              pn_pubsubid,
              pn_objectid,
	      pn_status)
            VALUES (
              $nextId,
              '" . pnVarPrepForStore($pubsubid) . "',
              '" . pnvarPrepForStore($objectid) . "',
              '" . pnvarPrepForStore('pending') . "')";
    $dbconn->Execute($sql);

    if ($dbconn->ErrorNo() != 0) {
        pnSessionSetVar('errormsg', _CREATEFAILED);
        return false;
    }
    // TODO implement queuing properly
    // for now we'll just go parse the queue immediately
    pubsub_adminapi_processq();

    // return handlingID
    return $nextId;
}

/**
 * Process the queue and run all pending jobs
 * @returns bool
 * @return number of jobs run on success, false if not
 */
function pubsub_adminapi_processq($args)
{
    // Get arguments from argument array
    extract($args);

    // Database information
    list($dbconn) = pnDBGetConn(); 
    $pntable = pnDBGetTables();
    $pubsubprocesstable = $pntable['pubsub_process'];

    // Get all jobs in pending state
    $sql = "SELECT pn_pubsubid,
    		   pn_objectid
            FROM $pubsubprocesstable
            WHERE pn_status = '" . pnVarPrepForStore('pending') . "'";
    $result = $dbconn->Execute($sql);
    // set count to 0
    $count = 0;

    if ($dbconn->ErrorNo() != 0) {
        pnSessionSetVar('errormsg', 'SQL Error');
        return false;
    } else {
        while (!$result->EOF) {
            // run the job passing it the pubsub and object ids.
	    pubsub_adminapi_runjob($result->fields[0], $result->fields[1]);
	    $count++;
	    $result->MoveNext();
        }
    }
    return $count;
}

/**
 * run the job
 * @param $args['pubsubid'] the subscription id
 * @param $args['objectid'] the specific object in the module
 * @returns bool
 * @return true on success, false on failure
 */
function pubsub_adminapi_runjob($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if ((!isset($pubsubid)) ||
        (!isset($objectid))) {
        pnSessionSetVar('errormsg', _MODARGSERROR);
        return false;
    }
    
    // Database information
    list($dbconn) = pnDBGetConn(); 
    $pntable = pnDBGetTables();
    $pubsubregtable = $pntable['pubsub_reg'];

    // Get info on job to run
    $sql = "SELECT pn_actionid,
    		   pn_eventid
            FROM $pubsubregtable
            WHERE pn_pubsubid = '" . pnVarPrepForStore($pubsubid) . "'";
    $result   = $dbconn->Execute($sql);
    $actionid = $result->fields[0];
    $eventid  = $result->fields[1]);
    list($action,$info)   =  explode(':', $actionid);
    if ($action = "mail") {
	// check mail address is a valid email address
	if (!eregi("^([A-Za-z0-9_]|\\-|\\.)+@(([A-Za-z0-9_]|\\-)+\\.)[A-Za-z]{2,4}$", $info)) {
	    // address invalid
	    pnSessionSetVar('errormsg', _PUBSUBINVALIDEMAIL);
	    return false;
	} else {
	    // addesss valid so send the mail
            mail($info,    // to
	         $subject, // subject
	         $message, // message
	         "From: pnConfigGetVar('adminmail')\r\nReply-to: pnConfigGetVar('adminmail')\r\n");
            // delete job from queue now its run
	    pubsub_adminapi_deljob($handlingid);
        }
    } else {
        // invalid action - update queue accordingly
        pnSessionSetVar('errormsg', _PUBSUBNOACTIONERROR);
	pubsub_adminapi_updatejob($handlingid,$pubsubid,$objectid,'error');
        return false;
    }
    return true;
}

/**
 * delete a pubsub job from the queue
 * @param $args['handlingid'] ID of the job to delete
 * @returns bool
 * @return true on success, false on failure
 */
function pubsub_adminapi_deljob($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if (!isset($handlingid)) {
        pnSessionSetVar('errormsg', _MODARGSERROR);
        return false;
    }

    // Security check
    if (!pnSecAuthAction(0, 'Pubsub', "::$handlingid", ACCESS_DELETE)) {
        pnSessionSetVar('errormsg', _PUBSUBNOAUTH);
        return false;
    }
    
    // Get datbase setup
    list($dbconn) = pnDBGetConn();
    $pntable = pnDBGetTables();
    $pubsubprocesstable = $pntable['pubsub_process'];

    // Delete item
    $sql = "DELETE FROM $pubsubprocesstable
            WHERE pn_handlingid = '" . pnVarPrepForStore($handlingid) . "'";
    $dbconn->Execute($sql);

    if ($dbconn->ErrorNo() != 0) {
        pnSessionSetVar('errormsg', _DELETEFAILED);
        return false;
    }

    return true;
}

/**
 * update an existing pubsub job
 * @param $args['handlingid'] the ID of the item
 * @param $args['pubsubid'] the new pubsub id for the item
 * @param $args['objectid'] the new object id for the item
 * @param $args['status']   the new status for the item
 * @returns bool
 * @return true on success, false on failure
 */ 
function pubsub_adminapi_updatejob($args)
{
    // Get arguments from argument array
    extract($args);
    
    // Argument check
    if ((!isset($handlingid)) ||
        (!isset($pubsubid))   ||
	(!isset($objectid))   ||
        (!isset($status))) {
        pnSessionSetVar('errormsg', _MODARGSERROR);
                return false;
    }

    // Security check
    if (!pnSecAuthAction(0, 'Pubsub', "::$handlingid", ACCESS_EDIT)) {
        pnSessionSetVar('errormsg', _PUBSUBNOAUTH);
        return false;
    }

    // Get database setup
    list($dbconn) = pnDBGetConn();
    $pntable = pnDBGetTables();
    $pubsubprocesstable = $pntable['pubsub_process'];

    // Update the item
    $sql = "UPDATE $pubsubprocesstable
            SET pn_pubsubid = '" . pnVarPrepForStore($pubsubid) . "',
                pn_objectid = '" . pnVarPrepForStore($objectid) . "',
                pn_status = '" . pnVarPrepForStore($status) . "'
            WHERE pn_handlingid = '" . pnVarPrepForStore($handlingid) . "'";
    $dbconn->Execute($sql);

    if ($dbconn->ErrorNo() != 0) {
        pnSessionSetVar('errormsg', _UPDATEFAILED);
        return false;
    }    
    return true;
}


?>
