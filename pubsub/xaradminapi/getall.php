<?php
/**
 * File: $Id$
 *
 * Pubsub getall
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
 * Get all events
 *
 * @returns array
 * @return array of events
*/
function pubsub_adminapi_getall($args)
{
    extract($args);
    $events = array();
    if (!xarSecurityCheck('AdminPubSub', 0)) {
        return $events;
    }

    // Load categories API
    if (!xarModAPILoad('categories', 'user')) {
        $msg = xarML('Unable to load #(1) #(2) API','categories','user');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'UNABLE_TO_LOAD', new SystemException($msg));
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $modulestable = $xartable['modules'];
    $categoriestable = $xartable['categories'];
    $pubsubeventstable = $xartable['pubsub_events'];
    $pubsubregtable = $xartable['pubsub_reg'];

    $query = "SELECT $pubsubeventstable.xar_eventid
                    ,$modulestable.xar_name
                    ,$pubsubeventstable.xar_itemtype
                    ,$categoriestable.xar_name
                    ,$categoriestable.xar_cid
                    ,COUNT($pubsubregtable.xar_userid) AS numsubscribers
                FROM $pubsubeventstable
                    ,$modulestable
                    ,$categoriestable
                    ,$pubsubregtable
               WHERE $pubsubeventstable.xar_modid = $modulestable.xar_regid
                 AND $pubsubeventstable.xar_cid = $categoriestable.xar_cid
                 AND $pubsubeventstable.xar_eventid = $pubsubregtable.xar_eventid
            GROUP BY $pubsubeventstable.xar_eventid
                    ,$modulestable.xar_name
                    ,$pubsubeventstable.xar_itemtype
                    ,$categoriestable.xar_name
                    ,$categoriestable.xar_cid";

    $result =& $dbconn->Execute($query);
    if (!$result) return;

    for (; !$result->EOF; $result->MoveNext()) {
        list($eventid, $modname, $itemtype, $catname, $cid, $numsubscribers) = $result->fields;
        if (xarSecurityCheck('AdminPubSub', 0)) {
            $events[] = array('eventid'        => $eventid
                             ,'modname'        => $modname
                             ,'itemtype'       => $itemtype
                             ,'catname'        => $catname
                             ,'cid'            => $cid
                             ,'numsubscribers' => $numsubscribers
                             );
        }
    }

    $result->Close();

    return $events;
}

?>
