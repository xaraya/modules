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

    // do nothing if user not logged in
    if (!xarUserIsLoggedIn()) {
         return;
    }

    extract($args);
    if (!isset($extrainfo)) {
         $extrainfo = array();
    }

    $cid = $objectid; // assuming categories have display hooks someday
    $itemtype = 0;
    if (isset($extrainfo) && is_array($extrainfo)) {
        if (isset($extrainfo['itemtype']) && is_numeric($extrainfo['itemtype'])) {
            $itemtype = $extrainfo['itemtype'];
        }
        if (isset($extrainfo['cid']) && is_numeric($extrainfo['cid'])) {
            $cid = $extrainfo['cid'];
        }
        if (isset($extrainfo['module']) && is_string($extrainfo['module'])) {
            $modname = $extrainfo['module'];
        }
    }

    // When called via hooks, the module name may be empty, so we get it from
    // the current module
    if (empty($modname)) {
        $modname = xarModGetName();
    }

    $modid = xarModGetIDFromName($modname);

    $data['modid'] = xarVarPrepForDisplay($modid);
    $data['cid'] = xarVarPrepForDisplay($cid);
    $data['itemtype'] = xarVarPrepForDisplay($itemtype);

    return $data;
}

/**
 * subscribe user to a pubsub element
 * @param $args['modid'] module ID of event 
 * @param $args['cid'] cid of event
 * @param $args['itemtype'] itemtype of event 
 * @returns output
 * @return output with pubsub information
 */
function pubsub_user_subscribe($args)
{
    list($modid,$cid,$itemtype) = xarVarCleanFromInput('modid','cid','itemtype');

    extract($args);
    // Argument check
    $invalid = array();
    if (!isset($modid) || !is_numeric($modid)) {
        $invalid[] = 'modid';
    }
    if (!isset($cid) || !is_numeric($cid)) {
        $invalid[] = 'cid';
    }
    if (!isset($itemtype) || !is_numeric($itemtype)) {
        $invalid[] = 'itemtype';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) in function #(2)() in module #(3)',
        join(', ',$invalid), 'subscribe', 'Pubsub');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }
		    
    // Database information
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();
    $pubsubeventstable = $xartable['pubsub_events'];
    $pubsubeventcidstable = $xartable['pubsub_eventcids'];

    // make sure event exists, create it if necessary
    if (!xarModAPILoad('pubsub','admin')) return;    
    pubsub_adminapi_createhook($cid, $itemtype, $modid);

    // fetch eventid to subscribe to
    $query = "SELECT $pubsubeventstable.xar_eventid
 	    FROM  $pubsubeventstable, $pubsubeventcidstable
	    WHERE $pubsubeventstable.xar_modid = '" . xarVarPrepForStore($modid) . "'
	    AND   $pubsubeventstable.xar_itemtype = '" . xarVarPrepForStore($itemtype) . "'
        AND   $pubsubeventstable.xar_eventid = $pubsubeventcidstable.xar_eid
	    AND   $pubsubeventcidstable.xar_cid = '" . xarVarPrepForStore($cid) . "'";
        
    $result = $dbconn->Execute($query);
    if (!$result) return;
    $eventid = $result->fields[0];

    if (!xarModAPILoad('pubsub','user')) return;    
// TODO: fill in eventid *and* actionid (wherever that is supposed to come from)
// AM hardcoding actionid to 'mail' for now, will have to work out options for htmlmail etc. later
    pubsub_userapi_adduser($eventid,'mail');

    return;
}

/**
 * unsubscribe user from a pubsub element
 * @param $args['modid'] module ID of event 
 * @param $args['cid'] cid of event
 * @param $args['itemtype'] itemtype of event 
 * @returns output
 * @return output with pubsub information
 */
function pubsub_user_unsubscribe($args)
{
    list($modid,$cid,$itemtype) = xarVarCleanFromInput('modid','cid','itemtype');

    extract($args);
    // Argument check
    $invalid = array();
    if (!isset($modid) || !is_numeric($modid)) {
        $invalid[] = 'modid';
    }
    if (!isset($cid) || !is_numeric($cid)) {
        $invalid[] = 'cid';
    }
    if (!isset($itemtype) || !is_numeric($itemtype)) {
        $invalid[] = 'itemtype';
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
    $pubsubeventcidstable = $xartable['pubsub_eventcids'];

    // fetch eventid to subscribe to
    $query = "SELECT xar_eventid
 	    FROM $pubsubeventstable, $pubsubeventcidstable
	    WHERE $pubsubeventstable.xar_modid = '" . xarVarPrepForStore($modid) . "'
	    AND   $pubsubeventstable.xar_itemtype = '" . xarVarPrepForStore($itemtype) . "'
        AND   $pubsubeventstable.xar_eventid = $pubsubeventcidstable.xar_eid
	    AND   $pubsubeventcidstable.xar_cid = '" . xarVarPrepForStore($cid) . "'";
        
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
