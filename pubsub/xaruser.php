<?php
/**
 * File: $Id$
 * 
 * Pubsub User Interface
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
 * the main user function
 */
function pubsub_user_main()
{
    // Return output
    return xarML('This module has no user interface *except* via display hooks');
}

/**
 * display pubsub element next to a registered event
 * @param $args['extrainfo'] URL to return 
 * @returns output
 * @return output with pubsub information
 */
function pubsub_user_displayicon($args)
{
// This function will display the output code next to an item that has a 
// registered pubsub event associated with it.
// It will display an icon to subscribe to the event if the user is registered
// if they arent then it will display nothing.
// If they are logged in and have already subscribed it will display an
// unsubscribe option.

    extract($args);
    if (!isset($extrainfo)) {
         $extrainfo = array();
    }
		    
    $cid = $extrainfo['cid'];
    $iid = $extrainfo['iid'];
    // When called via hooks, the module name may be empty, so we get it from
    // the current module
    if (empty($extrainfo['module'])) {
    $modname = xarModGetName();
    } else {
         $modname = $extrainfo['module'];
    }

    $modid = xarModGetIDFromName($modname);
    // do nothing if user not logged in
    if (!xarUserIsLoggedIn()) {
         return;
    }

    $data['modid'] = xarVarPrepForDisplay($modid);
    $data['cid'] = xarVarPrepForDisplay($cid);
    $data['iid'] = xarVarPrepForDisplay($iid);

    return $data;
}

/**
 * subscribe user to a pubsub element
 * @param $args['modid'] module ID of event 
 * @param $args['cid'] cid of event
 * @param $args['iid'] iid of event 
 * @returns output
 * @return output with pubsub information
 */
function pubsub_user_subscribe($args)
{

    extract($args);
    // Argument check
    $invalid = array();
    if (!isset($modid) || !is_numeric($modid)) {
        $invalid[] = 'modid';
    }
    if (!isset($cid) || !is_numeric($cid)) {
        $invalid[] = 'cid';
    }
    if (!isset($iid) || !is_numeric($iid)) {
        $invalid[] = 'iid';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) in function #(3)() in module #(4)',
        join(', ',$invalid), 'subscribe', 'Pubsub');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }
		    
    // Database information
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();
    $pubsubeventstable = $xartable['pubsub_events'];

    // fetch eventid to subscribe to
    $query = "SELECT xar_eventid
 	    FROM $pubsubeventstable
    	    WHERE xar_modid '" . xarVarPrepForStore($modid) . "',
    	          xar_cid '" . xarVarPrepForStore($cid) . "',
  	          xar_iid '" . xarVarPrepForStore($iid) . "'";
    $result = $dbconn->Execute($query);
    if (!$result) return;
    $eventid = $result->fields[0];

    if (!xarModAPILoad('pubsub','user')) return;    
    pubsub_userapi_adduser($eventid);

    return;
}

/**
 * unsubscribe user from a pubsub element
 * @param $args['modid'] module ID of event 
 * @param $args['cid'] cid of event
 * @param $args['iid'] iid of event 
 * @returns output
 * @return output with pubsub information
 */
function pubsub_user_unsubscribe($args)
{

    extract($args);
    // Argument check
    $invalid = array();
    if (!isset($modid) || !is_numeric($modid)) {
        $invalid[] = 'modid';
    }
    if (!isset($cid) || !is_numeric($cid)) {
        $invalid[] = 'cid';
    }
    if (!isset($iid) || !is_numeric($iid)) {
        $invalid[] = 'iid';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) in function #(3)() in module #(4)',
        join(', ',$invalid), 'unsubscribe', 'Pubsub');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }
		    
    // Database information
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();
    $pubsubeventstable = $xartable['pubsub_events'];

    // fetch eventid to subscribe to
    $query = "SELECT xar_eventid
 	    FROM $pubsubeventstable
    	    WHERE xar_modid '" . xarVarPrepForStore($modid) . "',
    	          xar_cid '" . xarVarPrepForStore($cid) . "',
  	          xar_iid '" . xarVarPrepForStore($iid) . "'";
    $result = $dbconn->Execute($query);
    if (!$result) return;
    $eventid = $result->fields[0];
    
    if (!xarModAPILoad('pubsub','user')) return;    
    pubsub_userapi_deluser($eventid);

    return;
}

/**
 * remove user from a pubsub element
 * @param $args['eventid'] event ID 
 * @returns output
 * @return output with pubsub information
 */
function pubsub_user_remove($args)
{

    extract($args);
    // Argument check
    $invalid = array();
    if (!isset($eventid) || !is_numeric($eventid)) {
        $invalid[] = 'eventid';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) in function #(3)() in module #(4)',
        join(', ',$invalid), 'remove', 'Pubsub');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }
		    
    if (!xarModAPILoad('pubsub','user')) return;    
    pubsub_userapi_deluser($eventid);

    return;
}


/**
 * handle fact a user may already be subscribed and give them option to unsubscribe
 * @param $args['eventid'] event already subscribed to 
 * @returns output
 * @return output with pubsub information
 */
function pubsub_user_subscribed($args)
{

    extract($args);
    $invalid = array();
    if (!isset($actionid) || !is_numeric($actionid)) {
        $invalid[] = 'actionid';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'user', 'subscribed', 'Pubsub');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }
		    
    $data['eventid'] = xarVarPrepForDisplay($eventid);

    return $data;
}
?>
