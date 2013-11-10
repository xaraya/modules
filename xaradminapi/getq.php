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
 * Get the queue of pending events
 * @return array list of events waiting to be processed
 * @throws DATABASE_ERROR
 */
function pubsub_adminapi_getq($args)
{
    // Get arguments from argument array
    extract($args);

    // Database information
    $dbconn =& xarDB::getConn();
    $xartable =& xarDB::getTables();
    $pubsubprocesstable = $xartable['pubsub_process'];
    $pubsubregtable = $xartable['pubsub_reg'];
    $pubsubeventstable = $xartable['pubsub_events'];
    $pubsubtemplatestable = $xartable['pubsub_templates'];

    $modulestable = $xartable['modules'];
    $rolestable = $xartable['roles'];

    // Load categories API
    if (!xarModAPILoad('categories', 'user')) {
        $msg = xarML('Unable to load #(1) #(2) API','categories','user');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'UNABLE_TO_LOAD', new SystemException($msg));
        return;
    }
    $categoriestable = $xartable['categories'];

    // Get all jobs in pending state
    $query = "SELECT $pubsubprocesstable.handlingid,
                     $pubsubprocesstable.pubsubid,
                     $pubsubprocesstable.objectid,
                     $pubsubprocesstable.templateid,
                     $pubsubprocesstable.status,
                     $pubsubregtable.eventid,
                     $pubsubregtable.userid,
                     $pubsubregtable.actionid,
                     $pubsubregtable.subdate,
                     $pubsubregtable.email,
                     $pubsubeventstable.modid,
                     $pubsubeventstable.itemtype,
                     $pubsubeventstable.cid,
                     $pubsubeventstable.extra,
                     $pubsubtemplatestable.name,
                     $modulestable.name,
                     $rolestable.uname,
                     $categoriestable.name
              FROM $pubsubprocesstable
         LEFT JOIN $pubsubregtable
                ON $pubsubprocesstable.pubsubid = $pubsubregtable.pubsubid
         LEFT JOIN $pubsubeventstable
                ON $pubsubregtable.eventid = $pubsubeventstable.eventid
         LEFT JOIN $pubsubtemplatestable
                ON $pubsubprocesstable.templateid = $pubsubtemplatestable.templateid
         LEFT JOIN $modulestable
                ON $pubsubeventstable.modid = $modulestable.regid
         LEFT JOIN $rolestable
                ON $pubsubregtable.userid = $rolestable.id
         LEFT JOIN $categoriestable
                ON $pubsubeventstable.cid = $categoriestable.id";

    if (!empty($status) && is_string($status)) {
        $query .= " WHERE $pubsubprocesstable.status = ?";
        $bindvars = array($status);
        $result = $dbconn->Execute($query,$bindvars);
    } else {
        $result = $dbconn->Execute($query);
    }
    if (!$result) return;

    $queue = array();
    while (!$result->EOF) {
        $info = array();
        list($info['handlingid'],$info['pubsubid'],$info['objectid'],$info['templateid'],$info['status'],
             $info['eventid'],$info['userid'],$info['actionid'],$info['subdate'],$info['email'],
             $info['modid'],$info['itemtype'],$info['cid'],$info['extra'],
             $info['templatename'],$info['modname'],$info['username'],$info['catname']) = $result->fields;
        $queue[] = $info;
        $result->MoveNext();
    }
    return $queue;

} // END getq

?>
