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
 * Get the queue of pending events
 * @return array list of events waiting to be processed
 * @throws DATABASE_ERROR
 */
function pubsub_userapi_getq($args)
{
    // Get arguments from argument array
    extract($args);

    // Database information
    $dbconn =& xarDB::getConn();
    $xartable =& xarDB::getTables();
    $pubsubprocesstable = $xartable['pubsub_process'];
    $pubsubsubscriptionstable = $xartable['pubsub_subscriptions'];
    $pubsubeventstable = $xartable['pubsub_events'];
    $pubsubtemplatestable = $xartable['pubsub_templates'];

    $modulestable = $xartable['modules'];
    $rolestable = $xartable['roles'];

    // Load categories API
    if (!xarMod::apiLoad('categories', 'user')) {
        $msg = xarML('Unable to load #(1) #(2) API', 'categories', 'user');
        throw new Exception($msg);
    }
    $categoriestable = $xartable['categories'];

    // Get all jobs in pending state
    $query = "SELECT $pubsubprocesstable.id,
                     $pubsubprocesstable.pubsub_id,
                     $pubsubprocesstable.object_id,
                     $pubsubprocesstable.template_id,
                     $pubsubprocesstable.status,
                     $pubsubsubscriptionstable.event_id,
                     $pubsubsubscriptionstable.userid,
                     $pubsubsubscriptionstable.action_id,
                     $pubsubsubscriptionstable.subdate,
                     $pubsubsubscriptionstable.email,
                     $pubsubeventstable.module_id,
                     $pubsubeventstable.itemtype,
                     $pubsubeventstable.cid,
                     $pubsubeventstable.extra,
                     $pubsubtemplatestable.name,
                     $modulestable.name,
                     $rolestable.uname,
                     $categoriestable.name
              FROM $pubsubprocesstable
         LEFT JOIN $pubsubsubscriptionstable
                ON $pubsubprocesstable.pubsub_id = $pubsubsubscriptionstable.id
         LEFT JOIN $pubsubeventstable
                ON $pubsubsubscriptionstable.event_id = $pubsubeventstable.id
         LEFT JOIN $pubsubtemplatestable
                ON $pubsubprocesstable.template_id = $pubsubtemplatestable.id
         LEFT JOIN $modulestable
                ON $pubsubeventstable.module_id = $modulestable.regid
         LEFT JOIN $rolestable
                ON $pubsubsubscriptionstable.userid = $rolestable.id
         LEFT JOIN $categoriestable
                ON $pubsubeventstable.cid = $categoriestable.id";

    if (!empty($status) && is_string($status)) {
        $query .= " WHERE $pubsubprocesstable.status = ?";
        $bindvars = [$status];
        $result = $dbconn->Execute($query, $bindvars);
    } else {
        $result = $dbconn->Execute($query);
    }
    if (!$result) {
        return;
    }

    $queue = [];
    while (!$result->EOF) {
        $info = [];
        [$info['id'],$info['pubsubid'],$info['objectid'],$info['template_id'],$info['status'],
            $info['eventid'],$info['userid'],$info['actionid'],$info['subdate'],$info['email'],
            $info['modid'],$info['itemtype'],$info['cid'],$info['extra'],
            $info['templatename'], $info['modname'], $info['username'], $info['catname']] = $result->fields;
        $queue[] = $info;
        $result->MoveNext();
    }
    return $queue;
} // END getq
