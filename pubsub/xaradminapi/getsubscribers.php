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
 * @param $args['eventid'] the event id we're looking for
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
    if (empty($eventid) || !is_numeric($eventid)) {
        return $events;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $rolestable           = $xartable['roles'];
    $modulestable         = $xartable['modules'];
    $pubsubeventstable    = $xartable['pubsub_events'];
    $pubsubregtable       = $xartable['pubsub_reg'];

    $query = "SELECT $rolestable.xar_uname  AS username
                    ,$modulestable.xar_name AS modname
                    ,$pubsubeventstable.xar_modid AS modid
                    ,$pubsubeventstable.xar_itemtype AS itemtype
                    ,$pubsubeventstable.xar_cid AS cid
                    ,$pubsubregtable.xar_subdate AS subdate
                    ,$pubsubregtable.xar_pubsubid AS pubsubid
                FROM $rolestable
                    ,$modulestable
                    ,$pubsubeventstable
                    ,$pubsubregtable
               WHERE $pubsubeventstable.xar_eventid = $pubsubregtable.xar_eventid
                 AND $pubsubeventstable.xar_modid   = $modulestable.xar_regid
                 AND $pubsubregtable.xar_userid     = $rolestable.xar_uid
                 AND $pubsubeventstable.xar_eventid = $eventid";

    $result =& $dbconn->Execute($query);
    if (!$result) return;

    for (; !$result->EOF; $result->MoveNext()) {
        list($username
            ,$modname
            ,$modid
            ,$itemtype
            ,$cid
            ,$subdate
            ,$pubsubid
           ) = $result->fields;
        if (xarSecurityCheck('AdminPubSub', 0)) {
            $subscribers[] = array('username'  => $username
                                  ,'modname'   => $modname
                                  ,'modid'     => $modid
                                  ,'itemtype'  => $itemtype
                                  ,'cid'       => $cid
                                  ,'subdate'   => xarLocaleFormatDate("%a, %d-%B-%Y",$subdate)
                                  ,'pubsubid'  => $pubsubid
                                  );
        }
    }

    $result->Close();

    return $subscribers;

} // END getsubscribers

?>
