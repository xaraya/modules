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
 * process a pubsub event, by adding a job for each subscriber to the process queue
 * @param $args['modid'] the module id for the event
 * @param $args['itemtype'] the itemtype for the event
 * @param $args['cid'] the category id for the event
 * @param $args['extra'] some extra group criteria, and
 * @param $args['objectid'] the specific object in the module
 * @param $args['templateid'] the template id for the jobs
 * @returns bool
 * @return true on success, false on failure
 * @throws BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function pubsub_adminapi_processevent($args)
{
    // Get arguments from argument array
    extract($args);
    $invalid = array();
    if (empty($modid) || !is_numeric($modid)) {
        $invalid[] = 'modid';
    }
    if (!isset($cid) || !is_numeric($cid)) {
        $invalid[] = 'cid';
    }
    if (!isset($objectid) || !is_numeric($objectid)) {
        $invalid[] = 'objectid';
    }
    if (!isset($templateid) || !is_numeric($templateid)) {
        $invalid[] = 'templateid';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'admin', 'processevent', 'Pubsub');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    if (empty($itemtype) || !is_numeric($itemtype)) {
        $itemtype = 0;
    }

    // Security check - not via hooks
//    if (!xarSecurityCheck('AddPubSub')) return;

    // Get datbase setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $pubsubeventstable  = $xartable['pubsub_events'];
    $pubsubregtable     = $xartable['pubsub_reg'];
    $pubsubprocesstable = $xartable['pubsub_process'];

    // Create an array to list the subscriptions that need to be processed.
    $markSubscriptions = array();

    $includechildren = xarModGetVar('pubsub','includechildren');
    if ( !empty($cid) && $includechildren == 1 )
    {
        $ancestors = xarModAPIFunc('categories','user','getancestors'
                                   , array('cid'=>$cid,'order'=>'self') );
        $ancestors = array_keys($ancestors);

        $query = "SELECT xar_pubsubid, xar_cid
                    FROM $pubsubeventstable, $pubsubregtable
                   WHERE $pubsubeventstable.xar_eventid = $pubsubregtable.xar_eventid
                     AND $pubsubeventstable.xar_modid =?
                     AND $pubsubeventstable.xar_itemtype = ?";
//                      . "
//                     AND $pubsubeventstable. = " . ($cid);
        $bindvars = array((int)$modid, (int)$itemtype);
        if (isset($extra)) {
            $query .= " AND $pubsubeventstable.xar_extra = ?";
            array_push($bindvars, $extra);
        }
        $result =& $dbconn->Execute($query, $bindvars);
        if (!$result) return;

        for (; !$result->EOF; $result->MoveNext())
        {
            list($pubsubid, $xar_cid) = $result->fields;

            if( $xar_cid == $cid || in_array($xar_cid, $ancestors))
            {
                $markSubscriptions[] = $pubsubid;
            }
        }


    } else {
        $query = "SELECT xar_pubsubid
                    FROM $pubsubeventstable, $pubsubregtable
                   WHERE $pubsubeventstable.xar_eventid = $pubsubregtable.xar_eventid
                     AND $pubsubeventstable.xar_modid = ?
                     AND $pubsubeventstable.xar_itemtype = ?
                     AND $pubsubeventstable.xar_cid = ?";
        $bindvars = array((int)$modid, (int)$itemtype, (int)$cid);
        if (isset($extra)) {
            $query .= " AND $pubsubeventstable.xar_extra = ?";
            array_push($bindvars, $extra);
        }
        $result =& $dbconn->Execute($query, $bindvars);
        if (!$result) return;

        for (; !$result->EOF; $result->MoveNext())
        {
            list($pubsubid) = $result->fields;

            $markSubscriptions[] = $pubsubid;
        }
    }

    foreach( $markSubscriptions as $pubsubid )
    {
        // Get next ID in table
        $nextId = $dbconn->GenId($pubsubprocesstable);

        // Add item
        $query = "INSERT INTO $pubsubprocesstable (
                  xar_handlingid,
                  xar_pubsubid,
                  xar_objectid,
                  xar_templateid,
              xar_status)
                VALUES (?,?,?,?,
                  'pending')";
        $bindvars = array((int)$nextId, (int)$pubsubid, (int)$objectid, (int)$templateid);
        $result2 =& $dbconn->Execute($query, $bindvars);
        if (!$result2) return;
    }

    return true;

} // END processevent

?>
