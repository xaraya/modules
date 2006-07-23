<?php
/**
 * Pubsub module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Pubsub Module
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
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
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
    $query = "SELECT $pubsubprocesstable.xar_handlingid,
                     $pubsubprocesstable.xar_pubsubid,
                     $pubsubprocesstable.xar_objectid,
                     $pubsubprocesstable.xar_templateid,
                     $pubsubprocesstable.xar_status,
                     $pubsubregtable.xar_eventid,
                     $pubsubregtable.xar_userid,
                     $pubsubregtable.xar_actionid,
                     $pubsubregtable.xar_subdate,
                     $pubsubregtable.xar_email,
                     $pubsubeventstable.xar_modid,
                     $pubsubeventstable.xar_itemtype,
                     $pubsubeventstable.xar_cid,
                     $pubsubeventstable.xar_extra,
                     $pubsubtemplatestable.xar_name,
                     $modulestable.xar_name,
                     $rolestable.xar_uname,
                     $categoriestable.xar_name
              FROM $pubsubprocesstable
         LEFT JOIN $pubsubregtable
                ON $pubsubprocesstable.xar_pubsubid = $pubsubregtable.xar_pubsubid
         LEFT JOIN $pubsubeventstable
                ON $pubsubregtable.xar_eventid = $pubsubeventstable.xar_eventid
         LEFT JOIN $pubsubtemplatestable
                ON $pubsubprocesstable.xar_templateid = $pubsubtemplatestable.xar_templateid
         LEFT JOIN $modulestable
                ON $pubsubeventstable.xar_modid = $modulestable.xar_regid
         LEFT JOIN $rolestable
                ON $pubsubregtable.xar_userid = $rolestable.xar_uid
         LEFT JOIN $categoriestable
                ON $pubsubeventstable.xar_cid = $categoriestable.xar_cid";

    if (!empty($status) && is_string($status)) {
        $query .= " WHERE $pubsubprocesstable.xar_status = ?";
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
