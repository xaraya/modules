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
    if (xarUserIsLoggedIn()) {
        if (!isset($userid)) {
            $userid = xarSessionGetVar('uid');
        }
    } else {
        return;
    }
//// var handling

    extract($args);
    if (!isset($extrainfo)) {
         $extrainfo = array();
    }

	/*(
	 * Validate parameters
	 */
	$invalid = array();
	if(!isset($extrainfo) || !is_array($extrainfo)) {
		$invalid[] = 'extrainfo';
	} elseif(isset($extrainfo['cid'])) {
		
		$cid = $extrainfo['cid'];
		
		if(isset($extrainfo['itemtype']) && is_numeric($extrainfo['itemtype'])) {
			$itemtype = $extrainfo['itemtype'];
		}
        if (isset($extrainfo['cid']) && is_numeric($extrainfo['cid'])) {
            $cid = $extrainfo['cid'];
        }
        if (isset($extrainfo['module']) && is_string($extrainfo['module'])) {
            $modname = $extrainfo['module'];
        }
	} else {
		// No cid, no display sub option, do we have a cids?

//		$cidsTrue = FALSE;
//		if (isset($extrainfo['cids'])) {
//	        $cidsTrue = TRUE;
//	    }
//		if ($cidsTrue) {
//			$cidsMsg = "have cids";
//		} else {
//			$cidsMsg = "have NO cids";
//		}
//        $msg = xarML('No cid, and #(1) for #(2) function #(3)() in module #(4)',
//            $cidsMsg, 'user', 'displayicon', 'pubsub');
//        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
//            				new SystemException($msg));
		
		return array('donotdisplay'=>TRUE);
	}
	
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'admin', 'updateItems', __ADDRESSBOOK__);
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            				new SystemException($msg));
    } else {
    }
///

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
        if (isset($extrainfo['returnurl']) && is_string($extrainfo['returnurl'])) {
            $returnurl = $extrainfo['returnurl'];
        }
    }

    // When called via hooks, the module name may be empty, so we get it from
    // the current module
    if (empty($modname)) {
        $modname = xarModGetName();
    }

    $modid = xarModGetIDFromName($modname);

/// check for unsubscribe
    /**
     * Fetch the eventid to check
     */
    // Database information
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $pubsubeventstable = $xartable['pubsub_events'];
    $pubsubeventcidstable = $xartable['pubsub_eventcids'];
    $pubsubregtable = $xartable['pubsub_reg'];

    $query = "SELECT xar_pubsubid
                FROM $pubsubeventstable, $pubsubeventcidstable, $pubsubregtable
               WHERE $pubsubeventstable.xar_modid = '" . xarVarPrepForStore($modid) . "'
                 AND $pubsubeventstable.xar_itemtype = '" . xarVarPrepForStore($itemtype) . "'
                 AND $pubsubeventstable.xar_eventid = $pubsubeventcidstable.xar_eid
                 AND $pubsubeventstable.xar_eventid = $pubsubregtable.xar_eventid
                 AND $pubsubeventcidstable.xar_cid = '" . xarVarPrepForStore($cid) . "'";

    $result = $dbconn->Execute($query);
    if (!$result) return;
    if ($result->EOF) {
        /**
         * If we get a hit on pubsub_reg, that mean we are already subscribed
         */
        $data['subscribe'] = TRUE;
    } // end if

    $data['modname'] = $modname;
    $data['modid'] = xarVarPrepForDisplay($modid);
    $data['cid'] = xarVarPrepForDisplay($cid);
    $data['itemtype'] = xarVarPrepForDisplay($itemtype);
    $data['returnurl'] = rawurlencode($returnurl);

    return $data;

} // END displayicon

?>