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
 * @author Garrett Hunter <garrett@blacktower.com>
 */

/**
 * subscribe user to a pubsub element
 * @param $args['modid'] module ID of event
 * @param $args['cid'] cid of event
 * @param $args['itemtype'] itemtype of event
 * @returns output
 * @return output with pubsub information
 */
function pubsub_user_subscribe()
{
    list($modid
        ,$cid
        ,$itemtype
        ,$returnurl
        ) = xarVarCleanFromInput('modid'
                                ,'cid'
                                ,'itemtype'
                                ,'returnurl'
                                );
    $returnurl = rawurldecode($returnurl);

    // Argument check
    $invalid = array();
    if (!isset($returnurl) || !is_string($returnurl)) {
        $invalid[] = 'returnurl';
    }
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

    // What is groupdescr???
    if (!isset($groupdescr))
        $groupdescr = 'Subscribe';

    // Database information
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();
    $pubsubeventstable = $xartable['pubsub_events'];
    $pubsubeventcidstable = $xartable['pubsub_eventcids'];

    // make sure event exists, create it if necessary
    $extrainfo = array('modid' => $modid,
                       'cid' => $cid,
                       'itemtype' => $itemtype,
                       'groupdescr' => $groupdescr);

    if (!xarModAPIFunc('pubsub',
                       'admin',
                       'createhook',
                        array('extrainfo' => $extrainfo))) {
        $msg = xarML('Step2 #(1) in function #(2)() in module #(3)',
        join(', ',$invalid), 'subscribe', 'Pubsub');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
    }

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

// TODO: fill in eventid *and* actionid (wherever that is supposed to come from)
// AM hardcoding actionid to 1 for now, will have to work out options for htmlmail etc. later
    if (!xarModAPIFunc('pubsub',
                       'user',
                       'adduser',
                        array('eventid' => $eventid,
                              'actionid' => 1))) {
        $msg = xarML('Bad return from #(1) in function #(2)() in module #(3)',
                     'adduser', 'subscribe', 'Pubsub');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
    }

    xarResponseRedirect($returnurl);
    return true;

} // END subscribe

?>