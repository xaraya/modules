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
 * @author Marc Lutolf <mfl@netspan.ch>
 */
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
    if (!xarSecurityCheck('AdminPubSub', 0)) {
        return $events;
    }

    // Load categories API
    if (!xarModAPILoad('categories', 'user')) {
        $msg = xarML('Unable to load #(1) #(2) API','categories','user');
        throw new Exception($msg);
    }

    $dbconn =& xarDB::getConn();
    $xartable =& xarDB::getTables();
    $modulestable = $xartable['modules'];
    $categoriestable = $xartable['categories'];
    $pubsubeventstable = $xartable['pubsub_events'];
    $pubsubsubscriptionstable = $xartable['pubsub_subscriptions'];

    $query = "SELECT $pubsubeventstable.id
                    ,$modulestable.name
                    ,$pubsubeventstable.itemtype
                    ,$categoriestable.name
                    ,$categoriestable.id
                    ,COUNT($pubsubsubscriptionstable.userid) AS numsubscribers
                FROM $pubsubeventstable
           LEFT JOIN $modulestable
                  ON $pubsubeventstable.module_id = $modulestable.regid
           LEFT JOIN $categoriestable
                  ON $pubsubeventstable.cid = $categoriestable.id
           LEFT JOIN $pubsubsubscriptionstable
                  ON $pubsubeventstable.id = $pubsubsubscriptionstable.event_id
            GROUP BY $pubsubeventstable.id
                    ,$modulestable.name
                    ,$pubsubeventstable.itemtype
                    ,$categoriestable.name
                    ,$categoriestable.id";

    $result =& $dbconn->Execute($query);
    if (!$result) return;

    for (; !$result->EOF; $result->MoveNext()) {
        list($id, $modname, $itemtype, $catname, $cid, $numsubscribers) = $result->fields;
        if (xarSecurityCheck('AdminPubSub', 0)) {
            $events[] = array('id'        => $id
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
