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
*/

/**
 * create a new pubsub event
 * create event for an item - hook for ('item','create','API')
 *
 * @param $args['module'] name of the module this event applies to
 * @param $args['eventtype'] the event type
 * @returns int
 * @return event ID on success, false on failure
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function pubsub_adminapi_createhook($args)
{
    // Get arguments from argument array
    extract($args);

    // This has to be an argument
    if (!isset($extrainfo)) {
        $msg = xarML('Invalid #(1) in function #(2)() in module #(3)',
                     'extrainfo', 'createhook', 'pubsub');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    //FIXME: <garrett> During an article->create $extrainfo['cid'] does not exist. Instead
    // the array $extrainfo['cids'] exists. Is this because an article can have multiple categories?
    // Q: What is hcid? it's in the extrainfo...
    // Q: If cid is an array, why are we returning a singleton?
    // Q: should we return an array? which array location do we check?
    $cid = '';
    if (isset($extrainfo['cid'])) {
        $cid = $extrainfo['cid'];
    } elseif (isset($extrainfo['cids'][0])) {
        $cid = $extrainfo['cids'][0];
    } else {
        // wtf - how'd we get here
        $msg = xarML('Invalid #(1) in function #(2)() in module #(3)',
                 'cid', 'createhook', 'pubsub');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                           new SystemException($msg));
    }
    $itemtype = $extrainfo['itemtype'];
    //FIXME: <garrett> groupdescr does not get passed in from article->create
    //       where should this REALLY come from.
    //$groupdescr = $extrainfo['groupdescr'];
    $groupdescr = "Fixme: Group Description";

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

	// if event already exists then just return;
    if (!$result->EOF) {
        return TRUE;
    }

    // Get next ID in table
    $eventid = $dbconn->GenId($pubsubeventstable);

    // Add item to events table
    $query = "INSERT INTO $pubsubeventstable (
              xar_eventid,
              xar_modid,
	          xar_itemtype,
	          xar_groupdescr)
            VALUES (
              $eventid,
              " . xarVarPrepForStore($modid) . ",
              " . xarVarPrepForStore($itemtype) . ",
              '" . xarvarPrepForStore($groupdescr) . "')";

    $dbconn->Execute($query);
    if (!$result) return;

    // Get the ID of the item that was inserted
    $eventid = $dbconn->PO_Insert_ID($pubsubeventstable, 'xar_eventid');

    $flag = true; // what is this???

    // Add category to event categories table
    $query = "INSERT INTO $pubsubeventcidstable (
              xar_eid,
              xar_cid,
              xar_flag)
            VALUES (
              " . xarVarPrepForStore($eventid) . ",
              " . xarVarPrepForStore($cid) . ",
              " . xarVarPrepForStore($flag) . ")";

    $dbconn->Execute($query);
    if (!$result) return;

    // return eventID
    return $eventid;
}

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
        $msg = xarML('Invalid #(1) in function #(2)() in module #(3)',
                     'extrainfo', 'deletehook', 'pubsub');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
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
}

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
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();
    $pubsubeventstable = $xartable['pubsub_events'];

    // Delete item from events table
    $query = "DELETE FROM $pubsubeventstable
            WHERE xar_eventid = " . xarVarPrepForStore($eventid);
    $dbconn->Execute($query);
    if (!$result) return;

    // Delete item from event categoriess table
    $query = "DELETE FROM $pubsubeventcidstable
            WHERE xar_eventid = " . xarVarPrepForStore($eventid) ;
    $dbconn->Execute($query);
    if (!$result) return;

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
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function pubsub_adminapi_updateevent($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    $invalid = array();
    if (!isset($eventid) || !is_numeric($eventid)) {
        $invalid[] = 'eventid';
    }
    if (!isset($modid) || !is_numeric($modid)) {
        $invalid[] = 'module';
    }
    if (!isset($itemtype) || !is_numberic($itemtype)) {
        $invalid[] = 'eventtype';
    }
    if (!isset($groupdescr) || !is_string($groupdescr)) {
        $invalid[] = 'groupdescr';
    }
    //if (!isset($actionid) || !is_numeric($actionid)) {
    //    $invalid[] = 'actionid';
    //}
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) function #(3)() in module #(4)',
                    join(', ',$invalid), 'updateevent', 'Pubsub');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    // Security check
    if (!xarSecurityCheck('EditPubSub', 1, 'item', "All:$eventid:All:All")) return;

    // Get database setup
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();
    $pubsubeventstable = $xartable['pubsub_events'];

    // Update the item
    $query = "UPDATE $pubsubeventstable
              SET xar_modid = " . xarVarPrepForStore($module) . ",
                  xar_itemtype = " . xarVarPrepForStore($groupdescr) . ",
                  xar_groupdescr = '" . xarVarPrepForStore($groupdescr) . "'
              WHERE xar_eventid = " . xarVarPrepForStore($eventid);
    $dbconn->Execute($query);
    if (!$result) return;

    return true;
}

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
    $dbconn->Execute($query);
    if (!$result) return;

    $nextId = $dbconn->PO_Insert_ID($pubsubprocesstable, 'xar_handlingid');

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
    $query = "SELECT xar_pubsubid,
    		         xar_objectid
              FROM $pubsubprocesstable
              WHERE xar_status = " . xarVarPrepForStore('pending');
    $result = $dbconn->Execute($query);
    if (!$result) return;

    // set count to 0
    $count = 0;

    while (!$result->EOF) {
        // run the job passing it the pubsub and object ids.
        pubsub_adminapi_runjob($result->fields[0], $result->fields[1]);
        $count++;
        $result->MoveNext();
    }
    return $count;
}

/**
 * run the job
 * @param $args['pubsubid'] the subscription id
 * @param $args['objectid'] the specific object in the module
 * @returns bool
 * @return true on success, false on failure
 * @raise BAD_PARAM, DATABASE_ERROR
 */
function pubsub_adminapi_runjob($args)
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
        $msg = xarML('Invalid #(1) function #(3)() in module #(4)',
                    join(', ',$invalid), 'runjob', 'Pubsub');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    // Database information
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();
    $pubsubregtable = $xartable['pubsub_reg'];

    // Get info on job to run
    $query = "SELECT xar_actionid,
    		         xar_userid,
    		         xar_eventid
              FROM $pubsubregtable
              WHERE xar_pubsubid = " . xarVarPrepForStore($pubsubid);
    $result   = $dbconn->Execute($query);
    if (!$result) return;

    $actionid = $result->fields[0];
    $userid   = $result->fields[1];
    $eventid  = $result->fields[2];
    $info = xarUserGetVar('email',$userid);
    $name = xarUserGetVar('uname',$userid);

    if ($actionid = "mail" || $actionid = "htmlmail") {
       	// Database information
    	$pubsubtemplatetable = $xartable['pubsub_template'];
    	// Get info on job to run
    	$query = "SELECT xar_template
                  FROM $pubsubtemplatetable
                  WHERE xar_eventid = " . xarVarPrepForStore($eventid);
    	$result   = $dbconn->Execute($query);
	if (!$result) return;

  	$template = $result->fields[0];
	// *** TODO  ***
	// need to define some variables for user firstname and surname,etc.
	// might not be able to use the normal BL user vars as they would
	// probabaly expand to currently logged in user, not the user for
	// this event.
	// need to create the $tplData array with all the information in it


    // Check for exceptions
    if (!isset($item) && xarExceptionMajor() != XAR_NO_EXCEPTION) return;

    // call BL with the template to parse it and generate the HTML
    $html = xarTplString($template, $tplData);
    $plaintext = strip_tags($html);

	if ($action = "htmlmail") {
	    $boundary = "b" . md5(uniqid(time()));
	    $message = "From: xarConfigGetVar('adminmail')\r\nReply-to: xarConfigGetVar('adminmail')\r\n";
	    $message .= "Content-type: multipart/mixed; ";
	    $message .= "boundary = $boundary\r\n\r\n";
	    $message .= "This is a MIME encoded message.\r\n\r\n";
	    // first the plaintext message
	    $message .= "--$boundary\r\n";
	    $message .= "Content-type: text/plain\r\n";
	    $message .= "Content-Transfer-Encoding: base64";
	    $message .= "\r\n\r\n" . chunk_split(base64_encode($plaintext)) . "\r\n";
	    // now the HTML version
	    $message .= "--$boundary\r\n";
	    $message .= "Content-type: text/html\r\n";
	    $message .= "Content-Transfer-Encoding: base64";
	    $message .= "\r\n\r\n" . chunk_split(base64_encode($html)) . "\r\n";
	 } else {
	    // plaintext mail
	    $message=$plaintext;
	 }
	 // Send the mail using the mail module
	 if (!xarModAPIFunc('mail',
                 'admin',
                 'sendmail',
                 array('info'     => $info,
                       'name'     => $name,
                       'subject'  => $subject,
                       'message'  => $message,
                       'from'     => $fmail,
                       'fromname' => $fname))) return;

         // delete job from queue now it has run
         pubsub_adminapi_deljob($handlingid);
    } else {
        // invalid action - update queue accordingly
    	pubsub_adminapi_updatejob($handlingid,$pubsubid,$objectid,'error');
    	$msg = xarML('Invalid #(1) action',
                     'Pubsub');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                 new SystemException($msg));
        return;
    }
    return true;
}

/**
 * delete a pubsub job from the queue
 * @param $args['handlingid'] ID of the job to delete
 * @returns bool
 * @return true on success, false on failure
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function pubsub_adminapi_deljob($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    $invalid = array();
    if (!isset($handlingid) || !is_numeric($handlingid)) {
        $invalid[] = 'handlingid';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) function #(3)() in module #(4)',
                    join(', ',$invalid), 'deljob', 'Pubsub');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    // Security check
    if (!xarSecurityCheck('DeletePubSub', 1, 'item', "All:All:$handlingid:All")) return;

    // Get datbase setup
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();
    $pubsubprocesstable = $xartable['pubsub_process'];

    // Delete item
    $query = "DELETE FROM $pubsubprocesstable
              WHERE xar_handlingid = " . xarVarPrepForStore($handlingid);
    $dbconn->Execute($query);
    if (!$result) return;

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
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function pubsub_adminapi_updatejob($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    $invalid = array();
    if (!isset($handlingid) || !is_numeric($handlingid)) {
        $invalid[] = 'handlingid';
    }
    if (!isset($pubsubid) || !is_numeric($pubsubid)) {
        $invalid[] = 'pubsubid';
    }
    if (!isset($objectid) || !is_numeric($objectid)) {
        $invalid[] = 'objectid';
    }
    if (!isset($status) || !is_string($status)) {
        $invalid[] = 'status';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) function #(3)() in module #(4)',
                    join(', ',$invalid), 'updatejob', 'Pubsub');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    // Security check
    if (!xarSecurityCheck('EditPubSub', 1, 'item', "All:All:$handlingid:All")) return;

    // Get database setup
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();
    $pubsubprocesstable = $xartable['pubsub_process'];

    // Update the item
    $query = "UPDATE $pubsubprocesstable
              SET xar_pubsubid = " . xarVarPrepForStore($pubsubid) . ",
                  xar_objectid = " . xarVarPrepForStore($objectid) . ",
                  xar_status = '" . xarVarPrepForStore($status) . "'
            WHERE xar_handlingid = " . xarVarPrepForStore($handlingid);
    $dbconn->Execute($query);
    if (!$result) return;

    return true;
}

/**
 * create a new pubsub template
 * @param $args['eventid'] name of the event this template applies to
 * @param $args['template'] the template text
 * @returns int
 * @return template ID on success, false on failure
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function pubsub_adminapi_addtemplate($args)
{
    // Get arguments from argument array
    extract($args);
    $invalid = array();
    if (!isset($template) || !is_string($template)) {
        $invalid[] = 'template';
    }
    if (!isset($eventid) || !is_numeric($eventid)) {
        $invalid[] = 'eventid';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) function #(3)() in module #(4)',
                    join(', ',$invalid), 'addtemplate', 'Pubsub');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    // Security check
    if (!xarSecurityCheck('AddPubSub')) return;

    // Get datbase setup
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();
    $pubsubtemplatetable = $xartable['pubsub_template'];

    // check this event isn't already in the DB
    $query = "SELECT xar_templateid
              FROM $pubsubtemplatetable
              WHERE xar_eventid " . xarVarPrepForStore($eventid);
    $result = $dbconn->Execute($query);
    if (!$result) return;

    if (count($result) > 0) {
        $msg = xarML('Item already exists in function #(1)() in module #(2)',
                    'addtemplate', 'Pubsub');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                      new SystemException($msg));
        return;
    }

    // Get next ID in table
    $nextId = $dbconn->GenId($pubsubtemplatetable);

    // Add item
    $query = "INSERT INTO $pubsubtemplatetable (
              xar_templateid,
              xar_eventid,
              xar_template)
            VALUES (
              $nextId,
              " . xarVarPrepForStore($eventid) . ",
              '" . xarvarPrepForStore($template) . "')";
    $dbconn->Execute($query);
    if (!$result) return;

    $nextId = $dbconn->PO_Insert_ID($pubsubtemplatetable, 'xar_templateid');

    // return eventID
    return $nextId;
}

/**
 * delete a pubsub template
 * @param $args['templateid'] ID of the item
 * @returns bool
 * @return true on success, false on failure
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function pubsub_adminapi_deltemplate($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    $invalid = array();
    if (!isset($templateid) || !is_numeric($templateid)) {
        $invalid[] = 'templateid';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) function #(3)() in module #(4)',
                    join(', ',$invalid), 'deltemplate', 'Pubsub');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    // Security check
    if (!xarSecurityCheck('DeletePubSub')) return;

    // Get datbase setup
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();
    $pubsubtemplatetable = $xartable['pubsub_template'];

    // Delete item
    $query = "DELETE FROM $pubsubtemplatetable
              WHERE xar_templateid = " . xarVarPrepForStore($templateid);
    $dbconn->Execute($query);
    if (!$result) return;

    return true;
}

/**
 * update an existing pubsub template
 * @param $args['templateid'] the ID of the item
 * @param $args['eventid'] the new eventid of the item
 * @param $args['template'] the new template text of the item
 * @returns bool
 * @return true on success, false on failure
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function pubsub_adminapi_updatetemplate($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    $invalid = array();
    if (!isset($eventid) || !is_numeric($eventid)) {
        $invalid[] = 'eventid';
    }
    if (!isset($temnplateid) || !is_numeric($templateid)) {
        $invalid[] = 'templateid';
    }
    if (!isset($template) || !is_string($template)) {
        $invalid[] = 'template';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) function #(3)() in module #(4)',
                    join(', ',$invalid), 'updatetemplate', 'Pubsub');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    // Security check
    if (!xarSecurityCheck('EditPubSub', 1, 'item', "All:All:All:$templateid")) return;

    // Get database setup
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();
    $pubsubtemplatetable = $xartable['pubsub_template'];

    // Update the item
    $query = "UPDATE $pubsubtemplatetable
              SET xar_template = '" . xarVarPrepForStore($template) . "',
                  xar_eventid = " . xarVarPrepForStore($eventid) . "
              WHERE xar_templateid = " . xarVarPrepForStore($templateid);
    $dbconn->Execute($query);
    if (!$result) return;

    return true;
}

/**
 * utility function pass individual menu items to the main menu
 *
 * @author the Example module development team
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function pubsub_adminapi_getmenulinks()
{
    if (xarSecurityCheck('EditPubSub', 0)) {

        $menulinks[] = Array('url'   => xarModURL('Pubsub',
                                                  'admin',
                                                  'view'),
                              'title' => xarML('View and Edit Pubsub Events'),
                              'label' => xarML('View'));
    }

    if (empty($menulinks)){
        $menulinks = '';
    }

    return $menulinks;
}
?>
