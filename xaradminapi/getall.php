<?php
/**
 * Pubsub Module
 *
 * @package modules
 * @subpackage pubsub module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/181.html
 * @author Pubsub Module Development Team
 * @author Chris Dudley <miko@xaraya.com>
 * @author Garrett Hunter <garrett@blacktower.com>
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
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'UNABLE_TO_LOAD', new SystemException($msg));
        return;
    }

    $dbconn =& xarDB::getConn();
    $xartable =& xarDB::getTables();
    $modulestable = $xartable['modules'];
    $categoriestable = $xartable['categories'];
    $pubsubeventstable = $xartable['pubsub_events'];
    $pubsubregtable = $xartable['pubsub_reg'];

    $query = "SELECT $pubsubeventstable.eventid
                    ,$modulestable.name
                    ,$pubsubeventstable.itemtype
                    ,$categoriestable.name
                    ,$categoriestable.id
                    ,COUNT($pubsubregtable.userid) AS numsubscribers
                FROM $pubsubeventstable
           LEFT JOIN $modulestable
                  ON $pubsubeventstable.modid = $modulestable.regid
           LEFT JOIN $categoriestable
                  ON $pubsubeventstable.cid = $categoriestable.id
           LEFT JOIN $pubsubregtable
                  ON $pubsubeventstable.eventid = $pubsubregtable.eventid
            GROUP BY $pubsubeventstable.eventid
                    ,$modulestable.name
                    ,$pubsubeventstable.itemtype
                    ,$categoriestable.name
                    ,$categoriestable.id";

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
