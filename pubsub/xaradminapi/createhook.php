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
    // the array $extrainfo['cids'] exists. Is this because an article can have 
    // multiple categories?
    // Q: What is hcid? it's in the extrainfo...
    // Q: If cid is an array, why are we returning a singleton? I think we should be 
    // subscribing the user to all cats assoc'd with the article, thus creating 
    // multiple events
    $cid = '';
    if (isset($extrainfo['cid']) && is_numeric($extrainfo['cid'])) {
        $cid = $extrainfo['cid'];
    } elseif (isset($extrainfo['cids'][0]) && is_numeric($extrainfo['cids'][0])) {
        $cid = $extrainfo['cids'][0];
    } else {
        // Do nothing if we do not get a cid. Go check displayicon() to see why
        // it even allowed the subscribe option, it should not!

	// FIXME: <garrett> change logic so that we do not have to make a 
	// return here

        return;
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

} // END createhook

?>