<?php
/**
 * File: $Id$
 *
 * Pubsub User API
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
 * Add a user's subscription to an event
 * @param $args['eventid'] Event to subscribe to
 * @param $args['actionid'] Requested action for this subscription
 * @param $args['userid'] UID of User to subscribe (optional)
 * @returns bool
 * @return pubsub ID on success, false if not
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function pubsub_userapi_adduser($args)
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
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'user', 'subscribe', 'Pubsub');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    // Anonymous user cannot subscribe to events
    if (xarUserIsLoggedIn()) {
        // if no userid was supplied then subscribe the currently logged in user
        if (!isset($userid)) {
	        $userid = xarSessionGetVar('uid');
	    }
    } else {
        //FIXME: <garrett> Error handling seems a bit primative...
        xarSessionSetVar('errormsg', _PUBSUBANONERROR);

        return;
    }

    // Security check
    if (!xarSecurityCheck('ReadPubSub', 1, 'item', 'All::$eventid')) return;

    // Database information
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();
    $pubsubregtable = $xartable['pubsub_reg'];

    // check not already subscribed
    $query = "SELECT xar_pubsubid FROM $pubsubregtable";
    $result = $dbconn->Execute($query);
    if (!$result) return;

    // Get next ID in table
    $nextId = $dbconn->GenID($pubsubregtable);

    // Add item
    $query = "INSERT INTO $pubsubregtable (
              xar_pubsubid,
              xar_eventid,
              xar_userid,
              xar_actionid)
            VALUES (
              $nextId,
              " . xarVarPrepForStore($eventid) . ",
              " . xarVarPrepForStore($userid) . ",
              " . xarvarPrepForStore($actionid) . ")";
    $dbconn->Execute($query);
    if (!$result) return;

    // return pubsub ID
    $nextId = $dbconn->PO_Insert_ID($pubsubregtable, 'xar_pubsubid');

    return $nextId;
}

/**
 * delete a user's pubsub subscription
 * @param $args['pubsubid'] ID of the subscription to delete
 * @returns bool
 * @return true on success, false on failure
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function pubsub_userapi_deluser($args)
{

    // Get arguments from argument array
    extract($args);

    // Argument check
    $invalid = array();
    if (!isset($pubsubid) || !is_numeric($pubsubid)) {
        $invalid[] = 'pubsubid';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'user', 'unsubscribe', 'Pubsub');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    // Security check
    if (!xarSecurityCheck('DeletePubSub', 1, 'item', 'All::$pubsubid')) return;

    // Get datbase setup
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();
    $pubsubregtable = $xartable['pubsub_reg'];

    // Delete item
    $query = "DELETE FROM $pubsubregtable
              WHERE xar_pubsubid = '" . xarVarPrepForStore($pubsubid) . "'";
    $dbconn->Execute($query);
    if (!$result) return;

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
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'user', 'updatesubscription', 'Pubsub');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    // Security check
    if (!xarSecurityCheck('EditPubSub', 1, 'item', 'All:$pubsubid')) return;

    // Get database setup
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();
    $pubsubregtable = $xartable['pubsub_reg'];

    // Update the item
    $query = "UPDATE $pubsubregtable
              SET xar_actionid = '" . xarVarPrepForStore($actionid) . "'
              WHERE xar_pubsubid = '" . xarVarPrepForStore($pubsubid) . "'";
    $dbconn->Execute($query);
    if (!$result) return;

    return true;
}

/**
 * return a pubsub user's subscriptions
 * @param $args['userid'] ID of the user whose subscriptions to return
 * @returns array
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function pubsub_userapi_getsubscriptions($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    $invalid = array();
    if (!isset($userid) || !is_numeric($userid)) {
        $invalid[] = 'userid';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'user', 'getsubscriptions', 'Pubsub');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    // Get datbase setup
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();
    $pubsubregtable = $xartable['pubsub_reg'];

    // fetch items
    $query = "SELECT xar_pubsubid FROM $pubsubregtable
              WHERE xar_userid = '" . xarVarPrepForStore($userid) . "'";
    $dbconn->Execute($query);
    if (!$result) return;

    return $result;
}

/**
 * delete a pubsub user's subscriptions
 * this needs to be done when a user unregisters from the site.
 * @param $args['userid'] ID of the user whose subscriptions to delete
 * @returns array
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function pubsub_userapi_delsubscriptions($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    $invalid = array();
    if (!isset($userid) || !is_numeric($userid)) {
        $invalid[] = 'userid';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'user', 'delsubscriptions', 'Pubsub');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    // Get datbase setup
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();
    $pubsubregtable = $xartable['pubsub_reg'];

    // Delete item
    $query = "DELETE FROM $pubsubregtable
              WHERE xar_userid = '" . xarVarPrepForStore($userid) . "'";
    $dbconn->Execute($query);
    if (!$result) return;

    return true;
}

/**
 * Get all events
 *
 * @returns array
 * @return array of events
*/
function pubsub_userapi_getall($args)
{
    extract($args);
    $events = array();
    if (!xarSecurityCheck('ReadPubSub', 0)) {
        return $events;
    }

    // Load categories API
    if (!xarModAPILoad('categories', 'user')) {
        $msg = xarML('Unable to load #(1) #(2) API','categories','user');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'UNABLE_TO_LOAD', new SystemException($msg));
        return;
    }

    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();
    $modulestable = $xartable['modules'];
    $categoriestable = $xartable['categories'];
    $pubsubtemplatetable = $xartable['pubsub_template'];
    $pubsubeventstable = $xartable['pubsub_events'];
    $pubsubeventcidstable = $xartable['pubsub_eventcids'];
    $pubsubregtable = $xartable['pubsub_reg'];

    $query = "SELECT $modulestable.xar_name AS ModuleName,
                     $categoriestable.xar_name AS Category,
                     COUNT($pubsubregtable.xar_userid) AS NumberOfSubscribers,
                     $pubsubtemplatetable.xar_template AS Template
              FROM $pubsubeventstable,
                   $pubsubeventcidstable,
                   $modulestable,
                   $categoriestable,
                   $pubsubtemplatetable,
                   $pubsubregtable
              WHERE $pubsubeventstable.xar_modid = $modulestable.xar_id
              AND   $pubsubeventstable.xar_eventid = $pubsubeventcidstable.xar_eid
              AND   $pubsubeventcidstable.xar_cid = $categoriestable.xar_cid
              AND   $pubsubeventstable.xar_eventid = $pubsubregtable.xar_eventid
              AND   $pubsubtemplatetable.xar_eventid = $pubsubeventstable.xar_eventid
              GROUP BY $pubsubeventstable.xar_eventid";

// ???         $pubsubeventstable.xar_itemtype = $itemstable.xar_id

    $result =& $dbconn->Execute($query);
    if (!$result) return;

    for (; !$result->EOF; $result->MoveNext()) {
        list($modname, $category, $item, $numsubscribers, $template) = $result->fields;
        if (xarSecurityCheck('ReadPubSub', 0)) {
            $events[] = array('modname'       => $modname,
                             'category'       => $category,
                             'item'           => $item,
                             'numsubscribers' => $numsubscribers,
                             'template'       => $template);
        }
    }

    $result->Close();

    return $events;
}
?>
