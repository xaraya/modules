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

?>
