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
 * @author Garrett Hunter <garrett@xaraya.com>
 */

/**
 * Get the subscribers for a particular event
 *
 * @returns array
 * @return array of events
*/
function pubsub_adminapi_getsubscribers($args)
{
	$subscribers = array();
    /*
     * lets get...
     *  - username (need to get from db)
     *  - subscribe date (need to get from db)
     *  - category name (should have from passed in cid
     *  - ??

     */
    extract($args);
    $events = array();
    if (!xarSecurityCheck('AdminPubSub', 0)) {
        return $events;
    }

    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $rolestable           = $xartable['roles'];
    $modulestable         = $xartable['modules'];
    $pubsubeventcidstable = $xartable['pubsub_eventcids'];
    $pubsubeventstable    = $xartable['pubsub_events'];
    $pubsubregtable       = $xartable['pubsub_reg'];

    $query = "SELECT $rolestable.xar_uname  AS username
                    ,$modulestable.xar_name AS modname
                    ,$pubsubeventstable.xar_modid AS modid
                    ,$pubsubregtable.xar_subdate AS subdate
                    ,$pubsubregtable.xar_pubsubid AS pubsubid
                FROM $rolestable
                    ,$modulestable
                    ,$pubsubeventcidstable
                    ,$pubsubeventstable
                    ,$pubsubregtable
               WHERE $pubsubeventcidstable.xar_cid  = $cid
                 AND $pubsubeventcidstable.xar_eid  = $pubsubregtable.xar_eventid
                 AND $pubsubeventstable.xar_modid   = $modulestable.xar_regid
                 AND $pubsubeventstable.xar_eventid = $pubsubeventcidstable.xar_eid
                 AND $pubsubregtable.xar_userid     = $rolestable.xar_uid";

    $result =& $dbconn->Execute($query);
    if (!$result) return;

    for (; !$result->EOF; $result->MoveNext()) {
        list($username
            ,$modname
            ,$modid
            ,$subdate
            ,$pubsubid
           ) = $result->fields;
        if (xarSecurityCheck('AdminPubSub', 0)) {
            $subscribers[] = array('username'  => $username
                                  ,'modname'   => $modname
                                  ,'modid'     => $modid
                                  ,'subdate'   => xarLocaleFormatDate($subdate,"%a, %d-%B-%Y")
                                  ,'pubsubid'  => $pubsubid
                                  );
        }
    }

    $result->Close();

    return $subscribers;

} // END getsubscribers

?>
